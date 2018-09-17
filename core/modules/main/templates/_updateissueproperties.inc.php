<div class="backdrop_box large issuedetailspopup workflow_transition" style="<?php if ($issue instanceof \thebuggenie\core\entities\Issue && (!isset($show) || !$show)): ?>display: none;<?php endif; ?>" id="issue_transition_container_<?= $transition->getId(); ?>">
    <div class="backdrop_detail_header">
        <span><?= $transition->getDescription(); ?></span>
        <?php if (($issue instanceof \thebuggenie\core\entities\Issue && ($issue->isUpdateable() && !$issue->isDuplicate()) || isset($issues)) && $transition->hasAction(\thebuggenie\core\entities\WorkflowTransitionAction::ACTION_SET_DUPLICATE)): ?>
            <a href="javascript:void(0);" onclick="$(this).up('.issuedetailspopup').toggleClassName('show_duplicate_search');" class="add_link" title="<?= __('Mark as duplicate'); ?>"><?= fa_image_tag('search-plus'); ?></a>
        <?php endif; ?>
        <a href="javascript:void(0);" id="transition_working_<?= $transition->getID(); ?>_cancel" onclick="($('workflow_transition_fullpage')) ? $('workflow_transition_fullpage').fade({duration: 0.2}) : TBG.Main.Helpers.Backdrop.reset();" class="closer"><?= fa_image_tag('times'); ?></a>
    </div>
<?php if (isset($interactive) && $interactive && $issue instanceof \thebuggenie\core\entities\Issue): ?>
    <form action="<?= make_url('transition_issues', array('project_key' => $project->getKey(), 'transition_id' => $transition->getID())); ?>" method="post" onsubmit="TBG.Search.interactiveWorkflowTransition('<?= make_url('transition_issues', array('project_key' => $project->getKey(), 'transition_id' => $transition->getID())); ?>', <?= $transition->getID(); ?>, 'workflow_transition_form');return false;" accept-charset="<?= \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>" id="workflow_transition_form">
        <input type="hidden" name="issue_ids[<?= $issue->getID(); ?>]" value="<?= $issue->getID(); ?>">
<?php elseif ($issue instanceof \thebuggenie\core\entities\Issue): ?>
    <form action="<?= make_url('transition_issue', array('project_key' => $project->getKey(), 'issue_id' => $issue->getID(), 'transition_id' => $transition->getID())); ?>" method="post" onsubmit="TBG.Search.nonInteractiveWorkflowTransition()" accept-charset="<?= \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>" id="workflow_transition_<?= $transition->getID(); ?>_form">
<?php else: ?>
        <form action="<?= make_url('transition_issues', array('project_key' => $project->getKey(), 'transition_id' => $transition->getID())); ?>" method="post" onsubmit="TBG.Search.bulkWorkflowTransition('<?= make_url('transition_issues', array('project_key' => $project->getKey(), 'transition_id' => $transition->getID())); ?>', <?= $transition->getID(); ?>);return false;" accept-charset="<?= \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>" id="bulk_workflow_transition_form">
    <?php foreach ($issues as $issue_id => $i): ?>
        <input type="hidden" name="issue_ids[<?= $issue_id; ?>]" value="<?= $issue_id; ?>">
    <?php endforeach; ?>
<?php endif; ?>
        <div id="backdrop_detail_content" class="backdrop_detail_content">
            <?php if (!$issue instanceof \thebuggenie\core\entities\Issue): ?>
            <div class="rounded_box yellow borderless"><?= __('This transition will be applied to %count selected issues', array('%count' => count($issues))); ?></div>
            <?php endif; ?>
            <ul class="simple_list">
                <?php if ((($issue instanceof \thebuggenie\core\entities\Issue && $issue->isUpdateable() && $issue->canEditAssignee()) || isset($issues)) && $transition->hasAction(\thebuggenie\core\entities\WorkflowTransitionAction::ACTION_ASSIGN_ISSUE) && !$transition->getAction(\thebuggenie\core\entities\WorkflowTransitionAction::ACTION_ASSIGN_ISSUE)->hasTargetValue()): ?>
                    <li id="transition_popup_assignee_div_<?= $transition->getID(); ?>">
                        <input type="hidden" name="assignee_id" id="popup_assigned_to_id_<?= $transition->getID(); ?>" value="<?= ($issue instanceof \thebuggenie\core\entities\Issue && $issue->hasAssignee() ? $issue->getAssignee()->getID() : 0); ?>">
                        <input type="hidden" name="assignee_type" id="popup_assigned_to_type_<?= $transition->getID(); ?>" value="<?= ($issue instanceof \thebuggenie\core\entities\Issue && $issue->hasAssignee() ? ($issue->getAssignee() instanceof \thebuggenie\core\entities\User ? 'user' : 'team') : ''); ?>">
                        <input type="hidden" name="assignee_teamup" id="popup_assigned_to_teamup_<?= $transition->getID(); ?>" value="0">
                        <label for="transition_popup_set_assignee_<?= $transition->getID(); ?>"><?= __('Assignee'); ?></label>
                        <span style="width: 170px; display: <?php if ($issue instanceof \thebuggenie\core\entities\Issue && $issue->isAssigned()): ?>inline<?php else: ?>none<?php endif; ?>;" id="popup_assigned_to_name_<?= $transition->getID(); ?>">
                            <?php if ($issue instanceof \thebuggenie\core\entities\Issue): ?>
                                <?php if ($issue->getAssignee() instanceof \thebuggenie\core\entities\User): ?>
                                    <?= include_component('main/userdropdown', array('user' => $issue->getAssignee())); ?>
                                <?php elseif ($issue->getAssignee() instanceof \thebuggenie\core\entities\Team): ?>
                                    <?= include_component('main/teamdropdown', array('team' => $issue->getAssignee())); ?>
                                <?php endif; ?>
                            <?php endif; ?>
                        </span>
                        <span class="faded_out" id="popup_no_assigned_to_<?= $transition->getID(); ?>"<?php if ($issue instanceof \thebuggenie\core\entities\Issue && $issue->isAssigned()): ?> style="display: none;"<?php endif; ?>><?= __('Not assigned to anyone'); ?></span>
                        <a href="javascript:void(0);" class="dropper" data-target="popup_assigned_to_change_<?= $transition->getID(); ?>" title="<?= __('Click to change assignee'); ?>" style="display: inline-block; float: right; line-height: 1em;"><?= image_tag('tabmenu_dropdown.png', array('style' => 'float: none; margin: 3px;')); ?></a>
                        <div id="popup_assigned_to_name_indicator_<?= $transition->getID(); ?>" style="display: none;"><?= image_tag('spinning_16.gif', array('style' => 'float: right; margin-left: 5px;')); ?></div>
                        <div class="faded_out" id="popup_assigned_to_teamup_info_<?= $transition->getID(); ?>" style="clear: both; display: none;"><?= __('You will be teamed up with this user'); ?></div>
                    </li>
                <?php endif; ?>
                <?php if (($issue instanceof \thebuggenie\core\entities\Issue && ($issue->isUpdateable() && !$issue->isDuplicate()) || isset($issues)) && $transition->hasAction(\thebuggenie\core\entities\WorkflowTransitionAction::ACTION_SET_DUPLICATE)): ?>
                    <li class="duplicate_search">
                        <label for="viewissue_find_issue_<?= $transition->getID(); ?>_input"><?= __('Find issue(s)'); ?>&nbsp;</label>
                        <input type="text" name="searchfor" id="viewissue_find_issue_<?= $transition->getID(); ?>_input">
                        <input class="button button-blue" type="button" onclick="TBG.Issues.findDuplicate($('duplicate_finder_transition_<?= $transition->getID(); ?>').getValue(), <?= $transition->getID(); ?>);return false;" value="<?= __('Find'); ?>" id="viewissue_find_issue_<?= $transition->getID(); ?>_submit">
                        <?= image_tag('spinning_20.gif', array('id' => 'viewissue_find_issue_'.$transition->getID().'_indicator', 'style' => 'display: none;')); ?><br>
                        <div id="viewissue_<?= $transition->getID(); ?>_duplicate_results"></div>
                        <input type="hidden" name="transition_duplicate_ulr[<?= $transition->getID(); ?>]" id="duplicate_finder_transition_<?= $transition->getID(); ?>" value="<?= make_url('viewissue_find_duplicated_issue', array('project_key' => $project->getKey(), 'issue_id' => $issue->getID())); ?>">
                        <?php if (!$issue instanceof \thebuggenie\core\entities\Issue): ?>
                        <script type="text/javascript">
                            var transition_id = <?= $transition->getID(); ?>;
                            $('viewissue_find_issue_' + transition_id + '_input').observe('keypress', function(event) {
                                if (event.keyCode == Event.KEY_RETURN) {
                                    TBG.Issues.findDuplicate($('duplicate_finder_transition_' + transition_id).getValue(), transition_id);
                                    event.stop();
                                }
                            });
                        </script>
                        <?php endif; ?>
                        <div class="faded_out">
                            <?= __('If you want to mark this issue as duplicate of another, existing issue, find the issue by entering details to search for, in the box above.'); ?>
                        </div>
                    </li>
                <?php endif; ?>
                <?php if (($issue instanceof \thebuggenie\core\entities\Issue && ($issue->isUpdateable() && $issue->canEditStatus()) || isset($issues)) && $transition->hasAction(\thebuggenie\core\entities\WorkflowTransitionAction::ACTION_SET_STATUS) && !$transition->getAction(\thebuggenie\core\entities\WorkflowTransitionAction::ACTION_SET_STATUS)->hasTargetValue()): ?>
                    <li>
                        <label for="transition_popup_set_status_<?= $transition->getID(); ?>"><?= __('Status'); ?></label>
                        <select name="status_id" id="transition_popup_set_status_<?= $transition->getID(); ?>">
                            <?php foreach ($statuses as $status): ?>
                                <?php if (!$transition->hasPostValidationRule(\thebuggenie\core\entities\WorkflowTransitionValidationRule::RULE_STATUS_VALID) || $transition->getPostValidationRule(\thebuggenie\core\entities\WorkflowTransitionValidationRule::RULE_STATUS_VALID)->isValueValid($status)): ?>
                                    <option value="<?= $status->getID(); ?>"<?php if ($issue instanceof \thebuggenie\core\entities\Issue && $issue->getStatus() instanceof \thebuggenie\core\entities\Status && $issue->getStatus()->getID() == $status->getID()): ?> selected<?php endif; ?>><?= $status->getName(); ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </li>
                <?php endif; ?>
                <?php if (($issue instanceof \thebuggenie\core\entities\Issue && ($issue->isUpdateable() && $issue->canEditPriority()) || isset($issues)) && $transition->hasAction(\thebuggenie\core\entities\WorkflowTransitionAction::ACTION_SET_PRIORITY) && !$transition->getAction(\thebuggenie\core\entities\WorkflowTransitionAction::ACTION_SET_PRIORITY)->hasTargetValue()): ?>
                    <li id="transition_popup_priority_div_<?= $transition->getID(); ?>">
                        <label for="transition_popup_set_priority_<?= $transition->getID(); ?>"><?= __('Priority'); ?></label>
                        <select name="priority_id" id="transition_popup_set_priority_<?= $transition->getID(); ?>">
                            <?php foreach ($fields_list['priority']['choices'] as $priority): ?>
                                <?php if (!$transition->hasPostValidationRule(\thebuggenie\core\entities\WorkflowTransitionValidationRule::RULE_PRIORITY_VALID) || $transition->getPostValidationRule(\thebuggenie\core\entities\WorkflowTransitionValidationRule::RULE_PRIORITY_VALID)->isValueValid($priority)): ?>
                                    <option value="<?= $priority->getID(); ?>"<?php if ($issue instanceof \thebuggenie\core\entities\Issue && $issue->getPriority() instanceof \thebuggenie\core\entities\Priority && $issue->getPriority()->getID() == $priority->getID()): ?> selected<?php endif; ?>><?= $priority->getName(); ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </li>
                    <?php if ($issue instanceof \thebuggenie\core\entities\Issue && !$issue->isPriorityVisible()): ?>
                        <li class="faded_out">
                            <?= __("Priority isn't visible for this issuetype / product combination"); ?>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if (($issue instanceof \thebuggenie\core\entities\Issue && ($issue->isUpdateable()) || isset($issues)) && $transition->hasAction(\thebuggenie\core\entities\WorkflowTransitionAction::ACTION_SET_PERCENT) && !$transition->getAction(\thebuggenie\core\entities\WorkflowTransitionAction::ACTION_SET_PERCENT)->hasTargetValue()): ?>
                    <li id="transition_popup_percent_complete_div_<?= $transition->getID(); ?>">
                        <label for="transition_popup_set_percent_complete_<?= $transition->getID(); ?>"><?= __('Percent complete'); ?></label>
                        <select name="percent_complete_id" id="transition_popup_set_percent_complete_<?= $transition->getID(); ?>">
                            <?php foreach (range(0, 100) as $percent_complete): ?>
                                <option value="<?= $percent_complete; ?>"<?php if ($issue instanceof \thebuggenie\core\entities\Issue && $issue->getPercentCompleted() == $percent_complete): ?> selected<?php endif; ?>><?= $percent_complete; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </li>
                    <?php if (($issue instanceof \thebuggenie\core\entities\Issue && (!$issue->isPercentCompletedVisible())) || isset($issues)): ?>
                        <li class="faded_out">
                            <?= __("Percent completed isn't visible for this issuetype / product combination"); ?>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if (($issue instanceof \thebuggenie\core\entities\Issue && ($issue->isEditable() && $issue->canEditReproducability()) || isset($issues)) && $transition->hasAction(\thebuggenie\core\entities\WorkflowTransitionAction::ACTION_SET_REPRODUCABILITY) && !$transition->getAction(\thebuggenie\core\entities\WorkflowTransitionAction::ACTION_SET_REPRODUCABILITY)->hasTargetValue()): ?>
                    <li id="transition_popup_reproducability_div_<?= $transition->getID(); ?>">
                        <label for="transition_popup_set_reproducability_<?= $transition->getID(); ?>"><?= __('Reproducability'); ?></label>
                        <select name="reproducability_id" id="transition_popup_set_reproducability_<?= $transition->getID(); ?>">
                            <?php foreach ($fields_list['reproducability']['choices'] as $reproducability): ?>
                                <?php if (!$transition->hasPostValidationRule(\thebuggenie\core\entities\WorkflowTransitionValidationRule::RULE_REPRODUCABILITY_VALID) || $transition->getPostValidationRule(\thebuggenie\core\entities\WorkflowTransitionValidationRule::RULE_REPRODUCABILITY_VALID)->isValueValid($reproducability)): ?>
                                    <option value="<?= $reproducability->getID(); ?>"<?php if ($issue instanceof \thebuggenie\core\entities\Issue && $issue->getReproducability() instanceof \thebuggenie\core\entities\Reproducability && $issue->getReproducability()->getID() == $reproducability->getID()): ?> selected<?php endif; ?>><?= $reproducability->getName(); ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </li>
                    <?php if (($issue instanceof \thebuggenie\core\entities\Issue && (!$issue->isReproducabilityVisible())) || isset($issues)): ?>
                        <li class="faded_out">
                            <?= __("Reproducability isn't visible for this issuetype / product combination"); ?>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if (($issue instanceof \thebuggenie\core\entities\Issue && ($issue->isUpdateable()) || isset($issues)) && $transition->hasAction(\thebuggenie\core\entities\WorkflowTransitionAction::ACTION_SET_RESOLUTION) && !$transition->getAction(\thebuggenie\core\entities\WorkflowTransitionAction::ACTION_SET_RESOLUTION)->hasTargetValue()): ?>
                    <li id="transition_popup_resolution_div_<?= $transition->getID(); ?>">
                        <label for="transition_popup_set_resolution_<?= $transition->getID(); ?>"><?= __('Resolution'); ?></label>
                        <select name="resolution_id" id="transition_popup_set_resolution_<?= $transition->getID(); ?>">
                            <?php foreach ($fields_list['resolution']['choices'] as $resolution): ?>
                                <?php if (!$transition->hasPostValidationRule(\thebuggenie\core\entities\WorkflowTransitionValidationRule::RULE_RESOLUTION_VALID) || $transition->getPostValidationRule(\thebuggenie\core\entities\WorkflowTransitionValidationRule::RULE_RESOLUTION_VALID)->isValueValid($resolution)): ?>
                                    <option value="<?= $resolution->getID(); ?>"<?php if ($issue instanceof \thebuggenie\core\entities\Issue && $issue->getResolution() instanceof \thebuggenie\core\entities\Resolution && $issue->getResolution()->getID() == $resolution->getID()): ?> selected<?php endif; ?>><?= $resolution->getName(); ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </li>
                    <?php if (($issue instanceof \thebuggenie\core\entities\Issue && (!$issue->isResolutionVisible())) || isset($issues)): ?>
                        <li class="faded_out">
                            <?= __("Resolution isn't visible for this issuetype / product combination"); ?>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if (($issue instanceof \thebuggenie\core\entities\Issue && ($issue->isUpdateable() && $issue->canEditMilestone()) || isset($issues)) && $transition->hasAction(\thebuggenie\core\entities\WorkflowTransitionAction::ACTION_SET_MILESTONE)): ?>
                    <li id="transition_popup_milestone_div_<?= $transition->getID(); ?>">
                        <label for="transition_popup_set_milestone_<?= $transition->getID(); ?>"><?= __('Milestone'); ?></label>
                        <select name="milestone_id" id="transition_popup_set_milestone_<?= $transition->getID(); ?>">
                            <option value="0"<?php if ($issue instanceof \thebuggenie\core\entities\Issue && $issue->getMilestone() instanceof \thebuggenie\core\entities\Milestone): ?> selected<?php endif; ?>><?= __('Not determined') ?></option>
                            <?php foreach ($project->getMilestonesForIssues() as $milestone): ?>
                                <option value="<?= $milestone->getID(); ?>"<?php if ($issue instanceof \thebuggenie\core\entities\Issue && $issue->getMilestone() instanceof \thebuggenie\core\entities\Milestone && $issue->getMilestone()->getID() == $milestone->getID()): ?> selected<?php endif; ?>><?= $milestone->getName(); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </li>
                    <li class="faded_out">
                        <?= __("Specify the target milestone for this issue"); ?>
                    </li>
                    <?php if (($issue instanceof \thebuggenie\core\entities\Issue && (!$issue->isMilestoneVisible())) || isset($issues)): ?>
                        <li class="faded_out">
                            <?= __("Milestone isn't visible for this issuetype / product combination"); ?>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if ( ! empty($customfields_list)): ?>
                <?php foreach ($customfields_list as $field => $info): ?>
                    <?php if (($issue instanceof \thebuggenie\core\entities\Issue && ($issue->isUpdateable()) || isset($issues)) && $transition->hasAction(\thebuggenie\core\entities\WorkflowTransitionAction::CUSTOMFIELD_SET_PREFIX.$field) && !$transition->getAction(\thebuggenie\core\entities\WorkflowTransitionAction::CUSTOMFIELD_SET_PREFIX.$field)->hasTargetValue()): ?>
                        <li id="transition_popup_<?= $field; ?>_div_<?= $transition->getID(); ?>">
                            <label for="transition_popup_set_<?= $field; ?>_<?= $transition->getID(); ?>"><?= $info['title']; ?></label>
                            <?php if (array_key_exists('choices', $info) && is_array($info['choices'])): ?>
                                <select name="<?= $field; ?>_id" id="transition_popup_set_<?= $field; ?>_<?= $transition->getID(); ?>">
                                    <?php foreach ($info['choices'] ?: array() as $choice): ?>
                                        <?php if (!$transition->hasPostValidationRule(\thebuggenie\core\entities\WorkflowTransitionValidationRule::CUSTOMFIELD_VALIDATE_PREFIX.$field) || $transition->getPostValidationRule(\thebuggenie\core\entities\WorkflowTransitionValidationRule::CUSTOMFIELD_VALIDATE_PREFIX.$field)->isValueValid($choice->getID())): ?>
                                            <option value="<?= $choice->getID(); ?>"<?php if ($issue instanceof \thebuggenie\core\entities\Issue && $issue->getCustomField($field) instanceof \thebuggenie\core\entities\CustomDatatypeOption && $issue->getCustomField($field)->getID() == $choice->getID()): ?> selected<?php endif; ?>><?= __($choice->getName()); ?></option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                            <?php elseif ($info['type'] == \thebuggenie\core\entities\CustomDatatype::DATE_PICKER || $info['type'] == \thebuggenie\core\entities\CustomDatatype::DATETIME_PICKER): ?>
                                <div id="customfield_<?= $field; ?>_calendar_container"></div>
                                <script type="text/javascript">
                                    require(['domReady', 'thebuggenie/tbg', 'calendarview'], function (domReady, tbgjs, Calendar) {
                                        domReady(function () {
                                            Calendar.setup({
                                                dateField: '<?= $field; ?>_id',
                                                parentElement: 'customfield_<?= $field; ?>_calendar_container'
                                            });
                                        });
                                    });
                                </script>
                            <?php elseif ($info['type'] == \thebuggenie\core\entities\CustomDatatype::INPUT_TEXTAREA_SMALL || $info['type'] == \thebuggenie\core\entities\CustomDatatype::INPUT_TEXTAREA_MAIN):
                                include_component('main/textarea', array('area_name' => $field.'_id', 'target_type' => 'issue', 'target_id' => $issue->getID(), 'area_id' => $field.'_'.$transition->getID(), 'height' => '120px', 'width' => '790px', 'value' => ''));
                            elseif ($info['type'] == \thebuggenie\core\entities\CustomDatatype::INPUT_TEXT): ?>
                                <input type="text" name="<?= $field; ?>_id" placeholder="<?= $info['name'] ?>">
                            <?php else: ?>
                                <select name="<?= $field; ?>_id" id="transition_popup_set_<?= $field; ?>_<?= $transition->getID(); ?>">
                                    <?php

                                        switch ($info['type'])
                                        {
                                            case \thebuggenie\core\entities\CustomDatatype::EDITIONS_CHOICE:
                                                foreach ($project->getEditions() as $choice): ?>
                                                    <option value="<?= $choice->getID(); ?>"<?php if ($issue instanceof \thebuggenie\core\entities\Issue && $issue->getCustomField($field) instanceof \thebuggenie\core\entities\Edition && $issue->getCustomField($field)->getID() == $choice->getID()): ?> selected<?php endif; ?>><?= __($choice->getName()); ?></option>
                                                <?php endforeach;
                                                break;
                                            case \thebuggenie\core\entities\CustomDatatype::MILESTONE_CHOICE:
                                                foreach ($project->getMilestones() as $choice): ?>
                                                    <option value="<?= $choice->getID(); ?>"<?php if ($issue instanceof \thebuggenie\core\entities\Issue && $issue->getCustomField($field) instanceof \thebuggenie\core\entities\Milestone && $issue->getCustomField($field)->getID() == $choice->getID()): ?> selected<?php endif; ?>><?= __($choice->getName()); ?></option>
                                                <?php endforeach;
                                                break;
                                            case \thebuggenie\core\entities\CustomDatatype::STATUS_CHOICE:
                                                foreach (\thebuggenie\core\entities\Status::getAll() as $choice): ?>
                                                    <option value="<?= $choice->getID(); ?>"<?php if ($issue instanceof \thebuggenie\core\entities\Issue && $issue->getCustomField($field) instanceof \thebuggenie\core\entities\Edition && $issue->getCustomField($field)->getID() == $choice->getID()): ?> selected<?php endif; ?>><?= __($choice->getName()); ?></option>
                                                <?php endforeach;
                                                break;
                                            case \thebuggenie\core\entities\CustomDatatype::COMPONENTS_CHOICE:
                                                foreach ($project->getComponents() as $choice): ?>
                                                    <option value="<?= $choice->getID(); ?>"<?php if ($issue instanceof \thebuggenie\core\entities\Issue && $issue->getCustomField($field) instanceof \thebuggenie\core\entities\Edition && $issue->getCustomField($field)->getID() == $choice->getID()): ?> selected<?php endif; ?>><?= __($choice->getName()); ?></option>
                                                <?php endforeach;
                                                break;
                                            case \thebuggenie\core\entities\CustomDatatype::RELEASES_CHOICE:
                                                foreach ($project->getBuilds() as $choice): ?>
                                                    <option value="<?= $choice->getID(); ?>"<?php if ($issue instanceof \thebuggenie\core\entities\Issue && $issue->getCustomField($field) instanceof \thebuggenie\core\entities\Edition && $issue->getCustomField($field)->getID() == $choice->getID()): ?> selected<?php endif; ?>><?= __($choice->getName()); ?></option>
                                                <?php endforeach;
                                                break;
                                        }
                                    ?>
                                </select>
                            <?php endif; ?>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
                <?php endif; ?>
                <?php if ($transition->hasAction(\thebuggenie\core\entities\WorkflowTransitionAction::ACTION_USER_STOP_WORKING)): ?>
                    <?php if ($issue instanceof \thebuggenie\core\entities\Issue): ?>
                        <li id="transition_popup_stop_working_div_<?= $transition->getID(); ?>">
                            <label for="transition_popup_set_stop_working"><?= __('Log time spent'); ?></label>
                            <div class="time_logger_summary">
                                <?php $time_spent = $issue->calculateTimeSpent(); ?>
                                <input type="radio" name="did" id="transition_popup_set_stop_working_<?= $transition->getID(); ?>" value="something" checked onchange="$('transition_popup_set_stop_working_specify_log_div_<?= $transition->getID(); ?>').hide();"><label for="transition_popup_set_stop_working_<?= $transition->getID(); ?>" class="simple"><?= __('Yes'); ?></label>&nbsp;&nbsp;&nbsp;&nbsp;<span class="faded_out"><?= __($issue->getTimeLoggerSomethingSummaryText(), array('%minutes' => $time_spent['minutes'], '%hours' => $time_spent['hours'], '%days' => $time_spent['days'], '%weeks' => $time_spent['weeks'])); ?></span><br>
                                <input type="radio" name="did" id="transition_popup_set_stop_working_no_log_<?= $transition->getID(); ?>" value="nothing" onchange="$('transition_popup_set_stop_working_specify_log_div_<?= $transition->getID(); ?>').hide();"><label for="transition_popup_set_stop_working_no_log_<?= $transition->getID(); ?>" class="simple"><?= __('No'); ?></label><br>
                                <input type="radio" name="did" id="transition_popup_set_stop_working_specify_log_<?= $transition->getID(); ?>" value="this" onchange="$('transition_popup_set_stop_working_specify_log_div_<?= $transition->getID(); ?>').show()"><label for="transition_popup_set_stop_working_specify_log_<?= $transition->getID(); ?>" class="simple"><?= __('Yes, let me specify'); ?></label>
                            </div>
                            <br style="clear: both;">
                        </li>
                        <li id="transition_popup_set_stop_working_specify_log_div_<?= $transition->getID(); ?>" class="lightyellowbox issue_timespent_form" style="display: none;">
                            <?php include_component('main/issuespenttimeentry', compact('issue')); ?>
                        </li>
                    <?php else: ?>
                        <input type="hidden" name="did" id="transition_popup_set_stop_working_no_log_<?= $transition->getID(); ?>" value="nothing">
                    <?php endif; ?>
                <?php endif; ?>
                <?php if ($issue instanceof \thebuggenie\core\entities\Issue || isset($issues)): ?>
                    <li style="margin-top: 10px;">
                        <label for="transition_popup_comment_body"><?= __('Write a comment if you want it to be added'); ?></label><br>
                        <?php include_component('main/textarea', array('area_name' => 'comment_body', 'target_type' => 'issue', 'target_id' => (isset($issue)) ? $issue->getID() : 0, 'area_id' => 'transition_popup_comment_body_'.$transition->getID(), 'height' => '120px', 'width' => '790px', 'value' => '')); ?>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
        <div class="backdrop_details_submit">
            <span class="explanation"></span>
            <div class="submit_container">
                <button type="submit" class="workflow_transition_submit_button" id="transition_working_<?= $transition->getID(); ?>_submit"><?= image_tag('spinning_16.gif', array('style' => 'display: none;', 'id' => 'transition_working_'.$transition->getID().'_indicator')) . $transition->getName(); ?></button>
            </div>
        </div>
    </form>
    <?php if (($issue instanceof \thebuggenie\core\entities\Issue && ($issue->canEditAssignee()) || isset($issues)) && $transition->hasAction(\thebuggenie\core\entities\WorkflowTransitionAction::ACTION_ASSIGN_ISSUE) && !$transition->getAction(\thebuggenie\core\entities\WorkflowTransitionAction::ACTION_ASSIGN_ISSUE)->hasTargetValue()): ?>
        <?php include_component('main/identifiableselector', array(    'html_id'             => 'popup_assigned_to_change_'.$transition->getID(),
                                                                'header'             => __('Assign this issue'),
                                                                'callback'             => "TBG.Issues.updateWorkflowAssignee('" . make_url('issue_gettempfieldvalue', array('field' => 'assigned_to', 'identifiable_type' => '%identifiable_type', 'value' => '%identifiable_value')) . "', %identifiable_value, %identifiable_type, ".$transition->getID().");",
                                                                'teamup_callback'     => "TBG.Issues.updateWorkflowAssigneeTeamup('" . make_url('issue_gettempfieldvalue', array('field' => 'assigned_to', 'identifiable_type' => '%identifiable_type', 'value' => '%identifiable_value')) . "', %identifiable_value, %identifiable_type, ".$transition->getID().");",
                                                                'clear_link_text'    => __('Clear current assignee'),
                                                                'base_id'            => 'popup_assigned_to_'.$transition->getID(),
                                                                'include_teams'        => true,
                                                                'allow_clear'        => false,
                                                                'style'                => array('top' => '38px', 'right' => '5px'),
                                                                'absolute'            => true)); ?>
    <?php endif; ?>
</div>
