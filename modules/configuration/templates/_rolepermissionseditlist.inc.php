<?php if (count($permissions_list) > 0): ?>
	<?php foreach ($permissions_list as $permission_key => $permission): ?>
		<?php if (!array_key_exists('container', $permission) || !$permission['container']): ?>
			<li style="clear: both; overflow: visible;">
				<div style="padding: 2px; float: left;"><?php echo $permission['description']; ?></div>
				<div style="float: right; padding-top: 1px;">
					<?php if (array_key_exists('details', $permission) && is_array($permission['details']) && !empty($permission['details'])): ?>
						<?php echo javascript_link_tag(image_tag('icon_project_permissions.png'), array('style' => 'display: inline;', 'onclick' => "$('role_{$role->getID()}_permission_{$permission_key}_sublist').toggle();")); ?>
					<?php endif; ?>
					<input type="checkbox" name="permissions[<?php echo $permission_key; ?>]" id="role_<?php echo $role->getID(); ?>_permission_<?php echo $permission_key; ?>_checkbox" value="1"<?php if ($role->hasPermission($permission_key)) echo ' checked'; ?>>&nbsp;<label for="role_<?php echo $role->getID(); ?>_permission_<?php echo $permission_key; ?>_checkbox" style="float: right; padding-right: 5px;"><?php echo __('Yes'); ?></label>
				</div>
				<br style="clear: both;">
				<?php if (array_key_exists('details', $permission) && is_array($permission['details']) && !empty($permission['details'])): ?>
					<ul id="role_<?php echo $role->getID(); ?>_permission_<?php echo $permission_key; ?>_sublist" style="display: none; width: auto;">
						<?php include_template('configuration/rolepermissionseditlist', array('permissions_list' => $permission['details'], 'role' => $role)); ?>
					</ul>
				<?php endif; ?>
			</li>
		<?php endif; ?>
	<?php endforeach; ?>
<?php endif; ?>