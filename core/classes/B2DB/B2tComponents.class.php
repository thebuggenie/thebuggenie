<?php

	/**
	 * Components table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Components table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 */
	class B2tComponents extends B2DBTable 
	{

		const B2DBNAME = 'components';
		const ID = 'components.id';
		const SCOPE = 'components.scope';
		const NAME = 'components.name';
		const VERSION_MAJOR = 'components.version_major';
		const VERSION_MINOR = 'components.version_minor';
		const VERSION_REVISION = 'components.version_revision';
		const PROJECT = 'components.project';

		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addVarchar(self::NAME, 100);
			parent::_addInteger(self::VERSION_MAJOR, 3);
			parent::_addInteger(self::VERSION_MINOR, 3);
			parent::_addInteger(self::VERSION_REVISION, 5);
			parent::_addForeignKeyColumn(self::PROJECT, B2DB::getTable('B2tProjects'), B2tProjects::ID);
			parent::_addForeignKeyColumn(self::SCOPE, B2DB::getTable('B2tScopes'), B2tScopes::ID);
		}
		
		public function createNew($name, $project_id)
		{
			$crit = $this->getCriteria();
			$crit->addInsert(self::NAME, $name);
			$crit->addInsert(self::PROJECT, $project_id);
			$crit->addInsert(self::SCOPE, BUGScontext::getScope()->getID());
			$res = $this->doInsert($crit);
			
			return $res->getInsertID();
		}
		
		public function getByProjectID($project_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::PROJECT, $project_id);
			$res = $this->doSelect($crit);
			return $res;
		}
		
	}
