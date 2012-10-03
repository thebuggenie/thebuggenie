<?php

	/**
	 * Projects table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Projects table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 *
	 * @Table(name="projects")
	 */
	class TBGProjectsTable3dot1 extends TBGB2DBTable
	{

		const B2DB_TABLE_VERSION = 1;
		const B2DBNAME = 'projects';
		const ID = 'projects.id';
		const SCOPE = 'projects.scope';
		const NAME = 'projects.name';
		const KEY = 'projects.key';
		const PREFIX = 'projects.prefix';
		const USE_PREFIX = 'projects.use_prefix';
		const USE_SCRUM = 'projects.use_scrum';
		const HOMEPAGE = 'projects.homepage';
		const OWNER = 'projects.owner';
		const OWNER_TYPE = 'projects.owner_type';
		const LEAD_BY = 'projects.leader';
		const LEAD_TYPE = 'projects.leader_type';
		const CLIENT = 'projects.client';
		const DESCRIPTION = 'projects.description';
		const DOC_URL = 'projects.doc_url';
		const WIKI_URL = 'projects.wiki_url';
		const RELEASED = 'projects.isreleased';
		const PLANNED_RELEASED = 'projects.isplannedreleased';
		const RELEASE_DATE = 'projects.release_date';
		const ENABLE_BUILDS = 'projects.enable_builds';
		const ENABLE_EDITIONS = 'projects.enable_editions';
		const ENABLE_COMPONENTS = 'projects.enable_components';
		const SHOW_IN_SUMMARY = 'projects.show_in_summary';
		const SUMMARY_DISPLAY = 'projects.summary_display';
		const QA = 'projects.qa_responsible';
		const QA_TYPE = 'projects.qa_responsible_type';
		const LOCKED = 'projects.locked';
		const DELETED = 'projects.deleted';
		const ALLOW_CHANGING_WITHOUT_WORKING = 'projects.allow_freelancing';
		const WORKFLOW_SCHEME_ID = 'projects.workflow_scheme_id';
		const ISSUETYPE_SCHEME_ID = 'projects.issuetype_scheme_id';
		const AUTOASSIGN = 'projects.autoassign';
		
		protected function _initialize()
		{
			parent::_setup(self::B2DBNAME, self::ID);
			parent::_addVarchar(self::NAME, 100);
			parent::_addVarchar(self::KEY, 100);
			parent::_addVarchar(self::PREFIX, 5, '');
			parent::_addBoolean(self::USE_PREFIX);
			parent::_addBoolean(self::USE_SCRUM);
			parent::_addVarchar(self::HOMEPAGE, 200, '');
			parent::_addInteger(self::OWNER, 10);
			parent::_addInteger(self::OWNER_TYPE, 3);
			parent::_addInteger(self::LEAD_BY, 10);
			parent::_addInteger(self::LEAD_TYPE, 3);
			parent::_addText(self::DESCRIPTION, false);
			parent::_addVarchar(self::DOC_URL, 200, '');
			parent::_addBoolean(self::ALLOW_CHANGING_WITHOUT_WORKING);
			parent::_addBoolean(self::RELEASED);
			parent::_addBoolean(self::PLANNED_RELEASED);
			parent::_addInteger(self::RELEASE_DATE, 10);
			parent::_addBoolean(self::ENABLE_BUILDS);
			parent::_addBoolean(self::ENABLE_EDITIONS);
			parent::_addBoolean(self::ENABLE_COMPONENTS);
			parent::_addBoolean(self::SHOW_IN_SUMMARY, true);
			parent::_addVarchar(self::SUMMARY_DISPLAY, 15, 'issuetypes');
			parent::_addInteger(self::QA, 10);
			parent::_addInteger(self::QA_TYPE, 3);
			parent::_addBoolean(self::LOCKED);
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
			parent::_addForeignKeyColumn(self::WORKFLOW_SCHEME_ID, TBGWorkflowSchemesTable::getTable(), TBGWorkflowSchemesTable::ID);
			parent::_addForeignKeyColumn(self::ISSUETYPE_SCHEME_ID, TBGIssuetypeSchemesTable::getTable(), TBGIssuetypeSchemesTable::ID);
			parent::_addForeignKeyColumn(self::CLIENT, TBGClientsTable::getTable(), TBGClientsTable::ID);
			parent::_addBoolean(self::DELETED);
			parent::_addBoolean(self::AUTOASSIGN);
		}

	}
