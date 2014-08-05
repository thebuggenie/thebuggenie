<?php

	use b2db\Core,
		b2db\Criteria,
		b2db\Criterion;

	/**
	 * Builds table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Builds table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 *
	 * @Table(name="builds")
	 * @Entity(class="TBGBuild")
	 */
	class TBGBuildsTable extends TBGB2DBTable 
	{

		const B2DB_TABLE_VERSION = 2;
		const B2DBNAME = 'builds';
		const ID = 'builds.id';
		const SCOPE = 'builds.scope';
		const NAME = 'builds.name';
		const VERSION_MAJOR = 'builds.version_major';
		const VERSION_MINOR = 'builds.version_minor';
		const VERSION_REVISION = 'builds.version_revision';
		const VERSION_PATCH = 'builds.version_patch';
		const EDITION = 'builds.edition';
		const RELEASE_DATE = 'builds.release_date';
		const LOCKED = 'builds.locked';
		const PROJECT = 'builds.project';
		const MILESTONE = 'builds.milestone';
		const RELEASED = 'builds.isreleased';
		const FILE_ID = 'builds.file_id';
		const FILE_URL = 'builds.file_url';
		
//		protected function _initialize()
//		{
//			parent::_setup(self::B2DBNAME, self::ID);
//			parent::_addVarchar(self::NAME, 100);
//			parent::_addText(self::FILE_URL);
//			parent::_addInteger(self::VERSION_MAJOR, 3);
//			parent::_addInteger(self::VERSION_MINOR, 3);
//			parent::_addInteger(self::VERSION_REVISION, 5);
//			parent::_addInteger(self::VERSION_PATCH, 5);
//			parent::_addInteger(self::RELEASE_DATE, 10);
//			parent::_addBoolean(self::RELEASED);
//			parent::_addBoolean(self::LOCKED);
//			parent::_addForeignKeyColumn(self::EDITION, TBGEditionsTable::getTable(), TBGEditionsTable::ID);
//			parent::_addForeignKeyColumn(self::PROJECT, TBGProjectsTable::getTable(), TBGProjectsTable::ID);
//			parent::_addForeignKeyColumn(self::FILE_ID, TBGFilesTable::getTable(), TBGFilesTable::ID);
//			parent::_addForeignKeyColumn(self::MILESTONE, TBGMilestonesTable::getTable(), TBGMilestonesTable::ID);
//			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
//		}
		
		public function _migrateData(\b2db\Table $old_table)
		{
			$sqls = array();
			$tn = $this->_getTableNameSQL();
			switch ($old_table->getVersion())
			{
				case 1:
					$crit = $this->getCriteria();
					$crit->addWhere(self::EDITION, 0, Criteria::DB_NOT_EQUALS);
					$res = $this->doSelect($crit);
					$editions = array();
					if ($res)
					{
						while ($row = $res->getNextRow())
						{
							$editions[$row->get(self::EDITION)] = 0;
						}
					}
					
					$edition_projects = TBGEditionsTable::getTable()->getProjectIDsByEditionIDs(array_keys($editions));

					foreach ($edition_projects as $edition => $project)
					{
						$crit = $this->getCriteria();
						$crit->addUpdate(self::PROJECT, $project);
						$crit->addWhere(self::EDITION, $edition);
						$res = $this->doUpdate($crit);
					}
					break;
			}
		}

		public function getByProjectID($project_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::PROJECT, $project_id);
			$crit->addOrderBy(self::RELEASE_DATE, Criteria::SORT_DESC);
			return $this->select($crit);
		}

		public function getByFileID($file_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::FILE_ID, $file_id);
			return $this->select($crit);
		}

		public function getByEditionID($edition_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::EDITION, $edition_id);
			$crit->addOrderBy(self::RELEASE_DATE, Criteria::SORT_DESC);
			$res = $this->doSelect($crit);
			
			return $res;
		}

		public function getByID($id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$row = $this->doSelectById($id, $crit);
			return $row;
		}
		
		public function getByIDs($ids)
		{
			if (empty($ids)) return array();

			$crit = $this->getCriteria();
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$crit->addWhere(self::ID, $ids, Criteria::DB_IN);
			return $this->select($crit);
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
