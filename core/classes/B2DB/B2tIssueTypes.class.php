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
	class B2tIssueTypes extends B2DBTable 
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

		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addVarchar(self::NAME, 50);
			parent::_addInteger(self::APPLIES_TO, 10);
			parent::_addInteger(self::APPLIES_TYPE, 3);
			parent::_addVarchar(self::ICON, 20, 'bug_report');
			parent::_addText(self::DESCRIPTION, false);
			parent::_addBoolean(self::IS_TASK);
			parent::_addBoolean(self::IS_REPORTABLE, true);
			parent::_addBoolean(self::REDIRECT_AFTER_REPORTING, true);
			parent::_addForeignKeyColumn(self::SCOPE, B2DB::getTable('B2tScopes'), B2tScopes::ID);
		}

		public function loadFixtures($scope)
		{
			$i18n = BUGScontext::getI18n();

			$crit = $this->getCriteria();
			$crit->addInsert(self::NAME, $i18n->__('Bug report'));
			$crit->addInsert(self::SCOPE, $scope);
			$crit->addInsert(self::ICON, 'bug_report');
			$crit->addInsert(self::DESCRIPTION, $i18n->__('Have you discovered a bug in the application, or is something not working as expected?'));
			$res = $this->doInsert($crit);
			$issue_type_bug_report_id = $res->getInsertID();
			BUGSsettings::saveSetting('defaultissuetypefornewissues', $issue_type_bug_report_id, 'core', $scope);
			BUGSsettings::saveSetting('issuetype_bug_report', $issue_type_bug_report_id, 'core', $scope);

			$crit = $this->getCriteria();
			$crit->addInsert(self::NAME, $i18n->__('Feature request'));
			$crit->addInsert(self::ICON, 'feature_request');
			$crit->addInsert(self::DESCRIPTION, $i18n->__('Are you missing some specific feature, or is your favourite part of the application a bit lacking?'));
			$crit->addInsert(self::SCOPE, $scope);
			$res = $this->doInsert($crit);
			$issue_type_feature_request_id = $res->getInsertID();
			BUGSsettings::saveSetting('issuetype_feature_request', $issue_type_feature_request_id, 'core', $scope);

			$crit = $this->getCriteria();
			$crit->addInsert(self::NAME, $i18n->__('Enhancement'));
			$crit->addInsert(self::ICON, 'enhancement');
			$crit->addInsert(self::DESCRIPTION, $i18n->__('Have you found something that is working in a way that could be improved?'));
			$crit->addInsert(self::SCOPE, $scope);
			$res = $this->doInsert($crit);
			$issue_type_enhancement_id = $res->getInsertID();
			BUGSsettings::saveSetting('issuetype_enhancement', $issue_type_enhancement_id, 'core', $scope);

			$crit = $this->getCriteria();
			$crit->addInsert(self::NAME, $i18n->__('Task'));
			$crit->addInsert(self::ICON, 'task');
			$crit->addInsert(self::IS_TASK, true);
			$crit->addInsert(self::IS_REPORTABLE, false);
			$crit->addInsert(self::SCOPE, $scope);
			$res = $this->doInsert($crit);
			$issue_type_task_id = $res->getInsertID();
			BUGSsettings::saveSetting('issuetype_task', $issue_type_task_id, 'core', $scope);

			$crit = $this->getCriteria();
			$crit->addInsert(self::NAME, $i18n->__('User story'));
			$crit->addInsert(self::ICON, 'developer_report');
			$crit->addInsert(self::DESCRIPTION, $i18n->__('Doing it Scrum-style. Issue type perfectly suited for entering user stories'));
			$crit->addInsert(self::REDIRECT_AFTER_REPORTING, false);
			$crit->addInsert(self::SCOPE, $scope);
			$res = $this->doInsert($crit);
			$issue_type_user_story_id = $res->getInsertID();
			BUGSsettings::saveSetting('issuetype_user_story', $issue_type_user_story_id, 'core', $scope);

			$crit = $this->getCriteria();
			$crit->addInsert(self::NAME, $i18n->__('Idea'));
			$crit->addInsert(self::ICON, 'idea');
			$crit->addInsert(self::DESCRIPTION, $i18n->__('Express yourself - share your ideas with the rest of the team!'));
			$crit->addInsert(self::IS_REPORTABLE, false);
			$crit->addInsert(self::SCOPE, $scope);
			$res = $this->doInsert($crit);
			$issue_type_idea_id = $res->getInsertID();
			BUGSsettings::saveSetting('issuetype_idea', $issue_type_idea_id, 'core', $scope);

			B2DB::getTable('B2tIssueFields')->loadFixtures($scope, $issue_type_bug_report_id, $issue_type_feature_request_id, $issue_type_enhancement_id, $issue_type_task_id, $issue_type_user_story_id, $issue_type_idea_id);
		}
		
	}
