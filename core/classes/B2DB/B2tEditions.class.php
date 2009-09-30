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
	class B2tEditions extends B2DBTable 
	{

		const B2DBNAME = 'bugs2_editions';
		const ID = 'bugs2_editions.id';
		const SCOPE = 'bugs2_editions.scope';
		const NAME = 'bugs2_editions.name';
		const DESCRIPTION = 'bugs2_editions.description';
		const PROJECT = 'bugs2_editions.project';
		const LEAD_BY = 'bugs2_editions.lead_by';
		const LEAD_TYPE = 'bugs2_editions.lead_type';
		const DOC_URL = 'bugs2_editions.doc_url';
		const QA = 'bugs2_editions.qa';
		const QA_TYPE = 'bugs2_editions.qa_type';
		const IS_DEFAULT = 'bugs2_editions.is_default';
		const RELEASED = 'bugs2_editions.released';
		const PLANNED_RELEASED = 'bugs2_editions.planned_released';
		const RELEASE_DATE = 'bugs2_editions.release_date';
		const LOCKED = 'bugs2_editions.locked';
		
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
			parent::_addForeignKeyColumn(self::PROJECT, B2DB::getTable('B2tProjects'), B2tProjects::ID);
			parent::_addForeignKeyColumn(self::SCOPE, B2DB::getTable('B2tScopes'), B2tScopes::ID);
		}
		
		public function getByProjectID($project_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::PROJECT, $project_id);
			$res = $this->doSelect($crit);
			return $res;
		}
	}
