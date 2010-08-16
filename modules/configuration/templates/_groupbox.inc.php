<div class="rounded_box mediumgrey borderless" style="margin-top: 5px;" id="groupbox_<?php echo $group->getID(); ?>">
	<?php echo image_tag('group_large.png', array('style' => 'float: left; margin-right: 5px;')); ?>
	<?php echo javascript_link_tag(image_tag('action_delete.png'), array('title' => __('Delete this user group'), 'onclick' => '$(\'confirm_group_'.$group->getID().'_delete\').toggle();', 'style' => 'float: right;', 'class' => 'image')); ?>
	<p class="groupbox_header"><?php echo $group->getName(); ?></p>
	<p class="groupbox_membercount"><?php echo __('%number_of% member(s)', array('%number_of%' => $group->getNumberOfMembers())); ?></p>
	<div class="rounded_box white shadowed" style="margin: 5px; display: none;" id="confirm_group_<?php echo $group->getID(); ?>_delete">
		<div class="dropdown_header"><?php echo __('Do you really want to delete this group?'); ?></div>
		<div class="dropdown_content">
			<?php echo __('If you delete this group, then all users in this group will be disabled until moved to a different group'); ?>
			<div style="text-align: right;">
				<?php echo javascript_link_tag(__('Yes'), array('onclick' => 'deleteGroup(\''.make_url('configure_users_delete_group', array('group_id' => $group->getID())).'\', '.$group->getID().');')); ?> :: <b><?php echo javascript_link_tag(__('No'), array('onclick' => '$(\'confirm_group_'.$group->getID().'_delete\').toggle();')); ?></b>
			</div>
		</div>
	</div>
</div>