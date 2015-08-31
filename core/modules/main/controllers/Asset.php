<?php

namespace thebuggenie\core\modules\main\controllers;

use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use thebuggenie\core\framework,
    thebuggenie\core\entities,
    thebuggenie\core\entities\tables,
    thebuggenie\modules\agile;

/**
 * Class AssetController serves theme assets that are not available via the /public folder directly.
 *
 * @package thebuggenie\core\modules\main\controller
 */
class Asset extends framework\Action
{
    public function runResolve(framework\Request $request)
    {
        $theme = isset($request['theme_name']) ? $request['theme_name'] : framework\Settings::getThemeName();
        if ($request->hasParameter('css')) {
            $this->getResponse()->setContentType('text/css');
            if (!$request->hasParameter('theme_name')) {
                $basepath = THEBUGGENIE_PATH . 'public'.DS.'css';
                $asset = THEBUGGENIE_PATH . 'public'.DS.'css'.DS.$request->getParameter('css');
            } else {
                $basepath = THEBUGGENIE_PATH . 'themes';
                $asset = THEBUGGENIE_PATH . 'themes'.DS.$theme.DS.'css'.DS.$request->getParameter('css');
            }
        } elseif ($request->hasParameter('js')) {
            $this->getResponse()->setContentType('text/javascript');
            if ($request->hasParameter('theme_name')) {
                $basepath = THEBUGGENIE_PATH . 'themes';
                $asset = THEBUGGENIE_PATH . 'themes'.DS.$theme.DS.'js'.DS.$request->getParameter('js');
            } elseif ($request->hasParameter('module_name') && framework\Context::isModuleLoaded($request['module_name'])) {
                $module_path = (framework\Context::isInternalModule($request['module_name'])) ? THEBUGGENIE_INTERNAL_MODULES_PATH : THEBUGGENIE_MODULES_PATH;
                $basepath = $module_path . $request['module_name'].DS.'public'.DS.'js';
                $asset = $module_path . $request['module_name'].DS.'public'.DS.'js'.DS.$request->getParameter('js');
            } else {
                $basepath = THEBUGGENIE_PATH . 'public'.DS.'js';
                $asset = THEBUGGENIE_PATH . 'public'.DS.'js'.DS.$request->getParameter('js');
            }
        } else {
            throw new \Exception('The expected theme Asset type is not supported.');
        }

        $fileAsset = new AssetCollection(array(
            new FileAsset($asset, array(), $basepath)
        ));
        $fileAsset->load();

        // Do not decorate the asset with the theme's header/footer
        $this->getResponse()->setDecoration(framework\Response::DECORATE_NONE);
        return $this->renderText($fileAsset->dump());
    }
}
