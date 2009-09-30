<?php

	/**
	 * Issue types table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Issue types table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 */
	class B2tIssueTypes extends B2DBTable 
	{

		const B2DBNAME = 'bugs2_issuetypes';
		const ID = 'bugs2_issuetypes.id';
		const SCOPE = 'bugs2_issuetypes.scope';
		const NAME = 'bugs2_issuetypes.name';
		const DESCRIPTION = 'bugs2_issuetypes.description';
		const APPLIES_TO = 'bugs2_issuetypes.applies_to';
		const APPLIES_TYPE = 'bugs2_issuetypes.applies_type';
		const ICON = 'bugs2_issuetypes.icon';
		const IS_TASK = 'bugs2_issuetypes.is_task';
		const REDIRECT_AFTER_REPORTING = 'bugs2_issuetypes.redirect_after_reporting';

		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addVarchar(self::NAME, 50);
			parent::_addInteger(self::APPLIES_TO, 10);
			parent::_addInteger(self::APPLIES_TYPE, 3);
			parent::_addVarchar(self::ICON, 20, 'bug_report');
			parent::_addText(self::DESCRIPTION);
			parent::_addBoolean(self::IS_TASK);
			parent::_addBoolean(self::REDIRECT_AFTER_REPORTING, true);
			parent::_addForeignKeyColumn(self::SCOPE, B2DB::getTable('B2tScopes'), B2tScopes::ID);
		}
		
	}
