<div class="backdrop_box large" id="project_config_popup_main_container">
    <div class="backdrop_detail_header">
        <?= ($project->getId()) ? __('Quick edit project') : __('Create new project'); ?>
    </div>
    <div id="backdrop_detail_content" class="backdrop_detail_content">
        <?php if ($project->getId()): ?>
            <h5 style="font-size: 13px; text-align: left;">
                <div class="button button-blue" style="float: right; margin: -5px 5px 5px 0;"><?= link_tag(make_url('project_settings', array('project_key' => $project->getKey())), '<span>'.__('More settings').'</span>'); ?></a></div>
                <?= __('Only showing basic project details. More settings available in the main project configuration.'); ?>
            </h5>
        <?php endif; ?>
        <?php if ($access_level == \thebuggenie\core\framework\Settings::ACCESS_FULL): ?>
            <form accept-charset="<?= \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>" action="<?= make_url('configure_project_settings', array('project_id' => $project->getID())); ?>" method="post" onsubmit="TBG.Project.submitInfo('<?= make_url('configure_project_settings', array('project_id' => $project->getID())); ?>', <?= $project->getID(); ?>); return false;" id="project_info">
        <?php endif; ?>
        <table style="clear: both; width: 780px;" class="padded_table" cellpadding=0 cellspacing=0>
            <tr>
                <td style="width: 200px;"><label for="project_name_input" style="font-size: 1.15em;"><?= __('Project name'); ?></label></td>
                <td style="width: 580px;">
                    <?php if ($access_level == \thebuggenie\core\framework\Settings::ACCESS_FULL): ?>
                        <input type="text" name="project_name" id="project_name_input" onblur="TBG.Project.updatePrefix('<?= make_url('configure_project_get_updated_key', array('project_id' => $project->getID())); ?>', <?= $project->getID(); ?>);" value="<?php print $project->getName(); ?>" style="width: 576px; padding: 4px; font-size: 1.2em;">
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
                <td><label for="use_prefix"><?= __('Use prefix'); ?></label></td>
                <td>
                    <?php if ($access_level == \thebuggenie\core\framework\Settings::ACCESS_FULL): ?>
                        <select name="use_prefix" id="use_prefix" style="width: 70px;" onchange="if ($('use_prefix').getValue() == 1) { $('prefix_input').enable();$('prefix_input').focus(); } else { $('prefix_input').disable(); }">
                            <option value=1<?php if ($project->usePrefix()): ?> selected<?php endif; ?>><?= __('Yes'); ?></option>
                            <option value=0<?php if (!$project->usePrefix()): ?> selected<?php endif; ?>><?= __('No'); ?></option>
                        </select>
                    <?php else: ?>
                        <?= ($project->usePrefix()) ? __('Yes') : __('No'); ?>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <td><label for="prefix_input"><?= __('Issue prefix'); ?></label></td>
                <td>
                    <?php if ($access_level == \thebuggenie\core\framework\Settings::ACCESS_FULL): ?>
                        <input type="text" name="prefix" id="prefix_input" maxlength="5" value="<?php print $project->getPrefix(); ?>" style="width: 70px;"<?php if (!$project->usePrefix()): ?> disabled<?php endif; ?>>
                    <?php elseif ($project->hasPrefix()): ?>
                        <?= $project->getPrefix(); ?>
                    <?php else: ?>
                        <span class="faded_out"><?= __('No prefix set'); ?></span>
                    <?php endif; ?>
                    <div style="float: right; margin-right: 5px;" class="faded_out"><?= __('See %about_issue_prefix for an explanation about issue prefixes', array('%about_issue_prefix' => link_tag(make_url('publish_article', array('article_name' => 'AboutIssuePrefixes')), __('about issue prefixes'), array('target' => '_new')))); ?></div>
                </td>
            </tr>
            <tr>
                <td><label for="project_description_input"><?= __('Project description'); ?></label></td>
                <td>
                    <?php if ($access_level == \thebuggenie\core\framework\Settings::ACCESS_FULL): ?>
                        <?php include_component('main/textarea', array('area_name' => 'description', 'target_type' => 'project', 'target_id' => $project->getID(), 'area_id' => 'project_description_input', 'height' => '75px', 'width' => '100%', 'value' => $project->getDescription(), 'hide_hint' => true)); ?>
                    <?php elseif ($project->hasDescription()): ?>
                        <?= $project->getDescription(); ?>
                    <?php else: ?>
                        <span class="faded_out"><?= __('No description set'); ?></span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php if ($access_level == \thebuggenie\core\framework\Settings::ACCESS_FULL): ?>
            <tr>
                <td colspan="2" style="padding: 10px 0 10px 10px; text-align: right;">
                    <div style="float: left; font-size: 13px; padding-top: 2px; font-style: italic;" class="config_explanation"><?= __('When you are done, click "%save" to save your changes', array('%save' => __('Save'))); ?></div>
                    <?= image_tag('spinning_16.gif', array('id' => 'project_info_indicator', 'style' => 'display: none; margin-right: 5px;')); ?>
                    <div class="button button-green" id="project_submit_settings_button" onclick="TBG.Project.submitInfo('<?= make_url('configure_project_settings', array('project_id' => $project->getID())); ?>', <?= $project->getID(); ?>);"><span><?= __('Save'); ?></span></div>
                </td>
            </tr>
        <?php endif; ?>
        </table>
        <?php if ($access_level == \thebuggenie\core\framework\Settings::ACCESS_FULL): ?>
        </form>
        <?php endif; ?>
    </div>
    <div class="backdrop_detail_footer">
        <?= image_tag('spinning_32.gif', array('id' => 'backdrop_detail_indicator', 'style' => 'display: none; float: right; margin-left: 5px;')); ?>
        <a href="javascript:void(0);" onclick="TBG.Main.Helpers.Backdrop.reset();"><?= __('Close'); ?></a>
    </div>
</div>
