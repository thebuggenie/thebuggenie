<ul class="simple_list">
<?php foreach ($role->getPermissions() as $permission): ?>
	<?php $permission_details = TBGContext::getPermissionDetails($permission); ?>
	<li>
		<?php echo image_tag('action_ok.png', array('style: margin-right: 5px;')).$permission_details['description']; ?>
	</li>
<?php endforeach; ?>
</ul>
