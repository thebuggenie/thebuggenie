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
	class B2tEditionAssignees extends B2DBTable 
	{

		const B2DBNAME = 'bugs2_editionassignees';
		const ID = 'bugs2_editionassignees.id';
		const SCOPE = 'bugs2_editionassignees.scope';
		const UID = 'bugs2_editionassignees.uid';
		const CID = 'bugs2_editionassignees.cid';
		const TID = 'bugs2_editionassignees.tid';
		const EDITION_ID = 'bugs2_editionassignees.edition_id';
		const TARGET_TYPE = 'bugs2_editionassignees.target_type';
		
		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addInteger(self::TARGET_TYPE, 5);
			parent::_addForeignKeyColumn(self::EDITION_ID, B2DB::getTable('B2tEditions'), B2tEditions::ID);
			parent::_addForeignKeyColumn(self::UID, B2DB::getTable('B2tUsers'), B2tUsers::ID);
			parent::_addForeignKeyColumn(self::TID, B2DB::getTable('B2tTeams'), B2tTeams::ID);
			parent::_addForeignKeyColumn(self::CID, B2DB::getTable('B2tCustomers'), B2tCustomers::ID);
			parent::_addForeignKeyColumn(self::SCOPE, B2DB::getTable('B2tScopes'), B2tScopes::ID);
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
					$projects[$row->get(B2tEditions::PROJECT)] = BUGSfactory::projectLab($row->get(B2tEditions::PROJECT)); 
				}
			}
			return $projects;
		}
		
	}
