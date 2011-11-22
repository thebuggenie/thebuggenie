<?php if (count($assignees) > 0): ?>
	<?php foreach ($assignees as $assignee): ?>
		<div style="width: auto; display: table-cell; clear: none; padding: 0 10px 0 0; ">
			<?php if ($assignee instanceof TBGUser): ?>
				<?php echo include_component('main/userdropdown', array('user' => $assignee)); ?>
			<?php else: ?>
				<?php echo include_component('main/teamdropdown', array('team' => $assignee)); ?>
			<?php endif; ?>
			<span class="faded_out"> -
				<?php $roles = ($assignee instanceof TBGUser) ? $project->getRolesForUser($assignee) : $project->getRolesForTeam($assignee); ?>
				<?php $role_names = array(); ?>
				<?php foreach ($roles as $role) $role_names[] = $role->getName(); ?>
				<?php echo join(',', $role_names); ?>
			</span>
		</div>
	<?php endforeach; ?>
<?php else: ?>
	<p class="content faded_out"><?php echo __('No users or teams assigned'); ?>.</p>
<?php endif; ?>
