<?php echo __('The following actions could not be performed because of missing or invalid values: %list', array('%list' => '')); ?><br>
<ul>
  <?php foreach (\thebuggenie\core\framework\Context::getMessageAndClear('issue_workflow_errors') as $error_field): ?>
    <li><?php

      switch ($error_field)
      {
        case \thebuggenie\core\entities\WorkflowTransitionValidationRule::RULE_MAX_ASSIGNED_ISSUES:
          echo __('Could not assign issue to the selected user because this users assigned issues limit is reached');
          break;
        case \thebuggenie\core\entities\WorkflowTransitionValidationRule::RULE_TEAM_MEMBERSHIP_VALID:
          echo __('Could not process issue transition since assigned user is not member of any of allowed teams');
          break;
        case \thebuggenie\core\entities\WorkflowTransitionValidationRule::RULE_ISSUE_IN_MILESTONE_VALID:
          echo __('Could not process issue transition since issue is not in of any of allowed milestones');
          break;
        case \thebuggenie\core\entities\WorkflowTransitionValidationRule::RULE_PRIORITY_VALID:
          echo __('Could not set priority');
          break;
        case \thebuggenie\core\entities\WorkflowTransitionValidationRule::RULE_REPRODUCABILITY_VALID:
          echo __('Could not set reproducability');
          break;
        case \thebuggenie\core\entities\WorkflowTransitionValidationRule::RULE_RESOLUTION_VALID:
          echo __('Could not set resolution');
          break;
        case \thebuggenie\core\entities\WorkflowTransitionValidationRule::RULE_STATUS_VALID:
          echo __('Could not set status');
          break;
        case \thebuggenie\core\entities\WorkflowTransitionAction::ACTION_ASSIGN_ISSUE:
          echo __('Could not assign issue to the any user or team because none were provided');
          break;
        case \thebuggenie\core\entities\WorkflowTransitionAction::ACTION_SET_MILESTONE:
          echo __('Could not assign the issue to a milestone because none was provided');
          break;
        case \thebuggenie\core\entities\WorkflowTransitionAction::ACTION_SET_PRIORITY:
          echo __('Could not set issue priority because none was provided');
          break;
        case \thebuggenie\core\entities\WorkflowTransitionAction::ACTION_SET_REPRODUCABILITY:
          echo __('Could not set issue reproducability because none was provided');
          break;
        case \thebuggenie\core\entities\WorkflowTransitionAction::ACTION_SET_RESOLUTION:
          echo __('Could not set issue resolution because none was provided');
          break;
        case \thebuggenie\core\entities\WorkflowTransitionAction::ACTION_SET_STATUS:
          echo __('Could not set issue status because none was provided or you do not have permission');
          break;
        default:
          echo $error_field;
          break;
      }

      ?></li>
  <?php endforeach; ?>
</ul>
