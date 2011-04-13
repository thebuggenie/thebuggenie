<?php

	/**
	 * User dashboard views table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * User dashboard views table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 */
	class TBGUserDashboardViewsTable extends TBGB2DBTable
	{

		const B2DB_TABLE_VERSION = 1;
		const B2DBNAME = 'user_dashboard_views';
		const ID = 'user_dashboard_views.id';
		const TYPE = 'user_dashboard_views.type';
		const VIEW = 'user_dashboard_views.view';
		const UID = 'user_dashboard_views.uid';
		const PID = 'user_dashboard_view.pid';
		const SCOPE = 'user_dashboard_views.scope';

		/**
		 * Return an instance of this table
		 *
		 * @return TBGUserDashboardViewsTable
		 */
		public static function getTable()
		{
			return B2DB::getTable('TBGUserDashboardViewsTable');
		}

		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addInteger(self::TYPE);
			parent::_addInteger(self::VIEW);
			parent::_addInteger(self::PID);
			parent::_addForeignKeyColumn(self::UID, TBGUsersTable::getTable(), TBGUsersTable::ID);
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
		}
		
		public function addView($user_id, $view)
		{
			if ($view['type'])
			{
				$crit = $this->getCriteria();
				$crit->addInsert(self::UID, $user_id);
				$crit->addInsert(self::TYPE, $view['type']);
				$crit->addInsert(self::VIEW, $view['id']);
				$crit->addInsert(self::SCOPE, TBGContext::getScope()->getID());
				$this->doInsert($crit);
			}
		}
		
		public function clearViews($user_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::UID, $user_id);
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$this->doDelete($crit);
		}

		public function getViews($user_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::UID, $user_id);
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$crit->addOrderBy(self::ID);
			$res = $this->doSelect($crit);
			if ($res instanceof B2DBResultset)
			{
				return $res->getAllRows();
			}
			return array();
		}
		
		public function setDefaultViews($user_id)
		{
			$this->clearViews($user_id);
			$this->addView($user_id, array('type' => TBGDashboard::DASHBOARD_VIEW_PREDEFINED_SEARCH, 'id' => TBGContext::PREDEFINED_SEARCH_MY_REPORTED_ISSUES));
			$this->addView($user_id, array('type' => TBGDashboard::DASHBOARD_VIEW_PREDEFINED_SEARCH, 'id' => TBGContext::PREDEFINED_SEARCH_MY_ASSIGNED_OPEN_ISSUES));
			$this->addView($user_id, array('type' => TBGDashboard::DASHBOARD_VIEW_PREDEFINED_SEARCH, 'id' => TBGContext::PREDEFINED_SEARCH_TEAM_ASSIGNED_OPEN_ISSUES));
			$this->addView($user_id, array('type' => TBGDashboard::DASHBOARD_VIEW_LOGGED_ACTION, 'id' => 0));
		}
	}