<?php
	/**
	 * B2DB Table, vcs_integration -> VCSIntegrationSettingsTable
	 *
	 * @author Philip Kent <kentphilip@gmail.com>
	 * @version 3.2
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage vcs_integration
	 */

	/**
	 * B2DB Table, vcs_integration -> VCSIntegrationSettingsTable
	 *
	 * @package thebuggenie
	 * @subpackage vcs_integration
	 */
	class TBGVCSIntegrationSettingsTable extends TBGB2DBTable 
	{

		const B2DB_TABLE_VERSION = 1;
		const B2DBNAME = 'vcsintegration_settings';
		const ID = 'vcsintegration_settings.id';
		const SCOPE = 'vcsintegration_settings.scope';
		const PROJECT_ID = 'vcsintegration_settings.project_id';
		const ENABLED = 'vcsintegration_settings.enabled';
		const KEYWORDS = 'vcsintegration_settings.keywords';
		const ACCESS_METHOD = 'vcsintegration_settings.access_method';
		const PASSKEY = 'vcsintegration_settings.passkey';
		const BROWSER_URL = 'vcsintegration_settings.browser_url';
		const COMMITS_URL = 'vcsintegration_settings.commits_url';
		const LOG_URL = 'vcsintegration_settings.log_url';
		const BLOB_URL = 'vcsintegration_settings.blob_url';
		const DIFF_URL = 'vcsintegration_settings.diff_url';
					
		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addInteger(self::ENABLED, 3);
			parent::_addVarchar(self::KEYWORDS, 200);
			parent::_addInteger(self::ACCESS_METHOD, 1);
			parent::_addVarchar(self::PASSKEY, 100);
			parent::_addVarchar(self::BROWSER_URL, 200);
			parent::_addVarchar(self::COMMITS_URL, 200);
			parent::_addVarchar(self::LOG_URL, 200);
			parent::_addVarchar(self::BLOB_URL, 200);
			parent::_addVarchar(self::DIFF_URL, 200);
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(),  TBGScopesTable::ID);
			parent::_addForeignKeyColumn(self::PROJECT_ID, TBGProjectsTable::getTable(),  TBGProjectsTable::ID);
		}
		
		/**
		 * Return an instance of this table
		 *
		 * @return TBGVCSIntegrationSettingsTable
		 */
		public static function getTable()
		{
			return B2DB::getTable('TBGVCSIntegrationSettingsTable');
		}
	
	}

