<?php

	class TBGVCSIntegrationTable extends B2DBTable 
	{
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
			$crit = new B2DBCriteria();
			$crit->addWhere(self::ISSUE_NO, $id);
			$crit->addOrderBy(self::DATE, B2DBCriteria::SORT_DESC);
			$results = $this->doSelect($crit);

			return $results->getNumberOfRows();
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

			if ($results->getNumberOfRows() == 0)
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
					$data[$results->get(TBGVCSIntegrationTable::NEW_REV)][0] = array($results->get(TBGVCSIntegrationTable::ID), $results->get(TBGVCSIntegrationTable::AUTHOR), $results->get(TBGVCSIntegrationTable::DATE), $results->get(TBGVCSIntegrationTable::LOG));
					$data[$results->get(TBGVCSIntegrationTable::NEW_REV)][1][] = $file;
				}
			}
			
			return $data;
		}
	}

?>