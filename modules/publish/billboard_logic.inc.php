<?php

	if (!defined('BUGS2_INCLUDE_PATH')) exit();

	if (BUGScontext::getRequest()->isAjaxCall())
	{
		if (BUGScontext::getRequest()->getParameter('billboard_post_new_link') && BUGScontext::getRequest()->getParameter('post_link_url') && trim(BUGScontext::getRequest()->getParameter('post_link_url')) != '' && BUGScontext::getRequest()->getParameter('post_link_billboard') !== null)
		{
			if ((BUGScontext::getRequest()->getParameter('post_link_billboard') == 0 && BUGScontext::getUser()->hasPermission('publish_postonglobalbillboard', 0, "publish")) || (BUGScontext::getUser()->hasPermission('publish_postonteambillboard', 0, "publish") && in_array(BUGScontext::getRequest()->getParameter('post_link_billboard'), BUGScontext::getUser()->getTeams())))
			{
				$url = trim(BUGScontext::getRequest()->getParameter('post_link_url'));
				$description = (trim(BUGScontext::getRequest()->getParameter('post_link_description')) != '') ? trim(BUGScontext::getRequest()->getParameter('post_link_description')) : trim(BUGScontext::getRequest()->getParameter('post_link_url'));
				$crit = new B2DBCriteria();
				$crit->addInsert(B2tBillboardPosts::AUTHOR, BUGScontext::getUser()->getUID());
				$crit->addInsert(B2tBillboardPosts::DATE, $_SERVER["REQUEST_TIME"]);
				$crit->addInsert(B2tBillboardPosts::LINK, $url);
				$crit->addInsert(B2tBillboardPosts::SCOPE, BUGScontext::getScope()->getID());
				$crit->addInsert(B2tBillboardPosts::TARGET_BOARD, (int) BUGScontext::getRequest()->getParameter('post_link_billboard'));
				$crit->addInsert(B2tBillboardPosts::TITLE, $description);
				$res = B2DB::getTable('B2tBillboardPosts')->doInsert($crit);
				BUGScontext::getModule('publish')->printBillboardPostOnBillboard(new PublishBillboardPost($res->getInsertID()));
			}
		}
		if (BUGScontext::getRequest()->getParameter('billboard_post_new_text') && BUGScontext::getRequest()->getParameter('post_text_title') && trim(BUGScontext::getRequest()->getParameter('post_text_title')) != '' && BUGScontext::getRequest()->getParameter('post_text_billboard') !== null)
		{
			if ((BUGScontext::getRequest()->getParameter('post_link_billboard') == 0 && BUGScontext::getUser()->hasPermission('publish_postonglobalbillboard', 0, "publish")) || (BUGScontext::getUser()->hasPermission('publish_postonteambillboard', 0, "publish") && in_array(BUGScontext::getRequest()->getParameter('post_link_billboard'), BUGScontext::getUser()->getTeams())))
			{
				$title = trim(BUGScontext::getRequest()->getParameter('post_text_title'));
				$content = (trim(BUGScontext::getRequest()->getParameter('post_text_content')) != '') ? trim(BUGScontext::getRequest()->getParameter('post_text_content')) : trim(BUGScontext::getRequest()->getParameter('post_text_title'));
				$crit = new B2DBCriteria();
				$crit->addInsert(B2tBillboardPosts::AUTHOR, BUGScontext::getUser()->getUID());
				$crit->addInsert(B2tBillboardPosts::DATE, $_SERVER["REQUEST_TIME"]);
				$crit->addInsert(B2tBillboardPosts::SCOPE, BUGScontext::getScope()->getID());
				$crit->addInsert(B2tBillboardPosts::TARGET_BOARD, (int) BUGScontext::getRequest()->getParameter('post_text_billboard'));
				$crit->addInsert(B2tBillboardPosts::TITLE, $title);
				$crit->addInsert(B2tBillboardPosts::CONTENT, $content);
				$res = B2DB::getTable('B2tBillboardPosts')->doInsert($crit);
				BUGScontext::getModule('publish')->printBillboardPostOnBillboard(new PublishBillboardPost($res->getInsertID()));
			}
		}
		exit();
	}
	
?>