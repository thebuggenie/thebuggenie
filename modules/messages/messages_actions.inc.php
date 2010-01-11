<?php

	$the_folder = ($_SESSION['messages_selected_folder'] != "") ? $_SESSION['messages_selected_folder'] : 1;
	$the_msg = ($_SESSION['messages_selected_msg'] != "") ? $_SESSION['messages_selected_msg'] : "";
	$_SESSION['messages_selected_folder'] = TBGContext::getRequest()->getParameter('select_folder', $_SESSION['messages_selected_folder']);
	$_SESSION['messages_selected_msg'] = TBGContext::getRequest()->getParameter('msg_id', $_SESSION['messages_selected_msg']);
	$_SESSION['messages_selected_team'] = TBGContext::getRequest()->getParameter('team_id', $_SESSION['messages_selected_team']);
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

	if (TBGContext::getRequest()->getParameter('set_filter'))
	{
		$_SESSION['messages_filter'] = TBGContext::getRequest()->getParameter('filter');
		$applied_filter = TBGContext::getRequest()->getParameter('filter');
	}
	
	if (TBGContext::getRequest()->getParameter('set_unreadfilter'))
	{
		$_SESSION['unread_filter'] = TBGContext::getRequest()->getParameter('unread_filter');
		$unread_filter = TBGContext::getRequest()->getParameter('unread_filter');
		if ($unread_filter == 2)
		{
			unset($_SESSION['unread_filter']);
		}
	}
	
	if (TBGContext::getRequest()->getParameter('move_message'))
	{
		$m_id = TBGContext::getRequest()->getParameter('msg_id');
		$folder_id = TBGContext::getRequest()->getParameter('to_folder');
		if (is_numeric($m_id) && is_numeric($folder_id) && $m_id > 0 && $folder_id > 0)
		{
			TBGContext::getModule('messages')->moveMessage($m_id, $folder_id);
			$_SESSION['messages_selected_msg'] = "";
			$the_msg = "";
		}
	}
	if (TBGContext::getRequest()->getParameter('add_folder'))
	{
		$folder_name = TBGContext::getRequest()->getParameter('folder_name');
		if (trim($folder_name) != "")
		{
			$the_folder = TBGContext::getModule('messages')->addFolder($folder_name);
			$_SESSION['messages_selected_folder'] = $the_folder;
			unset($_SESSION['messages_selected_msg']);
			$the_msg = "";
		}
	}
	if (TBGContext::getRequest()->getParameter('delete_folder'))
	{
		$folder_id = TBGContext::getRequest()->getParameter('folder_id');
		if (is_numeric($folder_id) && $folder_id > 4)
		{
			TBGContext::getModule('messages')->deleteFolder($folder_id);
			$the_folder = 1;
			unset($_SESSION['messages_selected_folder']);
			TBGContext::getRequest()->setParameter('select_folder', '');
			$the_msg = "";
		}
	}
	if (TBGContext::getRequest()->getParameter('delete_message'))
	{
		$msg_id = TBGContext::getRequest()->getParameter('msg_id');
		if (is_numeric($msg_id) && $msg_id > 0)
		{
			TBGContext::getModule('messages')->deleteMessage($msg_id);
			$the_msg = "";
			$_SESSION['messages_selected_msg'] = "";
		}
	}
	$sel_msg_id = 0;
	if ($the_msg != "")
	{
		if ($the_folder != 2)
		{
			TBGContext::getModule('messages')->setRead($the_msg, 1);
		}
		try
		{
			$the_msg = TBGContext::getModule('messages')->getMessages("details", TBGContext::getUser()->getUID(), $the_folder, $the_msg);
			
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

	if (is_numeric(TBGContext::getRequest()->getParameter('set_read')))
	{
		TBGContext::getModule('messages')->setRead(TBGContext::getRequest()->getParameter('the_msg'), TBGContext::getRequest()->getParameter('set_read'));
	}

	$messages_viewmode = TBGContext::getModule('messages')->getSetting('viewmode', TBGContext::getUser()->getUID()); // bugs_module_loadSetting("messages", "viewmode", TBGContext::getUser()->getUID());
	if ($messages_viewmode == '') $messages_viewmode = TBGContext::getModule('messages')->getSetting('viewmode');
	$message_folders = TBGContext::getModule('messages')->getFolders(TBGContext::getUser()->getUID());

?>