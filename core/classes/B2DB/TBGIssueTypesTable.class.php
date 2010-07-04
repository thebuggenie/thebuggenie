<?php

	/**
	 * Issue types table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Issue types table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 */
	class TBGIssueTypesTable extends B2DBTable 
	{

		const B2DBNAME = 'issuetypes';
		const ID = 'issuetypes.id';
		const SCOPE = 'issuetypes.scope';
		const NAME = 'issuetypes.name';
		const DESCRIPTION = 'issuetypes.description';
		const APPLIES_TO = 'issuetypes.applies_to';
		const APPLIES_TYPE = 'issuetypes.applies_type';
		const ICON = 'issuetypes.icon';
		const IS_TASK = 'issuetypes.is_task';
		const IS_REPORTABLE = 'issuetypes.is_reportable';
		const REDIRECT_AFTER_REPORTING = 'issuetypes.redirect_after_reporting';

		/**
		 * Return an instance of this table
		 *
		 * @return TBGIssuesTable
		 */
		public static function getTable()
		{
			return B2DB::getTable('TBGIssueTypesTable');
		}
		
		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addVarchar(self::NAME, 50);
			parent::_addInteger(self::APPLIES_TO, 10);
			parent::_addInteger(self::APPLIES_TYPE, 3);
			parent::_addVarchar(self::ICON, 30, 'bug_report');
			parent::_addText(self::DESCRIPTION, false);
			parent::_addBoolean(self::IS_TASK);
			parent::_addBoolean(self::IS_REPORTABLE, true);
			parent::_addBoolean(self::REDIRECT_AFTER_REPORTING, true);
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
		}

		public function createNew($name, $icon = 'bug_report')
		{
			$crit = $this->getCriteria();
			$crit->addInsert(self::NAME, $name);
			$crit->addInsert(self::SCOPE, TBGContext::getScope()->getID());
			$crit->addInsert(self::ICON, $icon);
			$crit->addInsert(self::DESCRIPTION, $name);
			$res = $this->doInsert($crit);

			return $res;
		}

		public function saveDetails(TBGIssuetype $issuetype)
		{
			$crit = $this->getCriteria();
			$crit->addUpdate(self::NAME, $issuetype->getName());
			$crit->addUpdate(self::ICON, $issuetype->getIcon());
			$crit->addUpdate(self::DESCRIPTION, $issuetype->getDescription());
			$crit->addUpdate(self::REDIRECT_AFTER_REPORTING, $issuetype->getRedirectAfterReporting());
			$crit->addUpdate(self::IS_REPORTABLE, $issuetype->isReportable());

			$res = $this->doUpdateById($crit, $issuetype->getID());
		}

		public function getBugReportTypeIDs()
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::ICON, 'bug_report');
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$res = $this->doSelect($crit);

			$retarr = array();
			if ($res)
			{
				while ($row = $res->getNextRow())
				{
					$retarr[] = $row->get(self::ID);
				}
			}

			return $retarr;
		}

		public function loadFixtures($scope)
		{
			$i18n = TBGContext::getI18n();

			$crit = $this->getCriteria();
			$crit->addInsert(self::NAME, 'Bug report');
			$crit->addInsert(self::SCOPE, $scope);
			$crit->addInsert(self::ICON, 'bug_report');
			$crit->addInsert(self::DESCRIPTION, 'Have you discovered a bug in the application, or is something not working as expected?');
			$res = $this->doInsert($crit);
			$issue_type_bug_report_id = $res->getInsertID();
			TBGSettings::saveSetting('defaultissuetypefornewissues', $issue_type_bug_report_id, 'core', $scope);
			TBGSettings::saveSetting('issuetype_bug_report', $issue_type_bug_report_id, 'core', $scope);

			$crit = $this->getCriteria();
			$crit->addInsert(self::NAME, 'Feature request');
			$crit->addInsert(self::ICON, 'feature_request');
			$crit->addInsert(self::DESCRIPTION, 'Are you missing some specific feature, or is your favourite part of the application a bit lacking?');
			$crit->addInsert(self::SCOPE, $scope);
			$res = $this->doInsert($crit);
			$issue_type_feature_request_id = $res->getInsertID();
			TBGSettings::saveSetting('issuetype_feature_request', $issue_type_feature_request_id, 'core', $scope);

			$crit = $this->getCriteria();
			$crit->addInsert(self::NAME, 'Enhancement');
			$crit->addInsert(self::ICON, 'enhancement');
			$crit->addInsert(self::DESCRIPTION, 'Have you found something that is working in a way that could be improved?');
			$crit->addInsert(self::SCOPE, $scope);
			$res = $this->doInsert($crit);
			$issue_type_enhancement_id = $res->getInsertID();
			TBGSettings::saveSetting('issuetype_enhancement', $issue_type_enhancement_id, 'core', $scope);

			$crit = $this->getCriteria();
			$crit->addInsert(self::NAME, 'Task');
			$crit->addInsert(self::ICON, 'task');
			$crit->addInsert(self::IS_TASK, true);
			$crit->addInsert(self::IS_REPORTABLE, false);
			$crit->addInsert(self::SCOPE, $scope);
			$res = $this->doInsert($crit);
			$issue_type_task_id = $res->getInsertID();
			TBGSettings::saveSetting('issuetype_task', $issue_type_task_id, 'core', $scope);

			$crit = $this->getCriteria();
			$crit->addInsert(self::NAME, 'User story');
			$crit->addInsert(self::ICON, 'developer_report');
			$crit->addInsert(self::DESCRIPTION, 'Doing it Scrum-style. Issue type perfectly suited for entering user stories');
			$crit->addInsert(self::REDIRECT_AFTER_REPORTING, false);
			$crit->addInsert(self::SCOPE, $scope);
			$res = $this->doInsert($crit);
			$issue_type_user_story_id = $res->getInsertID();
			TBGSettings::saveSetting('issuetype_user_story', $issue_type_user_story_id, 'core', $scope);

			$crit = $this->getCriteria();
			$crit->addInsert(self::NAME, 'Idea');
			$crit->addInsert(self::ICON, 'idea');
			$crit->addInsert(self::DESCRIPTION, 'Express yourself - share your ideas with the rest of the team!');
			$crit->addInsert(self::IS_REPORTABLE, false);
			$crit->addInsert(self::SCOPE, $scope);
			$res = $this->doInsert($crit);
			$issue_type_idea_id = $res->getInsertID();
			TBGSettings::saveSetting('issuetype_idea', $issue_type_idea_id, 'core', $scope);

			B2DB::getTable('TBGIssueFieldsTable')->loadFixtures($scope, $issue_type_bug_report_id, $issue_type_feature_request_id, $issue_type_enhancement_id, $issue_type_task_id, $issue_type_user_story_id, $issue_type_idea_id);
		}
		
	}
