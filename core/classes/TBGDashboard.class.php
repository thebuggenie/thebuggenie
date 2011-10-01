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
		
		const DASHBOARD_PROJECT_INFO = 101;
		const DASHBOARD_PROJECT_TEAM = 102;
		const DASHBOARD_PROJECT_CLIENT = 103;
		const DASHBOARD_PROJECT_SUBPROJECTS = 104;
		const DASHBOARD_PROJECT_LAST15 = 105;
		const DASHBOARD_PROJECT_STATISTICS_PRIORITY = 106;
		const DASHBOARD_PROJECT_STATISTICS_STATUS = 111;
		const DASHBOARD_PROJECT_STATISTICS_RESOLUTION = 112;
		const DASHBOARD_PROJECT_STATISTICS_STATE = 113;
		const DASHBOARD_PROJECT_STATISTICS_CATEGORY = 114;
		const DASHBOARD_PROJECT_RECENT_ISSUES = 107;
		const DASHBOARD_PROJECT_RECENT_ACTIVITIES = 108;
		const DASHBOARD_PROJECT_UPCOMING = 109;
		const DASHBOARD_PROJECT_DOWNLOADS = 110;
		
		public static function getViews($tid, $target_type)
		{
			return \b2db\Core::getTable('TBGDashboardViewsTable')->getViews($tid, $target_type);;
		}
		
		public static function getAvailableViews($target_type)
		{
			switch ($target_type)
			{
				case TBGDashboardViewsTable::TYPE_USER:
					$searches = array();
					$searches[self::DASHBOARD_VIEW_PREDEFINED_SEARCH] = array(	TBGContext::PREDEFINED_SEARCH_MY_REPORTED_ISSUES => TBGContext::geti18n()->__('Issues reported by me'),
																				TBGContext::PREDEFINED_SEARCH_MY_ASSIGNED_OPEN_ISSUES => TBGContext::geti18n()->__('Open issues assigned to me'),
																				TBGContext::PREDEFINED_SEARCH_TEAM_ASSIGNED_OPEN_ISSUES => TBGContext::geti18n()->__('Open issues assigned to my teams'),
																				TBGContext::PREDEFINED_SEARCH_PROJECT_OPEN_ISSUES => TBGContext::geti18n()->__('Open issues'),
																				TBGContext::PREDEFINED_SEARCH_PROJECT_CLOSED_ISSUES => TBGContext::geti18n()->__('Closed issues'),
																				TBGContext::PREDEFINED_SEARCH_PROJECT_MOST_VOTED => TBGContext::geti18n()->__('Most voted issues'));
					$searches[self::DASHBOARD_VIEW_LOGGED_ACTION] = array( 0 => TBGContext::geti18n()->__("What you've done recently"));
					if (TBGContext::getUser()->canViewComments())
					{
						$searches[self::DASHBOARD_VIEW_LAST_COMMENTS] = array( 0 => TBGContext::geti18n()->__('Recent comments'));	
					}
					$searches[self::DASHBOARD_VIEW_SAVED_SEARCH] = array();
					$allsavedsearches = \b2db\Core::getTable('TBGSavedSearchesTable')->getAllSavedSearchesByUserIDAndPossiblyProjectID(TBGContext::getUser()->getID());
					foreach ($allsavedsearches as $savedsearches)
					{
						foreach ($savedsearches as $a_savedsearch)
						{
							$searches[self::DASHBOARD_VIEW_SAVED_SEARCH][$a_savedsearch->get(TBGSavedSearchesTable::ID)] = $a_savedsearch->get(TBGSavedSearchesTable::NAME);
						}
					}
					break;
				case TBGDashboardViewsTable::TYPE_PROJECT:
					$issuetype_icons = array();
					foreach (TBGIssuetype::getIcons() as $key => $descr)
					{
						$issuetype_icons[] = TBGContext::geti18n()->__('Recent issues: %type%', array('%type%' => $descr));
					}
					
					$searches = array();
					$searches[self::DASHBOARD_PROJECT_INFO] = array( 0 => TBGContext::geti18n()->__('About this project'));
					$searches[self::DASHBOARD_PROJECT_TEAM] = array( 0 => TBGContext::geti18n()->__('Project team'));
					$searches[self::DASHBOARD_PROJECT_CLIENT] = array( 0 => TBGContext::geti18n()->__('Project client'));
					$searches[self::DASHBOARD_PROJECT_SUBPROJECTS] = array( 0 => TBGContext::geti18n()->__('Subprojects'));
					$searches[self::DASHBOARD_PROJECT_LAST15] = array( 0 => TBGContext::geti18n()->__('Graph of closed vs open issues, past 15 days'));
					$searches[self::DASHBOARD_PROJECT_STATISTICS_PRIORITY] = array( 0 => TBGContext::geti18n()->__('Statistics by priority'));
					$searches[self::DASHBOARD_PROJECT_STATISTICS_CATEGORY] = array( 0 => TBGContext::geti18n()->__('Statistics by category'));
					$searches[self::DASHBOARD_PROJECT_STATISTICS_STATUS] = array( 0 => TBGContext::geti18n()->__('Statistics by status'));
					$searches[self::DASHBOARD_PROJECT_STATISTICS_RESOLUTION] = array( 0 => TBGContext::geti18n()->__('Statistics by resolution'));
					$searches[self::DASHBOARD_PROJECT_RECENT_ISSUES] = $issuetype_icons;
					$searches[self::DASHBOARD_PROJECT_RECENT_ACTIVITIES] = array( 0 => TBGContext::geti18n()->__('Recent activities'));
					$searches[self::DASHBOARD_PROJECT_UPCOMING] = array( 0 => TBGContext::geti18n()->__('Upcoming milestones and deadlines'));
					$searches[self::DASHBOARD_PROJECT_DOWNLOADS] = array( 0 => TBGContext::geti18n()->__('Latest downloads'));
					break;				
			}

			return $searches;
		}
		
		public static function setViews($tid, $target_type, $views)
		{
			\b2db\Core::getTable('TBGDashboardViewsTable')->clearViews($tid, $target_type);
			foreach($views as $key => $view)
			{
				\b2db\Core::getTable('TBGDashboardViewsTable')->addView($tid, $target_type, $view);
			}
		}
		
		public static function resetViews($tid, $target_type)
		{
			$views = array();
			self::setUserViews($tid, $target_type, $views);
		}
	}
