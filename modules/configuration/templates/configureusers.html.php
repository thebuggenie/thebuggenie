<?php

	$tbg_response->setTitle(__('Configure users, teams and groups'));
	$users_text = (TBGContext::getScope()->getMaxUsers()) ? __('Users (%num%/%max%)', array('%num%' => '<span id="current_user_num_count">'.TBGUser::getUsersCount().'</span>', '%max%' => TBGContext::getScope()->getMaxUsers())) : __('Users');
	$teams_text = (TBGContext::getScope()->getMaxTeams()) ? __('Teams (%num%/%max%)', array('%num%' => '<span id="current_team_num_count">'.TBGTeam::countAll().'</span>', '%max%' => TBGContext::getScope()->getMaxTeams())) : __('Teams');

?>
<table style="table-layout: fixed; width: 100%" cellpadding=0 cellspacing=0>
	<tr>
		<?php include_component('leftmenu', array('selected_section' => TBGSettings::CONFIGURATION_SECTION_USERS)); ?>
		<td valign="top" style="padding-left: 15px;">
			<div style="width: 788px;">
				<h3><?php echo __('Configure users, teams and groups'); ?></h3>
				<div style="width: 100%; margin-top: 15px; clear: both; height: 30px;" class="tab_menu">
					<ul id="usersteamsgroups_menu">
						<li id="tab_users" class="selected"><?php echo javascript_link_tag(image_tag('cfg_icon_users.png', array('style' => 'float: left; margin-right: 5px;')) . $users_text, array('onclick' => "TBG.Main.Helpers.tabSwitcher('tab_users', 'usersteamsgroups_menu');")); ?></li>
						<li id="tab_groups"><?php echo javascript_link_tag(image_tag('cfg_icon_teamgroups.png', array('style' => 'float: left; margin-right: 5px;')) . __('Groups'), array('onclick' => "TBG.Main.Helpers.tabSwitcher('tab_groups', 'usersteamsgroups_menu');")); ?></li>
						<li id="tab_teams"><?php echo javascript_link_tag(image_tag('cfg_icon_teamgroups.png', array('style' => 'float: left; margin-right: 5px;')) . $teams_text, array('onclick' => "TBG.Main.Helpers.tabSwitcher('tab_teams', 'usersteamsgroups_menu');")); ?></li>
						<li id="tab_clients"><?php echo javascript_link_tag(image_tag('cfg_icon_teamgroups.png', array('style' => 'float: left; margin-right: 5px;')) . __('Clients'), array('onclick' => "TBG.Main.Helpers.tabSwitcher('tab_clients', 'usersteamsgroups_menu');")); ?></li>
					</ul>
				</div>
				<div id="usersteamsgroups_menu_panes">
					<div id="tab_users_pane" style="padding-top: 0; width: 100%;">
						<div class="rounded_box mediumgrey borderless" style="margin-top: 5px; padding: 0 5px 5px 5px;">
						<table cellpadding="0" cellspacing="0" border="0">
							<tr>
								<td style="padding: 3px;" rowspan="2"><label><?php echo __('Show user(s)'); ?>:</label></td>
								<td style="padding: 3px; font-size: 12px;">
									<?php foreach (range('A', 'Z') as $letter): ?>
										<?php echo javascript_link_tag($letter, array('onclick' => "TBG.Config.User.show('".make_url('configure_users_find_user')."', '{$letter}');")); ?> |
									<?php endforeach; ?>
									<?php echo javascript_link_tag('0-9', array('onclick' => "TBG.Config.User.show('".make_url('configure_users_find_user')."', '0-9');")); ?> |
									<?php echo javascript_link_tag('ALL', array('onclick' => "TBG.Config.User.show('".make_url('configure_users_find_user')."', 'all');")); ?>
								</td>
							</tr>
							<tr>
								<td style="padding: 3px; font-size: 12px;">
									<?php echo javascript_link_tag(__('Unactivated users'), array('onclick' => "TBG.Config.User.show('".make_url('configure_users_find_user')."', 'unactivated');")); ?> |
									<?php echo javascript_link_tag(__('New users'), array('onclick' => "TBG.Config.User.show('".make_url('configure_users_find_user')."', 'newusers');")); ?>
								</td>
							</tr>
							<tr>
								<td style="padding: 3px;"><label for="findusers"><?php echo __('Find user(s)'); ?>:</label></td>
								<td style="padding: 3px;">
									<form action="<?php echo make_url('configure_users_find_user'); ?>" method="post" onsubmit="TBG.Config.User.show('<?php echo make_url('configure_users_find_user'); ?>', $('findusers').getValue());return false;">
										<input type="text" name="findusers" id="findusers" style="width: 300px;" value="<?php echo $finduser; ?>">&nbsp;<input type="submit" value="<?php echo __('Find'); ?>" style="font-size: 12px; font-weight: bold;">
									</form>
								</td>
							</tr>
							<tr id="adduser_div"<?php if (!TBGContext::getScope()->hasUsersAvailable()): ?> style="display: none;"<?php endif; ?>>
								<td style="padding: 3px;"><label for="adduser_username"><?php echo __('Enter username'); ?>:</label></td>
								<td style="padding: 3px;">
									<form action="<?php echo make_url('configure_users_add_user'); ?>" method="post" onsubmit="TBG.Config.User.add('<?php echo make_url('configure_users_add_user'); ?>');return false;" id="createuser_form">
										<input type="text" name="username" id="adduser_username" style="width: 300px;">&nbsp;<input type="submit" value="<?php echo __('Create user'); ?>" style="font-size: 12px; font-weight: bold;">
									</form>
								</td>
							</tr>
						</table>
						</div>
						<div style="padding: 10px 0 10px 0; display: none;" id="find_users_indicator"><span style="float: left;"><?php echo image_tag('spinning_16.gif'); ?></span>&nbsp;<?php echo __('Please wait'); ?></div>
						<div id="users_results"></div>
					</div>
					<div id="tab_groups_pane" style="display: none; padding-top: 0; width: 750px;">
						<div class="rounded_box yellow borderless" style="margin-top: 5px; padding: 7px;">
							<form id="create_group_form" action="<?php echo make_url('configure_users_add_group'); ?>" method="post" accept-charset="<?php echo TBGSettings::getCharset(); ?>" onsubmit="createGroup('<?php echo make_url('configure_users_add_group'); ?>');return false;">
								<div id="add_group">
									<label for="group_name"><?php echo __('Create a new group'); ?></label>
									<input type="text" id="group_name" name="group_name">
									<input type="submit" value="<?php echo __('Create'); ?>">
								</div>
							</form>
						</div>
						<table cellpadding=0 cellspacing=0 style="display: none; margin-left: 5px; width: 300px;" id="create_group_indicator">
							<tr>
								<td style="width: 20px; padding: 2px;"><?php echo image_tag('spinning_20.gif'); ?></td>
								<td style="padding: 0px; text-align: left;"><?php echo __('Adding group, please wait'); ?>...</td>
							</tr>
						</table>
						<div id="groupconfig_list">
							<?php foreach ($groups as $group): ?>
								<?php include_template('configuration/groupbox', array('group' => $group)); ?>
							<?php endforeach; ?>
						</div>
					</div>
					<div id="tab_teams_pane" style="display: none; padding-top: 0; width: 750px;">
						<div class="rounded_box yellow borderless" style="margin-top: 5px; padding: 7px;<?php if (!TBGContext::getScope()->hasTeamsAvailable()): ?> display: none;<?php endif; ?>" id="add_team_div">
							<form id="create_team_form" action="<?php echo make_url('configure_users_add_team'); ?>" method="post" accept-charset="<?php echo TBGSettings::getCharset(); ?>" onsubmit="createTeam('<?php echo make_url('configure_users_add_team'); ?>');return false;">
								<label for="team_name"><?php echo __('Create a new team'); ?></label>
								<input type="text" id="team_name" name="team_name">
								<input type="submit" value="<?php echo __('Create'); ?>">
							</form>
						</div>
						<table cellpadding=0 cellspacing=0 style="display: none; margin-left: 5px; width: 300px;" id="create_team_indicator">
							<tr>
								<td style="width: 20px; padding: 2px;"><?php echo image_tag('spinning_20.gif'); ?></td>
								<td style="padding: 0px; text-align: left;"><?php echo __('Adding team, please wait'); ?>...</td>
							</tr>
						</table>
						<div id="teamconfig_list">
							<?php foreach ($teams as $team): ?>
								<?php include_template('configuration/teambox', array('team' => $team)); ?>
							<?php endforeach; ?>
						</div>
					</div>
					<div id="tab_clients_pane" style="display: none; padding-top: 0; width: 750px;">
						<div class="rounded_box yellow borderless" style="margin-top: 5px; padding: 7px;">
							<form id="create_client_form" action="<?php echo make_url('configure_users_add_client'); ?>" method="post" accept-charset="<?php echo TBGSettings::getCharset(); ?>" onsubmit="createClient('<?php echo make_url('configure_users_add_client'); ?>');return false;">
								<div id="add_client">
									<label for="client_name"><?php echo __('Create a new client'); ?></label>
									<input type="text" id="client_name" name="client_name">
									<input type="submit" value="<?php echo __('Create'); ?>">
								</div>
							</form>
							<?php echo __('You can set other details, such as an email address or telephone number, after creating the client.'); ?>
						</div>
						<table cellpadding=0 cellspacing=0 style="display: none; margin-left: 5px; width: 300px;" id="create_client_indicator">
							<tr>
								<td style="width: 20px; padding: 2px;"><?php echo image_tag('spinning_20.gif'); ?></td>
								<td style="padding: 0px; text-align: left;"><?php echo __('Adding client, please wait'); ?>...</td>
							</tr>
						</table>
						<div id="clientconfig_list">
							<?php foreach ($clients as $client): ?>
								<?php include_template('configuration/clientbox', array('client' => $client)); ?>
							<?php endforeach; ?>
						</div>
					</div>
				</div>
			</div>
		</td>
	</tr>
</table>
<?php if ($finduser): ?>
	<script type="text/javascript">
		Event.observe(window, 'load', function() {
			TBG.Config.User.show('<?php echo make_url('configure_users_find_user'); ?>', '<?php echo $finduser; ?>');
		});
	</script>
<?php endif; ?>