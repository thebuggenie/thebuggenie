<?php

/**
 * @var \thebuggenie\core\entities\Project $project
 * @var \thebuggenie\core\entities\Role[] $roles
 */

?>
<div class="backdrop_box large" id="project_config_popup_main_container">
    <div class="backdrop_detail_header">
        <span><?= ($project->getId()) ? __('Quick edit project') : __('Create new project'); ?></span>
        <a class="closer" href="javascript:void(0);" onclick="TBG.Main.Helpers.Backdrop.reset();"><?= fa_image_tag('times'); ?></a>
    </div>
    <div id="backdrop_detail_content" class="backdrop_detail_content">
        <?php if ($project->getId()): ?>
            <h5 style="font-size: 13px; text-align: left;">
                <div class="button button-blue" style="float: right; margin: -5px 5px 5px 0;"><?= link_tag(make_url('project_settings', ['project_key' => $project->getKey()]), '<span>'.__('More settings').'</span>'); ?></a></div>
                <?= __('Only showing basic project details. More settings available in the main project configuration.'); ?>
            </h5>
        <?php endif; ?>
        <?php if ($access_level == \thebuggenie\core\framework\Settings::ACCESS_FULL): ?>
            <form accept-charset="<?= \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>" action="<?= make_url('configure_project_settings', ['project_id' => $project->getID()]); ?>" method="post" onsubmit="TBG.Project.submitInfo('<?= make_url('configure_project_settings', ['project_id' => $project->getID()]); ?>', <?= $project->getID(); ?>); return false;" id="project_info">
        <?php endif; ?>
        <table style="clear: both; width: 780px;" class="padded_table" cellpadding=0 cellspacing=0>
            <tr>
                <td style="width: 200px;"><label for="project_name_input" style="font-size: 1.15em;"><?= __('Project name'); ?></label></td>
                <td style="width: 580px;">
                    <?php if ($access_level == \thebuggenie\core\framework\Settings::ACCESS_FULL): ?>
                        <input type="text" name="project_name" id="project_name_input" onblur="TBG.Project.updatePrefix('<?= make_url('configure_project_get_updated_key', ['project_id' => $project->getID()]); ?>', <?= $project->getID(); ?>);" value="<?php print $project->getName(); ?>" style="width: 576px; padding: 4px; font-size: 1.2em;">
                    <?php else: ?>
                        <?= $project->getName(); ?>
                    <?php endif; ?>
                </td>
            </tr>
            <?php if ($project->getId()): ?>
                <tr>
                    <td style="width: 200px;"><label for="project_key_input"><?= __('Project key'); ?></label></td>
                    <td style="width: 580px; position: relative;">
                        <?php if ($access_level == \thebuggenie\core\framework\Settings::ACCESS_FULL): ?>
                            <div id="project_key_indicator" class="semi_transparent" style="position: absolute; height: 23px; background-color: #FFF; width: 210px; text-align: center; display: none;"><?= image_tag('spinning_16.gif'); ?></div>
                            <input type="text" name="project_key" id="project_key_input" value="<?php print $project->getKey(); ?>" style="width: 150px;">
                        <?php else: ?>
                            <?= $project->getKey(); ?>
                        <?php endif; ?>
                        <div style="float: right; margin-right: 5px;" class="faded_out"><?= __('This is a part of all urls referring to this project'); ?></div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2"><label for="project_description_input"><?= __('Project description'); ?></label></td>
                </tr>
                <tr>
                    <td colspan="2">
                        <?php if ($access_level == \thebuggenie\core\framework\Settings::ACCESS_FULL): ?>
                            <?php include_component('main/textarea', ['area_name' => 'description', 'target_type' => 'project', 'target_id' => $project->getID(), 'area_id' => 'project_description_input', 'height' => '200px', 'width' => '100%', 'value' => $project->getDescription(), 'hide_hint' => true]); ?>
                        <?php elseif ($project->hasDescription()): ?>
                            <?= $project->getDescription(); ?>
                        <?php else: ?>
                            <span class="faded_out"><?= __('No description set'); ?></span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php else: ?>
                <tr>
                    <td colspan="2">
                        <label for="project_role_input"><?= __('Project permissions'); ?></label>
                        <div class="config_explanation">
                            <?= __('Choose the initial set of permissions and roles to apply. Permissions can be configured afterwards.'); ?>
                        </div>
                        <input type="checkbox" checked class="fancycheckbox" id="project_set_owner_checkbox" name="mark_as_owner" value="1"><label for="project_set_owner_checkbox"><?= fa_image_tag('check-square-o', ['class' => 'checked']) . fa_image_tag('square-o', ['class' => 'unchecked']) . __('Set myself as project owner'); ?></label><br>
                        <input type="hidden" name="assignee_type" value="<?= $assignee_type; ?>">
                        <input type="checkbox" checked class="fancycheckbox" id="project_role_checkbox" name="assignee_id" value="<?= $assignee_id; ?>" onchange="($('project_role_checkbox').checked) ? $('project_role_input').enable() : $('project_role_input').disable();"><label for="project_role_checkbox"><?= fa_image_tag('check-square-o', ['class' => 'checked']) . fa_image_tag('square-o', ['class' => 'unchecked']) . __('%name has the following role in this project: %list_of_roles', ['%name' => $assignee_name, '%list_of_roles' => '']); ?></label>
                        <select name="role_id" id="project_role_input">
                            <?php foreach ($roles as $role): ?>
                                <option value="<?= $role->getID(); ?>"><?= $role->getName(); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="project_type_container">
                        <label><?= __('Select type of project'); ?></label>
                        <div class="config_explanation">
                            <?= __('Select the type of project you are creating. The type of project decides initial workflows, issue types, settings and more. You can always configure this later.'); ?>
                        </div>
                        <div class="fancylabels">
                            <input type="radio" name="project_type" value="team" id="project_edit_type_team_project">
                            <label for="project_edit_type_team_project"><?= fa_image_tag('users'); ?><span><h1><?= __('Distributed teams project'); ?></h1><?= __('For projects with multiple teams, often distributed across locations'); ?></span></label>
                            <input type="radio" name="project_type" value="open-source" id="project_edit_type_open_source_regular">
                            <label for="project_edit_type_open_source_regular"><?= fa_image_tag('code-fork'); ?><span><h1><?= __('Classic open source'); ?></h1><?= __('For medium/small open source projects without multiple teams'); ?></span></label>
                            <input type="radio" name="project_type" value="classic" checked id="project_edit_type_regular">
                            <label for="project_edit_type_regular"><?= fa_image_tag('code'); ?><span><h1><?= __('Classic software project'); ?></h1><?= __('Classic project template without specific settings'); ?></span></label>
                            <input type="radio" name="project_type" value="agile" id="project_edit_type_agile">
                            <label for="project_edit_type_agile"><?= fa_image_tag('repeat', ['style' => 'transform: rotate(90deg)']); ?><span><h1><?= __('Agile software project'); ?></h1><?= __('For projects with an agile methodology like e.g. scrum or kanban'); ?></span></label>
                            <input type="radio" name="project_type" value="service-desk" id="project_edit_type_service_desk">
                            <label for="project_edit_type_service_desk"><?= fa_image_tag('phone'); ?><span><h1><?= __('Helpdesk / support'); ?></h1><?= __('For helpdesk or support projects without a traditional software development cycle'); ?></span></label>
                            <input type="radio" name="project_type" value="personal" id="project_edit_type_personal_todo">
                            <label for="project_edit_type_personal_todo"><?= fa_image_tag('th-list'); ?><span><h1><?= __('Personal todo-list'); ?></h1><?= __('A project acting like a personal todo-list. No fuzz, no headache.'); ?></span></label>
                        </div>
                    </td>
                </tr>
            <?php endif; ?>
        </table>
        <?php if ($access_level == \thebuggenie\core\framework\Settings::ACCESS_FULL): ?>
            <div class="backdrop_details_submit">
                <span class="explanation"><?= ($project->getId()) ? __('When you are done, click "%save" to save your changes', ['%save' => __('Save')]) : __('When you are done, click "%create_project"', ['%create_project' => __('Create project')]); ?></span>
                <div class="submit_container"><button class="button button-silver" id="project_submit_settings_button" onclick="TBG.Project.submitInfo('<?= make_url('configure_project_settings', ['project_id' => $project->getID()]); ?>', <?= $project->getID(); ?>);"><?= image_tag('spinning_16.gif', ['id' => 'project_info_indicator', 'style' => 'display: none;']) . (($project->getId()) ? __('Save') : __('Create project')); ?></button></div>
            </div>
        </form>
        <?php endif; ?>
    </div>
</div>
