<div class="rounded_box mediumgrey borderless" style="margin-top: 5px;">
	<?php echo image_tag('group_large.png', array('style' => 'float: left; margin-right: 5px;')); ?>
	<p class="groupbox_header"><?php echo $group->getName(); ?></p>
	<p class="groupbox_membercount"><?php echo __('%number_of% member(s)', array('%number_of%' => $group->getNumberOfMembers())); ?></p>
</div>