<?php

namespace thebuggenie\core\modules\main\controller;

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
class AssetController extends framework\Action
{

    public function runResolve(framework\Request $request)
    {
        $theme = \thebuggenie\core\framework\Settings::getThemeName();
        if ($request->hasParameter('css')) {
            $this->getResponse()->setContentType('text/css');
            $asset = THEBUGGENIE_PATH . 'themes'.DS.$theme.DS.'css'.DS.$request->getParameter('css');
        } elseif ($request->hasParameter('js')) {
            $this->getResponse()->setContentType('text/javascript');
            $asset = THEBUGGENIE_PATH . 'themes'.DS.$theme.DS.'js'.DS.$request->getParameter('js');
        } else {
            throw new \Exception('The expected theme Asset type is not supported.');
        }

        $fileAsset = new AssetCollection(array(
            new FileAsset($asset)
        ));
        $fileAsset->load();

        // Do not decorate the asset with the theme's header/footer
        $this->getResponse()->setDecoration(framework\Response::DECORATE_NONE);
        return $this->renderText($fileAsset->dump());
    }

}
