<?php

	use b2db\Core,
		b2db\Criteria,
		b2db\Criterion;

	/**
	 * Issue affects build table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Issue affects build table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 *
	 * @Table(name="issueaffectsbuild")
	 */
	class TBGIssueAffectsBuildTable extends TBGB2DBTable 
	{

		const B2DB_TABLE_VERSION = 1;
		const B2DBNAME = 'issueaffectsbuild';
		const ID = 'issueaffectsbuild.id';
		const SCOPE = 'issueaffectsbuild.scope';
		const ISSUE = 'issueaffectsbuild.issue';
		const BUILD = 'issueaffectsbuild.build';
		const CONFIRMED = 'issueaffectsbuild.confirmed';
		const STATUS = 'issueaffectsbuild.status';

		protected $_preloaded_values = null;

		protected function _initialize()
		{
			parent::_setup(self::B2DBNAME, self::ID);
			parent::_addBoolean(self::CONFIRMED);
			parent::_addForeignKeyColumn(self::BUILD, Core::getTable('TBGBuildsTable'), TBGBuildsTable::ID);
			parent::_addForeignKeyColumn(self::ISSUE, TBGIssuesTable::getTable(), TBGIssuesTable::ID);
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
			parent::_addForeignKeyColumn(self::STATUS, TBGListTypesTable::getTable(), TBGListTypesTable::ID);
		}
		
		protected function _setupIndexes()
		{
			$this->_addIndex('issue', self::ISSUE);
		}

		public function getByIssueIDs($issue_ids)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::ISSUE, $issue_ids, Criteria::DB_IN);
			$res = $this->doSelect($crit, false);
			return $res;
		}
		
		public function getByIssueID($issue_id)
		{
			if (is_array($this->_preloaded_values))
			{
				if (array_key_exists($issue_id, $this->_preloaded_values))
				{
					$values = $this->_preloaded_values[$issue_id];
					unset($this->_preloaded_values[$issue_id]);
					return $values;
				}
				else
				{
					return array();
				}
			}
			else
			{
				$res = $this->getByIssueIDs(array($issue_id));
				$rows = array();
				if ($res)
				{
					while ($row = $res->getNextRow())
					{
						$rows[] = $row;
					}
				}

				return $rows;
			}
		}

		public function preloadValuesByIssueIDs($issue_ids)
		{
			$this->_preloaded_values = array();
			$res = $this->getByIssueIDs($issue_ids);
			if ($res)
			{
				while ($row = $res->getNextRow())
				{
					$issue_id = $row->get(self::ISSUE);
					if (!array_key_exists($issue_id, $this->_preloaded_values)) $this->_preloaded_values[$issue_id] = array();
					$this->_preloaded_values[$issue_id][] = $row;
				}
			}
		}

		public function clearPreloadedValues()
		{
			$this->_preloaded_custom_fields = null;
		}

		public function getByIssueIDandBuildID($issue_id, $build_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::BUILD, $build_id);
			$crit->addWhere(self::ISSUE, $issue_id);
			$res = $this->doSelectOne($crit);
			return $res;
		}
		

		public function deleteByBuildID($build_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::BUILD, $build_id);
			$this->doDelete($crit);
		}
		
		public function deleteByIssueIDandBuildID($issue_id, $build_id)
		{
			if (!$this->getByIssueIDandBuildID($issue_id, $build_id))
			{
				return false;
			}
			else
			{
				$crit = $this->getCriteria();
				$crit->addWhere(self::ISSUE, $issue_id);
				$crit->addWhere(self::BUILD, $build_id);
				$this->doDelete($crit);
				return true;
			}
		}

		public function confirmByIssueIDandBuildID($issue_id, $build_id, $confirmed = true)
		{
			if (!($res = $this->getByIssueIDandBuildID($issue_id, $build_id)))
			{
				return false;
			}
			else
			{
				$crit = $this->getCriteria();
				$crit->addUpdate(self::CONFIRMED, $confirmed);
				$this->doUpdateById($crit, $res->get(self::ID));
				
				return true;
			}				
		}
		
		public function setStatusByIssueIDandBuildID($issue_id, $build_id, $status_id)
		{
			if (!($res = $this->getByIssueIDandBuildID($issue_id, $build_id)))
			{
				return false;
			}
			else
			{
				$crit = $this->getCriteria();
				$crit->addUpdate(self::STATUS, $status_id);
				$this->doUpdateById($crit, $res->get(self::ID));
				
				return true;
			}				
		}
		
		public function setIssueAffected($issue_id, $build_id)
		{
			if (!$this->getByIssueIDandBuildID($issue_id, $build_id))
			{
				$crit = $this->getCriteria();
				$crit->addInsert(self::ISSUE, $issue_id);
				$crit->addInsert(self::BUILD, $build_id);
				$crit->addInsert(self::SCOPE, TBGContext::getScope()->getID());
				$ret = $this->doInsert($crit);
				return $ret->getInsertID();
			}
			else
			{
				return false;
			}
		}
		
	}
