<?php

	use b2db\Core,
		b2db\Criteria,
		b2db\Criterion;

	/**
	 * Edition components table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Edition components table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 *
	 * @Table(name="editioncomponents")
	 */
	class TBGEditionComponentsTable extends TBGB2DBTable 
	{

		const B2DB_TABLE_VERSION = 1;
		const B2DBNAME = 'editioncomponents';
		const ID = 'editioncomponents.id';
		const SCOPE = 'editioncomponents.scope';
		const EDITION = 'editioncomponents.edition';
		const COMPONENT = 'editioncomponents.component';

		protected function _initialize()
		{
			parent::_setup(self::B2DBNAME, self::ID);
			parent::_addForeignKeyColumn(self::EDITION, Core::getTable('TBGEditionsTable'), TBGEditionsTable::ID);
			parent::_addForeignKeyColumn(self::COMPONENT, Core::getTable('TBGComponentsTable'), TBGComponentsTable::ID);
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
