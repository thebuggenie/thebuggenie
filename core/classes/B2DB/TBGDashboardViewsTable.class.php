<?php

	use b2db\Core,
		b2db\Criteria,
		b2db\Criterion,
		b2db\Resultset;

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
	class TBGDashboardViewsTable extends TBGB2DBTable
	{

		const B2DB_TABLE_VERSION = 1;
		const B2DBNAME = 'dashboard_views';
		const ID = 'dashboard_views.id';
		const TYPE = 'dashboard_views.type';
		const VIEW = 'dashboard_views.view';
		const TID = 'dashboard_views.tid';
		const PID = 'dashboard_views.pid';
		const TARGET_TYPE = 'dashboard_views.target_type';
		const SCOPE = 'dashboard_views.scope';

		const TYPE_USER = 1;
		const TYPE_PROJECT = 2;
		const TYPE_TEAM = 3;
		const TYPE_CLIENT = 4;

		/**
		 * Return an instance of this table
		 *
		 * @return TBGDashboardViewsTable
		 */
		public static function getTable()
		{
			return Core::getTable('TBGDashboardViewsTable');
		}

		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addInteger(self::TYPE);
			parent::_addInteger(self::VIEW);
			parent::_addInteger(self::PID);
			parent::_addInteger(self::TARGET_TYPE);
			parent::_addInteger(self::TID);
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
		}
		
		public function addView($target_id, $target_type, $view)
		{
			if ($view['type'])
			{
				$view_id = (array_key_exists('id', $view)) ? $view['id'] : 0;
				$crit = $this->getCriteria();
				$crit->addInsert(self::TID, $target_id);
				$crit->addInsert(self::TARGET_TYPE, $target_type);
				$crit->addInsert(self::TYPE, $view['type']);
				$crit->addInsert(self::VIEW, $view_id);
				$crit->addInsert(self::SCOPE, TBGContext::getScope()->getID());
				$this->doInsert($crit);
			}
		}
		
		public function clearViews($target_id, $target_type)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::TID, $target_id);
			$crit->addWhere(self::TARGET_TYPE, $target_type);
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$this->doDelete($crit);
		}

		public function getViews($target_id, $target_type)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::TID, $target_id);
			$crit->addWhere(self::TARGET_TYPE, $target_type);
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$crit->addOrderBy(self::ID);
			$res = $this->doSelect($crit);

			return $res;
		}
		
		public function setDefaultViews($target_id, $target_type)
		{
			switch ($target_type)
			{
				case self::TYPE_USER:
					$this->clearViews($target_id, $target_type);
					$this->addView($target_id, $target_type, array('type' => TBGDashboardView::VIEW_PREDEFINED_SEARCH, 'id' => TBGContext::PREDEFINED_SEARCH_MY_REPORTED_ISSUES));
					$this->addView($target_id, $target_type, array('type' => TBGDashboardView::VIEW_PREDEFINED_SEARCH, 'id' => TBGContext::PREDEFINED_SEARCH_MY_ASSIGNED_OPEN_ISSUES));
					$this->addView($target_id, $target_type, array('type' => TBGDashboardView::VIEW_PREDEFINED_SEARCH, 'id' => TBGContext::PREDEFINED_SEARCH_TEAM_ASSIGNED_OPEN_ISSUES));
					$this->addView($target_id, $target_type, array('type' => TBGDashboardView::VIEW_LOGGED_ACTIONS));
					break;
				case self::TYPE_PROJECT:
					$this->clearViews($target_id, $target_type);
					$this->addView($target_id, $target_type, array('type' => TBGDashboardView::VIEW_PROJECT_INFO));
					$this->addView($target_id, $target_type, array('type' => TBGDashboardView::VIEW_PROJECT_TEAM));
					$this->addView($target_id, $target_type, array('type' => TBGDashboardView::VIEW_PROJECT_DOWNLOADS));
					$this->addView($target_id, $target_type, array('type' => TBGDashboardView::VIEW_PROJECT_STATISTICS_LAST15));
					$this->addView($target_id, $target_type, array('type' => TBGDashboardView::VIEW_PROJECT_STATISTICS_PRIORITY));
					$this->addView($target_id, $target_type, array('type' => TBGDashboardView::VIEW_PROJECT_STATISTICS_STATUS));
					break;
			}

		}
	}