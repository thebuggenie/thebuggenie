<?php

	use b2db\Core,
		b2db\Criteria,
		b2db\Criterion;

	/**
	 * Roles <- permissions table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Roles <- permissions table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 *
	 * @Table(name="rolepermissions")
	 */
	class TBGRolePermissionsTable extends TBGB2DBTable
	{

		const B2DB_TABLE_VERSION = 1;
		const B2DBNAME = 'rolepermissions';
		const ID = 'rolepermissions.id';
		const SCOPE = 'rolepermissions.scope';
		const ROLE_ID = 'rolepermissions.role_id';
		const PERMISSION = 'rolepermissions.permission';

		public function _initialize()
		{
			parent::_setup(self::B2DBNAME, self::ID);
			parent::_addForeignKeyColumn(self::ROLE_ID, TBGListTypesTable::getTable());
			parent::_addVarchar(self::PERMISSION, 100);
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
			$res = $this->doSelect($crit);
			
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

		public function countByIssueID($issue_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::ISSUE_ID, $issue_id);
			return $this->doCount($crit);
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
