<?php

	// TODO: AJAXify messages (including auto-refresh for messages)
	// TODO: (AJAX) Regularly updating information in message central (front page)	

	$page = 'messages';
	define('THEBUGGENIE_PATH', '../../');
	
	require THEBUGGENIE_PATH . 'include/checkcookie.inc.php';
	require THEBUGGENIE_PATH . "include/b2_engine.inc.php";
	
	require TBGContext::getIncludePath() . "include/ui_functions.inc.php";

	TBGContext::getModule('messages')->activate();
	
	require TBGContext::getIncludePath() . "modules/messages/messages_actions.inc.php";
	require TBGContext::getIncludePath() . "include/header.inc.php";
	require TBGContext::getIncludePath() . "include/menu.inc.php";

?>
<table style="width: 100%; table-layout: fixed;" cellpadding=0 cellspacing=0>
<tr>
<td style="width: 250px; padding: 3px; padding-right: 0px;"<?php print ($messages_viewmode == "1") ? " rowspan=2" : ""; ?> valign="top">
<div style="border-bottom: 1px solid #DDD; background-color: #ECECEC; font-weight: bold; padding: 2px;"><?php echo __('Folders'); ?></div>
<div style="background-color: #F5F5F5; border-bottom: 1px solid #E5E5E5; padding: 3px;">
<table cellpadding=0 cellspacing=0 style="width: 100%; height: 27px;">
<tr>
<td style="width: 100px;" class="menu_item" onmouseover="this.className='menu_item_hover'" onmouseout="this.className='menu_item'" alt="<?php echo __('Opens up a new window, where you can compose a new message'); ?>" title="<?php echo __('Opens up a new window, where you can compose a new message'); ?>">
<a href="javascript:void(0);" onclick="<?php print MESSAGES_NEWMSGWINDOWSTRING; ?>">
<table cellpadding=0 cellspacing=0 style="width: 100%;">
<tr>
<td style="width: 20px;"><?php echo image_tag('messages_msg_new.png'); ?></td>
<td style="width: auto; text-align: center;"><?php echo __('New message'); ?></td>
</tr>
</table>
</a>
</td>
<td style="width: auto;">&nbsp;</td>
<td style="width: 85px;" class="menu_item" onclick="showHide('messages_newfolder');" onmouseover="this.className='menu_item_hover'" onmouseout="this.className='menu_item'">
<table cellpadding=0 cellspacing=0 style="width: 100%;">
<tr>
<td style="width: 20px;"><?php echo image_tag('messages_msg_newfolder.png'); ?></td>
<td style="width: auto; text-align: center;"><?php echo __('New folder'); ?></td>
</tr>
</table>
</td>
</tr>
</table>
<div id="messages_newfolder" style="display: none; width: 215px; font-weight: normal; text-align: left; border: 1px solid #DDD; background-color: #F1F1F1; position: absolute; margin-top: 2px; margin-left: 150px;">
<div style="background-color: #E5E5E5; margin: 2px; padding: 3px;"><b><?php echo __('Add folder'); ?></b></div>
<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="messages.php" method="post">
<input type="hidden" name="add_folder" value="true">
<input type="hidden" name="select_folder" value="<?php print TBGContext::getRequest()->getParameter('select_folder'); ?>">
<table cellpadding=0 cellspacing=0 style="width: 210px; margin: 2px;">
<tr><td style="width: 170px; padding: 3px;"><?php echo __('Enter the name of the new folder'); ?></td><td style="width: auto;">&nbsp;</td></tr>
<tr><td style="width: 170px;"><input type="text" name="folder_name" value="" style="width: 100%;"></td><td style="width: auto; text-align: right;"><input type="submit" value="<?php echo __('Add'); ?>" style="width: 30px;"></td></tr>
</table>
</form>
</div>
</div>
<div style="padding-top: 5px; width: 245px;">
<table cellpadding=0 cellspacing=0 style="width: 100%;">
<?php

	$messagecnt = TBGContext::getModule('messages')->countMessages(1);
	$teammessagecnt = TBGContext::getModule('messages')->countMessages(4);

?>
<tr<?php print ($the_folder == 1) ? " style=\"background-color: #F5F5F5;\"" : ""; ?>>
<td style="width: 20px; padding: 2px;"><?php echo image_tag('messages_folder_inbox.png'); ?></td>
<td style="width: auto;<?php print ($the_folder == 1) ? " font-weight: bold;" : ""; ?>" colspan=2><a href="messages.php?select_folder=1"><?php echo __('Inbox'); ?></a></td>
<td style="width: 40px; padding-right: 2px; text-align: right;"><?php print $messagecnt['total']; print ($messagecnt['unread'] >= 1) ? "<b> (" . $messagecnt['unread'] . ")</b>" : ""; ?></td>
</tr>
<?php if (count(TBGContext::getUser()->getTeams()) > 0): ?>
<tr<?php print ($the_folder == 4 && $the_team == "") ? " style=\"background-color: #F5F5F5;\"" : ""; ?>>
<td style="width: 20px; padding: 2px;"><?php echo image_tag('messages_folder_teaminbox.png'); ?></td>
<td style="width: auto;<?php print ($the_folder == 4 && $the_team == "") ? " font-weight: bold;" : ""; ?>" colspan=2><a href="messages.php?select_folder=4&amp;team_id=0"><?php echo __('Team inbox'); ?></a></td>
<td style="width: 40px; padding-right: 2px; text-align: right;"><?php print $teammessagecnt['total']; print ($teammessagecnt['unread'] >= 1) ? "<b> (" . $teammessagecnt['unread'] . ")</b>" : ""; ?></td>
</tr>
<?php endif; ?>
<?php

	foreach (TBGContext::getUser()->getTeams() as $tid)
	{
		$thisteam = TBGFactory::teamLab($tid);
		$thisteammsgcnt = TBGContext::getModule('messages')->countMessages(4, $tid);

		?>
		<tr<?php print ($the_folder == 4 && $the_team == $tid) ? " style=\"background-color: #F5F5F5;\"" : ""; ?>>
		<td>&nbsp;</td>
		<td style="width: 20px; padding: 2px;"><?php echo image_tag('icon_team.png'); ?></td>
		<td style="width: auto;<?php print ($the_folder == 4 && $the_team == $tid) ? " font-weight: bold;" : ""; ?>"><a href="messages.php?select_folder=4&amp;team_id=<?php print $tid; ?>"><?php print $thisteam->getName(); ?></a></td>
		<td style="width: 40px; padding-right: 2px; text-align: right;"><?php print $thisteammsgcnt['total']; print ($thisteammsgcnt['unread'] >= 1) ? "<b> (" . $thisteammsgcnt['unread'] . ")</b>" : ""; ?></td>
		</tr>
		<?php
	}

?>
<tr<?php print ($the_folder == 2) ? " style=\"background-color: #F5F5F5;\"" : ""; ?>>
<td style="width: 20px; padding: 2px;"><?php echo image_tag('messages_folder_sent.png'); ?></td>
<td colspan=2 style="width: auto;<?php print ($the_folder == 2) ? " font-weight: bold;" : ""; ?>"><a href="messages.php?select_folder=2"><?php echo __('Sent messages'); ?></a></td>
</tr>
<?php

	foreach ($message_folders as $aFolder)
	{
		$messagecnt = array();
		$messagecnt = TBGContext::getModule('messages')->countMessages($aFolder['id']);
		?>
		<tr<?php print ($the_folder == $aFolder['id']) ? " style=\"background-color: #F5F5F5;\"" : ""; ?>>
		<td style="width: 20px; padding: 2px;"><?php echo image_tag('messages_folder.png'); ?></td>
		<td colspan=2 style="width: auto;<?php print ($the_folder == $aFolder['id']) ? " font-weight: bold;" : ""; ?>"><a href="messages.php?select_folder=<?php print $aFolder['id']; ?>"><?php print $aFolder['foldername']; ?></a></td>
		<td style="width: 40px; padding-right: 2px; text-align: right;"><?php print $messagecnt['total']; print ($messagecnt['unread'] >= 1) ? "<b> (" . $messagecnt['unread'] . ")</b>" : ""; ?></td>
		</tr>
		<?php
	}

?>
</table>
</div>
</td>
<td style="padding: 3px; padding-bottom: 0px; width: <?php print ($messages_viewmode == "1") ? " auto; height: 300px" : "400px; padding-right: 0px"; ?>; padding-left: 0px;" valign="top">
<div style="border-bottom: 1px solid #DDD; background-color: #ECECEC; font-weight: bold; padding: 2px;"><?php echo __('Messages'); ?></div>
<div style="background-color: #F5F5F5; border-bottom: 1px solid #E5E5E5; padding: 3px;">
<table cellpadding=0 cellspacing=0 style="width: 100%; height: 27px;">
<tr>
<td style="width: 30px;">&nbsp;</td>
<?php

	if (is_numeric($the_folder) && $the_folder > 4)
	{
		?>
		<td style="width: 95px;" class="menu_item" onclick="showHide('messages_deletefolder');" onmouseover="this.className='menu_item_hover'" onmouseout="this.className='menu_item'">
		<table cellpadding=0 cellspacing=0 style="width: 100%;">
		<tr>
		<td style="width: 20px;"><?php echo image_tag('messages_folder_delete.png'); ?></td>
		<td style="width: auto; text-align: center;"><?php echo __('Delete folder'); ?></td>
		</tr>
		</table>
		</td>
		<?php
	}

?>
<td style="width: auto;">&nbsp;</td>
<?php if ($messages_viewmode == 1): ?>
	<td style="width: 460px;">
	<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="messages.php" method="post">
	<input type="hidden" name="set_filter" value="true">
	<table cellpadding=0 cellspacing=0 style="width: 100%;">
	<tr>
	<td style="width: 250px; text-align: right;"><b><?php echo __('Find message'); ?></b>&nbsp;(<?php echo __('subject, sender or content'); ?>):</td>
	<td style="width: auto; padding-left: 5px; padding-right: 5px;"><input type="text" name="filter" value="<?php echo $applied_filter; ?>" style="width: 100%; <?php if ($applied_filter != '') echo ' background-color: #488; color: #FFF;' ?>"></td>
	<td style="width: 20px;"><input type="image" src="<?php print TBGContext::getTBGPath() . "themes/" . TBGSettings::getThemeName(); ?>/messages_findmessage.png" style="width: 16px; height: 16px; border: 0px;"></td>
	</tr>
	</table>
	</form>
	</td>
	<td style="width: 160px;">
	<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="messages.php" method="post">
	<input type="hidden" name="set_unreadfilter" value="true">
	<table cellpadding=0 cellspacing=0 style="width: 100%;">
	<tr>
	<td style="width: auto; padding-left: 5px; padding-right: 5px;">
	<select name="unread_filter" style="width: 100%;" onchange="form.submit()">
	<option value=0 <?php if ($unread_filter == 0) echo ' selected'; ?>><?php echo __('Show only unread messages'); ?></option>
	<option value=1 <?php if ($unread_filter == 1) echo ' selected'; ?>><?php echo __('Show only read messages'); ?></option>
	<option value=2 <?php if ($unread_filter == 2) echo ' selected'; ?>><?php echo __('Show all messages'); ?></option>
	</select>
	</td>
	</tr>
	</table>
	</form>
	</td>
<?php endif; ?>
</tr>
</table>
<div id="messages_deletefolder" style="display: none; width: 250px; font-weight: normal; text-align: left; border: 1px solid #DDD; background-color: #F1F1F1; position: absolute; margin-top: 2px; margin-left: 30px;">
<div style="background-color: #E5E5E5; margin: 2px; padding: 3px;"><b><?php echo __('Delete folder'); ?></b></div>
<div style="padding: 5px;"><?php echo __('Are you sure you want to delete this folder?'); ?></div>
<div style="text-align: center; padding: 5px;"><a href="messages.php?delete_folder=true&amp;folder_id=<?php print $the_folder; ?>"><?php echo __('Yes'); ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="javascript:void(0);" onclick="showHide('messages_deletefolder');"><b><?php echo __('No'); ?></b></a></div>
</div>
</div>
<div style="border-left: 1px solid #F1F1F1;<?php print ($messages_viewmode == 2) ? " border-right: 1px solid #F1F1F1;" : ""; ?> <?php echo ($messages_viewmode == 1) ? 'height: 250px;' : 'height: auto;' ?> overflow-y: scroll;">
<table cellpadding=0 cellspacing=0 style="width: 100%;">
<?php

	$all_messages = ($the_team == "") ? TBGContext::getModule('messages')->getMessages("details", TBGContext::getUser()->getUID(), $the_folder) : TBGContext::getModule('messages')->getMessages("details", TBGContext::getUser()->getUID(), $the_folder, 0, $the_team);
	if (count($all_messages) > 0)
	{
		foreach ($all_messages as $aMessage)
		{
			?>
			<tr>
			<td style="text-align: center;<?php print ($sel_msg_id == $aMessage->get(B2tMessages::ID)) ? " background-color: #F5F5F5;" : ""; ?> width: 28px; padding: 2px; border-bottom: 1px solid #F1F1F1;"><a class="image" href="messages.php?set_read=<?php print ($aMessage->get(B2tMessages::IS_READ) == 0) ? 1 : 0; ?>&amp;the_msg=<?php print $aMessage->get(B2tMessages::ID); ?>"><?php echo image_tag('messages_' . (($aMessage->get(B2tTeams::TEAMNAME) != '') ? "team" : "user") . '_msg_' . (($aMessage->get(B2tMessages::IS_READ) == 0) ? "un" : "") . 'read.png'); ?></a></td>
			<td style="<?php print ($sel_msg_id == $aMessage->get(B2tMessages::ID)) ? "background-color: #F5F5F5;" : ""; ?>">
			<a name="msg<?php print $aMessage->get(B2tMessages::ID); ?>"></a>
			<div style="padding: 2px; border-bottom: 1px solid #F1F1F1;"><a href="messages.php?msg_id=<?php print $aMessage->get(B2tMessages::ID); ?>#msg<?php print $aMessage->get(B2tMessages::ID); ?>"><?php print ($aMessage->get(B2tMessages::IS_READ) == 0) ? "<b>" : ""; print tbg__BBDecode($aMessage->get(B2tMessages::TITLE)); print ($aMessage->get(B2tMessages::IS_READ) == 0) ? "</b>" : ""; ?></a>
			<div style="font-size: 10px;"><?php

				print ($the_folder != 2) ? "<b>From:&nbsp;</b>" . $aMessage->get(B2tUsers::BUDDYNAME, B2tMessages::FROM_USER) : (($aMessage->get(B2tTeams::TEAMNAME) == "") ? "<b>To:&nbsp;</b>" . $aMessage->get(B2tUsers::BUDDYNAME, B2tMessages::TO_USER) . "&nbsp;(" . $aMessage->get(B2tUsers::UNAME, B2tMessages::TO_USER) . ")" : "");
				if ($aMessage->get(B2tTeams::TEAMNAME) != '')
				{
					print ($the_folder != 2) ? ",&nbsp;" : "";
					print "<b>To:&nbsp;</b>" . $aMessage->get(B2tTeams::TEAMNAME);
				}

			?>, <?php print tbg__formatTime($aMessage->get(B2tMessages::SENT), 3); ?></div>
			</div>
			</td>
			</tr>
			<?php
		}
	}

?>
</table>
</div>
</td>
<?php

	if ($messages_viewmode == "1")
	{
		?>
		</tr>
		<tr>
		<?php
	}

?>
<td style="width: auto; <?php print ($messages_viewmode != 1) ? "padding-top: 3px;" : ""; ?> padding-right: 3px;" valign="top">
<div style="border-bottom: 1px solid #DDD; background-color: #ECECEC; font-weight: bold; padding: 2px;"><?php echo __('Message details'); ?></div>
<?php

	if (is_array($the_msg) || $messages_viewmode == 2)
	{
		?>
		<div style="background-color: #F5F5F5; border-bottom: 1px solid #E5E5E5; padding: 3px;">
		<table cellpadding=0 cellspacing=0 style="width: 100%; height: 27px;">
		<tr>
		<?php if (is_array($the_msg)): ?>
			<td style="width: 55px;" class="menu_item" onmouseover="this.className='menu_item_hover'" onmouseout="this.className='menu_item'">
			<?php
	
				$replystring = MESSAGES_NEWMSGWINDOWSTRING_TOUSER_STRING;
				$replystring = str_replace("{uid}", $the_msg[0]->get(B2tMessages::FROM_USER) . "&amp;set_title=RE: " . tbg__BBDecode($the_msg[0]->get(B2tMessages::TITLE)), $replystring);
	
			?>
			<a href="javascript:void(0);" onclick="<?php print $replystring; ?>">
			<table cellpadding=0 cellspacing=0 style="width: 100%;">
			<tr>
			<td style="width: 20px;"><?php echo image_tag('messages_msg_reply.png'); ?></td>
			<td style="width: auto; text-align: center;"><?php echo __('Reply'); ?></td>
			</tr>
			</table>
			</a>
			</td>
			<td style="width: 10px;">&nbsp;</td>
			<td style="width: 60px;" class="menu_item" onmouseover="this.className='menu_item_hover'" onmouseout="this.className='menu_item'" onclick="showHide('messages_deletemessage');">
			<table cellpadding=0 cellspacing=0 style="width: 100%;">
			<tr>
			<td style="width: 20px;"><?php echo image_tag('messages_msg_delete.png'); ?></td>
			<td style="width: auto; text-align: center;"><?php echo __('Delete'); ?></td>
			</tr>
			</table>
			</td>
			<td style="width: 10px;">&nbsp;</td>
			<td style="width: 55px;" class="menu_item" onmouseover="this.className='menu_item_hover'" onmouseout="this.className='menu_item'" onclick="showHide('messages_movemessage');">
			<table cellpadding=0 cellspacing=0 style="width: 100%;">
			<tr>
			<td style="width: 20px;"><?php echo image_tag('messages_msg_move.png'); ?></td>
			<td style="width: auto; text-align: center;"><?php echo __('Move'); ?></td>
			</tr>
			</table>
			</td>
		<?php endif; ?>
		<td style="width: auto;">&nbsp;</td>
		<?php if ($messages_viewmode == 2): ?>
			<td style="width: 460px;">
			<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="messages.php" method="post">
			<input type="hidden" name="set_filter" value="true">
			<table cellpadding=0 cellspacing=0 style="width: 100%;">
			<tr>
			<td style="width: 250px; text-align: right;"><b><?php echo __('Find message'); ?></b>&nbsp;(<?php echo __('subject, sender or content'); ?>):</td>
			<td style="width: auto; padding-left: 5px; padding-right: 5px;"><input type="text" name="filter" value="<?php echo $applied_filter; ?>" style="width: 100%; <?php if ($applied_filter != '') echo ' background-color: #488; color: #FFF;' ?>"></td>
			<td style="width: 20px;"><input type="image" src="<?php print TBGContext::getTBGPath() . "themes/" . TBGSettings::getThemeName(); ?>/messages_findmessage.png" style="width: 16px; height: 16px; border: 0px;"></td>
			</tr>
			</table>
			</form>
			</td>
			<td style="width: 160px;">
			<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="messages.php" method="post">
			<input type="hidden" name="set_unreadfilter" value="true">
			<table cellpadding=0 cellspacing=0 style="width: 100%;">
			<tr>
			<td style="width: auto; padding-left: 5px; padding-right: 5px;">
			<select name="unread_filter" style="width: 100%;" onchange="form.submit()">
			<option value=0 <?php if ($unread_filter == 0) echo ' selected'; ?>><?php echo __('Show only unread messages'); ?></option>
			<option value=1 <?php if ($unread_filter == 1) echo ' selected'; ?>><?php echo __('Show only read messages'); ?></option>
			<option value=2 <?php if ($unread_filter == 2) echo ' selected'; ?>><?php echo __('Show all messages'); ?></option>
			</select>
			</td>
			</tr>
			</table>
			</form>
			</td>
		<?php endif; ?>
		</tr>
		</table>
		</div>
		<?php if (is_array($the_msg)): ?>
			<div id="messages_movemessage" style="display: none; width: 250px; font-weight: normal; text-align: left; border: 1px solid #DDD; background-color: #F1F1F1; position: absolute; margin-top: -2px; margin-left: 155px;">
			<div style="background-color: #E5E5E5; margin: 2px; padding: 3px;"><b><?php echo __('Move message to folder'); ?></b></div>
			<div style="padding: 5px;"><?php echo __('Please select which folder you wish to move this message to, from the list below'); ?></div>
			<div style="margin: 2px; padding: 3px; background-color: #FFF; border: 1px solid #DDD;">
			<table cellpadding=0 cellspacing=0 style="width: auto;">
			<tr>
			<td style="width: 20px;"><?php echo image_tag('messages_folder_inbox.png'); ?></td>
			<td style="width: auto;"><a href="messages.php?move_message=true&amp;msg_id=<?php print $the_msg[0]->get(B2tMessages::ID); ?>&amp;to_folder=1"><?php echo __('Inbox'); ?></a></td>
			</tr>
			<?php
	
				foreach ($message_folders as $aFolder)
				{
					?>
					<tr>
					<td style="width: 20px;"><?php echo image_tag('messages_folder.png'); ?></td>
					<td style="width: auto;"><a href="messages.php?move_message=true&amp;msg_id=<?php print $the_msg[0]->get(B2tMessages::ID); ?>&amp;to_folder=<?php print $aFolder['id']; ?>"><?php print $aFolder['foldername']; ?></a></td>
					</tr>
					<?php
				}
	
			?>
			</table>
			</div>
			<div style="text-align: right; padding: 5px; font-size: 9px;"><a href="javascript:void(0);" onclick="showHide('messages_movemessage');"><?php echo __('Cancel'); ?></a></div>
			</div>
			<div id="messages_deletemessage" style="display: none; width: 250px; font-weight: normal; text-align: left; border: 1px solid #DDD; background-color: #F1F1F1; position: absolute; margin-top: 0px; margin-left: 80px;">
			<div style="background-color: #E5E5E5; margin: 2px; padding: 3px;"><b><?php echo __('Delete message'); ?></b></div>
			<div style="padding: 5px;"><?php echo __('Are you sure you want to delete this message?'); ?></div>
			<div style="text-align: center; padding: 5px;"><a href="messages.php?delete_message=true&amp;msg_id=<?php print $the_msg[0]->get(B2tMessages::ID); ?>"><?php echo __('Yes'); ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="javascript:void(0);" onclick="showHide('messages_deletemessage');"><b><?php echo __('No'); ?></b></a></div>
			</div>
			<div style="background-color: #F5F5F5; border-bottom: 1px solid #E5E5E5; padding: 5px;">
			<b><?php echo __('Sent:'); ?>&nbsp;</b><?php print tbg__formatTime($the_msg[0]->get(B2tMessages::SENT), 7); ?><br>
			<table cellpadding=0 cellspacing=0 style="width: 100%;">
			<tr>
			<td style="width: 35px;" valign="middle"><b><?php echo __('From:') ?></b></td>
			<td><table cellpadding=0 cellspacing=0 style="width: 100%;"><?php print tbg__userDropdown($the_msg[0]->get(B2tMessages::FROM_USER)); ?></table></td>
			</tr>
			</table>
			<div style="font-size: 13px; width: auto; padding: 3px; padding-top: 5px; padding-left: 0px;"><b><?php print tbg__BBDecode($the_msg[0]->get(B2tMessages::TITLE)); ?></b></div>
			</div>
			<div style="padding: 5px;">
			<?php
			print tbg__BBDecode($the_msg[0]->get(B2tMessages::BODY));
			?>
			</div>
		<?php endif; ?>
		<?php
	}
	else
	{
		?>
		<div style="padding: 5px; color: #CCC;"><?php echo __('Select a message from the list to display it'); ?></div>
		<?php
	}

?>
</td>
</tr>
</table>

<?php

	require_once TBGContext::getIncludePath() . "include/footer.inc.php";

?>
