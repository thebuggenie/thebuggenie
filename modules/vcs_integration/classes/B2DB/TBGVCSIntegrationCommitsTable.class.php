<?php
	/**
	 * B2DB Table, vcs_integration -> VCSIntegrationCommitsTable
	 *
	 * @author Philip Kent <kentphilip@gmail.com>
	 * @version 3.2
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage vcs_integration
	 */

	/**
	 * B2DB Table, vcs_integration -> VCSIntegrationCommitsTable
	 *
	 * @package thebuggenie
	 * @subpackage vcs_integration
	 */
	class TBGVCSIntegrationCommitsTable extends TBGB2DBTable 
	{

		const B2DB_TABLE_VERSION = 1;
		const B2DBNAME = 'vcsintegration_commits';
		const ID = 'vcsintegration_commits.id';
		const SCOPE = 'vcsintegration_commits.scope';
		const LOG = 'vcsintegration_commits.log';
		const OLD_REV = 'vcsintegration_commits.old_rev';
		const NEW_REV = 'vcsintegration_commits.new_rev';
		const AUTHOR = 'vcsintegration_commits.author';
		const DATE = 'vcsintegration_commits.date';
		const DATA = 'vcsintegration_commits.data';
					
		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addText(self::LOG, false);
			parent::_addVarchar(self::OLD_REV, 40);
			parent::_addVarchar(self::NEW_REV, 40);
			parent::_addVarchar(self::AUTHOR, 100);
			parent::_addInteger(self::DATE, 10);
			parent::_addText(self::DATA, false);
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(),  TBGScopesTable::ID);
		}
		
		/**
		 * Return an instance of this table
		 *
		 * @return TBGVCSIntegrationCommitsTable
		 */
		public static function getTable()
		{
			return B2DB::getTable('TBGVCSIntegrationCommitsTable');
		}

		/**
		 * Add a commit entry to the database
		 *
		 * @param $id Issue ID
		 * @param $action A/D/U action applied to file
		 * @param $commit_msg Log message
		 * @param $file File changed
		 * @param $new_rev New revision
		 * @param $old_rev Old revision
		 * @param $uid UID of changer
		 * @param $date POSIX timestamp of change
		 */
		/*public static function addEntry($id, $action, $commit_msg, $file, $new_rev, $old_rev, $uid, $date)
		{
			$crit = new B2DBCriteria();
			$crit->addInsert(TBGVCSIntegrationTable::ISSUE_NO, $id); 
			$crit->addInsert(TBGVCSIntegrationTable::ACTION, $action);
			$crit->addInsert(TBGVCSIntegrationTable::LOG, $commit_msg);
			$crit->addInsert(TBGVCSIntegrationTable::FILE_NAME, $file); 
			$crit->addInsert(TBGVCSIntegrationTable::NEW_REV, $new_rev);
			$crit->addInsert(TBGVCSIntegrationTable::OLD_REV, $old_rev);
			$crit->addInsert(TBGVCSIntegrationTable::AUTHOR, $uid);
			if ($date == null)
			{
				$crit->addInsert(TBGVCSIntegrationTable::DATE, time());
			}
			else
			{
				$crit->addInsert(TBGVCSIntegrationTable::DATE, $date);
			}
			$crit->addInsert(TBGVCSIntegrationTable::SCOPE, TBGContext::getScope()->getID());
			B2DB::getTable('TBGVCSIntegrationTable')->doInsert($crit);
		} */
	}

