<?php

	/**
	 * Links table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Links table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 */
	class TBGLinksTable extends B2DBTable 
	{

		const B2DBNAME = 'links';
		const ID = 'links.id';
		const UID = 'links.uid';
		const URL = 'links.url';
		const LINK_ORDER = 'links.link_order';
		const DESCRIPTION = 'links.description';
		const ISSUE = 'links.issue';
		const SCOPE = 'links.scope';

		/**
		 * Return an instance of this table
		 * 
		 * @return TBGLinksTable
		 */
		public static function getTable()
		{
			return B2DB::getTable('TBGLinksTable');
		}
		
		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addVarchar(self::URL, 300);
			parent::_addInteger(self::LINK_ORDER, 3);
			parent::_addVarchar(self::DESCRIPTION, 100, '');
			parent::_addForeignKeyColumn(self::UID, B2DB::getTable('TBGUsersTable'), TBGUsersTable::ID);
			parent::_addForeignKeyColumn(self::ISSUE, B2DB::getTable('TBGIssuesTable'), TBGIssuesTable::ID);
			parent::_addForeignKeyColumn(self::SCOPE, B2DB::getTable('TBGScopesTable'), TBGScopesTable::ID);
		}
		
		public function getMainLinks()
		{
			$links = array();
			$crit = $this->getCriteria();
			$crit->addWhere(self::ISSUE, 0);
			$crit->addOrderBy(self::LINK_ORDER, B2DBCriteria::SORT_ASC);
			if ($res = $this->doSelect($crit))
			{
				while ($row = $res->getNextRow())
				{
					$links[$row->get(self::ID)] = array('url' => $row->get(self::URL), 'description' => $row->get(self::DESCRIPTION));
				}
			}
			return $links;
		}
		
		public function addLinkToIssue($issue_id, $url, $description = null)
		{
			$crit = $this->getCriteria();
			$crit->addInsert(self::UID, TBGContext::getUser()->getID());
			$crit->addInsert(self::ISSUE, $issue_id);
			$crit->addInsert(self::URL, $url);
			if ($description !== null)
			{
				$crit->addInsert(self::DESCRIPTION, $description);
			}
			$crit->addInsert(self::SCOPE, TBGContext::getScope()->getID());
			$res = $this->doInsert($crit);

			return $res->getInsertID();
		}
		
		public function getByIssueID($issue_id)
		{
			$links = array();
			$crit = $this->getCriteria();
			$crit->addWhere(self::ISSUE, $issue_id);
			$crit->addOrderBy(self::ID, B2DBCriteria::SORT_ASC);
			if ($res = $this->doSelect($crit))
			{
				while ($row = $res->getNextRow())
				{
					$links[$row->get(self::ID)] = array('url' => $row->get(self::URL), 'description' => $row->get(self::DESCRIPTION));
				}
			}
			return $links;
		}

		public function removeByIssueIDandLinkID($issue_id, $link_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::ISSUE, $issue_id);
			$crit->addWhere(self::ID, $link_id);
			$res = $this->doDelete($crit);
		}
		
		public function addMainMenuLink($url = null, $description = null, $link_order = null)
		{
			if ($link_order === null)
			{
				$crit = $this->getCriteria();
				$crit->addSelectionColumn(self::LINK_ORDER, 'max_order', B2DBCriteria::DB_MAX, '', '+1');
				$crit->addWhere(self::ISSUE, '');
	
				$row = $this->doSelectOne($crit);
				$link_order = ($row->get('max_order')) ? $row->get('max_order') : 1;
			}
			
			$crit = $this->getCriteria();
			$crit->addInsert(self::URL, (string) $url);
			$crit->addInsert(self::DESCRIPTION, (string) $description);
			$crit->addInsert(self::LINK_ORDER, $link_order);
			$id = $this->doInsert($crit)->getInsertID();
			
			return $id;
		}

		public function loadFixtures($scope)
		{
			$this->addMainMenuLink('http://www.thebuggenie.com', 'The Bug Genie homepage', 1);
			$this->addMainMenuLink('http://www.thebuggenie.com/forum', 'The Bug Genie forums', 2);
			$this->addMainMenuLink();
			$this->addMainMenuLink('http://www.thebuggenie.com/b2', 'Online issue tracker');
		}
		
	}
