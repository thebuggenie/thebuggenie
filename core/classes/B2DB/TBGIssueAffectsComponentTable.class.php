<?php

	use b2db\Core,
		b2db\Criteria,
		b2db\Criterion;

	/**
	 * Issue affects component table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Issue affects component table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 *
	 * @Table(name="issueaffectscomponent")
	 */
	class TBGIssueAffectsComponentTable extends TBGB2DBTable 
	{

		const B2DB_TABLE_VERSION = 1;
		const B2DBNAME = 'issueaffectscomponent';
		const ID = 'issueaffectscomponent.id';
		const SCOPE = 'issueaffectscomponent.scope';
		const ISSUE = 'issueaffectscomponent.issue';
		const COMPONENT = 'issueaffectscomponent.component';
		const CONFIRMED = 'issueaffectscomponent.confirmed';
		const STATUS = 'issueaffectscomponent.status';
		
		protected function _initialize()
		{
			parent::_setup(self::B2DBNAME, self::ID);
			parent::_addBoolean(self::CONFIRMED);
			parent::_addForeignKeyColumn(self::COMPONENT, Core::getTable('TBGComponentsTable'), TBGComponentsTable::ID);
			parent::_addForeignKeyColumn(self::ISSUE, TBGIssuesTable::getTable(), TBGIssuesTable::ID);
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
			parent::_addForeignKeyColumn(self::STATUS, TBGListTypesTable::getTable(), TBGListTypesTable::ID);
		}
		
		protected function _setupIndexes()
		{
			$this->_addIndex('issue', self::ISSUE);
		}

		public function getByIssueID($issue_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::ISSUE, $issue_id);
			$res = $this->doSelect($crit);
			return $res;
		}
		
		public function getByIssueIDandComponentID($issue_id, $component_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::COMPONENT, $component_id);
			$crit->addWhere(self::ISSUE, $issue_id);
			$res = $this->doSelectOne($crit);
			return $res;
		}

		public function deleteByIssueIDandComponentID($issue_id, $component_id)
		{
			if (!($res = $this->getByIssueIDandComponentID($issue_id, $component_id)))
			{
				return false;
			}
			else
			{
				$crit = $this->getCriteria();
				$crit->addWhere(self::ISSUE, $issue_id);
				$crit->addWhere(self::COMPONENT, $component_id);
				$this->doDelete($crit);
				return true;
			}
		}
		
		public function confirmByIssueIDandComponentID($issue_id, $component_id, $confirmed = true)
		{
			if (!($res = $this->getByIssueIDandComponentID($issue_id, $component_id)))
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
		
		public function setStatusByIssueIDandComponentID($issue_id, $component_id, $status_id)
		{
			if (!($res = $this->getByIssueIDandComponentID($issue_id, $component_id)))
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
		
		public function setIssueAffected($issue_id, $component_id)
		{
			if (!$this->getByIssueIDandComponentID($issue_id, $component_id))
			{
				$crit = $this->getCriteria();
				$crit->addInsert(self::ISSUE, $issue_id);
				$crit->addInsert(self::COMPONENT, $component_id);
				$crit->addInsert(self::SCOPE, TBGContext::getScope()->getID());
				$crit->addInsert(self::STATUS, 20);
				$ret = $this->doInsert($crit);
				return $ret->getInsertID();
			}
			else
			{
				return false;
			}
		}
		
	}
