<?php

namespace thebuggenie\modules\agile\cli;

use thebuggenie\modules\agile\Agile;



function dd()
{
  array_map(function($x) { var_dump($x); }, func_get_args()); die;
}

/**
 * CLI command class, agile -> report
 *
 * @author
 * @version 0.1
 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
 * @package agile
 * @subpackage core
 */

/**
 * CLI command class, agile -> report
 *
 * @package thebuggenie
 * @subpackage agile
 */
class FixDeletedWorkflow extends \thebuggenie\core\framework\cli\Command
{

  protected function _setup()
  {
    $this->_command_name = 'fix_deleted_workflow';
    $this->_description = "Map new workflow steps for isses that had old (deleted) workflow steps";
    $this->addRequiredArgument('old_workflow_id', "Old workflow ID");
    $this->addRequiredArgument('new_workflow_id', "New workflow ID");
  }

  public function do_execute()
  {
    \thebuggenie\core\framework\Context::loadLibrary('ui');
    set_time_limit(60 * 60 * 24);
    ini_set('memory_limit', '1024M');

    $iteration = 0;
    $results_per_page = 1000;
    $default_workflow_id = 1;
    $statuses_workflow_steps = array();
    $default_workflow = \thebuggenie\core\entities\Workflow::getB2DBTable()->selectById($default_workflow_id);

    foreach ($default_workflow->getSteps() as $workflow_step)
    {
      if (! $workflow_step->hasLinkedStatus()) continue;

      $statuses_workflow_steps[$workflow_step->getLinkedStatus()->getID()] = $workflow_step;
    }

    foreach (\thebuggenie\core\entities\tables\WorkflowTransitionValidationRules::getTable()->getByWorkflowIDAndRule($default_workflow_id, \thebuggenie\core\entities\WorkflowTransitionValidationRule::RULE_STATUS_VALID) as $workflow_transition_validation_rule)
    {
      $statuses_ids = explode(',', $workflow_transition_validation_rule->getRuleValue());

      $outgoing_workflow_step = $workflow_transition_validation_rule->getTransition()->getOutgoingStep();

      if ($outgoing_workflow_step->hasLinkedStatus()) continue;

      foreach ($statuses_ids as $status_id) $statuses_workflow_steps[$status_id] = $outgoing_workflow_step;
    }

    $mailing = \thebuggenie\core\framework\Context::getModule('mailing');
    $enable_outgoing_notifications = false;

    if ($mailing->isOutgoingNotificationsEnabled()) {
      $enable_outgoing_notifications = true;
      $mailing->setOutgoingNotificationsEnabled(false);
    }

    //$iteration = 500;

    while (true) {
      $rows = \thebuggenie\core\entities\tables\Issues::getTable()->findIssues(array(), $results_per_page, $iteration * $results_per_page);

      //dd($rows);

      if (! count($rows[0])) break;

      $rows = $rows[0];

      foreach ($rows as $key => $row)
      {
        try
        {
          $issue = new \thebuggenie\core\entities\Issue($row->get(\thebuggenie\core\entities\tables\Issues::ID), $row);

          unset($rows[$key]);

          //dd($issue->getID(), $issue->getStatus()->getID(), $statuses_workflow_steps[$issue->getStatus()->getID()]->getID(), $issue->getWorkflowStep()->getID());

          $issue_status_id = $issue->getStatus()->getID();

          if (! array_key_exists($issue_status_id, $statuses_workflow_steps))
          {
            $this->cliEcho("Issue ". $issue->getID() . "\n");

            continue;
          }

          $issue->setWorkflowStep($statuses_workflow_steps[$issue_status_id]);
          $issue->save();
          unset($issue);
        }
        catch (\Exception $e) {}
        //break 2;
      }

      $iteration++;
    }

    if ($enable_outgoing_notifications) {
      $mailing->setOutgoingNotificationsEnabled(true);
    }

    //dd(1);

    $this->cliEcho('Done');
  }
}
