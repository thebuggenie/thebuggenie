<?php

    use thebuggenie\core\entities\Milestone;

    $savebuttonlabel = (isset($savebuttonlabel)) ? $savebuttonlabel : __e('Save milestone');
    $milestonenamelabel = (isset($milestonenamelabel)) ? $milestonenamelabel : __e('Milestone name');
    $milestoneplaceholder = (isset($milestoneplaceholder)) ? $milestoneplaceholder : __e('Enter a milestone name');
    if (!isset($milestoneheader)) {
        $milestoneheader = ($milestone->getId()) ? __('Edit milestone details') : __('Add milestone');
    }
    $milestone_type = (isset($milestone_type)) ? $milestone_type : $milestone->getType();
    $milestoneincludeissues_text = (isset($milestoneincludeissues_text)) ? $milestoneincludeissues_text : __('The %number selected issue(s) will be automatically assigned to the new milestone', array('%number' => '<span id="milestone_include_num_issues"></span>'));
    $action_url = (isset($action_url)) ? $action_url : make_url('project_milestone', array('project_key' => $milestone->getProject()->getKey(), 'milestone_id' => (int) $milestone->getID()));

?>
<div class="backdrop_box large sectioned" id="edit_milestone_container" style="<?php if (isset($starthidden) && $starthidden) echo 'display: none;'; ?>">
    <div class="backdrop_detail_header"><?php echo $milestoneheader; ?></div>
    <div id="backdrop_detail_content" class="backdrop_detail_content edit_milestone">
            <?php if (!isset($includeform) || $includeform): ?>
        <form accept-charset="<?php echo \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>" action="<?php echo $action_url; ?>" method="post" id="edit_milestone_form" onsubmit="TBG.Project.Milestone.save(this);return false;">
            <?php endif; ?>
            <label for="milestone_name_<?php echo $milestone->getID(); ?>"><?php echo $milestonenamelabel; ?></label>
            <input type="text" class="milestone_input_name primary" value="<?php echo $milestone->getName(); ?>" name="name" id="milestone_name_<?php echo $milestone->getID(); ?>" placeholder="<?php echo $milestoneplaceholder; ?>">
            <label for="milestone_description_<?php echo $milestone->getID(); ?>"><?php echo __('Description'); ?></label>
            <input type="text" class="milestone_input_description secondary" value="<?php echo $milestone->getDescription(); ?>" name="description" id="milestone_description_<?php echo $milestone->getID(); ?>">
            <table class="sectioned_table">
                <tr>
                    <td><label for="milestone_visibility_roadmap_<?php echo $milestone->getID(); ?>"><?php echo __('Project roadmap visibility'); ?></label></td>
                    <td>
                        <select name="visibility_roadmap" id="milestone_visibility_roadmap_<?php echo $milestone->getID(); ?>">
                            <option value="0"<?php if (!$milestone->isVisibleRoadmap()): ?> selected<?php endif; ?>><?php echo __('Not visible'); ?></option>
                            <option value="1"<?php if ($milestone->isVisibleRoadmap()): ?> selected<?php endif; ?>><?php echo __('Visible'); ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label for="milestone_visibility_issues_<?php echo $milestone->getID(); ?>"><?php echo __('Issue availability'); ?></label></td>
                    <td>
                        <select name="visibility_issues" id="milestone_visibility_issues_<?php echo $milestone->getID(); ?>">
                            <option value="0"<?php if (!$milestone->isVisibleIssues()): ?> selected<?php endif; ?>><?php echo __('Not available'); ?></option>
                            <option value="1"<?php if ($milestone->isVisibleIssues()): ?> selected<?php endif; ?>><?php echo __('Available'); ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label for="starting_date_<?php echo $milestone->getID(); ?>"><?php echo __('Planned start date'); ?></label></td>
                    <td style="width: auto;">
                        <input type="checkbox" value="1" name="is_starting" id="starting_date_<?php echo $milestone->getID(); ?>" onchange="if ($('starting_date_<?php echo $milestone->getID(); ?>').getValue() == '1') { $('starting_month_<?php echo $milestone->getID(); ?>').enable(); $('starting_day_<?php echo $milestone->getID(); ?>').enable(); $('starting_year_<?php echo $milestone->getID(); ?>').enable(); } else { $('starting_month_<?php echo $milestone->getID(); ?>').disable(); $('starting_day_<?php echo $milestone->getID(); ?>').disable(); $('starting_year_<?php echo $milestone->getID(); ?>').disable(); } " <?php if ($milestone->isStarting()) echo 'checked'; ?>>
                        <select style="width: 90px;" name="starting_month" id="starting_month_<?php echo $milestone->getID(); ?>"<?php if (!$milestone->hasStartingDate()): ?> disabled<?php endif; ?>>
                        <?php for ($cc = 1;$cc <= 12;$cc++): ?>
                            <option value="<?php echo $cc; ?>" <?php if ($milestone->getStartingMonth() == $cc || (!$milestone->hasStartingDate() && $cc == date('m'))) echo " selected"; ?>><?php echo strftime('%B', mktime(0, 0, 0, $cc, 1)); ?></option>
                        <?php endfor; ?>
                        </select>
                        <select style="width: 45px;" name="starting_day" id="starting_day_<?php echo $milestone->getID(); ?>"<?php if (!$milestone->hasStartingDate()): ?> disabled<?php endif; ?>>
                        <?php for ($cc = 1;$cc <= 31;$cc++): ?>
                            <option value="<?php echo $cc; ?>" <?php if ($milestone->getStartingDay() == $cc || (!$milestone->hasStartingDate() && $cc == date('d'))) echo " selected"; ?>><?php echo $cc; ?></option>
                        <?php endfor; ?>
                        </select>
                        <select style="width: 60px;" name="starting_year" id="starting_year_<?php echo $milestone->getID(); ?>"<?php if (!$milestone->hasStartingDate()): ?> disabled<?php endif; ?>>
                        <?php for ($cc = 1990;$cc <= (date("Y") + 10);$cc++): ?>
                            <option value="<?php echo $cc; ?>" <?php if ($milestone->getStartingYear() == $cc || (!$milestone->hasStartingDate() && $cc == date('Y'))) echo " selected"; ?>><?php echo $cc; ?></option>
                        <?php endfor; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label for="sch_date_<?php echo $milestone->getID(); ?>"><?php echo __('Planned end date'); ?></label></td>
                    <td style="width: auto;">
                        <input type="checkbox" value="1" name="is_scheduled" id="sch_date_<?php echo $milestone->getID(); ?>" onchange="if ($('sch_date_<?php echo $milestone->getID(); ?>').getValue() == '1') { $('sch_month_<?php echo $milestone->getID(); ?>').enable(); $('sch_day_<?php echo $milestone->getID(); ?>').enable(); $('sch_year_<?php echo $milestone->getID(); ?>').enable(); } else { $('sch_month_<?php echo $milestone->getID(); ?>').disable(); $('sch_day_<?php echo $milestone->getID(); ?>').disable(); $('sch_year_<?php echo $milestone->getID(); ?>').disable(); } " <?php if ($milestone->isScheduled()) echo 'checked'; ?>>
                        <select style="width: 90px;" name="sch_month" id="sch_month_<?php echo $milestone->getID(); ?>" <?php print (!$milestone->hasScheduledDate()) ? "disabled" : ""; ?>>
                        <?php for ($cc = 1;$cc <= 12;$cc++): ?>
                            <option value="<?php echo $cc; ?>" <?php if ($milestone->getScheduledMonth() == $cc || (!$milestone->hasScheduledDate() && $cc == date('m'))) echo " selected"; ?>><?php echo strftime('%B', mktime(0, 0, 0, $cc, 1)); ?></option>
                        <?php endfor; ?>
                        </select>
                        <select style="width: 45px;" name="sch_day" id="sch_day_<?php echo $milestone->getID(); ?>" <?php print (!$milestone->hasScheduledDate()) ? "disabled" : ""; ?>>
                        <?php for ($cc = 1;$cc <= 31;$cc++): ?>
                            <option value="<?php echo $cc; ?>" <?php if ($milestone->getScheduledDay() == $cc || (!$milestone->hasScheduledDate() && $cc == date('d'))) echo " selected"; ?>><?php echo $cc; ?></option>
                        <?php endfor; ?>
                        </select>
                        <select style="width: 60px;" name="sch_year" id="sch_year_<?php echo $milestone->getID(); ?>" <?php print (!$milestone->hasScheduledDate()) ? "disabled" : ""; ?>>
                        <?php for ($cc = 1990;$cc <= (date("Y") + 10);$cc++): ?>
                            <option value="<?php echo $cc; ?>" <?php if ($milestone->getScheduledYear() == $cc || (!$milestone->hasScheduledDate() && $cc == date('Y'))) echo " selected"; ?>><?php echo $cc; ?></option>
                        <?php endfor; ?>
                        </select>
                    </td>
                </tr>
            </table>
            <div id="milestone_include_issues" class="milestone_include_issues" style="display: none;">
                <?php echo $milestoneincludeissues_text; ?>
                <input id="include_selected_issues" value="0" name="include_selected_issues" type="hidden">
            </div>
            <?php if (isset($milestone_type)): ?>
                <input id="milestone_type" value="<?php echo $milestone_type; ?>" name="milestone_type" type="hidden">
            <?php endif; ?>
            <div class="backdrop_details_submit">
                <?php if ($milestone->getID()): ?>
                        <input type="hidden" name="milestone_id" value="<?php echo $milestone->getID(); ?>">
                <?php endif; ?>
                <?php echo __('%cancel or %save_milestone', array('%cancel' => javascript_link_tag(__('Cancel'), array('onclick' => 'TBG.Main.Helpers.Backdrop.reset();')), '%save_milestone' => '')); ?>
                <span id="milestone_edit_indicator" style="display: none;"><?php echo image_tag('spinning_20.gif'); ?></span>
                <input class="button button-silver" type="submit" value="<?php echo $savebuttonlabel; ?>">
            </div>
            <?php if (!isset($includeform) || $includeform): ?>
        </form>
            <?php endif; ?>
    </div>
</div>
