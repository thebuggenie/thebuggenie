<?php echo tbg_parse_text(__('Please see [[ConfigurePermissions]] for more information about how permissions work')); ?>
<div class="header_div light smaller"><?php echo __('Global permission'); ?></div>
<table cellpadding="0" cellspacing="0" style="width: 100%;">
	<thead class="light">
		<tr>
			<th>&nbsp;</th>
			<th style="width: 60px; text-align: center;"><?php echo __('Can set'); ?></th>
		</tr>
	</thead>
	<tbody>
		<tr class="canhover_light">
			<td style="padding: 2px;"><?php echo __('Everyone with access'); ?></td>
			<td style="padding: 2px; text-align: center;">
				<?php include_component('configuration/permissionsinfoitem', array('key' => $key, 'target_id' => $target_id, 'type' => 'everyone', 'mode' => $mode, 'item_id' => 0, 'module' => $module, 'access_level' => $access_level)); ?>
			</td>
		</tr>
		<tr>
			<td colspan="2"><div class="header_div smaller" style="margin-top: 15px;"><?php echo __('Group-specific permissions'); ?></div></td>
		</tr>
		<?php foreach (BUGSgroup::getAll() as $group): ?>
			<tr class="canhover_light">
				<td style="padding: 2px;"><?php echo $group->getName(); ?></td>
				<td style="padding: 2px; text-align: center;">
					<?php include_component('configuration/permissionsinfoitem', array('key' => $key, 'target_id' => $target_id, 'type' => 'group', 'mode' => $mode, 'item_id' => $group->getID(), 'item_name' => $group->getName(), 'module' => $module, 'access_level' => $access_level)); ?>
				</td>
			</tr>
		<?php endforeach; ?>
		<tr>
			<td colspan="2"><div class="header_div smaller" style="margin-top: 15px;"><?php echo __('Team-specific permissions'); ?></div></td>
		</tr>
		<?php foreach (BUGSteam::getAll() as $team): ?>
			<tr class="canhover_light">
				<td style="padding: 2px;"><?php echo $team->getName(); ?></td>
				<td style="padding: 2px; text-align: center;">
					<?php include_component('configuration/permissionsinfoitem', array('key' => $key, 'type' => 'team', 'target_id' => $target_id, 'mode' => $mode, 'item_id' => $team->getID(), 'item_name' => $team->getName(), 'module' => $module, 'access_level' => $access_level)); ?>
				</td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>