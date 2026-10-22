<?php

/**
 * @file plugins/generic/easyFileApi/EasyFileApiPlugin.php
 *
 * Copyright (c) 2023-2026 Thomas F. K. Jorna, Center of Trial and Error
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class EasyFileApiPlugin
 *
 * @brief Adds GET /api/v1/_files/{submissionFileId}, which downloads a submission file.
 */

namespace APP\plugins\generic\easyFileApi;

use APP\core\Application;
use PKP\core\APIRouter;
use PKP\plugins\GenericPlugin;
use PKP\plugins\Hook;

class EasyFileApiPlugin extends GenericPlugin
{
    /**
     * @copydoc Plugin::register()
     *
     * @param null|mixed $mainContextId
     */
    public function register($category, $path, $mainContextId = null)
    {
        $success = parent::register($category, $path, $mainContextId);
        if (!$success || Application::isUnderMaintenance()) {
            return $success;
        }

        if ($this->getEnabled($mainContextId)) {
            Hook::add('APIHandler::endpoints::plugin', function (string $hookName, APIRouter $router): bool {
                $router->registerPluginApiControllers([new EasyFileApiController()]);
                return Hook::CONTINUE;
            });
        }

        return $success;
    }

    /**
     * @copydoc Plugin::getDisplayName()
     */
    public function getDisplayName()
    {
        return __('plugins.generic.easyFileApi.displayName');
    }

    /**
     * @copydoc Plugin::getDescription()
     */
    public function getDescription()
    {
        return __('plugins.generic.easyFileApi.description');
    }
}
