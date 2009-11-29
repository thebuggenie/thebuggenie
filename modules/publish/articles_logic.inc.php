<?php

	if (!defined('THEBUGGENIE_PATH')) exit();
	
	if (BUGScontext::getRequest()->getParameter('create_new') && BUGScontext::getUser()->hasPermission('article_management', 0, 'publish'))
	{
		switch (BUGScontext::getRequest()->getParameter('article_type'))
		{
			case 1:
				$crit = new B2DBCriteria();
				$crit->addInsert(B2tArticles::TITLE, 'Article title');
				$crit->addInsert(B2tArticles::INTRO_TEXT, 'Introduction text');
				$crit->addInsert(B2tArticles::ICON, 'wordprocessing');
				$crit->addInsert(B2tArticles::CONTENT, 'Article content');
				$crit->addInsert(B2tArticles::LINK, '');
				$crit->addInsert(B2tArticles::IS_NEWS, 1);
				$crit->addInsert(B2tArticles::IS_PUBLISHED, 0);
				$crit->addInsert(B2tArticles::DATE, $_SERVER["REQUEST_TIME"]);
				$crit->addInsert(B2tArticles::SCOPE, BUGScontext::getScope()->getID());
				$res = B2DB::getTable('B2tArticles')->doInsert($crit);
				BUGScontext::getRequest()->setParameter('article_id', $res->getInsertID());
				break;
			case 2:
				$crit = new B2DBCriteria();
				$crit->addInsert(B2tArticles::TITLE, 'News headline');
				$crit->addInsert(B2tArticles::INTRO_TEXT, '');
				$crit->addInsert(B2tArticles::ICON, '');
				$crit->addInsert(B2tArticles::CONTENT, '');
				$crit->addInsert(B2tArticles::LINK, '');
				$crit->addInsert(B2tArticles::IS_NEWS, 1);
				$crit->addInsert(B2tArticles::IS_PUBLISHED, 0);
				$crit->addInsert(B2tArticles::DATE, $_SERVER["REQUEST_TIME"]);
				$crit->addInsert(B2tArticles::SCOPE, BUGScontext::getScope()->getID());
				$res = B2DB::getTable('B2tArticles')->doInsert($crit);
				BUGScontext::getRequest()->setParameter('article_id', $res->getInsertID());
				break;				
			case 3:
				$crit = new B2DBCriteria();
				$crit->addInsert(B2tArticles::TITLE, 'Link title');
				$crit->addInsert(B2tArticles::INTRO_TEXT, '');
				$crit->addInsert(B2tArticles::ICON, '');
				$crit->addInsert(B2tArticles::CONTENT, '');
				$crit->addInsert(B2tArticles::LINK, 'http://');
				$crit->addInsert(B2tArticles::IS_NEWS, 1);
				$crit->addInsert(B2tArticles::IS_PUBLISHED, 0);
				$crit->addInsert(B2tArticles::DATE, $_SERVER["REQUEST_TIME"]);
				$crit->addInsert(B2tArticles::SCOPE, BUGScontext::getScope()->getID());
				$res = B2DB::getTable('B2tArticles')->doInsert($crit);
				BUGScontext::getRequest()->setParameter('article_id', $res->getInsertID());
				break;				
		}
	}
	elseif (!BUGScontext::getRequest()->getParameter('article_id'))
	{
		BUGScontext::getRequest()->setParameter('article_id', $_SESSION['article_id']);
	}
	else
	{
		$_SESSION['article_id'] = BUGScontext::getRequest()->getParameter('article_id');
	}
	
	$issaved = false;
	
	if (BUGScontext::getRequest()->getParameter('submit_article_changes') && is_numeric(BUGScontext::getRequest()->getParameter('article_id')) && BUGScontext::getUser()->hasPermission('article_management', 0, 'publish'))
	{
		$crit = new B2DBCriteria();
		switch (BUGScontext::getRequest()->getParameter('article_type'))
		{
			case 1:
				BUGScontext::getRequest()->setParameter('link_url', '');
				break;
			case 2:
				BUGScontext::getRequest()->setParameter('intro', '');
				BUGScontext::getRequest()->setParameter('content', '');
				BUGScontext::getRequest()->setParameter('link_url', '');
				break;
			case 3:
				BUGScontext::getRequest()->setParameter('intro', '');
				BUGScontext::getRequest()->setParameter('content', '');
				break;
		}
		$crit->addUpdate(B2tArticles::TITLE, BUGScontext::getRequest()->getParameter('title'));
		$crit->addUpdate(B2tArticles::INTRO_TEXT, BUGScontext::getRequest()->getParameter('intro', null, false));
		$crit->addUpdate(B2tArticles::ICON, BUGScontext::getRequest()->getParameter('icon_select'));
		$crit->addUpdate(B2tArticles::CONTENT, BUGScontext::getRequest()->getParameter('content', null, false));
		$crit->addUpdate(B2tArticles::LINK, BUGScontext::getRequest()->getParameter('link_url'));
		$crit->addUpdate(B2tArticles::AUTHOR, BUGScontext::getUser()->getID());
		$crit->addUpdate(B2tArticles::IS_NEWS, ((BUGScontext::getRequest()->getParameter('is_news')) ? 1 : 0));
		$crit->addUpdate(B2tArticles::IS_PUBLISHED, ((BUGScontext::getRequest()->getParameter('is_published')) ? 1 : 0));
		$crit->addUpdate(B2tArticles::DATE, $_SERVER["REQUEST_TIME"]);
		$res = B2DB::getTable('B2tArticles')->doUpdateById($crit, BUGScontext::getRequest()->getParameter('article_id'));
		$issaved = true;
	}
	
	try
	{
		$article = new PublishArticle(BUGScontext::getRequest()->getParameter('article_id'));
		if (!$article->isPublished() && !BUGScontext::getUser()->hasPermission('article_management', 0, 'publish'))
		{
			throw new Exception('This article does not exist');
		}
	}
	catch (Exception $e)
	{
		bugs_showError('Not permitted', $e->getMessage(), true);
		exit();				
	}
	
	if (BUGScontext::getUser()->hasPermission('article_management', 0, 'publish'))
	{
		if (BUGScontext::getRequest()->getParameter('retract'))
		{
			$article->retract();
		}
		elseif (BUGScontext::getRequest()->getParameter('publish'))
		{
			$article->publish();
		}
		if (BUGScontext::getRequest()->getParameter('feature'))
		{
			BUGScontext::getModule('publish')->saveSetting('featured_article', $article->getID());
		}
	}
	
	$is_published = false;
	if (BUGScontext::getRequest()->getParameter('post_on_billboard') && is_numeric(BUGScontext::getRequest()->getParameter('billboard')))
	{
		if ((BUGScontext::getRequest()->getParameter('billboard') == 0 && BUGScontext::getUser()->hasPermission('publish_postonglobalbillboard', 0, "publish")) || (BUGScontext::getUser()->hasPermission('publish_postonteambillboard', 0, "publish") && in_array(BUGScontext::getRequest()->getParameter('billboard'), BUGScontext::getUser()->getTeams())))
		{
			$title = $article->getTitle();
			$crit = new B2DBCriteria();
			$crit->addInsert(B2tBillboardPosts::AUTHOR, BUGScontext::getUser()->getUID());
			$crit->addInsert(B2tBillboardPosts::DATE, $_SERVER["REQUEST_TIME"]);
			$crit->addInsert(B2tBillboardPosts::SCOPE, BUGScontext::getScope()->getID());
			$crit->addInsert(B2tBillboardPosts::TARGET_BOARD, (int) BUGScontext::getRequest()->getParameter('billboard'));
			$crit->addInsert(B2tBillboardPosts::TITLE, $title);
			$crit->addInsert(B2tBillboardPosts::ARTICLE_ID, $article->getID());
			$crit->addInsert(B2tBillboardPosts::CONTENT, $article->getIntro());
			$res = B2DB::getTable('B2tBillboardPosts')->doInsert($crit);
			$is_published = true;
		}
	}

?>
