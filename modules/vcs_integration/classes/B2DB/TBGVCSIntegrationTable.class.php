<?php

	use b2db\Core,
		b2db\Criteria,
		b2db\Criterion;
	
	/**
	 * B2DB Table, vcs_integration -> VCSIntegrationTable
	 *
	 * @author Philip Kent <kentphilip@gmail.com>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage vcs_integration
	 */

	/**
	 * B2DB Table, vcs_integration -> VCSIntegrationTable
	 *
	 * @package thebuggenie
	 * @subpackage vcs_integration
	 *
	 * @Table(name="vcsintegration")
	 */
	class TBGVCSIntegrationTable extends TBGB2DBTable 
	{

		const B2DB_TABLE_VERSION = 1;
		const B2DBNAME = 'vcsintegration';
		const ID = 'vcsintegration.id';
		const SCOPE = 'vcsintegration.scope';
		const ISSUE_NO = 'vcsintegration.issue_no';
		const FILE_NAME = 'vcsintegration.file_name';
		const LOG = 'vcsintegration.log';
		const OLD_REV = 'vcsintegration.old_rev';
		const NEW_REV = 'vcsintegration.new_rev';
		const AUTHOR = 'vcsintegration.author';
		const DATE = 'vcsintegration.date';
		const ACTION = 'vcsintegration.action';
					
		public function _initialize()
		{
			parent::_setup(self::B2DBNAME, self::ID);
			parent::_addText(self::FILE_NAME, false);
			parent::_addText(self::LOG, false);
			parent::_addVarchar(self::OLD_REV, 40);
			parent::_addVarchar(self::NEW_REV, 40);
			parent::_addVarchar(self::AUTHOR, 100);
			parent::_addVarchar(self::ACTION, 1);
			parent::_addInteger(self::DATE, 10);
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(),  TBGScopesTable::ID);
			parent::_addForeignKeyColumn(self::ISSUE_NO, TBGIssuesTable::getTable(),  TBGIssuesTable::ID);
		}
		
	}

