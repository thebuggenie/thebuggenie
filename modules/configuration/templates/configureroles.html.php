<?php $tbg_response->setTitle(__('Configure roles')); ?>
<table style="table-layout: fixed; width: 100%" cellpadding=0 cellspacing=0>
	<tr>
		<?php include_component('leftmenu', array('selected_section' => TBGSettings::CONFIGURATION_SECTION_ROLES)); ?>
		<td valign="top" style="padding-left: 15px;">
			<div id="config_roles" style="position: relative; width: 788px;">
				<h3>
					<?php echo __('Configure roles'); ?>
				</h3>
				<div class="content faded_out">
					<p><?php echo __("These roles acts as permission templates and can be applied when assigning people (or teams) to a project. When people (or teams) are unassigned from the project they will keep all permissions applied by any roles until their last role in the project is unassigned. Read more about roles and permissions in the %online_documentation%", array('%online_documentation%' => link_tag('http://issues.thebuggenie.com/wiki/TheBugGenie:RolesAndPermissions', '<b>'.__('online documentation').'</b>'))); ?></p>
				</div>
				<h5 style="margin-top: 10px;">
					<button class="button button-green" onclick="$('new_role').toggle();if ($('new_role').visible()) { $('add_new_role_input').focus(); }" style="float: right;"><?php echo __('Create new role'); ?></button>
					<?php echo __('Globally available roles'); ?>
				</h5>
				<div class="rounded_box white shadowed" id="new_role" style="display: none; position: absolute; right: 0; z-index: 10;">
					<form id="new_role_form" method="post" action="<?php echo make_url('configure_roles', array('mode' => 'new')); ?>" onsubmit="TBG.Config.Roles.add('<?php echo make_url('configure_roles', array('mode' => 'new')); ?>'); return false;" accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>">
						<label for="new_project_role_name"><?php echo __('Role name'); ?></label>
						<input type="text" style="width: 300px;" name="role_name" id="add_new_role_input">
						<?php echo image_tag('spinning_16.gif', array('style' => 'display: none; float: right; margin: 2px 5px 2px 5px;', 'id' => 'new_role_form_indicator')); ?>
						<input type="submit" value="<?php echo __('Create role'); ?>" class="button button-silver" style="float: right; margin: 1px 1px 1px 5px;">
					</form>
				</div>
				<ul id="global_roles_list" class="simple_list" style="width: 788px;">
					<?php foreach ($roles as $role): ?>
						<?php include_template('configuration/role', array('role' => $role)); ?>
					<?php endforeach; ?>
					<li class="faded_out no_roles" id="global_roles_no_roles"<?php if (count($roles)): ?> style="display: none;"<?php endif; ?>><?php echo __('There are no globally available roles'); ?></li>
				</ul>
			</div>
		</td>
	</tr>
</table>