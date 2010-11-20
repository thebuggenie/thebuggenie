<?php

	/**
	 * Edition components table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 ** @version 3.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Edition components table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 */
	class TBGEditionComponentsTable extends TBGB2DBTable 
	{

		const B2DBNAME = 'editioncomponents';
		const ID = 'editioncomponents.id';
		const SCOPE = 'editioncomponents.scope';
		const EDITION = 'editioncomponents.edition';
		const COMPONENT = 'editioncomponents.component';

		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addForeignKeyColumn(self::EDITION, B2DB::getTable('TBGEditionsTable'), TBGEditionsTable::ID);
			parent::_addForeignKeyColumn(self::COMPONENT, B2DB::getTable('TBGComponentsTable'), TBGComponentsTable::ID);
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
		}

		public function getByEditionID($edition_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::EDITION, $edition_id);
			$res = $this->doSelect($crit);

			return $res;
		}

		public function getByEditionIDandComponentID($edition_id, $component_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::EDITION, $edition_id);
			$crit->addWhere(self::COMPONENT, $component_id);

			return $this->doCount($crit);
		}

		public function addEditionComponent($edition_id, $component_id)
		{
			if ($this->getByEditionIDandComponentID($edition_id, $component_id) == 0)
			{
				$crit = $this->getCriteria();
				$crit->addInsert(self::EDITION, $edition_id);
				$crit->addInsert(self::COMPONENT, $component_id);
				$crit->addInsert(self::SCOPE, TBGContext::getScope()->getID());
				$res = $this->doInsert($crit);

				return true;
			}
			return false;
		}

		public function removeEditionComponent($edition_id, $component_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::EDITION, $edition_id);
			$crit->addWhere(self::COMPONENT, $component_id);
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$res = $this->doDelete($crit);
		}

	}
