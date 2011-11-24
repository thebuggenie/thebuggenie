<form action="<?php echo make_url('configure_role_permissions', array('role_id' => $role->getID())); ?>" id="role_<?php echo $role->getID(); ?>_form" method="post" onsubmit="TBG.Config.Roles.setPermissions('<?php echo make_url('configure_role_permissions', array('role_id' => $role->getID())); ?>', <?php echo $role->getID(); ?>);return false;">
	<ul class="simple_list" style="width: 750px;">
	<?php include_template('configuration/rolepermissionseditlist', array('role' => $role, 'permissions_list' => TBGContext::getAvailablePermissions('project'))); ?>
	<?php include_template('configuration/rolepermissionseditlist', array('role' => $role, 'permissions_list' => TBGContext::getAvailablePermissions('project_pages'))); ?>
	<?php include_template('configuration/rolepermissionseditlist', array('role' => $role, 'permissions_list' => TBGContext::getAvailablePermissions('issues'))); ?>
	</ul>
	<input type="submit" value="<?php echo __('Save role permissions'); ?>" style="float: right;">
	<?php echo image_tag('spinning_16.gif', array('id' => "role_{$role->getID()}_form_indicator", 'style' => 'display: none; float: right; margin-right: 5px;')); ?>
	<br style="clear: both;">
</form>