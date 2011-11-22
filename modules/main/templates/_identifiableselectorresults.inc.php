<b><?php echo __('Users found'); ?></b><br>
<?php if (count($users) > 0): ?>
	<?php foreach ($users as $user): ?>
		<?php if (isset($teamup_callback)): ?>
			<i><?php echo $user->getNameWithUsername(); ?></i><br>
			<a href="javascript:void(0);" onclick="<?php echo str_replace(array(urlencode('%identifiable_value%'), '%identifiable_value%'), array($user->getID(), $user->getID()), $callback); ?>"><b><?php echo __('Select this user'); ?></b></a>
			<?php echo __('%select_this_user% or %team_up_and_select%', array('%select_this_user%' => '', '%team_up_and_select%' => '')); ?>
			<a href="javascript:void(0);" onclick="<?php echo str_replace(array(urlencode('%identifiable_value%'), '%identifiable_value%'), array($user->getID(), $user->getID()), $teamup_callback); ?>"><b><?php echo __('Team up and select'); ?></b></a><br>
		<?php else: ?>
			<a href="javascript:void(0);" onclick="<?php echo str_replace(array(urlencode('%identifiable_value%'), '%identifiable_value%'), array($user->getID(), $user->getID()), $callback); ?>"><?php echo $user->getNameWithUsername(); ?></a><br>
		<?php endif; ?>
	<?php endforeach; ?>
<?php else: ?>
	<span class="faded_out"><?php echo __("Couldn't find any users"); ?></span><br>
<?php endif; ?>
<?php if ($include_teams): ?>
	<br>
	<b><?php echo __('Teams found'); ?></b><br>
	<?php if (count($teams) > 0): ?>
		<?php foreach ($teams as $team): ?>
			<a href="javascript:void(0);" onclick="<?php echo str_replace(array(urlencode('%identifiable_value%'), '%identifiable_value%'), array($team->getID(), $team->getID()), $team_callback); ?>"><?php echo $team->getName(); ?></a><br>
		<?php endforeach; ?>
	<?php else: ?>
		<span class="faded_out"><?php echo __("Couldn't find any teams"); ?></span>
	<?php endif; ?>
<?php endif; ?>