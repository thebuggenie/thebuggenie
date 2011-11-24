<form>
	<ul class="simple_list">
	<?php include_template('configuration/rolepermissionseditlist', array('role' => $role, 'permissions_list' => TBGContext::getAvailablePermissions('project'))); ?>
	<?php include_template('configuration/rolepermissionseditlist', array('role' => $role, 'permissions_list' => TBGContext::getAvailablePermissions('project_pages'))); ?>
	<?php include_template('configuration/rolepermissionseditlist', array('role' => $role, 'permissions_list' => TBGContext::getAvailablePermissions('issues'))); ?>
	</ul>
</form>