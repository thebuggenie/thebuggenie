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
	class B2tLinks extends B2DBTable 
	{

		const B2DBNAME = 'bugs2_links';
		const ID = 'bugs2_links.id';
		const UID = 'bugs2_links.uid';
		const URL = 'bugs2_links.url';
		const LINK_ORDER = 'bugs2_links.link_order';
		const DESCRIPTION = 'bugs2_links.description';
		const ISSUE = 'bugs2_links.issue';
		const SCOPE = 'bugs2_links.scope';

		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addVarchar(self::URL, 300);
			parent::_addInteger(self::LINK_ORDER, 3);
			parent::_addVarchar(self::DESCRIPTION, 100, '');
			parent::_addForeignKeyColumn(self::UID, B2DB::getTable('B2tUsers'), B2tUsers::ID);
			parent::_addForeignKeyColumn(self::ISSUE, B2DB::getTable('B2tIssues'), B2tIssues::ID);
			parent::_addForeignKeyColumn(self::SCOPE, B2DB::getTable('B2tScopes'), B2tScopes::ID);
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
			$crit->addInsert(self::UID, BUGScontext::getUser()->getID());
			$crit->addInsert(self::ISSUE, $issue_id);
			$crit->addInsert(self::URL, $url);
			if ($description !== null)
			{
				$crit->addInsert(self::DESCRIPTION, $description);
			}
			$crit->addInsert(self::SCOPE, BUGScontext::getScope()->getID());
			$res = $this->doInsert($crit);
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
		
	}
