<?php

	if (!defined('THEBUGGENIE_PATH')) exit();
	
	if (TBGContext::getRequest()->getParameter('set_sendto'))
	{
		$_SESSION['message_sendto'] = TBGContext::getRequest()->getParameter('set_sendto');
		if (TBGContext::getRequest()->getParameter('sendto_team') == 1)
		{
			$_SESSION['message_sendto_team'] = true;
		}
		else
		{
			unset($_SESSION['message_sendto_team']);
		}
	}
	if (TBGContext::getRequest()->getParameter('clear_sendto'))
	{
		unset($_SESSION['message_sendto']);
		unset($_SESSION['message_sendto_team']);
	}

	$issent = false;

	if (TBGContext::getRequest()->getParameter('dosendmessage'))
	{
		$isteam = (isset($_SESSION['message_sendto_team']) && $_SESSION['message_sendto_team']) ? 1 : 0;
		$message_title = (TBGContext::getRequest()->getParameter('message_title') && trim(TBGContext::getRequest()->getParameter('message_title')) != "") ? TBGContext::getRequest()->getParameter('message_title') : __("No subject");
		TBGContext::getModule('messages')->sendMessage($_SESSION['message_sendto'], $isteam, $message_title, TBGContext::getRequest()->getParameter('message_content'));
		$issent = true;
	}

?>