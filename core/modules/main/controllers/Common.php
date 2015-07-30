<?php

namespace thebuggenie\core\modules\main\controllers;

use thebuggenie\core\framework,
    thebuggenie\core\entities,
    thebuggenie\core\entities\tables,
    thebuggenie\modules\agile;

/**
 * actions for the main module
 */
class Common extends framework\Action
{

    /**
     * About page
     *
     * @param \thebuggenie\core\framework\Request $request
     */
    public function runAbout(framework\Request $request)
    {
        $this->forward403unless($this->getUser()->hasPageAccess('about'));
    }

    /**
     * 404 not found page
     *
     * @param \thebuggenie\core\framework\Request $request
     */
    public function runNotFound(framework\Request $request)
    {
        $this->getResponse()->setHttpStatus(404);
        $message = null;
    }

    /**
     * Logs the user out
     *
     * @param \thebuggenie\core\framework\Request $request
     *
     * @return bool
     */
    public function runLogout(framework\Request $request)
    {
        if ($this->getUser() instanceof entities\User)
        {
            framework\Logging::log('Setting user logout state');
            $this->getUser()->setOffline();
        }
        framework\Context::logout();
        if ($request->isAjaxCall())
        {
            return $this->renderJSON(array('status' => 'logout ok', 'url' => framework\Context::getRouting()->generate(framework\Settings::getLogoutReturnRoute())));
        }
        $this->forward(framework\Context::getRouting()->generate(framework\Settings::getLogoutReturnRoute()));
    }

}
