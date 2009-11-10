<?php

	/**
	 * Notifications table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Notifications table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 */
	class B2tNotifications extends B2DBTable 
	{
		
		const B2DBNAME = 'notifications';
		const ID = 'notifications.id';
		const SCOPE = 'notifications.scope';
		const MODULE_NAME = 'notifications.module_name';
		const NOTIFY_TYPE = 'notifications.notify_type';
		const TARGET_ID = 'notifications.target_id';
		const UID = 'notifications.uid';
		const GID = 'notifications.gid';
		const TID = 'notifications.tid';
		const TITLE = 'notifications.title';
		const CONTENTS = 'notifications.contents';
		const STATUS = 'notifications.status';

		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addVarchar(self::MODULE_NAME, 50);
			parent::_addInteger(self::NOTIFY_TYPE, 5);
			parent::_addInteger(self::TARGET_ID, 10);
			parent::_addVarchar(self::TITLE, 100);
			parent::_addText(self::CONTENTS, false);
			parent::_addInteger(self::STATUS, 5);
			parent::_addForeignKeyColumn(self::UID, B2DB::getTable('B2tUsers'), B2tUsers::ID);
			parent::_addForeignKeyColumn(self::GID, B2DB::getTable('B2tGroups'), B2tGroups::ID);
			parent::_addForeignKeyColumn(self::TID, B2DB::getTable('B2tTeams'), B2tTeams::ID);
			parent::_addForeignKeyColumn(self::SCOPE, B2DB::getTable('B2tScopes'), B2tScopes::ID);
		}
		
		
	}
