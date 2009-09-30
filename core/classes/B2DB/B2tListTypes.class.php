<?php

	/**
	 * List types table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * List types table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 */
	class B2tListTypes extends B2DBTable 
	{

		const B2DBNAME = 'bugs2_listtypes';
		const ID = 'bugs2_listtypes.id';
		const SCOPE = 'bugs2_listtypes.scope';
		const NAME = 'bugs2_listtypes.name';
		const ITEMTYPE = 'bugs2_listtypes.itemtype';
		const ITEMDATA = 'bugs2_listtypes.itemdata';
		const APPLIES_TO = 'bugs2_listtypes.applies_to';
		const APPLIES_TYPE = 'bugs2_listtypes.applies_type';
		
		protected static $_item_cache = null;
		
		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addVarchar(self::NAME, 100);
			parent::_addVarchar(self::ITEMTYPE, 25);
			parent::_addText(self::ITEMDATA, false);
			parent::_addInteger(self::APPLIES_TO, 10);
			parent::_addInteger(self::APPLIES_TYPE, 3);
			parent::_addForeignKeyColumn(self::SCOPE, B2DB::getTable('B2tScopes'), B2tScopes::ID);
		}
		
		public function clearListTypeCache()
		{
			self::$_item_cache = null;
		}
		
		protected function _populateItemCache()
		{
			if (self::$_item_cache === null)
			{
				self::$_item_cache = array();
				$crit = $this->getCriteria();
				$crit->addWhere(self::SCOPE, BUGScontext::getScope()->getID());
				if ($res = $this->doSelect($crit))
				{
					while ($row = $res->getNextRow())
					{
						self::$_item_cache[$row->get(self::ITEMTYPE)][$row->get(self::ID)] = $row;
					}
				}
			}
		}
		
		public function getAllByItemType($itemtype)
		{
			$this->_populateItemCache();
			if (array_key_exists($itemtype, self::$_item_cache))
			{
				return self::$_item_cache[$itemtype];
			}
			else
			{
				return null;
			}
		}
		
	}
