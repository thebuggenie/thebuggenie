<?php

	use b2db\Core,
		b2db\Criteria,
		b2db\Criterion;

	/**
	 * Custom fields table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Custom fields table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 */
	class TBGCustomFieldsTable extends TBGB2DBTable
	{

		const B2DB_TABLE_VERSION = 1;
		const B2DBNAME = 'customfields';
		const ID = 'customfields.id';
		const FIELD_NAME = 'customfields.name';
		const FIELD_DESCRIPTION = 'customfields.description';
		const FIELD_INSTRUCTIONS = 'customfields.instructions';
		const FIELD_KEY = 'customfields.key';
		const FIELD_TYPE = 'customfields.itemtype';
		const SCOPE = 'customfields.scope';

		/**
		 * Return an instance of this table
		 *
		 * @return TBGCustomFieldsTable
		 */
		public static function getTable()
		{
			return Core::getTable('TBGCustomFieldsTable');
		}

		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addVarchar(self::FIELD_NAME, 100);
			parent::_addVarchar(self::FIELD_KEY, 100);
			parent::_addVarchar(self::FIELD_DESCRIPTION, 200);
			parent::_addText(self::FIELD_INSTRUCTIONS);
			parent::_addInteger(self::FIELD_TYPE);
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
		}

		public function getAll()
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());

			$res = $this->doSelect($crit);
			$retval = array();

			if ($res)
			{
				while ($row = $res->getNextRow())
				{
					$retval[$row->get(self::ID)] = $row;
				}
			}

			return $retval;
		}

		public function countByKey($key)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::FIELD_KEY, $key);
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());

			return $this->doCount($crit);
		}

		public function getByKey($key)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::FIELD_KEY, $key);
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());

			return $this->doSelectOne($crit);
		}

		public function saveById($name, $description, $instructions, $id)
		{
			$crit = $this->getCriteria();
			$crit->addUpdate(self::FIELD_NAME, $name);
			$crit->addUpdate(self::FIELD_DESCRIPTION, $description);
			$crit->addUpdate(self::FIELD_INSTRUCTIONS, $instructions);

			$res = $this->doUpdateById($crit, $id);
		}

		public function getKeyFromID($id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::ID, $id);
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());

			$row = $this->doSelectOne($crit);
			if ($row instanceof \b2db\Row)
			{
				return $row->get(self::FIELD_KEY);
			}
			return null;
		}
	}
