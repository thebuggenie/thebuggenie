<?php $tbg_response->setTitle(__('Configure scopes')); ?>
<table style="table-layout: fixed; width: 100%" cellpadding=0 cellspacing=0>
    <tr>
        <?php include_component('leftmenu', array('selected_section' => \thebuggenie\core\framework\Settings::CONFIGURATION_SECTION_SCOPES)); ?>
        <td valign="top" style="padding-left: 15px;">
            <form method="post" accept-charset="<?php echo \thebuggenie\core\framework\Settings::getCharset(); ?>">
                <div style="width: 730px;" class="config_header"><?php echo __('Configure scope "%scope_name"', array('%scope_name' => $scope->getName())); ?></div>
                <div style="width: 730px;" id="config_scopes">
                    <?php if ($scope_save_error): ?>
                        <div class="redbox" style="margin: 0 0 5px 0; font-size: 14px;">
                            <?php echo $scope_save_error; ?>
                        </div>
                    <?php endif; ?>
                    <div class="greybox">
                        <div class="content">
                            <table style="clear: both; width: 700px; margin-top: 5px;" class="padded_table" cellpadding=0 cellspacing=0>
                                <tr>
                                    <td style="width: 200px;"><label for="scope_name_input"><?php echo __('Scope name'); ?></label></td>
                                    <td style="width: auto;"><input type="text" id="scope_name_input" name="name" value="<?php echo $scope->getName(); ?>" style="width: 300px;"></td>
                                </tr>
                                <tr>
                                    <td><label for="scope_description_input"><?php echo __('Scope description'); ?></label></td>
                                    <td><input id="scope_description_input" name="description" value="<?php echo $scope->getDescription(); ?>" style="width: 500px;"></td>
                                </tr>
                                <tr>
                                    <td><label><?php echo __('Scope hostname'); ?></label></td>
                                    <td>
                                        <?php foreach ($scope->getHostnames() as $hostname): ?>
                                            <?php /* <a href="javascript:void(0);" onclick="TBG.Main.Helpers.Message.error('not implemented yet')" class="rounded_box action_button" style="float: left; margin-left: 0; margin-right: 5px;"><?php echo image_tag('icon_delete.png', array('title' => __('Delete this hostname'))); ?></a> */ ?>
                                            <?php echo $hostname; ?>
                                            <br style="clear: both;">
                                        <?php endforeach; ?>
                                    </td>
                                </tr>
                                <tr><td class="config_explanation" colspan="2"><?php echo __('This is the list of hostnames for which this scope will be active.'); ?></td></tr>
                                <tr>
                                    <td><label for="scope_workflows_yes"><?php echo __('Allow custom workflows'); ?></label></td>
                                    <td>
                                        <input type="radio"<?php if ($scope->isCustomWorkflowsEnabled()): ?> checked<?php endif; ?> id="scope_workflows_yes" name="custom_workflows_enabled" value="1">
                                        <label for="scope_workflows_yes" style="font-weight: normal;"><?php echo __('Yes'); ?></label>&nbsp;
                                        <input type="radio"<?php if (!$scope->isCustomWorkflowsEnabled()): ?> checked<?php endif; ?> id="scope_workflows_no" name="custom_workflows_enabled" value="0">
                                        <label for="scope_workflows_no" style="font-weight: normal;"><?php echo __('No'); ?></label>&nbsp;
                                    </td>
                                </tr>
                                <tr>
                                    <td><label for="scope_workflow_limit"><?php echo __('Custom workflows'); ?></label></td>
                                    <td><input id="scope_workflow_limit" name="workflow_limit" value="<?php echo $scope->getMaxWorkflowsLimit(); ?>" style="width: 30px; text-align: right;"></td>
                                </tr>
                                <tr><td class="config_explanation" colspan="2"><?php echo __('Setting the workflow limit to "0" disables limitations on number of custom workflows completely.'); ?></td></tr>
                                <tr>
                                    <td><label for="scope_uploads_yes"><?php echo __('Allow file uploads'); ?></label></td>
                                    <td>
                                        <input type="radio"<?php if ($scope->isUploadsEnabled()): ?> checked<?php endif; ?> id="scope_uploads_yes" name="file_uploads_enabled" value="1">
                                        <label for="scope_uploads_yes" style="font-weight: normal;"><?php echo __('Yes'); ?></label>&nbsp;
                                        <input type="radio"<?php if (!$scope->isUploadsEnabled()): ?> checked<?php endif; ?> id="scope_uploads_no" name="file_uploads_enabled" value="0">
                                        <label for="scope_uploads_no" style="font-weight: normal;"><?php echo __('No'); ?></label>&nbsp;
                                    </td>
                                </tr>
                                <tr>
                                    <td><label for="scope_upload_limit"><?php echo __('Total upload quota'); ?></label></td>
                                    <td><input id="scope_upload_limit" name="upload_limit" value="<?php echo $scope->getMaxUploadLimit(); ?>" style="width: 30px; text-align: right;"> MB</td>
                                </tr>
                                <tr><td class="config_explanation" colspan="2"><?php echo __('Setting the upload quota to "0" MB disables the qouta completely'); ?></td></tr>
                                <tr>
                                    <td><label for="scope_project_limit"><?php echo __('Max projects'); ?></label></td>
                                    <td><input id="scope_project_limit" name="project_limit" value="<?php echo $scope->getMaxProjects(); ?>" style="width: 30px; text-align: right;"></td>
                                </tr>
                                <tr><td class="config_explanation" colspan="2"><?php echo __('Total number of allowed projects. Setting the value to "0" disables limitations on number of projects.'); ?></td></tr>
                                <tr>
                                    <td><label for="scope_user_limit"><?php echo __('Max users'); ?></label></td>
                                    <td><input id="scope_user_limit" name="user_limit" value="<?php echo $scope->getMaxUsers(); ?>" style="width: 30px; text-align: right;"></td>
                                </tr>
                                <tr><td class="config_explanation" colspan="2"><?php echo __('Total number of allowed users. Setting the value to "0" disables limitations on number of users.'); ?></td></tr>
                                <tr>
                                    <td><label for="scope_team_limit"><?php echo __('Max teams'); ?></label></td>
                                    <td><input id="scope_team_limit" name="team_limit" value="<?php echo $scope->getMaxTeams(); ?>" style="width: 30px; text-align: right;"></td>
                                </tr>
                                <tr><td class="config_explanation" colspan="2"><?php echo __('Total number of allowed teams. Setting the value to "0" disables limitations on number of teams.'); ?></td></tr>
                            </table>
                            <div class="header" style="margin: 20px 0 5px 0;"><?php echo __('Available modules'); ?></div>
                            <table style="clear: both; width: 700px;" class="padded_table" cellpadding=0 cellspacing=0>
                                <?php foreach (\thebuggenie\core\framework\Context::getModules() as $module): ?>
                                    <?php $module_is_disabled = (array_key_exists($module->getName(), $modules) && !$modules[$module->getName()]); ?>
                                    <tr>
                                        <td style="width: 300px; vertical-align: top;"><label for="module_<?php echo $module->getName(); ?>_available_yes"<?php if ($module_is_disabled): ?> class="faded_out" title="<?php echo __('This module has been disabled in the selected scope by its admin'); ?>"<?php endif; ?>><?php echo $module->getLongname(); ?></label></td>
                                        <td style="width: auto;">
                                            <?php if ($module->isCore()): ?>
                                                <?php echo image_tag('action_ok.png', array('style' => 'float: left;')); ?>&nbsp;<?php echo __('Available'); ?>&nbsp;<span class="faded_out">(<?php echo __('This is an internal module which must be available'); ?>)</span>
                                            <?php else: ?>
                                                <input type="radio"<?php if (array_key_exists($module->getName(), $modules)): ?> checked<?php endif; ?> name="module_enabled[<?php echo $module->getName(); ?>]" id="module_<?php echo $module->getName(); ?>_available_yes" value="1">
                                                <label for="module_<?php echo $module->getName(); ?>_available_yes" style="font-weight: normal;"><?php echo __('Available'); ?></label>&nbsp;
                                                <input type="radio"<?php if (!array_key_exists($module->getName(), $modules)): ?> checked<?php endif; ?> name="module_enabled[<?php echo $module->getName(); ?>]" id="module_<?php echo $module->getName(); ?>_available_no" value="0">
                                                <label for="module_<?php echo $module->getName(); ?>_available_no" style="font-weight: normal;"><?php echo __('Not available'); ?></label>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </table>
                        </div>
                        <input type="submit" value="<?php echo __('Save settings'); ?>" style="font-weight: bold; float: right; margin: 10px;">
                        <br style="clear: both;">
                    </div>
                </div>
            </form>
        </td>
    </tr>
</table>
