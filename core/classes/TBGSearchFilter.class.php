<?php

	use b2db\Criteria;

	/**
	 * Search filter class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage core
	 */

	/**
	 * Search filter class
	 *
	 * @package thebuggenie
	 * @subpackage core
	 *
	 * @Table(name="TBGSavedSearchFiltersTable")
	 */
	class TBGSearchFilter extends TBGIdentifiableScopedClass implements \ArrayAccess
	{

		const VALUE = 'savedsearchfilters.value';
		const OPERATOR = 'savedsearchfilters.operator';
		const SEARCH_ID = 'savedsearchfilters.search_id';
		const FILTER_KEY = 'savedsearchfilters.filter_key';

		/**
		 * The value of the filter
		 *
		 * @var string
		 * @Column(type="string", length=200)
		 */
		protected $_value;

		/**
		 * The operator for the filter
		 *
		 * @var string
		 * @Column(type="string", length=40)
		 */
		protected $_operator = '=';

		/**
		 * The filter key
		 *
		 * @var string
		 * @Column(type="string", length=100)
		 */
		protected $_filter_key;

		/**
		 * The saved search this filter applies to
		 *
		 * @var TBGSavedSearch
		 * @Column(type="integer", length=10)
		 * @Relates(class="TBGSavedSearch")
		 */
		protected $_search_id;

		/**
		 * The related custom data type
		 * 
		 * @var TBGCustomDatatype
		 */
		protected $_customtype;

		public static function createFilter($key, $options = array(), TBGSavedSearch $search = null)
		{
			if (isset($options['o'])) $options['operator'] = $options['o'];
			if (isset($options['v'])) $options['value'] = $options['v'];

			$options = array_merge(array('operator' => '=', 'value' => ''), $options);
			$filter = new TBGSearchFilter();
			$filter->setFilterKey($key);
			$filter->setOperator($options['operator']);
			$filter->setValue($options['value']);
			$filter->setSearchId($search);

			return $filter;
		}

		public static function getValidSearchFilters()
		{
			return array('project_id', 'subprojects', 'text', 'state', 'issuetype', 'status', 'resolution', 'reproducability', 'category', 'severity', 'priority', 'posted_by', 'assignee_user', 'assignee_team', 'owner_user', 'owner_team', 'component', 'build', 'edition', 'posted', 'last_updated', 'milestone', 'blocking', 'votes_total');
		}

		public static function getPredefinedFilters($type, TBGSavedSearch $search)
		{
			$filters = array();
			switch ($type)
			{
				case TBGContext::PREDEFINED_SEARCH_PROJECT_OPEN_ISSUES:
					$filters['status'] = self::createFilter('status', array('operator' => '=', 'value' => 'open'), $search);
					$filters['project_id'] = self::createFilter('project_id', array('operator' => '=', 'value' => TBGContext::getCurrentProject()->getID()), $search);
					break;
				case TBGContext::PREDEFINED_SEARCH_PROJECT_OPEN_ISSUES_INCLUDING_SUBPROJECTS:
					$filters['status'] = self::createFilter('status', array('operator' => '=', 'value' => 'open'), $search);
					$filters['project_id'] = self::createFilter('project_id', array('operator' => '=', 'value' => TBGContext::getCurrentProject()->getID()), $search);
					$filters['subprojects'] = self::createFilter('subprojects', array('operator' => '=', 'value' => 'all'), $search);
					break;
				case TBGContext::PREDEFINED_SEARCH_PROJECT_CLOSED_ISSUES:
					$filters['status'] = self::createFilter('status', array('operator' => '=', 'value' => 'closed'), $search);
					$filters['project_id'] = self::createFilter('project_id', array('operator' => '=', 'value' => TBGContext::getCurrentProject()->getID()), $search);
					break;
				case TBGContext::PREDEFINED_SEARCH_PROJECT_CLOSED_ISSUES_INCLUDING_SUBPROJECTS:
					$filters['status'] = self::createFilter('status', array('operator' => '=', 'value' => 'closed'), $search);
					$filters['project_id'] = self::createFilter('project_id', array('operator' => '=', 'value' => TBGContext::getCurrentProject()->getID()), $search);
					$filters['subprojects'] = self::createFilter('subprojects', array('operator' => '=', 'value' => 'all'), $search);
					break;
				case TBGContext::PREDEFINED_SEARCH_PROJECT_WISHLIST:
					$filters['status'] = self::createFilter('status', array('operator' => '=', 'value' => 'open'), $search);
					$filters['project_id'] = self::createFilter('project_id', array('operator' => '=', 'value' => TBGContext::getCurrentProject()->getID()), $search);
					$types = array();
					foreach (TBGContext::getCurrentProject()->getIssuetypeScheme()->getIssuetypes() as $issuetype)
					{
						if (in_array($issuetype->getIcon(), array('feature_request', 'enhancement')))
							$types[] = $issuetype->getID();
					}
					if (count($types))
					{
						$filters['issuetype'] = self::createFilter('issuetype', array('operator' => '=', 'value' => join(',', $types)));
					}
					break;
				case TBGContext::PREDEFINED_SEARCH_PROJECT_REPORTED_LAST_NUMBEROF_TIMEUNITS:
					$filters['project_id'] = self::createFilter('project_id', array('operator' => '=', 'value' => TBGContext::getCurrentProject()->getID()), $search);
					$units = TBGContext::getRequest()->getParameter('units');
					switch (TBGContext::getRequest()->getParameter('time_unit'))
					{
						case 'seconds':
							$time_unit = NOW - $units;
							break;
						case 'minutes':
							$time_unit = NOW - (60 * $units);
							break;
						case 'hours':
							$time_unit = NOW - (60 * 60 * $units);
							break;
						case 'days':
							$time_unit = NOW - (86400 * $units);
							break;
						case 'weeks':
							$time_unit = NOW - (86400 * 7 * $units);
							break;
						case 'months':
							$time_unit = NOW - (86400 * 30 * $units);
							break;
						case 'years':
							$time_unit = NOW - (86400 * 365 * $units);
							break;
						default:
							$time_unit = NOW - (86400 * 30);
					}
					$filters['posted'] = self::createFilter('posted', array('operator' => '>=', 'value' => $time_unit), $search);
					break;
				case TBGContext::PREDEFINED_SEARCH_PROJECT_REPORTED_THIS_MONTH:
					$filters['project_id'] = self::createFilter('project_id', array('operator' => '=', 'value' => TBGContext::getCurrentProject()->getID()), $search);
					$filters['posted'] = self::createFilter('posted', array('operator' => '>=', 'value' => mktime(date('H'), date('i'), date('s'), date('n'), 1)), $search);
					break;
				case TBGContext::PREDEFINED_SEARCH_PROJECT_MILESTONE_TODO:
					$filters['status'] = self::createFilter('status', array('operator' => '=', 'value' => 'open'), $search);
					$filters['project_id'] = self::createFilter('project_id', array('operator' => '=', 'value' => TBGContext::getCurrentProject()->getID()), $search);
					$filters['milestone'] = self::createFilter('milestone', array('operator' => '!=', 'value' => 0), $search);
					break;
				case TBGContext::PREDEFINED_SEARCH_PROJECT_MOST_VOTED:
					$filters['project_id'] = self::createFilter('project_id', array('operator' => '=', 'value' => TBGContext::getCurrentProject()->getID()), $search);
					$filters['status'] = self::createFilter('status', array('operator' => '=', 'value' => 'open'), $search);
					$filters['votes_total'] = self::createFilter('votes_total', array('operator' => '>=', 'value' => '1'), $search);
					break;
				case TBGContext::PREDEFINED_SEARCH_MY_REPORTED_ISSUES:
					$filters['posted_by'] = self::createFilter('posted_by', array('operator' => '=', 'value' => TBGContext::getUser()->getID()), $search);
					break;
				case TBGContext::PREDEFINED_SEARCH_MY_ASSIGNED_OPEN_ISSUES:
					$filters['status'] = self::createFilter('status', array('operator' => '=', 'value' => 'open'), $search);
					$filters['assignee_user'] = self::createFilter('assignee_user', array('operator' => '=', 'value' => TBGContext::getUser()->getID()), $search);
					break;
				case TBGContext::PREDEFINED_SEARCH_TEAM_ASSIGNED_OPEN_ISSUES:
					$filters['status'] = self::createFilter('status', array('operator' => '=', 'value' => 'open'), $search);
					$teams = array();
					foreach (TBGContext::getUser()->getTeams() as $team_id => $team)
					{
						$teams[] = $team_id;
					}
					$filters['assignee_team'] = self::createFilter('assignee_team', array('operator' => '=', 'value' => join(',', $teams)), $search);
					break;
				case TBGContext::PREDEFINED_SEARCH_MY_OWNED_OPEN_ISSUES:
					$filters['status'] = self::createFilter('status', array('operator' => '=', 'value' => 'open'), $search);
					$filters['owner_user'] = self::createFilter('owner_user', array('operator' => '=', 'value' => TBGContext::getUser()->getID()), $search);
					break;
			}

			return $filters;
		}

		public static function getFromRequest(TBGRequest $request, TBGSavedSearch $search)
		{
			$filters = $request->getRawParameter('fs', array());
			if ($request['quicksearch'])
			{
				$filters['text']['o'] = '=';
			}
			if (TBGContext::isProjectContext())
			{
				$filters['project_id'] = array('o' => '=', 'v' => TBGContext::getCurrentProject()->getID());
			}

			$return_filters = array();
			foreach ($filters as $key => $details)
			{
				if (!isset($details['o']))
				{
					foreach ($details as $subdetails)
					{
						$return_filters[$key][] = self::createFilter($key, $subdetails, $search);
					}
				}
				else
				{
					$return_filters[$key] = self::createFilter($key, $details, $search);
				}
			}

			return $return_filters;
		}

		/**
		 * @param string $filter_key
		 */
		public function setFilterKey($filter_key)
		{
			$this->_filter_key = $filter_key;
		}

		/**
		 * @return string
		 */
		public function getFilterKey()
		{
			return $this->_filter_key;
		}

		protected function _populateCustomtype()
		{
			if ($this->_customtype === null)
			{
				$this->_customtype = TBGCustomDatatype::getByKey($this->getFilterKey());
			}
		}

		public function isCustomFilter()
		{
			return (!in_array($this->getFilterKey(), self::getValidSearchFilters()));
		}

		public function getFilterType()
		{
			$this->_populateCustomtype();
			return $this->_customtype->getType();
		}

		public function getFilterTitle()
		{
			$this->_populateCustomtype();
			return $this->_customtype->getDescription();
		}

		/**
		 * @param string $operator
		 */
		public function setOperator($operator)
		{
			$this->_operator = $operator;
		}

		/**
		 * @return string
		 */
		public function getOperator()
		{
			return $this->_operator;
		}

		/**
		 * @param \TBGSavedSearch $search_id
		 */
		public function setSearchId($search_id)
		{
			$this->_search_id = $search_id;
		}

		/**
		 * @param \TBGSavedSearch $search
		 */
		public function setSearch(TBGSavedSearch $search)
		{
			$this->_search_id = $search;
		}

		/**
		 * @return \TBGSavedSearch
		 */
		public function getSearch()
		{
			return $this->_b2dbLazyload('_search_id');
		}

		/**
		 * @param string $value
		 */
		public function setValue($value)
		{
			$this->_value = $value;
		}

		/**
		 * @return string
		 */
		public function getValue()
		{
			return $this->_value;
		}

		public function hasExclusiveValues()
		{
			$this->_populateCustomtype();
			return in_array($this->_customtype->getType(), array(TBGCustomDatatype::RADIO_CHOICE, TBGCustomDatatype::COMPONENTS_CHOICE, TBGCustomDatatype::EDITIONS_CHOICE, TBGCustomDatatype::RELEASES_CHOICE, TBGCustomDatatype::STATUS_CHOICE));
		}

		public function getValues()
		{
			if (!$this->hasValue()) return array();

			$values = explode(',', $this->_value);
			return $values;
		}

		public function hasValue($value = null)
		{
			if ($value === null)
			{
				return $this->_value !== '';
			}
			else
			{
				if (!$this->hasValue()) return false;

				$values = explode(',', $this->_value);
				return in_array($value, $values);
			}
		}

		protected function _getAvailableComponentChoices()
		{
			if (TBGContext::isProjectContext()) return TBGContext::getCurrentProject()->getComponents();

			$components = array();
			foreach (TBGProject::getAll() as $project)
			{
				foreach ($project->getComponents() as $component)
					$components[$component->getID()] = $component;
			}

			return $components;
		}

		protected function _getAvailableMilestoneChoices()
		{
			if (TBGContext::isProjectContext()) return TBGContext::getCurrentProject()->getMilestones();

			$milestones = array();
			foreach (TBGProject::getAll() as $project)
			{
				foreach ($project->getMilestones() as $milestone)
					$milestones[$milestone->getID()] = $milestone;
			}

			return $milestones;
		}

		protected function _getAvailableBuildChoices()
		{
			if (TBGContext::isProjectContext()) return TBGContext::getCurrentProject()->getBuilds();

			$builds = array();
			foreach (TBGProject::getAll() as $project)
			{
				foreach ($project->getBuilds() as $build)
					$builds[$build->getID()] = $build;
			}

			return $builds;
		}

		protected function _getAvailableEditionChoices()
		{
			if (TBGContext::isProjectContext()) return TBGContext::getCurrentProject()->getEditions();

			$editions = array();
			foreach (TBGProject::getAll() as $project)
			{
				foreach ($project->getEditions() as $edition)
					$editions[$edition->getID()] = $edition;
			}

			return $editions;
		}

		protected function _getAvailableTeamChoices()
		{
			$teams = TBGContext::getUser()->getTeams();
			if (TBGContext::isProjectContext())
			{
				foreach (TBGContext::getCurrentProject()->getAssignedTeams() as $team)
				{
					$teams[$team->getID()] = $team;
				}
			}
			return $teams;
		}

		protected function _getAvailableUserChoices()
		{
			$me = TBGContext::getUser();
			$filters = array($me->getID() => $me);
			foreach ($me->getFriends() as $user)
			{
				$filters[$user->getID()] = $user;
			}
			if (count($this->getValues()))
			{
				$users = TBGUsersTable::getTable()->getByUserIDs($this->getValues());
				foreach ($users as $user)
				{
					$filters[$user->getID()] = $user;
				}
			}
			return $filters;
		}

		public function getAvailableValues($filters = array())
		{
			switch ($this->getFilterKey())
			{
				case 'issuetype':
					return (TBGContext::isProjectContext()) ? TBGContext::getCurrentProject()->getIssuetypeScheme()->getIssuetypes() : TBGIssuetype::getAll();
				case 'status':
					return TBGStatus::getAll();
				case 'category':
					return TBGCategory::getAll();
				case 'priority':
					return TBGPriority::getAll();
				case 'severity':
					return TBGSeverity::getAll();
				case 'reproducability':
					return TBGReproducability::getAll();
				case 'resolution':
					return TBGResolution::getAll();
				case 'project_id':
					return TBGProject::getAll();
				case 'build':
					return $this->_getAvailableBuildChoices();
				case 'component':
					return $this->_getAvailableComponentChoices();
				case 'edition':
					return $this->_getAvailableEditionChoices();
				case 'milestone':
					return $this->_getAvailableMilestoneChoices();
				case 'subprojects':
					$filters = array();
					$projects = TBGProject::getIncludingAllSubprojectsAsArray(TBGContext::getCurrentProject());
					foreach ($projects as $project)
					{
						if ($project->getID() == TBGContext::getCurrentProject()->getID()) continue;

						$filters[$project->getID()] = $project;
					}
					return $filters;
				case 'owner_user':
				case 'assignee_user':
				case 'posted_by':
					return $this->_getAvailableUserChoices();
				case 'owner_team':
				case 'assignee_team':
					return $this->_getAvailableTeamChoices();
				default:
					$customdatatype = TBGCustomDatatype::getByKey($this->getFilterKey());
					if ($customdatatype instanceof TBGCustomDatatype && $customdatatype->hasCustomOptions())
					{
						return $customdatatype->getOptions();
					}
					else
					{
						switch ($this->getFilterType())
						{
							case TBGCustomDatatype::COMPONENTS_CHOICE:
								return $this->_getAvailableComponentChoices();
							case TBGCustomDatatype::RELEASES_CHOICE:
								return $this->_getAvailableBuildChoices();
							case TBGCustomDatatype::EDITIONS_CHOICE:
								return $this->_getAvailableEditionChoices();
							case TBGCustomDatatype::MILESTONE_CHOICE:
								return $this->_getAvailableMilestoneChoices();
							case TBGCustomDatatype::USER_CHOICE:
								return $this->_getAvailableUserChoices();
							case TBGCustomDatatype::TEAM_CHOICE:
								return $this->_getAvailableTeamChoices();
							case TBGCustomDatatype::STATUS_CHOICE:
								return TBGStatus::getAll();
							default:
								return array();
						}
					}
			}
		}

		/**
		 * (PHP 5 &gt;= 5.0.0)<br/>
		 * Whether a offset exists
		 * @link http://php.net/manual/en/arrayaccess.offsetexists.php
		 * @param mixed $offset <p>
		 * An offset to check for.
		 * </p>
		 * @return boolean true on success or false on failure.
		 * </p>
		 * <p>
		 * The return value will be casted to boolean if non-boolean was returned.
		 */
		public function offsetExists($offset)
		{
			return in_array($offset, array('operator', 'o', 'value', 'v', 'key', 'filter', 'filter_key'));
		}

		/**
		 * (PHP 5 &gt;= 5.0.0)<br/>
		 * Offset to retrieve
		 * @link http://php.net/manual/en/arrayaccess.offsetget.php
		 * @param mixed $offset <p>
		 * The offset to retrieve.
		 * </p>
		 * @return mixed Can return all value types.
		 */
		public function offsetGet($offset)
		{
			switch ($offset)
			{
				case 'operator':
				case 'o':
					return $this->_operator;
				case 'value':
				case 'v':
					return $this->_value;
				case 'key':
				case 'filter':
				case 'filter_key':
					return $this->_filter_key;
			}
		}

		/**
		 * (PHP 5 &gt;= 5.0.0)<br/>
		 * Offset to set
		 * @link http://php.net/manual/en/arrayaccess.offsetset.php
		 * @param mixed $offset <p>
		 * The offset to assign the value to.
		 * </p>
		 * @param mixed $value <p>
		 * The value to set.
		 * </p>
		 * @return void
		 */
		public function offsetSet($offset, $value)
		{
			switch ($offset)
			{
				case 'operator':
					$this->_operator = $value;
				case 'value':
					$this->_value = $value;
				case 'key':
				case 'filter':
				case 'filter_key':
					$this->_filter_key = $value;
			}
		}

		/**
		 * (PHP 5 &gt;= 5.0.0)<br/>
		 * Offset to unset
		 * @link http://php.net/manual/en/arrayaccess.offsetunset.php
		 * @param mixed $offset <p>
		 * The offset to unset.
		 * </p>
		 * @return void
		 */
		public function offsetUnset($offset)
		{
			// TODO: Implement offsetUnset() method.
		}

		/**
		 *
		 * @param \b2db\Criteria $crit
		 * @param array|TBGSearchFilter $filters
		 * @param \b2db\Criterion $ctn
		 * @return null
		 */
		public function addToCriteria($crit, $filters, $ctn = null)
		{
			$filter_key = $this->getFilterKey();

			if (in_array($this['operator'], array('=', '!=', '<=', '>=', '<', '>')))
			{
				if ($filter_key == 'text')
				{
					if ($this['value'] != '')
					{
						$searchterm = (mb_strpos($this['value'], '%') !== false) ? $this['value'] : "%{$this['value']}%";
						if ($this['operator'] == '=')
						{
							if ($ctn === null) $ctn = $crit->returnCriterion(TBGIssuesTable::TITLE, $searchterm, Criteria::DB_LIKE);
							$ctn->addOr(TBGIssuesTable::DESCRIPTION, $searchterm, Criteria::DB_LIKE);
							$ctn->addOr(TBGIssuesTable::REPRODUCTION_STEPS, $searchterm, Criteria::DB_LIKE);
							$ctn->addOr(TBGIssueCustomFieldsTable::OPTION_VALUE, $searchterm, Criteria::DB_LIKE);
						}
						else
						{
							if ($ctn === null) $ctn = $crit->returnCriterion(TBGIssuesTable::TITLE, $searchterm, Criteria::DB_NOT_LIKE);
							$ctn->addWhere(TBGIssuesTable::DESCRIPTION, $searchterm, Criteria::DB_NOT_LIKE);
							$ctn->addWhere(TBGIssuesTable::REPRODUCTION_STEPS, $searchterm, Criteria::DB_NOT_LIKE);
							$ctn->addOr(TBGIssueCustomFieldsTable::OPTION_VALUE, $searchterm, Criteria::DB_NOT_LIKE);
						}
						return $ctn;
					}
				}
				elseif (in_array($filter_key, self::getValidSearchFilters()))
				{
					if ($filter_key == 'subprojects')
					{
						if (TBGContext::isProjectContext())
						{
							if ($ctn === null) $ctn = $crit->returnCriterion(TBGIssuesTable::PROJECT_ID, TBGContext::getCurrentProject()->getID());
							if ($this->hasValue())
							{
								foreach ($this->getValues() as $value)
								{
									switch ($value)
									{
										case 'all':
											$subprojects = TBGProject::getIncludingAllSubprojectsAsArray(TBGContext::getCurrentProject());
											foreach ($subprojects as $subproject)
											{
												if ($subproject->getID() == TBGContext::getCurrentProject()->getID()) continue;
												$ctn->addOr(TBGIssuesTable::PROJECT_ID, $subproject->getID());
											}
											break;
										case 'none':
										case '':
											break;
										default:
											$ctn->addOr(TBGIssuesTable::PROJECT_ID, (int) $value);
											break;
									}
								}
							}
							return $ctn;
						}
					}
					elseif (in_array($filter_key, array('build', 'edition', 'component')))
					{
						switch ($filter_key)
						{
							case 'component':
								$tbl = TBGIssueAffectsComponentTable::getTable();
								$fk  = TBGIssueAffectsComponentTable::ISSUE;
								break;
							case 'edition':
								$tbl = TBGIssueAffectsEditionTable::getTable();
								$fk  = TBGIssueAffectsEditionTable::ISSUE;
								break;
							case 'build':
								$tbl = TBGIssueAffectsBuildTable::getTable();
								$fk  = TBGIssueAffectsBuildTable::ISSUE;
								break;
						}
						$crit->addJoin($tbl, $fk, TBGIssuesTable::ID, array(array($tbl->getB2DBAlias().'.'.$filter_key, $this->getValues())), \b2db\Criteria::DB_INNER_JOIN);
						return null;
					}
					else
					{
						if ($filter_key == 'project_id' && in_array('subprojects', $filters)) return null;

						$values = $this->getValues();
						$num_values = 0;

						if ($filter_key == 'status')
						{
							if ($this->hasValue('open'))
							{
								$c = $crit->returnCriterion(TBGIssuesTable::STATE, TBGIssue::STATE_OPEN);
								$num_values++;
							}
							if ($this->hasValue('closed'))
							{
								$num_values++;
								if (isset($c)) $c->addWhere(TBGIssuesTable::STATE, TBGIssue::STATE_CLOSED);
								else $c = $crit->returnCriterion(TBGIssuesTable::STATE, TBGIssue::STATE_CLOSED);
							}

							if (isset($c))
							{
								if (count($values) == $num_values) return $c;
								else $crit->addWhere($c);
							}
						}

						$dbname     = TBGIssuesTable::getTable()->getB2DBName();

						foreach ($values as $value)
						{
							$operator = $this['operator'];
							$or = true;
							if ($filter_key == 'status' && in_array($value, array('open', 'closed')))
							{
								continue;
							}
							else
							{
								$field = $dbname.'.'.$filter_key;
								if ($operator == '!=' || in_array($filter_key, array('posted', 'last_updated')))
								{
									$or = false;
								}
							}
							if ($ctn === null)
							{
								$ctn = $crit->returnCriterion($field, $value, urldecode($operator));
							}
							elseif ($or)
							{
								$ctn->addOr($field, $value, urldecode($operator));
							}
							else
							{
								$ctn->addWhere($field, $value, urldecode($operator));
							}
						}

						return $ctn;
					}
				}
				elseif (TBGCustomDatatype::doesKeyExist($filter_key))
				{
					$customdatatype = TBGCustomDatatype::getByKey($filter_key);
					if (in_array($this->getFilterType(), TBGCustomDatatype::getInternalChoiceFieldsAsArray()))
					{
						$tbl = clone TBGIssueCustomFieldsTable::getTable();
						$crit->addJoin($tbl, TBGIssueCustomFieldsTable::ISSUE_ID, TBGIssuesTable::ID, array(array($tbl->getB2DBAlias().'.customfields_id', $customdatatype->getID()), array($tbl->getB2DBAlias().'.customfieldoption_id', $this->getValues())), \b2db\Criteria::DB_INNER_JOIN);
						return null;
					}
					else
					{
						foreach ($this->getValues() as $value)
						{
							if ($customdatatype->hasCustomOptions())
							{
								if ($ctn === null)
								{
									$ctn = $crit->returnCriterion(TBGIssueCustomFieldsTable::CUSTOMFIELDS_ID, $customdatatype->getID());
									$ctn->addWhere(TBGIssueCustomFieldsTable::CUSTOMFIELDOPTION_ID, $value, $this['operator']);
								}
								else
								{
									$ctn->addOr(TBGIssueCustomFieldsTable::CUSTOMFIELDOPTION_ID, $value, $this['operator']);
								}
							}
							else
							{
								if ($ctn === null)
								{
									$ctn = $crit->returnCriterion(TBGIssueCustomFieldsTable::CUSTOMFIELDS_ID, $customdatatype->getID());
									$ctn->addWhere(TBGIssueCustomFieldsTable::OPTION_VALUE, $value, $this['operator']);
								}
								else
								{
									$ctn->addOr(TBGIssueCustomFieldsTable::OPTION_VALUE, $value, $this['operator']);
								}
							}
						}
						return $ctn;
					}
				}
			}
		}

	}
