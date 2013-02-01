<?php

	use b2db\Core,
		b2db\Criteria,
		b2db\Criterion;

	/**
	 * Issue affects edition table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Issue affects edition table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 *
	 * @Table(name="issueaffectsedition")
	 */
	class TBGIssueAffectsEditionTable extends TBGB2DBTable 
	{

		const B2DB_TABLE_VERSION = 1;
		const B2DBNAME = 'issueaffectsedition';
		const ID = 'issueaffectsedition.id';
		const SCOPE = 'issueaffectsedition.scope';
		const ISSUE = 'issueaffectsedition.issue';
		const EDITION = 'issueaffectsedition.edition';
		const CONFIRMED = 'issueaffectsedition.confirmed';
		const STATUS = 'issueaffectsedition.status';
		
		protected $_preloaded_values = null;

		protected function _initialize()
		{
			parent::_setup(self::B2DBNAME, self::ID);
			parent::_addBoolean(self::CONFIRMED);
			parent::_addForeignKeyColumn(self::EDITION, Core::getTable('TBGEditionsTable'), TBGEditionsTable::ID);
			parent::_addForeignKeyColumn(self::ISSUE, TBGIssuesTable::getTable(), TBGIssuesTable::ID);
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
			parent::_addForeignKeyColumn(self::STATUS, TBGListTypesTable::getTable(), TBGListTypesTable::ID);
		}
		
		protected function _setupIndexes()
		{
			$this->_addIndex('issue', self::ISSUE);
		}

		public function getOpenAffectedIssuesByEditionID($edition_id, $limit_status, $limit_category, $limit_issuetype)
		{
			$crit = $this->getCriteria();
			if ($limit_status)
			{
				$crit->addWhere(TBGIssuesTable::STATUS, $limit_status);
			}
			if ($limit_category)
			{
				$crit->addWhere(TBGIssuesTable::CATEGORY, $limit_category);
			}
			if ($limit_issuetype)
			{
				$crit->addWhere(TBGIssuesTable::ISSUE_TYPE, $limit_issuetype);
			}
			$crit->addWhere(TBGIssuesTable::STATE, TBGIssue::STATE_OPEN);
			$crit->addWhere(self::EDITION, $edition_id);
			$res = $this->doSelect($crit);
			return $res;
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

		public function getByIssueIDandEditionID($issue_id, $edition_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::EDITION, $edition_id);
			$crit->addWhere(self::ISSUE, $issue_id);
			$res = $this->doSelectOne($crit);
			return $res;
		}
		
		public function setIssueAffected($issue_id, $edition_id)
		{
			if (!$this->getByIssueIDandEditionID($issue_id, $edition_id))
			{
				$crit = $this->getCriteria();
				$crit->addInsert(self::ISSUE, $issue_id);
				$crit->addInsert(self::EDITION, $edition_id);
				$crit->addInsert(self::SCOPE, TBGContext::getScope()->getID());
				$ret = $this->doInsert($crit);
				return $ret->getInsertID();
			}
			else
			{
				return false;
			}
		}

		public function deleteByIssueIDandEditionID($issue_id, $edition_id)
		{
			if (!$this->getByIssueIDandEditionID($issue_id, $edition_id))
			{
				return false;
			}
			else
			{
				$crit = $this->getCriteria();
				$crit->addWhere(self::ISSUE, $issue_id);
				$crit->addWhere(self::EDITION, $edition_id);
				$this->doDelete($crit);
				return true;
			}
		}
		
		public function confirmByIssueIDandEditionID($issue_id, $edition_id, $confirmed = true)
		{
			if (!($res = $this->getByIssueIDandEditionID($issue_id, $edition_id)))
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
		
		public function setStatusByIssueIDandEditionID($issue_id, $edition_id, $status_id)
		{
			if (!($res = $this->getByIssueIDandEditionID($issue_id, $edition_id)))
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
		
	}
