<div class="rounded_box mediumgrey borderless" style="padding: 0; margin-top: 5px;" id="teambox_<?php echo $team->getID(); ?>">
	<div style="padding: 5px;">
		<?php echo image_tag('team_large.png', array('style' => 'float: left; margin-right: 5px;')); ?>
		<?php echo javascript_link_tag(image_tag('action_delete.png'), array('title' => __('Delete this user team'), 'onclick' => '$(\'confirm_team_'.$team->getID().'_delete\').toggle();', 'style' => 'float: right;', 'class' => 'image')); ?>
		<?php echo javascript_link_tag(image_tag('team_clone.png'), array('title' => __('Clone this user team'), 'onclick' => '$(\'clone_team_'.$team->getID().'\').toggle();', 'style' => 'float: right; margin-right: 5px;', 'class' => 'image')); ?>
		<?php echo javascript_link_tag(image_tag('team_list_users.png'), array('title' => __('List users in this team'), 'onclick' => 'TBG.Config.Team.showMembers(\''.make_url('configure_users_get_team_members', array('team_id' => $team->getID())).'\', '.$team->getID().');', 'style' => 'float: right; margin-right: 5px;', 'class' => 'image')); ?>
		<p class="teambox_header"><?php echo $team->getName(); ?></p>
		<p class="teambox_membercount"><?php echo __('ID: %id%', array('%id%' => $team->getID())); ?> - <?php echo __('%number_of% member(s)', array('%number_of%' => '<span id="team_'.$team->getID().'_membercount">'.$team->getNumberOfMembers().'</span>')); ?></p>
		<div class="rounded_box white shadowed" style="margin: 5px; display: none;" id="clone_team_<?php echo $team->getID(); ?>">
			<div class="dropdown_header"><?php echo __('Please specify what parts of this team you want to clone'); ?></div>
			<div class="dropdown_content copy_team_link">
				<form id="clone_team_<?php echo $team->getID(); ?>_form" action="<?php echo make_url('configure_users_clone_team', array('team_id' => $team->getID())); ?>" method="post" accept-charset="<?php echo TBGSettings::getCharset(); ?>" onsubmit="TBG.Config.Team.clone('<?php echo make_url('configure_users_clone_team', array('team_id' => $team->getID())); ?>');return false;">
					<div id="add_team">
						<label for="clone_team_<?php echo $team->getID(); ?>_new_name"><?php echo __('New team name'); ?></label>
						<input type="text" id="clone_team_<?php echo $team->getID(); ?>_new_name" name="team_name"><br />
						<input type="checkbox" id="clone_team_<?php echo $team->getID(); ?>_permissions" name="clone_permissions" value="1" checked />
						<label for="clone_team_<?php echo $team->getID(); ?>_permissions" style="font-weight: normal;"><?php echo __('Clone permissions from the old team for the new team'); ?></label><br />
						<input type="checkbox" id="clone_team_<?php echo $team->getID(); ?>_memberships" name="clone_memberships" value="1" checked />
						<label for="clone_team_<?php echo $team->getID(); ?>_memberships" style="font-weight: normal;"><?php echo __('Clone memberships (make members in the old team also members in the new, cloned team)'); ?></label>
					</div>
				</form>
				<div style="text-align: right;">
					<?php echo javascript_link_tag(__('Clone this team'), array('onclick' => 'TBG.Config.Team.clone(\''.make_url('configure_users_clone_team', array('team_id' => $team->getID())).'\', '.$team->getID().');')); ?> :: <b><?php echo javascript_link_tag(__('Cancel'), array('onclick' => '$(\'clone_team_'.$team->getID().'\').toggle();')); ?></b>
				</div>
				<table cellpadding=0 cellspacing=0 style="display: none; margin-left: 5px; width: 300px;" id="clone_team_<?php echo $team->getID(); ?>_indicator">
					<tr>
						<td style="width: 20px; padding: 2px;"><?php echo image_tag('spinning_20.gif'); ?></td>
						<td style="padding: 0px; text-align: left;"><?php echo __('Cloning team, please wait'); ?>...</td>
					</tr>
				</table>
			</div>
		</div>
		<div class="rounded_box white shadowed" style="margin: 5px; display: none;" id="confirm_team_<?php echo $team->getID(); ?>_delete">
			<div class="dropdown_header"><?php echo __('Do you really want to delete this team?'); ?></div>
			<div class="dropdown_content">
				<?php echo __('If you delete this team, then all users in this team will be disabled until moved to a different team'); ?>
				<div style="text-align: right;">
					<?php echo javascript_link_tag(__('Yes'), array('onclick' => 'TBG.Config.Team.remove(\''.make_url('configure_users_delete_team', array('team_id' => $team->getID())).'\', '.$team->getID().');')); ?> :: <b><?php echo javascript_link_tag(__('No'), array('onclick' => '$(\'confirm_team_'.$team->getID().'_delete\').toggle();')); ?></b>
				</div>
				<table cellpadding=0 cellspacing=0 style="display: none; margin-left: 5px; width: 300px;" id="delete_team_<?php echo $team->getID(); ?>_indicator">
					<tr>
						<td style="width: 20px; padding: 2px;"><?php echo image_tag('spinning_20.gif'); ?></td>
						<td style="padding: 0px; text-align: left;"><?php echo __('Deleting team, please wait'); ?>...</td>
					</tr>
				</table>
			</div>
		</div>
	</div>
	<div class="rounded_box lightgrey" style="margin-bottom: 5px; display: none;" id="team_members_<?php echo $team->getID(); ?>_container">
		<div class="dropdown_header"><?php echo __('Users in this team'); ?></div>
		<table cellpadding=0 cellspacing=0 style="display: none; margin-left: 5px; width: 300px;" id="team_members_<?php echo $team->getID(); ?>_indicator">
			<tr>
				<td style="width: 20px; padding: 2px;"><?php echo image_tag('spinning_20.gif'); ?></td>
				<td style="padding: 0px; text-align: left;"><?php echo __('Retrieving members, please wait'); ?>...</td>
			</tr>
		</table>
		<div id="team_members_<?php echo $team->getID(); ?>_list"></div>
	</div>
</div>