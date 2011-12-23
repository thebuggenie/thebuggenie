<?php

	/**
	 * Editions table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Editions table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 *
	 * @Table(name="editions")
	 */
	class TBGEditionsTable3dot1 extends TBGB2DBTable
	{

		const B2DB_TABLE_VERSION = 1;
		const B2DBNAME = 'editions';
		const ID = 'editions.id';
		const SCOPE = 'editions.scope';
		const NAME = 'editions.name';
		const DESCRIPTION = 'editions.description';
		const PROJECT = 'editions.project';
		const LEAD_BY = 'editions.leader';
		const LEAD_TYPE = 'editions.leader_type';
		const OWNED_BY = 'editions.owner';
		const OWNED_TYPE = 'editions.owner_type';
		const DOC_URL = 'editions.doc_url';
		const QA = 'editions.qa_responsible';
		const QA_TYPE = 'editions.qa_responsible_type';
		const RELEASED = 'editions.isreleased';
		const PLANNED_RELEASED = 'editions.isplannedreleased';
		const RELEASE_DATE = 'editions.release_date';
		const LOCKED = 'editions.locked';
		
		protected function _initialize()
		{
			parent::_setup(self::B2DBNAME, self::ID);
			parent::_addVarchar(self::NAME, 100);
			parent::_addText(self::DESCRIPTION, false);
			parent::_addInteger(self::LEAD_BY, 10);
			parent::_addInteger(self::LEAD_TYPE, 3);
			parent::_addInteger(self::OWNED_BY, 10);
			parent::_addInteger(self::OWNED_TYPE, 3);
			parent::_addVarchar(self::DOC_URL, 200, '');
			parent::_addInteger(self::QA, 10);
			parent::_addInteger(self::QA_TYPE, 3);
			parent::_addInteger(self::RELEASE_DATE, 10);
			parent::_addBoolean(self::RELEASED);
			parent::_addBoolean(self::PLANNED_RELEASED);
			parent::_addBoolean(self::LOCKED);
			parent::_addForeignKeyColumn(self::PROJECT, TBGProjectsTable::getTable(), TBGProjectsTable::ID);
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
		}

	}
