<?php

    namespace thebuggenie\core\entities\tables;

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

        protected function _initialize()
        {
            parent::_setup(self::B2DBNAME, self::ID);
            parent::_addVarchar(self::FIELD_KEY, 100);
            parent::_addBoolean(self::REQUIRED);
            parent::_addBoolean(self::REPORTABLE);
            parent::_addBoolean(self::ADDITIONAL);
            parent::_addForeignKeyColumn(self::ISSUETYPE_ID, IssueTypes::getTable(), IssueTypes::ID);
            parent::_addForeignKeyColumn(self::ISSUETYPE_SCHEME_ID, IssuetypeSchemes::getTable(), IssuetypeSchemes::ID);
        }

        protected function _setupIndexes()
        {
            $this->_addIndex('scope_issuetypescheme_issuetype', array(self::SCOPE, self::ISSUETYPE_SCHEME_ID, self::ISSUETYPE_ID));
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
            $crit = $this->getCriteria();
            $crit->addWhere(self::ISSUETYPE_SCHEME_ID, $scheme_id);
            $crit->addWhere(self::ISSUETYPE_ID, $issuetype_id);
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            $res = $this->doDelete($crit);
        }

        public function copyBySchemeIDs($from_scheme_id, $to_scheme_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            $crit->addWhere(self::ISSUETYPE_SCHEME_ID, $from_scheme_id);
            if ($res = $this->doSelect($crit))
            {
                while ($row = $res->getNextRow())
                {
                    $crit2 = $this->getCriteria();
                    $crit2->addInsert(self::SCOPE, framework\Context::getScope()->getID());
                    $crit2->addInsert(self::ISSUETYPE_SCHEME_ID, $to_scheme_id);
                    $crit2->addInsert(self::FIELD_KEY, $row->get(self::FIELD_KEY));
                    $crit2->addInsert(self::ADDITIONAL, $row->get(self::ADDITIONAL));
                    $crit2->addInsert(self::ISSUETYPE_ID, $row->get(self::ISSUETYPE_ID));
                    $crit2->addInsert(self::REPORTABLE, $row->get(self::REPORTABLE));
                    $crit2->addInsert(self::REQUIRED, $row->get(self::REQUIRED));
                    $this->doInsert($crit2);
                }
            }
        }

        public function addFieldAndDetailsBySchemeIDandIssuetypeID($scheme_id, $issuetype_id, $key, $details)
        {
            $crit = $this->getCriteria();
            $crit->addInsert(self::ISSUETYPE_SCHEME_ID, $scheme_id);
            $crit->addInsert(self::ISSUETYPE_ID, $issuetype_id);
            $crit->addInsert(self::FIELD_KEY, $key);
            if (array_key_exists('reportable', $details))
            {
                $crit->addInsert(self::REPORTABLE, true);
            }
            if (array_key_exists('additional', $details))
            {
                $crit->addInsert(self::ADDITIONAL, true);
            }
            if (array_key_exists('required', $details))
            {
                $crit->addInsert(self::REQUIRED, true);
            }

            $crit->addInsert(self::SCOPE, framework\Context::getScope()->getID());
            $this->doInsert($crit);
        }

        public function getBySchemeIDandIssuetypeID($scheme_id, $issuetype_id)
        {
            $crit = $this->getCriteria();
            $crit->addJoin(CustomFields::getTable(), CustomFields::FIELD_KEY, self::FIELD_KEY);
            $crit->addWhere(self::ISSUETYPE_SCHEME_ID, $scheme_id);
            $crit->addWhere(self::ISSUETYPE_ID, $issuetype_id);
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            $res = $this->doSelect($crit, false);
            return $res;
        }

        public function deleteByIssuetypeSchemeID($scheme_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::ISSUETYPE_SCHEME_ID, $scheme_id);
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            $res = $this->doDelete($crit);
        }

        public function deleteByIssueFieldKey($key)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::FIELD_KEY, $key);
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            $res = $this->doDelete($crit);
        }

        public function loadFixtures(\thebuggenie\core\entities\Scope $scope, \thebuggenie\core\entities\IssuetypeScheme $scheme, $issue_type_bug_report_id, $issue_type_feature_request_id, $issue_type_enhancement_id, $issue_type_task_id, $issue_type_user_story_id, $issue_type_idea_id, $issue_type_epic_id)
        {
            $scope = $scope->getID();
            $scheme = $scheme->getID();

            $crit = $this->getCriteria();
            $crit->addInsert(self::ISSUETYPE_SCHEME_ID, $scheme);
            $crit->addInsert(self::ISSUETYPE_ID, $issue_type_bug_report_id);
            $crit->addInsert(self::FIELD_KEY, 'description');
            $crit->addInsert(self::REPORTABLE, true);
            $crit->addInsert(self::REQUIRED, true);
            $crit->addInsert(self::SCOPE, $scope);
            $this->doInsert($crit);

            $crit = $this->getCriteria();
            $crit->addInsert(self::ISSUETYPE_SCHEME_ID, $scheme);
            $crit->addInsert(self::ISSUETYPE_ID, $issue_type_bug_report_id);
            $crit->addInsert(self::FIELD_KEY, 'reproduction_steps');
            $crit->addInsert(self::REPORTABLE, true);
            $crit->addInsert(self::REQUIRED, true);
            $crit->addInsert(self::SCOPE, $scope);
            $this->doInsert($crit);

            $crit = $this->getCriteria();
            $crit->addInsert(self::ISSUETYPE_SCHEME_ID, $scheme);
            $crit->addInsert(self::ISSUETYPE_ID, $issue_type_bug_report_id);
            $crit->addInsert(self::FIELD_KEY, 'edition');
            $crit->addInsert(self::REPORTABLE, true);
            $crit->addInsert(self::REQUIRED, false);
            $crit->addInsert(self::SCOPE, $scope);
            $this->doInsert($crit);

            $crit = $this->getCriteria();
            $crit->addInsert(self::ISSUETYPE_SCHEME_ID, $scheme);
            $crit->addInsert(self::ISSUETYPE_ID, $issue_type_bug_report_id);
            $crit->addInsert(self::FIELD_KEY, 'build');
            $crit->addInsert(self::REPORTABLE, true);
            $crit->addInsert(self::REQUIRED, false);
            $crit->addInsert(self::SCOPE, $scope);
            $this->doInsert($crit);

            $crit = $this->getCriteria();
            $crit->addInsert(self::ISSUETYPE_SCHEME_ID, $scheme);
            $crit->addInsert(self::ISSUETYPE_ID, $issue_type_bug_report_id);
            $crit->addInsert(self::FIELD_KEY, 'component');
            $crit->addInsert(self::REPORTABLE, true);
            $crit->addInsert(self::SCOPE, $scope);
            $this->doInsert($crit);

            $crit = $this->getCriteria();
            $crit->addInsert(self::ISSUETYPE_SCHEME_ID, $scheme);
            $crit->addInsert(self::ISSUETYPE_ID, $issue_type_bug_report_id);
            $crit->addInsert(self::FIELD_KEY, 'category');
            $crit->addInsert(self::REPORTABLE, true);
            $crit->addInsert(self::ADDITIONAL, true);
            $crit->addInsert(self::SCOPE, $scope);
            $this->doInsert($crit);

            $crit = $this->getCriteria();
            $crit->addInsert(self::ISSUETYPE_SCHEME_ID, $scheme);
            $crit->addInsert(self::ISSUETYPE_ID, $issue_type_bug_report_id);
            $crit->addInsert(self::FIELD_KEY, 'reproducability');
            $crit->addInsert(self::REPORTABLE, true);
            $crit->addInsert(self::SCOPE, $scope);
            $this->doInsert($crit);

            $crit = $this->getCriteria();
            $crit->addInsert(self::ISSUETYPE_SCHEME_ID, $scheme);
            $crit->addInsert(self::ISSUETYPE_ID, $issue_type_bug_report_id);
            $crit->addInsert(self::FIELD_KEY, 'resolution');
            $crit->addInsert(self::SCOPE, $scope);
            $this->doInsert($crit);

            $crit = $this->getCriteria();
            $crit->addInsert(self::ISSUETYPE_SCHEME_ID, $scheme);
            $crit->addInsert(self::ISSUETYPE_ID, $issue_type_bug_report_id);
            $crit->addInsert(self::FIELD_KEY, 'milestone');
            $crit->addInsert(self::SCOPE, $scope);
            $this->doInsert($crit);

            $crit = $this->getCriteria();
            $crit->addInsert(self::ISSUETYPE_SCHEME_ID, $scheme);
            $crit->addInsert(self::ISSUETYPE_ID, $issue_type_bug_report_id);
            $crit->addInsert(self::FIELD_KEY, 'estimated_time');
            $crit->addInsert(self::SCOPE, $scope);
            $this->doInsert($crit);

            $crit = $this->getCriteria();
            $crit->addInsert(self::ISSUETYPE_SCHEME_ID, $scheme);
            $crit->addInsert(self::ISSUETYPE_ID, $issue_type_bug_report_id);
            $crit->addInsert(self::FIELD_KEY, 'spent_time');
            $crit->addInsert(self::SCOPE, $scope);
            $this->doInsert($crit);

            $crit = $this->getCriteria();
            $crit->addInsert(self::ISSUETYPE_SCHEME_ID, $scheme);
            $crit->addInsert(self::ISSUETYPE_ID, $issue_type_bug_report_id);
            $crit->addInsert(self::FIELD_KEY, 'priority');
            $crit->addInsert(self::SCOPE, $scope);
            $this->doInsert($crit);

            $crit = $this->getCriteria();
            $crit->addInsert(self::ISSUETYPE_SCHEME_ID, $scheme);
            $crit->addInsert(self::ISSUETYPE_ID, $issue_type_feature_request_id);
            $crit->addInsert(self::FIELD_KEY, 'description');
            $crit->addInsert(self::REPORTABLE, true);
            $crit->addInsert(self::REQUIRED, true);
            $crit->addInsert(self::SCOPE, $scope);
            $this->doInsert($crit);

            $crit = $this->getCriteria();
            $crit->addInsert(self::ISSUETYPE_SCHEME_ID, $scheme);
            $crit->addInsert(self::ISSUETYPE_ID, $issue_type_feature_request_id);
            $crit->addInsert(self::FIELD_KEY, 'milestone');
            $crit->addInsert(self::SCOPE, $scope);
            $this->doInsert($crit);

            $crit = $this->getCriteria();
            $crit->addInsert(self::ISSUETYPE_SCHEME_ID, $scheme);
            $crit->addInsert(self::ISSUETYPE_ID, $issue_type_feature_request_id);
            $crit->addInsert(self::FIELD_KEY, 'category');
            $crit->addInsert(self::REPORTABLE, true);
            $crit->addInsert(self::ADDITIONAL, true);
            $crit->addInsert(self::SCOPE, $scope);
            $this->doInsert($crit);

            $crit = $this->getCriteria();
            $crit->addInsert(self::ISSUETYPE_SCHEME_ID, $scheme);
            $crit->addInsert(self::ISSUETYPE_ID, $issue_type_feature_request_id);
            $crit->addInsert(self::FIELD_KEY, 'estimated_time');
            $crit->addInsert(self::SCOPE, $scope);
            $this->doInsert($crit);

            $crit = $this->getCriteria();
            $crit->addInsert(self::ISSUETYPE_SCHEME_ID, $scheme);
            $crit->addInsert(self::ISSUETYPE_ID, $issue_type_feature_request_id);
            $crit->addInsert(self::FIELD_KEY, 'spent_time');
            $crit->addInsert(self::SCOPE, $scope);
            $this->doInsert($crit);

            $crit = $this->getCriteria();
            $crit->addInsert(self::ISSUETYPE_SCHEME_ID, $scheme);
            $crit->addInsert(self::ISSUETYPE_ID, $issue_type_feature_request_id);
            $crit->addInsert(self::FIELD_KEY, 'percent_complete');
            $crit->addInsert(self::SCOPE, $scope);
            $this->doInsert($crit);

            $crit = $this->getCriteria();
            $crit->addInsert(self::ISSUETYPE_SCHEME_ID, $scheme);
            $crit->addInsert(self::ISSUETYPE_ID, $issue_type_feature_request_id);
            $crit->addInsert(self::FIELD_KEY, 'priority');
            $crit->addInsert(self::SCOPE, $scope);
            $this->doInsert($crit);

            $crit = $this->getCriteria();
            $crit->addInsert(self::ISSUETYPE_SCHEME_ID, $scheme);
            $crit->addInsert(self::ISSUETYPE_ID, $issue_type_feature_request_id);
            $crit->addInsert(self::FIELD_KEY, 'votes');
            $crit->addInsert(self::SCOPE, $scope);
            $this->doInsert($crit);

            $crit = $this->getCriteria();
            $crit->addInsert(self::ISSUETYPE_SCHEME_ID, $scheme);
            $crit->addInsert(self::ISSUETYPE_ID, $issue_type_enhancement_id);
            $crit->addInsert(self::FIELD_KEY, 'description');
            $crit->addInsert(self::REPORTABLE, true);
            $crit->addInsert(self::REQUIRED, true);
            $crit->addInsert(self::SCOPE, $scope);
            $this->doInsert($crit);

            $crit = $this->getCriteria();
            $crit->addInsert(self::ISSUETYPE_SCHEME_ID, $scheme);
            $crit->addInsert(self::ISSUETYPE_ID, $issue_type_enhancement_id);
            $crit->addInsert(self::FIELD_KEY, 'milestone');
            $crit->addInsert(self::SCOPE, $scope);
            $this->doInsert($crit);

            $crit = $this->getCriteria();
            $crit->addInsert(self::ISSUETYPE_SCHEME_ID, $scheme);
            $crit->addInsert(self::ISSUETYPE_ID, $issue_type_enhancement_id);
            $crit->addInsert(self::FIELD_KEY, 'category');
            $crit->addInsert(self::REPORTABLE, true);
            $crit->addInsert(self::ADDITIONAL, true);
            $crit->addInsert(self::SCOPE, $scope);
            $this->doInsert($crit);

            $crit = $this->getCriteria();
            $crit->addInsert(self::ISSUETYPE_SCHEME_ID, $scheme);
            $crit->addInsert(self::ISSUETYPE_ID, $issue_type_enhancement_id);
            $crit->addInsert(self::FIELD_KEY, 'estimated_time');
            $crit->addInsert(self::SCOPE, $scope);
            $this->doInsert($crit);

            $crit = $this->getCriteria();
            $crit->addInsert(self::ISSUETYPE_SCHEME_ID, $scheme);
            $crit->addInsert(self::ISSUETYPE_ID, $issue_type_enhancement_id);
            $crit->addInsert(self::FIELD_KEY, 'spent_time');
            $crit->addInsert(self::SCOPE, $scope);
            $this->doInsert($crit);

            $crit = $this->getCriteria();
            $crit->addInsert(self::ISSUETYPE_SCHEME_ID, $scheme);
            $crit->addInsert(self::ISSUETYPE_ID, $issue_type_enhancement_id);
            $crit->addInsert(self::FIELD_KEY, 'percent_complete');
            $crit->addInsert(self::SCOPE, $scope);
            $this->doInsert($crit);

            $crit = $this->getCriteria();
            $crit->addInsert(self::ISSUETYPE_SCHEME_ID, $scheme);
            $crit->addInsert(self::ISSUETYPE_ID, $issue_type_enhancement_id);
            $crit->addInsert(self::FIELD_KEY, 'priority');
            $crit->addInsert(self::SCOPE, $scope);
            $this->doInsert($crit);

            $crit = $this->getCriteria();
            $crit->addInsert(self::ISSUETYPE_SCHEME_ID, $scheme);
            $crit->addInsert(self::ISSUETYPE_ID, $issue_type_task_id);
            $crit->addInsert(self::FIELD_KEY, 'description');
            $crit->addInsert(self::REPORTABLE, true);
            $crit->addInsert(self::SCOPE, $scope);
            $this->doInsert($crit);

            $crit = $this->getCriteria();
            $crit->addInsert(self::ISSUETYPE_SCHEME_ID, $scheme);
            $crit->addInsert(self::ISSUETYPE_ID, $issue_type_task_id);
            $crit->addInsert(self::FIELD_KEY, 'category');
            $crit->addInsert(self::REPORTABLE, true);
            $crit->addInsert(self::ADDITIONAL, true);
            $crit->addInsert(self::SCOPE, $scope);
            $this->doInsert($crit);

            $crit = $this->getCriteria();
            $crit->addInsert(self::ISSUETYPE_SCHEME_ID, $scheme);
            $crit->addInsert(self::ISSUETYPE_ID, $issue_type_task_id);
            $crit->addInsert(self::FIELD_KEY, 'estimated_time');
            $crit->addInsert(self::REPORTABLE, true);
            $crit->addInsert(self::ADDITIONAL, true);
            $crit->addInsert(self::SCOPE, $scope);
            $this->doInsert($crit);

            $crit = $this->getCriteria();
            $crit->addInsert(self::ISSUETYPE_SCHEME_ID, $scheme);
            $crit->addInsert(self::ISSUETYPE_ID, $issue_type_task_id);
            $crit->addInsert(self::FIELD_KEY, 'spent_time');
            $crit->addInsert(self::SCOPE, $scope);
            $this->doInsert($crit);

            $crit = $this->getCriteria();
            $crit->addInsert(self::ISSUETYPE_SCHEME_ID, $scheme);
            $crit->addInsert(self::ISSUETYPE_ID, $issue_type_task_id);
            $crit->addInsert(self::FIELD_KEY, 'percent_complete');
            $crit->addInsert(self::SCOPE, $scope);
            $this->doInsert($crit);

            $crit = $this->getCriteria();
            $crit->addInsert(self::ISSUETYPE_SCHEME_ID, $scheme);
            $crit->addInsert(self::ISSUETYPE_ID, $issue_type_task_id);
            $crit->addInsert(self::FIELD_KEY, 'priority');
            $crit->addInsert(self::REPORTABLE, true);
            $crit->addInsert(self::ADDITIONAL, true);
            $crit->addInsert(self::SCOPE, $scope);
            $this->doInsert($crit);

            $crit = $this->getCriteria();
            $crit->addInsert(self::ISSUETYPE_SCHEME_ID, $scheme);
            $crit->addInsert(self::ISSUETYPE_ID, $issue_type_task_id);
            $crit->addInsert(self::FIELD_KEY, 'milestone');
            $crit->addInsert(self::REPORTABLE, true);
            $crit->addInsert(self::ADDITIONAL, true);
            $crit->addInsert(self::SCOPE, $scope);
            $this->doInsert($crit);

            $crit = $this->getCriteria();
            $crit->addInsert(self::ISSUETYPE_SCHEME_ID, $scheme);
            $crit->addInsert(self::ISSUETYPE_ID, $issue_type_user_story_id);
            $crit->addInsert(self::FIELD_KEY, 'description');
            $crit->addInsert(self::REPORTABLE, true);
            $crit->addInsert(self::SCOPE, $scope);
            $this->doInsert($crit);

            $crit = $this->getCriteria();
            $crit->addInsert(self::ISSUETYPE_SCHEME_ID, $scheme);
            $crit->addInsert(self::ISSUETYPE_ID, $issue_type_user_story_id);
            $crit->addInsert(self::FIELD_KEY, 'percent_complete');
            $crit->addInsert(self::SCOPE, $scope);
            $this->doInsert($crit);

            $crit = $this->getCriteria();
            $crit->addInsert(self::ISSUETYPE_SCHEME_ID, $scheme);
            $crit->addInsert(self::ISSUETYPE_ID, $issue_type_user_story_id);
            $crit->addInsert(self::FIELD_KEY, 'category');
            $crit->addInsert(self::REPORTABLE, true);
            $crit->addInsert(self::SCOPE, $scope);
            $this->doInsert($crit);

            $crit = $this->getCriteria();
            $crit->addInsert(self::ISSUETYPE_SCHEME_ID, $scheme);
            $crit->addInsert(self::ISSUETYPE_ID, $issue_type_user_story_id);
            $crit->addInsert(self::FIELD_KEY, 'milestone');
            $crit->addInsert(self::REPORTABLE, true);
            $crit->addInsert(self::ADDITIONAL, true);
            $crit->addInsert(self::SCOPE, $scope);
            $this->doInsert($crit);

            $crit = $this->getCriteria();
            $crit->addInsert(self::ISSUETYPE_SCHEME_ID, $scheme);
            $crit->addInsert(self::ISSUETYPE_ID, $issue_type_user_story_id);
            $crit->addInsert(self::FIELD_KEY, 'estimated_time');
            $crit->addInsert(self::REPORTABLE, true);
            $crit->addInsert(self::SCOPE, $scope);
            $this->doInsert($crit);

            $crit = $this->getCriteria();
            $crit->addInsert(self::ISSUETYPE_SCHEME_ID, $scheme);
            $crit->addInsert(self::ISSUETYPE_ID, $issue_type_user_story_id);
            $crit->addInsert(self::FIELD_KEY, 'spent_time');
            $crit->addInsert(self::SCOPE, $scope);
            $this->doInsert($crit);

            $crit = $this->getCriteria();
            $crit->addInsert(self::ISSUETYPE_SCHEME_ID, $scheme);
            $crit->addInsert(self::ISSUETYPE_ID, $issue_type_user_story_id);
            $crit->addInsert(self::FIELD_KEY, 'priority');
            $crit->addInsert(self::REPORTABLE, true);
            $crit->addInsert(self::ADDITIONAL, true);
            $crit->addInsert(self::SCOPE, $scope);
            $this->doInsert($crit);

            $crit = $this->getCriteria();
            $crit->addInsert(self::ISSUETYPE_SCHEME_ID, $scheme);
            $crit->addInsert(self::ISSUETYPE_ID, $issue_type_idea_id);
            $crit->addInsert(self::FIELD_KEY, 'description');
            $crit->addInsert(self::REPORTABLE, true);
            $crit->addInsert(self::REQUIRED, true);
            $crit->addInsert(self::SCOPE, $scope);
            $this->doInsert($crit);

            $crit = $this->getCriteria();
            $crit->addInsert(self::ISSUETYPE_SCHEME_ID, $scheme);
            $crit->addInsert(self::ISSUETYPE_ID, $issue_type_idea_id);
            $crit->addInsert(self::FIELD_KEY, 'milestone');
            $crit->addInsert(self::REPORTABLE, true);
            $crit->addInsert(self::ADDITIONAL, true);
            $crit->addInsert(self::SCOPE, $scope);
            $this->doInsert($crit);

            $crit = $this->getCriteria();
            $crit->addInsert(self::ISSUETYPE_SCHEME_ID, $scheme);
            $crit->addInsert(self::ISSUETYPE_ID, $issue_type_idea_id);
            $crit->addInsert(self::FIELD_KEY, 'category');
            $crit->addInsert(self::REPORTABLE, true);
            $crit->addInsert(self::ADDITIONAL, true);
            $crit->addInsert(self::SCOPE, $scope);
            $this->doInsert($crit);

            $crit = $this->getCriteria();
            $crit->addInsert(self::ISSUETYPE_SCHEME_ID, $scheme);
            $crit->addInsert(self::ISSUETYPE_ID, $issue_type_idea_id);
            $crit->addInsert(self::FIELD_KEY, 'estimated_time');
            $crit->addInsert(self::REPORTABLE, true);
            $crit->addInsert(self::ADDITIONAL, true);
            $crit->addInsert(self::SCOPE, $scope);
            $this->doInsert($crit);

            $crit = $this->getCriteria();
            $crit->addInsert(self::ISSUETYPE_SCHEME_ID, $scheme);
            $crit->addInsert(self::ISSUETYPE_ID, $issue_type_idea_id);
            $crit->addInsert(self::FIELD_KEY, 'spent_time');
            $crit->addInsert(self::SCOPE, $scope);
            $this->doInsert($crit);

            $crit = $this->getCriteria();
            $crit->addInsert(self::ISSUETYPE_SCHEME_ID, $scheme);
            $crit->addInsert(self::ISSUETYPE_ID, $issue_type_idea_id);
            $crit->addInsert(self::FIELD_KEY, 'percent_complete');
            $crit->addInsert(self::SCOPE, $scope);
            $this->doInsert($crit);

            $crit = $this->getCriteria();
            $crit->addInsert(self::ISSUETYPE_SCHEME_ID, $scheme);
            $crit->addInsert(self::ISSUETYPE_ID, $issue_type_idea_id);
            $crit->addInsert(self::FIELD_KEY, 'priority');
            $crit->addInsert(self::SCOPE, $scope);
            $this->doInsert($crit);

            $crit = $this->getCriteria();
            $crit->addInsert(self::ISSUETYPE_SCHEME_ID, $scheme);
            $crit->addInsert(self::ISSUETYPE_ID, $issue_type_epic_id);
            $crit->addInsert(self::FIELD_KEY, 'shortname');
            $crit->addInsert(self::REPORTABLE, true);
            $crit->addInsert(self::SCOPE, $scope);
            $this->doInsert($crit);

        }

    }
