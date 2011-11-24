<?php if ($role instanceof TBGRole): ?>
	<li class="rounded_box lightgrey" style="padding: 3px 5px; margin-bottom: 5px;">
		<div class="button-group" style="float: right; margin-left: 10px;">
			<?php echo javascript_link_tag(__('Show/hide permissions'), array('onclick' => "TBG.Config.Roles.getPermissions('".make_url('configure_role_permissions_list', array('role_id' => $role->getID()))."', 'role_{$role->getID()}_permissions_list');", 'class' => 'button button-silver')); ?>
			<?php if (!TBGContext::isProjectContext() || !$role->isSystemRole()): ?>
				<?php echo javascript_link_tag(__('Edit'), array('onclick' => "TBG.Config.Roles.getPermissionsEdit('".make_url('configure_role_permissions', array('role_id' => $role->getID()))."', 'role_{$role->getID()}_permissions_edit');", 'class' => 'button button-silver')); ?>
			<?php endif; ?>
		</div>
		<strong><?php echo $role->getName(); ?></strong>&nbsp;&nbsp;
		<span class="faded_out"><?php echo __('%number_of_permissions% permission(s)', array('%number_of_permissions%' => '<b id="role_'.$role->getID().'_permissions_count">'.count($role->getPermissions()).'</b>')); ?></span>
		<?php echo image_tag('spinning_16.gif', array('style' => 'display: none; margin: 2px 0 -2px 5px;', 'id' => 'role_'.$role->getID().'_permissions_list_indicator')); ?>
		<div id="role_<?php echo $role->getID(); ?>_permissions_list" style="display: none;"></div>
		<?php echo image_tag('spinning_16.gif', array('style' => 'display: none; margin: 2px 0 -2px 5px;', 'id' => 'role_'.$role->getID().'_permissions_edit_indicator')); ?>
		<div id="role_<?php echo $role->getID(); ?>_permissions_edit" style="display: none;"></div>
	</li>
<?php endif; ?>