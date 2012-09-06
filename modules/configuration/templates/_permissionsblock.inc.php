<?php $user_id = isset($user_id) ? $user_id : null; ?>
<?php if (count($permissions_list) > 0): ?>
	<?php foreach ($permissions_list as $permission_key => $permission): ?>
		<?php if (is_numeric($permission_key)): ?>
			<?php include_template('configuration/permissionsblock', array('base_id' => $base_id.'_'.$permission_key, 'permissions_list' => $permission, 'mode' => $mode, 'target_id' => $target_id, 'module' => $module, 'access_level' => $access_level)); ?>
		<?php else: ?>
			<?php $current_target_id = (array_key_exists('target_id', $permission)) ? $permission['target_id'] : $target_id; ?>
			<li>
				<?php if (!isset($user_id) || !$user_id): ?>
					<?php if (!array_key_exists('iscontainer', $permission) || $permission['iscontainer'] == false): ?>
						<a href="javascript:void(0);" onclick="<?php if(array_key_exists('details', $permission) && count($permission['details']) > 0): ?>$('<?php echo $base_id; ?>_<?php echo $permission_key; ?>_details').hide();<?php endif; ?>TBG.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'permissions', 'permission_key' => $permission_key, 'mode' => $mode, 'target_id' => $current_target_id, 'target_module' => $module, 'access_level' => $access_level)); ?>');" style="float: right;"><?php echo image_tag('cfg_icon_permissions.png'); ?></a>
					<?php endif; ?>
				<?php else: ?>
					<?php $user = TBGContext::factory()->TBGUser($user_id); ?>
					<div style="float: right;"><?php include_component('configuration/permissionsinfoitem', array('key' => $permission_key, 'target_id' => $current_target_id, 'type' => 'user', 'mode' => $mode, 'item_id' => $user->getID(), 'item_name' => urlencode($user->getName()), 'module' => $module, 'access_level' => $access_level)); ?></div>
				<?php endif; ?>
				<?php if(array_key_exists('details', $permission) && count($permission['details']) > 0): ?>
					<a href="javascript:void(0);" onclick="TBG.Config.Permissions.getOptions('<?php echo make_url('configure_permissions_get_permissions', array('base_id' => $base_id . $permission_key, 'user_id' => (int) $user_id, 'permissions_list' => $permission_key, 'mode' => $mode, 'target_id' => $current_target_id, 'target_module' => $module)); ?>', '<?php echo $base_id; ?>_<?php echo $permission_key; ?>_details');" style="float: right; margin-right: 5px;" title="<?php echo __('More fine-tuned permissions are available. Click to see them.'); ?>"><?php echo image_tag('icon_project_permissions.png'); ?></a>
				<?php endif; ?>
				<?php if (!isset($user_id) || !$user_id): ?>
					<a href="javascript:void(0);" onclick="<?php if(array_key_exists('details', $permission) && count($permission['details']) > 0): ?>$('<?php echo $base_id; ?>_<?php echo $permission_key; ?>_details').hide();<?php endif; ?>TBG.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'permissions', 'permission_key' => $permission_key, 'mode' => $mode, 'target_id' => $current_target_id, 'target_module' => $module, 'access_level' => $access_level)); ?>');" class="permission_description"><?php echo $permission['description']; ?></a>
				<?php else: ?>
					<?php echo $permission['description']; ?>
				<?php endif; ?>
				<?php if(array_key_exists('details', $permission) && count($permission['details']) > 0): ?>
					<?php echo image_tag('spinning_20.gif', array('style' => 'display: none;', 'id' => $base_id . '_' . $permission_key . '_details_indicator')); ?>
					<ul style="display: none;" id="<?php echo $base_id; ?>_<?php echo $permission_key; ?>_details"> </ul>
				<?php endif; ?>
			</li>
		<?php endif; ?>
	<?php endforeach; ?>
<?php else: ?>
	<li class="faded_out"><?php echo __('This permission list is empty'); ?></li>
<?php endif; ?>