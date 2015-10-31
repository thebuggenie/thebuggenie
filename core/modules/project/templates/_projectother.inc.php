<?php if ($access_level == \thebuggenie\core\framework\Settings::ACCESS_FULL): ?>
    <form action="<?php echo make_url('configure_project_updateother', array('project_id' => $project->getID())); ?>" method="post" id="project_other" onsubmit="TBG.Project.submitDisplaySettings('<?php echo make_url('configure_project_updateother', array('project_id' => $project->getID())); ?>'); return false;">
        <div class="project_save_container">
            <span id="project_other_indicator" style="display: none;"><?php echo image_tag('spinning_20.gif'); ?></span>
            <input class="button button-silver" type="submit" id="project_submit_settings_button" value="<?php echo __('Save display settings'); ?>">
        </div>
<?php endif; ?>
    <table class="padded_table" cellpadding=0 cellspacing=0>
        <tr>
            <td><label for="frontpage_summary"><?php echo __('On the frontpage summary, show %name_of_setting', array('%name_of_setting' => '')); ?></label></td>
            <td>
            <?php if ($access_level == \thebuggenie\core\framework\Settings::ACCESS_FULL): ?>
                <select name="frontpage_summary" id="frontpage_summary" <?php if ($access_level == \thebuggenie\core\framework\Settings::ACCESS_FULL): ?>onchange="$('checkboxes_issuetypes').hide();$('checkboxes_issuelist').hide();$('checkboxes_milestones').hide();if ($('checkboxes_'+this.getValue())) { $('checkboxes_'+this.getValue()).show(); }"<?php else: ?> disabled<?php endif; ?>>
                    <option value=""<?php if (!$project->isAnythingVisibleInFrontpageSummary()): ?> selected<?php endif; ?>><?php echo __('%on_frontpage_comma_show only project information', array('%on_frontpage_comma_show' => '')); ?></option>
                    <option value="milestones"<?php if ($project->isMilestonesVisibleInFrontpageSummary()): ?> selected<?php endif; ?>><?php echo __('%on_frontpage_comma_show status per milestone', array('%on_frontpage_comma_show' => '')); ?></option>
                    <option value="issuetypes"<?php if ($project->isIssuetypesVisibleInFrontpageSummary()): ?> selected<?php endif; ?>><?php echo __('%on_frontpage_comma_show status per issue types', array('%on_frontpage_comma_show' => '')); ?></option>
                    <option value="issuelist"<?php if ($project->isIssuelistVisibleInFrontpageSummary()): ?> selected<?php endif; ?>><?php echo __('%on_frontpage_comma_show list of open issues', array('%on_frontpage_comma_show' => '')); ?></option>
                </select>
                <div id="checkboxes_issuetypes" style="margin-top: 5px;<?php if (!$project->isIssuetypesVisibleInFrontpageSummary()): ?> display: none;<?php endif;?>">
                    <?php foreach ($project->getIssuetypeScheme()->getIssueTypes() as $issuetype): ?>
                        <div style="clear: both; font-size: 12px;">
                            <input type="checkbox" name="showissuetype[<?php echo $issuetype->getID(); ?>]" onChange="$('checkboxes_issuelist').select('input#showissuetype_<?php echo $issuetype->getID(); ?>')[0].checked=this.checked;" value="<?php echo $issuetype->getID(); ?>"<?php if ($project->isIssuetypeVisible($issuetype->getID())): ?> checked<?php endif; ?> id="showissuetype_<?php echo $issuetype->getID(); ?>" style="float: left;"<?php if ($access_level != \thebuggenie\core\framework\Settings::ACCESS_FULL): ?> disabled<?php endif; ?>>
                            <label for="showissuetype_<?php echo $issuetype->getID(); ?>"><?php echo __('Show %issuetype', array('%issuetype' => $issuetype->getName())); ?></label>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div id="checkboxes_issuelist" style="margin-top: 5px;<?php if (!$project->isIssuelistVisibleInFrontpageSummary()): ?> display: none;<?php endif;?>">
                    <?php foreach ($project->getIssuetypeScheme()->getIssueTypes() as $issuetype): ?>
                        <div style="clear: both; font-size: 12px;">
                            <input type="checkbox" name="showissuetype[<?php echo $issuetype->getID(); ?>]" onChange="$('checkboxes_issuetypes').select('input#showissuetype_<?php echo $issuetype->getID(); ?>')[0].checked=this.checked;" value="<?php echo $issuetype->getID(); ?>"<?php if ($project->isIssuetypeVisible($issuetype->getID())): ?> checked<?php endif; ?> id="showissuetype_<?php echo $issuetype->getID(); ?>" style="float: left;"<?php if ($access_level != \thebuggenie\core\framework\Settings::ACCESS_FULL): ?> disabled<?php endif; ?>>
                            <label for="showissuetype_<?php echo $issuetype->getID(); ?>"><?php echo __('Show %issuetype', array('%issuetype' => $issuetype->getName())); ?></label>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div id="checkboxes_milestones" style="margin-top: 5px;<?php if (!$project->isMilestonesVisibleInFrontpageSummary()): ?> display: none;<?php endif;?>">
                    <?php if (count($project->getMilestones()) == 0): ?>
                        <div class="faded_out" style="padding: 5px; font-size: 1.1em;"><?php echo __('There are no milestones'); ?></div>
                    <?php else: ?>
                        <?php foreach ($project->getMilestonesForRoadmap() as $milestone): ?>
                            <?php if ($milestone->isVisibleIssues() || $milestone->isVisibleRoadmap() || $project->isMilestoneVisible($milestone->getID())): ?>
                                <div style="clear: both; font-size: 12px;">
                                    <input type="checkbox" name="showmilestone[<?php echo $milestone->getID(); ?>]" value="<?php echo $milestone->getID(); ?>"<?php if ($project->isMilestoneVisible($milestone->getID())): ?> checked<?php endif; ?> id="showmilestone_<?php echo $milestone->getID(); ?>" style="float: left;"<?php if ($access_level != \thebuggenie\core\framework\Settings::ACCESS_FULL): ?> disabled<?php endif; ?>>
                                    <label for="showmilestone_<?php echo $milestone->getID(); ?>"><?php echo __('Show %milestone', array('%milestone' => $milestone->getName())); ?></label>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <?php

                    if (!$project->isAnythingVisibleInFrontpageSummary()) echo __('%on_frontpage_comma_show only project information', array('%on_frontpage_comma_show' => ''));
                    elseif ($project->isMilestonesVisibleInFrontpageSummary()) echo __('%on_frontpage_comma_show status per milestone', array('%on_frontpage_comma_show' => ''));
                    elseif ($project->isIssuetypesVisibleInFrontpageSummary()) echo __('%on_frontpage_comma_show status per issue types', array('%on_frontpage_comma_show' => ''));
                    elseif ($project->isIssuelistVisibleInFrontpageSummary()) echo __('%on_frontpage_comma_show list of open issues', array('%on_frontpage_comma_show' => ''));

                ?>
            <?php endif; ?>
            </td>
        </tr>
        <tr>
            <td><label for="project_downloads_enabled"><?php echo __('Show project downloads'); ?></label></td>
            <td>
                <?php if ($access_level == \thebuggenie\core\framework\Settings::ACCESS_FULL): ?>
                    <select name="has_downloads" id="released" style="width: 70px;">
                        <option value=1<?php if ($project->hasDownloads()): ?> selected<?php endif; ?>><?php echo __('Yes'); ?></option>
                        <option value=0<?php if (!$project->hasDownloads()): ?> selected<?php endif; ?>><?php echo __('No'); ?></option>
                    </select>
                <?php else: ?>
                    <?php echo ($project->hasDownloads()) ? __('Yes') : __('No'); ?>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <td colspan="2" class="faded_out"><?php echo __('Choose whether download links and tools are available'); ?></td>
        </tr>
    </table>
<?php if ($access_level == \thebuggenie\core\framework\Settings::ACCESS_FULL): ?>
</form>
<?php endif; ?>
