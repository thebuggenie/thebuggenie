<?php

	class B2tCalendarTasks extends B2DBTable
	{
		const B2DBNAME = 'bugs2_calendartasks';
		const ID = 'bugs2_calendartasks.id';
		const SCOPE = 'bugs2_calendartasks.scope';
		const CALENDAR = 'bugs2_calendartasks.calendar';
		const TITLE = 'bugs2_calendartasks.title';
		const STARTS = 'bugs2_calendartasks.starts';
		const ENDS = 'bugs2_calendartasks.ends';
		const LOCATION = 'bugs2_calendartasks.location';
		const ITEMTYPE = 'bugs2_calendartasks.itemtype';
		const DESCRIPTION = 'bugs2_calendartasks.description';
		const STATUS = 'bugs2_calendartasks.status';
					
		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addVarchar(self::TITLE, 200);
			parent::_addText(self::DESCRIPTION, false);
			parent::_addInteger(self::STARTS, 10);
			parent::_addInteger(self::ENDS, 10);
			parent::_addInteger(self::ITEMTYPE, 3);
			parent::_addInteger(self::STATUS, 3);
			parent::_addVarchar(self::LOCATION, 200, '');
			parent::_addForeignKeyColumn(self::SCOPE, B2DB::getTable('B2tScopes'), B2tScopes::ID);
			parent::_addForeignKeyColumn(self::CALENDAR, B2DB::getTable('B2tCalendars'), B2tCalendars::ID);
		}
	}

?>