<?php

	$the_folder = ($_SESSION['messages_selected_folder'] != "") ? $_SESSION['messages_selected_folder'] : 1;
	$the_msg = ($_SESSION['messages_selected_msg'] != "") ? $_SESSION['messages_selected_msg'] : "";
	$_SESSION['messages_selected_folder'] = BUGScontext::getRequest()->getParameter('select_folder', $_SESSION['messages_selected_folder']);
	$_SESSION['messages_selected_msg'] = BUGScontext::getRequest()->getParameter('msg_id', $_SESSION['messages_selected_msg']);
	$_SESSION['messages_selected_team'] = BUGScontext::getRequest()->getParameter('team_id', $_SESSION['messages_selected_team']);
	$the_msg = ($_SESSION['messages_selected_msg'] != "") ? $_SESSION['messages_selected_msg'] : "";
	$the_team = ($_SESSION['messages_selected_team'] != "") ? $_SESSION['messages_selected_team'] : "";
	$the_team = ($the_team == 0) ? "" : $the_team;
	$applied_filter = ($_SESSION['messages_filter'] != '') ? $_SESSION['messages_filter'] : '';
	$unread_filter = ($_SESSION['unread_filter'] != '') ? $_SESSION['unread_filter'] : 2;
	
	if ($_SESSION['messages_selected_folder'] != $the_folder)
	{
		$the_msg = "";
		$_SESSION['messages_selected_msg'] = "";
		$_SESSION['messages_selected_team'] = "";
	}
	$the_folder = ($_SESSION['messages_selected_folder'] != "") ? $_SESSION['messages_selected_folder'] : 1;

	if (BUGScontext::getRequest()->getParameter('set_filter'))
	{
		$_SESSION['messages_filter'] = BUGScontext::getRequest()->getParameter('filter');
		$applied_filter = BUGScontext::getRequest()->getParameter('filter');
	}
	
	if (BUGScontext::getRequest()->getParameter('set_unreadfilter'))
	{
		$_SESSION['unread_filter'] = BUGScontext::getRequest()->getParameter('unread_filter');
		$unread_filter = BUGScontext::getRequest()->getParameter('unread_filter');
		if ($unread_filter == 2)
		{
			unset($_SESSION['unread_filter']);
		}
	}
	
	if (BUGScontext::getRequest()->getParameter('move_message'))
	{
		$m_id = BUGScontext::getRequest()->getParameter('msg_id');
		$folder_id = BUGScontext::getRequest()->getParameter('to_folder');
		if (is_numeric($m_id) && is_numeric($folder_id) && $m_id > 0 && $folder_id > 0)
		{
			BUGScontext::getModule('messages')->moveMessage($m_id, $folder_id);
			$_SESSION['messages_selected_msg'] = "";
			$the_msg = "";
		}
	}
	if (BUGScontext::getRequest()->getParameter('add_folder'))
	{
		$folder_name = BUGScontext::getRequest()->getParameter('folder_name');
		if (trim($folder_name) != "")
		{
			$the_folder = BUGScontext::getModule('messages')->addFolder($folder_name);
			$_SESSION['messages_selected_folder'] = $the_folder;
			unset($_SESSION['messages_selected_msg']);
			$the_msg = "";
		}
	}
	if (BUGScontext::getRequest()->getParameter('delete_folder'))
	{
		$folder_id = BUGScontext::getRequest()->getParameter('folder_id');
		if (is_numeric($folder_id) && $folder_id > 4)
		{
			BUGScontext::getModule('messages')->deleteFolder($folder_id);
			$the_folder = 1;
			unset($_SESSION['messages_selected_folder']);
			BUGScontext::getRequest()->setParameter('select_folder', '');
			$the_msg = "";
		}
	}
	if (BUGScontext::getRequest()->getParameter('delete_message'))
	{
		$msg_id = BUGScontext::getRequest()->getParameter('msg_id');
		if (is_numeric($msg_id) && $msg_id > 0)
		{
			BUGScontext::getModule('messages')->deleteMessage($msg_id);
			$the_msg = "";
			$_SESSION['messages_selected_msg'] = "";
		}
	}
	$sel_msg_id = 0;
	if ($the_msg != "")
	{
		if ($the_folder != 2)
		{
			BUGScontext::getModule('messages')->setRead($the_msg, 1);
		}
		try
		{
			$the_msg = BUGScontext::getModule('messages')->getMessages("details", BUGScontext::getUser()->getUID(), $the_folder, $the_msg);
			
			if (count($the_msg) > 0)
			{
				if ($the_msg[0] !== null)
				{
					$sel_msg_id = $the_msg[0]->get(B2tMessages::ID);
				}
			}
			else
			{
				$sel_msg_id = $the_msg->get(B2tMessages::ID);
			}
		}
		catch (Exception $e)
		{
			unset($sel_msg_id);
			$_SESSION['messages_selected_msg'] = '';
		}
	}

	if (is_numeric(BUGScontext::getRequest()->getParameter('set_read')))
	{
		BUGScontext::getModule('messages')->setRead(BUGScontext::getRequest()->getParameter('the_msg'), BUGScontext::getRequest()->getParameter('set_read'));
	}

	$messages_viewmode = BUGScontext::getModule('messages')->getSetting('viewmode', BUGScontext::getUser()->getUID()); // bugs_module_loadSetting("messages", "viewmode", BUGScontext::getUser()->getUID());
	if ($messages_viewmode == '') $messages_viewmode = BUGScontext::getModule('messages')->getSetting('viewmode');
	$message_folders = BUGScontext::getModule('messages')->getFolders(BUGScontext::getUser()->getUID());

?>