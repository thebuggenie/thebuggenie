<div class="header_div light smaller"><?php echo __('Global permission'); ?></div>
<table cellpadding="0" cellspacing="0" style="width: 100%;">
	<thead class="light">
		<tr>
			<th>&nbsp;</th>
			<th style="width: 60px; text-align: center;"><?php echo __('Can set'); ?></th>
		</tr>
	</thead>
	<tbody>
		<tr id="permissions_<?php echo $key; ?>_everyone" class="canhover_light">
			<?php include_component('configuration/permissionsinfoitem', array('key' => $key, 'target_id' => 0, 'type' => 'everyone', 'mode' => $mode, 'item_id' => 0, 'item_name' => __('Everyone with access'), 'module' => $module, 'access_level' => $access_level)); ?>
		</tr>
		<tr>
			<td colspan="2"><div class="header_div smaller" style="margin-top: 15px;"><?php echo __('Group-specific permissions'); ?></div></td>
		</tr>
		<?php foreach (BUGSgroup::getAll() as $group): ?>
			<tr id="permissions_<?php echo $key; ?>_group_<?php echo $group->getID(); ?>" class="canhover_light">
				<?php include_component('configuration/permissionsinfoitem', array('key' => $key, 'target_id' => 0, 'type' => 'group', 'mode' => $mode, 'item_id' => $group->getID(), 'item_name' => $group->getName(), 'module' => $module, 'access_level' => $access_level)); ?>
			</tr>
		<?php endforeach; ?>
		<tr>
			<td colspan="2"><div class="header_div smaller" style="margin-top: 15px;"><?php echo __('Team-specific permissions'); ?></div></td>
		</tr>
		<?php foreach (BUGSteam::getAll() as $team): ?>
			<tr id="permissions_<?php echo $key; ?>_team_<?php echo $team->getID(); ?>" class="canhover_light">
				<?php include_component('configuration/permissionsinfoitem', array('key' => $key, 'type' => 'team', 'target_id' => 0, 'mode' => $mode, 'item_id' => $team->getID(), 'item_name' => $team->getName(), 'module' => $module, 'access_level' => $access_level)); ?>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>