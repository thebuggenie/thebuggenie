<?php

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
	 *
	 * @Table(name="TBGDashboardViewsTable")
	 */
	class TBGDashboardView extends TBGIdentifiableScopedClass
	{

		const VIEW_PREDEFINED_SEARCH = 1;
		const VIEW_SAVED_SEARCH = 2;
		const VIEW_LOGGED_ACTIONS = 3;
		const VIEW_RECENT_COMMENTS = 4;
		const VIEW_FRIENDS = 5;
		const VIEW_PROJECTS = 6;
		const VIEW_MILESTONES = 7;

		const VIEW_PROJECT_INFO = 101;
		const VIEW_PROJECT_TEAM = 102;
		const VIEW_PROJECT_CLIENT = 103;
		const VIEW_PROJECT_SUBPROJECTS = 104;
		const VIEW_PROJECT_STATISTICS_LAST15 = 105;
		const VIEW_PROJECT_STATISTICS_PRIORITY = 106;
		const VIEW_PROJECT_STATISTICS_STATUS = 111;
		const VIEW_PROJECT_STATISTICS_RESOLUTION = 112;
		const VIEW_PROJECT_STATISTICS_STATE = 113;
		const VIEW_PROJECT_STATISTICS_CATEGORY = 114;
		const VIEW_PROJECT_RECENT_ISSUES = 107;
		const VIEW_PROJECT_RECENT_ACTIVITIES = 108;
		const VIEW_PROJECT_UPCOMING = 109;
		const VIEW_PROJECT_DOWNLOADS = 110;

		const TYPE_USER = 1;
		const TYPE_PROJECT = 2;
		const TYPE_TEAM = 3;
		const TYPE_CLIENT = 4;

		/**
		 * The name of the object
		 *
		 * @var string
		 * @Column(type="string", length=200)
		 */
		protected $_name;

		/**
		 * @Column(type="integer", length=10)
		 */
		protected $_view;

		/**
		 * @Column(type="integer", length=10)
		 */
		protected $_pid;

		/**
		 * @Column(type="integer", length=10)
		 */
		protected $_tid;

		/**
		 * @Column(type="integer", length=10)
		 */
		protected $_target_type;

		public static function getViews($tid, $target_type)
		{
			$views = array();
			if ($res = TBGDashboardViewsTable::getTable()->getViews($tid, $target_type))
			{
				while ($row = $res->getNextRow())
				{
					$id = $row->getID();
					$view = new TBGDashboardView($id, $row);
					$views[$id] = $view;
				}
			}
//die();
			return $views;
		}

		public static function getUserViews($user_id)
		{
			return self::getViews($user_id, self::TYPE_USER);
		}

		public static function getProjectViews($project_id)
		{
			return self::getViews($project_id, self::TYPE_PROJECT);
		}

		public static function getAvailableViews($target_type)
		{
			switch ($target_type)
			{
				case TBGDashboardView::TYPE_USER:
					$searches = array();
					$searches[self::VIEW_PREDEFINED_SEARCH] = array(TBGContext::PREDEFINED_SEARCH_MY_REPORTED_ISSUES => TBGContext::getI18n()->__('Issues reported by me'),
																	TBGContext::PREDEFINED_SEARCH_MY_ASSIGNED_OPEN_ISSUES => TBGContext::getI18n()->__('Open issues assigned to me'),
																	TBGContext::PREDEFINED_SEARCH_TEAM_ASSIGNED_OPEN_ISSUES => TBGContext::getI18n()->__('Open issues assigned to my teams'));
					$searches[self::VIEW_LOGGED_ACTIONS] = array(0 => TBGContext::getI18n()->__("What you've done recently"));
					if (TBGContext::getUser()->canViewComments())
					{
						$searches[self::VIEW_RECENT_COMMENTS] = array(0 => TBGContext::getI18n()->__('Recent comments'));
					}
					$searches[self::VIEW_SAVED_SEARCH] = array();
					$allsavedsearches = TBGSavedSearchesTable::getTable()->getAllSavedSearchesByUserIDAndPossiblyProjectID(TBGContext::getUser()->getID());
					foreach ($allsavedsearches as $savedsearches)
					{
						foreach ($savedsearches as $a_savedsearch)
						{
							$searches[self::VIEW_SAVED_SEARCH][$a_savedsearch->get(TBGSavedSearchesTable::ID)] = $a_savedsearch->get(TBGSavedSearchesTable::NAME);
						}
					}
					break;
				case TBGDashboardView::TYPE_PROJECT:
					$issuetype_icons = array();
					foreach (TBGIssuetype::getAll() as $id => $issuetype)
					{
						$issuetype_icons[$id] = TBGContext::getI18n()->__('Recent issues: %issuetype%', array('%issuetype%' => $issuetype->getName()));
					}

					$searches = array();
					$searches[self::VIEW_PROJECT_INFO] = array(0 => TBGContext::getI18n()->__('About this project'));
					$searches[self::VIEW_PROJECT_TEAM] = array(0 => TBGContext::getI18n()->__('Project team'));
					$searches[self::VIEW_PROJECT_CLIENT] = array(0 => TBGContext::getI18n()->__('Project client'));
					$searches[self::VIEW_PROJECT_SUBPROJECTS] = array(0 => TBGContext::getI18n()->__('Subprojects'));
					$searches[self::VIEW_PROJECT_STATISTICS_LAST15] = array(0 => TBGContext::getI18n()->__('Graph of closed vs open issues, past 15 days'));
					$searches[self::VIEW_PROJECT_STATISTICS_PRIORITY] = array(0 => TBGContext::getI18n()->__('Statistics by priority'));
					$searches[self::VIEW_PROJECT_STATISTICS_CATEGORY] = array(0 => TBGContext::getI18n()->__('Statistics by category'));
					$searches[self::VIEW_PROJECT_STATISTICS_STATUS] = array(0 => TBGContext::getI18n()->__('Statistics by status'));
					$searches[self::VIEW_PROJECT_STATISTICS_RESOLUTION] = array(0 => TBGContext::getI18n()->__('Statistics by resolution'));
					$searches[self::VIEW_PROJECT_RECENT_ISSUES] = $issuetype_icons;
					$searches[self::VIEW_PROJECT_RECENT_ACTIVITIES] = array(0 => TBGContext::getI18n()->__('Recent activities'));
					$searches[self::VIEW_PROJECT_UPCOMING] = array(0 => TBGContext::getI18n()->__('Upcoming milestones and deadlines'));
					$searches[self::VIEW_PROJECT_DOWNLOADS] = array(0 => TBGContext::getI18n()->__('Latest downloads'));
					break;
			}

			return $searches;
		}

		public static function setViews($tid, $target_type, $views)
		{
			TBGDashboardViewsTable::getTable()->clearViews($tid, $target_type);
			foreach($views as $key => $view)
			{
				TBGDashboardViewsTable::getTable()->addView($tid, $target_type, $view);
			}
		}

		public static function resetViews($tid, $target_type)
		{
			$views = array();
			self::setUserViews($tid, $target_type, $views);
		}

		/**
		 * Return the items name
		 *
		 * @return string
		 */
		public function getName()
		{
			return $this->_name;
		}

		/**
		 * Set the edition name
		 *
		 * @param string $name
		 */
		public function setName($name)
		{
			$this->_name = $name;
		}

		public function getType()
		{
			return $this->_name;
		}

		public function setType($_type)
		{
			$this->_name = $_type;
		}

		public function getDetail()
		{
			return $this->_view;
		}

		public function setDetail($detail)
		{
			$this->_view = $view;
		}

		public function getProjectID()
		{
			return $this->_tid;
		}

		public function setProjectID($pid)
		{
			$this->_pid = $pid;
		}

		public function getTargetID()
		{
			return $this->_tid;
		}

		public function setTargetID($tid)
		{
			$this->_tid = $tid;
		}

		public function getTargetType()
		{
			return $this->_target_type;
		}

		public function setTargetType($target_type)
		{
			$this->_target_type = $target_type;
		}

		public function isSearchView()
		{
			return (in_array($this->getType(), array(
				self::VIEW_PREDEFINED_SEARCH,
				self::VIEW_SAVED_SEARCH
			)));
		}

		public function hasRSS()
		{
			return (in_array($this->getType(), array(
				self::VIEW_PREDEFINED_SEARCH,
				self::VIEW_SAVED_SEARCH,
				self::VIEW_PROJECT_RECENT_ACTIVITIES,
				self::VIEW_PROJECT_RECENT_ISSUES
			)));
		}

		public function hasJS()
		{
			return (in_array($this->getType(), array(
				self::VIEW_PROJECT_STATISTICS_LAST15,
			)));
		}

		public function getJS()
		{
			return array('js/jquery.flot.min.js', 'js/jquery.flot.resize.min.js');
		}

		public function getRSSUrl()
		{
			switch ($this->getType())
			{
				case self::VIEW_PREDEFINED_SEARCH:
				case self::VIEW_SAVED_SEARCH:
					return TBGContext::getRouting()->generate('search', $this->getSearchParameters(true));
					break;
			}
		}

		public function getSearchParameters($rss = false)
		{
			$paramaters = ($rss) ? array('format' => 'rss') : array();
			switch ($this->getType())
			{
				case TBGDashboardView::VIEW_PREDEFINED_SEARCH :
					$parameters['predefined_search'] = $this->getDetail();
					break;
				case TBGDashboardView::VIEW_SAVED_SEARCH :
					$parameters['saved_search'] = $this->getDetail();
					break;
			}
			return $parameters;
		}

		public function shouldBePreloaded()
		{
			return (boolean) in_array($this->getType(), array(self::VIEW_FRIENDS,
																self::VIEW_PROJECT_DOWNLOADS,
																self::VIEW_PROJECT_INFO,
																self::VIEW_PROJECT_UPCOMING));
		}

		public function getTitle()
		{
			$titles = self::getAvailableViews($this->getTargetType());
			if (array_key_exists($this->getType(), $titles) && array_key_exists($this->getDetail(), $titles[$this->getType()]))
			{
				return $titles[$this->getType()][$this->getDetail()];
			}
			else
			{
				return TBGContext::getI18n()->__('Unknown dashboard item');
			}
		}

		public function getTemplate()
		{
			switch ($this->getType())
			{
				case TBGDashboardView::VIEW_PREDEFINED_SEARCH:
				case TBGDashboardView::VIEW_SAVED_SEARCH:
					return 'search/results_view';
				case self::VIEW_PROJECT_INFO:
					return 'project/dashboardviewprojectinfo';
				case self::VIEW_PROJECT_TEAM:
					return 'project/dashboardviewprojectteam';
				case self::VIEW_PROJECT_CLIENT:
					return 'project/dashboardviewprojectclient';
				case self::VIEW_PROJECT_SUBPROJECTS:
					return 'project/dashboardviewprojectsubprojects';
				case self::VIEW_PROJECT_STATISTICS_LAST15:
					return 'project/dashboardviewprojectstatisticslast15';
				case self::VIEW_PROJECT_RECENT_ISSUES:
					return 'project/dashboardviewprojectrecentissues';
				case self::VIEW_PROJECT_RECENT_ACTIVITIES:
					return 'project/dashboardviewprojectrecentactivities';
				case self::VIEW_PROJECT_STATISTICS_CATEGORY:
				case self::VIEW_PROJECT_STATISTICS_PRIORITY:
				case self::VIEW_PROJECT_STATISTICS_RESOLUTION:
				case self::VIEW_PROJECT_STATISTICS_STATE:
				case self::VIEW_PROJECT_STATISTICS_STATUS:
					return 'project/dashboardviewprojectstatistics';
				case self::VIEW_PROJECT_UPCOMING:
					return 'project/dashboardviewprojectupcoming';
				case self::VIEW_PROJECT_DOWNLOADS:
					return 'project/dashboardviewprojectdownloads';
				case self::VIEW_RECENT_COMMENTS:
					return 'main/dashboardviewrecentcomments';
				case self::VIEW_LOGGED_ACTIONS:
					return 'main/dashboardviewloggedactions';
			}
		}

	}
