<?php

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
	 */
	class TBGBuildsTable3dot1 extends TBGB2DBTable
	{

		const B2DB_TABLE_VERSION = 1;
		const B2DBNAME = 'builds';
		const ID = 'builds.id';
		const SCOPE = 'builds.scope';
		const NAME = 'builds.name';
		const VERSION_MAJOR = 'builds.version_major';
		const VERSION_MINOR = 'builds.version_minor';
		const VERSION_REVISION = 'builds.version_revision';
		const EDITION = 'builds.edition';
		const RELEASE_DATE = 'builds.release_date';
		const LOCKED = 'builds.locked';
		const PROJECT = 'builds.project';
		const RELEASED = 'builds.isreleased';
		
		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addVarchar(self::NAME, 100);
			parent::_addInteger(self::VERSION_MAJOR, 3);
			parent::_addInteger(self::VERSION_MINOR, 3);
			parent::_addInteger(self::VERSION_REVISION, 5);
			parent::_addInteger(self::RELEASE_DATE, 10);
			parent::_addBoolean(self::RELEASED);
			parent::_addBoolean(self::LOCKED);
			parent::_addForeignKeyColumn(self::EDITION, TBGEditionsTable::getTable(), TBGEditionsTable::ID);
			parent::_addForeignKeyColumn(self::PROJECT, TBGProjectsTable::getTable(), TBGProjectsTable::ID);
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
		}
		
	}
