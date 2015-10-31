<?php if ($mode == 'configuration'): ?>
    <?php echo __('Configuration access is always "Restrictive", regardless of the system settings.'); ?> 
<?php elseif ($mode == 'datatype'): ?>
    <?php echo __("This setting allows who can set this datatype, assuming the have access to manipulate the field itself. Ex: If you don't have access to setting the status field, giving a user access to set one specific status won't let them manipulate the status field."); ?> 
<?php endif; ?>
<?php echo tbg_parse_text(__('Please see [[ConfigurePermissions]] for more information about how permissions work in general.', array(), true)); ?><br>
<table cellpadding="0" cellspacing="0" style="width: 100%; margin-top: 10px;">
    <thead class="light">
        <tr>
            <th><?php echo __('Users / groups / teams'); ?></th>
            <?php if ($mode == 'datatype'): ?>
                <th style="width: 60px; text-align: center;"><?php echo __('Can set'); ?></th>
            <?php elseif ($mode == 'general'): ?>
                <th style="width: 60px; text-align: center;"><?php echo __('Can'); ?></th>
            <?php elseif (in_array($mode, array('configuration', 'pages', 'project_pages', 'project_hierarchy', 'module_permissions', 'user'))): ?>
                <th style="width: 60px; text-align: center;"><?php echo __('Access'); ?></th>
            <?php else: ?>
                <th style="width: 60px;">&nbsp;</th>
            <?php endif; ?>
        </tr>
    </thead>
    <tbody>
        <tr class="hover_highlight">
            <td style="padding: 2px; border-bottom: 1px solid #EAEAEA;"><?php echo __('<b>Global </b>(Everyone with access)', array(), true); ?></td>
            <td style="padding: 2px; border-bottom: 1px solid #EAEAEA; text-align: center;">
                <?php include_component('configuration/permissionsinfoitem', array('key' => $key, 'target_id' => $target_id, 'type' => 'everyone', 'mode' => $mode, 'item_id' => 0, 'module' => $module, 'access_level' => $access_level)); ?>
            </td>
        </tr>
        <?php $groups = \thebuggenie\core\entities\Group::getAll(); ?>
        <?php $gcount = count($groups); $cc = 1; ?>
        <?php foreach ($groups as $group): ?>
            <tr class="hover_highlight">
                <td style="padding: 2px;<?php if ($cc == $gcount): ?> border-bottom: 1px solid #EAEAEA;<?php endif; ?>"><?php echo '<b>'.__('Group: %group_name', array('%group_name' => '</b>'.$group->getName())); ?></td>
                <td style="padding: 2px;<?php if ($cc == $gcount): ?> border-bottom: 1px solid #EAEAEA;<?php endif; ?> text-align: center;">
                    <?php include_component('configuration/permissionsinfoitem', array('key' => $key, 'target_id' => $target_id, 'type' => 'group', 'mode' => $mode, 'item_id' => $group->getID(), 'item_name' => $group->getName(), 'module' => $module, 'access_level' => $access_level)); ?>
                </td>
            </tr>
            <?php $cc++; ?>
        <?php endforeach; ?>
        <?php $teams = \thebuggenie\core\entities\Team::getAll(); ?>
        <?php foreach ($teams as $team): ?>
            <tr class="hover_highlight">
                <td style="padding: 2px;"><?php echo '<b>'.__('Team: %team_name', array('%team_name' => '</b>'.$team->getName())); ?></td>
                <td style="padding: 2px; text-align: center;">
                    <?php include_component('configuration/permissionsinfoitem', array('key' => $key, 'type' => 'team', 'target_id' => $target_id, 'mode' => $mode, 'item_id' => $team->getID(), 'item_name' => $team->getName(), 'module' => $module, 'access_level' => $access_level)); ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
