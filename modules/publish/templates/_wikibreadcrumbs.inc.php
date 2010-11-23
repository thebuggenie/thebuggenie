<?php

	if (!TBGContext::isProjectContext() || (TBGContext::isProjectContext() && strtolower($article_name) != strtolower(TBGContext::getCurrentProject()->getStrippedProjectName() . ':mainpage')))
	{
		if (TBGContext::isProjectContext())
		{
			$tbg_response->addBreadcrumb(TBGPublish::getModule()->getMenuTitle(), make_url('publish_article', array('article_name' => TBGContext::getCurrentProject()->getStrippedProjectName() . ':MainPage')));
		}
		else
		{
			$tbg_response->addBreadcrumb(TBGPublish::getModule()->getMenuTitle(), make_url('publish_article', array('article_name' => 'MainPage')));
		}
		$items = explode(':', $article_name);
		$bcpath = array_shift($items);
		if (strtolower($bcpath) == 'category')
		{
			$tbg_response->addBreadcrumb(__('Categories'));
			if (TBGContext::isProjectContext())
			{
				$bcpath .= ":".array_shift($items);
			}
		}
		elseif (!TBGContext::isProjectContext() && strtolower($bcpath) != 'mainpage')
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
		$tbg_response->addBreadcrumb(TBGPublish::getModule()->getMenuTitle());
	}

?>