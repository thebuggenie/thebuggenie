<form action="<?php echo make_url('configure_role', array('role_id' => $role->getID(), 'mode' => 'edit')); ?>" id="role_<?php echo $role->getID(); ?>_form" method="post" onsubmit="TBG.Config.Roles.update('<?php echo make_url('configure_role', array('role_id' => $role->getID(), 'mode' => 'edit')); ?>', <?php echo $role->getID(); ?>);return false;">
	<p style="padding: 5px 0;"><?php echo __('Enter the role name below, and select permissions inherited by users or teams assigned with this role'); ?></p>
	<label style="padding-left: 0;" for="role_<?php echo $role->getID(); ?>_name"><?php echo __('Role name'); ?></label>
	<input type="text" id="role_<?php echo $role->getID(); ?>_name" name="name" value="<?php echo htmlentities($role->getName(), ENT_COMPAT, TBGContext::getI18n()->getCharset()); ?>" style="width: 300px;"><br>
	<div style="margin-top: 10px; font-weight: bold;"><?php echo __('Role permissions'); ?></div>
	<ul class="simple_list" style="width: 775px;">
	<?php include_template('configuration/rolepermissionseditlist', array('role' => $role, 'permissions_list' => TBGContext::getAvailablePermissions('project'), 'module' => 'core', 'target_id' => null)); ?>
	<?php include_template('configuration/rolepermissionseditlist', array('role' => $role, 'permissions_list' => TBGContext::getAvailablePermissions('project_pages'), 'module' => 'core', 'target_id' => null)); ?>
	<?php include_template('configuration/rolepermissionseditlist', array('role' => $role, 'permissions_list' => TBGContext::getAvailablePermissions('issues'), 'module' => 'core', 'target_id' => null)); ?>
	<?php TBGEvent::createNew('core', 'rolepermissionsedit', $role)->trigger(); ?>
	</ul>
	<input type="submit" value="<?php echo __('Save role'); ?>" style="float: right;">
	<?php echo image_tag('spinning_16.gif', array('id' => "role_{$role->getID()}_form_indicator", 'style' => 'display: none; float: right; margin-right: 5px;')); ?>
	<br style="clear: both;">
</form>