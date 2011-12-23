<?php

	use b2db\Core,
		b2db\Criteria,
		b2db\Criterion;

	/**
	 * Components table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Components table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 *
	 * @Table(name="components")
	 * @Entity(class="TBGComponent")
	 */
	class TBGComponentsTable extends TBGB2DBTable 
	{

		const B2DB_TABLE_VERSION = 2;
		const B2DBNAME = 'components';
		const ID = 'components.id';
		const SCOPE = 'components.scope';
		const NAME = 'components.name';
		const VERSION_MAJOR = 'components.version_major';
		const VERSION_MINOR = 'components.version_minor';
		const VERSION_REVISION = 'components.version_revision';
		const PROJECT = 'components.project';
		const LEAD_BY = 'components.leader';
		const LEAD_TYPE = 'components.leader_type';
		
//		protected function _initialize()
//		{
//			parent::_setup(self::B2DBNAME, self::ID);
//			parent::_addVarchar(self::NAME, 100);
//			parent::_addInteger(self::VERSION_MAJOR, 3);
//			parent::_addInteger(self::VERSION_MINOR, 3);
//			parent::_addInteger(self::VERSION_REVISION, 5);
//			parent::_addInteger(self::LEAD_BY, 10);
//			parent::_addInteger(self::LEAD_TYPE, 3);
//			parent::_addForeignKeyColumn(self::PROJECT, TBGProjectsTable::getTable(), TBGProjectsTable::ID);
//			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
//		}
		
		public function getByProjectID($project_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::PROJECT, $project_id);
			$res = $this->doSelect($crit, false);
			return $res;
		}
		
	}
