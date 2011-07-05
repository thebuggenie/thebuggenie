<?php
	/**
	 * B2DB Table, vcs_integration -> VCSIntegrationTable
	 *
	 * @author Philip Kent <kentphilip@gmail.com>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage vcs_integration
	 */

	/**
	 * B2DB Table, vcs_integration -> VCSIntegrationTable
	 *
	 * @package thebuggenie
	 * @subpackage vcs_integration
	 */
	class TBGVCSIntegrationTable extends TBGB2DBTable 
	{

		const B2DB_TABLE_VERSION = 1;
		const B2DBNAME = 'vcsintegration';
		const ID = 'vcsintegration.id';
		const SCOPE = 'vcsintegration.scope';
		const ISSUE_NO = 'vcsintegration.issue_no';
		const FILE_NAME = 'vcsintegration.file_name';
		const LOG = 'vcsintegration.log';
		const OLD_REV = 'vcsintegration.old_rev';
		const NEW_REV = 'vcsintegration.new_rev';
		const AUTHOR = 'vcsintegration.author';
		const DATE = 'vcsintegration.date';
		const ACTION = 'vcsintegration.action';
					
		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addText(self::FILE_NAME, false);
			parent::_addText(self::LOG, false);
			parent::_addVarchar(self::OLD_REV, 40);
			parent::_addVarchar(self::NEW_REV, 40);
			parent::_addVarchar(self::AUTHOR, 100);
			parent::_addVarchar(self::ACTION, 1);
			parent::_addInteger(self::DATE, 10);
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(),  TBGScopesTable::ID);
			parent::_addForeignKeyColumn(self::ISSUE_NO, TBGIssuesTable::getTable(),  TBGIssuesTable::ID);
		}
		
		/**
		 * Return an instance of this table
		 *
		 * @return TBGVCSIntegrationTable
		 */
		public static function getTable()
		{
			return B2DB::getTable('TBGVCSIntegrationTable');
		}

		/**
		 * Return number commits associated to a given issue
		 *
		 * @param $id ID number of issue
		 *
		 * @return integer
		 */
		public function getNumberOfCommitsByIssue($id)
		{
			$commits = $this->getCommitsByIssue($id);
			
			if ($commits === false)
			{
				return 0;
			}
			
			return count($commits);
		}
		
		/**
		 * Return all commits associated to a given issue
		 *
		 * @param $id ID number of issue
		 *
		 * @return false if no commits, otherwise array
		 */
		public function getCommitsByIssue($id)
		{
			$crit = new B2DBCriteria();
			$crit->addWhere(self::ISSUE_NO, $id);
			$crit->addOrderBy(self::DATE, B2DBCriteria::SORT_DESC);
			$results = $this->doSelect($crit);

			if (!is_object($results) || $results->getNumberOfRows() == 0)
			{
				return false;
			}
			
			$data = array();
			
			/* Build revision details */
			while ($results->next())
			{
				$file = array($results->get(TBGVCSIntegrationTable::FILE_NAME), $results->get(TBGVCSIntegrationTable::ACTION), $results->get(TBGVCSIntegrationTable::NEW_REV), $results->get(TBGVCSIntegrationTable::OLD_REV));
				if (array_key_exists($results->get(TBGVCSIntegrationTable::NEW_REV), $data))
				{
					$data[$results->get(TBGVCSIntegrationTable::NEW_REV)][1][] = $file;
				}
				else
				{
					// one array for revision details, other for files
					$data[$results->get(TBGVCSIntegrationTable::NEW_REV)] = array(array(), array());
					$data[$results->get(TBGVCSIntegrationTable::NEW_REV)][0] = array($results->get(TBGVCSIntegrationTable::ID), $results->get(TBGVCSIntegrationTable::AUTHOR), $results->get(TBGVCSIntegrationTable::DATE), $results->get(TBGVCSIntegrationTable::LOG), $results->get(TBGVCSIntegrationTable::ISSUE_NO));
					$data[$results->get(TBGVCSIntegrationTable::NEW_REV)][1][] = $file;
				}
			}
			
			return $data;
		}
		
		/**
		 * Return all commits associated to a given project
		 *
		 * @param $id ID number of project
		 * @param $limit Maximum age of commits to show, use strtotime format (default is 2 weeks ago)
		 *
		 * @return false if no commits, otherwise array
		 */
		public function getCommitsByProject($id, $limit = 40, $offset = null)
		{
			$crit = new B2DBCriteria();
			
			$issues = TBGIssuesTable::getTable()->getIssuesByProjectId($id);
			
			$crit->addWhere(self::ISSUE_NO, $issues[0]->getID());
			for($i = 1; $i != count($issues); $i++)
			{
				$crit->addOr(self::ISSUE_NO, $issues[$i]->getID());
			}
			
			$crit->addWhere(self::DATE, strtotime($limit), $crit::DB_GREATER_THAN_EQUAL);
			
			$crit->addOrderBy(self::DATE, B2DBCriteria::SORT_DESC);

			if ($limit !== null)
			{
				$crit->setLimit($limit);
			}
			if ($offset !== null)
			{
				$crit->setOffset($offset);
			}
			
			$results = $this->doSelect($crit);

			if (!is_object($results) || $results->getNumberOfRows() == 0)
			{
				return false;
			}
			
			$data = array();
			
			/* Build revision details */
			while ($results->next())
			{
				$file = array($results->get(TBGVCSIntegrationTable::FILE_NAME), $results->get(TBGVCSIntegrationTable::ACTION), $results->get(TBGVCSIntegrationTable::NEW_REV), $results->get(TBGVCSIntegrationTable::OLD_REV));
				if (array_key_exists($results->get(TBGVCSIntegrationTable::NEW_REV), $data))
				{
					$data[$results->get(TBGVCSIntegrationTable::NEW_REV)][1][] = $file;
				}
				else
				{
					// one array for revision details, other for files
					$data[$results->get(TBGVCSIntegrationTable::NEW_REV)] = array(array(), array());
					$data[$results->get(TBGVCSIntegrationTable::NEW_REV)][0] = array($results->get(TBGVCSIntegrationTable::ID), $results->get(TBGVCSIntegrationTable::AUTHOR), $results->get(TBGVCSIntegrationTable::DATE), $results->get(TBGVCSIntegrationTable::LOG), $results->get(TBGVCSIntegrationTable::ISSUE_NO));
					$data[$results->get(TBGVCSIntegrationTable::NEW_REV)][1][] = $file;
				}
			}
			
			return $data;
		}
	}

