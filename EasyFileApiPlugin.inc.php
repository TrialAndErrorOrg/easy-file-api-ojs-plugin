<?php

import('lib.pkp.classes.plugins.GenericPlugin');

class EasyFileApiPlugin extends GenericPlugin
{
    /**
     * @copydoc Plugin::getName()
     */
    function getName()
    {
        return 'EasyFileAPIPlugin';
    }

    /**
     * Get the display name of this plugin.
     * @return string
     */
    public function getDisplayName()
    {
        return __('plugins.generic.easyFileApi.displayName');
    }

    /**
     * Get a brief description of this plugin.
     * @return string
     */
    public function getDescription()
    {
        return __('plugins.generic.easyFileApi.description');
    }

    /**
     * @copydoc Plugin::register()
     */
    public function register($category, $path, $mainContextId = null)
    {

        if (!parent::register($category, $path, $mainContextId)) {
            return false;
        }

        if ($this->getEnabled($mainContextId)) {
            HookRegistry::register('Dispatcher::dispatch', function (string $hook, \PKPRequest $request): bool {
                $path = array_slice(explode('/', trim($request->getRequestPath(), '/')), -2);
                $router = $request->getRouter();
                if ($router instanceof \APIRouter && $path[0] == '_files') {
                    import('plugins.generic.easyFileApi.EasyFileApiHandler');
                    $handler = new EasyFileApiHandler($this);
                    $router->setHandler($handler);
                    $handler->getApp()->run();
                    exit;
                }
                return false;
            });
            return true;
        }

        return true;
    }

}

?>