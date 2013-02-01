<?php

	use b2db\Core,
		b2db\Criteria,
		b2db\Criterion;

	/**
	 * Issues <-> Files table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Issues <-> Files table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 *
	 * @Table(name="issuefiles")
	 */
	class TBGIssueFilesTable extends TBGB2DBTable
	{

		const B2DB_TABLE_VERSION = 1;
		const B2DBNAME = 'issuefiles';
		const ID = 'issuefiles.id';
		const SCOPE = 'issuefiles.scope';
		const UID = 'issuefiles.uid';
		const ATTACHED_AT = 'issuefiles.attached_at';
		const FILE_ID = 'issuefiles.file_id';
		const ISSUE_ID = 'issuefiles.issue_id';

		protected $_preloaded_issue_counts;

		protected function _initialize()
		{
			parent::_setup(self::B2DBNAME, self::ID);
			parent::_addForeignKeyColumn(self::UID, TBGUsersTable::getTable(), TBGUsersTable::ID);
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
			parent::_addForeignKeyColumn(self::ISSUE_ID, TBGIssuesTable::getTable(), TBGIssuesTable::ID);
			parent::_addForeignKeyColumn(self::FILE_ID, TBGFilesTable::getTable(), TBGFilesTable::ID);
			parent::_addInteger(self::ATTACHED_AT, 10);
		}

		protected function _setupIndexes()
		{
			$this->_addIndex('issueid', self::ISSUE_ID);
		}

		public function addByIssueIDandFileID($issue_id, $file_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::ISSUE_ID, $issue_id);
			$crit->addWhere(self::FILE_ID, $file_id);
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			if ($this->doCount($crit) == 0)
			{
				$crit = $this->getCriteria();
				$crit->addInsert(self::SCOPE, TBGContext::getScope()->getID());
				$crit->addInsert(self::ATTACHED_AT, time());
				$crit->addInsert(self::ISSUE_ID, $issue_id);
				$crit->addInsert(self::FILE_ID, $file_id);
				$this->doInsert($crit);
			}
		}

		public function getByIssueID($issue_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::ISSUE_ID, $issue_id);
			$crit->addJoin(TBGFilesTable::getTable(), TBGFilesTable::ID, self::FILE_ID);
			$res = $this->doSelect($crit, false);
			
			$ret_arr = array();

			if ($res)
			{
				while ($row = $res->getNextRow())
				{
					$file = TBGContext::factory()->TBGFile($row->get(TBGFilesTable::ID), $row);
					$file->setUploadedAt($row->get(self::ATTACHED_AT));
					$ret_arr[$row->get(TBGFilesTable::ID)] = $file;
				}
			}
			
			return $ret_arr;
		}

		public function preloadIssueFileCounts($target_ids)
		{
			$crit = $this->getCriteria();
			$crit->addSelectionColumn(self::ID, 'num_files', Criteria::DB_COUNT);
			$crit->addSelectionColumn(self::ISSUE_ID);
			$crit->addWhere(self::ISSUE_ID, $target_ids, Criteria::DB_IN);
			$crit->addGroupBy(self::ISSUE_ID);

			$res = $this->doSelect($crit, false);
			$this->_preloaded_issue_counts = array();
			if ($res)
			{
				while ($row = $res->getNextRow())
				{
					$this->_preloaded_issue_counts[$row->get(self::ISSUE_ID)] = $row->get('num_files');
				}
			}
		}

		public function clearPreloadedIssueFileCounts()
		{
			$this->_preloaded_issue_counts = null;
		}

		public function getPreloadedIssueFileCount($target_id)
		{
			if (!is_array($this->_preloaded_issue_counts)) return null;

			if (isset($this->_preloaded_issue_counts[$target_id]))
			{
				$val = $this->_preloaded_issue_counts[$target_id];
				unset($this->_preloaded_issue_counts[$target_id]);
				return $val;
			}
			return 0;
		}

		public function countByIssueID($issue_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::ISSUE_ID, $issue_id);
			return $this->doCount($crit);
		}

		public function getIssuesByFileID($file_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::FILE_ID, $file_id);

			$issue_ids = array();
			if ($res = $this->doSelect($crit))
			{
				while ($row = $res->getNextRow())
				{
					$i_id = $row->get(self::ISSUE_ID);
					$issue_ids[$i_id] = $i_id;
				}
			}
			return $issue_ids;
		}

		public function removeByIssueIDandFileID($issue_id, $file_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::ISSUE_ID, $issue_id);
			$crit->addWhere(self::FILE_ID, $file_id);
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			if ($res = $this->doSelectOne($crit))
			{
				$this->doDelete($crit);
			}
			return $res;
		}
		
	}
