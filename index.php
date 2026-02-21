<?php
/**
 * @file api/v1/site/PKPSiteHandler.inc.php
 *
 * Copyright (c) 2014-2021 Simon Fraser University
 * Copyright (c) 2000-2021 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class PKPSiteHandler
 * @ingroup api_v1_users
 *
 * @brief Base class to handle API requests for the site object.
 */

import('lib.pkp.classes.handler.APIHandler');
use APP\Services\SubmissionFileService;

class EasyFileApiHandler extends APIHandler
{
    /** @var string One of the SCHEMA_... constants */
    public $schemaName = SCHEMA_SITE;

    /**
     * @copydoc APIHandler::__construct()
     */
    public function __construct()
    {
        $this->_handlerPath = '_files';
        $roles = [ROLE_ID_SITE_ADMIN, ROLE_ID_MANAGER];
        $this->_endpoints = array(
            'GET' => array(
                array(
                    'pattern' => $this->getEndpointPattern() . '/{submissionFileId}',
                    'handler' => array($this, 'getFile'),
                    'roles' => $roles,
                ),
            ),
        );
        parent::__construct();

    }

    /**
     * @copydoc PKPHandler::authorize
     */
    public function authorize($request, &$args, $roleAssignments)
    {
        import('lib.pkp.classes.security.authorization.PolicySet');
        $rolePolicy = new PolicySet(COMBINING_PERMIT_OVERRIDES);

        import('lib.pkp.classes.security.authorization.RoleBasedHandlerOperationPolicy');
        foreach ($roleAssignments as $role => $operations) {
            $rolePolicy->addPolicy(new RoleBasedHandlerOperationPolicy($request, $role, $operations));
        }
        $this->addPolicy($rolePolicy);


        return parent::authorize($request, $args, $roleAssignments);
    }
    /**
     * Get the File 
     *
     * @param $slimRequest Request Slim request object
     * @param $response Response object
     * @param array $args arguments
     *
     * @return Response
     */
    public function getFile($slimRequest, $response, $args)
    {

        $submissionFileService = Services::get('submissionFile'); /* @var $submissionFileService SubmissionFileService */

        $submissionFileId = $args['submissionFileId'];

        $submissionFile = $submissionFileService::get($submissionFileId);
        $fileId = $submissionFile->getData('fileId');
        $submissionFileName = $submissionFile->getLocalizedData('name');
        Services::get('file')->download((int) $fileId, $submissionFileName);

        return $response->withJson([], 200);
    }



}

return new EasyFileApiHandler();