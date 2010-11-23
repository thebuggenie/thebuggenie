<?php if (!$team instanceof TBGTeam || $team->getID() == 0): ?>
	<span class="faded_out"><?php echo __('No such team'); ?></span>
<?php else: ?>
	<?php echo image_tag('icon_team.png', array('style' => "width: 16px; height: 16px; float: left; margin-right: 5px;")); ?><?php echo $team->getName(); ?>
<?php endif; ?>