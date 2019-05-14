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
        $module_path = (framework\Context::isInternalModule($request['module_name'])) ? THEBUGGENIE_INTERNAL_MODULES_PATH : THEBUGGENIE_MODULES_PATH;
        if ($request->hasParameter('css')) {
            $this->getResponse()->setContentType('text/css');
            if ($request->hasParameter('module_name') && framework\Context::isModuleLoaded($request['module_name'])) {
                $basepath = $module_path . $request['module_name'].DS.'public'.DS.'css';
                $asset = $module_path . $request['module_name'].DS.'public'.DS.'css'.DS.$request->getParameter('css');
            } elseif (!$request->hasParameter('theme_name')) {
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
                $basepath = $module_path . $request['module_name'].DS.'public'.DS.'js';
                $asset = $module_path . $request['module_name'].DS.'public'.DS.'js'.DS.$request->getParameter('js');
            } else {
                $basepath = THEBUGGENIE_PATH . 'public'.DS.'js';
                $asset = THEBUGGENIE_PATH . 'public'.DS.'js'.DS.$request->getParameter('js');
            }
        } elseif ($request->hasParameter('image')) {
            $basepath = THEBUGGENIE_PATH . 'themes';
            $asset = THEBUGGENIE_PATH . 'themes'.DS.$theme.DS.'images';

            if (isset($request['module_name'])) {
                $asset .= DS."modules".DS.$request['module_name'];
            }
            if (isset($request['folder'])) {
                $asset .= DS.$request['folder'];
            }

            $asset .= DS.$request->getParameter('image');

            if (!file_exists($asset) &&
                isset($request['module_name']) &&
                framework\Context::isModuleLoaded($request['module_name'])
            ) {
                $basepath = $module_path . $request['module_name'].DS.'public'.DS.'images';
                $asset = $module_path . $request['module_name'].DS.'public'.DS.'images';

                if (isset($request['folder'])) {
                    $asset .= DS.$request['folder'];
                }

                $asset .= DS.$request->getParameter('image');
            }
            $mimetype = null;
            if (function_exists('finfo_open')) {
                $fileinfo = finfo_open(FILEINFO_MIME_TYPE);
                $mimetype = finfo_file($fileinfo, $asset);
                finfo_close($fileinfo);
            } elseif (function_exists('mime_content_type')) {
                $mimetype = mime_content_type($asset);
            }
            $this->getResponse()->setContentType($mimetype);
        } else {
            throw new \Exception('The expected theme Asset type is not supported.');
        }

        $last_modified = filemtime($asset);
        $this->getResponse()->addHeader('Cache-Control: max-age=3600, must-revalidate');
        $this->getResponse()->addHeader('Last-Modified: ' . gmdate('D, d M Y H:i:s ', $last_modified) . 'GMT');
        $this->getResponse()->addHeader('ETag: ' . md5($last_modified));
        if (!$this->getResponse()->isModified($last_modified)) {
            return $this->return304();
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
