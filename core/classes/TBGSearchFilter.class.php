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

		public static function createFilter($key, $options = array(), TBGSavedSearch $search = null)
		{
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
			return array('project_id', 'subprojects', 'text', 'state', 'issuetype', 'status', 'resolution', 'reproducability', 'category', 'severity', 'priority', 'posted_by', 'assignee_user', 'assignee_team', 'owner_user', 'owner_team', 'component', 'build', 'edition', 'posted', 'last_updated', 'milestone');
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
					$filters['posted'] = array(
						self::createFilter('posted', array('operator' => '<=', 'value' => NOW), $search),
						self::createFilter('posted', array('operator' => '>=', 'value' => $time_unit), $search)
					);
					break;
				case TBGContext::PREDEFINED_SEARCH_PROJECT_REPORTED_THIS_MONTH:
					$filters['project_id'] = self::createFilter('project_id', array('operator' => '=', 'value' => TBGContext::getCurrentProject()->getID()), $search);
					$filters['posted'] = array(
						self::createFilter('posted', array('operator' => '<=', 'value' => mktime(date('H'), date('i'), date('s'), date('n'))), $search),
						self::createFilter('posted', array('operator' => '>=', 'value' => mktime(date('H'), date('i'), date('s'), date('n'), 1)), $search)
					);
					break;
				case TBGContext::PREDEFINED_SEARCH_PROJECT_MILESTONE_TODO:
					$filters['status'] = self::createFilter('status', array('operator' => '=', 'value' => 'open'), $search);
					$filters['project_id'] = self::createFilter('project_id', array('operator' => '=', 'value' => TBGContext::getCurrentProject()->getID()), $search);
					$filters['milestone'] = self::createFilter('milestone', array('operator' => '!=', 'value' => 0), $search);
					break;
				case TBGContext::PREDEFINED_SEARCH_PROJECT_MOST_VOTED:
					$filters['project_id'] = self::createFilter('project_id', array('operator' => '=', 'value' => TBGContext::getCurrentProject()->getID()), $search);
					$filters['status'] = self::createFilter('status', array('operator' => '=', 'value' => 'open'), $search);
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
					foreach (TBGContext::getUser()->getTeams() as $team_id => $team)
					{
						$filters['assignee_team'][] = self::createFilter('assignee_team', array('operator' => '=', 'value' => $team_id), $search);
					}
					$filters['assignee_team'][] = self::createFilter('assignee_team', array('operator' => '!=', 'value' => 0), $search);
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
			$filters = $request->getRawParameter('filters', array());
			if ($request['quicksearch'])
			{
				$filters['text']['operator'] = '=';
			}
			if (TBGContext::isProjectContext())
			{
				$filters['project_id'] = array('operator' => '=', 'value' => TBGContext::getCurrentProject()->getID());
			}

			$return_filters = array();
			foreach ($filters as $key => $details)
			{
				if (!isset($details['operator']))
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

		public function getAvailableValues($filters = array())
		{
			switch ($this->getFilterKey())
			{
				case 'issuetype':
					return (TBGContext::isProjectContext()) ? TBGContext::getCurrentProject()->getIssuetypeScheme()->getIssuetypes() : TBGIssuetype::getAll();
					break;
				case 'status':
					return TBGStatus::getAll();
					break;
				case 'category':
					return TBGCategory::getAll();
					break;
				case 'priority':
					return TBGPriority::getAll();
					break;
				case 'severity':
					return TBGSeverity::getAll();
					break;
				case 'reproducability':
					return TBGReproducability::getAll();
					break;
				case 'resolution':
					return TBGResolution::getAll();
					break;
				case 'project_id':
					return TBGProject::getAll();
					break;
				case 'build':
					if (TBGContext::isProjectContext()) return TBGContext::getCurrentProject()->getBuilds();

					$builds = array();
					foreach (TBGProject::getAll() as $project)
					{
						foreach ($project->getBuilds() as $build)
							$builds[$build->getID()] = $build;
					}

					return $builds;
					break;
				case 'component':
					if (TBGContext::isProjectContext()) return TBGContext::getCurrentProject()->getComponents();

					$components = array();
					foreach (TBGProject::getAll() as $project)
					{
						foreach ($project->getComponents() as $component)
							$components[$component->getID()] = $component;
					}

					return $components;
					break;
				case 'edition':
					if (TBGContext::isProjectContext()) return TBGContext::getCurrentProject()->getEditions();

					$editions = array();
					foreach (TBGProject::getAll() as $project)
					{
						foreach ($project->getEditions() as $edition)
							$editions[$edition->getID()] = $edition;
					}

					return $editions;
					break;
				case 'milestone':
					if (TBGContext::isProjectContext()) return TBGContext::getCurrentProject()->getMilestones();

					$milestones = array();
					foreach (TBGProject::getAll() as $project)
					{
						foreach ($project->getMilestones() as $milestone)
							$milestones[$milestone->getID()] = $milestone;
					}

					return $milestones;
					break;
				case 'subprojects':
					$filters = array();
					$projects = TBGProject::getIncludingAllSubprojectsAsArray(TBGContext::getCurrentProject());
					foreach ($projects as $project)
					{
						if ($project->getID() == TBGContext::getCurrentProject()->getID()) continue;

						$filters[$project->getID()] = $project;
					}
					return $filters;
					break;
				case 'owner_user':
				case 'assignee_user':
				case 'posted_by':
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
					break;
				case 'owner_team':
				case 'assignee_team':
					$teams = TBGContext::getUser()->getTeams();
					if (TBGContext::isProjectContext())
					{
						foreach (TBGContext::getCurrentProject()->getAssignedTeams() as $team)
						{
							$teams[$team->getID()] = $team;
						}
					}
					return $teams;
					break;
				default:
					return array();
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
			return in_array($offset, array('operator', 'value', 'key', 'filter', 'filter_key'));
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
					return $this->_operator;
				case 'value':
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

		public function addToCriteria($crit, $filters, $ctn = null)
		{
			$filter_key = $this->getFilterKey();

			switch ($filter_key)
			{
				case 'component':
					$dbname = TBGIssueAffectsComponentTable::getTable();
					break;
				case 'edition':
					$dbname = TBGIssueAffectsEditionTable::getTable();
					break;
				case 'build':
					$dbname = TBGIssueAffectsBuildTable::getTable();
					break;
				default:
					$dbname = TBGIssuesTable::getTable()->getB2DBName();
			}

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

//						if ($ctn === null) $ctn = $crit->returnCriterion($dbname.'.'.$filter_key, $this['value'], urldecode($this['operator']));
//						if (in_array($this['operator'], array('=', '<=', '>=', '<', '>')) && !in_array($filter_key, array('posted', 'last_updated')))
//						{
//							$ctn->addOr($dbname.'.'.$filter_key, $this['value'], urldecode($this['operator']));
//						}
//						elseif ($this['operator'] == '!=' || in_array($filter_key, array('posted', 'last_updated')))
//						{
//							$ctn->addWhere($dbname.'.'.$filter_key, $this['value'], urldecode($this['operator']));
//						}

						return $ctn;
					}
				}
				elseif (TBGCustomDatatype::doesKeyExist($filter_key))
				{
					$customdatatype = TBGCustomDatatype::getByKey($filter_key);
					if ($ctn === null)
					{
						$ctn = $crit->returnCriterion(TBGIssueCustomFieldsTable::CUSTOMFIELDS_ID, $customdatatype->getID());
						$ctn->addWhere(TBGIssueCustomFieldsTable::CUSTOMFIELDOPTION_ID, $this['value'], $this['operator']);
					}
					else
					{
						$ctn->addOr(TBGIssueCustomFieldsTable::CUSTOMFIELDOPTION_ID, $this['value'], $this['operator']);
					}
					return $ctn;
				}
			}
		}

	}
