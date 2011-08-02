<?php
/** NOTE : may be integrated to user class **/


	/**
	 * Dashboard class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
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
		
		public static function getViews($target_type)
		{
			return B2DB::getTable('TBGDashboardViewsTable')->getViews(TBGContext::getUser()->getID(), $target_type);;
		}
		
		public static function getAvailableViews($target_type)
		{
			switch ($target_type)
			{
				case TBGDashboardViewsTable::TYPE_USER:
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
					break;				
			}

			return $searches;
		}
		
		public static function setViews($tid, $target_type, $views)
		{
			B2DB::getTable('TBGDashboardViewsTable')->clearViews($tid, $target_type);
			foreach($views as $key => $view)
			{
				B2DB::getTable('TBGDashboardViewsTable')->addView($tid, $target_type, $view);
			}
		}
		
		public static function resetViews($tid, $target_type)
		{
			$views = array();
			self::setUserViews($tid, $target_type, $views);
		}
	}
