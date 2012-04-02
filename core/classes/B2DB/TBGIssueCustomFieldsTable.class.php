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
	 *
	 * @Table(name="issuecustomfields")
	 */
	class TBGIssueCustomFieldsTable extends TBGB2DBTable
	{

		const B2DB_TABLE_VERSION = 2;
		const B2DBNAME = 'issuecustomfields';
		const ID = 'issuecustomfields.id';
		const SCOPE = 'issuecustomfields.scope';
		const ISSUE_ID = 'issuecustomfields.issue_id';
		const CUSTOMFIELDOPTION_ID = 'issuecustomfields.customfieldoption_id';
		const CUSTOMFIELDS_ID = 'issuecustomfields.customfields_id';
		const OPTION_VALUE = 'issuecustomfields.option_value';

		protected function _initialize()
		{
			parent::_setup(self::B2DBNAME, self::ID);
			parent::_addForeignKeyColumn(self::ISSUE_ID, TBGIssuesTable::getTable(), TBGIssuesTable::ID);
			parent::_addForeignKeyColumn(self::CUSTOMFIELDS_ID, TBGCustomFieldsTable::getTable(), TBGCustomFieldsTable::ID);
			parent::_addForeignKeyColumn(self::CUSTOMFIELDOPTION_ID, TBGCustomFieldOptionsTable::getTable(), TBGCustomFieldOptionsTable::ID);
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
			$crit->addWhere(self::ISSUE_ID, $issue_id);
			$crit->addWhere(self::CUSTOMFIELDS_ID, $customdatatype_id);
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());

			$row = $this->doSelectOne($crit);

			return $row;
		}

		public function saveIssueCustomFieldValue($value, $customdatatype_id, $issue_id)
		{
			$crit = $this->getCriteria();
			if ($row = $this->getRowByCustomFieldIDandIssueID($customdatatype_id, $issue_id))
			{
				if ($value === null)
				{
					$this->doDeleteById($row->get(self::ID));
				}
				else
				{
					$crit->addUpdate(self::OPTION_VALUE, $value);
					$res = $this->doUpdateById($crit, $row->get(self::ID));
				}
			}
			elseif ($value !== null)
			{
				$crit->addInsert(self::ISSUE_ID, $issue_id);
				$crit->addInsert(self::OPTION_VALUE, $value);
				$crit->addInsert(self::CUSTOMFIELDS_ID, $customdatatype_id);
				$crit->addInsert(self::SCOPE, TBGContext::getScope()->getID());
				$res = $this->doInsert($crit);
			}
		}
		
		public function saveIssueCustomFieldOption($option_id, $customdatatype_id, $issue_id)
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
					$crit->addUpdate(self::CUSTOMFIELDOPTION_ID, $option_id);
					$res = $this->doUpdateById($crit, $row->get(self::ID));
				}
			}
			elseif ($option_id !== null)
			{
				$crit->addInsert(self::ISSUE_ID, $issue_id);
				$crit->addInsert(self::CUSTOMFIELDOPTION_ID, $option_id);
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

		public function _migrateData(\b2db\Table $old_table)
		{
			switch ($old_table->getVersion())
			{
				case 1:
					if ($res = $old_table->doSelectAll())
					{
						$customfields = TBGCustomDatatype::getB2DBTable()->selectAll();
						while ($row = $res->getNextRow())
						{
							$customfield_id = $row->get(self::CUSTOMFIELDS_ID);
							$customfield = (array_key_exists($customfield_id, $customfields)) ? $customfields[$customfield_id] : null;
							if ($customfield instanceof TBGCustomDatatype && $customfield->hasCustomOptions())
							{
								$customfieldoption = TBGCustomFieldOptionsTable::getTable()->getByValueAndCustomfieldID((int) $row->get(self::OPTION_VALUE), $customfield->getID());
								if ($customfieldoption instanceof TBGCustomDatatypeOption)
								{
									$crit = $this->getCriteria();
									$crit->addUpdate(self::CUSTOMFIELDOPTION_ID, $customfieldoption->getID());
									$crit->addUpdate(self::OPTION_VALUE, null);
									$this->doUpdateById($crit, $row->get(self::ID));
								}
								elseif($row->get(self::ID))
								{
									$this->doDeleteById($row->get(self::ID));
								}
							}
						}
					}
					break;
			}
		}

	}
