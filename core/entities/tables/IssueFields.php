<?php

    namespace thebuggenie\core\entities\tables;

    use b2db\Insertion;
    use thebuggenie\core\entities\IssuetypeScheme;
    use thebuggenie\core\entities\Scope;
    use thebuggenie\core\framework;

    /**
     * Issue fields table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage tables
     */

    /**
     * Issue fields table
     *
     * @package thebuggenie
     * @subpackage tables
     *
     * @Table(name="issuefields")
     */
    class IssueFields extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 2;
        const B2DBNAME = 'issuefields';
        const ID = 'issuefields.id';
        const SCOPE = 'issuefields.scope';
        const ADDITIONAL = 'issuefields.is_additional';
        const ISSUETYPE_ID = 'issuefields.issuetype_id';
        const ISSUETYPE_SCHEME_ID = 'issuefields.issuetype_scheme_id';
        const FIELD_KEY = 'issuefields.field_key';
        const REPORTABLE = 'issuefields.is_reportable';
        const REQUIRED = 'issuefields.required';

        protected function initialize()
        {
            parent::setup(self::B2DBNAME, self::ID);
            parent::addVarchar(self::FIELD_KEY, 100);
            parent::addBoolean(self::REQUIRED);
            parent::addBoolean(self::REPORTABLE);
            parent::addBoolean(self::ADDITIONAL);
            parent::addForeignKeyColumn(self::ISSUETYPE_ID, IssueTypes::getTable(), IssueTypes::ID);
            parent::addForeignKeyColumn(self::ISSUETYPE_SCHEME_ID, IssuetypeSchemes::getTable(), IssuetypeSchemes::ID);
        }

        protected function setupIndexes()
        {
            $this->addIndex('scope_issuetypescheme_issuetype', array(self::SCOPE, self::ISSUETYPE_SCHEME_ID, self::ISSUETYPE_ID));
        }

        public function getSchemeVisibleFieldsArrayByIssuetypeID($scheme_id, $issuetype_id)
        {
            $res = $this->getBySchemeIDandIssuetypeID($scheme_id, $issuetype_id);
            $retval = array();
            if ($res)
            {
                while ($row = $res->getNextRow())
                {
                    $retval[$row->get(IssueFields::FIELD_KEY)] = array(
                        'label' => $row->get(CustomFields::FIELD_DESCRIPTION),
                        'required' => (bool)$row->get(IssueFields::REQUIRED),
                        'reportable' => (bool)$row->get(IssueFields::REPORTABLE),
                        'additional' => (bool)$row->get(IssueFields::ADDITIONAL),
                        'type' => $row->get(CustomFields::FIELD_TYPE) ? $row->get(CustomFields::FIELD_TYPE) : 'builtin',
                    );
                }
            }
            return $retval;
        }

        public function deleteBySchemeIDandIssuetypeID($scheme_id, $issuetype_id)
        {
            $query = $this->getQuery();
            $query->where(self::ISSUETYPE_SCHEME_ID, $scheme_id);
            $query->where(self::ISSUETYPE_ID, $issuetype_id);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $res = $this->rawDelete($query);
        }

        public function copyBySchemeIDs($from_scheme_id, $to_scheme_id)
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->where(self::ISSUETYPE_SCHEME_ID, $from_scheme_id);
            if ($res = $this->rawSelect($query))
            {
                while ($row = $res->getNextRow())
                {
                    $insertion = new Insertion();
                    $insertion->add(self::SCOPE, framework\Context::getScope()->getID());
                    $insertion->add(self::ISSUETYPE_SCHEME_ID, $to_scheme_id);
                    $insertion->add(self::FIELD_KEY, $row->get(self::FIELD_KEY));
                    $insertion->add(self::ADDITIONAL, $row->get(self::ADDITIONAL));
                    $insertion->add(self::ISSUETYPE_ID, $row->get(self::ISSUETYPE_ID));
                    $insertion->add(self::REPORTABLE, $row->get(self::REPORTABLE));
                    $insertion->add(self::REQUIRED, $row->get(self::REQUIRED));
                    $this->rawInsert($insertion);
                }
            }
        }

        public function addFieldAndDetailsBySchemeIDandIssuetypeID($scheme_id, $issuetype_id, $key, $details)
        {
            $insertion = new Insertion();
            $insertion->add(self::ISSUETYPE_SCHEME_ID, $scheme_id);
            $insertion->add(self::ISSUETYPE_ID, $issuetype_id);
            $insertion->add(self::FIELD_KEY, $key);
            if (array_key_exists('reportable', $details))
            {
                $insertion->add(self::REPORTABLE, true);
            }
            if (array_key_exists('additional', $details))
            {
                $insertion->add(self::ADDITIONAL, true);
            }
            if (array_key_exists('required', $details))
            {
                $insertion->add(self::REQUIRED, true);
            }

            $insertion->add(self::SCOPE, framework\Context::getScope()->getID());
            $this->rawInsert($insertion);
        }

        public function getBySchemeIDandIssuetypeID($scheme_id, $issuetype_id)
        {
            $query = $this->getQuery();
            $query->join(CustomFields::getTable(), CustomFields::FIELD_KEY, self::FIELD_KEY);
            $query->where(self::ISSUETYPE_SCHEME_ID, $scheme_id);
            $query->where(self::ISSUETYPE_ID, $issuetype_id);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $res = $this->rawSelect($query, false);
            return $res;
        }

        public function deleteByIssuetypeSchemeID($scheme_id)
        {
            $query = $this->getQuery();
            $query->where(self::ISSUETYPE_SCHEME_ID, $scheme_id);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $res = $this->rawDelete($query);
        }

        public function deleteByIssueFieldKey($key)
        {
            $query = $this->getQuery();
            $query->where(self::FIELD_KEY, $key);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $res = $this->rawDelete($query);
        }

        public function loadFixtures(Scope $scope, IssuetypeScheme $full_range_scheme, IssuetypeScheme $balanced_scheme, IssuetypeScheme $balanced_agile_scheme, IssuetypeScheme $simple_scheme, $issue_type_bug_report_id, $issue_type_feature_request_id, $issue_type_enhancement_id, $issue_type_task_id, $issue_type_user_story_id, $issue_type_idea_id, $issue_type_epic_id)
        {
            $scope = $scope->getID();
            $schemes = [
                $full_range_scheme->getID() => [
                    $issue_type_bug_report_id => [
                        'description' => ['reportable' => true, 'required' => true, 'additional' => false],
                        'reproduction_steps' => ['reportable' => true, 'required' => true, 'additional' => false],
                        'edition' => ['reportable' => true, 'required' => false, 'additional' => true],
                        'build' => ['reportable' => true, 'required' => false, 'additional' => false],
                        'component' => ['reportable' => true, 'required' => false, 'additional' => false],
                        'category' => ['reportable' => true, 'required' => false, 'additional' => true],
                        'reproducability' => ['reportable' => true, 'required' => false, 'additional' => false],
                        'resolution' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'milestone' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'estimated_time' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'spent_time' => ['reportable' => false, 'required' => false, 'additional' => false],
                    ],
                    $issue_type_feature_request_id => [
                        'description' => ['reportable' => true, 'required' => true, 'additional' => false],
                        'category' => ['reportable' => true, 'required' => false, 'additional' => true],
                        'milestone' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'estimated_time' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'spent_time' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'percent_complete' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'priority' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'votes' => ['reportable' => false, 'required' => false, 'additional' => false],
                    ],
                    $issue_type_enhancement_id => [
                        'description' => ['reportable' => true, 'required' => true, 'additional' => false],
                        'category' => ['reportable' => true, 'required' => false, 'additional' => true],
                        'milestone' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'estimated_time' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'spent_time' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'percent_complete' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'priority' => ['reportable' => false, 'required' => false, 'additional' => false],
                    ],
                    $issue_type_task_id => [
                        'description' => ['reportable' => true, 'required' => true, 'additional' => false],
                        'category' => ['reportable' => true, 'required' => false, 'additional' => true],
                        'milestone' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'estimated_time' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'spent_time' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'percent_complete' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'priority' => ['reportable' => false, 'required' => false, 'additional' => false],
                    ],
                    $issue_type_idea_id => [
                        'description' => ['reportable' => true, 'required' => true, 'additional' => false],
                        'category' => ['reportable' => true, 'required' => false, 'additional' => true],
                        'milestone' => ['reportable' => true, 'required' => false, 'additional' => true],
                        'estimated_time' => ['reportable' => true, 'required' => false, 'additional' => true],
                        'spent_time' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'percent_complete' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'priority' => ['reportable' => false, 'required' => false, 'additional' => false],
                    ],
                    $issue_type_user_story_id => [
                        'description' => ['reportable' => true, 'required' => true, 'additional' => false],
                        'category' => ['reportable' => true, 'required' => false, 'additional' => false],
                        'milestone' => ['reportable' => true, 'required' => false, 'additional' => true],
                        'estimated_time' => ['reportable' => true, 'required' => false, 'additional' => false],
                        'spent_time' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'percent_complete' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'priority' => ['reportable' => true, 'required' => false, 'additional' => true],
                    ],
                    $issue_type_epic_id => [
                        'shortname' => ['reportable' => true, 'required' => true, 'additional' => false],
                    ],
                ],
                $balanced_scheme->getID() => [
                    $issue_type_bug_report_id => [
                        'description' => ['reportable' => true, 'required' => true, 'additional' => false],
                        'reproduction_steps' => ['reportable' => true, 'required' => false, 'additional' => false],
                        'edition' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'build' => ['reportable' => true, 'required' => false, 'additional' => false],
                        'component' => ['reportable' => true, 'required' => false, 'additional' => true],
                        'category' => ['reportable' => true, 'required' => false, 'additional' => false],
                        'reproducability' => ['reportable' => true, 'required' => false, 'additional' => false],
                        'resolution' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'milestone' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'estimated_time' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'spent_time' => ['reportable' => false, 'required' => false, 'additional' => false],
                    ],
                    $issue_type_feature_request_id => [
                        'description' => ['reportable' => true, 'required' => true, 'additional' => false],
                        'category' => ['reportable' => true, 'required' => false, 'additional' => true],
                        'milestone' => ['reportable' => true, 'required' => false, 'additional' => true],
                        'estimated_time' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'spent_time' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'percent_complete' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'priority' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'votes' => ['reportable' => false, 'required' => false, 'additional' => false],
                    ],
                    $issue_type_task_id => [
                        'description' => ['reportable' => true, 'required' => true, 'additional' => false],
                        'category' => ['reportable' => true, 'required' => false, 'additional' => true],
                        'estimated_time' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'spent_time' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'priority' => ['reportable' => false, 'required' => false, 'additional' => false],
                    ],
                    $issue_type_idea_id => [
                        'description' => ['reportable' => true, 'required' => true, 'additional' => false],
                        'category' => ['reportable' => true, 'required' => false, 'additional' => true],
                        'milestone' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'estimated_time' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'priority' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'votes' => ['reportable' => false, 'required' => false, 'additional' => false],
                    ],
                ],
                $balanced_agile_scheme->getID() => [
                    $issue_type_bug_report_id => [
                        'description' => ['reportable' => true, 'required' => true, 'additional' => false],
                        'reproduction_steps' => ['reportable' => true, 'required' => false, 'additional' => false],
                        'edition' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'build' => ['reportable' => true, 'required' => false, 'additional' => false],
                        'component' => ['reportable' => true, 'required' => false, 'additional' => true],
                        'category' => ['reportable' => true, 'required' => false, 'additional' => false],
                        'reproducability' => ['reportable' => true, 'required' => false, 'additional' => false],
                        'resolution' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'milestone' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'estimated_time' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'spent_time' => ['reportable' => false, 'required' => false, 'additional' => false],
                    ],
                    $issue_type_feature_request_id => [
                        'description' => ['reportable' => true, 'required' => true, 'additional' => false],
                        'category' => ['reportable' => true, 'required' => false, 'additional' => true],
                        'milestone' => ['reportable' => true, 'required' => false, 'additional' => true],
                        'estimated_time' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'spent_time' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'percent_complete' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'priority' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'votes' => ['reportable' => false, 'required' => false, 'additional' => false],
                    ],
                    $issue_type_task_id => [
                        'description' => ['reportable' => true, 'required' => true, 'additional' => false],
                        'category' => ['reportable' => true, 'required' => false, 'additional' => true],
                        'estimated_time' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'spent_time' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'priority' => ['reportable' => false, 'required' => false, 'additional' => false],
                    ],
                    $issue_type_idea_id => [
                        'description' => ['reportable' => true, 'required' => true, 'additional' => false],
                        'category' => ['reportable' => true, 'required' => false, 'additional' => true],
                        'milestone' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'estimated_time' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'priority' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'votes' => ['reportable' => false, 'required' => false, 'additional' => false],
                    ],
                    $issue_type_user_story_id => [
                        'description' => ['reportable' => true, 'required' => true, 'additional' => false],
                        'category' => ['reportable' => true, 'required' => false, 'additional' => false],
                        'milestone' => ['reportable' => true, 'required' => false, 'additional' => true],
                        'estimated_time' => ['reportable' => true, 'required' => false, 'additional' => false],
                        'spent_time' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'percent_complete' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'priority' => ['reportable' => true, 'required' => false, 'additional' => true],
                    ],
                    $issue_type_epic_id => [
                        'shortname' => ['reportable' => true, 'required' => true, 'additional' => false],
                    ],
                ],
                $simple_scheme->getID() => [
                    $issue_type_bug_report_id => [
                        'description' => ['reportable' => true, 'required' => true, 'additional' => false],
                        'reproduction_steps' => ['reportable' => true, 'required' => false, 'additional' => false],
                        'build' => ['reportable' => true, 'required' => false, 'additional' => false],
                        'edition' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'component' => ['reportable' => true, 'required' => false, 'additional' => true],
                        'category' => ['reportable' => true, 'required' => false, 'additional' => false],
                        'reproducability' => ['reportable' => true, 'required' => false, 'additional' => false],
                        'resolution' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'milestone' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'estimated_time' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'spent_time' => ['reportable' => false, 'required' => false, 'additional' => false],
                    ],
                    $issue_type_feature_request_id => [
                        'description' => ['reportable' => true, 'required' => true, 'additional' => false],
                        'category' => ['reportable' => true, 'required' => false, 'additional' => true],
                        'milestone' => ['reportable' => true, 'required' => false, 'additional' => true],
                        'estimated_time' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'spent_time' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'percent_complete' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'priority' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'votes' => ['reportable' => false, 'required' => false, 'additional' => false],
                    ],
                    $issue_type_task_id => [
                        'description' => ['reportable' => true, 'required' => true, 'additional' => false],
                        'category' => ['reportable' => true, 'required' => false, 'additional' => true],
                        'estimated_time' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'spent_time' => ['reportable' => false, 'required' => false, 'additional' => false],
                        'priority' => ['reportable' => false, 'required' => false, 'additional' => false],
                    ],
                ],
            ];

            foreach ($schemes as $scheme_id => $issuetypes) {
                foreach ($issuetypes as $issuetype_id => $fields) {
                    foreach ($fields as $field => $settings) {
                        $insertion = new Insertion();
                        $insertion->add(self::ISSUETYPE_SCHEME_ID, $scheme_id);
                        $insertion->add(self::ISSUETYPE_ID, $issuetype_id);
                        $insertion->add(self::FIELD_KEY, $field);
                        $insertion->add(self::REPORTABLE, $settings['reportable']);
                        $insertion->add(self::REQUIRED, $settings['required']);
                        $insertion->add(self::ADDITIONAL, $settings['additional']);
                        $insertion->add(self::SCOPE, $scope);
                        $this->rawInsert($insertion);
                    }
                }
            }

        }

    }
