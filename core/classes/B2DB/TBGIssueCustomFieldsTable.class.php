<?php

	use b2db\Core,
		b2db\Criteria,
		b2db\Criterion;

	/**
	 * Issue <-> custom fields relations table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Issue <-> custom fields relations table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 */
	class TBGIssueCustomFieldsTable extends TBGB2DBTable
	{

		const B2DB_TABLE_VERSION = 1;
		const B2DBNAME = 'issuecustomfields';
		const ID = 'issuecustomfields.id';
		const SCOPE = 'issuecustomfields.scope';
		const ISSUE_ID = 'issuecustomfields.issue_id';
		const OPTION_VALUE = 'issuecustomfields.option_value';
		const CUSTOMFIELDS_ID = 'issuecustomfields.customfields_id';

		/**
		 * Return an instance of this table
		 *
		 * @return TBGIssueCustomFieldsTable
		 */
		public static function getTable()
		{
			return Core::getTable('TBGIssueCustomFieldsTable');
		}

		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addForeignKeyColumn(self::ISSUE_ID, TBGIssuesTable::getTable(), TBGIssuesTable::ID);
			parent::_addForeignKeyColumn(self::CUSTOMFIELDS_ID, Core::getTable('TBGCustomFieldsTable'), TBGCustomFieldsTable::ID);
			parent::_addText(self::OPTION_VALUE, false);
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
		}

		public function getAllValuesByIssueID($issue_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::ISSUE_ID, $issue_id);
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());

			$res = $this->doSelect($crit, false);

			return $res;
		}

		public function getRowByCustomFieldIDandIssueID($customdatatype_id, $issue_id)
		{
			$crit = $this->getCriteria();
//			$crit->addJoin(Core::getTable('TBGCustomFieldOptionsTable'), TBGCustomFieldOptionsTable::ID, self::OPTION_VALUE);
			$crit->addWhere(self::ISSUE_ID, $issue_id);
			$crit->addWhere(self::CUSTOMFIELDS_ID, $customdatatype_id);
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());

			$row = $this->doSelectOne($crit);

			return $row;
		}

		public function saveIssueCustomFieldValue($option_id, $customdatatype_id, $issue_id)
		{
			$crit = $this->getCriteria();
			if ($row = $this->getRowByCustomFieldIDandIssueID($customdatatype_id, $issue_id))
			{
				if ($option_id === null)
				{
					$this->doDeleteById($row->get(self::ID));
				}
				else
				{
					$crit->addUpdate(self::OPTION_VALUE, $option_id);
					$res = $this->doUpdateById($crit, $row->get(self::ID));
				}
			}
			elseif ($option_id !== null)
			{
				$crit->addInsert(self::ISSUE_ID, $issue_id);
				$crit->addInsert(self::OPTION_VALUE, $option_id);
				$crit->addInsert(self::CUSTOMFIELDS_ID, $customdatatype_id);
				$crit->addInsert(self::SCOPE, TBGContext::getScope()->getID());
				$res = $this->doInsert($crit);
			}
		}
		
		public function doDeleteByFieldId($id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::CUSTOMFIELDS_ID, $id);
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());

			$res = $this->doSelect($crit);
			
			if ($res)
			{
				while ($row = $res->getNextRow())
				{
					$this->doDeleteById($row->get(self::ID));
				}
			}
		}

	}
