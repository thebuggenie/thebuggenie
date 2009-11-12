<?php

	/**
	 * Team members table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Team members table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 */
	class B2tTeamMembers extends B2DBTable 
	{

		const B2DBNAME = 'teammembers';
		const ID = 'teammembers.id';
		const SCOPE = 'teammembers.scope';
		const UID = 'teammembers.uid';
		const TID = 'teammembers.tid';
		
		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			
			parent::_addForeignKeyColumn(self::UID, B2DB::getTable('B2tUsers'), B2tUsers::ID);
			parent::_addForeignKeyColumn(self::TID, B2DB::getTable('B2tTeams'), B2tTeams::ID);
			parent::_addForeignKeyColumn(self::SCOPE, B2DB::getTable('B2tScopes'), B2tScopes::ID);
		}

		public function getUIDsForTeamID($team_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::TID, $team_id);

			$uids = array();
			if ($res = $this->doSelect($crit))
			{
				while ($row = $res->getNextRow())
				{
					$uids[$row->get(self::UID)] = $row->get(self::UID);
				}
			}

			return $uids;
		}
		
	}
