<div class="rounded_box mediumgrey borderless" style="padding: 0; margin-top: 5px;" id="groupbox_<?php echo $group->getID(); ?>">
	<div style="padding: 5px;">
		<?php echo image_tag('group_large.png', array('style' => 'float: left; margin-right: 5px;')); ?>
		<?php if ($group->getID() > 3): echo javascript_link_tag(image_tag('action_delete.png'), array('title' => __('Delete this user group'), 'onclick' => '$(\'confirm_group_'.$group->getID().'_delete\').toggle();', 'style' => 'float: right;', 'class' => 'image')); endif; ?>
		<?php echo javascript_link_tag(image_tag('group_clone.png'), array('title' => __('Clone this user group'), 'onclick' => '$(\'clone_group_'.$group->getID().'\').toggle();', 'style' => 'float: right; margin-right: 5px;', 'class' => 'image')); ?>
		<?php echo javascript_link_tag(image_tag('group_list_users.png'), array('title' => __('List users in this group'), 'onclick' => 'TBG.Config.Group.showMembers(\''.make_url('configure_users_get_group_members', array('group_id' => $group->getID())).'\', '.$group->getID().');', 'style' => 'float: right; margin-right: 5px;', 'class' => 'image')); ?>
		<p class="groupbox_header"><?php echo $group->getName(); ?></p>
		<p class="groupbox_membercount"><?php echo __('ID: %id%', array('%id%' => $group->getID())); ?> - <?php echo __('%number_of% member(s)', array('%number_of%' => '<span id="group_'.$group->getID().'_membercount">'.$group->getNumberOfMembers().'</span>')); ?></p>
		<div class="rounded_box white shadowed" style="margin: 5px; display: none;" id="clone_group_<?php echo $group->getID(); ?>">
			<div class="dropdown_header"><?php echo __('Please specify what parts of this group you want to clone'); ?></div>
			<div class="dropdown_content">
				<form id="clone_group_<?php echo $group->getID(); ?>_form" action="<?php echo make_url('configure_users_clone_group', array('group_id' => $group->getID())); ?>" method="post" accept-charset="<?php echo TBGSettings::getCharset(); ?>" onsubmit="TBG.Config.Group.clone('<?php echo make_url('configure_users_clone_group', array('group_id' => $group->getID())); ?>');return false;">
					<div id="add_group">
						<label for="clone_group_<?php echo $group->getID(); ?>_new_name"><?php echo __('New group name'); ?></label>
						<input type="text" id="clone_group_<?php echo $group->getID(); ?>_new_name" name="group_name"><br />
						<input type="checkbox" id="clone_group_<?php echo $group->getID(); ?>_permissions" name="clone_permissions" value="1" checked />
						<label for="clone_group_<?php echo $group->getID(); ?>_permissions" style="font-weight: normal;"><?php echo __('Clone permissions from the old group for the new group'); ?></label>
					</div>
				</form>
				<div style="text-align: right;">
					<?php echo javascript_link_tag(__('Clone this group'), array('onclick' => 'TBG.Config.Group.clone(\''.make_url('configure_users_clone_group', array('group_id' => $group->getID())).'\', '.$group->getID().');')); ?> :: <b><?php echo javascript_link_tag(__('Cancel'), array('onclick' => '$(\'clone_group_'.$group->getID().'\').toggle();')); ?></b>
				</div>
				<table cellpadding=0 cellspacing=0 style="display: none; margin-left: 5px; width: 300px;" id="clone_group_<?php echo $group->getID(); ?>_indicator">
					<tr>
						<td style="width: 20px; padding: 2px;"><?php echo image_tag('spinning_20.gif'); ?></td>
						<td style="padding: 0px; text-align: left;"><?php echo __('Cloning group, please wait'); ?>...</td>
					</tr>
				</table>
			</div>
		</div>
		<?php if ($group->getID() > 3): ?>
		<div class="rounded_box white shadowed" style="margin: 5px; display: none;" id="confirm_group_<?php echo $group->getID(); ?>_delete">
			<div class="dropdown_header"><?php echo __('Do you really want to delete this group?'); ?></div>
			<div class="dropdown_content">
				<?php echo __('If you delete this group, then all users in this group will be disabled until moved to a different group'); ?>
				<div style="text-align: right;">
					<?php echo javascript_link_tag(__('Yes'), array('onclick' => 'TBG.Config.Group.remove(\''.make_url('configure_users_delete_group', array('group_id' => $group->getID())).'\', '.$group->getID().');')); ?> :: <b><?php echo javascript_link_tag(__('No'), array('onclick' => '$(\'confirm_group_'.$group->getID().'_delete\').toggle();')); ?></b>
				</div>
				<table cellpadding=0 cellspacing=0 style="display: none; margin-left: 5px; width: 300px;" id="delete_group_<?php echo $group->getID(); ?>_indicator">
					<tr>
						<td style="width: 20px; padding: 2px;"><?php echo image_tag('spinning_20.gif'); ?></td>
						<td style="padding: 0px; text-align: left;"><?php echo __('Deleting group, please wait'); ?>...</td>
					</tr>
				</table>
			</div>
		</div>
		<?php endif; ?>
	</div>
	<div class="rounded_box lightgrey" style="margin-bottom: 5px; display: none;" id="group_members_<?php echo $group->getID(); ?>_container">
		<div class="dropdown_header"><?php echo __('Users in this group'); ?></div>
		<table cellpadding=0 cellspacing=0 style="display: none; margin-left: 5px; width: 300px;" id="group_members_<?php echo $group->getID(); ?>_indicator">
			<tr>
				<td style="width: 20px; padding: 2px;"><?php echo image_tag('spinning_20.gif'); ?></td>
				<td style="padding: 0px; text-align: left;"><?php echo __('Retrieving members, please wait'); ?>...</td>
			</tr>
		</table>
		<div id="group_members_<?php echo $group->getID(); ?>_list"></div>
	</div>
</div>