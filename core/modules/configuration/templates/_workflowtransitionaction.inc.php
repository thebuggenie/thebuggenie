<tr id="workflowtransitionaction_<?php echo $action->getID(); ?>">
    <?php
    
        $show_edit = true;

        switch ($action->getActionType())
        {
            case \thebuggenie\core\entities\WorkflowTransitionAction::ACTION_ASSIGN_ISSUE_SELF:
            case \thebuggenie\core\entities\WorkflowTransitionAction::ACTION_CLEAR_ASSIGNEE:
            case \thebuggenie\core\entities\WorkflowTransitionAction::ACTION_CLEAR_PRIORITY:
            case \thebuggenie\core\entities\WorkflowTransitionAction::ACTION_CLEAR_PERCENT:
            case \thebuggenie\core\entities\WorkflowTransitionAction::ACTION_CLEAR_REPRODUCABILITY:
            case \thebuggenie\core\entities\WorkflowTransitionAction::ACTION_CLEAR_RESOLUTION:
            case \thebuggenie\core\entities\WorkflowTransitionAction::ACTION_CLEAR_MILESTONE:
            case \thebuggenie\core\entities\WorkflowTransitionAction::ACTION_CLEAR_DUPLICATE:
            case \thebuggenie\core\entities\WorkflowTransitionAction::ACTION_USER_START_WORKING:
            case \thebuggenie\core\entities\WorkflowTransitionAction::ACTION_USER_STOP_WORKING:
            case \thebuggenie\core\entities\WorkflowTransitionAction::ACTION_SET_MILESTONE:
            case \thebuggenie\core\entities\WorkflowTransitionAction::ACTION_SET_DUPLICATE:
            case \thebuggenie\core\entities\WorkflowTransitionAction::CUSTOMFIELD_CLEAR_PREFIX . $action->getCustomActionType():
                ?>
                <td id="workflowtransitionaction_<?php echo $action->getID(); ?>_description" style="padding: 2px;">
                    <?php if ($action->getActionType() == \thebuggenie\core\entities\WorkflowTransitionAction::ACTION_ASSIGN_ISSUE_SELF): ?>
                        <?php echo __('Assign the issue to the current user'); ?>
                    <?php elseif ($action->getActionType() == \thebuggenie\core\entities\WorkflowTransitionAction::ACTION_SET_MILESTONE): ?>
                        <?php echo __('Set milestone to milestone provided by user'); ?>
                    <?php elseif ($action->getActionType() == \thebuggenie\core\entities\WorkflowTransitionAction::ACTION_CLEAR_ASSIGNEE): ?>
                        <?php echo __('Clear issue assignee'); ?>
                    <?php elseif ($action->getActionType() == \thebuggenie\core\entities\WorkflowTransitionAction::ACTION_CLEAR_PRIORITY): ?>
                        <?php echo __('Clear issue priority'); ?>
                    <?php elseif ($action->getActionType() == \thebuggenie\core\entities\WorkflowTransitionAction::ACTION_CLEAR_PERCENT): ?>
                        <?php echo __('Clear issue percent completed'); ?>
                    <?php elseif ($action->getActionType() == \thebuggenie\core\entities\WorkflowTransitionAction::ACTION_CLEAR_REPRODUCABILITY): ?>
                        <?php echo __('Clear issue reproducability'); ?>
                    <?php elseif ($action->getActionType() == \thebuggenie\core\entities\WorkflowTransitionAction::ACTION_CLEAR_RESOLUTION): ?>
                        <?php echo __('Clear issue resolution'); ?>
                    <?php elseif ($action->getActionType() == \thebuggenie\core\entities\WorkflowTransitionAction::ACTION_CLEAR_MILESTONE): ?>
                        <?php echo __('Clear issue milestone'); ?>
                    <?php elseif ($action->getActionType() == \thebuggenie\core\entities\WorkflowTransitionAction::ACTION_USER_START_WORKING): ?>
                        <?php echo __('Start logging time'); ?>
                    <?php elseif ($action->getActionType() == \thebuggenie\core\entities\WorkflowTransitionAction::ACTION_USER_STOP_WORKING): ?>
                        <?php echo __('Stop logging time and optionally add time spent'); ?>
                    <?php elseif ($action->getActionType() == \thebuggenie\core\entities\WorkflowTransitionAction::ACTION_SET_DUPLICATE): ?>
                        <?php echo __('Mark issue as duplicate of another, existing issue'); ?>
                    <?php elseif ($action->getActionType() == \thebuggenie\core\entities\WorkflowTransitionAction::ACTION_CLEAR_DUPLICATE): ?>
                        <?php echo __('Mark issue as unique (no longer a duplicate) issue'); ?>
                    <?php elseif ($action->isCustomClearAction()): ?>
                        <?php echo __('Clear issue field %key', array('%key' => $action->getCustomActionType())); ?>
                    <?php endif; ?>
                </td>
                <?php
                $show_edit = false;
                break;
            case \thebuggenie\core\entities\WorkflowTransitionAction::ACTION_SET_STATUS:
            case \thebuggenie\core\entities\WorkflowTransitionAction::ACTION_SET_PRIORITY:
            case \thebuggenie\core\entities\WorkflowTransitionAction::ACTION_SET_PERCENT:
            case \thebuggenie\core\entities\WorkflowTransitionAction::ACTION_SET_REPRODUCABILITY:
            case \thebuggenie\core\entities\WorkflowTransitionAction::ACTION_SET_RESOLUTION:
            case \thebuggenie\core\entities\WorkflowTransitionAction::ACTION_ASSIGN_ISSUE:
            case \thebuggenie\core\entities\WorkflowTransitionAction::CUSTOMFIELD_SET_PREFIX . $action->getCustomActionType(): ?>
                
                <td id="workflowtransitionaction_<?php echo $action->getID(); ?>_description" style="padding: 2px;">
                    <?php if ($action->hasValidTarget()): ?>
                        <?php if ($action->getActionType() == \thebuggenie\core\entities\WorkflowTransitionAction::ACTION_SET_STATUS): ?>
                            <?php
                                $target = ($action->getTargetValue()) ? \thebuggenie\core\entities\Status::getB2DBTable()->selectById((int) $action->getTargetValue()) : '';
                                if ($target instanceof \thebuggenie\core\entities\Status)
                                    echo __('Set status to %status', array('%status' => '<span id="workflowtransitionaction_'.$action->getID().'_value" style="font-weight: bold;"><span class="status_badge" style="background-color: '.$target->getColor().'; color: '.$target->getTextColor().';">' . $target->getName() . '</span></span>'));
                                else
                                    echo __('Set status to %status', array('%status' => '<span id="workflowtransitionaction_'.$action->getID().'_value" style="font-weight: bold;">' . __('Status provided by user') . '</span>'));
                            ?>
                        <?php elseif ($action->getActionType() == \thebuggenie\core\entities\WorkflowTransitionAction::ACTION_SET_PRIORITY): ?>
                            <?php echo __('Set priority to %priority', array('%priority' => '<span id="workflowtransitionaction_'.$action->getID().'_value" style="font-weight: bold;">' . (($action->getTargetValue()) ? \thebuggenie\core\entities\Priority::getB2DBTable()->selectById((int) $action->getTargetValue())->getName() : __('Priority provided by user')) . '</span>')); ?>
                        <?php elseif ($action->getActionType() == \thebuggenie\core\entities\WorkflowTransitionAction::ACTION_SET_PERCENT): ?>
                            <?php echo __('Set percent completed to %percentcompleted', array('%percentcompleted' => '<span id="workflowtransitionaction_'.$action->getID().'_value" style="font-weight: bold;">' . (($action->getTargetValue()) ? (int) $action->getTargetValue() : __('Percentage provided by user')) . '</span>')); ?>
                        <?php elseif ($action->getActionType() == \thebuggenie\core\entities\WorkflowTransitionAction::ACTION_SET_RESOLUTION): ?>
                            <?php echo __('Set resolution to %resolution', array('%resolution' => '<span id="workflowtransitionaction_'.$action->getID().'_value" style="font-weight: bold;">' . (($action->getTargetValue()) ? \thebuggenie\core\entities\Resolution::getB2DBTable()->selectById((int) $action->getTargetValue())->getName() : __('Resolution provided by user')) . '</span>')); ?>
                        <?php elseif ($action->getActionType() == \thebuggenie\core\entities\WorkflowTransitionAction::ACTION_SET_REPRODUCABILITY): ?>
                            <?php echo __('Set reproducability to %reproducability', array('%reproducability' => '<span id="workflowtransitionaction_'.$action->getID().'_value" style="font-weight: bold;">' . (($action->getTargetValue()) ? \thebuggenie\core\entities\Reproducability::getB2DBTable()->selectById((int) $action->getTargetValue())->getName() : __('Reproducability provided by user')) . '</span>')); ?>
                        <?php elseif ($action->getActionType() == \thebuggenie\core\entities\WorkflowTransitionAction::ACTION_ASSIGN_ISSUE): ?>
                            <?php 

                            if ($action->hasTargetValue())
                            {
                                $target_details = explode('_', $action->getTargetValue());
                                echo __('Assign issue to %assignee', array('%assignee' => '<span id="workflowtransitionaction_'.$action->getID().'_value" style="font-weight: bold;">' . (($target_details[0] == 'user') ? \thebuggenie\core\entities\User::getB2DBTable()->selectById((int) $target_details[1])->getNameWithUsername() : \thebuggenie\core\entities\Team::getB2DBTable()->selectById((int) $target_details[1])->getName()) . '</span>')); 
                            }
                            else
                            {
                                echo __('Assign issue to %assignee', array('%assignee' => '<span id="workflowtransitionaction_'.$action->getID().'_value" style="font-weight: bold;">' . __('User or team specified during transition') . '</span>')); 
                            }

                            ?>
                        <?php elseif ($action->isCustomSetAction()): ?>
                            <?php
                            $tbg_response->addJavascript('calendarview.js');

                            switch (\thebuggenie\core\entities\CustomDatatype::getByKey($action->getCustomActionType())->getType()) {
                                case \thebuggenie\core\entities\CustomDatatype::INPUT_TEXTAREA_MAIN:
                                case \thebuggenie\core\entities\CustomDatatype::INPUT_TEXTAREA_SMALL:
                                case \thebuggenie\core\entities\CustomDatatype::INPUT_TEXT:
                                case \thebuggenie\core\entities\CustomDatatype::CALCULATED_FIELD:
                                    echo __('Set issue field %key to %value', array('%key' => $action->getCustomActionType(), '%value' => '<span id="workflowtransitionaction_'.$action->getID().'_value" style="font-weight: bold;">' . (($action->getTargetValue()) ?: __('Value provided by user')) . '</span>'));
                                    break;
                                case \thebuggenie\core\entities\CustomDatatype::DATE_PICKER:
                                    echo __('Set issue field %key to %value', array('%key' => $action->getCustomActionType(), '%value' => '<span id="workflowtransitionaction_'.$action->getID().'_value" style="font-weight: bold;">' . (($action->getTargetValue()) ? date('Y-m-d', (int) $action->getTargetValue()) : __('Value provided by user')) . '</span>'));
                                    break;
                                case \thebuggenie\core\entities\CustomDatatype::USER_CHOICE:
                                    echo __('Set issue field %key to ', array('%key' => $action->getCustomActionType()));
                                    echo '<div id="workflowtransitionaction_'.$action->getID().'_value" style="font-weight: bold;display: inline;">';
                                    echo $action->getTargetValue() ? include_component('main/userdropdown', array('user' => $action->getTargetValue())) : __('Value provided by user');
                                    echo '</div>';
                                    break;
                                case \thebuggenie\core\entities\CustomDatatype::TEAM_CHOICE:
                                    echo __('Set issue field %key to ', array('%key' => $action->getCustomActionType()));
                                    echo '<div id="workflowtransitionaction_'.$action->getID().'_value" style="font-weight: bold;display: inline;">';
                                    echo $action->getTargetValue() ? include_component('main/teamdropdown', array('team' => $action->getTargetValue())) : __('Value provided by user');
                                    echo '</div>';
                                    break;
                                case \thebuggenie\core\entities\CustomDatatype::CLIENT_CHOICE:
                                    $target = ($action->getTargetValue()) ? \thebuggenie\core\entities\tables\Clients::getTable()->selectById((int) $action->getTargetValue()) : null;
                                    echo __('Set issue field %key to %value', array('%key' => $action->getCustomActionType(), '%value' => '<span id="workflowtransitionaction_'.$action->getID().'_value" style="font-weight: bold;">' . (($action->getTargetValue()) ? $target->getName() : __('Value provided by user')) . '</span>'));
                                    break;
                                case \thebuggenie\core\entities\CustomDatatype::RELEASES_CHOICE:
                                    $target = ($action->getTargetValue()) ? \thebuggenie\core\entities\tables\Builds::getTable()->selectById((int) $action->getTargetValue()) : null;
                                    echo __('Set issue field %key to %value', array('%key' => $action->getCustomActionType(), '%value' => '<span id="workflowtransitionaction_'.$action->getID().'_value" style="font-weight: bold;">' . (($action->getTargetValue()) ? $target->getProject()->getName() . ' - ' . $target->getName() : __('Value provided by user')) . '</span>'));
                                    break;
                                case \thebuggenie\core\entities\CustomDatatype::COMPONENTS_CHOICE:
                                    $target = ($action->getTargetValue()) ? \thebuggenie\core\entities\tables\Components::getTable()->selectById((int) $action->getTargetValue()) : null;
                                    echo __('Set issue field %key to %value', array('%key' => $action->getCustomActionType(), '%value' => '<span id="workflowtransitionaction_'.$action->getID().'_value" style="font-weight: bold;">' . (($action->getTargetValue()) ? $target->getProject()->getName() . ' - ' . $target->getName() : __('Value provided by user')) . '</span>'));
                                    break;
                                case \thebuggenie\core\entities\CustomDatatype::EDITIONS_CHOICE:
                                    $target = ($action->getTargetValue()) ? \thebuggenie\core\entities\tables\Editions::getTable()->selectById((int) $action->getTargetValue()) : null;
                                    echo __('Set issue field %key to %value', array('%key' => $action->getCustomActionType(), '%value' => '<span id="workflowtransitionaction_'.$action->getID().'_value" style="font-weight: bold;">' . (($action->getTargetValue()) ? $target->getProject()->getName() . ' - ' . $target->getName() : __('Value provided by user')) . '</span>'));
                                    break;
                                case \thebuggenie\core\entities\CustomDatatype::MILESTONE_CHOICE:
                                    $target = ($action->getTargetValue()) ? \thebuggenie\core\entities\tables\Milestones::getTable()->selectById((int) $action->getTargetValue()) : null;
                                    echo __('Set issue field %key to %value', array('%key' => $action->getCustomActionType(), '%value' => '<span id="workflowtransitionaction_'.$action->getID().'_value" style="font-weight: bold;">' . (($action->getTargetValue()) ? $target->getProject()->getName() . ' - ' . $target->getName() : __('Value provided by user')) . '</span>'));
                                    break;
                                case \thebuggenie\core\entities\CustomDatatype::STATUS_CHOICE:
                                    $target = ($action->getTargetValue()) ? \thebuggenie\core\entities\ListTypes::getTable()->selectById((int) $action->getTargetValue()) : null;
                                    echo __('Set issue field %key to %value', array('%key' => $action->getCustomActionType(), '%value' => '<span id="workflowtransitionaction_'.$action->getID().'_value" class="status_badge" style="background-color: '.$target->getColor().'; color: '.$target->getTextColor().';">' . (($action->getTargetValue()) ? $target->getName() : __('Value provided by user')) . '</span>'));
                                    break;
                                case \thebuggenie\core\entities\CustomDatatype::DROPDOWN_CHOICE_TEXT:
                                default:
                                    $target = ($action->getTargetValue()) ? \thebuggenie\core\entities\CustomDatatypeOption::getB2DBTable()->selectById((int) $action->getTargetValue()) : null;
                                    echo __('Set issue field %key to %value', array('%key' => $action->getCustomActionType(), '%value' => '<span id="workflowtransitionaction_'.$action->getID().'_value" style="font-weight: bold;">' . (($action->getTargetValue()) ? $target->getName() : __('Value provided by user')) . '</span>'));
                                    break;
                            }
                            ?>
                        <?php endif; ?>
                    <?php elseif ($action->getTargetValue()): ?>
                        <span class="generic_error_message"><?php echo __('Invalid transition configuration'); ?></span>
                    <?php endif; ?>
                </td>
                <?php if (!$action->getTransition()->isCore()): ?>
                    <?php if (! ($action->isCustomSetAction() && in_array(\thebuggenie\core\entities\CustomDatatype::getByKey($action->getCustomActionType())->getType(), array(\thebuggenie\core\entities\CustomDatatype::USER_CHOICE, \thebuggenie\core\entities\CustomDatatype::TEAM_CHOICE, \thebuggenie\core\entities\CustomDatatype::CLIENT_CHOICE)))): ?>
                        <td id="workflowtransitionaction_<?php echo $action->getID(); ?>_edit" style="display: none; padding: 2px;">
                            <form action="<?php echo make_url('configure_workflow_transition_update_action', array('workflow_id' => $action->getWorkflow()->getID(), 'transition_id' => $action->getTransition()->getID(), 'action_id' => $action->getID())); ?>" onsubmit="TBG.Config.Workflows.Transition.Actions.update('<?php echo make_url('configure_workflow_transition_update_action', array('workflow_id' => $action->getWorkflow()->getID(), 'transition_id' => $action->getTransition()->getID(), 'action_id' => $action->getID())); ?>', <?php echo $action->getID(); ?>);return false;" id="workflowtransitionaction_<?php echo $action->getID(); ?>_form">
                                <input type="submit" value="<?php echo __('Update'); ?>" style="float: right;position: relative;z-index:1;">
                                <label for="workflowtransitionaction_<?php echo $action->getID(); ?>_input">
                                    <?php if ($action->getActionType() == \thebuggenie\core\entities\WorkflowTransitionAction::ACTION_SET_STATUS): ?>
                                        <?php echo __('Set status to %status', array('%status' => '')); ?>
                                    <?php elseif ($action->getActionType() == \thebuggenie\core\entities\WorkflowTransitionAction::ACTION_SET_PRIORITY): ?>
                                        <?php echo __('Set priority to %priority', array('%priority' => '')); ?>
                                    <?php elseif ($action->getActionType() == \thebuggenie\core\entities\WorkflowTransitionAction::ACTION_SET_PERCENT): ?>
                                        <?php echo __('Set percent completed to %percentcompleted', array('%percentcompleted' => '')); ?>
                                    <?php elseif ($action->getActionType() == \thebuggenie\core\entities\WorkflowTransitionAction::ACTION_SET_RESOLUTION): ?>
                                        <?php echo __('Set resolution to %resolution', array('%resolution' => '')); ?>
                                    <?php elseif ($action->getActionType() == \thebuggenie\core\entities\WorkflowTransitionAction::ACTION_SET_REPRODUCABILITY): ?>
                                        <?php echo __('Set reproducability to %reproducability', array('%reproducability' => '')); ?>
                                    <?php elseif ($action->getActionType() == \thebuggenie\core\entities\WorkflowTransitionAction::ACTION_ASSIGN_ISSUE): ?>
                                        <?php echo __('Assign issue to %user', array('%user' => '')); ?>
                                    <?php elseif ($action->isCustomSetAction()): ?>
                                        <?php echo __('Set issue field %key to %value', array('%key' => $action->getCustomActionType(), '%value' => '')); ?>
                                    <?php endif; ?>
                                </label>
                                <?php

                                    if ($action->getActionType() == \thebuggenie\core\entities\WorkflowTransitionAction::ACTION_SET_STATUS)
                                        $options = \thebuggenie\core\entities\Status::getAll();
                                    elseif ($action->getActionType() == \thebuggenie\core\entities\WorkflowTransitionAction::ACTION_SET_PRIORITY)
                                        $options = \thebuggenie\core\entities\Priority::getAll();
                                    elseif ($action->getActionType() == \thebuggenie\core\entities\WorkflowTransitionAction::ACTION_SET_PERCENT)
                                        $options = range(1, 100);
                                    elseif ($action->getActionType() == \thebuggenie\core\entities\WorkflowTransitionAction::ACTION_SET_RESOLUTION)
                                        $options = \thebuggenie\core\entities\Resolution::getAll();
                                    elseif ($action->getActionType() == \thebuggenie\core\entities\WorkflowTransitionAction::ACTION_SET_REPRODUCABILITY)
                                        $options = \thebuggenie\core\entities\Reproducability::getAll();
                                    elseif ($action->isCustomAction()) {
                                        $customfield = \thebuggenie\core\entities\CustomDatatype::getByKey($action->getCustomActionType());
                                        if ($customfield->getType() == \thebuggenie\core\entities\CustomDatatype::CALCULATED_FIELD) {
                                            $options = array();
                                        } else {
                                            $options = $customfield->getOptions();
                                        }
                                    }

                                ?>
                                <?php if (isset($options) && count($options)): ?>
                                    <select id="workflowtransitionaction_<?php echo $action->getID(); ?>_input" name="target_value">
                                        <option value="0"<?php if ((int) $action->getTargetValue() == 0) echo ' selected'; ?> <?php if (!$action->getTransition()->hasTemplate()): ?>style="display: none;"<?php endif; ?>>
                                            <?php if ($action->getActionType() == \thebuggenie\core\entities\WorkflowTransitionAction::ACTION_SET_STATUS): ?>
                                                <?php echo __('Status provided by user'); ?>
                                            <?php elseif ($action->getActionType() == \thebuggenie\core\entities\WorkflowTransitionAction::ACTION_SET_PRIORITY): ?>
                                                <?php echo __('Priority provided by user'); ?>
                                            <?php elseif ($action->getActionType() == \thebuggenie\core\entities\WorkflowTransitionAction::ACTION_SET_PERCENT): ?>
                                                <?php echo __('Percentage provided by user'); ?>
                                            <?php elseif ($action->getActionType() == \thebuggenie\core\entities\WorkflowTransitionAction::ACTION_SET_RESOLUTION): ?>
                                                <?php echo __('Resolution provided by user'); ?>
                                            <?php elseif ($action->getActionType() == \thebuggenie\core\entities\WorkflowTransitionAction::ACTION_SET_REPRODUCABILITY): ?>
                                                <?php echo __('Reproducability provided by user'); ?>
                                            <?php elseif ($action->isCustomAction()): ?>
                                                <?php echo __('Value provided by user'); ?>
                                            <?php elseif ($action->getActionType() == \thebuggenie\core\entities\WorkflowTransitionAction::ACTION_ASSIGN_ISSUE): ?>
                                                <?php echo __('User or team specified during transition'); ?>
                                            <?php endif; ?>
                                        </option>
                                        <?php if ($action->getActionType() == \thebuggenie\core\entities\WorkflowTransitionAction::ACTION_ASSIGN_ISSUE): ?>
                                            <optgroup label="<?php echo __('Available users'); ?>">
                                                <?php foreach ($available_assignees_users as $option): ?>
                                                    <option value="user_<?php echo $option->getID(); ?>"<?php if (isset($target_details) && (int) $target_details[1] == $option->getID()) echo ' selected'; ?>>
                                                        <?php echo $option->getNameWithUsername(); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </optgroup>
                                            <optgroup label="<?php echo __('Available teams'); ?>">
                                                <?php foreach ($available_assignees_teams as $option): ?>
                                                    <option value="team_<?php echo $option->getID(); ?>"<?php if (isset($target_details) && (int) $target_details[1] == $option->getID()) echo ' selected'; ?>>
                                                        <?php echo $option->getName(); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </optgroup>
                                        <?php else: ?>
                                            <?php foreach ((isset($options) ? $options : array()) as $option): ?>
                                                <option value="<?php echo ($option instanceof \thebuggenie\core\entities\common\Identifiable) ? $option->getID() : $option; ?>"<?php if (($option instanceof \thebuggenie\core\entities\common\Identifiable && (int) $action->getTargetValue() == $option->getID()) || (!$option instanceof \thebuggenie\core\entities\common\Identifiable && (int) $action->getTargetValue() == $option)) echo ' selected'; ?>>
                                                    <?php if ($option instanceof \thebuggenie\core\entities\User): ?>
                                                        <?php echo $option->getNameWithUsername(); ?>
                                                    <?php elseif ($option instanceof \thebuggenie\core\entities\Milestone || $option instanceof \thebuggenie\core\entities\Build || $option instanceof \thebuggenie\core\entities\Component): ?>
                                                        <?php echo $option->getProject()->getName() . ' - ' . $option->getName(); ?>
                                                    <?php elseif ($option instanceof \thebuggenie\core\entities\common\Identifiable): ?>
                                                        <?php echo $option->getName(); ?>
                                                    <?php else: ?>
                                                        <?php echo $option; ?>
                                                    <?php endif; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                <?php else: ?>
                                    <?php if ($action->isCustomSetAction()): ?>
                                    <?php
                                        switch (\thebuggenie\core\entities\CustomDatatype::getByKey($action->getCustomActionType())->getType()) {
                                            case \thebuggenie\core\entities\CustomDatatype::INPUT_TEXTAREA_MAIN:
                                            case \thebuggenie\core\entities\CustomDatatype::INPUT_TEXTAREA_SMALL:
                                                echo include_component('main/textarea', array('area_name' => 'target_value', 'target_type' => 'workflowtransitionaction', 'target_id' => $action->getID(), 'area_id' => 'workflowtransitionaction_'. $action->getID() .'_value', 'height' => '100px', 'width' => '100%', 'value' => $action->getTargetValue()));
                                                break;
                                            case \thebuggenie\core\entities\CustomDatatype::DATE_PICKER: ?>
                                                <input type="hidden" id="workflowtransitionaction_<?php echo $action->getID(); ?>_value_1" name="target_value" value="<?php echo ($action->getTargetValue() ? date('Y-m-d', $action->getTargetValue()) : ''); ?>">
                                                <div id="customfield_<?php echo 'workflowtransitionaction_'. $action->getID(); ?>_calendar_container"></div>
                                                <script type="text/javascript">
                                                    Calendar.setup({
                                                        dateField: "<?php echo 'workflowtransitionaction_'. $action->getID(); ?>_value_1",
                                                        parentElement: "customfield_<?php echo 'workflowtransitionaction_'. $action->getID(); ?>_calendar_container"
                                                    });
                                                </script>
                                                <?php
                                                break;
                                            case \thebuggenie\core\entities\CustomDatatype::INPUT_TEXT:
                                            case \thebuggenie\core\entities\CustomDatatype::CALCULATED_FIELD: ?>
                                                <input type="text" id="workflowtransitionaction_<?php echo $action->getID(); ?>_value_1" name="target_value" value="<?php echo ($action->getTargetValue() ?: ''); ?>" style="width: 400px;">
                                                <?php
                                                break;
                                        }
                                    ?>
                                    <?php endif; ?>
                                    <?php  ?>
                                <?php endif; ?>
                                <?php echo image_tag('spinning_16.gif', array('id' => 'workflowtransitionaction_' . $action->getID() . '_indicator', 'style' => 'display: none; margin-left: 5px;')); ?>
                            </form>
                        </td>
                    <?php endif; ?>
                <?php endif; ?>
                <?php
                break;
        }

    ?>
    <?php if (!$action->getTransition()->isCore()): ?>
        <td style="width: 100px; text-align: right;">
            <div style="position: relative;">
                <?php if ($show_edit): ?>
                    <?php if ($action->isCustomSetAction() && in_array(\thebuggenie\core\entities\CustomDatatype::getByKey($action->getCustomActionType())->getType(), array(\thebuggenie\core\entities\CustomDatatype::USER_CHOICE, \thebuggenie\core\entities\CustomDatatype::TEAM_CHOICE, \thebuggenie\core\entities\CustomDatatype::CLIENT_CHOICE))): ?>
                        <button id="workflowtransitionaction_<?php echo $action->getID(); ?>_edit_button" class="dropper"><?php echo __('Edit'); ?></button>
                        <?php
                            switch (\thebuggenie\core\entities\CustomDatatype::getByKey($action->getCustomActionType())->getType()) {
                                case \thebuggenie\core\entities\CustomDatatype::USER_CHOICE:
                                     echo include_component('main/identifiableselector', array('html_id'        => 'workflowtransitionaction_'. $action->getID().'_edit',
                                                                            'header'          => __('Select a user'),
                                                                                'callback' => "TBG.Config.Workflows.Transition.Actions.update('". make_url('configure_workflow_transition_update_action', array('workflow_id' => $action->getWorkflow()->getID(), 'transition_id' => $action->getTransition()->getID(), 'action_id' => $action->getID())) ."?target_value=%identifiable_value', '". $action->getID() ."')",
                                                                                'clear_link_text' => __('Clear currently selected user'),
                                                                                'base_id'         => 'workflowtransitionaction_'. $action->getID(),
                                                                                'include_users'   => true,
                                                                                'include_teams'   => false,
                                                                                'include_clients' => false,
                                                                                'absolute'        => true,
                                                                                'hidden'          => false,
                                                                                'classes'         => 'leftie popup_box more_actions_dropdown'));
                                    break;
                                case \thebuggenie\core\entities\CustomDatatype::TEAM_CHOICE:
                                     echo include_component('main/identifiableselector', array('html_id'        => 'workflowtransitionaction_'. $action->getID().'_edit',
                                                                            'header'          => __('Select a team'),
                                                                                'callback' => "TBG.Config.Workflows.Transition.Actions.update('". make_url('configure_workflow_transition_update_action', array('workflow_id' => $action->getWorkflow()->getID(), 'transition_id' => $action->getTransition()->getID(), 'action_id' => $action->getID())) ."?target_value=%identifiable_value', '". $action->getID() ."')",
                                                                                'clear_link_text' => __('Clear currently selected team'),
                                                                                'base_id'         => 'workflowtransitionaction_'. $action->getID(),
                                                                                'include_users'   => false,
                                                                                'include_teams'   => true,
                                                                                'include_clients' => false,
                                                                                'absolute'        => true,
                                                                                'hidden'          => false,
                                                                                'classes'         => 'leftie popup_box more_actions_dropdown'));
                                    break;
                                case \thebuggenie\core\entities\CustomDatatype::CLIENT_CHOICE:
                                     echo include_component('main/identifiableselector', array('html_id'        => 'workflowtransitionaction_'. $action->getID().'_edit',
                                                                            'header'          => __('Select a client'),
                                                                                'callback' => "TBG.Config.Workflows.Transition.Actions.update('". make_url('configure_workflow_transition_update_action', array('workflow_id' => $action->getWorkflow()->getID(), 'transition_id' => $action->getTransition()->getID(), 'action_id' => $action->getID())) ."?target_value=%identifiable_value', '". $action->getID() ."')",
                                                                                'clear_link_text' => __('Clear currently selected client'),
                                                                                'base_id'         => 'workflowtransitionaction_'. $action->getID(),
                                                                                'include_users'   => false,
                                                                                'include_teams'   => false,
                                                                                'include_clients' => true,
                                                                                'absolute'        => true,
                                                                                'hidden'          => false,
                                                                                'classes'         => 'leftie popup_box more_actions_dropdown'));
                                    break;
                            } ?>
                    <?php else: ?>
                        <button id="workflowtransitionaction_<?php echo $action->getID(); ?>_edit_button" onclick="$('workflowtransitionaction_<?php echo $action->getID(); ?>_edit_button').toggle();$('workflowtransitionaction_<?php echo $action->getID(); ?>_delete_button').toggle();$('workflowtransitionaction_<?php echo $action->getID(); ?>_cancel_button').toggle();$('workflowtransitionaction_<?php echo $action->getID(); ?>_description').toggle();$('workflowtransitionaction_<?php echo $action->getID(); ?>_edit').toggle();"><?php echo __('Edit'); ?></button>
                    <?php endif; ?>
                <?php endif; ?>
                <button id="workflowtransitionaction_<?php echo $action->getID(); ?>_delete_button" onclick="TBG.Main.Helpers.Dialog.show('<?php echo __('Do you really want to delete this transition action?'); ?>', '<?php echo __('Please confirm that you really want to delete this transition action.'); ?>', {yes: {click: function() {TBG.Config.Workflows.Transition.Actions.remove('<?php echo make_url('configure_workflow_transition_delete_action', array('workflow_id' => $action->getWorkflow()->getID(), 'transition_id' => $action->getTransition()->getID(), 'action_id' => $action->getID())); ?>', <?php echo $action->getID(); ?>, '<?php echo $action->getActionType(); ?>'); }}, no: { click: TBG.Main.Helpers.Dialog.dismiss }});"><?php echo __('Delete'); ?></button>
                <?php if ($show_edit): ?>
                    <button id="workflowtransitionaction_<?php echo $action->getID(); ?>_cancel_button" onclick="$('workflowtransitionaction_<?php echo $action->getID(); ?>_edit_button').toggle();$('workflowtransitionaction_<?php echo $action->getID(); ?>_delete_button').toggle();$('workflowtransitionaction_<?php echo $action->getID(); ?>_cancel_button').toggle();$('workflowtransitionaction_<?php echo $action->getID(); ?>_description').toggle();$('workflowtransitionaction_<?php echo $action->getID(); ?>_edit').toggle();" style="display: none;"><?php echo __('Cancel'); ?></button>
                <?php endif; ?>
            </div>
        </td>
    <?php endif; ?>
</tr>
