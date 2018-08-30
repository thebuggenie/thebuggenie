<?php /** @var \thebuggenie\core\entities\Project $project */ ?>
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
            <?php endif; ?>
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
