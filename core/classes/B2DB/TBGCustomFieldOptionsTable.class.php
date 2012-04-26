<?php

	use b2db\Core,
		b2db\Criteria,
		b2db\Criterion;

	/**
	 * Custom field options table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Custom field options table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 *
	 * @static TBGCustomFieldOptionsTable getTable() Returns an instance of this table
	 *
	 * @Table(name="customfieldoptions")
	 * @Entity(class="TBGCustomDatatypeOption")
	 */
	class TBGCustomFieldOptionsTable extends TBGB2DBTable
	{

		const B2DB_TABLE_VERSION = 2;
		const B2DBNAME = 'customfieldoptions';
		const ID = 'customfieldoptions.id';
		const NAME = 'customfieldoptions.name';
		const ITEMDATA = 'customfieldoptions.itemdata';
		const OPTION_VALUE = 'customfieldoptions.value';
		const SORT_ORDER = 'customfieldoptions.sort_order';
		const CUSTOMFIELD_ID = 'customfieldoptions.customfield_id';
		const SCOPE = 'customfieldoptions.scope';

		public function getByValueAndCustomfieldID($value, $customfield_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::OPTION_VALUE, $value);
			$crit->addWhere(self::CUSTOMFIELD_ID, $customfield_id);

			$row = $this->selectOne($crit);

			return $row;
		}

		public function deleteCustomFieldOptions($customfield_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::CUSTOMFIELD_ID, $customfield_id);
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$this->doDelete($crit);
		}

		public function _migrateData(\b2db\Table $old_table)
		{
			switch ($old_table->getVersion())
			{
				case 1:
					if ($res = $old_table->doSelectAll())
					{
						$customdatatypes_table = TBGCustomDatatype::getB2DBTable();
						$crit = $customdatatypes_table->getCriteria();
						$crit->indexBy(TBGCustomFieldsTable::FIELD_KEY);
						$customfields = $customdatatypes_table->select($crit);
						while ($row = $res->getNextRow())
						{
							$key = $row->get('customfieldoptions.customfield_key');
							$customfield = (array_key_exists($key, $customfields)) ? $customfields[$key] : null;
							if ($customfield instanceof TBGCustomDatatype)
							{
								$crit = $this->getCriteria();
								$crit->addUpdate(self::CUSTOMFIELD_ID, $customfield->getID());
								$this->doUpdateById($crit, $row->get(self::ID));
							}
							else
							{
								$this->doDeleteById($row->get(self::ID));
							}
						}
					}
					break;
			}
		}

		public function saveOptionOrder($options, $customfield_id)
		{
			foreach ($options as $key => $option_id)
			{
				$crit = $this->getCriteria();
				$crit->addUpdate(self::SORT_ORDER, $key + 1);
				$crit->addWhere(self::CUSTOMFIELD_ID, $customfield_id);
				$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
				$this->doUpdateById($crit, $option_id);
			}
		}

	}
