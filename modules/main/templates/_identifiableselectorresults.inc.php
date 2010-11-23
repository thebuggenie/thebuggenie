<b><?php echo __('Users found'); ?></b><br>
<?php if (count($users) > 0): ?>
	<?php foreach ($users as $user): ?>
		<a href="javascript:void(0);" onclick="<?php echo str_replace(array(urlencode('%identifiable_type%'), urlencode('%identifiable_value%')), array(TBGIdentifiableClass::TYPE_USER, $user->getID()), $callback); ?>"><?php echo $user->getNameWithUsername(); ?></a><br>
	<?php endforeach; ?>
<?php else: ?>
	<span class="faded_out"><?php echo __("Couldn't find any users"); ?></span><br>
<?php endif; ?>
<?php if ($include_teams): ?>
	<br>
	<b><?php echo __('Teams found'); ?></b><br>
	<?php if (count($teams) > 0): ?>
		<?php foreach ($teams as $team): ?>
			<a href="javascript:void(0);" onclick="<?php echo str_replace(array(urlencode('%identifiable_type%'), urlencode('%identifiable_value%')), array(TBGIdentifiableClass::TYPE_TEAM, $team->getID()), $callback); ?>"><?php echo $team->getName(); ?></a><br>
		<?php endforeach; ?>
	<?php else: ?>
		<span class="faded_out"><?php echo __("Couldn't find any teams"); ?></span>
	<?php endif; ?>
<?php endif; ?>