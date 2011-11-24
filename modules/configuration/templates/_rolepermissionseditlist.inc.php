<?php if (count($permissions_list) > 0): ?>
	<?php foreach ($permissions_list as $permission_key => $permission): ?>
		<?php if (is_numeric($permission_key)): ?>
			<?php include_template('configuration/rolepermissionseditlist', array('permissions_list' => $permission, 'role' => $role)); ?>
		<?php else: ?>
			<li>
				<?php echo $permission['description']; ?>
				<input type="radio" name="permission[<?php echo $permission_key; ?>]" value="1">&nbsp;<?php echo __('Yes'); ?>
				<input type="radio" name="permission[<?php echo $permission_key; ?>]" value="0">&nbsp;<?php echo __('No'); ?>
			</li>
		<?php endif; ?>
	<?php endforeach; ?>
<?php endif; ?>