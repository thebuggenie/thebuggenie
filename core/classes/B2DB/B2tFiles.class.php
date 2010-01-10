<?php

	/**
	 * Files table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Files table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 */
	class B2tFiles extends B2DBTable 
	{

		const B2DBNAME = 'files';
		const ID = 'files.id';
		const SCOPE = 'files.scope';
		const UID = 'files.uid';
		const ISSUE = 'files.issue';
		const UPLOADED_AT = 'files.uploaded_at';
		const FILENAME = 'files.filename';
		const DESCRIPTION = 'files.description';

		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addForeignKeyColumn(self::UID, B2DB::getTable('B2tUsers'), B2tUsers::ID);
			parent::_addForeignKeyColumn(self::SCOPE, B2DB::getTable('B2tScopes'), B2tScopes::ID);
			parent::_addForeignKeyColumn(self::ISSUE, B2DB::getTable('B2tIssues'), B2tIssues::ID);
			parent::_addVarchar(self::FILENAME, 250);
			parent::_addInteger(self::UPLOADED_AT, 10);
			parent::_addText(self::DESCRIPTION, false);
		}
		
		public function addFileToIssue($issue_id, $filename, $description = null)
		{
			$crit = $this->getCriteria();
			$crit->addInsert(self::ISSUE, $issue_id);
			$crit->addInsert(self::UID, BUGScontext::getUser()->getUID());
			$crit->addInsert(self::FILENAME, $filename);
			$crit->addInsert(self::SCOPE, BUGScontext::getScope()->getID());
			if ($description !== null)
			{
				$crit->addInsert(self::DESCRIPTION, $description);
			}
			$res = $this->doInsert($crit);
		}
		
		public function getByIssueID($issue_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::ISSUE, $issue_id);
			$res = $this->doSelect($crit);
			
			$ret_arr = array();

			if ($res)
			{
				while ($row = $res->getNextRow())
				{
					$ret_arr[$row->get(B2tFiles::ID)] = array('filename' => $row->get(B2tFiles::FILENAME), 'description' => $row->get(B2tFiles::DESCRIPTION), 'timestamp' => $row->get(B2tFiles::UPLOADED_AT));
				}
			}
			
			return $ret_arr;
		}

		public function removeByIssueIDandFileID($issue_id, $file_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::ISSUE, $issue_id);
			if ($res = $this->doSelectById($file_id, $crit))
			{
				$this->doDelete($crit);
			}
			return $res;
		}
		
	}
