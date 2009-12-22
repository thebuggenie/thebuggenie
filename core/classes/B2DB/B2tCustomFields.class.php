<?php

	/**
	 * Custom fields table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
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
	class B2tCustomFields extends B2DBTable
	{

		const B2DBNAME = 'customfields';
		const ID = 'customfields.id';
		const FIELD_NAME = 'customfields.field_name';
		const FIELD_DESCRIPTION = 'customfields.field_description';
		const FIELD_INSTRUCTIONS = 'customfields.field_instructions';
		const FIELD_KEY = 'customfields.field_key';
		const FIELD_TYPE = 'customfields.field_type';
		const SCOPE = 'customfields.scope';

		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addVarchar(self::FIELD_NAME, 100);
			parent::_addVarchar(self::FIELD_KEY, 100);
			parent::_addVarchar(self::FIELD_DESCRIPTION, 200);
			parent::_addText(self::FIELD_INSTRUCTIONS);
			parent::_addInteger(self::FIELD_TYPE);
			parent::_addForeignKeyColumn(self::SCOPE, B2DB::getTable('B2tScopes'), B2tScopes::ID);
		}

		public function createNew($name, $key, $fieldtype = 1, $scope = null)
		{
			$scope = ($scope === null) ? BUGScontext::getScope()->getID() : $scope;

			$crit = $this->getCriteria();
			$crit->addInsert(self::FIELD_NAME, $name);
			$crit->addInsert(self::FIELD_TYPE, $fieldtype);
			$crit->addInsert(self::FIELD_KEY, $key);
			$crit->addInsert(self::SCOPE, $scope);

			return $this->doInsert($crit);
		}

		public function getAll()
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::SCOPE, BUGScontext::getScope()->getID());

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
			$crit->addWhere(self::SCOPE, BUGScontext::getScope()->getID());

			return $this->doCount($crit);
		}

		public function getByKey($key)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::FIELD_KEY, $key);
			$crit->addWhere(self::SCOPE, BUGScontext::getScope()->getID());

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

	}
