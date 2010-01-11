<?php

	if (!defined('THEBUGGENIE_PATH')) exit();

	if (TBGContext::getRequest()->isAjaxCall())
	{
		if (TBGContext::getRequest()->getParameter('billboard_post_new_link') && TBGContext::getRequest()->getParameter('post_link_url') && trim(TBGContext::getRequest()->getParameter('post_link_url')) != '' && TBGContext::getRequest()->getParameter('post_link_billboard') !== null)
		{
			if ((TBGContext::getRequest()->getParameter('post_link_billboard') == 0 && TBGContext::getUser()->hasPermission('publish_postonglobalbillboard', 0, "publish")) || (TBGContext::getUser()->hasPermission('publish_postonteambillboard', 0, "publish") && in_array(TBGContext::getRequest()->getParameter('post_link_billboard'), TBGContext::getUser()->getTeams())))
			{
				$url = trim(TBGContext::getRequest()->getParameter('post_link_url'));
				$description = (trim(TBGContext::getRequest()->getParameter('post_link_description')) != '') ? trim(TBGContext::getRequest()->getParameter('post_link_description')) : trim(TBGContext::getRequest()->getParameter('post_link_url'));
				$crit = new B2DBCriteria();
				$crit->addInsert(B2tBillboardPosts::AUTHOR, TBGContext::getUser()->getUID());
				$crit->addInsert(B2tBillboardPosts::DATE, $_SERVER["REQUEST_TIME"]);
				$crit->addInsert(B2tBillboardPosts::LINK, $url);
				$crit->addInsert(B2tBillboardPosts::SCOPE, TBGContext::getScope()->getID());
				$crit->addInsert(B2tBillboardPosts::TARGET_BOARD, (int) TBGContext::getRequest()->getParameter('post_link_billboard'));
				$crit->addInsert(B2tBillboardPosts::TITLE, $description);
				$res = B2DB::getTable('B2tBillboardPosts')->doInsert($crit);
				TBGContext::getModule('publish')->printBillboardPostOnBillboard(new PublishBillboardPost($res->getInsertID()));
			}
		}
		if (TBGContext::getRequest()->getParameter('billboard_post_new_text') && TBGContext::getRequest()->getParameter('post_text_title') && trim(TBGContext::getRequest()->getParameter('post_text_title')) != '' && TBGContext::getRequest()->getParameter('post_text_billboard') !== null)
		{
			if ((TBGContext::getRequest()->getParameter('post_link_billboard') == 0 && TBGContext::getUser()->hasPermission('publish_postonglobalbillboard', 0, "publish")) || (TBGContext::getUser()->hasPermission('publish_postonteambillboard', 0, "publish") && in_array(TBGContext::getRequest()->getParameter('post_link_billboard'), TBGContext::getUser()->getTeams())))
			{
				$title = trim(TBGContext::getRequest()->getParameter('post_text_title'));
				$content = (trim(TBGContext::getRequest()->getParameter('post_text_content')) != '') ? trim(TBGContext::getRequest()->getParameter('post_text_content')) : trim(TBGContext::getRequest()->getParameter('post_text_title'));
				$crit = new B2DBCriteria();
				$crit->addInsert(B2tBillboardPosts::AUTHOR, TBGContext::getUser()->getUID());
				$crit->addInsert(B2tBillboardPosts::DATE, $_SERVER["REQUEST_TIME"]);
				$crit->addInsert(B2tBillboardPosts::SCOPE, TBGContext::getScope()->getID());
				$crit->addInsert(B2tBillboardPosts::TARGET_BOARD, (int) TBGContext::getRequest()->getParameter('post_text_billboard'));
				$crit->addInsert(B2tBillboardPosts::TITLE, $title);
				$crit->addInsert(B2tBillboardPosts::CONTENT, $content);
				$res = B2DB::getTable('B2tBillboardPosts')->doInsert($crit);
				TBGContext::getModule('publish')->printBillboardPostOnBillboard(new PublishBillboardPost($res->getInsertID()));
			}
		}
		exit();
	}
	
?>