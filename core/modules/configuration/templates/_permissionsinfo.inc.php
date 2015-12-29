<?php if ($mode == 'configuration'): ?>
    <?= __('Configuration access is always "Restrictive", regardless of the system settings.'); ?>
<?php elseif ($mode == 'datatype'): ?>
    <?= __("This setting allows who can set this datatype, assuming the have access to manipulate the field itself. Ex: If you don't have access to setting the status field, giving a user access to set one specific status won't let them manipulate the status field."); ?>
<?php endif; ?>
<?= tbg_parse_text(__('Please see [[ConfigurePermissions]] for more information about how permissions work in general.', array(), true)); ?><br>
<table cellpadding="0" cellspacing="0" style="width: 100%; margin-top: 10px;">
    <thead class="light">
        <tr>
            <th>&nbsp;</th>
            <?php if ($mode == 'datatype'): ?>
                <th style="width: 60px; text-align: center;"><?= __('Can set'); ?></th>
            <?php elseif ($mode == 'general'): ?>
                <th style="width: 60px; text-align: center;"><?= __('Can'); ?></th>
            <?php elseif (in_array($mode, array('configuration', 'pages', 'project_pages', 'project_hierarchy', 'module_permissions', 'user'))): ?>
                <th style="width: 60px; text-align: center;"><span title="<?= __('Inherited access / Explicit access'); ?>"><?= __('I / E'); ?></th>
            <?php else: ?>
                <th style="width: 60px;">&nbsp;</th>
            <?php endif; ?>
        </tr>
    </thead>
    <tbody>
        <tr class="hover_highlight">
            <td style="padding: 2px; border-bottom: 1px solid #EAEAEA;"><?= __('<b>Global </b>(Everyone with access)', array(), true); ?></td>
            <td style="padding: 2px; border-bottom: 1px solid #EAEAEA; text-align: center;">
                <?php include_component('configuration/permissionsinfoitem', array('key' => $key, 'target_id' => $target_id, 'type' => 'everyone', 'mode' => $mode, 'item_id' => 0, 'module' => $module, 'access_level' => $access_level)); ?>
            </td>
        </tr>
    </tbody>
</table>
<table cellpadding="0" cellspacing="0" style="width: 100%; margin-top: 10px; table-layout: fixed;">
    <tbody>
        <tr>
            <td style="width: 50%; vertical-align: top; padding-right: 5px; box-sizing: border-box;">
                <table cellpadding="0" cellspacing="0" style="width: 100%;">
                    <thead class="light">
                        <tr>
                            <th><?= __('Groups'); ?></th>
                            <?php if ($mode == 'datatype'): ?>
                                <th style="width: 60px; text-align: center;"><?= __('Can set'); ?></th>
                            <?php elseif ($mode == 'general'): ?>
                                <th style="width: 60px; text-align: center;"><?= __('Can'); ?></th>
                            <?php elseif (in_array($mode, array('configuration', 'pages', 'project_pages', 'project_hierarchy', 'module_permissions', 'user'))): ?>
                                <th style="width: 60px; text-align: center;"><span title="<?= __('Inherited access / Explicit access'); ?>"><?= __('I / E'); ?></th>
                            <?php else: ?>
                                <th style="width: 60px;">&nbsp;</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <?php $groups = \thebuggenie\core\entities\Group::getAll(); ?>
                    <?php foreach ($groups as $group): ?>
                        <tr class="hover_highlight">
                            <td style="padding: 2px;"><?= $group->getName(); ?></td>
                            <td style="padding: 2px; text-align: center;">
                                <?php include_component('configuration/permissionsinfoitem', array('key' => $key, 'target_id' => $target_id, 'type' => 'group', 'mode' => $mode, 'item_id' => $group->getID(), 'item_name' => $group->getName(), 'module' => $module, 'access_level' => $access_level)); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </td>
            <td style="width: 50%; vertical-align: top; padding-left: 5px; box-sizing: border-box;">
                <?php $teams = \thebuggenie\core\framework\Context::isProjectContext() ? \thebuggenie\core\framework\Context::getCurrentProject()->getAssignedTeams() : \thebuggenie\core\entities\Team::getAll(); ?>
                <?php if (!count($teams)): ?>
                    <span class="faded_out"><?= __('There are no teams'); ?></span>
                <?php else: ?>
                    <table cellpadding="0" cellspacing="0" style="width: 100%;">
                        <thead class="light">
                        <tr>
                            <th><?= __('Teams'); ?></th>
                            <?php if ($mode == 'datatype'): ?>
                                <th style="width: 60px; text-align: center;"><?= __('Can set'); ?></th>
                            <?php elseif ($mode == 'general'): ?>
                                <th style="width: 60px; text-align: center;"><?= __('Can'); ?></th>
                            <?php elseif (in_array($mode, array('configuration', 'pages', 'project_pages', 'project_hierarchy', 'module_permissions', 'user'))): ?>
                                <th style="width: 60px; text-align: center;"><span title="<?= __('Inherited access / Explicit access'); ?>"><?= __('I / E'); ?></th>
                            <?php else: ?>
                                <th style="width: 60px;">&nbsp;</th>
                            <?php endif; ?>
                        </tr>
                        </thead>
                        <?php foreach ($teams as $team): ?>
                            <tr class="hover_highlight">
                                <td style="padding: 2px;"><?= $team->getName(); ?></td>
                                <td style="padding: 2px; text-align: center;">
                                    <?php include_component('configuration/permissionsinfoitem', array('key' => $key, 'type' => 'team', 'target_id' => $target_id, 'mode' => $mode, 'item_id' => $team->getID(), 'item_name' => $team->getName(), 'module' => $module, 'access_level' => $access_level)); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                <?php endif; ?>
            </td>
        </tr>
    </tbody>
</table>
