<?php

	$tbg_response->setTitle(__('Configure users, teams and groups'));
	$tbg_response->addJavascript('config/teamgroups_ajax.js');
	$tbg_response->addJavascript('config/permissions.js');

?>
<table style="table-layout: fixed; width: 100%" cellpadding=0 cellspacing=0>
	<tr>
		<?php
		
			include_component('configleftmenu', array('selected_section' => 2));
		
		?>
		<td valign="top">
			<div class="configheader" style="width: 750px;"><?php echo __('Configure users, teams and groups'); ?></div>
			<div style="width: 750px; clear: both; height: 30px;" class="tab_menu">
				<ul id="usersteamsgroups_menu">
					<li id="tab_users" class="selected"><?php echo javascript_link_tag(image_tag('cfg_icon_users.png', array('style' => 'float: left; margin-right: 5px;')) . __('Users'), array('onclick' => "switchSubmenuTab('tab_users', 'usersteamsgroups_menu');")); ?></li>
					<li id="tab_groups"><?php echo javascript_link_tag(image_tag('cfg_icon_teamgroups.png', array('style' => 'float: left; margin-right: 5px;')) . __('Groups'), array('onclick' => "switchSubmenuTab('tab_groups', 'usersteamsgroups_menu');failedMessage('".__('This configuration section has not been completed yet')."');")); ?></li>
					<li id="tab_teams"><?php echo javascript_link_tag(image_tag('cfg_icon_teamgroups.png', array('style' => 'float: left; margin-right: 5px;')) . __('Teams'), array('onclick' => "switchSubmenuTab('tab_teams', 'usersteamsgroups_menu');failedMessage('".__('This configuration section has not been completed yet')."');")); ?></li>
				</ul>
			</div>
			<div id="usersteamsgroups_menu_panes">
				<div id="tab_users_pane" style="padding-top: 0; width: 750px;">
					<div class="rounded_box mediumgrey_borderless" style="margin-top: 5px;">
						<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
						<div class="xboxcontent" style="padding: 0 5px 5px 5px;">
						<table cellpadding="0" cellspacing="0" border="0">
							<tr>
								<td style="padding: 3px;" rowspan="2"><label><?php echo __('Show user(s)'); ?>:</label></td>
								<td style="padding: 3px; font-size: 12px;">
									<?php foreach (range('A', 'Z') as $letter): ?>
										<?php echo javascript_link_tag($letter, array('onclick' => "showUsers('".make_url('configure_users_find_user')."', '{$letter}');")); ?> |
									<?php endforeach; ?>
									<?php echo javascript_link_tag('0-9', array('onclick' => "showUsers('".make_url('configure_users_find_user')."', '0-9');")); ?> |
									<?php echo javascript_link_tag('ALL', array('onclick' => "showUsers('".make_url('configure_users_find_user')."', 'all');")); ?>
								</td>
							</tr>
							<tr>
								<td style="padding: 3px; font-size: 12px;">
									<?php echo javascript_link_tag('Unactivated users', array('onclick' => "showUsers('".make_url('configure_users_find_user')."', 'unactivated');")); ?> |
									<?php echo javascript_link_tag('New users', array('onclick' => "showUsers('".make_url('configure_users_find_user')."', 'newusers');")); ?>
								</td>
							</tr>
							<tr>
								<td style="padding: 3px;"><label for="findusers"><?php echo __('Find user(s)'); ?>:</label></td>
								<td style="padding: 3px;">
									<form action="<?php echo make_url('configure_users_find_user'); ?>" method="post" onsubmit="showUsers('<?php echo make_url('configure_users_find_user'); ?>', $('findusers').getValue());return false;">
										<input type="text" name="findusers" id="findusers" style="width: 300px;">&nbsp;<input type="submit" value="<?php echo __('Find'); ?>" style="font-size: 12px; font-weight: bold;">
									</form>
								</td>
							</tr>
							<tr id="adduser_div">
								<td style="padding: 3px;"><label for="adduser"><?php echo __('Enter username'); ?>:</label></td>
								<td style="padding: 3px;">
									<form action="<?php echo make_url('configure_users_add_user'); ?>" method="post" onsubmit="showUsers('<?php echo make_url('configure_users_find_user'); ?>', $('findusers').getValue());return false;">
										<input type="text" name="findusers" id="findusers" style="width: 300px;">&nbsp;<input type="submit" value="<?php echo __('Find'); ?>" style="font-size: 12px; font-weight: bold;">
									</form>
								</td>
							</tr>
						</table>
						</div>
						<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
					</div>
					<div style="padding: 10px 0 10px 0; display: none;" id="find_users_indicator"><span style="float: left;"><?php echo image_tag('spinning_16.gif'); ?></span>&nbsp;<?php echo __('Please wait'); ?></div>
					<div id="users_results"></div>
				</div>
				<div id="tab_groups_pane" style="display: none;">
					<p class="faded_medium" style="font-size: 12px; padding-top: 5px;"><?php echo __('This configuration section has not been completed yet'); ?></p>
				</div>
				<div id="tab_teams_pane" style="display: none;">
					<p class="faded_medium" style="font-size: 12px; padding-top: 5px;"><?php echo __('This configuration section has not been completed yet'); ?></p>
				</div>
			</div>
		</td>
	</tr>
</table>