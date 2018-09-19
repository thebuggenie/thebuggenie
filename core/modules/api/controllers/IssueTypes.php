<?php

namespace thebuggenie\core\modules\api\controllers;

use thebuggenie\core\framework,
    thebuggenie\core\entities;

/** @noinspection PhpInconsistentReturnPointsInspection */

/**
 * Main actions for the api module
 *
 * @property entities\Project[] $projects
 * @property entities\Issuetype[] $issuetypes
 * @property array $json
 *
 * @Routes(name_prefix="api_projects_issuefields_", url_prefix="/api/v1/projects/:project_id")
 */
class IssueTypes extends ProjectNamespacedController
{

    /**
     * List all project's issue fields
     *
     * @Route(name="get", url="/issuetypes")
     * @param framework\Request $request
     */
    public function runListIssuetypes(framework\Request $request)
    {
        $this->issuetypes = $this->selected_project->getIssuetypeScheme()->getIssuetypes();
    }

    /**
     * List all project's issue fields
     *
     * @Route(name="list", url="/issuetypes/:issuetype")
     * @param framework\Request $request
     */
    public function runListIssuefields(framework\Request $request)
    {
        try {
            $issuetype = entities\tables\IssueTypes::getTable()->selectById($request['issuetype']);

            if (!$issuetype instanceof entities\Issuetype) {
                $this->getResponse()->setHttpStatus(404);
                return $this->renderJSON(['error' => 'Issue type not found']);
            }

            $json = $issuetype->toJSON();
            $json['fields'] = $this->selected_project->getVisibleFieldsArray($issuetype->getID());

            $this->json = $json;
        } catch (\Exception $e) {
            $this->getResponse()->setHttpStatus(500);
            return $this->renderJSON(['error' => 'An exception occurred: ' . $e]);
        }
    }

    /**
     * List all available values for a project's issue field
     *
     * @Route(name="get_value", url="/issuetypes/:issuetype/fields/:field_key")
     * @param framework\Request $request
     */
    public function runListFieldValue(framework\Request $request)
    {
        $field_key = $request['field_key'];
        $field_keys = array_merge(entities\DatatypeBase::getAvailableFields(true), ['title', 'activitytype']);
        $return_array = [
            'description' => null,
            'type' => null,
            'choices' => null
        ];

        if (!in_array($field_key, $field_keys)) {
            $this->getResponse()->setHttpStatus(404);
            return $this->renderJSON(['error' => 'Issue field not found']);
        }

        switch ($field_key)
        {
            case 'title':
                $return_array['description'] = framework\Context::getI18n()->__('Single line text input without formatting');
                $return_array['type'] = 'single_line_input';
                break;
            case 'description':
            case 'reproduction_steps':
                $return_array['description'] = framework\Context::getI18n()->__('Text input with wiki formatting capabilities');
                $return_array['type'] = 'wiki_input';
                break;
            case 'status':
            case 'resolution':
            case 'reproducability':
            case 'priority':
            case 'severity':
            case 'category':
            case 'activitytype':
                $return_array['description'] = framework\Context::getI18n()->__('Choose one of the available values');
                $return_array['type'] = 'choice';

                if ($field_key == 'status') {
                    $choices = $this->selected_project->getAvailableStatuses();
                } else {
                    $class_prefix = "\\thebuggenie\\core\\entities\\";
                    $class_name = ($field_key == 'activitytype') ? 'ActivityType' : ucfirst($field_key);
                    $fqcn = $class_prefix . $class_name;
                    $choices = $fqcn::getAll();
                }

                foreach ($choices as $choice_key => $choice)
                {
                    $return_array['choices'][$choice->getID()] = $choice->toJSON(true);
                }
                break;
            case 'percent_complete':
                $return_array['description'] = framework\Context::getI18n()->__('Value of percentage completed');
                $return_array['type'] = 'choice';
                $return_array['choices'] = range(0, 100);
                break;
            case 'owner':
            case 'assignee':
                $return_array['description'] = framework\Context::getI18n()->__('Select an existing user or <none>');
                $return_array['type'] = 'select_user';
                break;
            case 'estimated_time':
            case 'spent_time':
                $return_array['description'] = framework\Context::getI18n()->__('Enter time, such as points, hours, minutes, etc or <none>');
                $return_array['type'] = 'time';
                break;
            case 'milestone':
                $return_array['description'] = framework\Context::getI18n()->__('Select from available project milestones');
                $return_array['type'] = 'choice';
                $milestones = $this->selected_project->getAvailableMilestones();
                foreach ($milestones as $milestone)
                {
                    $return_array['choices'][$milestone->getID()] = $milestone->toJSON(false);
                }
                break;
        }

        $this->field_info = $return_array;
    }

}
