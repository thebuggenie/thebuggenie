<?php

	define('THEBUGGENIE_PATH', '../../');
	$page = "newmessage";
	$stripmode = true;
	$striptitle = "Write a new message";
	
	require THEBUGGENIE_PATH . 'include/checkcookie.inc.php';
	require THEBUGGENIE_PATH . "include/b2_engine.inc.php";
	
	require BUGScontext::getIncludePath() . "include/ui_functions.inc.php";

	require BUGScontext::getIncludePath() . 'modules/messages/newmessage_logic.inc.php';
	require BUGScontext::getIncludePath() . "include/header.inc.php";
	require BUGScontext::getIncludePath() . "include/menustrip.inc.php";

?>
</table>
<div style="padding: 3px; width: auto;">
<div style="width: 100%;">
<?php

	if ($issent)
	{
		?><div style="padding: 15px;">
		<b><?php echo __('Your message has been sent'); ?></b><br>
		<?php echo __('To send another message, search for a user or a team below.'); ?>
		</div>
		<?php
	}

?>
<table style="border: 1px solid #DDD; width: 100%;" cellpadding=0 cellspacing=0>
<tr>
<td style="background-color: #F1F1F1; width: 100px; padding: 5px;"><b><?php echo __('Send message to'); ?></b></td>
<td style="background-color: #F1F1F1; width: auto; padding: 5px;">
<?php

	if (BUGScontext::getRequest()->getParameter('message_findsendto'))
	{
		?>
		<div style="border-bottom: 1px solid #DDD; padding: 2px;"><b><?php echo __('Users found:'); ?></b></div>
		<div style="padding: 2px; padding-left: 4px;">
		<?php
		$matchUsers = array();
		$matchUsers = BUGSuser::getUsers(BUGScontext::getRequest()->getParameter('message_findsendto'), false, true);
		if ($matchUsers != false)
		{
			foreach ($matchUsers as $aUser)
			{
				$aUser = new BUGSuser($aUser['id']);
				?>
				<a href="newmessage.php?set_sendto=<?php print $aUser->getID(); ?>"><?php echo $aUser; ?></a><br>
				<?php
			}
			?><div style="padding: 4px; padding-left: 0px;"><?php echo __('Click on a user to select him/her'); ?></div><?php
		}
		else
		{
			?><div style="color: #AAA; padding: 2px;"><?php echo __('No users found, please try again'); ?></div>
			<?php
		}
		?>
		</div>
		<div style="border-bottom: 1px solid #DDD; padding: 2px;"><b><?php echo __('Teams found:') ?></b></div>
		<div style="padding: 2px; padding-left: 4px;">
		<?php
			$allTeams = BUGSteam::getAll(BUGScontext::getRequest()->getParameter('message_findsendto'));
			foreach ($allTeams as $aTeam)
			{
				$aTeam = BUGSfactory::teamLab($aTeam['id']);
				?>
				<a href="newmessage.php?set_sendto=<?php print $aTeam->getID(); ?>&amp;sendto_team=1"><?php print $aTeam->getName(); ?></a><br>
				<?php
			}
			if (count($allTeams) == 0)
			{
				?>
				<div style="color: #AAA; padding: 2px;"><?php echo __('No teams found, please try again'); ?></div>
				<?php
			}
			else
			{
				?><div style="padding: 4px; padding-left: 0px;"><?php echo __('Click on a team to select it'); ?></div><?php
			}
		?>
		</div>
		<div style="margin-top: 5px; border-bottom: 1px solid #DDD; padding: 2px;"><b><?php echo __('Search again'); ?></b></div>
		<?php
	}
	elseif (!isset($_SESSION['message_sendto']) || $issent)
	{
		?>
		<div style="padding: 2px;"><?php echo __('Enter any detail to search for a user or a team'); ?></div>
		<?php
	}

	if (!isset($_SESSION['message_sendto']) || $issent)
	{
		?>
		<form accept-charset="<?php echo BUGScontext::getI18n()->getCharset(); ?>" action="newmessage.php" enctype="multipart/form-data" method="post" name="message_setsendto">
		<input type="hidden" name="clear_sendto" value="true">
		<table cellpadding=0 cellspacing=0 style="width: 100%;">
		<tr>
			<td style="width: auto; padding: 2px;"><input type="text" style="width: 100%;" name="message_findsendto" value="<?php print BUGScontext::getRequest()->getParameter('message_findsendto', ''); ?>"></td>
			<td style="width: 40px; padding: 2px;"><input type="submit" value="<?php echo __('Find'); ?>" style="width: 100%;"></td>
		</tr>
		</table>
		</form>
		</div>
		</td>
		</tr>
		</table>
		</div>
		<?php
	}
	else
	{

		if (isset($_SESSION['message_sendto_team']) && $_SESSION['message_sendto_team'])
		{
			$theTeam = BUGSfactory::teamLab($_SESSION['message_sendto']);
			print $theTeam->getName() . " (team message)";
		}
		else
		{
			$theUser = BUGSfactory::userLab($_SESSION['message_sendto']);
			print $theUser;
		}

		?>
		<td style="width: 40px; font-size: 10px; background-color: #F1F1F1;"><a href="newmessage.php?clear_sendto=true"><?php echo __('Change'); ?></a></td>
		</tr>
		<form accept-charset="<?php echo BUGScontext::getI18n()->getCharset(); ?>" action="newmessage.php" enctype="multipart/form-data" method="post" name="message_sendto_done">
		<input type="hidden" name="dosendmessage" value="true">
		<tr>
		<td style="background-color: #F1F1F1; padding: 5px;"><b><?php echo __('Title'); ?></b></td>
		<td style="background-color: #F1F1F1; padding: 5px;" colspan=2><input type="text" name="message_title" value="<?php print BUGScontext::getRequest()->getParameter('set_title', ''); ?>" style="width: 100%;"></td>
		</tr>
		<tr>
		<td style="background-color: #F1F1F1; padding: 5px; text-align: left;" colspan=3><b><?php echo __('Message'); ?></b><br>
		<?php

			echo bugs_newTextArea("message_content", "280px", "100%");

		?>
		</div>
		</td>
		</tr>
		</table>
		<table style="border: 1px solid #DDD; border-top: 0px; width: 100%;" cellpadding=0 cellspacing=0>
		<tr>
		<td style="background-color: #F1F1F1; width: auto; padding: 5px;"><?php echo __('When you are done, click the "Send message" button to the right'); ?></td>
		<td style="background-color: #F1F1F1; width: 100px; padding: 5px;"><input type="submit" value="<?php echo __('Send message'); ?>" style="width: 100%"></td>
		</tr>
		</table>
		</div>
		<?php

	}

?>
