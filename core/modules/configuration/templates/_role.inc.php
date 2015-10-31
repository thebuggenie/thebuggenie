<?php if ($role instanceof \thebuggenie\core\entities\Role): ?>
    <li class="greybox" style="padding: 3px 5px; margin-bottom: 5px;" id="role_<?php echo $role->getID(); ?>_container">
        <div class="button-group" style="float: right; margin-right: -3px; margin-top: -1px;">
            <?php echo javascript_link_tag(__('Details'), array('onclick' => "TBG.Config.Roles.getPermissions('".make_url('configure_role', array('role_id' => $role->getID(), 'mode' => 'list_permissions'))."', 'role_{$role->getID()}_permissions_list');", 'class' => 'button button-silver')); ?>
            <?php if (!\thebuggenie\core\framework\Context::isProjectContext() || !$role->isSystemRole()): ?>
                <?php echo javascript_link_tag(__('Edit'), array('onclick' => "TBG.Config.Roles.getPermissionsEdit('".make_url('configure_role', array('role_id' => $role->getID(), 'mode' => 'edit'))."', 'role_{$role->getID()}_permissions_edit');", 'class' => 'button button-silver')); ?>
                <button class="button button-silver" onclick="TBG.Main.Helpers.Dialog.show('<?php echo __('Delete this role?'); ?>', '<?php echo __('Do you really want to delete this role?').'<br>'.__('Users assigned via this role will be unassigned, and depending on other roles their project permissions may be reset.').'<br><b>'.__('This action cannot be reverted').'</b>'; ?>', {yes: {click: function() {TBG.Config.Roles.remove('<?php echo make_url('configure_role', array('role_id' => $role->getID(), 'mode' => 'delete')); ?>', <?php print $role->getID(); ?>);}}, no: {click: TBG.Main.Helpers.Dialog.dismiss}});"><?php echo __('Delete'); ?></button>
            <?php endif; ?>
        </div>
        <strong id="role_<?php echo $role->getID(); ?>_name"><?php echo $role->getName(); ?></strong>&nbsp;&nbsp;
        <span class="faded_out"><?php echo __('%number_of_permissions permission(s)', array('%number_of_permissions' => '<b id="role_'.$role->getID().'_permissions_count">'.count($role->getPermissions()).'</b>')); ?></span>
        <?php echo image_tag('spinning_16.gif', array('style' => 'display: none; margin: 2px 0 -2px 5px;', 'id' => 'role_'.$role->getID().'_permissions_list_indicator')); ?>
        <?php echo image_tag('spinning_16.gif', array('style' => 'display: none; margin: 2px 0 -2px 5px;', 'id' => 'role_'.$role->getID().'_permissions_edit_indicator')); ?>
        <div id="role_<?php echo $role->getID(); ?>_permissions_list" style="display: none;"></div>
        <div id="role_<?php echo $role->getID(); ?>_permissions_edit" style="display: none;"></div>
    </li>
<?php endif; ?>
