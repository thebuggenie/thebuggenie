<?php

	use b2db\Core,
		b2db\Criteria,
		b2db\Criterion;

	/**
	 * Visible issue types table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Visible issue types table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 *
	 * @Table(name="visible_issue_types")
	 */
	class TBGVisibleIssueTypesTable extends TBGB2DBTable 
	{

		const B2DB_TABLE_VERSION = 1;
		const B2DBNAME = 'visible_issue_types';
		const ID = 'visible_issue_types.id';
		const SCOPE = 'visible_issue_types.scope';
		const PROJECT_ID = 'visible_issue_types.project_id';
		const ISSUETYPE_ID = 'visible_issue_types.issuetype_id';
		
		protected function _initialize()
		{
			parent::_setup(self::B2DBNAME, self::ID);
			parent::_addForeignKeyColumn(self::ISSUETYPE_ID, TBGIssueTypesTable::getTable(), TBGIssueTypesTable::ID);
			parent::_addForeignKeyColumn(self::PROJECT_ID, TBGProjectsTable::getTable(), TBGProjectsTable::ID);
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
		}
		
		public function getAllByProjectID($project_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::PROJECT_ID, $project_id);
			$crit->addOrderBy(TBGIssueTypesTable::NAME, Criteria::SORT_ASC);
			$res = $this->doSelect($crit);
			return $res;
		}
		
		public function clearByProjectID($project_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::PROJECT_ID, $project_id);
			$this->doDelete($crit);
			return true;
		}
		
		public function addByProjectIDAndIssuetypeID($project_id, $issuetype_id)
		{
			$crit = $this->getCriteria();
			$crit->addInsert(self::PROJECT_ID, $project_id);
			$crit->addInsert(self::ISSUETYPE_ID, $issuetype_id);
			$crit->addInsert(self::SCOPE, TBGContext::getScope()->getID());
			$res = $this->doInsert($crit);
			return true;
		}
		
		public function deleteByIssuetypeID($issuetype_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::ISSUETYPE_ID, $issuetype_id);
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$this->doDelete($crit);
			return true;
		}
		
	}
