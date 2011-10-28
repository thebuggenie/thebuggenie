<?php if ((count($assignees['users']) > 0) || (count($assignees['teams']) > 0)): ?>
	<?php foreach ($assignees['users'] as $user_id => $info): ?>
		<div style="width: auto; display: table-cell; clear: none; padding: 0 10px 0 0; ">
			<?php echo include_component('main/userdropdown', array('user' => $user_id)); ?>
			<span class="faded_out"> -
			<?php foreach ($info as $type => $bool): ?>
				<?php if ($bool == true): ?>
					<?php echo ' '.TBGProjectAssigneesTable::getTypeName($type); ?>
				<?php endif; ?>
			<?php endforeach; ?>
			</span>
		</div>
	<?php endforeach; ?>
	<?php foreach ($assignees['teams'] as $team_id => $info): ?>
		<div style="width: auto; display: table-cell; clear: none; padding: 0 10px 0 0; ">
			<?php echo include_component('main/teamdropdown', array('team' => $team_id)); ?>
			<span class="faded_out"> -
			<?php foreach ($info as $type => $bool): ?>
				<?php if ($bool == true): ?>
					<?php echo ' '.TBGProjectAssigneesTable::getTypeName($type); ?>
				<?php endif; ?>
			<?php endforeach; ?>
			</span>
		</div>
	<?php endforeach; ?>
<?php else: ?>
	<p class="content faded_out"><?php echo __('No users or teams assigned'); ?>.</p>
<?php endif; ?>
