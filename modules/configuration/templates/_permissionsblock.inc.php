<?php if (count($permissions_list) > 0): ?>
	<?php foreach ($permissions_list as $permission_key => $permission): ?>
		<li>
			<a href="javascript:void(0);" onclick="<?php if(array_key_exists('details', $permission)): ?>$('<?php echo $base_id; ?>_<?php echo $permission_key; ?>_details').hide();<?php endif; ?>$('<?php echo $base_id; ?>_<?php echo $permission_key; ?>_settings').toggle();" style="float: right;"><?php echo image_tag('cfg_icon_permissions.png'); ?></a>
			<?php if(array_key_exists('details', $permission)): ?>
				<a href="javascript:void(0);" onclick="$('<?php echo $base_id; ?>_<?php echo $permission_key; ?>_settings').hide();getPermissionOptions('<?php echo make_url('configure_permissions_get_permissions', array('base_id' => $base_id . $permission_key, 'permissions_list' => $permission_key, 'mode' => $mode, 'target_id' => $target_id, 'target_module' => $module)); ?>', '<?php echo $base_id; ?>_<?php echo $permission_key; ?>_details');" style="float: right; margin-right: 5px;" title="<?php echo __('More fine-tuned permissions are available. Click to see them.'); ?>"><?php echo image_tag('icon_project_permissions.png'); ?></a>
			<?php endif; ?>
			<a href="javascript:void(0);" onclick="<?php if(array_key_exists('details', $permission)): ?>$('<?php echo $base_id; ?>_<?php echo $permission_key; ?>_details').hide();<?php endif; ?>$('<?php echo $base_id; ?>_<?php echo $permission_key; ?>_settings').toggle();" class="permission_description"><?php echo $permission['description']; ?></a>
			<?php if(array_key_exists('details', $permission)): ?>
				<?php echo image_tag('spinning_20.gif', array('style' => 'display: none;', 'id' => $base_id . '_' . $permission_key . '_details_indicator')); ?>
				<ul style="display: none;" id="<?php echo $base_id; ?>_<?php echo $permission_key; ?>_details"> </ul>
			<?php endif; ?>
			<div class="rounded_box white" style="margin: 5px 5px 10px 0; display: none;" id="<?php echo $base_id; ?>_<?php echo $permission_key; ?>_settings" style="display: none;">
				<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
				<div class="xboxcontent" style="padding: 3px; font-size: 12px;">
					<div class="content">
						<?php include_component('configuration/permissionsinfo', array('key' => $permission_key, 'mode' => $mode, 'target_id' => $target_id, 'module' => $module, 'access_level' => $access_level)); ?>
					</div>
				</div>
				<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
			</div>
		</li>
	<?php endforeach; ?>
<?php else: ?>
	<li class="faded_medium"><?php echo __('This permission list is empty'); ?></li>
<?php endif; ?>