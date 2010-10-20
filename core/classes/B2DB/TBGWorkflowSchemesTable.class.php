<?php

	/**
	 * Workflow schemes table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 ** @version 3.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Workflow schemes table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 */
	class TBGWorkflowSchemesTable extends B2DBTable
	{

		const B2DBNAME = 'workflow_schemes';
		const ID = 'workflow_schemes.id';
		const SCOPE = 'workflow_schemes.scope';
		const NAME = 'workflow_schemes.name';
		const DESCRIPTION = 'workflow_schemes.description';

		/**
		 * Return an instance of this table
		 *
		 * @return TBGWorkflowSchemesTable
		 */
		public static function getTable()
		{
			return B2DB::getTable('TBGWorkflowSchemesTable');
		}

		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
			parent::_addVarchar(self::NAME, 200);
			parent::_addText(self::DESCRIPTION, false);
		}

	}