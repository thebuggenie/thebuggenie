<?php

	/**
	 * Saved search class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage core
	 */

	/**
	 * Saved search class
	 *
	 * @package thebuggenie
	 * @subpackage core
	 *
	 * @Table(name="TBGSavedSearchesTable")
	 */
	class TBGSavedSearch extends TBGIdentifiableScopedClass
	{

		/**
		 * The name of the saved search
		 *
		 * @var string
		 * @Column(type="string", length=200)
		 */
		protected $_name;

		/**
		 * The description of the saved search
		 *
		 * @var string
		 * @Column(type="string", length=255)
		 */
		protected $_description = null;

		/**
		 * Whether the saved search is public or not
		 *
		 * @var boolean
		 * @Column(type="boolean")
		 */
		protected $_is_public = true;

		/**
		 * The template used by the saved search
		 *
		 * @var string
		 * @Column(type="string", length=200)
		 */
		protected $_templatename = 'results_normal';

		/**
		 * Template parameter used by the saved search
		 *
		 * @var string
		 * @Column(type="string", length=200)
		 */
		protected $_templateparameter;

		/**
		 * Number of issues per page
		 *
		 * @var integer
		 * @Column(type="integer", length=10, default=50)
		 */
		protected $_issues_per_page = 50;

		/**
		 * Search offset
		 *
		 * @var integer
		 */
		protected $_offset = 0;

		/**
		 * Custom search title
		 *
		 * @var string
		 */
		protected $_searchtitle;

		/**
		 * Date order
		 *
		 * @var string
		 */
		protected $_dateorder = 'asc';

		/**
		 * The grouping used by the saved search
		 *
		 * @var string
		 * @Column(type="string", length=100)
		 */
		protected $_groupby = 'issuetype';

		/**
		 * The group order used by the saved search
		 *
		 * @var string
		 * @Column(type="string", length=5)
		 */
		protected $_grouporder = 'asc';

		/**
		 * The project this saved search applies to
		 *
		 * @var TBGProject
		 * @Column(type="integer", length=10)
		 * @Relates(class="TBGProject")
		 */
		protected $_applies_to_project;

		/**
		 * Who saved the search
		 *
		 * @var TBGUser
		 * @Column(type="integer", length=10)
		 * @Relates(class="TBGUser")
		 */
		protected $_uid;

		/**
		 * An array of TBGSearchFilters
		 *
		 * @var array|TBGSearchFilter
		 * @Relates(class="TBGSearchFilter", collection=true, foreign_column="search_id")
		 */
		protected $_filters;

		/**
		 * An array of TBGIssues
		 *
		 * @var array|TBGIssue
		 */
		protected $_issues;

		/**
		 * The total number of issues found
		 *
		 * @var integer
		 */
		protected $_total_number_of_issues;

		public static function getTemplates($display_only = true)
		{
			$i18n = TBGContext::getI18n();
			$templates = array();
			$templates['results_normal'] = array('title' => $i18n->__('Standard'), 'description' => $i18n->__('Standard search results'));
			$templates['results_todo'] = array('title' => $i18n->__('Todo-list'), 'description' => $i18n->__('Todo-list with progress indicator'));
			$templates['results_votes'] = array('title' => $i18n->__('Voting results'), 'description' => $i18n->__('Most voted-for issues'));
			$templates['results_userpain_singlepainthreshold'] = array('title' => $i18n->__('User pain with threshold'), 'description' => $i18n->__('User pain indicator with custom single bug pain threshold'));
			//$templates['results_userpain_totalpainthreshold'] = TBGContext::getI18n()->__('User pain indicator with custom total pain threshold');
			if (!$display_only)
			{
				$templates['results_rss'] = $i18n->__('RSS feed');
			}
			return $templates;
		}

		public static function isTemplateValid($template_name)
		{
			return in_array($template_name, array_keys(self::getTemplates(false)));
		}

		protected function _postSave($is_new)
		{
			foreach ($this->getFilters() as $filter)
			{
				if ($is_new) $filter->clearID();

				$filter->setSearchId($this);
				$filter->save();
			}
		}

		public function setPredefinedVariables($type)
		{
			$i18n = TBGContext::getI18n();
			switch ($type)
			{
				case TBGContext::PREDEFINED_SEARCH_PROJECT_OPEN_ISSUES:
					$this->_searchtitle = (TBGContext::isProjectContext()) ? $i18n->__('Open issues for %project_name%', array('%project_name%' => TBGContext::getCurrentProject()->getName())) : $i18n->__('All open issues');
					$this->_groupby = 'issuetype';
					break;
				case TBGContext::PREDEFINED_SEARCH_PROJECT_OPEN_ISSUES_INCLUDING_SUBPROJECTS:
					$this->_searchtitle = $i18n->__('Open issues for %project_name% (including subprojects)', array('%project_name%' => TBGContext::getCurrentProject()->getName()));
					$this->_groupby = 'issuetype';
					break;
				case TBGContext::PREDEFINED_SEARCH_PROJECT_CLOSED_ISSUES:
					$this->_searchtitle = (TBGContext::isProjectContext()) ? $i18n->__('Closed issues for %project_name%', array('%project_name%' => TBGContext::getCurrentProject()->getName())) : $i18n->__('All closed issues');
					$this->_groupby = 'issuetype';
					break;
				case TBGContext::PREDEFINED_SEARCH_PROJECT_CLOSED_ISSUES_INCLUDING_SUBPROJECTS:
					$this->_searchtitle = $i18n->__('Closed issues for %project_name% (including subprojects)', array('%project_name%' => TBGContext::getCurrentProject()->getName()));
					$this->_groupby = 'issuetype';
					break;
				case TBGContext::PREDEFINED_SEARCH_PROJECT_WISHLIST:
					$this->_searchtitle = $i18n->__('%project_name% wishlist', array('%project_name%' => TBGContext::getCurrentProject()->getName()));
					$this->_groupby = 'issuetype';
					break;
				case TBGContext::PREDEFINED_SEARCH_PROJECT_MILESTONE_TODO:
					$this->_searchtitle = $i18n->__('Milestone todo-list for %project_name%', array('%project_name%' => TBGContext::getCurrentProject()->getName()));
					$this->_templatename = 'results_todo';
					$this->_groupby = 'milestone';
					break;
				case TBGContext::PREDEFINED_SEARCH_PROJECT_MOST_VOTED:
					$this->_searchtitle = (TBGContext::isProjectContext()) ? $i18n->__('Most voted issues for %project_name%', array('%project_name%' => TBGContext::getCurrentProject()->getName())) : $i18n->__('Most voted issues');
					$this->_templatename = 'results_votes';
					$this->_groupby = 'votes';
					$this->_grouporder = 'desc';
					$this->_issues_per_page = 100;
					break;
				case TBGContext::PREDEFINED_SEARCH_MY_REPORTED_ISSUES:
					$this->_searchtitle = $i18n->__('Issues reported by me');
					$this->_groupby = 'issuetype';
					break;
				case TBGContext::PREDEFINED_SEARCH_MY_ASSIGNED_OPEN_ISSUES:
					$this->_searchtitle = $i18n->__('Open issues assigned to me');
					$this->_groupby = 'issuetype';
					break;
				case TBGContext::PREDEFINED_SEARCH_TEAM_ASSIGNED_OPEN_ISSUES:
					$this->_searchtitle = $i18n->__('Open issues assigned to my teams');
					$this->_groupby = 'issuetype';
					break;
				case TBGContext::PREDEFINED_SEARCH_MY_OWNED_OPEN_ISSUES:
					$this->_searchtitle = $i18n->__('Open issues owned by me');
					$this->_groupby = 'issuetype';
					break;
			}
		}

		public function setValuesFromRequest(TBGRequest $request)
		{
			if ($request->hasParameter('predefined_search'))
			{
				$this->setPredefinedVariables($request['predefined_search']);
				$this->_filters = TBGSearchFilter::getPredefinedFilters($request['predefined_search'], $this);
			}
			else
			{
				$this->_templatename = ($request->hasParameter('template') && self::isTemplateValid($request['template'])) ? $request['template'] : 'results_normal';
				$this->_templateparameter = $request['template_parameter'];

				$this->_issues_per_page = $request->getParameter('issues_per_page', 50);
				$this->_offset = $request->getParameter('offset', 0);
				$this->_filters = TBGSearchFilter::getFromRequest($request, $this);
				$this->_applies_to_project = TBGContext::getCurrentProject();

				if ($request['quicksearch']) $this->_dateorder = 'desc';

				$this->_groupby = $request['groupby'];
				$this->_grouporder = $request->getParameter('grouporder', 'asc');

				if (in_array($this->_templatename, array('results_userpain_singlepainthreshold', 'results_userpain_totalpainthreshold')))
				{
					$this->_searchtitle = TBGContext::getI18n()->__('Showing "bug report" issues sorted by user pain, threshold set at %threshold%', array('%threshold%' => $this->_template_parameter));
					$this->_issues_per_page = 0;
					$this->_groupby = 'user_pain';
					$this->_grouporder = 'desc';
					$this->_filters['issuetype'] = TBGSearchFilter::createFilter('issuetype', join(',', TBGIssueTypesTable::getTable()->getBugReportTypeIDs()));
				}
				elseif ($this->_templatename == 'results_votes')
				{
					$this->_searchtitle = TBGContext::getI18n()->__('Showing issues ordered by number of votes');
					$this->_issues_per_page = $request->getParameter('issues_per_page', 100);
					$this->_groupby = 'votes';
					$this->_grouporder = 'desc';
				}
			}
			$this->_setupGenericFilters();
		}

		/**
		 * @param TBGRequest $request
		 *
		 * @return TBGSavedSearch
		 */
		public static function getFromRequest(TBGRequest $request)
		{
			$search = null;
			$search_id = ($request['saved_search_id']) ? $request['saved_search_id'] : $request['saved_search'];
			if ($search_id)
			{
				$search = TBGSavedSearchesTable::getTable()->selectById($search_id);
			}
			else
			{
				$search = new TBGSavedSearch();
				$search->setValuesFromRequest($request);
			}

			return $search;
		}

		/**
		 * @param \TBGProject $applies_to_project
		 */
		public function setAppliesToProject($applies_to_project)
		{
			$this->_applies_to_project = $applies_to_project;
		}

		/**
		 * @return \TBGProject
		 */
		public function getAppliesToProject()
		{
			return $this->_b2dbLazyload('_applies_to_project');
		}

		public function getProject()
		{
			return $this->getAppliesToProject();
		}

		/**
		 * @param string $description
		 */
		public function setDescription($description)
		{
			$this->_description = $description;
		}

		/**
		 * @return string
		 */
		public function getDescription()
		{
			return $this->_description;
		}

		/**
		 * @param string $groupby
		 */
		public function setGroupby($groupby)
		{
			$this->_groupby = $groupby;
		}

		/**
		 * @return string
		 */
		public function getGroupby()
		{
			return $this->_groupby;
		}

		/**
		 * @param string $grouporder
		 */
		public function setGrouporder($grouporder)
		{
			$this->_grouporder = $grouporder;
		}

		/**
		 * @return string
		 */
		public function getGrouporder()
		{
			return $this->_grouporder;
		}

		/**
		 * @param boolean $is_public
		 */
		public function setIsPublic($is_public)
		{
			$this->_is_public = $is_public;
		}

		/**
		 * @return boolean
		 */
		public function getIsPublic()
		{
			return $this->_is_public;
		}

		public function isPublic()
		{
			return $this->getIsPublic();
		}

		/**
		 * @param int $issues_per_page
		 */
		public function setIssuesPerPage($issues_per_page)
		{
			$this->_issues_per_page = $issues_per_page;
		}

		/**
		 * @return int
		 */
		public function getIssuesPerPage()
		{
			return $this->_issues_per_page;
		}

		/**
		 * @param string $name
		 */
		public function setName($name)
		{
			$this->_name = $name;
		}

		/**
		 * @return string
		 */
		public function getName()
		{
			return $this->_name;
		}

		/**
		 * @param string $template_name
		 */
		public function setTemplateName($template_name)
		{
			$this->_templatename = self::isTemplateValid($template_name) ? $template_name : 'results_normal';
		}

		/**
		 * @return string
		 */
		public function getTemplateName()
		{
			return $this->_templatename;
		}

		/**
		 * @param string $template_parameter
		 */
		public function setTemplateParameter($template_parameter)
		{
			$this->_templateparameter = $template_parameter;
		}

		/**
		 * @return string
		 */
		public function getTemplateParameter()
		{
			return $this->_templateparameter;
		}

		/**
		 * @param \TBGUser $uid
		 */
		public function setUid($uid)
		{
			$this->_uid = $uid;
		}

		public function setUser($user)
		{
			$this->setUid($user);
		}

		/**
		 * @return \TBGUser
		 */
		public function getUser()
		{
			return $this->_b2dbLazyload('_uid');
		}

		public function getUserID()
		{
			$user = $this->getUser();
			return ($user instanceof TBGUser) ? $user->getID() : 0;
		}

		protected function _setupGenericFilters()
		{
			if (!isset($this->_filters['issuetype'])) $this->_filters['issuetype'] = TBGSearchFilter::createFilter('issuetype', array(), $this);
			if (!isset($this->_filters['status'])) $this->_filters['status'] = TBGSearchFilter::createFilter('status', array(), $this);
			if (!isset($this->_filters['category'])) $this->_filters['category'] = TBGSearchFilter::createFilter('category', array(), $this);
			if (!TBGContext::isProjectContext() && !isset($this->_filters['project_id'])) $this->_filters['project_id'] = TBGSearchFilter::createFilter('project_id', array(), $this);
		}

		public function getFilters()
		{
			if ($this->_filters === null)
			{
				$filters = array();
				$this->_b2dbLazyload('_filters');
				foreach ($this->_filters as $filter)
				{
					$filters[$filter->getFilterKey()] = $filter;
				}
				$this->_filters = $filters;
				$this->_setupGenericFilters();
			}
			return $this->_filters;
		}

		public function hasFilter($key)
		{
			return isset($this->_filters[$key]);
		}

		public function getFilter($key)
		{
			return ($this->hasFilter($key)) ? $this->_filters[$key] : null;
		}

		public function getSearchterm()
		{
			$filters = $this->getFilters();

			return (array_key_exists('text', $filters)) ? $filters['text']->getValue() : null;
		}

		/**
		 * @param int $offset
		 */
		public function setOffset($offset)
		{
			$this->_offset = $offset;
		}

		/**
		 * @return int
		 */
		public function getOffset()
		{
			return $this->_offset;
		}

		public function getTitle()
		{
			return (isset($this->_searchtitle)) ? $this->_searchtitle : $this->_name;
		}

		/**
		 * @param string $dateorder
		 */
		public function setDateorder($dateorder)
		{
			$this->_dateorder = $dateorder;
		}

		/**
		 * @return string
		 */
		public function getDateorder()
		{
			return $this->_dateorder;
		}

		protected function _performSearch()
		{
			list ($this->_issues, $this->_total_number_of_issues) = TBGIssue::findIssues($this->getFilters(), $this->getIssuesPerPage(), $this->getOffset(), $this->getGroupby(), $this->getGrouporder(), $this->getDateorder());
		}

		public function getIssues()
		{
			if ($this->_issues === null)
			{
				$this->_performSearch();
			}

			return $this->_issues;
		}

		public function getTotalNumberOfIssues()
		{
			if ($this->_total_number_of_issues === null)
			{
				$this->_performSearch();
			}

			return $this->_total_number_of_issues;
		}

		public function getNumberOfIssues()
		{
			if ($this->_issues === null)
			{
				$this->_performSearch();
			}

			return count($this->_issues);
		}

		public function hasPagination()
		{
			return ($this->getIssuesPerPage() > 0);
		}

		public function needsPagination()
		{
			return ($this->getTotalNumberOfIssues() > $this->getIssuesPerPage());
		}

		public function getCurrentPage()
		{
			return ceil($this->getOffset() / $this->getIssuesPerPage()) + 1;
		}

		public function getNumberOfPages()
		{
			return ceil($this->getTotalNumberOfIssues() / $this->getIssuesPerPage());
		}

		public function getParameters()
		{
			$parameters = array();
			foreach ($this->getFilters() as $key => $filter)
			{
				if (is_array($filter))
				{
					foreach ($filter as $subkey => $subfilter)
					{
						if (is_array($subfilter))
						{
							foreach ($subfilter as $subsubkey => $subsubfilter)
							{
								$parameters[] = "filters[{$key}][{$subkey}][{$subsubkey}]=".urlencode($subsubfilter['value']);
							}
						}
						else
						{
							$parameters[] = "filters[{$key}][{$subkey}]=".urlencode($subfilter['value']);
						}
					}
				}
				else
				{
					$parameters[] = "filters[{$key}][operator]=".urlencode($filter['operator']);
					$parameters[] = "filters[{$key}][value]=".urlencode($filter['value']);
				}
			}
			$parameters[] = 'template='.$this->getTemplateName();
			$parameters[] = 'template_parameter='.$this->getTemplateParameter();
			$parameters[] = 'searchterm='.$this->getSearchterm();
			$parameters[] = 'groupby='.$this->getGroupby();
			$parameters[] = 'grouporder='.$this->getGrouporder();
			$parameters[] = 'issues_per_page='.$this->getIssuesPerPage();

			return $parameters;
		}

		public function getParametersAsString()
		{
			return join('&', $this->getParameters());
		}

	}
