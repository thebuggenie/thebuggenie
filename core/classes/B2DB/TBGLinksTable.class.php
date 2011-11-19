<?php

	use b2db\Core,
		b2db\Criteria,
		b2db\Criterion;

	/**
	 * Links table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Links table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 *
	 * @Table(name="links")
	 */
	class TBGLinksTable extends TBGB2DBTable 
	{

		const B2DB_TABLE_VERSION = 1;
		const B2DBNAME = 'links';
		const ID = 'links.id';
		const UID = 'links.uid';
		const URL = 'links.url';
		const LINK_ORDER = 'links.link_order';
		const DESCRIPTION = 'links.description';
		const TARGET_TYPE = 'links.target_type';
		const TARGET_ID = 'links.target_id';
		const SCOPE = 'links.scope';

		public function _initialize()
		{
			parent::_setup(self::B2DBNAME, self::ID);
			parent::_addVarchar(self::URL, 300);
			parent::_addInteger(self::LINK_ORDER, 3);
			parent::_addVarchar(self::TARGET_TYPE, 30);
			parent::_addInteger(self::TARGET_ID, 10);
			parent::_addVarchar(self::DESCRIPTION, 100, '');
			parent::_addForeignKeyColumn(self::UID, TBGUsersTable::getTable(), TBGUsersTable::ID);
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
		}
		
		public function addLink($target_type, $target_id = 0, $url = null, $description = null, $link_order = null, $scope = null)
		{
			$scope = ($scope === null) ? TBGContext::getScope()->getID() : $scope; 
			if ($link_order === null)
			{
				$crit = $this->getCriteria();
				$crit->addSelectionColumn(self::LINK_ORDER, 'max_order', Criteria::DB_MAX, '', '+1');
				$crit->addWhere(self::TARGET_TYPE, $target_type);
				$crit->addWhere(self::TARGET_ID, $target_id);
				$crit->addWhere(self::SCOPE, $scope);
	
				$row = $this->doSelectOne($crit);
				$link_order = ($row->get('max_order')) ? $row->get('max_order') : 1;
			}
			
			$crit = $this->getCriteria();
			$crit->addInsert(self::TARGET_TYPE, $target_type);
			$crit->addInsert(self::TARGET_ID, $target_id);
			$crit->addInsert(self::URL, $url);
			$crit->addInsert(self::DESCRIPTION, $description);
			$crit->addInsert(self::LINK_ORDER, $link_order);
			$crit->addInsert(self::UID, (TBGContext::getUser() instanceof TBGUser) ? TBGContext::getUser()->getID() : 0);
			$crit->addInsert(self::SCOPE, $scope);
			$res = $this->doInsert($crit);

			return $res->getInsertID();
		}
		
		public function getLinks($target_type, $target_id = 0)
		{
			$links = array();
			$crit = $this->getCriteria();
			$crit->addWhere(self::TARGET_TYPE, $target_type);
			$crit->addWhere(self::TARGET_ID, $target_id);
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$crit->addOrderBy(self::LINK_ORDER, Criteria::SORT_ASC);
			if ($res = $this->doSelect($crit, 'none'))
			{
				while ($row = $res->getNextRow())
				{
					$links[$row->get(self::ID)] = array('target_type' => $row->get(self::TARGET_TYPE), 'target_id' => $row->get(self::TARGET_ID), 'url' => $row->get(self::URL), 'description' => $row->get(self::DESCRIPTION));
				}
			}
			return $links;
		}
		
		public function addLinkToIssue($issue_id, $url, $description = null)
		{
			return $this->addLink('issue', $issue_id, $url, $description);
		}
		
		public function getMainLinks()
		{
			return $this->getLinks('main_menu');
		}
		
		public function getByIssueID($issue_id)
		{
			return $this->getLinks('issue', $issue_id);
		}
		
		public function removeByTargetTypeTargetIDandLinkID($target_type, $target_id, $link_id = null)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::TARGET_TYPE, $target_type);
			$crit->addWhere(self::TARGET_ID, $target_id);
			if ($link_id !== null)
			{
				$crit->addWhere(self::ID, $link_id);
			}
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$res = $this->doDelete($crit);
			
			return true;
		}

		public function removeByIssueIDandLinkID($issue_id, $link_id)
		{
			return $this->removeByTargetTypeTargetIDandLinkID('issue', $issue_id, $link_id);
		}
		
		public function addMainMenuLink($url = null, $description = null, $link_order = null, $scope = null)
		{
			return $this->addLink('main_menu', 0, $url, $description, $link_order, $scope);
		}

		public function loadFixtures(TBGScope $scope)
		{
			$scope_id = $scope->getID();
			
			$this->addMainMenuLink('http://www.thebuggenie.com', 'The Bug Genie homepage', 1, $scope_id);
			$this->addMainMenuLink('http://www.thebuggenie.com/forum', 'The Bug Genie forums', 2, $scope_id);
			$this->addMainMenuLink(null, null, 3, $scope_id);
			$this->addMainMenuLink('http://thebuggenie.com/thebuggenie', 'Online issue tracker', 4, $scope_id);
			$this->addMainMenuLink('', "''This is the issue tracker for The Bug Genie''", 5, $scope_id);
			$this->addMainMenuLink(null, null, 6, $scope_id);
			$this->addMainMenuLink('http://thebuggenie.wordpress.com/', 'The Bug Genie team blog', 7, $scope_id);
			$this->addMainMenuLink('', "''Stay up to date on the latest development''", 8, $scope_id);
		}
		
	}
