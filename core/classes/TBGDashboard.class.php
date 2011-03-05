<?php
/** NOTE : may be integrated to user class **/


	/**
	 * Dashboard class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 ** @version 3.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage main
	 */

	/**
	 * Dashboard class
	 *
	 * @package thebuggenie
	 * @subpackage main
	 */
	class TBGDashboard extends TBGB2DBTable
	{
		
		const B2DB_TABLE_VERSION = 1;
		const DASHBOARD_VIEW_PREDEFINED_SEARCH = 1;
		const DASHBOARD_VIEW_SAVED_SEARCH = 2;
		const DASHBOARD_VIEW_LOGGED_ACTION = 3;
		const DASHBOARD_VIEW_LAST_COMMENTS = 4;
		
		public static function getUserViews()
		{
			return B2DB::getTable('TBGUserDashboardViewsTable')->getViews(TBGContext::getUser()->getID());;
		}
		
		public static function getAvailableUserViews()
		{
			$searches = array();
			$searches[self::DASHBOARD_VIEW_PREDEFINED_SEARCH] = array(	TBGContext::PREDEFINED_SEARCH_MY_REPORTED_ISSUES => 'Issues reported by me',
																		TBGContext::PREDEFINED_SEARCH_MY_ASSIGNED_OPEN_ISSUES => 'Open issues assigned to me',
																		TBGContext::PREDEFINED_SEARCH_TEAM_ASSIGNED_OPEN_ISSUES => 'Open issues assigned to my teams',
																		TBGContext::PREDEFINED_SEARCH_PROJECT_OPEN_ISSUES => 'Open issues',
																		TBGContext::PREDEFINED_SEARCH_PROJECT_CLOSED_ISSUES => 'Closed issues',
																		TBGContext::PREDEFINED_SEARCH_PROJECT_MOST_VOTED => 'Most voted issues');
			$searches[self::DASHBOARD_VIEW_LOGGED_ACTION] = array( 0 => 'What you\'ve done recently');
			if (TBGContext::getUser()->canViewComments())
			{
				$searches[self::DASHBOARD_VIEW_LAST_COMMENTS] = array( 0 => 'Recent comments');	
			}
			$searches[self::DASHBOARD_VIEW_SAVED_SEARCH] = array();
			$allsavedsearches = B2DB::getTable('TBGSavedSearchesTable')->getAllSavedSearchesByUserIDAndPossiblyProjectID(TBGContext::getUser()->getID());
			foreach ($allsavedsearches as $savedsearches)
			{
				foreach ($savedsearches as $a_savedsearch)
				{
					$searches[self::DASHBOARD_VIEW_SAVED_SEARCH][$a_savedsearch->get(TBGSavedSearchesTable::ID)] = $a_savedsearch->get(TBGSavedSearchesTable::NAME);
				}
			}
			
			return $searches;
		}
		
		public static function setUserViews($uid, $views)
		{
			B2DB::getTable('TBGUserDashboardViewsTable')->clearViews($uid);
			foreach($views as $key => $view)
			{
				B2DB::getTable('TBGUserDashboardViewsTable')->addView($uid, $view);
			}
		}
		
		public static function resetUserViews($uid)
		{
			$views = array();
			self::setUserViews($uid, $views);
		}
	}
