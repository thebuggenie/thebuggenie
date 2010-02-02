<?php

	/**
	 * Edition assignees table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Edition assignees table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 */
	class TBGEditionAssigneesTable extends B2DBTable 
	{

		const B2DBNAME = 'editionassignees';
		const ID = 'editionassignees.id';
		const SCOPE = 'editionassignees.scope';
		const UID = 'editionassignees.uid';
		const CID = 'editionassignees.cid';
		const TID = 'editionassignees.tid';
		const EDITION_ID = 'editionassignees.edition_id';
		const TARGET_TYPE = 'editionassignees.target_type';
		
		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addInteger(self::TARGET_TYPE, 5);
			parent::_addForeignKeyColumn(self::EDITION_ID, B2DB::getTable('TBGEditionsTable'), TBGEditionsTable::ID);
			parent::_addForeignKeyColumn(self::UID, B2DB::getTable('TBGUsersTable'), TBGUsersTable::ID);
			parent::_addForeignKeyColumn(self::TID, B2DB::getTable('TBGTeamsTable'), TBGTeamsTable::ID);
			parent::_addForeignKeyColumn(self::CID, B2DB::getTable('TBGCustomersTable'), TBGCustomersTable::ID);
			parent::_addForeignKeyColumn(self::SCOPE, B2DB::getTable('TBGScopesTable'), TBGScopesTable::ID);
		}
		
		public function getByEditionIDs($edition_ids)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::EDITION_ID, $edition_ids, B2DBCriteria::DB_IN);
			$res = $this->doSelect($crit);
			return $res;
		}
		
		public function getProjectsByUserID($user_id)
		{
			$projects = array();
			
			$crit = $this->getCriteria();
			$crit->addWhere(self::UID, $user_id);
			if ($res = $this->doSelect($crit))
			{
				foreach ($res->getNextRow() as $row)
				{
					$projects[$row->get(TBGEditionsTable::PROJECT)] = TBGFactory::projectLab($row->get(TBGEditionsTable::PROJECT)); 
				}
			}
			return $projects;
		}
		
	}
