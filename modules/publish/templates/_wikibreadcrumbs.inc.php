<?php

    $article_name = (isset($article_name)) ? $article_name : '';
    if (!\thebuggenie\core\framework\Context::isProjectContext() || (\thebuggenie\core\framework\Context::isProjectContext() && mb_strtolower($article_name) != mb_strtolower(\thebuggenie\core\framework\Context::getCurrentProject()->getKey() . ':mainpage')))
    {
        if (\thebuggenie\core\framework\Context::isProjectContext())
        {
            $tbg_response->addBreadcrumb(\thebuggenie\core\framework\Context::getModule('publish')->getMenuTitle(), make_url('publish_article', array('article_name' => \thebuggenie\core\framework\Context::getCurrentProject()->getKey() . ':MainPage')), tbg_get_breadcrumblinks('project_summary', \thebuggenie\core\framework\Context::getCurrentProject()));
        }
        else
        {
            $tbg_response->addBreadcrumb(\thebuggenie\core\framework\Context::getModule('publish')->getMenuTitle(), make_url('publish_article', array('article_name' => 'MainPage')), tbg_get_breadcrumblinks('main_links'));
        }
        $items = explode(':', $article_name);
        $bcpath = array_shift($items);
        if (mb_strtolower($bcpath) == 'category')
        {
            $tbg_response->addBreadcrumb(__('Categories'));
            if (\thebuggenie\core\framework\Context::isProjectContext())
            {
                $bcpath .= ":".array_shift($items);
            }
        }
        elseif (!\thebuggenie\core\framework\Context::isProjectContext() && mb_strtolower($bcpath) != 'mainpage')
        {
            $tbg_response->addBreadcrumb($bcpath, make_url('publish_article', array('article_name' => $bcpath)));
        }
        foreach ($items as $bc_name)
        {
            $bcpath .= ":".$bc_name;
            $tbg_response->addBreadcrumb($bc_name, make_url('publish_article', array('article_name' => $bcpath)));
        }
    }
    else
    {
        $tbg_response->addBreadcrumb(\thebuggenie\core\framework\Context::getModule('publish')->getMenuTitle(), make_url('publish_article', array('article_name' => \thebuggenie\core\framework\Context::getCurrentProject()->getKey() . ':MainPage')), tbg_get_breadcrumblinks('project_summary', \thebuggenie\core\framework\Context::getCurrentProject()));
    }
