<?php
	
	if (!defined('THEBUGGENIE_PATH')) exit();
	
	if (BUGScontext::getUser()->hasPermission('manage_billboard', 0, 'publish') && BUGScontext::getRequest()->isAjaxCall() && BUGScontext::getRequest()->getParameter('p_id'))
	{
		$billboardpost = new PublishBillboardPost((int) BUGScontext::getRequest()->getParameter('p_id'));
		$billboardpost->delete();
	}
	
	if (BUGScontext::getUser()->hasPermission('article_management', 0, 'publish'))
	{
		if (BUGScontext::getRequest()->getParameter('manage') && BUGScontext::getRequest()->getParameter('article_id'))
		{
			try
			{
				$article = new PublishArticle(BUGScontext::getRequest()->getParameter('article_id'));
				if (!$article->isPublished() && !BUGScontext::getUser()->hasPermission('article_management', 0, 'publish'))
				{
					throw new Exception('This article does not exist');
				}
				if (BUGScontext::getRequest()->getParameter('retract'))
				{
					$article->retract();
				}
				elseif (BUGScontext::getRequest()->getParameter('publish'))
				{
					$article->publish();
				}
				if (BUGScontext::getRequest()->getParameter('show'))
				{
					$article->showInNews();
				}
				elseif (BUGScontext::getRequest()->getParameter('hide'))
				{
					$article->hideFromNews();
				}
				if (BUGScontext::getRequest()->getParameter('feature'))
				{
					BUGScontext::getModule('publish')->saveSetting('featured_article', $article->getID());
				}
			}
			catch (Exception $e)
			{
			}
		}
		if (BUGScontext::getRequest()->isAjaxCall())
		{
			if (BUGScontext::getRequest()->getParameter('articles'))
			{
				$articles = array();
				parse_str(BUGScontext::getRequest()->getParameter('articles'), $articles);
				for ($cc = 0; $cc < count($articles['article_list']); $cc++)
				{
					$article = new PublishArticle($articles['article_list'][$cc]);
					$article->setOrder($cc);
				}
			}
		}
	}

	if (BUGScontext::getRequest()->isAjaxCall())
	{
		exit();
	}
	
?>