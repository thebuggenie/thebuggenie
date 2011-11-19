<?php

	use b2db\Core,
		b2db\Criteria,
		b2db\Criterion;
	
	/**
	 * B2DB Table, vcs_integration -> VCSIntegrationCommitsTable
	 *
	 * @author Philip Kent <kentphilip@gmail.com>
	 * @version 3.2
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage vcs_integration
	 */

	/**
	 * B2DB Table, vcs_integration -> VCSIntegrationCommitsTable
	 *
	 * @package thebuggenie
	 * @subpackage vcs_integration
	 *
	 * @Table(name="vcsintegration_commits")
	 */
	class TBGVCSIntegrationCommitsTable extends TBGB2DBTable 
	{

		const B2DB_TABLE_VERSION = 1;
		const B2DBNAME = 'vcsintegration_commits';
		const ID = 'vcsintegration_commits.id';
		const SCOPE = 'vcsintegration_commits.scope';
		const LOG = 'vcsintegration_commits.log';
		const OLD_REV = 'vcsintegration_commits.old_rev';
		const NEW_REV = 'vcsintegration_commits.new_rev';
		const AUTHOR = 'vcsintegration_commits.author';
		const DATE = 'vcsintegration_commits.date';
		const DATA = 'vcsintegration_commits.data';
		const PROJECT_ID = 'vcsintegration_commits.project_id';
					
		public function _initialize()
		{
			parent::_setup(self::B2DBNAME, self::ID);
			parent::_addText(self::LOG, false);
			parent::_addVarchar(self::OLD_REV, 40);
			parent::_addVarchar(self::NEW_REV, 40);
			parent::_addInteger(self::DATE, 10);
			parent::_addText(self::DATA, false);
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(),  TBGScopesTable::ID);
			parent::_addForeignKeyColumn(self::AUTHOR, TBGUsersTable::getTable(), TBGUsersTable::ID);
			parent::_addForeignKeyColumn(self::PROJECT_ID, TBGProjectsTable::getTable(), TBGProjectsTable::ID);
		}
		
		/**
		 * Get all commits relating to issues inside a project
		 * @param integer $id
		 * @param integer $limit
		 * @param integer $offset
		 */
		public function getCommitsByProject($id, $limit = 40, $offset = null)
		{
			$crit = new Criteria();
			
			$crit->addWhere(self::PROJECT_ID, $id);
			$crit->addWhere(self::DATE, strtotime($limit), $crit::DB_GREATER_THAN_EQUAL);
			$crit->addOrderBy(self::DATE, Criteria::SORT_DESC);
		
			if ($limit !== null)
			{
				$crit->setLimit($limit);
			}
			
			if ($offset !== null)
			{
				$crit->setOffset($offset);
			}
				
			$results = $this->doSelect($crit);
			return $results;
		}
	}

