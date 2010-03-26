<?php

	/**
	 * Editions table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Editions table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 */
	class TBGEditionsTable extends B2DBTable 
	{

		const B2DBNAME = 'editions';
		const ID = 'editions.id';
		const SCOPE = 'editions.scope';
		const NAME = 'editions.name';
		const DESCRIPTION = 'editions.description';
		const PROJECT = 'editions.project';
		const LEAD_BY = 'editions.lead_by';
		const LEAD_TYPE = 'editions.lead_type';
		const DOC_URL = 'editions.doc_url';
		const QA = 'editions.qa';
		const QA_TYPE = 'editions.qa_type';
		const IS_DEFAULT = 'editions.is_default';
		const RELEASED = 'editions.released';
		const PLANNED_RELEASED = 'editions.planned_released';
		const RELEASE_DATE = 'editions.release_date';
		const LOCKED = 'editions.locked';
		
		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addVarchar(self::NAME, 100);
			parent::_addText(self::DESCRIPTION, false);
			parent::_addInteger(self::LEAD_BY, 10);
			parent::_addInteger(self::LEAD_TYPE, 3);
			parent::_addVarchar(self::DOC_URL, 200, '');
			parent::_addInteger(self::QA, 10);
			parent::_addInteger(self::QA_TYPE, 3);
			parent::_addBoolean(self::IS_DEFAULT);
			parent::_addInteger(self::RELEASE_DATE, 10);
			parent::_addBoolean(self::RELEASED);
			parent::_addBoolean(self::PLANNED_RELEASED);
			parent::_addBoolean(self::LOCKED);
			parent::_addForeignKeyColumn(self::PROJECT, B2DB::getTable('TBGProjectsTable'), TBGProjectsTable::ID);
			parent::_addForeignKeyColumn(self::SCOPE, B2DB::getTable('TBGScopesTable'), TBGScopesTable::ID);
		}
		
		public function getByProjectID($project_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::PROJECT, $project_id);
			$res = $this->doSelect($crit);
			return $res;
		}

		public function createNew($name, $p_id, $e_id = null)
		{
			$crit = $this->getCriteria();
			if ($e_id !== null)
			{
				$crit->addInsert(self::ID, $e_id);
			}
			$crit->addInsert(self::PROJECT, $p_id);
			$crit->addInsert(self::NAME, $name);
			$crit->addInsert(self::SCOPE, TBGContext::getScope()->getID());
			$res = $this->doInsert($crit);
			return $res->getInsertID();
		}

	}
