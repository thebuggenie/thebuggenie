<?php

	if (!defined('BUGS2_INCLUDE_PATH')) exit();
	
	if (BUGScontext::getRequest()->getParameter('set_sendto'))
	{
		$_SESSION['message_sendto'] = BUGScontext::getRequest()->getParameter('set_sendto');
		if (BUGScontext::getRequest()->getParameter('sendto_team') == 1)
		{
			$_SESSION['message_sendto_team'] = true;
		}
		else
		{
			unset($_SESSION['message_sendto_team']);
		}
	}
	if (BUGScontext::getRequest()->getParameter('clear_sendto'))
	{
		unset($_SESSION['message_sendto']);
		unset($_SESSION['message_sendto_team']);
	}

	$issent = false;

	if (BUGScontext::getRequest()->getParameter('dosendmessage'))
	{
		$isteam = (isset($_SESSION['message_sendto_team']) && $_SESSION['message_sendto_team']) ? 1 : 0;
		$message_title = (BUGScontext::getRequest()->getParameter('message_title') && trim(BUGScontext::getRequest()->getParameter('message_title')) != "") ? BUGScontext::getRequest()->getParameter('message_title') : __("No subject");
		BUGScontext::getModule('messages')->sendMessage($_SESSION['message_sendto'], $isteam, $message_title, BUGScontext::getRequest()->getParameter('message_content'));
		$issent = true;
	}

?>