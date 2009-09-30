<?php

	if (($access_level != "full" && $access_level != "read") || BUGScontext::getRequest()->getParameter('access_level'))
	{
		bugs_msgbox(false, "", __('You do not have access to this section'));
	}
	else
	{
		
		require_once BUGScontext::getIncludePath() . 'include/config/users_logic.inc.php';
		
		?>
		<table style="width: 100%" cellpadding=0 cellspacing=0>
			<tr>
			<td style="padding-right: 10px;">
				<table class="configstrip" cellpadding=0 cellspacing=0>
					<tr>
						<td class="cleft"><b><?php echo __('Manage users'); ?></b></td>
						<td class="cright">&nbsp;</td>
					</tr>
					<tr>
						<td colspan=2 class="cdesc">
						<?php echo __('From here you can manage users, as well as their individual permissions.'); ?><br>
						<?php echo __('User-related settings are available from the %general_settings% page.', array('%general_settings%' => '<span style="display: float;">' . image_tag('cfg_icon_general.png') . '&nbsp;<a href="config.php?module=core&amp;section=12"><b>' . __('General settings') . '</b></a></span>')) ?>
						<br>
						<?php echo __('To learn more about this configuration page, please refer to the %bugs_online_help%.', array('%bugs_online_help%' => bugs_helpBrowserHelper('usersettings', 'The Bug Genie online help'))); ?>
						</td>
					</tr>
				</table>
				</td>
			</tr>
		</table>
		<table style="width: 100%" cellpadding=0 cellspacing=0>
		<tr>
		<td style="width: <?php print ($addsql == "") ? 520 : 740; ?>px;">
		<div style="padding: 5px; padding-left: 0px;">
		<?php

			if ($theMessage != "")
			{
				?>
				<div style="padding: 5px; background-color: #F1D1D1; border: 1px solid #DBB;"><?php print $theMessage; ?></div>
				<br>
				<?php
			}

		?>
		<div style="padding: 5px; background-color: #F1F1F1; border: 1px solid #DDD;">
		<table style="width: 100%;" cellpadding=0 cellspacing=0 id="add_user_link">
		<tr>
		<td><?php echo __('To add a user, click the "Add user" link'); ?></td>
		<td style="width: 80px; text-align: center;"><a href="javascript:void(0);" onclick="javascript:$('add_user').toggle();$('add_user_link').toggle();"><b><?php echo __('Add user'); ?></b></a></td>
		</tr>
		</table>
		<form accept-charset="<?php echo BUGScontext::getI18n()->getCharset(); ?>" action="config.php" enctype="multipart/form-data" method="post">
		<input type="hidden" name="module" value="core">
		<input type="hidden" name="section" value=2>
		<input type="hidden" name="adduser" value="true">
		<table style="width: 100%; display: none;" cellpadding=0 cellspacing=0 id="add_user">
		<tr>
		<td style="padding: 5px; width: 100px;"><b><?php echo __('Username:'); ?></b></td>
		<td style=""><input type="text" name="uname" value="<?php print BUGScontext::getRequest()->getParameter('uname'); ?>" style="width: 100%;"></td>
		<td style="width: 70px; text-align: center;" rowspan=5><input type="submit" value="<?php echo __('Add user'); ?>"><br>
		<br>
		<a href="javascript:void(0);" onclick="javascript:$('add_user').toggle();$('add_user_link').toggle();" style="font-size: 9px;"><?php echo __('Cancel'); ?></a></td>
		</tr>
		<tr>
		<td style="padding: 5px;"><b><?php echo __('Email address: %email_address%', array('%email_address%' => '')); ?></b></td>
		<td><input type="text" name="email" value="<?php print BUGScontext::getRequest()->getParameter('email'); ?>" style="width: 100%;"></td>
		</tr>
		<tr>
		<td style="padding: 5px;"><b><?php echo __('In group(s):'); ?></b></td>
		<td><select name="group" style="width: 100%;">
		<?php

			foreach (BUGSgroup::getAll() as $aGroup)
			{
				?>
				<option value=<?php print $aGroup->getID(); print ($aGroup->getID() == BUGScontext::getRequest()->getParameter('gid')) ? " selected" : ""; ?>><?php print $aGroup->getName(); ?></option>
				<?php
			}

		?></select></td>
		</tr>
		<tr>
		<td style="padding: 5px;"><b><?php echo __('Real name: %real_name%', array('%real_name%' => '')); ?></b></td>
		<td><input type="text" name="realname" value="<?php print BUGScontext::getRequest()->getParameter('realname'); ?>" style="width: 100%;"></td>
		</tr>
		<tr>
		<td style="padding: 5px;"><b><?php echo __('Friendly name: %buddy_name%', array('%buddy_name%' => '')); ?></b></td>
		<td><input type="text" name="buddyname" value="<?php print BUGScontext::getRequest()->getParameter('buddyname'); ?>" style="width: 100%;"></td>
		</tr>
		</table>
		</form>
		</div>
		<br>
		<form accept-charset="<?php echo BUGScontext::getI18n()->getCharset(); ?>" action="config.php" enctype="multipart/form-data" method="post" name="searchform">
		<?php

			if ($isSearching == true)
			{
				?>
				<table style="width: 100%;" cellpadding=0 cellspacing=0 id="searchSummary">
				<tr>
				<td style="width: auto; padding: 2px; background-color: #F5F5F5;"><?php echo '<b>' . __('Searching for: %searchstring%', array('%searchstring%' => '</b>' . ((strlen($searchTerm) > 1) ? (($searchTerm != "%%") ? "'" . $searchTerm . "'" : __('All users')) : __('users beginning with "%searchterm%"', array('%searchterm%' => $searchTerm))))); ?></td>
				<td style="width: 70px; text-align: right; padding: 2px; background-color: #F5F5F5;" valign="middle"><a href="javascript:void(0);" onclick="javscript:$('searchSummary').toggle();$('searchBox').toggle();"><?php echo __('New search'); ?></a></td>
				</tr>
				</table>
				<?php
			}

		?>
		<table style="width: 100%;<?php if ($isSearching == true) { print " display: none;"; } ?>" cellpadding=0 cellspacing=0 id="searchBox">
		<tr>
		<td valign="middle" style="width: auto; padding: 2px; background-color: #F5F5F5; border-bottom: 1px solid #DDD;" colspan=2>
		<b><?php echo __('Search for'); ?>&nbsp;</b>
		</td>
		</tr>
		<tr>
			<td style="width: auto; padding: 2px; text-align: left;" colspan=2><?php echo __('Enter a username, real name, email address or buddy name to search for.'); ?></td>
		</tr>
		<tr>
		<td valign="middle" style="width: auto; padding: 2px; text-align: right;">
		<input type="hidden" name="module" value="core">
		<input type="hidden" name="section" value="2">
		<input type="text" name="searchfor" value="<?php ($isSearching == true && strlen($searchTerm) > 1 && $searchTerm != "%%") ? print $searchTerm : print ""; ?>" style="width: 440px;">
		</td>
		<td valign="middle" style="width: 70px; padding: 2px; text-align: left;"><input style="width: 100%;" type="submit" value="<?php echo __('Search'); ?>"></td>
		</tr>
		<tr>
		<td colspan=2 style="padding-top: 2px;">
		<?php

			$theLink = "config.php?module=core&amp;section=2&amp;searchfor=";

					print "<a href=\"" . $theLink . "%%\">" . __('ALL') . "</a>&nbsp;|&nbsp;";
					print "<a href=\"" . $theLink . "a\">A</a>&nbsp;|&nbsp;";
					print "<a href=\"" . $theLink . "b\">B</a>&nbsp;|&nbsp;";
					print "<a href=\"" . $theLink . "c\">C</a>&nbsp;|&nbsp;";
					print "<a href=\"" . $theLink . "d\">D</a>&nbsp;|&nbsp;";
					print "<a href=\"" . $theLink . "e\">E</a>&nbsp;|&nbsp;";
					print "<a href=\"" . $theLink . "f\">F</a>&nbsp;|&nbsp;";
					print "<a href=\"" . $theLink . "g\">G</a>&nbsp;|&nbsp;";
					print "<a href=\"" . $theLink . "h\">H</a>&nbsp;|&nbsp;";
					print "<a href=\"" . $theLink . "i\">I</a>&nbsp;|&nbsp;";
					print "<a href=\"" . $theLink . "j\">J</a>&nbsp;|&nbsp;";
					print "<a href=\"" . $theLink . "k\">K</a>&nbsp;|&nbsp;";
					print "<a href=\"" . $theLink . "l\">L</a>&nbsp;|&nbsp;";
					print "<a href=\"" . $theLink . "m\">M</a>&nbsp;|&nbsp;";
					print "<a href=\"" . $theLink . "n\">N</a>&nbsp;|&nbsp;";
					print "<a href=\"" . $theLink . "o\">O</a>&nbsp;|&nbsp;";
					print "<a href=\"" . $theLink . "p\">P</a>&nbsp;|&nbsp;";
					print "<a href=\"" . $theLink . "q\">Q</a>&nbsp;|&nbsp;";
					print "<a href=\"" . $theLink . "r\">R</a>&nbsp;|&nbsp;";
					print "<a href=\"" . $theLink . "s\">S</a>&nbsp;|&nbsp;";
					print "<a href=\"" . $theLink . "t\">T</a>&nbsp;|&nbsp;";
					print "<a href=\"" . $theLink . "u\">U</a>&nbsp;|&nbsp;";
					print "<a href=\"" . $theLink . "v\">V</a>&nbsp;|&nbsp;";
					print "<a href=\"" . $theLink . "w\">W</a>&nbsp;|&nbsp;";
					print "<a href=\"" . $theLink . "x\">X</a>&nbsp;|&nbsp;";
					print "<a href=\"" . $theLink . "y\">Y</a>&nbsp;|&nbsp;";
					print "<a href=\"" . $theLink . "z\">Z</a><br>";

				?>
		</td>
		</table>
		</form>
		<?php

			if ($isSearching == true)
			{
				if (BUGScontext::getRequest()->getParameter('getbyverified'))
				{
				}
				$matchUsers = BUGSuser::getUsers($searchTerm);
				$num_hits = (is_array($matchUsers)) ? count($matchUsers) : 0;
				?>
				<div style="padding-top: 5px; padding-bottom: 5px; background-color: #F1F1F1; border-top: 1px solid #DDD; border-bottom: 1px solid #DDD;">&nbsp;<b><?php echo __('Found %number_of% users', array('%number_of%' => $num_hits)); ?></b></div>
				<?php
				
				if (is_array($matchUsers))
				{
					foreach ($matchUsers as $aUser)
					{
						$aUser = BUGSfactory::userLab($aUser['id']);
						if ($aUser->getID() != BUGScontext::getRequest()->getParameter('uid') && BUGScontext::getRequest()->getParameter('permissions')) 
						{
							continue;
						}
						if ($aUser->isDeleted())
						{
							?><div style="color: #AAA;"><?php
						}
						?><a name="uid_<?php print $aUser->getID(); ?>"></a>
						<div style="padding-top: 5px; padding-bottom: 5px; border-bottom: <?php print (BUGScontext::getRequest()->getParameter('permissions') == true) ? "0px" : "1px solid #DDD"; ?>;">
						<!-- <a href="config.php?module=core&amp;section=2&amp;searchfor=<?php print $searchTerm; ?>"><b><?php print $aUser->getName(); ?></b></a><br> -->
						<?php
	
							if (BUGScontext::getRequest()->getParameter('edituname') == true && BUGScontext::getRequest()->getParameter('uid') == $aUser->getID())
							{
								?>
								<form accept-charset="<?php echo BUGScontext::getI18n()->getCharset(); ?>" action="config.php" enctype="multipart/form-data" method="post">
								<input type="hidden" name="module" value="core">
								<input type="hidden" name="section" value=2>
								<input type="hidden" name="uid" value=<?php print $aUser->getID(); ?>>
								<input type="hidden" name="searchfor" value="<?php print $searchTerm; ?>">
								<input type="hidden" name="saveedituname" value="true">
								<table style="width: 100%;" cellpadding=0 cellspacing=0>
								<tr>
								<td style="padding: 5px; width: 130px; background-color: #F1F1F1; border-left: 1px solid #DDD; border-top: 1px solid #DDD;"><b><?php echo __('Username:'); ?></b></td>
								<td style="background-color: #F1F1F1; border-top: 1px solid #DDD;"><input type="text" name="uname" value="<?php print $aUser->getUname(); ?>" style="width: 100%;"></td>
								<td style="width: 60px; background-color: #F1F1F1; text-align: center; border: 1px solid #DDD; border-left: 0px;" rowspan=6><input type="submit" value="<?php echo __('Save'); ?>"><br>
								<br>
								<a href="config.php?module=core&amp;section=2&amp;searchfor=<?php print $searchTerm; ?>" style="font-size: 9px;"><?php echo __('Cancel'); ?></a></td>
								</tr>
								<tr>
								<td style="padding: 5px; background-color: #F1F1F1; border-left: 1px solid #DDD;"><b><?php echo __('Email:'); ?></b></td>
								<td style="background-color: #F1F1F1;"><input type="text" name="email" value="<?php print $aUser->getEmail(); ?>" style="width: 100%;"></td>
								</tr>
								<tr>
								<td style="padding: 5px; background-color: #F1F1F1; border-left: 1px solid #DDD;"><b><?php echo __('In group(s):') ?></b></td>
								<td style="background-color: #F1F1F1;"><select name="group" style="width: 100%;">
								<?php
	
									foreach (BUGSgroup::getAll() as $aGroup)
									{
										?>
										<option value=<?php print $aGroup->getID(); print ($aGroup->getID() == $aUser->getGroup()->getID()) ? " selected" : ""; ?>><?php print $aGroup->getName(); ?></option>
										<?php
									}
	
								?></select></td>
								</tr>
								<tr>
								<td style="padding: 5px; background-color: #F1F1F1; border-left: 1px solid #DDD;"><b><?php echo __('Team membership:'); ?></b></td>
								<td style="background-color: #F1F1F1;">
								<?php
	
									if (count($aUser->getTeams()) > 0)
									{
										$first = true;
	
										foreach($aUser->getTeams() as $aTeam)
										{
											$aTeam = BUGSfactory::teamLab($aTeam);
											print (!$first) ? ", " : "";
											print $aTeam->getName();
											$first = false;
										}
									}
									else
									{
										?><font style="color: #AAA;"><?php echo __('None'); ?></font><?php
									}
	
									$teams = BUGSteam::getAll();
	
								?><br><a href="javascript:void(0);" onclick="$('addteam').toggle();" style="font-size: 10px;"><?php echo __('Add'); ?></a> | <a href="javascript:void(0);" onclick="$('removeteam').toggle();" style="font-size: 10px;"><?php echo __('Remove'); ?></a>
								<div style="display: none; position: absolute; width: 200px; padding: 5px; border: 1px solid #DDD; background-color: #FFF;" id="addteam">
								<?php
	
									foreach($teams as $aTeam)
									{
										$aTeam = BUGSfactory::teamLab($aTeam['id']);
										?><a href="config.php?module=core&amp;section=2&amp;searchfor=<?php print $searchTerm; ?>&amp;uid=<?php print $aUser->getID(); ?>&amp;edituname=true&amp;addteam=<?php print $aTeam->getID(); ?>#uid_<?php print $aUser->getID(); ?>"><?php print $aTeam->getName(); ?></a><br><?php
									}
	
								?>
								</div>
								<div style="display: none; position: absolute; width: 200px; padding: 5px; border: 1px solid #DDD; background-color: #FFF;" id="removeteam">
								<?php
	
									if (count($aUser->getTeams()) > 0)
									{
										foreach($aUser->getTeams() as $aTeam)
										{
											$aTeam = BUGSfactory::teamLab($aTeam);
											?><a href="config.php?module=core&amp;section=2&amp;searchfor=<?php print $searchTerm; ?>&amp;uid=<?php print $aUser->getID(); ?>&amp;edituname=true&amp;removeteam=<?php print $aTeam->getID(); ?>#uid_<?php print $aUser->getID(); ?>"><?php print $aTeam->getName(); ?></a><br><?php
										}
									}
									else
									{
										?><font style="color: #AAA;"><?php echo __('None'); ?></font><?php
									}
	
								?>
								</div>
								</td>
								</tr>
								<tr>
								<td style="padding: 5px; background-color: #F1F1F1; border-left: 1px solid #DDD;"><b><?php echo __('Real name: %real_name%', array('%real_name%' => '')); ?></b></td>
								<td style="background-color: #F1F1F1;"><input type="text" name="realname" value="<?php print $aUser->getRealname(); ?>" style="width: 100%;"></td>
								</tr>
								<tr>
								<td style="padding: 5px; background-color: #F1F1F1; border-left: 1px solid #DDD; border-bottom: 1px solid #DDD;"><b><?php echo __('Friendly name: %buddy_name%', array('%buddy_name%' => '')); ?></b></td>
								<td style="background-color: #F1F1F1; border-bottom: 1px solid #DDD;"><input type="text" name="buddyname" value="<?php print $aUser->getBuddyname(); ?>" style="width: 100%;"></td>
								</tr>
								<?php
							}
							else
							{
								?>
								<table style="width: 100%;" cellpadding=0 cellspacing=0>
								<tr>
								<td <?php print ($aUser->isDeleted()) ? " style=\"color: #AAA;\"" : ""; ?>><b><?php print $aUser->getUname(); ?></b>&nbsp;(<?php
	
									if ($aUser->getEmail() != "")
									{
										?><a <?php print ($aUser->isDeleted()) ? " style=\"color: #AAA;\"" : ""; ?> href="mailto:<?php print $aUser->getEmail(); ?>"><?php print $aUser->getEmail(); ?></a><?php
									}
									else
									{
										?><font style="color: #AAA;"><?php echo __('No email'); ?></font><?php
									}
								?>)</td>
								<?php
	
									if ($aUser->isDeleted() == false)
									{
										?>
										<td rowspan=4 style="width: 30px; text-align: right;"><a href="config.php?module=core&amp;section=2&amp;searchfor=<?php print $searchTerm; ?>&amp;uid=<?php print $aUser->getID(); ?>&amp;edituname=true#uid_<?php print $aUser->getID(); ?>" style="font-size: 9px;"><?php echo __('Edit'); ?></a></td>
										<?php
									}
	
								?>
								<tr>
								<td <?php print ($aUser->isDeleted()) ? " style=\"color: #AAA;\"" : ""; ?>>
                  <?php if ($aUser->getGroup() instanceof BUGSgroup): ?>
								    <?php print $aUser->getGroup()->getName(); ?>
                  <?php else: ?>
                    <span style="color: #AAA;"><?php echo __('This user is a member of a deleted group'); ?></span>
                  <?php endif; ?>
                </td>
								</tr>
								<tr>
								<td><?php echo ($aUser->getLastSeen() != 0) ? __('Joined %joined_date%, last online %last_online_date%', array('%joined_date%' => bugs_formatTime($aUser->getJoinedDate(), 9), '%last_online_date%' => bugs_formatTime($aUser->getLastSeen(), 9))) : __('Joined %joined_date%, has not logged in yet.', array('%joined_date%' => bugs_formatTime($aUser->getJoinedDate(), 9))); ?></td>
								</tr>
								<tr>
								<td <?php print ($aUser->isDeleted()) ? " style=\"color: #AAA;\"" : ""; ?>><?php echo __('Team membership:'); ?>&nbsp;
								<?php
	
									if (count($aUser->getTeams()) > 0)
									{
										$first = true;
	
										foreach($aUser->getTeams() as $aTeam)
										{
											$aTeam = BUGSfactory::teamLab($aTeam);
											print (!$first) ? ", " : "";
											print $aTeam->getName();
											$first = false;
										}
									}
									else
									{
										?><font style="color: #AAA;"><?php echo __('None'); ?></font><?php
									}
									
								?>
								</td>
								</tr>
								<tr>
								<td style="color: #AAA;"><?php print $aUser->getRealname(); ?> / <?php print $aUser->getBuddyname(); ?></td>
								</tr>
								<?php
							}
	
						?>
						<tr>
						<td style="color: #AAA;" colspan=<?php print (BUGScontext::getRequest()->getParameter('edituname') == true) ? 3 : 2; ?>>
						<?php
	
							if ($aUser->isDeleted())
							{
								echo __('This account has been deleted');
							}
							else
							{
								if (!$aUser->isActivated())
								{
									echo __('This account has not been validated') . '<br>';
								}
								if (!$aUser->isEnabled())
								{
									echo __('This account has been suspended') . '<br>';
								}
							}
	
						?>
						</td>
						</tr>
						</table>
						<?php
	
							if (BUGScontext::getRequest()->getParameter('edituname') == true && BUGScontext::getRequest()->getParameter('uid') == $aUser->getID())
							{
								print "</form>";
							}
	
							if ($aUser->isDeleted())
							{
								?><a href="config.php?module=core&amp;section=2&amp;searchfor=<?php print $searchTerm; ?>&amp;uid=<?php print $aUser->getID(); ?>&amp;restore=true#uid_<?php print $aUser->getID(); ?>" style="font-size: 10px;"><?php echo __('REOPEN ACCOUNT'); ?></a> | <?php
								?><a href="javascript:void(0);" onclick="javascript:$('purge_<?php print $aUser->getID(); ?>').toggle();" style="font-size: 10px;"><?php echo __('DELETE PERMANENTLY'); ?></a><?php
							}
							else
							{
								if (!$aUser->isActivated())
								{
									?><a href="config.php?module=core&amp;section=2&amp;searchfor=<?php print $searchTerm; ?>&amp;uid=<?php print $aUser->getID(); ?>&amp;validate=true#uid_<?php print $aUser->getID(); ?>" style="font-size: 10px;"><?php echo __('VALIDATE'); ?></a> | <?php
								}
								else
								{
									?><a href="config.php?module=core&amp;section=2&amp;searchfor=<?php print $searchTerm; ?>&amp;uid=<?php print $aUser->getID(); ?>&amp;unvalidate=true#uid_<?php print $aUser->getID(); ?>" style="font-size: 10px;"><?php echo __('UNVALIDATE'); ?></a> | <?php
								}
								if (!$aUser->isEnabled())
								{
									?><a href="config.php?module=core&amp;section=2&amp;searchfor=<?php print $searchTerm; ?>&amp;uid=<?php print $aUser->getID(); ?>&amp;enable=true#uid_<?php print $aUser->getID(); ?>" style="font-size: 10px;"><?php echo __('RESTORE'); ?></a> | <?php
								}
								elseif ($aUser->getID() != BUGScontext::getUser()->getUID())
								{
									?><a href="config.php?module=core&amp;section=2&amp;searchfor=<?php print $searchTerm; ?>&amp;uid=<?php print $aUser->getID(); ?>&amp;suspend=true#uid_<?php print $aUser->getID(); ?>" style="font-size: 10px;"><?php echo __('SUSPEND'); ?></a> | <?php
								}
	
								if ($aUser->getID() != BUGScontext::getUser()->getUID())
								{
									?><a href="javascript:void(0);" onclick="javascript:$('pwd_change_<?php print $aUser->getID(); ?>').toggle();" style="font-size: 10px;"><?php echo __('CHANGE PASSWORD'); ?></a> | <?php
								}
	
								if (BUGScontext::getRequest()->getParameter('permissions') != "true")
								{
									?><a href="config.php?module=core&amp;section=2&amp;searchfor=<?php print $searchTerm; ?>&amp;uid=<?php print $aUser->getID(); ?>&amp;permissions=true#uid_<?php print $aUser->getID(); ?>" style="font-size: 10px;"><?php echo __('SET PERMISSIONS'); ?></a> | <?php
								}
								else
								{
									?><a href="config.php?module=core&amp;section=2&amp;searchfor=<?php print $searchTerm; ?>" style="font-size: 10px;"><?php echo __('BACK TO SEARCH RESULTS'); ?></a> | <?php
								}
	
								if ($aUser->getID() != BUGScontext::getUser()->getUID())
								{
									?><a href="javascript:void(0);" onclick="javascript:$('delete_<?php print $aUser->getID(); ?>').toggle();" style="font-size: 10px;"><?php echo __('DELETE'); ?></a><?php
								}
								else
								{
									?><font style="color: #AAA; font-size: 10px;"><?php echo __('THIS IS YOUR ACCOUNT'); ?></font><?php
								}
	
								if (BUGScontext::getRequest()->getParameter('permissions') == true && BUGScontext::getRequest()->getParameter('uid') == $aUser->getID())
								{
									BUGScontext::getRequest()->setParameter('user', "true");
									$theuid = BUGScontext::getRequest()->getParameter('uid');
									$tid = 0;
									$gid = 0;
									$thelink = "config.php?module=core&amp;section=2&amp;searchfor=$searchTerm&amp;uid=" . $aUser->getID() . "&amp;permissions=true";
									require_once BUGScontext::getIncludePath() . 'include/permissions.inc.php';
								}
							}
	
						?>
						</div>
						<?php
	
							if ($md5newPass != "" && BUGScontext::getRequest()->getParameter('uid') == $aUser->getID())
							{
								?>
								<div style="background-color: #F1F1F1; padding: 5px; border-bottom: 1px solid #DDD;" id="newPass">
								<?php echo __('A new password has been saved. The new password is %new_password%', array('%new_password%' => '<b>' . $newPass . '</b>')); ?>&nbsp;&nbsp;[<a href="javascript:void(0);" onclick="$('newPass').toggle();"><?php echo __('Ok'); ?></a>]
								</div>
								<?php
							}
	
							if ($aUser->isDeleted())
							{
								?>
								<div style="color: #555; background-color: #F1F1F1; padding: 5px; border-bottom: 1px solid #DDD; display: none;" id="purge_<?php print $aUser->getID(); ?>">
								<?php echo __('Permanently deleting a user-account will also delete all data posted by this user, including comments and issue reports. Are you sure you want to do this?'); ?><br>
								<br>
								<a href="config.php?module=core&amp;section=2&amp;searchfor=<?php print $searchTerm; ?>&amp;uid=<?php print $aUser->getID(); ?>&amp;purge=true" style="font-size: 10px;"><?php echo __('YES, DELETE PERMANENTLY'); ?></a> | <a href="javascript:void(0);" onclick="javascript:$('purge_<?php print $aUser->getID(); ?>').toggle();" style="font-size: 10px;"><b><?php echo __('NO, DO NOT DELETE'); ?></b></a>
								</div>
								<?php
							}
							else
							{
								?>
								<div style="color: #555; background-color: #F1F1F1; padding: 5px; border-bottom: 1px solid #DDD; display: none;" id="delete_<?php print $aUser->getID(); ?>">
								<?php echo __('Deleting an account will also delete all user-data for this user, including messages, permissions and friends lists. Are you sure you want to do this?'); ?><br>
								<br>
								<?php
	
									if ($aUser->isEnabled())
									{
										?>
										<i>(<?php echo __('If you just want to suspend the account, making it impossible to use - select "SUSPEND"'); ?>)</i><br>
										<br>
										<?php
									}
	
								?>
								<a href="config.php?module=core&amp;section=2&amp;searchfor=<?php print $searchTerm; ?>&amp;uid=<?php print $aUser->getID(); ?>&amp;delete=true#uid_<?php print $aUser->getID(); ?>" style="font-size: 10px;"><?php echo __('YES, DELETE THE ACCOUNT'); ?></a> | <a href="javascript:void(0);" onclick="javascript:$('delete_<?php print $aUser->getID(); ?>').toggle();" style="font-size: 10px;"><b><?php echo __('NO, DO NOT DELETE'); ?></b></a>
								<?php
	
									if ($aUser->isEnabled())
									{
										?> | <a href="config.php?module=core&amp;section=2&amp;searchfor=<?php print $searchTerm; ?>&amp;uid=<?php print $aUser->getID(); ?>&amp;suspend=true#uid_<?php print $aUser->getID(); ?>" style="font-size: 10px;"><?php echo __('NO, SUSPEND THE ACCOUNT INSTEAD'); ?></a><?php
									}
	
								?>
								</div>
								<?php
							}
	
	
	
						?>
						<div style="background-color: #F1F1F1; display: none; border-bottom: 1px solid #DDD;" id="pwd_change_<?php print $aUser->getID(); ?>">
						<table style="width: 100%" cellpadding=0 cellspacing=0>
						<tr>
						<td style="padding: 5px;">
						<div style="border-bottom: 1px solid #DDD;"><b><?php echo __('Specify a new password below'); ?></b></div>
						</td>
						<td style="width: 190px; padding: 5px;">
						<div style="text-align: center; border-bottom: 1px solid #DDD;"><b><?php echo __('or generate a random password'); ?></b></div>
						</td>
						</tr>
						<tr>
						<td style="padding: 5px;">
						<form accept-charset="<?php echo BUGScontext::getI18n()->getCharset(); ?>" action="config.php" enctype="multipart/form-data" method="post">
						<input type="hidden" name="module" value="core">
						<input type="hidden" name="section" value=2>
						<input type="hidden" name="uid" value=<?php print $aUser->getID(); ?>>
						<input type="hidden" name="searchfor" value="<?php print $searchTerm; ?>">
						<table style="width: 100%" cellpadding=0 cellspacing=0>
						<tr>
						<td style="width: 65px;"><?php echo __('Password:'); ?></td>
						<td style="text-align: center;"><input type="password" name="pwd_1" style="width: 90px;"></td>
						<td style="width: 60px; text-align: right;" rowspan=2>
						<input type="submit" value="<?php echo __('Change'); ?>">
						</td>
						</tr>
						<tr>
						<td><?php echo __('Repeat:'); ?></td>
						<td style="text-align: center;"><input type="password" name="pwd_2" style="width: 90px;"></td>
						</tr>
						</table>
						</form>
						</td>
						<td style="text-align: center;" valign="middle">
						<form accept-charset="<?php echo BUGScontext::getI18n()->getCharset(); ?>" action="config.php" enctype="multipart/form-data" method="post">
						<input type="hidden" name="module" value="core">
						<input type="hidden" name="section" value=2>
						<input type="hidden" name="uid" value=<?php print $aUser->getID(); ?>>
						<input type="hidden" name="searchfor" value="<?php print $searchTerm; ?>">
						<input type="hidden" name="randompassword" value="true">
						<input type="submit" value="<?php echo __('Generate new password'); ?>" style="width: 140px;">
						</form>
						</td>
						</table>
						</div>
						<?php
	
						if ($aUser->isDeleted())
						{
							?></div><?php
						}
					}
				}

			}

		?>
		</div>
		</td>
		<td style="width: auto; vertical-align: top;">
		<div style="padding: 5px;">
		</div>
		</td>
		</tr>
		</table>
		<?php // END PERMISSION DOUBLECHECK
	}
?>