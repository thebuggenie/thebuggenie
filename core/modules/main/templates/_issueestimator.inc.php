<?php use thebuggenie\modules\agile\entities\AgileBoard; ?>
<ul class="<?php if (isset($mode) && $mode == 'inline'): ?>borderless<?php else: ?>popup_box <?php endif; ?> more_actions_dropdown" id="<?php echo $field . '_' . $issue_id; ?>_change" style="<?php if (isset($mode) && $mode == 'inline'): ?>position: relative;<?php endif; ?> <?php echo (isset($mode) && $mode == 'left') ? 'left' : 'right'; ?>: 0; text-align: left;">
    <?php if (!isset($headers) || $headers == true): ?>
        <li class="header">
            <?php if ($field == 'estimated_time'): ?>
                <?php echo __('Estimate this issue'); ?>
            <?php else: ?>
                <?php echo __('Log time spent on this issue'); ?>
            <?php endif; ?>
        </li>
    <?php endif; ?>
    <?php if ($field == 'estimated_time' && (!isset($clear) || $clear == true)): ?>
        <li>
            <a href="javascript:void(0);" onclick="TBG.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $project_key, 'issue_id' => $issue_id, 'field' => $field, 'value' => 0)); ?>', '<?php echo $field; ?>');"><?php echo ($field == 'estimated_time') ? __('Clear current estimate') : __('Clear time spent on this issue'); ?></a>
        </li>
    <?php endif; ?>
    <li class="separator"></li>
    <li class="dropdown_content nohover form_container">
<?php if (!isset($save) || $save == true): ?>
    <form id="<?php echo $field . '_' . $issue_id; ?>_form" method="post" accept-charset="<?php echo \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>" action="" onsubmit="TBG.Issues.Field.setTime('<?php echo make_url('issue_setfield', array('project_key' => $project_key, 'issue_id' => $issue_id, 'field' => $field)); ?>', '<?php echo $field; ?>', <?php echo $issue_id; ?>);return false;">
        <input type="hidden" name="do_save" value="<?php echo (integer) (isset($instant_save) && $instant_save); ?>">
<?php endif; ?>
        <label for="<?php echo $field . '_' . $issue_id; ?>_input">
            <?php if ($field == 'estimated_time'): ?>
                <?php echo trim(__('%clear_current_estimate type a new estimate %or_specify_below', array('%clear_current_estimate' => '', '%or_specify_below' => ''))); ?>:
            <?php else: ?>
                <?php echo trim(__('Type a value for the time spent %or_specify_below', array('%clear_current_time_spent' => '', '%or_specify_below' => ''))); ?>:
            <?php endif; ?>
        </label>
        <input type="text" name="<?php echo $field; ?>" id="<?php echo $field . '_' . $issue_id; ?>_input" placeholder="<?php echo ($field == 'estimated_time') ? __('Enter your estimate here') : __('Enter time spent here'); ?>" style="width: 240px; padding: 1px 1px 1px;">
        <?php if (!isset($save) || $save == true): ?>
            <input type="submit" style="width: 60px;" value="<?php echo ($field == 'estimated_time') ? __('Set') : __('Add'); ?>">
        <?php endif; ?>
        <?php if (!isset($headers) || $headers == true): ?>
            <div class="faded_out" style="padding: 5px 0 5px 0;">
                <?php if (isset($board)): ?>
                    <?php if ($board->getType() == AgileBoard::TYPE_SCRUM && $board->getTaskIssueTypeID() == $issue->getIssuetype()->getID()): ?>
                        <?php echo __('Enter a value in plain text, like "1 hour", "7 hours", or similar'); ?>.
                    <?php elseif ($board->getType() == AgileBoard::TYPE_SCRUM && $board->getTaskIssueTypeID() != $issue->getEpicIssuetypeID()): ?>
                        <?php echo __('Enter a value in plain text, like "1 point", "11 points", or similar'); ?>.
                    <?php elseif ($board->getType() == AgileBoard::TYPE_GENERIC): ?>
                        <?php echo __('Enter a value in plain text, like "1 week, 2 hours", "1 day", or similar'); ?>.
                    <?php endif; ?>
                <?php else: ?>
                    <?php echo __('Enter a value in plain text, like "1 week, 2 hours", "3 months and 1 day", or similar'); ?>.
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <label for="<?php echo $field . '_' . $issue_id; ?>_hours_input"><?php echo __('%enter_a_value_in_plain_text or specify below', array('%enter_a_value_in_plain_text' => '')); ?>:</label>
        <table class="estimator_table">
            <tr>
                <?php if (isset($board)): ?>
                    <?php if ($board->getType() == AgileBoard::TYPE_SCRUM && $board->getTaskIssueTypeID() == $issue->getIssuetype()->getID()): ?>
                        <td><input type="text" value="<?php echo $hours; ?>" name="hours" id="<?php echo $field . '_' . $issue_id; ?>_hours_input">
                    <?php elseif ($board->getType() == AgileBoard::TYPE_SCRUM && $board->getTaskIssueTypeID() != $issue->getEpicIssuetypeID()): ?>
                        <td><input type="text" value="<?php echo $points; ?>" name="points" id="<?php echo $field . '_' . $issue_id; ?>_points_input">
                    <?php elseif ($board->getType() == AgileBoard::TYPE_GENERIC): ?>
                        <td><input type="text" value="<?php echo $hours; ?>" name="hours" id="<?php echo $field . '_' . $issue_id; ?>_hours_input">
                        <td><input type="text" value="<?php echo $days; ?>" name="days" id="<?php echo $field . '_' . $issue_id; ?>_days_input">
                    <td><input type="text" value="<?php echo $weeks; ?>" name="weeks" id="<?php echo $field . '_' . $issue_id; ?>_weeks_input">
                    <td><input type="text" value="<?php echo $months; ?>" name="months" id="<?php echo $field . '_' . $issue_id; ?>_months_input">
                    <?php endif; ?>
                <?php else: ?>
                    <td><input type="text" value="<?php echo $hours; ?>" name="hours" id="<?php echo $field . '_' . $issue_id; ?>_hours_input">
                    <td><input type="text" value="<?php echo $days; ?>" name="days" id="<?php echo $field . '_' . $issue_id; ?>_days_input">
                    <td><input type="text" value="<?php echo $weeks; ?>" name="weeks" id="<?php echo $field . '_' . $issue_id; ?>_weeks_input">
                    <td><input type="text" value="<?php echo $months; ?>" name="months" id="<?php echo $field . '_' . $issue_id; ?>_months_input">
                    <td><input type="text" value="<?php echo $points; ?>" name="points" id="<?php echo $field . '_' . $issue_id; ?>_points_input">
                <?php endif; ?>
            </tr>
            <tr>
                <?php if (isset($board)): ?>
                    <?php if ($board->getType() == AgileBoard::TYPE_SCRUM && $board->getTaskIssueTypeID() == $issue->getIssuetype()->getID()): ?>
                        <td><?php echo __('%number_of hours', array('%number_of' => '')); ?></td>
                    <?php elseif ($board->getType() == AgileBoard::TYPE_SCRUM && $board->getTaskIssueTypeID() != $issue->getEpicIssuetypeID()): ?>
                        <td><?php echo __('%number_of points', array('%number_of' => '')); ?></td>
                    <?php elseif ($board->getType() == AgileBoard::TYPE_GENERIC): ?>
                        <td><?php echo __('%number_of hours', array('%number_of' => '')); ?></td>
                        <td><?php echo __('%number_of days', array('%number_of' => '')); ?></td>
                        <td><?php echo __('%number_of weeks', array('%number_of' => '')); ?></td>
                        <td><?php echo __('%number_of months', array('%number_of' => '')); ?></td>
                    <?php endif; ?>
                <?php else: ?>
                    <td><?php echo __('%number_of hours', array('%number_of' => '')); ?></td>
                    <td><?php echo __('%number_of days', array('%number_of' => '')); ?></td>
                    <td><?php echo __('%number_of weeks', array('%number_of' => '')); ?></td>
                    <td><?php echo __('%number_of months', array('%number_of' => '')); ?></td>
                    <td><?php echo __('%number_of points', array('%number_of' => '')); ?></td>
                <?php endif; ?>
            </tr>
        </table>
        <?php if ($issue->hasChildIssues()): ?>
            <?php echo __('Note that the total estimated effort of parent issues is the sum of its child issues. This estimate will be replaced if any child issues are updated.'); ?>
        <?php endif; ?>
        <?php if (!isset($save) || $save == true): ?>
            <div class="form_controls">
                <input type="submit" class="button button-silver" value="<?php echo ($field == 'estimated_time') ? __('Save') : __('Add time spent'); ?>">
            </div>
        <?php endif; ?>
<?php if (!isset($save) || $save == true): ?>
    </form>
    </li>
    <li id="<?php echo $field ?>_spinning" style="margin-top: 3px; display: none;"><?php echo image_tag('spinning_20.gif', array('style' => 'float: left; margin-right: 5px;')) . '&nbsp;' . __('Please wait'); ?>...</li>
    <li id="<?php echo $field . '_' . $issue_id; ?>_spinning" style="margin-top: 3px; display: none;"><?php echo image_tag('spinning_20.gif', array('style' => 'float: left; margin-right: 5px;')) . '&nbsp;' . __('Please wait'); ?>...</li>
    <li id="<?php echo $field . '_' . $issue_id; ?>_change_error" class="error_message" style="display: none;"></li>
<?php endif; ?>
</ul>
