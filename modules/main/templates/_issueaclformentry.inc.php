<li>
	<?php if ($target instanceof TBGUser): ?>
		<input type="hidden" name="access_list_users[<?php echo $target->getID(); ?>]" value="<?php echo $target->getID(); ?>">
		<?php echo include_component('main/userdropdown', array('user' => $target)); ?>
	<?php elseif ($target instanceof TBGTeam): ?>
		<input type="hidden" name="access_list_teams[<?php echo $target->getID(); ?>]" value="<?php echo $target->getID(); ?>">
		<?php echo include_component('main/teamdropdown', array('team' => $target)); ?>
	<?php endif; ?>
</li>