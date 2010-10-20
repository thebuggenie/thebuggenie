<?php

	/**
	 * Builds table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 ** @version 3.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Builds table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 */
	class TBGBuildsTable extends B2DBTable 
	{

		const B2DBNAME = 'builds';
		const ID = 'builds.id';
		const SCOPE = 'builds.scope';
		const NAME = 'builds.name';
		const VERSION_MAJOR = 'builds.version_major';
		const VERSION_MINOR = 'builds.version_minor';
		const VERSION_REVISION = 'builds.version_revision';
		const EDITION = 'builds.edition';
		const TIMESTAMP = 'builds.timestamp';
		const RELEASE_DATE = 'builds.release_date';
		const IS_DEFAULT = 'builds.is_default';
		const LOCKED = 'builds.locked';
		const PROJECT = 'builds.project';
		const RELEASED = 'builds.released';
		
		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addVarchar(self::NAME, 100);
			parent::_addInteger(self::VERSION_MAJOR, 3);
			parent::_addInteger(self::VERSION_MINOR, 3);
			parent::_addInteger(self::VERSION_REVISION, 5);
			parent::_addInteger(self::TIMESTAMP, 10);
			parent::_addInteger(self::RELEASE_DATE, 10);
			parent::_addBoolean(self::RELEASED);
			parent::_addBoolean(self::IS_DEFAULT);
			parent::_addBoolean(self::LOCKED);
			parent::_addForeignKeyColumn(self::EDITION, B2DB::getTable('TBGEditionsTable'), TBGEditionsTable::ID);
			parent::_addForeignKeyColumn(self::PROJECT, TBGProjectsTable::getTable(), TBGProjectsTable::ID);
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
		}
		
		public function getByProjectID($project_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::PROJECT, $project_id);
			$crit->addOrderBy(self::VERSION_MAJOR, B2DBCriteria::SORT_DESC);
			$crit->addOrderBy(self::VERSION_MINOR, B2DBCriteria::SORT_DESC);
			$crit->addOrderBy(self::VERSION_REVISION, B2DBCriteria::SORT_DESC);
			$res = $this->doSelect($crit);
			return $res;
		}

		public function getByEditionID($edition_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::EDITION, $edition_id);
			$crit->addOrderBy(self::VERSION_MAJOR, B2DBCriteria::SORT_DESC);
			$crit->addOrderBy(self::VERSION_MINOR, B2DBCriteria::SORT_DESC);
			$crit->addOrderBy(self::VERSION_REVISION, B2DBCriteria::SORT_DESC);
			$res = $this->doSelect($crit);
			
			return $res;
		}

		public function createNew($name, $project = null, $edition = null, $ver_mj = 0, $ver_mn = 0, $ver_rev = 1, $b_id = null)
		{
			$ver_mj = ((int) $ver_mj > 0) ? (int) $ver_mj : 0;
			$ver_mn = ((int) $ver_mn > 0) ? (int) $ver_mn : 0;
			$ver_rev = ((int) $ver_rev > 0) ? (int) $ver_rev : 0;

			$crit = $this->getCriteria();
			if ($b_id !== null)
			{
				$crit->addInsert(self::ID, $b_id);
			}
			if ($edition !== null)
			{
				$crit->addInsert(self::EDITION, $edition);
			}
			if ($project !== null)
			{
				$crit->addInsert(self::PROJECT, $project);
			}
			$crit->addInsert(self::NAME, $name);
			$crit->addInsert(self::VERSION_MAJOR, $ver_mj);
			$crit->addInsert(self::VERSION_MINOR, $ver_mn);
			$crit->addInsert(self::VERSION_REVISION, $ver_rev);
			$crit->addInsert(self::SCOPE, TBGContext::getScope()->getID());
			$res = $this->doInsert($crit);
			
			return ($b_id !== null) ? $b_id : $res->getInsertID();
		}

		public function getByID($id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$row = $this->doSelectById($id, $crit);
			return $row;
		}
		
		public function clearDefaultsByProjectID($project_id)
		{
			$crit = $this->getCriteria();
			$crit->addUpdate(self::IS_DEFAULT, false);
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$crit->addWhere(self::PROJECT, $project_id);
			$res = $this->doUpdate($crit);
		}
		
		public function clearDefaultsByEditionID($edition_id)
		{
			$crit = $this->getCriteria();
			$crit->addUpdate(self::IS_DEFAULT, false);
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$crit->addWhere(self::EDITION, $edition_id);
			$res = $this->doUpdate($crit);
		}
		
		public function setDefaultBuild($build_id)
		{
			$crit = $this->getCriteria();
			$crit->addUpdate(self::IS_DEFAULT, true);
			$res = $this->doUpdateById($crit, $build_id);
		}
		
		
		
	}
