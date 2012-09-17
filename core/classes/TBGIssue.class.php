<?php

	/**
	 * Issue class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage main
	 */

	/**
	 * Issue class
	 *
	 * @package thebuggenie
	 * @subpackage main
	 *
	 * @Table(name="TBGIssuesTable")
	 */
	class TBGIssue extends TBGChangeableItem
	{
	
		/**
		 * Open issue state
		 * 
		 * @static integer
		 */
		const STATE_OPEN = 0;
		
		/**
		 * Closed issue state
		 * 
		 * @static integer
		 */
		const STATE_CLOSED = 1;

		/**
		 * @Column(type="string", name="name", length=255)
		 */
		protected $_title;

		/**
		 * Array of links attached to this issue
		 *
		 * @var array
		 */
		protected $_links = null;
	
		/**
		 * Array of files attached to this issue
		 *
		 * @var array
		 */
		protected $_files = null;

		/**
		 * Number of attached files
		 *
		 * @var integer
		 */
		protected $_num_files = null;
		
		/**
		 * The issue number
		 *
		 * @var integer
		 * @Column(type="integer", length=10)
		 */
		protected $_issue_no;
		
		/**
		 * The issue type
		 *
		 * @var TBGIssuetype
		 * @Column(type="integer", length=10)
		 * @Relates(class="TBGIssuetype")
		 */
		protected $_issuetype;
		
		/**
		 * The project which this issue affects
		 *
		 * @var TBGProject
		 * @access protected
		 * @Column(type="integer", length=10)
		 * @Relates(class="TBGProject")
		 */
		protected $_project_id;
		
		/**
		 * The affected editions for this issue
		 *
		 * @var array
		 */
		protected $_editions = null;
		
		/**
		 * The affected builds for this issue
		 * 
		 * @var array
		 */
		protected $_builds = null;
		
		/**
		 * The affected components for this issue
		 * 
		 * @var array
		 */
		protected $_components = null;

		/**
		 * This issues long description
		 * 
		 * @var string
		 * @Column(type="text")
		 */
		protected $_description;
		
		/**
		 * This issues reproduction steps
		 * 
		 * @var string
		 * @Column(type="text")
		 */
		protected $_reproduction_steps;
		
		/**
		 * When the issue was posted
		 * 
		 * @var integer
		 * @Column(type="integer", length=10)
		 */
		protected $_posted;
		
		/**
		 * When the issue was last updated
		 * 
		 * @var integer
		 * @Column(type="integer", length=10)
		 */
		protected $_last_updated;
		
		/**
		 * Who posted the issue
		 * 
		 * @var TBGUser
		 * @Column(type="integer", length=10)
		 * @Relates(class="TBGUser")
		 */
		protected $_posted_by;
		
		/**
		 * The project assignee if team
		 *
		 * @var TBGTeam
		 * @Column(type="integer", length=10)
		 * @Relates(class="TBGTeam")
		 */
		protected $_assignee_team;

		/**
		 * The project assignee if user
		 *
		 * @var TBGUser
		 * @Column(type="integer", length=10)
		 * @Relates(class="TBGUser")
		 */
		protected $_assignee_user;

		/**
		 * What kind of bug this is
		 * 
		 * @var integer
		 * @Column(type="integer", length=3)
		 */
		protected $_pain_bug_type;

		/**
		 * What effect this bug has on users
		 *
		 * @var integer
		 * @Column(type="integer", length=3)
		 */
		protected $_pain_effect;

		/**
		 * How likely users are to experience this bug
		 *
		 * @var integer
		 * @Column(type="integer", length=3)
		 */
		protected $_pain_likelihood;

		/**
		 * Calculated user pain score
		 * 
		 * @var float
		 * @Column(type="float")
		 */
		protected $_user_pain = 0.00;
		
		/**
		 * The resolution
		 * 
		 * @var TBGResolution
		 * @Column(type="integer", length=10)
		 * @Relates(class="TBGResolution")
		 */
		protected $_resolution;
		
		/**
		 * The issues' state (open or closed)
		 * 
		 * @var integer
		 * @Column(type="integer", length=2)
		 */
		protected $_state = self::STATE_OPEN;
		
		/**
		 * The category
		 * 
		 * @var TBGCategory
		 * @Column(type="integer", length=10)
		 * @Relates(class="TBGCategory")
		 */
		protected $_category;
		
		/**
		 * The status
		 * 
		 * @var TBGStatus
		 * @Column(type="integer", length=10)
		 * @Relates(class="TBGStatus")
		 */
		protected $_status;
		
		/**
		 * The prioroty
		 * 
		 * @var TBGPriority
		 * @Column(type="integer", length=10)
		 * @Relates(class="TBGPriority")
		 */
		protected $_priority;
		
		/**
		 * The reproducability
		 * 
		 * @var TBGReproducability
		 * @Column(type="integer", length=10)
		 * @Relates(class="TBGReproducability")
		 */
		protected $_reproducability;
		
		/**
		 * The severity
		 * 
		 * @var TBGSeverity
		 * @Column(type="integer", length=10)
		 * @Relates(class="TBGSeverity")
		 */
		protected $_severity;

		/**
		 * The scrum color
		 *
		 * @var string
		 * @Column(type="string", length=7, default_value="#FFFFFF")
		 */
		protected $_scrumcolor;

		/**
		 * The estimated time (months) to fix this issue
		 * 
		 * @var integer
		 * @Column(type="integer", length=10)
		 */
		protected $_estimated_months;

		/**
		 * The estimated time (weeks) to fix this issue
		 * 
		 * @var integer
		 * @Column(type="integer", length=10)
		 */
		protected $_estimated_weeks;

		/**
		 * The estimated time (days) to fix this issue
		 * 
		 * @var integer
		 * @Column(type="integer", length=10)
		 */
		protected $_estimated_days;

		/**
		 * The estimated time (hours) to fix this issue
		 * 
		 * @var integer
		 * @Column(type="integer", length=10)
		 */
		protected $_estimated_hours;

		/**
		 * The estimated time (points) to fix this issue
		 * 
		 * @var integer
		 * @Column(type="integer", length=10)
		 */
		protected $_estimated_points;

		/**
		 * The time spent (months) to fix this issue
		 * 
		 * @var integer
		 * @Column(type="integer", length=10)
		 */
		protected $_spent_months;

		/**
		 * The time spent (weeks) to fix this issue
		 * 
		 * @var integer
		 * @Column(type="integer", length=10)
		 */
		protected $_spent_weeks;

		/**
		 * The time spent (days) to fix this issue
		 * 
		 * @var integer
		 * @Column(type="integer", length=10)
		 */
		protected $_spent_days;

		/**
		 * The time spent (hours) to fix this issue
		 * 
		 * @var integer
		 * @Column(type="integer", length=10)
		 */
		protected $_spent_hours;

		/**
		 * The time spent (points) to fix this issue
		 * 
		 * @var integer
		 * @Column(type="integer", length=10)
		 */
		protected $_spent_points;
		
		/**
		 * How far along the issus is
		 * 
		 * @var integer
		 * @Column(type="integer", length=2)
		 */
		protected $_percent_complete;
		
		/**
		 * Which user is currently working on this issue
		 * 
		 * @var TBGUser
		 * @Column(type="integer", length=10)
		 * @Relates(class="TBGUser")
		 */
		protected $_being_worked_on_by_user;
		
		/**
		 * When the last user started working on the issue
		 * 
		 * @var integer
		 * @Column(type="integer", length=10)
		 * @Relates(class="TBGUser")
		 */
		protected $_being_worked_on_by_user_since;
		
		/**
		 * List of tasks for this issue
		 * 
		 * @var array
		 */
		protected $_tasks;

		/**
		 * List of tags for this issue
		 *
		 * @var array
		 */
		protected $_tags;

		/**
		 * Whether the issue is deleted
		 * 
		 * @var boolean
		 * @Column(type="boolean")
		 */
		protected $_deleted = false;
		
		/**
		 * Whether the issue is blocking the next release
		 * 
		 * @var boolean
		 * @Column(type="boolean")
		 */
		protected $_blocking = false;

		/**
		 * Votes for this issue
		 * 
		 * @var array
		 */
		protected $_votes = null;

		/**
		 * Sum of votes for this issue
		 *
		 * @var integer
		 * @Column(type="integer", length=10)
		 */
		protected $_votes_total = null;
		
		/**
		 * The issue this issue is a duplicate of
		 * 
		 * @var TBGIssue
		 * @Column(type="integer", length=10)
		 * @Relates(class="TBGIssue")
		 */
		protected $_duplicate_of;
		
		/**
		 * The milestone this issue is assigned to
		 * 
		 * @var TBGMilestone
		 * @Column(type="integer", length=10)
		 * @Relates(class="TBGMilestone")
		 */
		protected $_milestone;
		
		/**
		 * List of issues this issue depends on
		 * 
		 * @var array
		 */
		protected $_parent_issues;
		
		/**
		 * List of issues that depends on this issue
		 * 
		 * @var array
		 */
		protected $_child_issues;
		
		/**
		 * List of issues which are duplicates of this one
		 * 
		 * @var array
		 */
		protected $_duplicate_issues;
		
		/**
		 * List of log entries
		 * 
		 * @var array
		 */
		protected $_log_entries;

		/**
		 * Whether the issue is locked for changes
		 *
		 * @var boolean
		 * @Column(type="boolean")
		 */
		protected $_locked;

		/**
		 * The issues current step in the associated workflow
		 *
		 * @var TBGWorkflowStep
		 * @Column(type="integer", length=10)
		 * @Relates(class="TBGWorkflowStep")
		 */
		protected $_workflow_step_id;

		/**
		 * An array of TBGComments
		 * 
		 * @var array
		 * @Relates(class="TBGComment", collection=true, foreign_column="target_id")
		 */
		protected $_comments;

		protected $_num_comments;

		protected $_num_user_comments;

		protected $_custom_populated = false;

		protected $_log_items_added = array();

		protected $_save_comment = '';

		/**
		 * All custom data type properties
		 *
		 * @property $_customfield*
		 * @var mixed
		 */
		
		/**
		 * Count the number of open and closed issues for a specific project id
		 * 
		 * @param integer $project_id The project ID
		 * 
		 * @return array
		 */
		public static function getIssueCountsByProjectID($project_id)
		{
			return TBGIssuesTable::getTable()->getCountsByProjectID($project_id);
		}

		public static function getPainTypesOrLabel($type, $id = null)
		{
			$i18n = TBGContext::getI18n();

			$bugtypes = array();
			$bugtypes[7] = $i18n->__('Crash: Bug causes crash or data loss / asserts in the debug release');
			$bugtypes[6] = $i18n->__('Major usability: Impairs usability in key scenarios');
			$bugtypes[5] = $i18n->__('Minor usability: Impairs usability in secondary scenarios');
			$bugtypes[4] = $i18n->__('Balancing: Enables degenerate usage strategies that harm the experience');
			$bugtypes[3] = $i18n->__('Visual and Sound Polish: Aesthetic issues');
			$bugtypes[2] = $i18n->__('Localization');
			$bugtypes[1] = $i18n->__('Documentation: A documentation issue');

			$effects = array();
			$effects[5] = $i18n->__('Blocking further progress on the daily build');
			$effects[4] = $i18n->__('A User would return the product / cannot RTM / the team would hold the release for this bug');
			$effects[3] = $i18n->__('A User would likely not purchase the product / will show up in review / clearly a noticeable issue');
			$effects[2] = $i18n->__("A Pain – users won't like this once they notice it / a moderate number of users won't buy");
			$effects[1] = $i18n->__('Nuisance – not a big deal but noticeable / extremely unlikely to affect sales');

			$likelihoods = array();
			$likelihoods[5] = $i18n->__('Will affect all users');
			$likelihoods[4] = $i18n->__('Will affect most users');
			$likelihoods[3] = $i18n->__('Will affect average number of users');
			$likelihoods[2] = $i18n->__('Will only affect a few users');
			$likelihoods[1] = $i18n->__('Will affect almost no one');

			if ($id === 0) return null;

			switch ($type)
			{
				case 'pain_bug_type':
					return ($id === null) ? $bugtypes : $bugtypes[$id];
					break;
				case 'pain_likelihood':
					return ($id === null) ? $likelihoods : $likelihoods[$id];
					break;
				case 'pain_effect':
					return ($id === null) ? $effects : $effects[$id];
					break;
			}

			return ($id === null) ? array() : null;
		}

		/**
		 * Count the number of open and closed issues for a specific project id
		 * and issue type id
		 * 
		 * @param integer $project_id The project ID
		 * @param integer $issuetype_id The issue type ID
		 * 
		 * @return array
		 */
		public static function getIssueCountsByProjectIDandIssuetype($project_id, $issuetype_id)
		{
			return TBGIssuesTable::getTable()->getCountsByProjectIDandIssuetype($project_id, $issuetype_id);
		}

		/**
		 * Count the number of open and closed issues for a specific project id
		 * and milestone id
		 * 
		 * @param integer $project_id The project ID
		 * @param integer $milestone_id The milestone ID
		 * @param boolean $exclude_tasks Whether to exclude tasks
		 * 
		 * @return array
		 */
		public static function getIssueCountsByProjectIDandMilestone($project_id, $milestone_id, $exclude_tasks = false)
		{
			return TBGIssuesTable::getTable()->getCountsByProjectIDandMilestone($project_id, $milestone_id, $exclude_tasks);
		}
		
		/**
		 * Returns a TBGIssue from an issue no
		 *
		 * @param string $issue_no An integer or issue number
		 * 
		 * @return TBGIssue
		 */
		public static function getIssueFromLink($issue_no, $project = null)
		{
			$project = ($project !== null) ? $project : TBGContext::getCurrentProject();
			$theIssue = null;
			$issue_no = mb_strtolower($issue_no);
			if (mb_strpos($issue_no, ' ') !== false)
			{
				$issue_no = mb_substr($issue_no, strrpos($issue_no, ' ') + 1);
			}
			if (mb_substr($issue_no, 0, 1) == '#') $issue_no = mb_substr($issue_no, 1);
			if (is_numeric($issue_no))
			{
				try
				{
					if (!$project instanceof TBGProject) return null;
					if ($project->usePrefix()) return null;
					$theIssue = TBGIssuesTable::getTable()->getByProjectIDAndIssueNo($project->getID(), (integer) $issue_no);
				}
				catch (Exception $e)
				{
					throw $e;
				}
			}
			else
			{
				$issue_no = explode('-', mb_strtoupper($issue_no));
				TBGLogging::log('exploding');
				if (count($issue_no) == 2 && ($theIssue = TBGIssuesTable::getTable()->getByPrefixAndIssueNo($issue_no[0], $issue_no[1])) instanceof TBGIssue)
				{
					if (!$theIssue->getProject()->usePrefix()) return null;
				}
				TBGLogging::log('exploding done');
			}
		
			return ($theIssue instanceof TBGIssue) ? $theIssue : null;
		}

		public static function findIssues($filters = array(), $results_per_page = 30, $offset = 0, $groupby = null, $grouporder = null)
		{
			$issues = array();
			list ($res, $count) = TBGIssuesTable::getTable()->findIssues($filters, $results_per_page, $offset, $groupby, $grouporder);
			if ($res)
			{
				while ($row = $res->getNextRow())
				{
					try
					{
						$issue = TBGContext::factory()->TBGIssue($row->get(TBGIssuesTable::ID), $row);
						if (!$issue->hasAccess() || $issue->getProject()->isDeleted()) continue;
						$issues[] = $issue;
					}
					catch (Exception $e) {}
				}
			}
			return array($issues, $count);
		}

		public static function findIssuesByText($text, $project = null)
		{
			$issue = self::getIssueFromLink($text);
			if ($issue instanceof TBGIssue)
				return array(array($issue), 1);
			
			$filters = array('text' => array('value' => $text, 'operator' => '='));
			if ($project instanceof TBGProject)
			{
				$filters['project_id'] = array('value' => $project->getID(), 'operator' => '=');
			}
			return self::findIssues($filters);
		}
		
		/**
		 * Class constructor
		 *
		 * @param \b2db\Row $row
		 */
		public function _construct(\b2db\Row $row, $foreign_key = null)
		{
			$this->_initializeCustomfields();
			$this->_mergeChangedProperties();
//			if ($this->isDeleted())
//			{
//				throw new Exception(TBGContext::geti18n()->__('This issue has been deleted'));
//			}
		}
		
		/**
		 * Print the issue number and title nicely formatted
		 *
		 * @param boolean $link_formatted[optional] Whether to include the # if it's only numeric (default false)
		 *
		 * @return string
		 */
		public function getFormattedTitle($link_formatted = false, $include_issuetype = true)
		{
			return $this->getFormattedIssueNo($link_formatted, $include_issuetype) . ' - ' . $this->getTitle();
		}

		public function getAccessList()
		{
			$permissions = TBGPermissionsTable::getTable()->getByPermissionTargetIDAndModule('canviewissue', $this->getID());
			return $permissions;
		}
		
		/**
		 * Whether or not the current user can access the issue
		 * 
		 * @return boolean
		 */
		public function hasAccess()
		{
			TBGLogging::log('checking access to issue ' . $this->getFormattedIssueNo());
			$i_id = $this->getID();
			$user = TBGContext::getUser();
			if (!$user->isGuest() && $user->isAuthenticated())
			{
				$specific_access = $user->hasPermission("canviewissue", $i_id, 'core', true, null);
				if ($specific_access !== null)
				{
					TBGLogging::log('done checking, returning specific access ' . (($specific_access) ? 'allowed' : 'denied'));
					return $specific_access;
				}
				if ($this->getPostedByID() == $user->getID())
				{
					TBGLogging::log('done checking, allowed since this user posted it');
					return true;
				}
				if ($this->getOwner() instanceof TBGUser && $this->getOwner()->getID() == $user->getID())
				{
					TBGLogging::log('done checking, allowed since this user owns it');
					return true;
				}
				if ($this->getAssignee() instanceof TBGUser && $this->getAssignee()->getID() == $user->getID())
				{
					TBGLogging::log('done checking, allowed since this user is assigned to it');
					return true;
				}
				if ($user->hasPermission('canseegroupissues', 0, 'core', true, true) &&
					$this->getPostedBy() instanceof TBGUser &&
					$this->getPostedBy()->getGroupID() == $user->getGroupID())
				{
					TBGLogging::log('done checking, allowed since this user is in same group as user that posted it');
					return true;
				}
				if ($user->hasPermission('canseeallissues', 0, 'core', true, true) === false)
				{
					TBGLogging::log('done checking, not allowed to access issues not posted by themselves');
					return false;
				}
			}
			if ($this->getCategory() instanceof TBGCategory)
			{
				if (!$this->getCategory()->hasAccess())
				{
					TBGLogging::log('done checking, not allowed to access issues in this category');
					return false;
				}
			}
			if ($this->getProject()->hasAccess())
			{
				TBGLogging::log('done checking, can access project');
				return true;
			}
			TBGLogging::log('done checking, denied');
			return false;
		}
		
		public function setProject($project)
		{
			$this->_project_id = $project;
		}

		/**
		 * Returns the project for this issue
		 *
		 * @return TBGProject
		 */
		public function getProject()
		{
			return $this->_b2dbLazyload('_project_id');
		}
		
		/**
		 * Returns the project id for this issue
		 * 
		 * @return integer
		 */
		public function getProjectID()
		{
			$project = $this->getProject();
			return ($project instanceof TBGProject) ? $project->getID() : null;
		}

		/**
		 * Return the issues current step in the workflow
		 * 
		 * @return TBGWorkflowStep
		 */
		public function getWorkflowStep()
		{
			return $this->_b2dbLazyload('_workflow_step_id');
		}

		public function getWorkflow()
		{
			return $this->getProject()->getWorkflowScheme()->getWorkflowForIssuetype($this->getIssueType());
		}
		
		public function setWorkflowStep(TBGWorkflowStep $step)
		{
			$this->_addChangedProperty('_workflow_step_id', $step->getID());
		}
		
		public function getAvailableWorkflowTransitions()
		{
			return ($this->getWorkflowStep() instanceof TBGWorkflowStep) ? $this->getWorkflowStep()->getAvailableTransitionsForIssue($this) : array();
		}

		protected function _initializeCustomfields()
		{
			foreach (TBGCustomDatatype::getAll() as $key => $customdatatype)
			{
				$var_name = "_customfield".$key;
				$this->$var_name = null;
			}
			if ($res = TBGIssueCustomFieldsTable::getTable()->getAllValuesByIssueID($this->getID()))
			{
				while ($row = $res->getNextRow())
				{
					$datatype = new TBGCustomDatatype($row->get(TBGIssueCustomFieldsTable::CUSTOMFIELDS_ID));
					$var_name = "_customfield".$datatype->getKey();

					if ($datatype->hasCustomOptions())
					{
						$option = TBGCustomFieldOptionsTable::getTable()->selectById((int) $row->get(TBGIssueCustomFieldsTable::CUSTOMFIELDOPTION_ID));
						if ($option instanceof TBGCustomDatatypeOption)
						{
							$this->$var_name = $option;
						}
					}
					else if($datatype->hasPredefinedOptions())
					{
						$this->$var_name = $row->get(TBGIssueCustomFieldsTable::CUSTOMFIELDOPTION_ID);
					}
					else
					{
						$this->$var_name = $row->get(TBGIssueCustomFieldsTable::OPTION_VALUE);
					}
				}
			}
		}

		/**
		 * Populates the affected items
		 */
		protected function _populateAffected()
		{
			if ($this->_editions === null && $this->_builds === null && $this->_components === null)
			{
				$this->_editions = array();
				$this->_builds = array();
				$this->_components = array();
		
				if ($res = TBGIssueAffectsEditionTable::getTable()->getByIssueID($this->getID()))
				{
					while ($row = $res->getNextRow())
					{
						try
						{
							$status_id = $row->get(TBGIssueAffectsEditionTable::STATUS);
							$this->_editions[$row->get(TBGIssueAffectsEditionTable::ID)] = array(	'edition' => TBGContext::factory()->TBGEdition($row->get(TBGIssueAffectsEditionTable::EDITION)),
														'status' => ($status_id) ? TBGContext::factory()->TBGStatus($status_id, $row) : null,
														'confirmed' => (bool) $row->get(TBGIssueAffectsEditionTable::CONFIRMED),
														'a_id' => $row->get(TBGIssueAffectsEditionTable::ID));
						}
						catch (Exception $e) {}
					}
				}
				
				if ($res = TBGIssueAffectsBuildTable::getTable()->getByIssueID($this->getID()))
				{
					while ($row = $res->getNextRow())
					{
						try
						{
							$status_id = $row->get(TBGIssueAffectsBuildTable::STATUS);
							$this->_builds[$row->get(TBGIssueAffectsBuildTable::ID)] = array(	'build' => TBGContext::factory()->TBGBuild($row->get(TBGIssueAffectsBuildTable::BUILD)),
														'status' => ($status_id) ? TBGContext::factory()->TBGStatus($status_id, $row) : null,
														'confirmed' => (bool) $row->get(TBGIssueAffectsBuildTable::CONFIRMED),
														'a_id' => $row->get(TBGIssueAffectsBuildTable::ID));
						}
						catch (Exception $e) {}
					}
				}
				
				if ($res = TBGIssueAffectsComponentTable::getTable()->getByIssueID($this->getID()))
				{
					while ($row = $res->getNextRow())
					{
						try
						{
							$status_id = $row->get(TBGIssueAffectsComponentTable::STATUS);
							$this->_components[$row->get(TBGIssueAffectsComponentTable::ID)] = array(	'component' => TBGContext::factory()->TBGComponent($row->get(TBGIssueAffectsComponentTable::COMPONENT)),
															'status' => ($status_id) ? TBGContext::factory()->TBGStatus($status_id, $row) : null,
															'confirmed' => (bool) $row->get(TBGIssueAffectsComponentTable::CONFIRMED),
															'a_id' => $row->get(TBGIssueAffectsComponentTable::ID));
						}
						catch (Exception $e) {}
					}
				}
			}
		}
		
		/**
		 * Returns the unique id for this issue
		 *
		 * @return integer
		 */
		public function getID()
		{
			return $this->_id;
		}
		
		/**
		 * Returns the issue no for this issue
		 * 
		 * @return string
		 */
		public function getIssueNo()
		{
			return $this->_issue_no;
		}
		
		/**
		 * Returns the title for this issue
		 *
		 * @return string
		 */
		public function getName()
		{
			return $this->getTitle();
		}
		
		/**
		 * Whether or not this issue is a duplicate of another issue
		 * 
		 * @return boolean
		 */
		public function isDuplicate()
		{
			return ($this->getDuplicateOf() instanceof TBGIssue) ? true : false;
		}
		
		/**
		 * Mark this issue as a duplicate of another issue
		 * 
		 * @param integer $d_id Issue ID for the duplicated issue
		 */
		public function setDuplicateOf($d_id)
		{
			TBGIssuesTable::getTable()->setDuplicate($this->getID(), $d_id);
			if ($d_id)
			{
				TBGUserIssuesTable::getTable()->copyStarrers($this->getID(), $d_id);
			}
			$this->_duplicate_of = $d_id;
		}
		
		/**
		 * Clears the issue from being a duplicate
		 */
		public function clearDuplicate()
		{
			$this->setDuplicateOf(0);
		}
		
		/**
		 * Returns the issue which this is a duplicate of
		 *
		 * @return TBGIssue
		 */
		public function getDuplicateOf()
		{
			/*if (is_numeric($this->_duplicate_of))
			{
				try
				{
					$this->_duplicate_of = TBGContext::factory()->TBGIssue($this->_duplicate_of);
				}
				catch (Exception $e) 
				{
					$this->_duplicate_of = null;
				}
			}
			return $this->_duplicate_of;*/
			return $this->_b2dbLazyload('_duplicate_of');
		}
		
		/**
		 * Returns an array of all issues which are duplicates of this one
		 * 
		 * @return array of TBGIssues
		 */
		public function getDuplicateIssues()
		{
			$this->_populateDuplicateIssues();
			return $this->_duplicate_issues;
		}
		
		/**
		 * Returns whether or not this item is locked
		 *
		 * @return boolean
		 * @access public
		 */
		public function isLocked()
		{
			return $this->_locked;
		}

		/**
		 * Returns whether or not this item is locked
		 *
		 * @return boolean
		 * @access public
		 */
		public function isUnlocked()
		{
			return !$this->isLocked();
		}

		/**
		 * Specify whether or not this item is locked
		 *
		 * @param boolean $locked[optional]
		 */
		public function setLocked($locked = true)
		{
			$this->_locked = (bool) $locked;
		}

		public function isEditable()
		{
			if ($this->getProject()->isArchived()) return false;
			return ($this->isOpen() && ($this->getProject()->canChangeIssuesWithoutWorkingOnThem() || ($this->getWorkflowStep() instanceof TBGWorkflowStep && $this->getWorkflowStep()->isEditable())));
		}
		
		public function isUpdateable()
		{
			if ($this->getProject()->isArchived()) return false;
			return ($this->isOpen() && ($this->getProject()->canChangeIssuesWithoutWorkingOnThem() || !($this->getWorkflowStep() instanceof TBGWorkflowStep) || !$this->getWorkflowStep()->isClosed()));
		}
		
		/**
		 * Perform a permission check based on a key, and whether or not to
		 * check for the equivalent "*own" permission if the issue is posted
		 * by the same user
		 *
		 * @param string $key The permission key to check for
		 * @param boolean $exclusive Whether to perform a similar check for "own"
		 *
		 * @return boolean
		 */
		protected function _permissionCheck($key, $exclusive = false)
		{
			if (TBGContext::getUser()->isGuest()) return false;
			$retval = ($this->isInvolved() && !$exclusive) ? $this->getProject()->permissionCheck($key.'own', true) : null;
			$retval = ($retval !== null) ? $retval : $this->getProject()->permissionCheck($key, !$this->isInvolved());

			return $retval;
		}

		public function isWorkflowTransitionsAvailable()
		{
			if ($this->getProject()->isArchived()) return false;
			return (bool) $this->_permissionCheck('caneditissue', true);
		}

		public function isInvolved()
		{
			$user_id = TBGContext::getUser()->getID();
			return (bool) ($this->getPostedByID() == $user_id || ($this->isAssigned() && $this->getAssignee()->getID() == $user_id && $this->getAssignee() instanceof TBGUser) || ($this->isOwned() && $this->getOwner()->getID() == $user_id && $this->getOwner() instanceof TBGUser));
		}
		
		/**
		 * Return if the user can edit title
		 *
		 * @return boolean
		 */
		public function canEditAccessPolicy()
		{
			return $this->_permissionCheck('canlockandeditlockedissues');
		}

		/**
		 * Check whether or not this user can edit issue details
		 * 
		 * @return boolean
		 */
		public function canEditIssueDetails()
		{
			$retval = $this->_permissionCheck('caneditissuebasic');
			$retval = ($retval === null) ? ($this->isInvolved() || $this->_permissionCheck('cancreateandeditissues')) : $retval;
			$retval = ($retval === null) ? $this->_permissionCheck('caneditissue', true) : $retval;

			return ($retval !== null) ? $retval : TBGSettings::isPermissive();
		}
		
		/**
		 * Return if the user can edit title
		 *
		 * @return boolean
		 */
		public function canEditTitle()
		{
			$retval = $this->_permissionCheck('caneditissuetitle');
			$retval = ($retval === null) ? $this->canEditIssueDetails() : $retval;
			
			return ($retval !== null) ? $retval : TBGSettings::isPermissive();
		}

		/**
		 * Return if the user can edit description
		 *
		 * @return boolean
		 */
		public function canEditIssuetype()
		{
			return $this->canEditIssueDetails();
		}

		/**
		 * Return if the user can edit description
		 *
		 * @return boolean
		 */
		public function canEditUserPain()
		{
			return $this->canEditIssueDetails();
		}

		/**
		 * Return if the user can edit description
		 *
		 * @return boolean
		 */
		public function canEditDescription()
		{
			$retval = $this->_permissionCheck('caneditissuedescription');
			$retval = ($retval === null) ? $this->canEditIssueDetails() : $retval;
			
			return ($retval !== null) ? $retval : TBGSettings::isPermissive();
		}

		/**
		 * Return if the user can edit description
		 *
		 * @return boolean
		 */
		public function canEditReproductionSteps()
		{
			$retval = $this->_permissionCheck('caneditissuereproduction_steps');
			$retval = ($retval === null) ? $this->canEditIssueDetails() : $retval;
			
			return ($retval !== null) ? $retval : TBGSettings::isPermissive();
		}

		/**
		 * Return if the user can edit basic parameters
		 *
		 * @return boolean
		 */
		public function canEditIssue()
		{
			return (bool) ($this->_permissionCheck('caneditissue', true));
		}
		
		protected function _canPermissionOrEditIssue($permission, $fallback = null)
		{
			$retval = $this->_permissionCheck($permission);
			$retval = ($retval === null) ? $this->canEditIssue() : $retval;
			
			if ($retval !== null)
			{
				return $retval;
			}
			else
			{
				return ($fallback !== null) ? $fallback : TBGSettings::isPermissive();
			}
		}
		
		/**
		 * Return if the user can edit posted by
		 *
		 * @return boolean
		 */
		public function canEditPostedBy()
		{
			return $this->_canPermissionOrEditIssue('caneditissueposted_by');
		}

		/**
		 * Return if the user can edit assigned to
		 *
		 * @return boolean
		 */
		public function canEditAssignee()
		{
			return $this->_canPermissionOrEditIssue('caneditissueassigned_to');
		}
		
		/**
		 * Return if the user can edit owned by
		 *
		 * @return boolean
		 */
		public function canEditOwner()
		{
			return $this->_canPermissionOrEditIssue('caneditissueowned_by');
		}
		
		/**
		 * Return if the user can edit status
		 *
		 * @return boolean
		 */
		public function canEditStatus()
		{
			return $this->_canPermissionOrEditIssue('caneditissuestatus');
		}
		
		/**
		 * Return if the user can edit category
		 *
		 * @return boolean
		 */
		public function canEditCategory()
		{
			return $this->_canPermissionOrEditIssue('caneditissuecategory');
		}
		
		/**
		 * Return if the user can edit resolution
		 *
		 * @return boolean
		 */
		public function canEditResolution()
		{
			return $this->_canPermissionOrEditIssue('caneditissueresolution');
		}
		
		/**
		 * Return if the user can edit reproducability
		 *
		 * @return boolean
		 */
		public function canEditReproducability()
		{
			return $this->_canPermissionOrEditIssue('caneditissuereproducability');
		}
		
		/**
		 * Return if the user can edit severity
		 *
		 * @return boolean
		 */
		public function canEditSeverity()
		{
			return $this->_canPermissionOrEditIssue('caneditissueseverity');
		}
		
		/**
		 * Return if the user can edit priority
		 *
		 * @return boolean
		 */
		public function canEditPriority()
		{
			return $this->_canPermissionOrEditIssue('caneditissuepriority');
		}
		
		/**
		 * Return if the user can edit estimated time
		 *
		 * @return boolean
		 */
		public function canEditEstimatedTime()
		{
			return $this->_canPermissionOrEditIssue('caneditissueestimated_time');
		}
		
		/**
		 * Return if the user can edit spent time
		 *
		 * @return boolean
		 */
		public function canEditSpentTime()
		{
			return $this->_canPermissionOrEditIssue('caneditissuespent_time');
		}
		
		/**
		 * Return if the user can edit progress (percent)
		 *
		 * @return boolean
		 */
		public function canEditPercentage()
		{
			return $this->_canPermissionOrEditIssue('caneditissuepercent_complete');
		}

		/**
		 * Return if the user can edit milestone
		 *
		 * @return boolean
		 */
		public function canEditMilestone()
		{
			return $this->_canPermissionOrEditIssue('caneditissuemilestone');
		}
		
		/**
		 * Return if the user can delete the issue
		 *
		 * @return boolean
		 */
		public function canDeleteIssue()
		{
			return $this->_canPermissionOrEditIssue('candeleteissues', false);
		}
		
		/**
		 * Return if the user can edit any custom fields
		 *
		 * @return boolean
		 */
		public function canEditCustomFields()
		{
			return (bool) $this->_permissionCheck('caneditissuecustomfields');
		}

		/**
		 * Return if the user can close the issue
		 *
		 * @return boolean
		 */
		public function canCloseIssue()
		{
			$retval = $this->_permissionCheck('cancloseissues');
			$retval = ($retval === null) ? $this->_permissionCheck('canclosereopenissues') : $retval;
			$retval = ($retval === null) ? $this->canEditIssue() : $retval;
			
			return ($retval !== null) ? $retval : TBGSettings::isPermissive();
		}

		/**
		 * Return if the user can close the issue
		 *
		 * @return boolean
		 */
		public function canReopenIssue()
		{
			$retval = $this->_permissionCheck('canreopenissues');
			$retval = ($retval === null) ? $this->_permissionCheck('canclosereopenissues') : $retval;
			$retval = ($retval === null) ? $this->canEditIssue() : $retval;
			
			return ($retval !== null) ? $retval : TBGSettings::isPermissive();
		}

		protected function _dualPermissionsCheck($permission_1, $permission_2)
		{
			$retval = $this->_permissionCheck($permission_1);
			$retval = ($retval === null) ? $this->_permissionCheck($permission_2) : $retval;
			
			return ($retval !== null) ? $retval : TBGSettings::isPermissive();
		}
		
		/**
		 * Return if the user can add/modify extra data for an issue
		 *
		 * @return boolean
		 */
		public function canAddExtraInformation()
		{
			return (bool) $this->_permissionCheck('canaddextrainformationtoissues');
		}

		protected function _canPermissionsOrExtraInformation($permission)
		{
			$retval = $this->_permissionCheck($permission);
			$retval = ($retval === null) ? $this->canAddExtraInformation() : $retval;
			
			return ($retval !== null) ? $retval : TBGSettings::isPermissive();
		}
		
		/**
		 * Return if the user can post comments on this issue
		 *
		 * @return boolean
		 */
		public function canPostComments()
		{
			return $this->_dualPermissionsCheck('canpostcomments', 'canpostandeditcomments');
		}

		/**
		 * Return if the user can attach files
		 *
		 * @return boolean
		 */
		public function canAttachFiles()
		{
			return $this->_canPermissionsOrExtraInformation('canaddfilestoissues');
		}

		/**
		 * Return if the user can add related issues to this issue
		 *
		 * @return boolean
		 */
		public function canAddRelatedIssues()
		{
			return $this->_canPermissionsOrExtraInformation('canaddrelatedissues');
		}

		/**
		 * Return if the user can add related issues to this issue
		 *
		 * @return boolean
		 */
		public function canEditAffectedComponents()
		{
			return $this->_canPermissionsOrExtraInformation('canaddcomponents');
		}

		/**
		 * Return if the user can add related issues to this issue
		 *
		 * @return boolean
		 */
		public function canEditAffectedEditions()
		{
			return $this->_canPermissionsOrExtraInformation('canaddeditions');
		}

		/**
		 * Return if the user can add related issues to this issue
		 *
		 * @return boolean
		 */
		public function canEditAffectedBuilds()
		{
			return $this->_canPermissionsOrExtraInformation('canaddbuilds');
		}

		/**
		 * Return if the user can remove attachments
		 *
		 * @return boolean
		 */
		public function canRemoveAttachments()
		{
			return $this->_canPermissionsOrExtraInformation('canremovefilesfromissues');
		}

		/**
		 * Return if the user can attach links
		 *
		 * @return boolean
		 */
		public function canAttachLinks()
		{
			return $this->_canPermissionsOrExtraInformation('canaddlinkstoissues');
		}

		/**
		 * Return if the user can start working on the issue
		 * 
		 * @return boolean
		 */
		public function canStartWorkingOnIssue()
		{
			if ($this->isBeingWorkedOn()) return false;
			return $this->canEditSpentTime();
		}
	
		/**
		 * Returns a complete issue no
		 * 
		 * @param boolean $link_formatted[optional] Whether to include the # if it's only numeric (default false)
		 * 
		 * @return string
		 */
		public function getFormattedIssueNo($link_formatted = false, $include_issuetype = false)
		{
			try
			{
				$issuetype_description = ($this->getIssueType() instanceof TBGIssuetype && $include_issuetype) ? $this->getIssueType()->getName().' ' : '';
			}
			catch (Exception $e)
			{
				$issuetype_description = TBGContext::getI18n()->__('Unknown issuetype') . ' ';
			}

			if ($this->getProject()->usePrefix())
			{
				$issue_no = $this->getProject()->getPrefix() . '-' . $this->getIssueNo();
			}
			else
			{
				$issue_no = (($link_formatted) ? '#' : '') . $this->getIssueNo();
			}
			return $issuetype_description . $issue_no;
		}
	
		/**
		 * Returns the issue type for this issue
		 *
		 * @return TBGIssuetype
		 */
		public function getIssueType()
		{
			return $this->_b2dbLazyload('_issuetype');
		}

		public function hasIssueType()
		{
			try
			{
				return ($this->getIssueType() instanceof TBGIssuetype);
			}
			catch (Exception $e)
			{
				return false;
			}
		}
		
		/**
		 * Return timestamp for when the issue was posted
		 *
		 * @return integer
		 */
		public function getPosted()
		{
			return $this->_posted;
		}
		
		/**
		 * Set the posted time
		 * 
		 * @param integer $time 
		 */
		public function setPosted($time)
		{
			$this->_posted = $time;
		}
		
		/**
		 * Set the created at time
		 * 
		 * @see TBGIssue::setPosted()
		 * @param integer $time 
		 */
		public function setCreatedAt($time)
		{
			$this->setPosted($time);
		}
		
		/**
		 * Returns the issue status
		 *
		 * @return TBGDatatype
		 */
		public function getStatus()
		{
			return $this->_b2dbLazyload('_status');
		}
	
		/**
		 * Returns the editions for this issue
		 *
		 * @return array Returns an array with 'edition' (TBGEdition), 'status' (TBGDatatype), 'confirmed' (boolean) and 'a_id'
		 */
		public function getEditions()
		{
			$this->_populateAffected();
			return $this->_editions;
		}
		
		public function isEditionAffected(TBGEdition $edition)
		{
			$editions = $this->getEditions();
			if (count($editions))
			{
				foreach ($editions as $info)
				{
					if ($info['edition']->getID() == $edition->getID())
						return true;
				}
			}
			return false;
		}
		
		public function getFirstAffectedEdition()
		{
			$editions = $this->getEditions();
			if (count($editions))
			{
				foreach ($editions as $info)
				{
					return $info['edition'];
				}
			}
		}
		
		/**
		 * Returns the builds for this issue
		 *
		 * @return array Returns an array with 'build' (TBGBuild), 'status' (TBGDatatype), 'confirmed' (boolean) and 'a_id'
		 */
		public function getBuilds()
		{
			$this->_populateAffected();
			return $this->_builds;
		}
	
		public function isBuildAffected(TBGBuild $build)
		{
			$builds = $this->getBuilds();
			if (count($builds))
			{
				foreach ($builds as $info)
				{
					if ($info['build']->getID() == $build->getID())
						return true;
				}
			}
			return false;
		}
		
		public function getFirstAffectedBuild()
		{
			$builds = $this->getBuilds();
			if (count($builds))
			{
				foreach ($builds as $info)
				{
					return $info['build'];
				}
			}
		}
		
		/**
		 * Returns the components for this issue
		 *
		 * @return array Returns an array with 'component' (TBGComponent), 'status' (TBGDatatype), 'confirmed' (boolean) and 'a_id'
		 */
		public function getComponents()
		{
			$this->_populateAffected();
			return $this->_components;
		}
		
		public function isComponentAffected(TBGComponent $component)
		{
			$components = $this->getComponents();
			if (count($components))
			{
				foreach ($components as $info)
				{
					if ($info['component']->getID() == $component->getID())
						return true;
				}
			}
			return false;
		}

		public function getComponentNames()
		{
			$components = $this->getComponents();
			$names = array();
			foreach ($components as $info)
			{
				$names[] = $info['component']->getName();
			}

			return $names;
		}
		
		public function getFirstAffectedComponent()
		{
			$components = $this->getComponents();
			if (count($components))
			{
				foreach ($components as $info)
				{
					return $info['component'];
				}
			}
		}
		
		/**
		 * Returns a string-formatted time based on project setting
		 *
		 * @param array $time array of weeks, days and hours
		 * 
		 * @return string
		 */
		public function getFormattedTime($time)
		{
			$values = array();
			$i18n = TBGContext::getI18n();
			if (!is_array($time)) throw new Exception("That's not a valid time");
			if (array_key_exists('months', $time) && $time['months'] > 0)
			{
				$values[] = ($time['months'] == 1) ? $i18n->__('1 month') : $i18n->__('%number_of% months', array('%number_of%' => $time['months']));
			}
			if (array_key_exists('weeks', $time) && $time['weeks'] > 0)
			{
				$values[] = ($time['weeks'] == 1) ? $i18n->__('1 week') : $i18n->__('%number_of% weeks', array('%number_of%' => $time['weeks']));
			}
			if (array_key_exists('days', $time) && $time['days'] > 0)
			{
				$values[] = ($time['days'] == 1) ? $i18n->__('1 day') : $i18n->__('%number_of% days', array('%number_of%' => $time['days']));
			}
			if (array_key_exists('hours', $time) && $time['hours'] > 0)
			{
				$values[] = ($time['hours'] == 1) ? $i18n->__('1 hour') : $i18n->__('%number_of% hours', array('%number_of%' => $time['hours']));
			}
			$retval = join(', ', $values);
			
			if (array_key_exists('points', $time) && $time['points'] > 0)
			{
				if (!empty($values))
				{
					$retval .= ' / ';
				}
				$retval .= ($time['points'] == 1) ? $i18n->__('1 point') : $i18n->__('%number_of% points', array('%number_of%' => $time['points']));
			}

			return ($retval != '') ? $retval : $i18n->__('No time');
		}
	
		/**
		 * Attach a link to the issue
		 * 
		 * @param string $url The url of the link
		 * @param string $description[optional] a description
		 */
		public function attachLink($url, $description = null)
		{
			$link_id = \b2db\Core::getTable('TBGLinksTable')->addLinkToIssue($this->getID(), $url, $description);
			return $link_id;
		}

		/**
		 * Attach a file to the issue
		 * 
		 * @param TBGFile $file The file to attach
		 */
		public function attachFile(TBGFile $file, $file_comment = '', $description = '')
		{
			TBGIssueFilesTable::getTable()->addByIssueIDandFileID($this->getID(), $file->getID());
			$comment = new TBGComment();
			$comment->setPostedBy(TBGContext::getUser()->getID());
			$comment->setTargetID($this->getID());
			$comment->setTargetType(TBGComment::TYPE_ISSUE);
			if ($file_comment)
			{
				$comment->setContent(TBGContext::getI18n()->__('A file was uploaded. %link_to_file% This comment was attached: %comment%', array('%comment%' => "\n\n".$file_comment, '%link_to_file%' => "[[File:{$file->getOriginalFilename()}|thumb|{$description}]]")));
			}
			else
			{
				$comment->setContent(TBGContext::getI18n()->__('A file was uploaded. %link_to_file%', array('%link_to_file%' => "[[File:{$file->getOriginalFilename()}|thumb|{$description}]]")));
			}
			$comment->save();
			if ($this->_files !== null)
			{
				$this->_files[$file->getID()] = $file;
			}
		}

		/**
		 * populates related issues
		 */
		protected function _populateRelatedIssues()
		{
			if ($this->_parent_issues === null || $this->_child_issues === null)
			{
				$this->_parent_issues = array();
				$this->_child_issues = array();
				
				if ($res = \b2db\Core::getTable('TBGIssueRelationsTable')->getRelatedIssues($this->getID()))
				{
					while ($row = $res->getNextRow())
					{
						try
						{
							if ($row->get(TBGIssueRelationsTable::PARENT_ID) == $this->getID())
							{
								$issue = TBGContext::factory()->TBGIssue($row->get(TBGIssueRelationsTable::CHILD_ID));
								$this->_child_issues[$row->get(TBGIssueRelationsTable::ID)] = $issue;
							}
							else
							{
								$issue = TBGContext::factory()->TBGIssue($row->get(TBGIssueRelationsTable::PARENT_ID));
								$this->_parent_issues[$row->get(TBGIssueRelationsTable::ID)] = $issue;
							}
						}
						catch (Exception $e) 
						{
						}
					}
				}
			}
		}
		
		/**
		 * populates list of issues which are duplicates of this one
		 */
		protected function _populateDuplicateIssues()
		{
			if ($this->_duplicate_issues === null)
			{
				$this->_duplicate_issues = array();
				
				if ($res = TBGIssuesTable::getTable()->getDuplicateIssuesByIssueNo($this->getID()))
				{
					while ($row = $res->getNextRow())
					{
						try
						{
							$issue = TBGContext::factory()->TBGIssue($row->get(TBGIssuesTable::ID));
							$this->_duplicate_issues[$row->get(TBGIssuesTable::ID)] = $issue;
						}
						catch (Exception $e) 
						{
						}
					}
				}
			}
		}
		
		/**
		 * Return issues relating to this
		 * 
		 * @return array
		 */
		public function getParentIssues()
		{
			$this->_populateRelatedIssues();
			return $this->_parent_issues;
		}

		public function isChildIssue()
		{
			return (bool) count($this->getParentIssues());
		}

		/**
		 * Return related issues
		 * 
		 * @return array
		 */
		public function getChildIssues()
		{
			$this->_populateRelatedIssues();
			return $this->_child_issues;
		} 
		
		/**
		 * Returns the vote sum for this issue
		 * 
		 * @return integer
		 */
		public function getVotes()
		{
			return (int) $this->_votes_total;
		}

		/**
		 * Set total number of votes
		 * 
		 * @param integer
		 */
		public function setVotes($votes)
		{
			$this->_votes_total = $votes;
		}
		
		/**
		 * Load user votes
		 */
		protected function _setupVotes()
		{
			if ($this->_votes === null)
			{
				$this->_votes = array();
				if ($res = TBGVotesTable::getTable()->getByIssueId($this->getID()))
				{
					while ($row = $res->getNextRow())
					{
						$this->_votes[$row->get(TBGVotesTable::UID)] = $row->get(TBGVotesTable::VOTE);
					}
				}
			}

		}

		/**
		 * Whether or not the current user has voted
		 *
		 * @return boolean
		 */
		public function hasUserVoted($user_id, $up)
		{
			$user_id = (is_object($user_id)) ? $user_id->getID() : $user_id;
			$this->_setupVotes();
			
			if (($user_id == TBGSettings::getDefaultUserID() && TBGSettings::isDefaultUserGuest()) || !$this->getProject()->canVoteOnIssues())
			{
				return true;
			}
			
			if (array_key_exists($user_id, $this->_votes))
			{
				return ($up) ? ((int) $this->_votes[$user_id] > 0) : ((int) $this->_votes[$user_id] < 0);
			}
			else
			{
				return false;
			}
		}

		/**
		 * Vote for this issue, returns false if user cant vote or has voted the same before
		 * 
		 * @return boolean
		 */
		public function vote($up = true)
		{
			$user_id = TBGContext::getUser()->getID();
			if (!$this->hasUserVoted($user_id, $up))
			{
				TBGVotesTable::getTable()->addByUserIdAndIssueId($user_id, $this->getID(), $up);
				$this->_votes[$user_id] = ($up) ? 1 : -1;
				$this->_votes_total = array_sum($this->_votes);
				TBGIssuesTable::getTable()->saveVotesTotalForIssueID($this->_votes_total, $this->getID());
				return true;
			}
			else
			{
				return false;
			}
		}
	
		/**
		 * Returns an array with tasks
		 *
		 * @return array
		 */
		public function getTasks()
		{
			if ($this->getProject()->isTasksEnabled())
			{
				if ($this->_tasks == null)
				{
					$this->_tasks = array();
					if ($res = \b2db\Core::getTable('TBGIssueTasksTable')->getByIssueID($this->getID()))
					{
						while ($row = $resultset->getNextRow())
						{
							$this->_tasks[$row->get(TBGIssueTasksTable::ID)] = TBGContext::factory()->task($row->get(TBGIssueTasksTable::ID), $row);
						}
					}
				}
			}
	
			return $this->_tasks;
		}

		/**
		 * Returns an array of tags
		 *
		 * @return array
		 */
		public function getTags()
		{
			if ($this->_tags == null)
			{
				$this->_tags = array();
				if ($res = \b2db\Core::getTable('TBGIssueTagsTable')->getByIssueID($this->getID()))
				{
					while ($row = $resultset->getNextRow())
					{
						$this->_tags[$row->get(TBGIssueTagsTable::ID)] = $row->get(TBGIssueTagsTable::TAG_NAME);
					}
				}
			}

			return $this->_tasks;
		}

		/**
		 * Returns whether or not the issue has been deleted
		 *
		 * @return boolean
		 */
		public function isDeleted()
		{
			return $this->_deleted;
		}
	
		/**
		 * Returns the issue title
		 *
		 * @return string
		 */
		public function getTitle()
		{
			return htmlentities($this->_title, ENT_COMPAT, TBGContext::getI18n()->getCharset());
		}
		
		/**
		 * Returns the issue title
		 *
		 * @return string
		 */
		public function getRawTitle()
		{
			return $this->_title;
		}
		
		/**
		 * Set the title
		 * 
		 * @param string $title The new title to set
		 */
		public function setTitle($title)
		{
			if (trim($title) == '')
			{
				throw new Exception("Can't set an empty title");
			}
			$this->_addChangedProperty('_title', $title);
		}
		
		/**
		 * Returns the description
		 *
		 * @return string
		 */
		public function getDescription()
		{
			return $this->_description;
		}
		
		/**
		 * Return whether or not this issue has a description set
		 * 
		 * @return boolean
		 */
		public function hasDescription()
		{
			return (bool) (trim($this->getDescription()) != '');
		}
	
		/**
		 * Set the description
		 * 
		 * @param string $description
		 */
		public function setDescription($description)
		{
			$this->_addChangedProperty('_description', $description);
		}
	
		/**
		 * Returns the issues reproduction steps
		 *
		 * @return string
		 */
		public function getReproductionSteps()
		{
			return $this->_reproduction_steps;
		}
		
		/**
		 * Set the reproduction steps
		 * 
		 * @param string $reproduction_steps
		 */
		public function setReproductionSteps($reproduction_steps)
		{
			$this->_addChangedProperty('_reproduction_steps', $reproduction_steps);
		}
		
		/**
		 * Returns the category
		 *
		 * @return TBGDatatype
		 */
		public function getCategory()
		{
			return $this->_b2dbLazyload('_category');
		}
		
		/**
		 * Set the category
		 * 
		 * @param integer $category_id The category ID to change to
		 */
		public function setCategory($category_id)
		{
			$this->_addChangedProperty('_category', $category_id);
		}

		/**
		 * Set the status
		 * 
		 * @param integer $status_id The status ID to change to
		 */
		public function setStatus($status_id)
		{
			$this->_addChangedProperty('_status', $status_id);			
		}
		
		/**
		 * Returns the reproducability
		 *
		 * @return TBGDatatype
		 */
		public function getReproducability()
		{
			if (is_numeric($this->_reproducability))
			{
				try
				{
					$this->_reproducability = TBGContext::factory()->TBGReproducability($this->_reproducability);
				}
				catch (Exception $e)
				{
					$this->_reproducability = null;
				}
			}
			return $this->_reproducability;
		}
		
		/**
		 * Set the reproducability
		 * 
		 * @param integer $reproducability_id The reproducability id to change to
		 */
		public function setReproducability($reproducability_id)
		{
			$this->_addChangedProperty('_reproducability', $reproducability_id);
		}
	
		/**
		 * Returns the priority
		 *
		 * @return TBGDatatype
		 */
		public function getPriority()
		{
			if (is_numeric($this->_priority))
			{
				try
				{
					$this->_priority = TBGContext::factory()->TBGPriority($this->_priority);
				}
				catch (Exception $e)
				{
					$this->_priority = null;
				}
			}
			return $this->_priority;
		}

		/**
		 * Set the priority
		 *
		 * @param integer $priority_id The priority id to change to
		 */
		public function setPriority($priority_id)
		{
			$this->_addChangedProperty('_priority', $priority_id);
		}

		/**
		 * Get all custom fields and their values
		 *
		 * @return array
		 */
		public function getCustomFields()
		{
			$retarr = array();
			foreach (TBGCustomDatatype::getAll() as $key => $customdatatype)
			{
				$var_name = '_customfield'.$key;
				$retarr[$key] = $this->$var_name;
			}
			return $retarr;
		}

		/**
		 * Set the value of a custom field
		 *
		 * @param string $key
		 * @param mixed $value
		 */
		public function setCustomField($key, $value)
		{
			$this->_addChangedProperty('_customfield'.$key, $value);
		}

		/**
		 * Return the value of a custom field
		 *
		 * @param string $key
		 * 
		 * @return mixed
		 */
		public function getCustomField($key)
		{
			$var_name = "_customfield{$key}";
			if (property_exists($this, $var_name))
			{
				$customtype = TBGCustomDatatype::getByKey($key);
				if ($customtype->getType() == TBGCustomDatatype::CALCULATED_FIELD)
				{
					$result = null;
					$options = $customtype->getOptions();
					if (!empty($options)) {
						$formula = array_pop($options)->getValue();

						preg_match_all('/{([[:alnum:]]+)}/', $formula, $matches);

						$hasValues = false;
						for($i=0; $i<count($matches[0]); $i++) {
							$value = $this->getCustomField($matches[1][$i]);
							if ($value instanceof TBGCustomDatatypeOption) {
								$value = $value->getValue();
							}
							if (is_numeric($value)) {
								$hasValues = true;
							}
							$value = floatval($value);
							$formula = str_replace($matches[0][$i], $value, $formula);
						}

						// Check to verify formula only includes numbers and allowed operators
						if ($hasValues && !preg_match('/[^0-9\+-\/*\(\)%]/', $formula)) {
							try {
								$m = new EvalMath();
								$m->suppress_errors = true;
								$result = $m->evaluate($formula);
								if (!empty($m->last_error)) {
									$result = $m->last_error;
								} else {
									$result = round($result, 2);
								}
							} catch (Exception $e) {
								$result = 'N/A';
							}
						}
					}
					return $result;
				}
				elseif ($this->$var_name && $customtype->hasCustomOptions() && !$this->$var_name instanceof TBGCustomDatatypeOption)
				{
					$this->$var_name = new TBGCustomDatatypeOption($this->$var_name);
				}
				return $this->$var_name;
			}
			else
			{
				return null;
			}
		}

		/**
		 * Get string value of any built-in or custom field for this issue
		 *
		 * @param $key Key of field
		 * @return string
		 */
		public function getFieldValue($key)
		{
			$methodname = 'get'.str_replace(' ', '', ucwords(str_replace('_', ' ', $key)));

			if (method_exists($this, $methodname)) {
				// Use existing getter if available
				return $this->$methodname();

			} elseif ($key == 'component' || $key == 'edition' || $key == 'build') {
				$valueString = '';
				$methodname .= 's'; // Turn getComponent to getComponents
				$items = $this->$methodname();
				foreach ($items as $item) {
					$valueString .= ', '.$item[$key]->getName();
				}
				if (strlen($valueString) > 0) {
					$valueString = substr($valueString, 2);
				}
				return $valueString;

			} elseif ($key == 'percent_complete') {
				return $this->getPercentCompleted();

			} else {
				return $this->getCustomField($key);
			}
		}

		/**
		 * Returns the scrum color
		 *
		 * @return string
		 */
		public function getScrumColor()
		{
			return $this->_scrumcolor;
		}

		/**
		 * Set the priority
		 *
		 * @param integer $priority_id The priority id to change to
		 */
		public function setScrumColor($color)
		{
			$this->_addChangedProperty('_scrumcolor', $color);
		}

		/**
		 * Returns the assigned milestone if any
		 *
		 * @return TBGMilestone
		 */
		public function getMilestone()
		{
			/*if (is_numeric($this->_milestone))
			{
				try
				{
					$this->_milestone = TBGContext::factory()->TBGMilestone($this->_milestone);
				}
				catch (Exception $e)
				{
					$this->_milestone = null;
				}
			}
			return $this->_milestone;*/
			return $this->_b2dbLazyload('_milestone');
		}
		
		/**
		 * Set the milestone
		 * 
		 * @param integer $milestone_id The milestone id to assign
		 */
		public function setMilestone($milestone_id)
		{
			$this->_addChangedProperty('_milestone', $milestone_id);
		}

		/**
		 * Remove the assigned milestone
		 */
		public function removeMilestone()
		{
			$this->setMilestone(0);
		}
	
		/**
		 * Remove a dependant issue
		 * 
		 * @param integer $issue_id The issue ID to remove
		 */
		public function removeDependantIssue($issue_id)
		{
			if ($row = TBGIssueRelationsTable::getTable()->getIssueRelation($this->getID(), $issue_id))
			{
				$related_issue = TBGContext::factory()->TBGIssue($issue_id);
				$relation_id = $row->get(TBGIssueRelationsTable::ID);
				if ($row->get(TBGIssueRelationsTable::PARENT_ID) == $this->getID())
				{
					$this->_removeChildIssue($related_issue, $relation_id);
				}
				else
				{
					$this->_removeParentIssue($related_issue, $relation_id);
				}
				TBGIssueRelationsTable::getTable()->doDeleteById($relation_id);
			}
		}
		
		/**
		 * Removes a related issue
		 *
		 * @see removeDependantIssue()
		 * 
		 * @param TBGIssue $related_issue The issue to remove relations from
		 * @param integer $relation_id The relation id to delete
		 */
		protected function _removeParentIssue($related_issue, $relation_id)
		{
			$this->addLogEntry(TBGLogTable::LOG_ISSUE_DEPENDS, TBGContext::getI18n()->__('This issue no longer depends on the solution of issue %issue_no%', array('%issue_no%' => $related_issue->getFormattedIssueNo())));
			$related_issue->addLogEntry(TBGLogTable::LOG_ISSUE_DEPENDS, TBGContext::getI18n()->__('Issue %issue_no% no longer depends on the solution of this issue', array('%issue_no%' => $this->getFormattedIssueNo())));
			
			if ($this->_parent_issues !== null && array_key_exists($relation_id, $this->_parent_issues))
			{
				unset($this->_parent_issues[$relation_id]);
			}
		}
		
		/**
		 * Removes a related issue
		 * 
		 * @see removeDependantIssue()
		 * 
		 * @param TBGIssue $related_issue The issue to remove relations from
		 * @param integer $relation_id The relation id to delete
		 */
		protected function _removeChildIssue($related_issue, $relation_id)
		{
			$this->addLogEntry(TBGLogTable::LOG_ISSUE_DEPENDS, TBGContext::getI18n()->__('Issue %issue_no% no longer depends on the solution of this issue', array('%issue_no%' => $related_issue->getFormattedIssueNo())));
			$related_issue->addLogEntry(TBGLogTable::LOG_ISSUE_DEPENDS, TBGContext::getI18n()->__('This issue no longer depends on the solution of issue %issue_no%', array('%issue_no%' => $this->getFormattedIssueNo())));
			
			if ($this->_child_issues !== null && array_key_exists($relation_id, $this->_child_issues))
			{
				unset($this->_child_issues[$relation_id]);
			}
		}

		/**
		 * Add a related issue
		 * 
		 * @param TBGIssue $related_issue
		 * 
		 * @return boolean
		 */
		public function addParentIssue(TBGIssue $related_issue)
		{
			if (!$row = TBGIssueRelationsTable::getTable()->getIssueRelation($this->getID(), $related_issue->getID()))
			{
				TBGIssueRelationsTable::getTable()->addParentIssue($this->getID(), $related_issue->getID());
				$this->_parent_issues = null;
				
				$related_issue->addLogEntry(TBGLogTable::LOG_ISSUE_DEPENDS, TBGContext::getI18n()->__('This %this_issuetype% now depends on the solution of %issuetype% %issue_no%', array('%this_issuetype%' => $related_issue->getIssueType()->getName(), '%issuetype%' => $this->getIssueType()->getName(), '%issue_no%' => $this->getFormattedIssueNo())));
				$this->addLogEntry(TBGLogTable::LOG_ISSUE_DEPENDS, TBGContext::getI18n()->__('%issuetype% %issue_no% now depends on the solution of this %this_issuetype%', array('%this_issuetype%' => $this->getIssueType()->getName(), '%issuetype%' => $related_issue->getIssueType()->getName(), '%issue_no%' => $related_issue->getFormattedIssueNo())));
				
				return true;
			}
			return false;
		}

		/**
		 * Add a related issue
		 * 
		 * @param TBGIssue $related_issue
		 * 
		 * @return boolean
		 */
		public function addChildIssue(TBGIssue $related_issue)
		{
			if (!$row = TBGIssueRelationsTable::getTable()->getIssueRelation($this->getID(), $related_issue->getID()))
			{
				$res = TBGIssueRelationsTable::getTable()->addChildIssue($this->getID(), $related_issue->getID());
				$this->_child_issues = null;
				
				$related_issue->addLogEntry(TBGLogTable::LOG_ISSUE_DEPENDS, TBGContext::getI18n()->__('%issuetype% %issue_no% now depends on the solution of this %this_issuetype%', array('%this_issuetype%' => $related_issue->getIssueType()->getName(), '%issuetype%' => $this->getIssueType()->getName(), '%issue_no%' => $this->getFormattedIssueNo())));
				$this->addLogEntry(TBGLogTable::LOG_ISSUE_DEPENDS, TBGContext::getI18n()->__('This %this_issuetype% now depends on the solution of %issuetype% %issue_no%', array('%this_issuetype%' => $this->getIssueType()->getName(), '%issuetype%' => $related_issue->getIssueType()->getName(), '%issue_no%' => $related_issue->getFormattedIssueNo())));
				
				return ($comment instanceof TBGComment) ? $comment : true;
			}
			return false;
		}

		/**
		 * Return the poster
		 *
		 * @return TBGUser
		 */
		public function getPostedBy()
		{
			if (is_numeric($this->_posted_by))
			{
				try
				{
					$this->_posted_by = TBGContext::factory()->TBGUser($this->_posted_by);
				}
				catch (Exception $e)
				{
					$this->_posted_by = null;
				}
			}
	
			return $this->_posted_by;
		}
		
		/**
		 * Whether or not the issue is posted by someone
		 *
		 * @return boolean
		 */
		public function isPostedBy()
		{
			return (bool) ($this->getPostedBy() instanceof TBGIdentifiable);
		}

		/**
		 * Return the poster id
		 *
		 * @return integer
		 */
		public function getPostedByID()
		{
			$poster = $this->getPostedBy();
			return ($poster instanceof TBGIdentifiable) ? $poster->getID() : null;
		}
		
		/**
		 * Set issue poster
		 * 
		 * @param TBGIdentifiableClass $poster The user/team you want to have posted the issue
		 */
		public function setPostedBy(TBGIdentifiableClass $poster)
		{
			$this->_addChangedProperty('_posted_by', $poster->getID());
		}

		/**
		 * @return bool
		 */
		public function isPostedByChanged()
		{
			return $this->_isPropertyChanged('_posted_by');
		}
		
		/**
		 * Returns the percentage completed
		 * 
		 * @return integer
		 */
		public function getPercentCompleted()
		{
			return (int) $this->_percent_complete;
		}
		
		/**
		 * Set percentage completed
		 * 
		 * @param integer $percentage
		 */
		public function setPercentCompleted($percentage)
		{
			$this->_addChangedProperty('_percent_complete', (int) $percentage);
		}
	
		/**
		 * Returns the resolution
		 *
		 * @return TBGDatatype
		 */
		public function getResolution()
		{
			return $this->_b2dbLazyload('_resolution');
		}
		
		/**
		 * Set the resolution
		 * 
		 * @param integer $resolution_id The resolution ID you want to set it to
		 */
		public function setResolution($resolution_id)
		{
			$this->_addChangedProperty('_resolution', $resolution_id);
		}

		/**
		 * Returns the severity
		 *
		 * @return TBGDatatype
		 */
		public function getSeverity()
		{
			return $this->_b2dbLazyload('_severity');
		}

		/**
		 * Set the severity
		 * 
		 * @param integer $severity_id The severity ID you want to set it to
		 */
		public function setSeverity($severity_id)
		{
			$this->_addChangedProperty('_severity', $severity_id);
		}
	
		/**
		 * Set the issue type
		 * 
		 * @param integer $issuetype_id The issue type ID you want to set
		 */
		public function setIssuetype($issuetype_id)
		{
			$this->_addChangedProperty('_issuetype', $issuetype_id);
		}
	
		/**
		 * Returns an array with the estimated time
		 *
		 * @return array
		 */
		public function getEstimatedTime()
		{
			return array('months' => (int) $this->_estimated_months, 'weeks' => (int) $this->_estimated_weeks, 'days' => (int) $this->_estimated_days, 'hours' => (int) $this->_estimated_hours, 'points' => (int) $this->_estimated_points);
		}
		
		/**
		 * Returns the estimated months
		 * 
		 * @return integer
		 */
		public function getEstimatedMonths()
		{
			return (int) $this->_estimated_months;
		}

		/**
		 * Returns the estimated weeks
		 * 
		 * @return integer
		 */
		public function getEstimatedWeeks()
		{
			return (int) $this->_estimated_weeks;
		}

		/**
		 * Returns the estimated days
		 * 
		 * @return integer
		 */
		public function getEstimatedDays()
		{
			return (int) $this->_estimated_days;
		}
		
		/**
		 * Returns the estimated hours
		 * 
		 * @return integer
		 */
		public function getEstimatedHours()
		{
			return (int) $this->_estimated_hours;
		}
		
		/**
		 * Returns the estimated points
		 * 
		 * @return integer
		 */
		public function getEstimatedPoints()
		{
			return (int) $this->_estimated_points;
		}
		
		/**
		 * Turns a string into a months/weeks/days/hours/points array
		 * 
		 * @param string $string The string to convert
		 * 
		 * @return array
		 */
		protected function _convertFancyStringToTime($string)
		{
			$retarr = array('months' => 0, 'weeks' => 0, 'days' => 0, 'hours' => 0, 'points' => 0);
			$string = mb_strtolower(trim($string));
			$time_arr = preg_split('/(\,|\/|and|or|plus)/', $string);
			foreach ($time_arr as $time_elm)
			{
				$time_parts = explode(' ', trim($time_elm));
				if (is_array($time_parts) && count($time_parts) > 1)
				{
					switch (true)
					{
						case mb_stristr($time_parts[1], 'month'):
							$retarr['months'] = (int) trim($time_parts[0]);
							break;
						case mb_stristr($time_parts[1], 'week'):
							$retarr['weeks'] = (int) trim($time_parts[0]);
							break;
						case mb_stristr($time_parts[1], 'day'):
							$retarr['days'] = (int) trim($time_parts[0]);
							break;
						case mb_stristr($time_parts[1], 'hour'):
							$retarr['hours'] = (int) trim($time_parts[0]);
							break;
						case mb_stristr($time_parts[1], 'point'):
							$retarr['points'] = (int) trim($time_parts[0]);
							break;
					}
				}
			}
			return $retarr;
		}
		
		/**
		 * Returns whether or not there is an estimated time for this issue
		 * 
		 * @return boolean
		 */
		public function hasEstimatedTime()
		{
			$time = $this->getEstimatedTime();
			return (array_sum($time) > 0) ? true : false;
		}
		
		/**
		 * Set estimated time
		 *
		 * @param integer $time
		 */
		public function setEstimatedTime($time)
		{
			if (is_numeric($time))
			{
				$this->_addChangedProperty('_estimated_months', 0);
				$this->_addChangedProperty('_estimated_weeks', 0);
				$this->_addChangedProperty('_estimated_days', 0);
				$this->_addChangedProperty('_estimated_hours', 0);
				$this->_addChangedProperty('_estimated_points', 0);
			}
			else
			{
				$time = $this->_convertFancyStringToTime($time);
				$this->_addChangedProperty('_estimated_months', $time['months']);
				$this->_addChangedProperty('_estimated_weeks', $time['weeks']);
				$this->_addChangedProperty('_estimated_days', $time['days']);
				$this->_addChangedProperty('_estimated_hours', $time['hours']);
				$this->_addChangedProperty('_estimated_points', $time['points']);
			}
		}
		
		/**
		 * Set estimated months
		 * 
		 * @param integer $months The number of months estimated
		 */
		public function setEstimatedMonths($months)
		{
			$this->_addChangedProperty('_estimated_months', $months);
		}
	
		/**
		 * Set estimated weeks
		 * 
		 * @param integer $weeks The number of weeks estimated
		 */
		public function setEstimatedWeeks($weeks)
		{
			$this->_addChangedProperty('_estimated_weeks', $weeks);
		}
	
		/**
		 * Set estimated days
		 * 
		 * @param integer $days The number of days estimated
		 */
		public function setEstimatedDays($days)
		{
			$this->_addChangedProperty('_estimated_days', $days);
		}
	
		/**
		 * Set estimated hours
		 * 
		 * @param integer $hours The number of hours estimated
		 */
		public function setEstimatedHours($hours)
		{
			$this->_addChangedProperty('_estimated_hours', $hours);
		}
		
		/**
		 * Set issue number
		 * 
		 * @param integer $no New issue number
		 */
		public function setIssueNumber($no)
		{
			$this->_issue_no = $no;
		}
	
		/**
		 * Set estimated points
		 * 
		 * @param integer $points The number of points estimated
		 */
		public function setEstimatedPoints($points)
		{
			$this->_addChangedProperty('_estimated_points', $points);
		}
		
		/**
		 * Check to see whether the estimated time is changed
		 * 
		 * @return boolean
		 */
		public function isEstimatedTimeChanged()
		{
			return (bool) ($this->isEstimated_MonthsChanged() || $this->isEstimated_WeeksChanged() || $this->isEstimated_DaysChanged() || $this->isEstimated_HoursChanged() || $this->isEstimated_PointsChanged());
		}

		/**
		 * Check to see whether the estimated time is merged
		 * 
		 * @return boolean
		 */
		public function isEstimatedTimeMerged()
		{
			return (bool) ($this->isEstimated_MonthsMerged() || $this->isEstimated_WeeksMerged() || $this->isEstimated_DaysMerged() || $this->isEstimated_HoursMerged() || $this->isEstimated_PointsMerged());
		}
		
		/**
		 * Reverts estimated time
		 */
		public function revertEstimatedTime()
		{
			$this->revertEstimated_Months();
			$this->revertEstimated_Weeks();
			$this->revertEstimated_Days();
			$this->revertEstimated_Hours();
			$this->revertEstimated_Points();
		}
	
		/**
		 * Check to see whether the percent completed is changed
		 *
		 * @return boolean
		 */
		public function isPercentCompletedChanged()
		{
			return $this->_isPropertyChanged('_percent_complete');
		}

		/**
		 * Check to see whether the percent completed is merged
		 *
		 * @return boolean
		 */
		public function isPercentCompletedMerged()
		{
			return $this->_isPropertyMerged('_percent_complete');
		}

		/**
		 * Reverts percent completed
		 */
		public function revertPercentCompleted()
		{
			$this->_revertPropertyChange('_percent_complete');
		}

		/**
		 * Check to see whether the owner is changed
		 *
		 * @return boolean
		 */
		public function isOwnerUserChanged()
		{
			return $this->_isPropertyChanged('_owner_user');
		}

		/**
		 * Check to see whether the owner is merged
		 *
		 * @return boolean
		 */
		public function isOwnerUserMerged()
		{
			return $this->_isPropertyMerged('_owner_user');
		}

		/**
		 * Reverts estimated time
		 */
		public function revertOwnerUser()
		{
			$this->_revertPropertyChange('_owner_user');
		}

		/**
		 * Check to see whether the owner is changed
		 *
		 * @return boolean
		 */
		public function isOwnerTeamChanged()
		{
			return $this->_isPropertyChanged('_owner_team');
		}

		/**
		 * Check to see whether the owner is merged
		 *
		 * @return boolean
		 */
		public function isOwnerTeamMerged()
		{
			return $this->_isPropertyMerged('_owner_team');
		}

		/**
		 * Reverts estimated time
		 */
		public function revertOwnerTeam()
		{
			$this->_revertPropertyChange('_owner_team');
		}

		public function isOwnerChanged()
		{
			return (bool) $this->isOwnerTeamChanged() || $this->isOwnerUserChanged();
		}

		public function isOwned()
		{
			return (bool) ($this->getOwner() instanceof TBGIdentifiable);
		}

		public function revertOwner()
		{
			if ($this->isOwnerTeamChanged())
				$this->revertOwnerTeam();
			else
				$this->revertOwnerUser();
		}

		/**
		 * Check to see whether the assignee is changed
		 * 
		 * @return boolean
		 */
		public function isAssigneeUserChanged()
		{
			return $this->_isPropertyChanged('_assignee_user');
		}

		/**
		 * Check to see whether the owner is merged
		 * 
		 * @return boolean
		 */
		public function isAssigneeUserMerged()
		{
			return $this->_isPropertyMerged('_assignee_user');
		}
		
		/**
		 * Reverts estimated time
		 */
		public function revertAssigneeUser()
		{
			$this->_revertPropertyChange('_assignee_user');
		}

		/**
		 * Check to see whether the assignee is changed
		 *
		 * @return boolean
		 */
		public function isAssigneeTeamChanged()
		{
			return $this->_isPropertyChanged('_assignee_team');
		}

		/**
		 * Check to see whether the owner is merged
		 *
		 * @return boolean
		 */
		public function isAssigneeTeamMerged()
		{
			return $this->_isPropertyMerged('_assignee_team');
		}

		public function isAssigneeChanged()
		{
			return (bool) $this->isAssigneeTeamChanged() || $this->isAssigneeUserChanged();
		}

		public function isAssigned()
		{
			return (bool) ($this->getAssignee() instanceof TBGIdentifiable);
		}

		/**
		 * Reverts estimated time
		 */
		public function revertAssigneeTeam()
		{
			$this->_revertPropertyChange('_assignee_team');
		}

		public function revertAssignee()
		{
			if ($this->isAssigneeTeamChanged())
				$this->revertAssigneeTeam();
			else
				$this->revertAssigneeUser();
		}

		/**
		 * Returns an array with the spent time
		 *
		 * @return array
		 */
		public function getSpentTime()
		{
			return array('months' => (int) $this->_spent_months, 'weeks' => (int) $this->_spent_weeks, 'days' => (int) $this->_spent_days, 'hours' => (int) $this->_spent_hours, 'points' => (int) $this->_spent_points);
		}
		
		/**
		 * Returns the spent months
		 * 
		 * @return integer
		 */
		public function getSpentMonths()
		{
			return (int) $this->_spent_months;
		}

		/**
		 * Returns the spent weeks
		 * 
		 * @return integer
		 */
		public function getSpentWeeks()
		{
			return (int) $this->_spent_weeks;
		}

		/**
		 * Returns the spent days
		 * 
		 * @return integer
		 */
		public function getSpentDays()
		{
			return (int) $this->_spent_days;
		}
		
		/**
		 * Returns the spent hours
		 * 
		 * @return integer
		 */
		public function getSpentHours()
		{
			return (int) $this->_spent_hours;
		}
		
		/**
		 * Returns the spent points
		 * 
		 * @return integer
		 */
		public function getSpentPoints()
		{
			return (int) $this->_spent_points;
		}
		
		/**
		 * Returns an array with the spent time
		 *
		 * @see getSpentTime()
		 *
		 * @return array
		 */
		public function getTimeSpent()
		{
			return $this->getSpentTime();
		}

		/**
		 * Set time spent on this issue
		 *
		 * @param integer $time
		 */
		public function setSpentTime($time)
		{
			if (is_numeric($time))
			{
				$this->_addChangedProperty('_spent_months', 0);
				$this->_addChangedProperty('_spent_weeks', 0);
				$this->_addChangedProperty('_spent_days', 0);
				if ($this->getIssueType()->isTask())
				{
					$this->_addChangedProperty('_spent_points', 0);
					$this->_addChangedProperty('_spent_hours', (int) $time);
				}
				else
				{
					$this->_addChangedProperty('_spent_hours', 0);
					$this->_addChangedProperty('_spent_points', (int) $time);
				}
			}
			else
			{
				$time = $this->_convertFancyStringToTime($time);
				$this->_addChangedProperty('_spent_months', $time['months']);
				$this->_addChangedProperty('_spent_weeks', $time['weeks']);
				$this->_addChangedProperty('_spent_days', $time['days']);
				$this->_addChangedProperty('_spent_hours', $time['hours']);
				$this->_addChangedProperty('_spent_points', $time['points']);
			}
		}
		
		/**
		 * Add to spent time
		 *
		 * @param integer $time
		 */
		public function addSpentTime($time)
		{
			if (is_numeric($time))
			{
				if ($this->getIssuetype()->isTask())
				{
					$this->_addChangedProperty('_spent_hours', $this->_spent_hours + (int) $time);
				}
				else
				{
					$this->_addChangedProperty('_spent_points', $this->_spent_points + (int) $time);
				}
			}
			else
			{
				$time = $this->_convertFancyStringToTime($time);
				$this->_addChangedProperty('_spent_months', $this->_spent_months + $time['months']);
				$this->_addChangedProperty('_spent_weeks', $this->_spent_weeks + $time['weeks']);
				$this->_addChangedProperty('_spent_days', $this->_spent_days + $time['days']);
				$this->_addChangedProperty('_spent_hours', $this->_spent_hours + $time['hours']);
				$this->_addChangedProperty('_spent_points', $this->_spent_points + $time['points']);
			}
		}		

		/**
		 * Set spent months
		 * 
		 * @param integer $months The number of months spent
		 */
		public function setSpentMonths($months)
		{
			$this->_addChangedProperty('_spent_months', $months);
		}
	
		/**
		 * Set spent weeks
		 * 
		 * @param integer $weeks The number of weeks spent
		 */
		public function setSpentWeeks($weeks)
		{
			$this->_addChangedProperty('_spent_weeks', $weeks);
		}
	
		/**
		 * Set spent days
		 * 
		 * @param integer $days The number of days spent
		 */
		public function setSpentDays($days)
		{
			$this->_addChangedProperty('_spent_days', $days);
		}
	
		/**
		 * Set spent hours
		 * 
		 * @param integer $hours The number of hours spent
		 */
		public function setSpentHours($hours)
		{
			$this->_addChangedProperty('_spent_hours', $hours);
		}
	
		/**
		 * Set spent points
		 * 
		 * @param integer $points The number of points spent
		 */
		public function setSpentPoints($points)
		{
			$this->_addChangedProperty('_spent_points', $points);
		}

		/**
		 * Add spent months
		 * 
		 * @param integer $months The number of months spent
		 */
		public function addSpentMonths($months)
		{
			$this->_addChangedProperty('_spent_months', $this->_spent_months + $months);
		}
	
		/**
		 * Add spent weeks
		 * 
		 * @param integer $weeks The number of weeks spent
		 */
		public function addSpentWeeks($weeks)
		{
			$this->_addChangedProperty('_spent_weeks', $this->_spent_weeks + $weeks);
		}
	
		/**
		 * Add spent days
		 * 
		 * @param integer $days The number of days spent
		 */
		public function addSpentDays($days)
		{
			$this->_addChangedProperty('_spent_days', $this->_spent_days + $days);
		}
	
		/**
		 * Add spent hours
		 * 
		 * @param integer $hours The number of hours spent
		 */
		public function addSpentHours($hours)
		{
			$this->_addChangedProperty('_spent_hours', $this->_spent_hours + $hours);
		}
	
		/**
		 * Add spent points
		 * 
		 * @param integer $points The number of points spent
		 */
		public function addSpentPoints($points)
		{
			$this->_addChangedProperty('_spent_points', $this->_spent_points + $points);
		}
		
		/**
		 * Check to see whether the spent time is changed
		 * 
		 * @return boolean
		 */
		public function isSpentTimeChanged()
		{
			return (bool) ($this->isSpent_MonthsChanged() || $this->isSpent_WeeksChanged() || $this->isSpent_DaysChanged() || $this->isSpent_HoursChanged() || $this->isSpent_PointsChanged());
		}

		/**
		 * Check to see whether the spent time is merged
		 * 
		 * @return boolean
		 */
		public function isSpentTimeMerged()
		{
			return (bool) ($this->isSpent_MonthsMerged() || $this->isSpent_WeeksMerged() || $this->isSpent_DaysMerged() || $this->isSpent_HoursMerged() || $this->isSpent_PointsMerged());
		}
		
		/**
		 * Reverts spent time
		 */
		public function revertSpentTime()
		{
			$this->revertSpent_Months();
			$this->revertSpent_Weeks();
			$this->revertSpent_Days();
			$this->revertSpent_Hours();
			$this->revertSpent_Points();
		}
		
		/**
		 * Returns whether or not there is an spent time for this issue
		 * 
		 * @return boolean
		 */
		public function hasSpentTime()
		{
			$time = $this->getSpentTime();
			return (array_sum($time) > 0) ? true : false;
		}
		
		/**
		 * Returns the timestamp for when the issue was last updated
		 *
		 * @return integer
		 */
		public function getLastUpdatedTime()
		{
			return $this->_last_updated;
		}
	
		/**
		 * Returns the issues state
		 *
		 * @return integer
		 */
		public function getState()
		{
			return $this->_state;
		}
		
		/**
		 * Whether or not the issue is closed
		 * 
		 * @see getState()
		 * @see isOpen()
		 * 
		 * @return boolean
		 */
		public function isClosed()
		{
			return ($this->getState() == self::STATE_CLOSED) ? true : false;
		}
		
		/**
		 * Whether or not the issue is open
		 * 
		 * @see getState()
		 * @see isClosed()
		 * 
		 * @return boolean
		 */
		public function isOpen()
		{
			return !$this->isClosed();
		}
		
		/**
		 * Set the issue state
		 * 
		 * @param integer $state The state
		 */
		public function setState($state)
		{
			if (!in_array($state, array(self::STATE_CLOSED, self::STATE_OPEN)))
			{
				return false;
			}

			$this->_addChangedProperty('_state', $state);
			
			return true;
		}
		
		/**
		 * Close the issue
		 */
		public function close()
		{
			$this->setState(self::STATE_CLOSED);
		}
	
		/**
		 * (Re-)open the issue
		 */
		public function open()
		{
			$this->setState(self::STATE_OPEN);
		}
	
		/**
		 * Add a build to the list of affected builds
		 * 
		 * @param TBGBuild $build The build to add
		 * 
		 * @return boolean
		 */
		public function addAffectedBuild($build)
		{
			if ($this->getProject() && $this->getProject()->isBuildsEnabled())
			{
				$retval = \b2db\Core::getTable('TBGIssueAffectsBuildTable')->setIssueAffected($this->getID(), $build->getID());
				if ($retval !== false)
				{
					$this->addLogEntry(TBGLogTable::LOG_AFF_ADD, TBGContext::getI18n()->__("'%release_name%' added", array('%release_name%' => $build->getName())));
					return array('a_id' => $retval, 'build' => $build, 'confirmed' => 0, 'status' => null);
				}
			}
			return false;
		}
	
		/**
		 * Add an edition to the list of affected editions
		 * 
		 * @param TBGEdition $edition The edition to add
		 * 
		 * @return boolean
		 */
		public function addAffectedEdition($edition)
		{
			if ($this->getProject() && $this->getProject()->isEditionsEnabled())
			{
				$retval = \b2db\Core::getTable('TBGIssueAffectsEditionTable')->setIssueAffected($this->getID(), $edition->getID());
				if ($retval !== false)
				{
					$this->addLogEntry(TBGLogTable::LOG_AFF_ADD, TBGContext::getI18n()->__("'%edition_name%' added", array('%edition_name%' => $edition->getName())));
					return array('a_id' => $retval, 'edition' => $edition, 'confirmed' => 0, 'status' => null);
				}
			}
			return false;
		}
	
		/**
		 * Add a component to the list of affected components
		 * 
		 * @param TBGComponent $component The component to add
		 * 
		 * @return boolean
		 */
		public function addAffectedComponent($component)
		{
			if ($this->getProject() && $this->getProject()->isComponentsEnabled())
			{
				$retval = \b2db\Core::getTable('TBGIssueAffectsComponentTable')->setIssueAffected($this->getID(), $component->getID());
				if ($retval !== false)
				{
					$this->addLogEntry(TBGLogTable::LOG_AFF_ADD, TBGContext::getI18n()->__("'%component_name%' added", array('%component_name%' => $component->getName())));
					return array('a_id' => $retval, 'component' => $component, 'confirmed' => 0, 'status' => null);
				}
			}
			return false;
		}
		
			/**
		 * Remove an affected edition
		 * 
		 * @see removeAffectedItem()
		 * @see removeAffectedBuild()
		 * @see removeAffectedComponent()
		 * 
		 * @param TBGEdition $item The edition to remove
		 * 
		 * @return boolean
		 */
		public function removeAffectedEdition($item)
		{
			if (\b2db\Core::getTable('TBGIssueAffectsEditionTable')->deleteByIssueIDandEditionID($this->getID(), $item->getID()))
			{
				$this->addLogEntry(TBGLogTable::LOG_AFF_DELETE, TBGContext::getI18n()->__("'%item_name%' removed", array('%item_name%' => $item->getName())));
				return true;
			}
			return false;
		}
		
		/**
		 * Remove an affected build
		 *
		 * @see removeAffectedItem()
		 * @see removeAffectedEdition()
		 * @see removeAffectedComponent()
		 * 
		 * @param TBGBuild $item The build to remove
		 * 
		 * @return boolean
		 */
		public function removeAffectedBuild($item)
		{
			if (\b2db\Core::getTable('TBGIssueAffectsBuildTable')->deleteByIssueIDandBuildID($this->getID(), $item->getID()))
			{
				$this->addLogEntry(TBGLogTable::LOG_AFF_DELETE, TBGContext::getI18n()->__("'%item_name%' removed", array('%item_name%' => $item->getName())));
				return true;
			}
			return false;
		}
		
		/**
		 * Remove an affected component
		 *
		 * @see removeAffectedItem()
		 * @see removeAffectedEdition()
		 * @see removeAffectedBuild()
		 * 
		 * @param TBGComponent $item The component to remove
		 * 
		 * @return boolean
		 */
		public function removeAffectedComponent($item)
		{
			if (\b2db\Core::getTable('TBGIssueAffectsComponentTable')->deleteByIssueIDandComponentID($this->getID(), $item->getID()))
			{
				$this->addLogEntry(TBGLogTable::LOG_AFF_DELETE, TBGContext::getI18n()->__("'%item_name%' removed", array('%item_name%' => $item->getName())));
				return true;
			}
			return false;
		}
		
		/**
		 * Remove an affected edition
		 * 
		 * @see confirmAffectedItem()
		 * @see confirmAffectedBuild()
		 * @see confirmAffectedComponent()
		 * 
		 * @param TBGEdition $item The edition to remove
		 * @param boolean $confirmed[optional] Whether it's confirmed or not
		 * 
		 * @return boolean
		 */
		public function confirmAffectedEdition($item, $confirmed = true)
		{
			if (TBGIssueAffectsEditionTable::getTable()->confirmByIssueIDandEditionID($this->getID(), $item->getID(), $confirmed))
			{
				if ($confirmed)
				{
					$this->addLogEntry(TBGLogTable::LOG_AFF_UPDATE, TBGContext::getI18n()->__("'%edition%' is now confirmed for this issue", array('%edition%' => $item->getName())));
				}
				else
				{
					$this->addLogEntry(TBGLogTable::LOG_AFF_UPDATE, TBGContext::getI18n()->__("'%edition%' is now unconfirmed for this issue", array('%edition%' => $item->getName())));
				}
				return true;
			}
			return false;
		}
		
		/**
		 * Remove an affected build
		 *
		 * @see confirmAffectedItem()
		 * @see confirmAffectedEdition()
		 * @see confirmAffectedComponent()
		 * 
		 * @param TBGBuild $item The build to remove
		 * @param boolean $confirmed[optional] Whether it's confirmed or not
		 * 
		 * @return boolean
		 */
		public function confirmAffectedBuild($item, $confirmed = true)
		{
			if (TBGIssueAffectsBuildTable::getTable()->confirmByIssueIDandBuildID($this->getID(), $item->getID(), $confirmed))
			{
				if ($confirmed)
				{
					$this->addLogEntry(TBGLogTable::LOG_AFF_UPDATE, TBGContext::getI18n()->__("'%build%' is now confirmed for this issue", array('%build%' => $item->getName())));
				}
				else
				{
					$this->addLogEntry(TBGLogTable::LOG_AFF_UPDATE, TBGContext::getI18n()->__("'%build%' is now unconfirmed for this issue", array('%build%' => $item->getName())));
				}
				return true;
			}
			return false;
		}
		
		/**
		 * Remove an affected component
		 *
		 * @see confirmAffectedItem()
		 * @see confirmAffectedEdition()
		 * @see confirmAffectedBuild()
		 * 
		 * @param TBGComponent $item The component to remove
		 * @param boolean $confirmed[optional] Whether it's confirmed or not
		 * 
		 * @return boolean
		 */
		public function confirmAffectedComponent($item, $confirmed = true)
		{
			if (TBGIssueAffectsComponentTable::getTable()->confirmByIssueIDandComponentID($this->getID(), $item->getID(), $confirmed))
			{
				if ($confirmed)
				{
					$this->addLogEntry(TBGLogTable::LOG_AFF_UPDATE, TBGContext::getI18n()->__("'%component%' is now confirmed for this issue", array('%component%' => $item->getName())));
				}
				else
				{
					$this->addLogEntry(TBGLogTable::LOG_AFF_UPDATE, TBGContext::getI18n()->__("'%component%' is now unconfirmed for this issue", array('%component%' => $item->getName())));
				}
				return true;
			}
			return false;
		}

		/**
		 * Set status for affected edition
		 * 
		 * @see setAffectedItemStatus()
		 * @see setAffectedBuildStatus()
		 * @see setAffectedComponentStatus()
		 * 
		 * @param TBGEdition $item The edition to set status for
		 * @param TBGDatatype $status The status to set
		 * 
		 * @return boolean
		 */
		public function setAffectedEditionStatus($item, $status)
		{
			if (TBGIssueAffectsEditionTable::getTable()->setStatusByIssueIDandEditionID($this->getID(), $item->getID(), $status->getID()))
			{
				$this->addLogEntry(TBGLogTable::LOG_AFF_DELETE, TBGContext::getI18n()->__("'%item_name%' -> '%status_name%", array('%item_name%' => $item->getName(), '%status_name%' => $status->getName())));
				return true;
			}
			return false;
		}
		
		/**
		 * Set status for affected build
		 * 
		 * @see setAffectedItemStatus()
		 * @see setAffectedEditionStatus()
		 * @see setAffectedComponentStatus()
		 * 
		 * @param TBGBuild $item The build to set status for
		 * @param TBGDatatype $status The status to set
		 * 
		 * @return boolean
		 */
		public function setAffectedBuildStatus($item, $status)
		{
			if (TBGIssueAffectsBuildTable::getTable()->setStatusByIssueIDandBuildID($this->getID(), $item->getID(), $status->getID()))
			{
				$this->addLogEntry(TBGLogTable::LOG_AFF_DELETE, TBGContext::getI18n()->__("'%item_name%' -> '%status_name%", array('%item_name%' => $item->getName(), '%status_name%' => $status->getName())));
				return true;
			}
			return false;
		}
				
		/**
		 * Set status for affected component
		 * 
		 * @see setAffectedItemStatus()
		 * @see setAffectedBuildStatus()
		 * @see setAffectedEditionStatus()
		 * 
		 * @param TBGComponent $item The component to set status for
		 * @param TBGDatatype $status The status to set
		 * 
		 * @return boolean
		 */
		public function setAffectedComponentStatus($item, $status)
		{
			if (TBGIssueAffectsComponentTable::getTable()->setStatusByIssueIDandComponentID($this->getID(), $item->getID(), $status->getID()))
			{
				$this->addLogEntry(TBGLogTable::LOG_AFF_DELETE, TBGContext::getI18n()->__("'%item_name%' -> '%status_name%", array('%item_name%' => $item->getName(), '%status_name%' => $status->getName())));
				return true;
			}
			return false;
		}
		
		/**
		 * Updates the issue's last_updated time to "now"
		 */
		public function updateTime()
		{
			$this->_addChangedProperty('_last_updated', NOW);
		}

		/**
		 * Delete this issue
		 */
		public function deleteIssue()
		{
			$this->_deleted = true;
		}
	
		/**
		 * Adds a log entry
		 * 
		 * @param integer $change_type Type of log entry
		 * @param string $text The text to log
		 * @param boolean $system Whether this is a user entry or a system entry
		 */
		public function addLogEntry($change_type, $text = null, $system = false, $time = null)
		{
			$uid = ($system) ? 0 : TBGContext::getUser()->getID();
			$log_item = new TBGLogItem();
			$log_item->setChangeType($change_type);
			$log_item->setText($text);
			$log_item->setTargetType(TBGLogTable::TYPE_ISSUE);
			$log_item->setTarget($this->getID());
			$log_item->setUser($uid);
			$log_item->save();
			$this->_log_items_added[$log_item->getID()] = $log_item;
			return $log_item;
		}
	
		/**
		 * Adds a system comment
		 * 
		 * @param string $title Comment title
		 * @param string $text Comment text
		 * @param integer $uid The user ID that posted the comment
		 * 
		 * @return TBGComment
		 */
		public function addSystemComment($text, $uid)
		{
			$comment = new TBGComment();
			$comment->setTitle('');
			$comment->setContent($text);
			$comment->setPostedBy($uid);
			$comment->setTargetID($this->getID());
			$comment->setTargetType(TBGComment::TYPE_ISSUE);
			$comment->setSystemComment();
			if (!TBGSettings::isCommentTrailClean())
			{
				$comment->save();
			}
			return $comment;
		}
	
		/**
		 * Return an array with all the links:
		 * 		'id' => array('url', 'description')
		 * 
		 * @return array
		 */
		public function getLinks()
		{
			$this->_populateLinks();
			return $this->_links;
		}
		
		/**
		 * Populate the internal links array
		 */
		protected function _populateLinks()
		{
			if ($this->_links === null)
			{
				$this->_links = \b2db\Core::getTable('TBGLinksTable')->getByIssueID($this->getID());
			}
		}
	
		/**
		 * Remove a link
		 * 
		 * @param integer $link_id The link ID to remove
		 */
		public function removeLink($link_id)
		{
			if ($res = \b2db\Core::getTable('TBGLinksTable')->removeByIssueIDandLinkID($this->getID(), $link_id))
			{
				if (is_array($this->_links) && array_key_exists($link_id, $this->_links))
				{
					unset($this->_links[$link_id]);
				}
			}
		}
		
		/**
		 * Populate the files array
		 */
		protected function _populateFiles()
		{
			if ($this->_files === null)
			{
				$this->_files = TBGFile::getByIssueID($this->getID());
			}
		}

		/**
		 * Return an array with all files attached to this issue
		 * 
		 * @return array
		 */
		public function getFiles()
		{
			$this->_populateFiles();
			return $this->_files;
		}

		public function countFiles()
		{
			if ($this->_num_files === null)
			{
				if ($this->_files !== null)
				{
					$this->_num_files = count($this->_files);
				}
				else
				{
					$this->_num_files = TBGFile::countByIssueID($this->getID());
				}
			}

			return $this->_num_files;
		}

		/**
		 * Return a file by the filename if it is attached to this issue
		 * 
		 * @param string $filename The original filename to match against
		 *
		 * @return TBGFile
		 */
		public function getFileByFilename($filename)
		{
			foreach ($this->getFiles() as $file_id => $file)
			{
				if (mb_strtolower($filename) == mb_strtolower($file->getOriginalFilename()))
				{
					return $file;
				}
			}
			return null;
		}
		
		/**
		 * Remove a file
		 * 
		 * @param TBGFile $file The file to be removed
		 * 
		 * @return boolean
		 */
		public function removeFile(TBGFile $file)
		{
			TBGIssueFilesTable::getTable()->removeByIssueIDandFileID($this->getID(), $file->getID());
			if (is_array($this->_files) && array_key_exists($file->getID(), $this->_files))
			{
				unset($this->_files[$file->getID()]);
			}
			$file->delete();
		}
	
		/**
		 * Retrieve all log entries for this issue
		 * 
		 * @return array
		 */
		public function getLogEntries()
		{
			$this->_populateLogEntries();
			return $this->_log_entries;
		}
		
		/**
		 * Populate log entries array
		 */
		protected function _populateLogEntries()
		{
			if ($this->_log_entries === null)
			{
				$this->_log_entries = TBGLogTable::getTable()->getByIssueID($this->getID()); 
			}
		}

		/**
		 * Mark issue as blocking or not blocking
		 * 
		 * @param boolean $blocking[optional] Whether it's blocking or not
		 */
		public function setBlocking($blocking = true)
		{
			$this->_addChangedProperty('_blocking', (bool) $blocking);
		}
		
		/**
		 * Return whether the issue is blocking the next release or not
		 * 
		 * @return boolean
		 */
		public function isBlocking()
		{
			return $this->_blocking;
		}
		
		/**
		 * Retrieve all comments for this issue
		 * 
		 * @return array
		 */
		public function getComments()
		{
			$this->_populateComments();
			return $this->_comments;
		}
		
		/**
		 * Populate comments array
		 */
		protected function _populateComments()
		{
			if ($this->_comments === null)
			{
//				$this->_comments = TBGComment::getComments($this->getID(), TBGComment::TYPE_ISSUE);
//				$this->_num_comments = count($this->_comments);
//				$sc = 0;
//				foreach ($this->_comments as $comment)
//				{
//					if ($comment->isSystemComment())
//						$sc++;
//				}
//				$this->_num_user_comments = $sc;
				$this->_b2dbLazyload('_comments');
			}
		}
		
		/**
		 * Return the number of comments
		 * 
		 * @return integer
		 */
		public function getCommentCount()
		{
			if ($this->_num_comments === null)
			{
				if ($this->_comments !== null)
					$this->_num_comments = count($this->_comments);
				else
					$this->_num_comments = $this->_b2dbLazycount('_comments');
			}

			return $this->_num_comments;
		}

		public function countComments()
		{
			return $this->getCommentCount();
		}

		public function countUserComments()
		{
			if ($this->_num_user_comments === null)
			{
				$this->_num_user_comments = TBGComment::countComments($this->getID(), TBGComment::TYPE_ISSUE, false);
			}

			return (int) $this->_num_user_comments;
		}
		
		public function isReproductionStepsChanged()
		{
			return $this->isReproduction_StepsChanged();
		}

		/**
		 * Return whether or not a specific field is visible
		 *  
		 * @param string $fieldname the fieldname key
		 * 
		 * @return boolean
		 */
		public function isFieldVisible($fieldname)
		{
			if (!$this->hasIssueType()) return false;
			try
			{
				$fields_array = $this->getProject()->getVisibleFieldsArray($this->getIssueType()->getID());
				return array_key_exists($fieldname, $fields_array);
			}
			catch (Exception $e)
			{
				return false;
			}
		}

		/**
		 * Return whether or not the triaging fields for user pain are visible
		 *
		 * @return boolean
		 */
		public function isUserPainVisible()
		{
			return (bool) ($this->isFieldVisible('user_pain'));
		}

		/**
		 * Return whether or not voting is enabled for this issue type
		 *
		 * @return boolean
		 */
		public function isVotesVisible()
		{
			return (bool) ($this->isFieldVisible('votes'));
		}

		/**
		 * Return whether or not the "description" field is visible
		 * 
		 * @return boolean
		 */
		public function isDescriptionVisible()
		{
			return (bool) ($this->isFieldVisible('description') || $this->getDescription() != '');
		}

		/**
		 * Return whether or not the "reproduction steps" field is visible
		 * 
		 * @return boolean
		 */
		public function isReproductionStepsVisible()
		{
			return (bool) ($this->isFieldVisible('reproduction_steps') || $this->getReproductionSteps());
		} 
		
		/**
		 * Return whether or not the "category" field is visible
		 * 
		 * @return boolean
		 */
		public function isCategoryVisible()
		{
			return (bool) ($this->isFieldVisible('category') || $this->getCategory() instanceof TBGDatatype);
		} 

		/**
		 * Return whether or not the "resolution" field is visible
		 * 
		 * @return boolean
		 */
		public function isResolutionVisible()
		{
			return (bool) ($this->isFieldVisible('resolution') || $this->getResolution() instanceof TBGDatatype);
		} 
		
		/**
		 * Return whether or not the "editions" field is visible
		 * 
		 * @return boolean
		 */
		public function isEditionsVisible()
		{
			return (bool) ($this->isFieldVisible('edition') || count($this->getEditions()) > 0);
		} 

		/**
		 * Return whether or not the "builds" field is visible
		 * 
		 * @return boolean
		 */
		public function isBuildsVisible()
		{
			return (bool) ($this->isFieldVisible('build') || count($this->getBuilds()) > 0);
		} 
		
		/**
		 * Return whether or not the "components" field is visible
		 * 
		 * @return boolean
		 */
		public function isComponentsVisible()
		{
			return (bool) ($this->isFieldVisible('component') || count($this->getComponents()) > 0);
		} 
		
		/**
		 * Return whether or not the "reproducability" field is visible
		 * 
		 * @return boolean
		 */
		public function isReproducabilityVisible()
		{
			return (bool) ($this->isFieldVisible('reproducability') || $this->getReproducability() instanceof TBGDatatype);
		} 
		
		/**
		 * Return whether or not the "severity" field is visible
		 * 
		 * @return boolean
		 */
		public function isSeverityVisible()
		{
			return (bool) ($this->isFieldVisible('severity') || $this->getSeverity() instanceof TBGDatatype);
		} 
		
		/**
		 * Return whether or not the "priority" field is visible
		 * 
		 * @return boolean
		 */
		public function isPriorityVisible()
		{
			return (bool) ($this->isFieldVisible('priority') || $this->getPriority() instanceof  TBGDatatype);
		} 
		
		/**
		 * Return whether or not the "estimated time" field is visible
		 * 
		 * @return boolean
		 */
		public function isEstimatedTimeVisible()
		{
			return (bool) ($this->isFieldVisible('estimated_time') || $this->hasEstimatedTime());
		} 
		
		/**
		 * Return whether or not the "spent time" field is visible
		 * 
		 * @return boolean
		 */
		public function isSpentTimeVisible()
		{
			return (bool) ($this->isFieldVisible('spent_time') || $this->hasSpentTime());
		} 
		
		/**
		 * Return whether or not the "milestone" field is visible
		 * 
		 * @return boolean
		 */
		public function isMilestoneVisible()
		{
			return (bool) ($this->isFieldVisible('milestone') || $this->getMilestone() instanceof TBGMilestone);
		} 

		/**
		 * Return whether or not the "percent_complete" field is visible
		 * 
		 * @return boolean
		 */
		public function isPercentCompletedVisible()
		{
			return (bool) ($this->isFieldVisible('percent_complete') || $this->getPercentCompleted() > 0);
		}

		/**
		 * Return the time when the issue was closed
		 * 
		 * @return false if closed, otherwise a timestamp
		 */
		public function whenClosed()
		{
			if (!$this->isClosed()) return false;
			$crit = new \b2db\Criteria();
			$crit->addSelectionColumn(TBGLogTable::TIME);
			$crit->addWhere(TBGLogTable::TARGET, $this->_id);
			$crit->addWhere(TBGLogTable::TARGET_TYPE, 1);
			$crit->addWhere(TBGLogTable::CHANGE_TYPE, 14);
			$crit->addOrderBy(TBGLogTable::TIME, 'desc');
			$res = TBGLogTable::getTable()->doSelect($crit);
			
			$ret_arr = array();

			$row = $res->getNextRow();
			return($row->get(TBGLogTable::TIME));
		}	

		/**
		 * Return the time when the issue was reopened
		 * 
		 * @return false if closed, otherwise a timestamp
		 */
		public function whenReopened()
		{
			if ($this->isClosed()) return false;
			$crit = new \b2db\Criteria();
			$crit->addSelectionColumn(TBGLogTable::TIME);
			$crit->addWhere(TBGLogTable::TARGET, $this->_id);
			$crit->addWhere(TBGLogTable::TARGET_TYPE, 1);
			$crit->addWhere(TBGLogTable::CHANGE_TYPE, 22);
			$crit->addOrderBy(TBGLogTable::TIME, 'desc');
			$res = TBGLogTable::getTable()->doSelect($crit);
			
			$ret_arr = array();

			if (count($res) == 0)
			{
				return false;
			}
			
			$row = $res->getNextRow();
			return($row->get(TBGLogTable::TIME));
		}

		protected function _saveCustomFieldValues()
		{
			foreach (TBGCustomDatatype::getAll() as $key => $customdatatype)
			{
				switch ($customdatatype->getType())
				{
					case TBGCustomDatatype::INPUT_TEXT:
					case TBGCustomDatatype::INPUT_TEXTAREA_SMALL:
					case TBGCustomDatatype::INPUT_TEXTAREA_MAIN:
						$option_id = $this->getCustomField($key);
						TBGIssueCustomFieldsTable::getTable()->saveIssueCustomFieldValue($option_id, $customdatatype->getID(), $this->getID());
						break;
					case TBGCustomDatatype::EDITIONS_CHOICE:
					case TBGCustomDatatype::COMPONENTS_CHOICE:
					case TBGCustomDatatype::RELEASES_CHOICE:
					case TBGCustomDatatype::STATUS_CHOICE:
						$option_object = null;
						try
						{
							switch ($customdatatype->getType())
							{
								case TBGCustomDatatype::EDITIONS_CHOICE:
									$option_object = TBGContext::factory()->TBGEdition($this->getCustomField($key));
									break;
								case TBGCustomDatatype::COMPONENTS_CHOICE:
									$option_object = TBGContext::factory()->TBGComponent($this->getCustomField($key));
									break;
								case TBGCustomDatatype::RELEASES_CHOICE:
									$option_object = TBGContext::factory()->TBGBuild($this->getCustomField($key));
									break;
								case TBGCustomDatatype::STATUS_CHOICE:
									$option_object = TBGContext::factory()->TBGStatus($this->getCustomField($key));
									break;
							}
						}
						catch (Exception $e) {}
						$option_id = (is_object($option_object)) ? $option_object->getID() : null;
						TBGIssueCustomFieldsTable::getTable()->saveIssueCustomFieldOption($option_id, $customdatatype->getID(), $this->getID());
						break;
					default:
						$option_id = ($this->getCustomField($key) instanceof TBGCustomDatatypeOption) ? $this->getCustomField($key)->getID() : null;
						TBGIssueCustomFieldsTable::getTable()->saveIssueCustomFieldOption($option_id, $customdatatype->getID(), $this->getID());
						break;
				}
			}
		}
		
		/**
		 * Save changes made to the issue since last time
		 * 
		 * @return boolean
		 */
		protected function _preSave($is_new)
		{
			parent::_preSave($is_new);
			if ($is_new)
			{
				if (!$this->_issue_no)
					$this->_issue_no = TBGIssuesTable::getTable()->getNextIssueNumberForProductID($this->getProject()->getID());
				
				if (!$this->_posted) $this->_posted = NOW;
				if (!$this->_last_updated) $this->_last_updated = NOW;
				if (!$this->_posted_by) $this->_posted_by = TBGContext::getUser();
				
				$step = $this->getProject()->getWorkflowScheme()->getWorkflowForIssuetype($this->getIssueType())->getFirstStep();
				$step->applyToIssue($this);
				return;
			}

			$this->_last_updated = NOW;
		}

		protected function _processChanges()
		{
			$related_issues_to_save = array();
			$changed_properties = $this->_getChangedProperties();

			if (count($changed_properties))
			{
				$is_saved_estimated = false;
				$is_saved_spent = false;
				$is_saved_assignee = false;
				$is_saved_owner = false;
				foreach ($changed_properties as $property => $value)
				{
					$compare_value = (is_object($this->$property)) ? $this->$property->getID() : $this->$property;
					if ($value['original_value'] != $compare_value)
					{
						switch ($property)
						{
							case '_title':
								$this->addLogEntry(TBGLogTable::LOG_ISSUE_UPDATE, TBGContext::getI18n()->__("Title updated"));
								break;
							case '_description':
								$this->addLogEntry(TBGLogTable::LOG_ISSUE_UPDATE, TBGContext::getI18n()->__("Description updated"));
								break;
							case '_reproduction_steps':
								$this->addLogEntry(TBGLogTable::LOG_ISSUE_REPRODUCABILITY, TBGContext::getI18n()->__("Reproduction steps updated"));
								break;
							case '_category':
								if ($value['original_value'] != 0)
								{
									$old_name = ($old_item = TBGContext::factory()->TBGCategory($value['original_value'])) ? $old_item->getName() : TBGContext::getI18n()->__('Not determined');
								}
								else
								{
									$old_name = TBGContext::getI18n()->__('Not determined');
								}
								$new_name = ($this->getCategory() instanceof TBGDatatype) ? $this->getCategory()->getName() : TBGContext::getI18n()->__('Not determined');

								$this->addLogEntry(TBGLogTable::LOG_ISSUE_CATEGORY, $old_name . ' &rArr; ' . $new_name);
								break;
							case '_pain_bug_type':
								if ($value['original_value'] != 0)
								{
									$old_name = ($old_item = self::getPainTypesOrLabel('pain_bug_type', $value['original_value'])) ? $old_item : TBGContext::getI18n()->__('Not determined');
								}
								else
								{
									$old_name = TBGContext::getI18n()->__('Not determined');
								}
								$new_name = ($new_item = self::getPainTypesOrLabel('pain_bug_type', $value['current_value'])) ? $new_item : TBGContext::getI18n()->__('Not determined');

								$this->addLogEntry(TBGLogTable::LOG_ISSUE_PAIN_BUG_TYPE, $old_name . ' &rArr; ' . $new_name);
								break;
							case '_pain_effect':
								if ($value['original_value'] != 0)
								{
									$old_name = ($old_item = self::getPainTypesOrLabel('pain_effect', $value['original_value'])) ? $old_item : TBGContext::getI18n()->__('Not determined');
								}
								else
								{
									$old_name = TBGContext::getI18n()->__('Not determined');
								}
								$new_name = ($new_item = self::getPainTypesOrLabel('pain_effect', $value['current_value'])) ? $new_item : TBGContext::getI18n()->__('Not determined');

								$this->addLogEntry(TBGLogTable::LOG_ISSUE_PAIN_EFFECT, $old_name . ' &rArr; ' . $new_name);
								break;
							case '_pain_likelihood':
								if ($value['original_value'] != 0)
								{
									$old_name = ($old_item = self::getPainTypesOrLabel('pain_likelihood', $value['original_value'])) ? $old_item : TBGContext::getI18n()->__('Not determined');
								}
								else
								{
									$old_name = TBGContext::getI18n()->__('Not determined');
								}
								$new_name = ($new_item = self::getPainTypesOrLabel('pain_likelihood', $value['current_value'])) ? $new_item : TBGContext::getI18n()->__('Not determined');

								$this->addLogEntry(TBGLogTable::LOG_ISSUE_PAIN_LIKELIHOOD, $old_name . ' &rArr; ' . $new_name);
								break;
							case '_user_pain':
								$this->addLogEntry(TBGLogTable::LOG_ISSUE_PAIN_CALCULATED, $value['original_value'] . ' &rArr; ' . $value['current_value']);
								break;
							case '_status':
								if ($value['original_value'] != 0)
								{
									$old_name = ($old_item = TBGContext::factory()->TBGStatus($value['original_value'])) ? $old_item->getName() : TBGContext::getI18n()->__('Unknown');
								}
								else
								{
									$old_name = TBGContext::getI18n()->__('Not determined');
								}
								$new_name = ($this->getStatus() instanceof TBGDatatype) ? $this->getStatus()->getName() : TBGContext::getI18n()->__('Not determined');

								$this->addLogEntry(TBGLogTable::LOG_ISSUE_STATUS, $old_name . ' &rArr; ' . $new_name);
								break;
							case '_reproducability':
								if ($value['original_value'] != 0)
								{
									$old_name = ($old_item = TBGContext::factory()->TBGReproducability($value['original_value'])) ? $old_item->getName() : TBGContext::getI18n()->__('Unknown');
								}
								else
								{
									$old_name = TBGContext::getI18n()->__('Not determined');
								}
								$new_name = ($this->getReproducability() instanceof TBGDatatype) ? $this->getReproducability()->getName() : TBGContext::getI18n()->__('Not determined');

								$this->addLogEntry(TBGLogTable::LOG_ISSUE_REPRODUCABILITY, $old_name . ' &rArr; ' . $new_name);
								break;
							case '_priority':
								if ($value['original_value'] != 0)
								{
									$old_name = ($old_item = TBGContext::factory()->TBGPriority($value['original_value'])) ? $old_item->getName() : TBGContext::getI18n()->__('Unknown');
								}
								else
								{
									$old_name = TBGContext::getI18n()->__('Not determined');
								}
								$new_name = ($this->getPriority() instanceof TBGDatatype) ? $this->getPriority()->getName() : TBGContext::getI18n()->__('Not determined');

								$this->addLogEntry(TBGLogTable::LOG_ISSUE_PRIORITY, $old_name . ' &rArr; ' . $new_name);
								break;
							case '_assignee_team':
							case '_assignee_user':
								if (!$is_saved_assignee)
								{
									$new_name = ($this->getAssignee() instanceof TBGIdentifiable) ? $this->getAssignee()->getName() : TBGContext::getI18n()->__('Not assigned');

									if ($this->getAssignee() instanceof TBGUser)
									{
										$this->startWorkingOnIssue($this->getAssignee());
									}

									$this->addLogEntry(TBGLogTable::LOG_ISSUE_ASSIGNED, $new_name);
									$is_saved_assignee = true;
								}
								break;
							case '_posted_by':
								$old_identifiable = ($value['original_value']) ? TBGContext::factory()->TBGUser($value['original_value']) : TBGContext::getI18n()->__('Unknown');
								$old_name = ($old_identifiable instanceof TBGUser) ? $old_identifiable->getName() : TBGContext::getI18n()->__('Unknown');
								$new_name = $this->getPostedBy()->getName();

								$this->addLogEntry(TBGLogTable::LOG_ISSUE_POSTED, $old_name . ' &rArr; ' . $new_name);
								break;
							case '_being_worked_on_by_user':
								if ($value['original_value'] != 0)
								{
									$old_identifiable = TBGContext::factory()->TBGUser($value['original_value']);
									$old_name = ($old_identifiable instanceof TBGUser) ? $old_identifiable->getName() : TBGContext::getI18n()->__('Unknown');
								}
								else
								{
									$old_name = TBGContext::getI18n()->__('Not being worked on');
								}
								$new_name = ($this->getUserWorkingOnIssue() instanceof TBGUser) ? $this->getUserWorkingOnIssue()->getName() : TBGContext::getI18n()->__('Not being worked on');

								$this->addLogEntry(TBGLogTable::LOG_ISSUE_USERS, $old_name . ' &rArr; ' . $new_name);
								break;
							case '_owner_team':
							case '_owner_user':
								if (!$is_saved_owner)
								{
									$new_name = ($this->getOwner() instanceof TBGIdentifiable) ? $this->getOwner()->getName() : TBGContext::getI18n()->__('Not owned by anyone');

									$this->addLogEntry(TBGLogTable::LOG_ISSUE_OWNED, $new_name);
									$is_saved_owner = true;
								}
								break;
							case '_percent_complete':
								$this->addLogEntry(TBGLogTable::LOG_ISSUE_PERCENT, $value['original_value'] . '% &rArr; ' . $this->getPercentCompleted() . '%');
								break;
							case '_resolution':
								if ($value['original_value'] != 0)
								{
									$old_name = ($old_item = TBGContext::factory()->TBGResolution($value['original_value'])) ? $old_item->getName() : TBGContext::getI18n()->__('Unknown');
								}
								else
								{
									$old_name = TBGContext::getI18n()->__('Not determined');
								}
								$new_name = ($this->getResolution() instanceof TBGDatatype) ? $this->getResolution()->getName() : TBGContext::getI18n()->__('Not determined');

								$this->addLogEntry(TBGLogTable::LOG_ISSUE_RESOLUTION, $old_name . ' &rArr; ' . $new_name);
								break;
							case '_severity':
								if ($value['original_value'] != 0)
								{
									$old_name = ($old_item = TBGContext::factory()->TBGSeverity($value['original_value'])) ? $old_item->getName() : TBGContext::getI18n()->__('Unknown');
								}
								else
								{
									$old_name = TBGContext::getI18n()->__('Not determined');
								}
								$new_name = ($this->getSeverity() instanceof TBGDatatype) ? $this->getSeverity()->getName() : TBGContext::getI18n()->__('Not determined');

								$this->addLogEntry(TBGLogTable::LOG_ISSUE_SEVERITY, $old_name . ' &rArr; ' . $new_name);
								break;
							case '_milestone':
								if ($value['original_value'] != 0)
								{
									$old_name = ($old_item = TBGContext::factory()->TBGMilestone($value['original_value'])) ? $old_item->getName() : TBGContext::getI18n()->__('Not determined');
								}
								else
								{
									$old_name = TBGContext::getI18n()->__('Not determined');
								}
								$new_name = ($this->getMilestone() instanceof TBGMilestone) ? $this->getMilestone()->getName() : TBGContext::getI18n()->__('Not determined');

								$this->addLogEntry(TBGLogTable::LOG_ISSUE_MILESTONE, $old_name . ' &rArr; ' . $new_name);
								break;
							case '_issuetype':
								if ($value['original_value'] != 0)
								{
									$old_name = ($old_item = TBGContext::factory()->TBGIssuetype($value['original_value'])) ? $old_item->getName() : TBGContext::getI18n()->__('Unknown');
								}
								else
								{
									$old_name = TBGContext::getI18n()->__('Unknown');
								}
								$new_name = ($this->getIssuetype() instanceof TBGIssuetype) ? $this->getIssuetype()->getName() : TBGContext::getI18n()->__('Unknown');

								$this->addLogEntry(TBGLogTable::LOG_ISSUE_ISSUETYPE, $old_name . ' &rArr; ' . $new_name);
								break;
							case '_estimated_months':
							case '_estimated_weeks':
							case '_estimated_days':
							case '_estimated_hours':
							case '_estimated_points':
								if (!$is_saved_estimated)
								{
									$old_time = array('months' => $this->getChangedPropertyOriginal('_estimated_months'),
														'weeks' => $this->getChangedPropertyOriginal('_estimated_weeks'),
														'days' => $this->getChangedPropertyOriginal('_estimated_days'),
														'hours' => $this->getChangedPropertyOriginal('_estimated_hours'),
														'points' => $this->getChangedPropertyOriginal('_estimated_points'));

									$old_formatted_time = (array_sum($old_time) > 0) ? $this->getFormattedTime($old_time) : TBGContext::getI18n()->__('Not estimated');
									$new_formatted_time = ($this->hasEstimatedTime()) ? $this->getFormattedTime($this->getEstimatedTime()) : TBGContext::getI18n()->__('Not estimated');
									$this->addLogEntry(TBGLogTable::LOG_ISSUE_TIME_ESTIMATED, $old_formatted_time . ' &rArr; ' . $new_formatted_time);
									$is_saved_estimated = true;
								}
								break;
							case '_spent_months':
							case '_spent_weeks':
							case '_spent_days':
							case '_spent_hours':
							case '_spent_points':
								if (!$is_saved_spent)
								{
									$old_time = array('months' => $this->getChangedPropertyOriginal('_spent_months'),
														'weeks' => $this->getChangedPropertyOriginal('_spent_weeks'),
														'days' => $this->getChangedPropertyOriginal('_spent_days'),
														'hours' => $this->getChangedPropertyOriginal('_spent_hours'),
														'points' => $this->getChangedPropertyOriginal('_spent_points'));

									$old_formatted_time = (array_sum($old_time) > 0) ? $this->getFormattedTime($old_time) : TBGContext::getI18n()->__('No time spent');
									$new_formatted_time = ($this->hasSpentTime()) ? $this->getFormattedTime($this->getSpentTime()) : TBGContext::getI18n()->__('No time spent');
									$this->addLogEntry(TBGLogTable::LOG_ISSUE_TIME_SPENT, $old_formatted_time . ' &rArr; ' . $new_formatted_time);
									$is_saved_spent = true;
								}
								break;
							case '_state':
								if ($this->isClosed())
								{
									$this->addLogEntry(TBGLogTable::LOG_ISSUE_CLOSE);
									if ($this->getMilestone() instanceof TBGMilestone)
									{
										if ($this->getMilestone()->isSprint())
										{
											if (!$this->getIssueType()->isTask())
											{
												$this->setSpentPoints($this->getEstimatedPoints());
											}
											else
											{
												if ($this->getSpentHours() < $this->getEstimatedHours())
												{
													$this->setSpentHours($this->getEstimatedHours());
												}
												foreach ($this->getParentIssues() as $parent_issue)
												{
													if ($parent_issue->checkTaskStates())
													{
														$related_issues_to_save[$parent_issue->getID()] = true;
													}
												}
											}
										}
										$this->getMilestone()->updateStatus();
									}
								}
								else
								{
									$this->addLogEntry(TBGLogTable::LOG_ISSUE_REOPEN);
								}
								break;
							case '_blocking':
								if ($this->isBlocking())
								{
									$this->addLogEntry(TBGLogTable::LOG_ISSUE_BLOCKED);
								}
								else
								{
									$this->addLogEntry(TBGLogTable::LOG_ISSUE_UNBLOCKED);
								}
								break;
							default:
								if (mb_substr($property, 0, 12) == '_customfield')
								{
									$key = mb_substr($property, 12);
									$customdatatype = TBGCustomDatatype::getByKey($key);

									switch ($customdatatype->getType())
									{
										case TBGCustomDatatype::INPUT_TEXT:
											$new_value = ($this->getCustomField($key) != '') ? $this->getCustomField($key) : TBGContext::getI18n()->__('Unknown');
											$this->addLogEntry(TBGLogTable::LOG_ISSUE_CUSTOMFIELD_CHANGED, $customdatatype->getDescription() . ': ' . $new_value);
											break;
										case TBGCustomDatatype::INPUT_TEXTAREA_SMALL:
										case TBGCustomDatatype::INPUT_TEXTAREA_MAIN:
											$new_value = ($this->getCustomField($key) != '') ? $this->getCustomField($key) : TBGContext::getI18n()->__('Unknown');
											$this->addLogEntry(TBGLogTable::LOG_ISSUE_CUSTOMFIELD_CHANGED, $customdatatype->getDescription() . ': ' . $new_value);
											break;
										case TBGCustomDatatype::EDITIONS_CHOICE:
										case TBGCustomDatatype::COMPONENTS_CHOICE:
										case TBGCustomDatatype::RELEASES_CHOICE:
										case TBGCustomDatatype::STATUS_CHOICE:
											$old_object = null;
											$new_object = null;
											try
											{
												switch ($customdatatype->getType())
												{
													case TBGCustomDatatype::EDITIONS_CHOICE:
														$old_object = TBGContext::factory()->TBGEdition($value['original_value']);
														break;
													case TBGCustomDatatype::COMPONENTS_CHOICE:
														$old_object = TBGContext::factory()->TBGComponent($value['original_value']);
														break;
													case TBGCustomDatatype::RELEASES_CHOICE:
														$old_object = TBGContext::factory()->TBGBuild($value['original_value']);
														break;
													case TBGCustomDatatype::STATUS_CHOICE:
														$old_object = TBGContext::factory()->TBGStatus($value['original_value']);
														break;
												}
											}
											catch (Exception $e) {}
											try
											{
												switch ($customdatatype->getType())
												{
													case TBGCustomDatatype::EDITIONS_CHOICE:
														$new_object = TBGContext::factory()->TBGEdition($this->getCustomField($key));
														break;
													case TBGCustomDatatype::COMPONENTS_CHOICE:
														$new_object = TBGContext::factory()->TBGComponent($this->getCustomField($key));
														break;
													case TBGCustomDatatype::RELEASES_CHOICE:
														$new_object = TBGContext::factory()->TBGBuild($this->getCustomField($key));
														break;
													case TBGCustomDatatype::STATUS_CHOICE:
														$new_object = TBGContext::factory()->TBGStatus($this->getCustomField($key));
														break;
												}
											}
											catch (Exception $e) {}
											$old_value = (is_object($old_object)) ? $old_object->getName() : TBGContext::getI18n()->__('Unknown');
											$new_value = (is_object($new_object)) ? $new_object->getName() : TBGContext::getI18n()->__('Unknown');
											$this->addLogEntry(TBGLogTable::LOG_ISSUE_CUSTOMFIELD_CHANGED, $customdatatype->getDescription() . ': ' . $old_value . ' &rArr; ' . $new_value);
											break;
										default:
											$old_item = null;
											try
											{
												$old_item = ($value['original_value']) ? new TBGCustomDatatypeOption($value['original_value']) : null;
											}
											catch (Exception $e) {}
											$old_value = ($old_item instanceof TBGCustomDatatypeOption) ? $old_item->getName() : TBGContext::getI18n()->__('Unknown');
											$new_value = ($this->getCustomField($key) instanceof TBGCustomDatatypeOption) ? $this->getCustomField($key)->getName() : TBGContext::getI18n()->__('Unknown');
											$this->addLogEntry(TBGLogTable::LOG_ISSUE_CUSTOMFIELD_CHANGED, $customdatatype->getDescription() . ': ' . $old_value . ' &rArr; ' . $new_value);
											break;
									}
								}
								break;
						}
					}
				}

				if ($is_saved_estimated)
				{
					TBGIssueEstimates::getTable()->saveEstimate($this->getID(), $this->_estimated_months, $this->_estimated_weeks, $this->_estimated_days, $this->_estimated_hours, $this->_estimated_points);
				}

				if ($is_saved_spent)
				{
					TBGIssueSpentTimes::getTable()->saveSpentTime($this->getID(), $this->_spent_months, $this->_spent_weeks, $this->_spent_days, $this->_spent_hours, $this->_spent_points);
				}

			}

			return $related_issues_to_save;
		}

		public function triggerSaveEvent($comment, $updated_by)
		{
			$log_items = $this->_log_items_added;
			if ($comment instanceof TBGComment && count($log_items))
			{
				if ($comment->getID())
				{
					foreach ($log_items as $item)
					{
						$item->setComment($comment);
						$item->save();
					}
				}
			}
			$event = TBGEvent::createNew('core', 'TBGIssue::save', $this, compact('comment', 'log_items', 'updated_by'));
			$event->trigger();
		}

		protected function _postSave($is_new)
		{
			$this->_saveCustomFieldValues();

			if (!$is_new)
			{
				$related_issues_to_save = $this->_processChanges();
				$comment = (isset($this->_save_comment)) ? $this->_save_comment : $this->addSystemComment('', TBGContext::getUser()->getID());

				$this->triggerSaveEvent($comment, TBGContext::getUser());

				if (count($related_issues_to_save))
				{
					foreach (array_keys($related_issues_to_save) as $i_id)
					{
						$related_issue = TBGIssuesTable::getTable()->selectById((int) $i_id);
						$related_issue->save();
					}
				}
			}
			else
			{
				$this->addLogEntry(TBGLogTable::LOG_ISSUE_CREATED, null, false, $this->getPosted());
				TBGEvent::createNew('core', 'TBGIssue::createNew', $this)->trigger();
			}

			$this->_clearChangedProperties();
			$this->_log_items_added = array();
			$this->getProject()->clearRecentActivities();

			if ($this->getMilestone() instanceof TBGMilestone)
			{
				$this->getMilestone()->updateStatus();
			}
			
			return true;
		}
		
		public function checkTaskStates()
		{
			if ($this->isOpen())
			{
				$open_issues = false;
				foreach ($this->getChildIssues() as $child_issue)
				{
					if ($child_issue->getIssueType()->isTask())
					{
						if ($child_issue->isOpen())
						{
							$open_issues = true;
							break;
						}
					}
				}
				if (!$open_issues)
				{
					$this->close();
					return true;
				}
			}
			return false;
		}

		/**
		 * Return the user working on this issue if any
		 *
		 * @return TBGUser
		 */
		public function getUserWorkingOnIssue()
		{
			return $this->_b2dbLazyload('_being_worked_on_by_user');
		}
		
		/**
		 * Clear the user currently working on this issue
		 * 
		 * @return null
		 */
		public function clearUserWorkingOnIssue()
		{
			$this->_addChangedProperty('_being_worked_on_by_user', null);
			$this->_being_worked_on_by_user_since = null;
		}
		
		/**
		 * Register a user as working on the issue
		 * 
		 * @param TBGUser $user
		 */
		public function startWorkingOnIssue(TBGUser $user)
		{
			$this->_addChangedProperty('_being_worked_on_by_user', $user->getID());
			$this->_being_worked_on_by_user_since = NOW;
		}
		
		public function calculateTimeSpent()
		{
			$ts_array = array('hours' => 0, 'days' => 0, 'weeks' => 0);
			$time_spent = ($this->_being_worked_on_by_user_since) ? NOW - $this->_being_worked_on_by_user_since : 0;
			if ($time_spent > 0)
			{
				$weeks_spent = 0;
				$days_spent = 0;
				$hours_spent = 0;
				
				$weeks_spent = floor($time_spent / 604800);
				$days_spent = floor(($time_spent - ($weeks_spent * 604800)) / 86400);
				$hours_spent = ceil(($time_spent - ($weeks_spent * 604800) - ($days_spent * 86400)) / 3600);

				$ts_array['hours'] = ($hours_spent < 0) ? 0 : $hours_spent;
				$ts_array['days'] = ($days_spent < 0) ? 0 : $days_spent;
				$ts_array['weeks'] = ($weeks_spent < 0) ? 0 : $weeks_spent;
			}
			return $ts_array;
		}

		/**
		 * Stop working on the issue, and save time spent
		 * 
		 * @return null
		 */
		public function stopWorkingOnIssue()
		{
			$time_spent = $this->calculateTimeSpent();
			$this->clearUserWorkingOnIssue();
			if ($time_spent['hours'] > 0) $this->addSpentHours($time_spent['hours']);
			if ($time_spent['days'] > 0) $this->addSpentDays($time_spent['days']);
			if ($time_spent['weeks'] > 0) $this->addSpentWeeks($time_spent['weeks']);
		}
		
		/**
		 * Return whether or not this issue is being worked on by a user
		 * 
		 * @return boolean
		 */
		public function isBeingWorkedOn()
		{
			return ($this->getUserWorkingOnIssue() instanceof TBGUser) ? true : false;
		}
		
		public function getWorkedOnSince()
		{
			return $this->_being_worked_on_by_user_since;
		}

		public function getPainBugType()
		{
			return $this->_pain_bug_type;
		}

		public function getPainBugTypeLabel()
		{
			return self::getPainTypesOrLabel('pain_bug_type', $this->_pain_bug_type);
		}

		public function setPainBugType($value)
		{
			$this->_addChangedProperty('_pain_bug_type', (int) $value);
			$this->_calculateUserPain();
		}

		public function getPainLikelihood()
		{
			return $this->_pain_likelihood;
		}

		public function getPainLikelihoodLabel()
		{
			return self::getPainTypesOrLabel('pain_likelihood', $this->_pain_likelihood);
		}

		public function setPainLikelihood($value)
		{
			$this->_addChangedProperty('_pain_likelihood', (int) $value);
			$this->_calculateUserPain();
		}

		public function getPainEffect()
		{
			return $this->_pain_effect;
		}

		public function getPainEffectLabel()
		{
			return self::getPainTypesOrLabel('pain_effect', $this->_pain_effect);
		}

		public function setPainEffect($value)
		{
			$this->_addChangedProperty('_pain_effect', (int) $value);
			$this->_calculateUserPain();
		}

		protected function _calculateUserPain()
		{
			$this->_addChangedProperty('_user_pain', round($this->_pain_bug_type * $this->_pain_likelihood * $this->_pain_effect / 1.75, 1));
		}

		protected function _calculateDatePain()
		{
			$user_pain = $this->_user_pain;
			if ($this->_user_pain > 0 && $this->_user_pain < 100)
			{
				$offset = NOW - $this->getPosted();
				$user_pain += floor($offset / 60 / 60 / 24) * 0.1;
			}
			return $user_pain;
		}

		public function getUserPain($real = false)
		{
			return (int) (($real) ? $this->getRealUserPain() : $this->_calculateDatePain());
		}

		public function getUserPainDiffText()
		{
			return $this->getUserPain(true) . ' + ' . ($this->getUserPain() - $this->getUserPain(true));
		}

		protected function getRealUserPain()
		{
			return $this->_user_pain;
		}

		public function hasPainBugType()
		{
			return (bool) ($this->_pain_bug_type > 0);
		}

		public function isPainBugTypeChanged()
		{
			return $this->_isPropertyChanged('_pain_bug_type');
		}

		public function isPainBugTypeMerged()
		{
			return $this->_isPropertyMerged('_pain_bug_type');
		}

		public function revertPainBugType()
		{
			$this->_revertPropertyChange('_pain_bug_type');
			$this->_calculateUserPain();
		}

		public function hasPainLikelihood()
		{
			return (bool) ($this->_pain_likelihood > 0);
		}

		public function isPainLikelihoodChanged()
		{
			return $this->_isPropertyChanged('_pain_likelihood');
		}

		public function isPainLikelihoodMerged()
		{
			return $this->_isPropertyMerged('_pain_likelihood');
		}

		public function revertPainLikelihood()
		{
			$this->_revertPropertyChange('_pain_likelihood');
			$this->_calculateUserPain();
		}

		public function hasPainEffect()
		{
			return (bool) ($this->_pain_effect > 0);
		}

		public function isPainEffectChanged()
		{
			return $this->_isPropertyChanged('_pain_effect');
		}

		public function isPainEffectMerged()
		{
			return $this->_isPropertyMerged('_pain_effect');
		}

		public function revertPainEffect()
		{
			$this->_revertPropertyChange('_pain_effect');
			$this->_calculateUserPain();
		}

		public function toJSON()
		{
			$return_values = array(
				'id' => $this->getID(),
				'issue_no' => $this->getFormattedIssueNo(),
				'state' => $this->getState(),
				'created_at' => $this->getPosted(),
				'updated_at' => $this->getLastUpdatedTime(),
				'title' => $this->getTitle(),
				'posted_by' => ($this->getPostedBy() instanceof TBGIdentifiable) ? $this->getPostedBy()->toJSON() : null,
				'assignee' => ($this->getAssignee() instanceof TBGIdentifiable) ? $this->getAssignee()->toJSON() : null,
				'status' => ($this->getStatus() instanceof TBGIdentifiable) ? $this->getStatus()->toJSON() : null,
			);

			$fields = $this->getProject()->getVisibleFieldsArray($this->getIssueType());

			foreach ($fields as $field => $details)
			{
				$identifiable = true;
				switch ($field)
				{
					case 'description':
					case 'votes':
						$identifiable = false;
					case 'resolution':
					case 'priority':
					case 'severity':
					case 'milestone':
					case 'category':
					case 'reproducability':
						$method = 'get'.ucfirst($field);
						$value = $this->$method();
						break;
					case 'owner':
						$value = $this->getOwner();
						break;
					case 'assignee':
						$value = $this->getAssignee();
						break;
					case 'percent_complete':
						$value = $this->getPercentCompleted();
						$identifiable = false;
						break;
					case 'user_pain':
						$value = $this->getUserPain();
						$identifiable = false;
						break;
					case 'reproduction_steps':
						$value = $this->getReproductionSteps();
						$identifiable = false;
						break;
					case 'estimated_time':
						$value = $this->getEstimatedTime();
						$identifiable = false;
						break;
					case 'spent_time':
						$value = $this->getSpentTime();
						$identifiable = false;
						break;
					case 'build':
					case 'edition':
					case 'component':
						break;
					default:
						$value = $this->getCustomField($field);
						$identifiable = false;
						break;
				}
				if ($identifiable)
					$return_values[$field] = ($value instanceof TBGIdentifiable) ? $value->toJSON() : null;
				else
					$return_values[$field] = $value;

			}

			$comments = array();
			foreach ($this->getComments() as $comment)
			{
				$comments[$comment->getCommentNumber()] = $comment->toJSON();
			}

			$return_values['comments'] = $comments;
			$return_values['visible_fields'] = $fields;

			return $return_values;
		}

		public function getAssignee()
		{
			$this->_b2dbLazyload('_assignee_team');
			$this->_b2dbLazyload('_assignee_user');

			if ($this->_assignee_team instanceof TBGTeam) {
				return $this->_assignee_team;
			} elseif ($this->_assignee_user instanceof TBGUser) {
				return $this->_assignee_user;
			} else {
				return null;
			}
		}

		public function hasAssignee()
		{
			return (bool) ($this->getAssignee() instanceof TBGIdentifiable);
		}

		public function setAssignee(TBGIdentifiable $assignee)
		{
			if ($assignee instanceof TBGTeam) {
				$this->_addChangedProperty('_assignee_user', null);
				$this->_addChangedProperty('_assignee_team', $assignee->getID());
			} else {
				$this->_addChangedProperty('_assignee_user', $assignee->getID());
				$this->_addChangedProperty('_assignee_team', null);
			}
		}

		public function clearAssignee()
		{
			$this->_addChangedProperty('_assignee_user', null);
			$this->_addChangedProperty('_assignee_team', null);
		}

		public function getOwner()
		{
			$this->_b2dbLazyload('_owner_team');
			$this->_b2dbLazyload('_owner_user');

			if ($this->_owner_team instanceof TBGTeam) {
				return $this->_owner_team;
			} elseif ($this->_owner_user instanceof TBGUser) {
				return $this->_owner_user;
			} else {
				return null;
			}
		}

		public function hasOwner()
		{
			return (bool) ($this->getOwner() instanceof TBGIdentifiable);
		}

		public function setOwner(TBGIdentifiable $owner)
		{
			if ($owner instanceof TBGTeam) {
				$this->_addChangedProperty('_owner_user', null);
				$this->_addChangedProperty('_owner_team', $owner);
			} else {
				$this->_addChangedProperty('_owner_user', $owner);
				$this->_addChangedProperty('_owner_team', null);
			}
		}

		public function clearOwner()
		{
			$this->_owner_team = null;
			$this->_owner_user = null;
		}

		public function setSaveComment($comment)
		{
			$this->_save_comment = $comment;
		}

	}
