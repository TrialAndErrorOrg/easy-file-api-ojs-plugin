<?php

/**
 * @file plugins/generic/easyFileApi/EasyFileApiController.php
 *
 * Copyright (c) 2023-2026 Thomas F. K. Jorna, Center of Trial and Error
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class EasyFileApiController
 *
 * @brief GET /api/v1/_files/{submissionFileId}: download the current revision of a submission file.
 */

namespace APP\plugins\generic\easyFileApi;

use APP\facades\Repo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use PKP\core\PKPBaseController;
use PKP\core\PKPRequest;
use PKP\security\authorization\ContextAccessPolicy;
use PKP\security\authorization\PolicySet;
use PKP\security\authorization\RoleBasedHandlerOperationPolicy;
use PKP\security\Role;

class EasyFileApiController extends PKPBaseController
{
    /**
     * @copydoc PKPBaseController::getHandlerPath()
     */
    public function getHandlerPath(): string
    {
        return '_files';
    }

    /**
     * @copydoc PKPBaseController::getRouteGroupMiddleware()
     */
    public function getRouteGroupMiddleware(): array
    {
        return [
            'has.user',
            'has.context',
            self::roleAuthorizer([Role::ROLE_ID_SITE_ADMIN, Role::ROLE_ID_MANAGER]),
        ];
    }

    /**
     * @copydoc PKPBaseController::getGroupRoutes()
     */
    public function getGroupRoutes(): void
    {
        Route::get('{submissionFileId}', $this->getFile(...))
            ->name('_files.getFile')
            ->whereNumber('submissionFileId');
    }

    /**
     * @copydoc PKPBaseController::authorize()
     */
    public function authorize(PKPRequest $request, array &$args, array $roleAssignments): bool
    {
        $this->addPolicy(new ContextAccessPolicy($request, $roleAssignments));

        $rolePolicy = new PolicySet(PolicySet::COMBINING_PERMIT_OVERRIDES);
        foreach ($roleAssignments as $role => $operations) {
            $rolePolicy->addPolicy(new RoleBasedHandlerOperationPolicy($request, $role, $operations));
        }
        $this->addPolicy($rolePolicy);

        return parent::authorize($request, $args, $roleAssignments);
    }

    /**
     * Stream the file, or answer 404 when it isn't a file of this journal.
     */
    public function getFile(Request $illuminateRequest): JsonResponse
    {
        $submissionFile = Repo::submissionFile()->get((int) $illuminateRequest->route('submissionFileId'));
        $submission = $submissionFile ? Repo::submission()->get($submissionFile->getData('submissionId')) : null;

        if (!$submission || $submission->getData('contextId') != $this->getRequest()->getContext()->getId()) {
            return response()->json([
                'error' => 'api.404.resourceNotFound',
                'errorMessage' => __('api.404.resourceNotFound'),
            ], Response::HTTP_NOT_FOUND);
        }

        app()->get('file')->download((int) $submissionFile->getData('fileId'), $submissionFile->getLocalizedData('name'));
    }
}
