<?php $tbg_response->setTitle(__('Configure roles')); ?>
<table style="table-layout: fixed; width: 100%" cellpadding=0 cellspacing=0 class="configuration_page">
    <tr>
        <?php include_component('leftmenu', array('selected_section' => \thebuggenie\core\framework\Settings::CONFIGURATION_SECTION_ROLES)); ?>
        <td valign="top" style="padding-left: 15px;">
            <div id="config_roles" style="position: relative; width: 730px;">
                <h3>
                    <a class="dropper button button-silver"><?php echo __('Actions'); ?></a>
                    <ul class="simple_list rounded_box white shadowed more_actions_dropdown dropdown_box popup_box">
                        <li><?php echo link_tag(make_url('configure_permissions'), __('Show advanced permissions')); ?></li>
                    </ul>
                    <?php echo __('Configure roles'); ?>
                </h3>
                <div class="content faded_out">
                    <p><?php echo __("Roles are applied when assigning users or teams to a project, granting them access to specific parts of the project or giving users access to update and edit information. Updating permissions in this list will add or remove permissions for all users and / or team members with that role, on all assigned projects. Removing a role removes all permissions granted by that role for all users and teams. Read more about roles and permissions in the %online_documentation", array('%online_documentation' => link_tag('http://issues.thebuggenie.com/wiki/TheBugGenie:RolesAndPermissions', '<b>'.__('online documentation').'</b>'))); ?></p>
                </div>
                <div class="lightyellowbox" id="new_role" style="margin-top: 15px;">
                    <form id="new_role_form" method="post" action="<?php echo make_url('configure_roles', array('mode' => 'new')); ?>" onsubmit="TBG.Config.Roles.add('<?php echo make_url('configure_roles', array('mode' => 'new')); ?>'); return false;" accept-charset="<?php echo \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>">
                        <label for="new_project_role_name"><?php echo __('Add new role'); ?></label>
                        <input type="text" style="width: 300px;" name="role_name" id="add_new_role_input">
                        <div style="text-align: right; float: right;">
                            <?php echo image_tag('spinning_16.gif', array('style' => 'display: none; margin: 3px 5px -4px;', 'id' => 'new_role_form_indicator')); ?>
                            <input type="submit" value="<?php echo __('Create role'); ?>" class="button button-silver">
                        </div>
                    </form>
                </div>
                <h5 style="margin-top: 10px;">
                    <?php echo __('Globally available roles'); ?>
                </h5>
                <ul id="global_roles_list" class="simple_list" style="width: 730px;">
                    <?php foreach ($roles as $role): ?>
                        <?php include_component('configuration/role', array('role' => $role)); ?>
                    <?php endforeach; ?>
                    <li class="faded_out no_roles" id="global_roles_no_roles"<?php if (count($roles)): ?> style="display: none;"<?php endif; ?>><?php echo __('There are no globally available roles'); ?></li>
                </ul>
            </div>
        </td>
    </tr>
</table>
