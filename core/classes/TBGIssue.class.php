<?php

	/**
	 * Issue class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage main
	 */

	/**
	 * Issue class
	 *
	 * @package thebuggenie
	 * @subpackage main
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
		 * The issue title
		 *
		 * @var string
		 */
		protected $_title;
		
		/**
		 * The issue number
		 *
		 * @var integer
		 */
		protected $_issue_no;
		
		/**
		 * The unique id of the issue, used in the database
		 *
		 * @var integer
		 */
		protected $_issue_uniqueid;
		
		/**
		 * The issue type
		 *
		 * @var TBGDatatype
		 */
		protected $_issuetype;
		
		/**
		 * The project which this issue affects
		 *
		 * @var TBGProject
		 * @access protected
		 */
		protected $_project;
		
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
		 */
		protected $_description;
		
		/**
		 * This issues reproduction steps
		 * 
		 * @var string
		 */
		protected $_reproduction_steps;
		
		/**
		 * When the issue was posted
		 * 
		 * @var integer
		 */
		protected $_posted;
		
		/**
		 * When the issue was last updated
		 * 
		 * @var integer
		 */
		protected $_last_updated;
		
		/**
		 * Who posted the issue
		 * 
		 * @var TBGIdentifiable
		 */
		protected $_postedby;
		
		/**
		 * Who owns the issue
		 * 
		 * @var TBGIdentifiable
		 */
		protected $_ownedby;

		/**
		 * What kind of bug this is
		 * 
		 * @var integer
		 */
		protected $_pain_bug_type;

		/**
		 * What effect this bug has on users
		 *
		 * @var integer
		 */
		protected $_pain_effect;

		/**
		 * How likely users are to experience this bug
		 *
		 * @var integer
		 */
		protected $_pain_likelihood;

		/**
		 * Calculated user pain score
		 * 
		 * @var float
		 */
		protected $_user_pain;
		
		/**
		 * Owner type
		 * 
		 * @var integer
		 */
		protected $_ownedtype;
		
		/**
		 * Whos assigned the issue
		 * 
		 * @var TBGIdentifiable
		 */
		protected $_assignedto;
		
		/**
		 * Assignee type
		 * 
		 * @var TBGIdentifiable
		 */
		protected $_assignedtype;
		
		/**
		 * The resolution
		 * 
		 * @var TBGDatatype
		 */
		protected $_resolution;
		
		/**
		 * The issues' state (open or closed)
		 * 
		 * @var integer
		 */
		protected $_state;
		
		/**
		 * The category
		 * 
		 * @var TBGDatatype
		 */
		protected $_category;
		
		/**
		 * The status
		 * 
		 * @var TBGDatatype
		 */
		protected $_status;
		
		/**
		 * The prioroty
		 * 
		 * @var TBGDatatype
		 */
		protected $_priority;
		
		/**
		 * The reproducability
		 * 
		 * @var TBGDatatype
		 */
		protected $_reproducability;
		
		/**
		 * The severity
		 * 
		 * @var TBGDatatype
		 */
		protected $_severity;

		/**
		 * The scrum color
		 *
		 * @var string
		 */
		protected $_scrumcolor;

		/**
		 * The estimated time (months) to fix this issue
		 * 
		 * @var integer
		 */
		protected $_estimatedmonths;

		/**
		 * The estimated time (weeks) to fix this issue
		 * 
		 * @var integer
		 */
		protected $_estimatedweeks;

		/**
		 * The estimated time (days) to fix this issue
		 * 
		 * @var integer
		 */
		protected $_estimateddays;

		/**
		 * The estimated time (hours) to fix this issue
		 * 
		 * @var integer
		 */
		protected $_estimatedhours;

		/**
		 * The estimated time (points) to fix this issue
		 * 
		 * @var integer
		 */
		protected $_estimatedpoints;

		/**
		 * The time spent (months) to fix this issue
		 * 
		 * @var integer
		 */
		protected $_spentmonths;

		/**
		 * The time spent (weeks) to fix this issue
		 * 
		 * @var integer
		 */
		protected $_spentweeks;

		/**
		 * The time spent (days) to fix this issue
		 * 
		 * @var integer
		 */
		protected $_spentdays;

		/**
		 * The time spent (hours) to fix this issue
		 * 
		 * @var integer
		 */
		protected $_spenthours;

		/**
		 * The time spent (points) to fix this issue
		 * 
		 * @var integer
		 */
		protected $_spentpoints;
		
		/**
		 * How far along the issus is
		 * 
		 * @var integer
		 */
		protected $_percentcompleted;
		
		/**
		 * Which user is currently working on this issue
		 * 
		 * @var TBGUser
		 */
		protected $_being_worked_on_by;
		
		/**
		 * When the last user started working on the issue
		 * 
		 * @var integer
		 */
		protected $_being_worked_on_since;
		
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
		 * List of related users
		 * 
		 * @var array
		 */
		protected $_related_users;
		
		/**
		 * Whether the issue is deleted
		 * 
		 * @var boolean
		 */
		protected $_deleted;
		
		/**
		 * Whether the issue is blocking the next release
		 * 
		 * @var boolean
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
		 */
		protected $_votes_total = null;
		
		/**
		 * The issue this issue is a duplicate of
		 * 
		 * @var TBGIssue
		 */
		protected $_duplicateof;
		
		/**
		 * The milestone this issue is assigned to
		 * 
		 * @var TBGMilestone
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
		 */
		protected $_locked;

		/**
		 * An array of TBGComment s
		 * 
		 * @var array
		 */
		protected $_comments;

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

			$likelihoods = array();
			$likelihoods[5] = $i18n->__('Blocking further progress on the daily build');
			$likelihoods[4] = $i18n->__('A User would return the product / cannot RTM / the team would hold the release for this bug');
			$likelihoods[3] = $i18n->__('A User would likely not purchase the product / will show up in review / clearly a noticeable issue');
			$likelihoods[2] = $i18n->__("A Pain – users won't like this once they notice it / a moderate number of users won't buy");
			$likelihoods[1] = $i18n->__('Nuisance – not a big deal but noticeable / extremely unlikely to affect sales');

			$effects = array();
			$effects[5] = $i18n->__('Will affect all users');
			$effects[4] = $i18n->__('Will affect most users');
			$effects[3] = $i18n->__('Will affect average number of users');
			$effects[2] = $i18n->__('Will only affect a few users');
			$effects[1] = $i18n->__('Will affect almost no one');

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
		 * Creates a new issue and returns it
		 *
		 * @param string $title The title
		 * @param integer $issuetype The issue type
		 * @param integer $p_id The Project ID for the issue
		 * @param integer $issue_id[optional] specific issue_id
		 * 
		 * @return TBGIssue
		 */
		public static function createNew($title, $issuetype, $p_id, $issue_id = null, $notify = true)
		{
			try
			{
				$i_id = TBGIssuesTable::getTable()->createNewWithTransaction($title, $issuetype, $p_id, $issue_id);
				
				$theIssue = TBGFactory::TBGIssueLab($i_id);
				$theIssue->addLogEntry(TBGLogTable::LOG_ISSUE_CREATED);

				if ($notify)
				{
					TBGEvent::createNew('core', 'TBGIssue::createNew', $theIssue)->trigger();
				}
				return $theIssue;
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}
		
		/**
		 * Returns a TBGIssue from an issue no
		 *
		 * @param string $issue_no An integer or issue number
		 * 
		 * @return TBGIssue
		 */
		public static function getIssueFromLink($issue_no)
		{
			$theIssue = null;
			$issue_no = strtolower($issue_no);
			if (strpos($issue_no, ' ') !== false)
			{
				$issue_no = substr($issue_no, strrpos($issue_no, ' ') + 1);
			}
			if (substr($issue_no, 0, 1) == '#') $issue_no = substr($issue_no, 1);
			if (is_numeric($issue_no))
			{
				try
				{
					if (!TBGContext::isProjectContext()) return null;
					if (TBGContext::getCurrentProject()->usePrefix()) return null;
					if ($row = TBGIssuesTable::getTable()->getByProjectIDAndIssueNo(TBGContext::getCurrentProject()->getID(), $issue_no))
					$theIssue = TBGFactory::TBGIssueLab($row->get(TBGIssuesTable::ID), $row);
				}
				catch (Exception $e)
				{
					return null;
				}
			}
			else
			{
				$issue_no = explode('-', strtoupper($issue_no));
				TBGLogging::log('exploding');
				if (count($issue_no) == 2 && $row = TBGIssuesTable::getTable()->getByPrefixAndIssueNo($issue_no[0], $issue_no[1]))
				{
					$theIssue = TBGFactory::TBGIssueLab($row->get(TBGIssuesTable::ID), $row);
					if (!$theIssue->getProject()->usePrefix())
					{
						return null;
					}
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
						$issue = TBGFactory::TBGIssueLab($row->get(TBGIssuesTable::ID), $row);
						if (!$issue->hasAccess()) continue;
						$issues[] = $issue;
					}
					catch (Exception $e) {}
				}
			}
			return array($issues, $count);
		}

		public static function findIssuesByText($text)
		{
			return self::findIssues(array('text' => array('value' => $text, 'operator' => '=')));
		}
		
		/**
		 * Construct a new issue
		 *
		 * @param integer $i_id
		 * @param B2DBRow $row
		 */
		public function __construct($i_id, $row = null)
		{
			if (!is_numeric($i_id))
			{
				throw new Exception('Please specify an issue id');
			}
			if ($row === null)
			{
				$row = TBGIssuesTable::getTable()->getByID($i_id, false);
			}
	
			if (!$row instanceof B2DBRow)
			{
				throw new Exception('The specified issue id does not exist');
			}
			
			$this->_title 					= $row->get(TBGIssuesTable::TITLE);
			$this->_project					= $row->get(TBGIssuesTable::PROJECT_ID);
			$this->_issue_no 				= $row->get(TBGIssuesTable::ISSUE_NO);
			$this->_issuetype 				= $row->get(TBGIssuesTable::ISSUE_TYPE);
			$this->_issue_uniqueid 			= $row->get(TBGIssuesTable::ID);
			$this->_description 			= $row->get(TBGIssuesTable::LONG_DESCRIPTION);
			$this->_reproduction_steps		= $row->get(TBGIssuesTable::REPRODUCTION);
			$this->_posted 					= $row->get(TBGIssuesTable::POSTED);
			$this->_last_updated 			= $row->get(TBGIssuesTable::LAST_UPDATED);
			$this->_resolution 				= $row->get(TBGIssuesTable::RESOLUTION);
			$this->_state 					= $row->get(TBGIssuesTable::STATE);
			$this->_locked 					= $row->get(TBGIssuesTable::LOCKED);
			$this->_status 					= $row->get(TBGIssuesTable::STATUS);
			$this->_priority 				= $row->get(TBGIssuesTable::PRIORITY);
			$this->_severity 				= $row->get(TBGIssuesTable::SEVERITY);
			$this->_category 				= $row->get(TBGIssuesTable::CATEGORY);
			$this->_reproducability 		= $row->get(TBGIssuesTable::REPRODUCABILITY);
			$this->_scrumcolor				= $row->get(TBGIssuesTable::SCRUMCOLOR);
			$this->_postedby 				= $row->get(TBGIssuesTable::POSTED_BY);
			$this->_ownedby 				= $row->get(TBGIssuesTable::OWNED_BY);
			$this->_ownedtype				= $row->get(TBGIssuesTable::OWNED_TYPE);
			$this->_assignedto	 			= $row->get(TBGIssuesTable::ASSIGNED_TO);
			$this->_assignedtype			= $row->get(TBGIssuesTable::ASSIGNED_TYPE);
			$this->_blocking 				= (bool) $row->get(TBGIssuesTable::BLOCKING);
			$this->_duplicateof 			= $row->get(TBGIssuesTable::DUPLICATE);
			$this->_estimatedmonths			= $row->get(TBGIssuesTable::ESTIMATED_MONTHS);
			$this->_estimatedweeks			= $row->get(TBGIssuesTable::ESTIMATED_WEEKS);
			$this->_estimateddays			= $row->get(TBGIssuesTable::ESTIMATED_DAYS);
			$this->_estimatedhours			= $row->get(TBGIssuesTable::ESTIMATED_HOURS);
			$this->_estimatedpoints			= $row->get(TBGIssuesTable::ESTIMATED_POINTS);
			$this->_spentmonths				= $row->get(TBGIssuesTable::SPENT_MONTHS);
			$this->_spentweeks				= $row->get(TBGIssuesTable::SPENT_WEEKS);
			$this->_spentdays				= $row->get(TBGIssuesTable::SPENT_DAYS);
			$this->_spenthours				= $row->get(TBGIssuesTable::SPENT_HOURS);
			$this->_spentpoints				= $row->get(TBGIssuesTable::SPENT_POINTS);
			$this->_percentcompleted 		= $row->get(TBGIssuesTable::PERCENT_COMPLETE);
			$this->_milestone 				= $row->get(TBGIssuesTable::MILESTONE);
			$this->_being_worked_on_by		= (int) $row->get(TBGIssuesTable::USER_WORKING_ON);
			$this->_being_worked_on_since	= (int) $row->get(TBGIssuesTable::USER_WORKED_ON_SINCE);
			$this->_user_pain				= $row->get(TBGIssuesTable::USER_PAIN);
			$this->_pain_bug_type			= $row->get(TBGIssuesTable::PAIN_BUG_TYPE);
			$this->_pain_effect				= $row->get(TBGIssuesTable::PAIN_EFFECT);
			$this->_pain_likelihood			= $row->get(TBGIssuesTable::PAIN_LIKELIHOOD);
			$this->_votes_total				= $row->get(TBGIssuesTable::VOTES);
			$this->_deleted 				= (bool) $row->get(TBGIssuesTable::DELETED);

			if ($this->getProject() instanceof TBGProject && $this->getProject()->isDeleted())
			{
				throw new Exception("This issue ({$this->getID()}) belongs to a project that has been deleted");
			}

			$this->_populateCustomfields();
			$this->_mergeChangedProperties();
		}
		
		/**
		 * @deprecated
		 */
		public function __toString()
		{
			throw new Exception("Don't print the issue, use getFormattedTitle() instead.");
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
			return $this->getFormattedIssueNo($link_formatted, $include_issuetype) . ' - ' . $this->_title;
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
			$specific_access = TBGContext::getUser()->hasPermission("canviewissue", $i_id, 'core', true, null);
			if ($specific_access !== null)
			{
				TBGLogging::log('done checking, returning specific access ' . (($specific_access) ? 'allowed' : 'denied'));
				return $specific_access;
			}
			if ($this->getPostedByID() == TBGContext::getUser()->getID())
			{
				TBGLogging::log('done checking, allowed since this user posted it');
				return true;
			}
			if ($this->getOwnerType() == TBGIdentifiableClass::TYPE_USER && $this->getOwnerID() == TBGContext::getUser()->getID())
			{
				TBGLogging::log('done checking, allowed since this user owns it');
				return true;
			}
			if ($this->getAssigneeType() == TBGIdentifiableClass::TYPE_USER && $this->getAssigneeID() == TBGContext::getUser()->getID())
			{
				TBGLogging::log('done checking, allowed since this user is assigned to it');
				return true;
			}
			if (!TBGContext::getUser()->hasPermission('canseeallissues', 0, 'core', true, true))
			{
				TBGLogging::log('done checking, not allowed to access issues not posted by themselves');
				return false;
			}
			if ($this->getProject()->hasAccess())
			{
				TBGLogging::log('done checking, can access project');
				return true;
			}
			TBGLogging::log('done checking, denied');
			return false;
		}
		
		/**
		 * Returns the project for this issue
		 *
		 * @return TBGProject
		 */
		public function getProject()
		{
			if (is_numeric($this->_project))
			{
				try
				{
					$this->_project = TBGFactory::projectLab($this->_project);
				}
				catch (Exception $e) 
				{
					$this->_project = null;
				}
			}
			return $this->_project;
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
		 * Populates all the custom field values
		 */
		protected function _populateCustomfields()
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
					$var_name = "_customfield".$row->get(TBGCustomFieldsTable::FIELD_KEY);
					$datatype = new TBGCustomDatatype($row->get(TBGCustomFieldsTable::ID));
					
					if ($datatype->hasCustomOptions())
					{
						$this->$var_name = $row->get(TBGCustomFieldOptionsTable::OPTION_VALUE);
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
		
				if ($res = B2DB::getTable('TBGIssueAffectsEditionTable')->getByIssueID($this->getID()))
				{
					while ($row = $res->getNextRow())
					{
						try
						{
							$this->_editions[] = array(	'edition' => TBGFactory::editionLab($row->get(TBGIssueAffectsEditionTable::EDITION)),
														'status' => TBGFactory::TBGStatusLab($row->get(TBGIssueAffectsEditionTable::STATUS), $row),
														'confirmed' => (bool) $row->get(TBGIssueAffectsEditionTable::CONFIRMED),
														'a_id' => $row->get(TBGIssueAffectsEditionTable::ID));
						}
						catch (Exception $e) {}
					}
				}
				
				if ($res = B2DB::getTable('TBGIssueAffectsBuildTable')->getByIssueID($this->getID()))
				{
					while ($row = $res->getNextRow())
					{
						try
						{
							$this->_builds[] = array(	'build' => TBGFactory::buildLab($row->get(TBGIssueAffectsBuildTable::BUILD)),
														'status' => TBGFactory::TBGStatusLab($row->get(TBGIssueAffectsBuildTable::STATUS), $row),
														'confirmed' => (bool) $row->get(TBGIssueAffectsBuildTable::CONFIRMED),
														'a_id' => $row->get(TBGIssueAffectsBuildTable::ID));
						}
						catch (Exception $e) {}
					}
				}
				
				if ($res = B2DB::getTable('TBGIssueAffectsComponentTable')->getByIssueID($this->getID()))
				{
					while ($row = $res->getNextRow())
					{
						try
						{
							$this->_components[] = array(	'component' => TBGFactory::componentLab($row->get(TBGIssueAffectsComponentTable::COMPONENT)),
															'status' => TBGFactory::TBGStatusLab($row->get(TBGIssueAffectsComponentTable::STATUS), $row),
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
			return $this->_issue_uniqueid;
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
			return $this->_title;
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
			$this->_duplicateof = $d_id;
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
			if (is_numeric($this->_duplicateof))
			{
				try
				{
					$this->_duplicateof = TBGFactory::TBGIssueLab($this->_duplicateof);
				}
				catch (Exception $e) 
				{
					$this->_duplicateof = null;
				}
			}
			return $this->_duplicateof;
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
		 * Return or set whether the issue is locked
		 * 
		 * @param boolean $val[optional]
		 * 
		 * @return boolean
		 */
		public function isLocked($val = null)
		{
			if ($val !== null)
			{
				$this->setLocked($val);
			}
			return (bool) $this->_locked;
		}
		
		/**
		 * Set whether the issue is locked
		 * 
		 * @param boolean $val[optional]
		 */
		public function setLocked($val = true)
		{
			$this->_locked = $val;
		}
		
		/**
		 * Set whether the issue is unlocked
		 * 
		 * @param boolean $val[optional]
		 */
		public function setUnlocked($val = true)
		{
			$this->_locked = !$val;
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
			$retval = null;
			if ($this->getPostedByID() == TBGContext::getUser()->getID() && !$exclusive)
			{
				$retval = $this->getProject()->permissionCheck($key.'own', true);
			}
			return ($retval !== null) ? $retval : $this->getProject()->permissionCheck($key);
		}

		/**
		 * Check whether or not this user can edit issue details
		 * 
		 * @return boolean
		 */
		public function canEditIssueDetails()
		{
			if ($this->isLocked())
			{
				return $this->_permissionCheck('canlockandeditlockedissues');
			}
			if (!$this->getProject()->canChangeIssuesWithoutWorkingOnThem())
			{
				if (!$this->isBeingWorkedOn())
					return false;
				
				if ($this->getUserWorkingOnIssue()->getID() == TBGContext::getUser()->getID())
					return true;
					
				return false;
			}
			return true;
		}
		
		/**
		 * Return if the user can edit title
		 *
		 * @return boolean
		 */
		public function canEditTitle()
		{
			return (bool) ($this->_permissionCheck('caneditissuetitle') || $this->_permissionCheck('caneditissuebasic') || $this->_permissionCheck('cancreateandeditissues') || $this->_permissionCheck('caneditissue', true));
		}

		/**
		 * Return if the user can edit description
		 *
		 * @return boolean
		 */
		public function canEditDescription()
		{
			return (bool) ($this->_permissionCheck('caneditissuedescription') || $this->_permissionCheck('caneditissuebasic') || $this->_permissionCheck('cancreateandeditissues') || $this->_permissionCheck('caneditissue', true));
		}

		/**
		 * Return if the user can edit description
		 *
		 * @return boolean
		 */
		public function canEditReproductionSteps()
		{
			return (bool) ($this->_permissionCheck('caneditissuereproduction_steps') || $this->_permissionCheck('caneditissuebasic') || $this->_permissionCheck('cancreateandeditissues') || $this->_permissionCheck('caneditissue', true));
		}

		/**
		 * Return if the user can edit posted by
		 *
		 * @return boolean
		 */
		public function canEditPostedBy()
		{
			return (bool) ($this->_permissionCheck('caneditissueposted_by') || $this->_permissionCheck('caneditissue', true));
		}

		/**
		 * Return if the user can edit assigned to
		 *
		 * @return boolean
		 */
		public function canEditAssignedTo()
		{
			return (bool) ($this->_permissionCheck('caneditissueassigned_to') || $this->_permissionCheck('caneditissue', true));
		}
		
		/**
		 * Return if the user can edit owned by
		 *
		 * @return boolean
		 */
		public function canEditOwnedBy()
		{
			return (bool) ($this->_permissionCheck('caneditissueowned_by') || $this->_permissionCheck('caneditissue', true));
		}
		
		/**
		 * Return if the user can edit status
		 *
		 * @return boolean
		 */
		public function canEditStatus()
		{
			return (bool) ($this->_permissionCheck('caneditissuestatus') || $this->_permissionCheck('caneditissue', true));
		}
		
		/**
		 * Return if the user can edit category
		 *
		 * @return boolean
		 */
		public function canEditCategory()
		{
			return (bool) ($this->_permissionCheck('caneditissuecategory') || $this->_permissionCheck('caneditissue', true));
		}
		
		/**
		 * Return if the user can edit resolution
		 *
		 * @return boolean
		 */
		public function canEditResolution()
		{
			return (bool) ($this->_permissionCheck('caneditissueresolution') || $this->_permissionCheck('caneditissue', true));
		}
		
		/**
		 * Return if the user can edit reproducability
		 *
		 * @return boolean
		 */
		public function canEditReproducability()
		{
			return (bool) ($this->_permissionCheck('caneditissuereproducability') || $this->_permissionCheck('caneditissue', true));
		}
		
		/**
		 * Return if the user can edit severity
		 *
		 * @return boolean
		 */
		public function canEditSeverity()
		{
			return (bool) ($this->_permissionCheck('caneditissueseverity') || $this->_permissionCheck('caneditissue', true));
		}
		
		/**
		 * Return if the user can edit priority
		 *
		 * @return boolean
		 */
		public function canEditPriority()
		{
			return (bool) ($this->_permissionCheck('caneditissuepriority') || $this->_permissionCheck('caneditissue', true));
		}
		
		/**
		 * Return if the user can edit estimated time
		 *
		 * @return boolean
		 */
		public function canEditEstimatedTime()
		{
			return (bool) ($this->_permissionCheck('caneditissueestimated_time') || $this->_permissionCheck('caneditissue', true));
		}
		
		/**
		 * Return if the user can edit spent time
		 *
		 * @return boolean
		 */
		public function canEditSpentTime()
		{
			return (bool) ($this->_permissionCheck('caneditissueelapsed_time') || $this->_permissionCheck('caneditissue', true));
		}
		
		/**
		 * Return if the user can edit progress (percent)
		 *
		 * @return boolean
		 */
		public function canEditPercentage()
		{
			return (bool) ($this->_permissionCheck('caneditissuepercent_complete') || $this->_permissionCheck('caneditissue', true));
		}

		/**
		 * Return if the user can edit milestone
		 *
		 * @return boolean
		 */
		public function canEditMilestone()
		{
			return (bool) ($this->_permissionCheck('caneditissuemilestone') || $this->_permissionCheck('caneditissue', true));
		}
		
		/**
		 * Return if the user can delete the issue
		 *
		 * @return boolean
		 */
		public function canDeleteIssue()
		{
			return (bool) ($this->_permissionCheck('candeleteissues', true) || $this->_permissionCheck('caneditissue', true));
		}
		
		/**
		 * Return if the user can close the issue
		 *
		 * @return boolean
		 */
		public function canCloseIssue()
		{
			return (bool) ($this->_permissionCheck('cancloseissues') || $this->_permissionCheck('canclosereopenissues') || $this->_permissionCheck('caneditissue', true));
		}

		/**
		 * Return if the user can close the issue
		 *
		 * @return boolean
		 */
		public function canReopenIssue()
		{
			return (bool) ($this->_permissionCheck('canreopenissues') || $this->_permissionCheck('canclosereopenissues') || $this->_permissionCheck('caneditissue', true));
		}

		/**
		 * Return if the user can post comments on this issue
		 *
		 * @return boolean
		 */
		public function canPostComments()
		{
			return (bool) ($this->_permissionCheck('canpostcomments') || $this->_permissionCheck('canpostandeditcomments'));
		}

		/**
		 * Return if the user can attach files
		 *
		 * @return boolean
		 */
		public function canAttachFiles()
		{
			return (bool) ($this->_permissionCheck('canaddfilestoissues') || $this->_permissionCheck('canaddextrainformationtoissues'));
		}

		/**
		 * Return if the user can add related issues to this issue
		 *
		 * @return boolean
		 */
		public function canAddRelatedIssues()
		{
			return (bool) ($this->_permissionCheck('canaddrelatedissues') || $this->_permissionCheck('canaddextrainformationtoissues'));
		}

		/**
		 * Return if the user can remove attachments
		 *
		 * @return boolean
		 */
		public function canRemoveAttachments()
		{
			return (bool) ($this->_permissionCheck('canremovefilesfromissues') || $this->_permissionCheck('canaddextrainformationtoissues'));
		}

		/**
		 * Return if the user can attach links
		 *
		 * @return boolean
		 */
		public function canAttachLinks()
		{
			return (bool) ($this->_permissionCheck('canaddlinkstoissues') || $this->_permissionCheck('canaddextrainformationtoissues'));
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
			$issuetype_description = ($this->getIssueType() instanceof TBGIssuetype && $include_issuetype) ? $this->getIssueType()->getName().' ' : '';

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
			if (is_numeric($this->_issuetype))
			{
				try
				{
					$this->_issuetype = TBGFactory::TBGIssuetypeLab($this->_issuetype);
				}
				catch (Exception $e) 
				{
					$this->_issuetype = null;
				}
			}
			return $this->_issuetype;
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
		 * Returns the issue status
		 *
		 * @return TBGDatatype
		 */
		public function getStatus()
		{
			if (is_numeric($this->_status))
			{
				try
				{
					$this->_status = TBGFactory::TBGStatusLab($this->_status);
				}
				catch (Exception $e)
				{
					$this->_status = null;
				}
			}
			return $this->_status;
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
			$link_id = B2DB::getTable('TBGLinksTable')->addLinkToIssue($this->getID(), $url, $description);
			return $link_id;
		}

		/**
		 * Attach a file to the issue
		 * 
		 * @param string $file_id The file id
		 */
		public function attachFile($file_id)
		{
			B2DB::getTable('TBGIssueFilesTable')->addFileToIssue($this->getID(), $file_id);
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
				
				if ($res = B2DB::getTable('TBGIssueRelationsTable')->getRelatedIssues($this->getID()))
				{
					while ($row = $res->getNextRow())
					{
						try
						{
							if ($row->get(TBGIssueRelationsTable::PARENT_ID) == $this->getID())
							{
								$issue = TBGFactory::TBGIssueLab($row->get(TBGIssueRelationsTable::CHILD_ID));
								$this->_child_issues[$row->get(TBGIssueRelationsTable::ID)] = $issue;
							}
							else
							{
								$issue = TBGFactory::TBGIssueLab($row->get(TBGIssueRelationsTable::PARENT_ID));
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
							$issue = TBGFactory::TBGIssueLab($row->get(TBGIssuesTable::ID));
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
			return $this->_votes_total;
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
			var_dump($this->_votes);

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
			if (array_key_exists($user_id, $this->_votes))
			{
				return ($up) ? $this->_votes[$user_id] > 0 : $this->_votes[$user_id] < 0;
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
					if ($res = B2DB::getTable('TBGIssueTasksTable')->getByIssueID($this->getID()))
					{
						while ($row = $resultset->getNextRow())
						{
							$this->_tasks[$row->get(TBGIssueTasksTable::ID)] = TBGFactory::taskLab($row->get(TBGIssueTasksTable::ID), $row);
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
				if ($res = B2DB::getTable('TBGIssueTagsTable')->getByIssueID($this->getID()))
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
			if (is_numeric($this->_category))
			{
				try
				{
					$this->_category = TBGFactory::TBGCategoryLab($this->_category);
				}
				catch (Exception $e)
				{
					$this->_category = null;
				}
			}
			return $this->_category;
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
					$this->_reproducability = TBGFactory::TBGReproducabilityLab($this->_reproducability);
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
					$this->_priority = TBGFactory::TBGPriorityLab($this->_priority);
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
			if (!is_null($this->$var_name))
			{
				$datatype = TBGCustomDatatype::getByKey($key);
				if ($datatype->hasCustomOptions())
				{
					if (!is_object($this->$var_name))
					{
						$this->$var_name = TBGCustomDatatypeOption::getByValueAndKey($this->$var_name, $key);
					}
				}
			}
			return $this->$var_name;
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
			if (is_numeric($this->_milestone))
			{
				try
				{
					$this->_milestone = TBGFactory::TBGMilestoneLab($this->_milestone);
				}
				catch (Exception $e)
				{
					$this->_milestone = null;
				}
			}
			return $this->_milestone;
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
			if ($row = B2DB::getTable('TBGIssueRelationsTable')->getIssueRelation($this->getID(), $issue_id))
			{
				$related_issue = TBGFactory::TBGIssueLab($issue_id);
				if ($row->get(TBGIssueRelationsTable::PARENT_ID) == $this->getID())
				{
					$this->_removeChildIssue($related_issue, $row->get(TBGIssueRelationsTable::ID));
				}
				else
				{
					$this->_removeParentIssue($related_issue, $row->get(TBGIssueRelationsTable::ID));
				}
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
			$this->addSystemComment(TBGContext::getI18n()->__('Issue dependancy removed'), TBGContext::getI18n()->__('This issue no longer depends on the solution of issue %issue_no%', array('%issue_no%' => $related_issue->getFormattedIssueNo())), TBGContext::getUser()->getUID());
			
			$related_issue->addLogEntry(TBGLogTable::LOG_ISSUE_DEPENDS, TBGContext::getI18n()->__('Issue %issue_no% no longer depends on the solution of this issue', array('%issue_no%' => $this->getFormattedIssueNo())));
			$related_issue->addSystemComment(TBGContext::getI18n()->__('Issue dependancy removed'), TBGContext::getI18n()->__('Issue %issue_no% no longer depends on the solution of this issue', array('%issue_no%' => $this->getFormattedIssueNo())), TBGContext::getUser()->getUID());
			
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
			$this->addSystemComment(TBGContext::getI18n()->__('Issue dependancy removed'), TBGContext::getI18n()->__('Issue %issue_no% no longer depends on the solution of this issue', array('%issue_no%' => $related_issue->getFormattedIssueNo())), TBGContext::getUser()->getUID());
			
			$related_issue->addLogEntry(TBGLogTable::LOG_ISSUE_DEPENDS, TBGContext::getI18n()->__('This issue no longer depends on the solution of issue %issue_no%', array('%issue_no%' => $this->getFormattedIssueNo())));
			$related_issue->addSystemComment(TBGContext::getI18n()->__('Issue dependancy removed'), TBGContext::getI18n()->__('This issue no longer depends on the solution of issue %issue_no%', array('%issue_no%' => $this->getFormattedIssueNo())), TBGContext::getUser()->getUID());
			
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
			if (!$row = B2DB::getTable('TBGIssueRelationsTable')->getIssueRelation($this->getID(), $related_issue->getID()))
			{
				$res = B2DB::getTable('TBGIssueRelationsTable')->addParentIssue($this->getID(), $related_issue->getID());
				$this->_parent_issues = null;
				
				$related_issue->addLogEntry(TBGLogTable::LOG_ISSUE_DEPENDS, TBGContext::getI18n()->__('This %this_issue_type% now depends on the solution of %issue_type% %issue_no%', array('%this_issue_type%' => $related_issue->getIssueType()->getName(), '%issue_type%' => $this->getIssueType()->getName(), '%issue_no%' => $this->getFormattedIssueNo())));
				$related_issue->addSystemComment(TBGContext::getI18n()->__('Dependancy added'), TBGContext::getI18n()->__('This %this_issue_type% now depends on the solution of %issue_type% %issue_no%', array('%this_issue_type%' => $related_issue->getIssueType()->getName(), '%issue_type%' => $this->getIssueType()->getName(), '%issue_no%' => $this->getFormattedIssueNo())), TBGContext::getUser()->getUID());
				
				$this->addLogEntry(TBGLogTable::LOG_ISSUE_DEPENDS, TBGContext::getI18n()->__('%issue_type% %issue_no% now depends on the solution of this %this_issue_type%', array('%this_issue_type%' => $this->getIssueType()->getName(), '%issue_type%' => $related_issue->getIssueType()->getName(), '%issue_no%' => $related_issue->getFormattedIssueNo())));
				$comment = $this->addSystemComment(TBGContext::getI18n()->__('Dependancy added'), TBGContext::getI18n()->__('%issue_type% %issue_no% now depends on the solution of this %this_issue_type%', array('%this_issue_type%' => $this->getIssueType()->getName(), '%issue_type%' => $related_issue->getIssueType()->getName(), '%issue_no%' => $related_issue->getFormattedIssueNo())), TBGContext::getUser()->getUID());
				
				return ($comment instanceof TBGComment) ? $comment : true;
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
			if (!$row = B2DB::getTable('TBGIssueRelationsTable')->getIssueRelation($this->getID(), $related_issue->getID()))
			{
				$res = B2DB::getTable('TBGIssueRelationsTable')->addChildIssue($this->getID(), $related_issue->getID());
				$this->_child_issues = null;
				
				$related_issue->addLogEntry(TBGLogTable::LOG_ISSUE_DEPENDS, TBGContext::getI18n()->__('%issue_type% %issue_no% now depends on the solution of this %this_issue_type%', array('%this_issue_type%' => $related_issue->getIssueType()->getName(), '%issue_type%' => $this->getIssueType()->getName(), '%issue_no%' => $this->getFormattedIssueNo())));
				$related_issue->addSystemComment(TBGContext::getI18n()->__('Dependancy added'), TBGContext::getI18n()->__('%issue_type% %issue_no% now depends on the solution of this %this_issue_type%', array('%this_issue_type%' => $related_issue->getIssueType()->getName(), '%issue_type%' => $this->getIssueType()->getName(), '%issue_no%' => $this->getFormattedIssueNo())), TBGContext::getUser()->getUID());
				
				$this->addLogEntry(TBGLogTable::LOG_ISSUE_DEPENDS, TBGContext::getI18n()->__('This %this_issue_type% now depends on the solution of %issue_type% %issue_no%', array('%this_issue_type%' => $this->getIssueType()->getName(), '%issue_type%' => $related_issue->getIssueType()->getName(), '%issue_no%' => $related_issue->getFormattedIssueNo())));
				$comment = $this->addSystemComment(TBGContext::getI18n()->__('Dependancy added'), TBGContext::getI18n()->__('This %this_issue_type% now depends on the solution of %issue_type% %issue_no%', array('%this_issue_type%' => $this->getIssueType()->getName(), '%issue_type%' => $related_issue->getIssueType()->getName(), '%issue_no%' => $related_issue->getFormattedIssueNo())), TBGContext::getUser()->getUID());
				
				return ($comment instanceof TBGComment) ? $comment : true;
			}
			return false;
		}

		/**
		 * Return the assignee
		 *
		 * @return TBGIdentifiableClass
		 */
		public function getAssignee()
		{
			if (is_numeric($this->_assignedto))
			{
				try
				{
					if ($this->_assignedtype == TBGIdentifiableClass::TYPE_USER)
					{
						$this->_assignedto = TBGFactory::userLab($this->_assignedto);
					}
					elseif ($this->_assignedtype == TBGIdentifiableClass::TYPE_TEAM)
					{
						$this->_assignedto = TBGFactory::teamLab($this->_assignedto);
					}
				}
				catch (Exception $e)
				{
					$this->_assignedto = null;
					$this->_assignedtype = null;
				}
			}
	
			return $this->_assignedto;
		}
		
		/**
		 * Whether or not the issue is assigned to someone
		 *
		 * @return boolean
		 */
		public function isAssigned()
		{
			return (bool) ($this->getAssignee() instanceof TBGIdentifiableClass);
		}
		
		/**
		 * Returns the assignee type
		 *
		 * @return integer
		 */
		public function getAssigneeType()
		{
			$assignee = $this->getAssignee();
			return ($assignee instanceof TBGIdentifiableClass) ? $assignee->getType() : null;
		}
		
		/**
		 * Return the assignee id
		 *
		 * @return integer
		 */
		public function getAssigneeID()
		{
			$assignee = $this->getAssignee();
			return ($assignee instanceof TBGIdentifiableClass) ? $assignee->getID() : null;
		}
		
		/**
		 * Assign the issue
		 * 
		 * @param TBGIdentifiableClass $assignee The user/team you want to assign it to
		 */
		public function setAssignee(TBGIdentifiableClass $assignee)
		{
			$this->_addChangedProperty('_assignedto', $assignee->getID());
			$this->_addChangedProperty('_assignedtype', $assignee->getType());
		}
		
		/**
		 * Set issue assignee to noone
		 */
		public function unsetAssignee()
		{
			$this->_addChangedProperty('_assignedto', 0);
			$this->_addChangedProperty('_assignedtype', 0);
		}
		
		/**
		 * Return the owner
		 *
		 * @return TBGIdentifiableClass
		 */
		public function getOwner()
		{
			if (is_numeric($this->_ownedby))
			{
				try
				{
					if ($this->_ownedtype == TBGIdentifiableClass::TYPE_USER)
					{
						$this->_ownedby = TBGFactory::userLab($this->_ownedby);
					}
					elseif ($this->_ownedtype == TBGIdentifiableClass::TYPE_TEAM)
					{
						$this->_ownedby = TBGFactory::teamLab($this->_ownedby);
					}
				}
				catch (Exception $e)
				{
					$this->_ownedby = null;
					$this->_ownedtype = null;
				}
			}
	
			return $this->_ownedby;
		}
		
		/**
		 * Whether or not the issue is owned by someone
		 *
		 * @return boolean
		 */
		public function isOwned()
		{
			return (bool) ($this->getOwner() instanceof TBGIdentifiableClass);
		}
		
		/**
		 * Returns the owner type
		 *
		 * @return integer
		 */
		public function getOwnerType()
		{
			$owner = $this->getOwner();
			return ($owner instanceof TBGIdentifiableClass) ? $owner->getType() : null;
		}
		
		/**
		 * Return the owner id
		 *
		 * @return integer
		 */
		public function getOwnerID()
		{
			$owner = $this->getOwner();
			return ($owner instanceof TBGIdentifiableClass) ? $owner->getID() : null;
		}
		
		/**
		 * Set issue owner
		 * 
		 * @param TBGIdentifiableClass $owner The user/team you want to own the issue
		 */
		public function setOwner(TBGIdentifiableClass $owner)
		{
			$this->_addChangedProperty('_ownedby', $owner->getID());
			$this->_addChangedProperty('_ownedtype', $owner->getType());
		}
		
		/**
		 * Set issue owner to noone
		 */
		public function unsetOwner()
		{
			$this->_addChangedProperty('_ownedby', 0);
			$this->_addChangedProperty('_ownedtype', 0);
		}
		
		/**
		 * Return the poster
		 *
		 * @return TBGUser
		 */
		public function getPostedBy()
		{
			if (is_numeric($this->_postedby))
			{
				try
				{
					$this->_postedby = TBGFactory::userLab($this->_postedby);
				}
				catch (Exception $e)
				{
					$this->_postedby = null;
				}
			}
	
			return $this->_postedby;
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
			$this->_addChangedProperty('_postedby', $poster->getID());
		}
		
		/**
		 * Returns the percentage completed
		 * 
		 * @return integer
		 */
		public function getPercentCompleted()
		{
			return (int) $this->_percentcompleted;
		}
		
		/**
		 * Set percentage completed
		 * 
		 * @param integer $percentage
		 */
		public function setPercentCompleted($percentage)
		{
			$this->_addChangedProperty('_percentcompleted', (int) $percentage);
		}
	
		/**
		 * Returns the resolution
		 *
		 * @return TBGDatatype
		 */
		public function getResolution()
		{
			if (is_numeric($this->_resolution))
			{
				try
				{
					$this->_resolution = TBGFactory::TBGResolutionLab($this->_resolution);
				}
				catch (Exception $e)
				{
					$this->_resolution = null;
				}
			}
			return $this->_resolution;
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
			if (is_numeric($this->_severity))
			{
				try
				{
					$this->_severity = TBGFactory::TBGSeverityLab($this->_severity);
				}
				catch (Exception $e)
				{
					$this->_severity = null;
				}
			}
			return $this->_severity;
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
			return array('months' => (int) $this->_estimatedmonths, 'weeks' => (int) $this->_estimatedweeks, 'days' => (int) $this->_estimateddays, 'hours' => (int) $this->_estimatedhours, 'points' => (int) $this->_estimatedpoints);
		}
		
		/**
		 * Returns the estimated months
		 * 
		 * @return integer
		 */
		public function getEstimatedMonths()
		{
			return (int) $this->_estimatedmonths;
		}

		/**
		 * Returns the estimated weeks
		 * 
		 * @return integer
		 */
		public function getEstimatedWeeks()
		{
			return (int) $this->_estimatedweeks;
		}

		/**
		 * Returns the estimated days
		 * 
		 * @return integer
		 */
		public function getEstimatedDays()
		{
			return (int) $this->_estimateddays;
		}
		
		/**
		 * Returns the estimated hours
		 * 
		 * @return integer
		 */
		public function getEstimatedHours()
		{
			return (int) $this->_estimatedhours;
		}
		
		/**
		 * Returns the estimated points
		 * 
		 * @return integer
		 */
		public function getEstimatedPoints()
		{
			return (int) $this->_estimatedpoints;
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
			$string = strtolower(trim($string));
			$time_arr = preg_split('/(\,|\/|and|or|plus)/', $string);
			foreach ($time_arr as $time_elm)
			{
				$time_parts = explode(' ', trim($time_elm));
				if (is_array($time_parts) && count($time_parts) > 1)
				{
					switch (true)
					{
						case stristr($time_parts[1], 'month'):
							$retarr['months'] = (int) trim($time_parts[0]);
							break;
						case stristr($time_parts[1], 'week'):
							$retarr['weeks'] = (int) trim($time_parts[0]);
							break;
						case stristr($time_parts[1], 'day'):
							$retarr['days'] = (int) trim($time_parts[0]);
							break;
						case stristr($time_parts[1], 'hour'):
							$retarr['hours'] = (int) trim($time_parts[0]);
							break;
						case stristr($time_parts[1], 'point'):
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
				$this->_addChangedProperty('_estimatedmonths', 0);
				$this->_addChangedProperty('_estimatedweeks', 0);
				$this->_addChangedProperty('_estimateddays', 0);
				if ($this->getProject()->getTimeUnit() == TBGProject::TIME_UNIT_HOURS)
				{
					$this->_addChangedProperty('_estimatedpoints', 0);
					$this->_addChangedProperty('_estimatedhours', (int) $time);
				}
				elseif ($this->getProject()->getTimeUnit() == TBGProject::TIME_UNIT_POINTS)
				{
					$this->_addChangedProperty('_estimatedhours', 0);
					$this->_addChangedProperty('_estimatedpoints', (int) $time);
				}
				else
				{
					$this->_addChangedProperty('_estimatedhours', 0);
					$this->_addChangedProperty('_estimatedpoints', 0);
				}
			}
			else
			{
				$time = $this->_convertFancyStringToTime($time);
				$this->_addChangedProperty('_estimatedmonths', $time['months']);
				$this->_addChangedProperty('_estimatedweeks', $time['weeks']);
				$this->_addChangedProperty('_estimateddays', $time['days']);
				$this->_addChangedProperty('_estimatedhours', $time['hours']);
				$this->_addChangedProperty('_estimatedpoints', $time['points']);
			}
		}
		
		/**
		 * Set estimated months
		 * 
		 * @param integer $months The number of months estimated
		 */
		public function setEstimatedMonths($months)
		{
			$this->_addChangedProperty('_estimatedmonths', $months);
		}
	
		/**
		 * Set estimated weeks
		 * 
		 * @param integer $weeks The number of weeks estimated
		 */
		public function setEstimatedWeeks($weeks)
		{
			$this->_addChangedProperty('_estimatedweeks', $weeks);
		}
	
		/**
		 * Set estimated days
		 * 
		 * @param integer $days The number of days estimated
		 */
		public function setEstimatedDays($days)
		{
			$this->_addChangedProperty('_estimateddays', $days);
		}
	
		/**
		 * Set estimated hours
		 * 
		 * @param integer $hours The number of hours estimated
		 */
		public function setEstimatedHours($hours)
		{
			$this->_addChangedProperty('_estimatedhours', $hours);
		}
	
		/**
		 * Set estimated points
		 * 
		 * @param integer $points The number of points estimated
		 */
		public function setEstimatedPoints($points)
		{
			$this->_addChangedProperty('_estimatedpoints', $points);
		}
		
		/**
		 * Check to see whether the estimated time is changed
		 * 
		 * @return boolean
		 */
		public function isEstimatedTimeChanged()
		{
			return (bool) ($this->isEstimatedMonthsChanged() || $this->isEstimatedWeeksChanged() || $this->isEstimatedDaysChanged() || $this->isEstimatedHoursChanged() || $this->isEstimatedPointsChanged());
		}

		/**
		 * Check to see whether the estimated time is merged
		 * 
		 * @return boolean
		 */
		public function isEstimatedTimeMerged()
		{
			return (bool) ($this->isEstimatedMonthsMerged() || $this->isEstimatedWeeksMerged() || $this->isEstimatedDaysMerged() || $this->isEstimatedHoursMerged() || $this->isEstimatedPointsMerged());
		}
		
		/**
		 * Reverts estimated time
		 */
		public function revertEstimatedTime()
		{
			$this->revertEstimatedMonths();
			$this->revertEstimatedWeeks();
			$this->revertEstimatedDays();
			$this->revertEstimatedHours();
			$this->revertEstimatedPoints();
		}
	
		/**
		 * Check to see whether the owner is changed
		 * 
		 * @return boolean
		 */
		public function isOwnedByChanged()
		{
			return (bool) ($this->isOwnedTypeChanged() || $this->_isPropertyChanged('_ownedby'));
		}

		/**
		 * Check to see whether the owner is merged
		 * 
		 * @return boolean
		 */
		public function isOwnedByMerged()
		{
			return (bool) ($this->isOwnedTypeMerged() || $this->_isPropertyMerged('_ownedby'));
		}
		
		/**
		 * Reverts estimated time
		 */
		public function revertOwnedBy()
		{
			$this->revertOwnedType();
			$this->_revertPropertyChange('_ownedby');
		}

		/**
		 * Check to see whether the assignee is changed
		 * 
		 * @return boolean
		 */
		public function isAssignedToChanged()
		{
			return (bool) ($this->isAssignedTypeChanged() || $this->_isPropertyChanged('_assignedto'));
		}

		/**
		 * Check to see whether the owner is merged
		 * 
		 * @return boolean
		 */
		public function isAssignedToMerged()
		{
			return (bool) ($this->isAssignedTypeMerged() || $this->_isPropertyMerged('_assignedto'));
		}
		
		/**
		 * Reverts estimated time
		 */
		public function revertAssignedTo()
		{
			$this->revertAssignedType();
			$this->_revertPropertyChange('_assignedto');
		}

		/**
		 * Returns an array with the spent time
		 *
		 * @return array
		 */
		public function getSpentTime()
		{
			return array('months' => (int) $this->_spentmonths, 'weeks' => (int) $this->_spentweeks, 'days' => (int) $this->_spentdays, 'hours' => (int) $this->_spenthours, 'points' => (int) $this->_spentpoints);
		}
		
		/**
		 * Returns the spent months
		 * 
		 * @return integer
		 */
		public function getSpentMonths()
		{
			return (int) $this->_spentmonths;
		}

		/**
		 * Returns the spent weeks
		 * 
		 * @return integer
		 */
		public function getSpentWeeks()
		{
			return (int) $this->_spentweeks;
		}

		/**
		 * Returns the spent days
		 * 
		 * @return integer
		 */
		public function getSpentDays()
		{
			return (int) $this->_spentdays;
		}
		
		/**
		 * Returns the spent hours
		 * 
		 * @return integer
		 */
		public function getSpentHours()
		{
			return (int) $this->_spenthours;
		}
		
		/**
		 * Returns the spent points
		 * 
		 * @return integer
		 */
		public function getSpentPoints()
		{
			return (int) $this->_spentpoints;
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
				$this->_addChangedProperty('_spentmonths', 0);
				$this->_addChangedProperty('_spentweeks', 0);
				$this->_addChangedProperty('_spentdays', 0);
				if ($this->getProject()->getTimeUnit() == TBGProject::TIME_UNIT_HOURS)
				{
					$this->_addChangedProperty('_spentpoints', 0);
					$this->_addChangedProperty('_spenthours', (int) $time);
				}
				elseif ($this->getProject()->getTimeUnit() == TBGProject::TIME_UNIT_POINTS)
				{
					$this->_addChangedProperty('_spenthours', 0);
					$this->_addChangedProperty('_spentpoints', (int) $time);
				}
				else
				{
					$this->_addChangedProperty('_spenthours', 0);
					$this->_addChangedProperty('_spentpoints', 0);
				}
			}
			else
			{
				$time = $this->_convertFancyStringToTime($time);
				$this->_addChangedProperty('_spentmonths', $time['months']);
				$this->_addChangedProperty('_spentweeks', $time['weeks']);
				$this->_addChangedProperty('_spentdays', $time['days']);
				$this->_addChangedProperty('_spenthours', $time['hours']);
				$this->_addChangedProperty('_spentpoints', $time['points']);
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
				if ($this->getProject()->getTimeUnit() == TBGProject::TIME_UNIT_HOURS)
				{
					$this->_addChangedProperty('_spenthours', $this->_spenthours + (int) $time);
				}
				elseif ($this->getProject()->getTimeUnit() == TBGProject::TIME_UNIT_POINTS)
				{
					$this->_addChangedProperty('_spentpoints', $this->_spentpoints + (int) $time);
				}
			}
			else
			{
				$time = $this->_convertFancyStringToTime($time);
				$this->_addChangedProperty('_spentmonths', $this->_spentmonths + $time['months']);
				$this->_addChangedProperty('_spentweeks', $this->_spentweeks + $time['weeks']);
				$this->_addChangedProperty('_spentdays', $this->_spentdays + $time['days']);
				$this->_addChangedProperty('_spenthours', $this->_spenthours + $time['hours']);
				$this->_addChangedProperty('_spentpoints', $this->_spentpoints + $time['points']);
			}
		}		

		/**
		 * Set spent months
		 * 
		 * @param integer $months The number of months spent
		 */
		public function setSpentMonths($months)
		{
			$this->_addChangedProperty('_spentmonths', $months);
		}
	
		/**
		 * Set spent weeks
		 * 
		 * @param integer $weeks The number of weeks spent
		 */
		public function setSpentWeeks($weeks)
		{
			$this->_addChangedProperty('_spentweeks', $weeks);
		}
	
		/**
		 * Set spent days
		 * 
		 * @param integer $days The number of days spent
		 */
		public function setSpentDays($days)
		{
			$this->_addChangedProperty('_spentdays', $days);
		}
	
		/**
		 * Set spent hours
		 * 
		 * @param integer $hours The number of hours spent
		 */
		public function setSpentHours($hours)
		{
			$this->_addChangedProperty('_spenthours', $hours);
		}
	
		/**
		 * Set spent points
		 * 
		 * @param integer $points The number of points spent
		 */
		public function setSpentPoints($points)
		{
			$this->_addChangedProperty('_spentpoints', $points);
		}

		/**
		 * Add spent months
		 * 
		 * @param integer $months The number of months spent
		 */
		public function addSpentMonths($months)
		{
			$this->_addChangedProperty('_spentmonths', $this->_spentmonths + $months);
		}
	
		/**
		 * Add spent weeks
		 * 
		 * @param integer $weeks The number of weeks spent
		 */
		public function addSpentWeeks($weeks)
		{
			$this->_addChangedProperty('_spentweeks', $this->_spentweeks + $weeks);
		}
	
		/**
		 * Add spent days
		 * 
		 * @param integer $days The number of days spent
		 */
		public function addSpentDays($days)
		{
			$this->_addChangedProperty('_spentdays', $this->_spentdays + $days);
		}
	
		/**
		 * Add spent hours
		 * 
		 * @param integer $hours The number of hours spent
		 */
		public function addSpentHours($hours)
		{
			$this->_addChangedProperty('_spenthours', $this->_spenthours + $hours);
		}
	
		/**
		 * Add spent points
		 * 
		 * @param integer $points The number of points spent
		 */
		public function addSpentPoints($points)
		{
			$this->_addChangedProperty('_spentpoints', $this->_spentpoints + $points);
		}
		
		/**
		 * Check to see whether the spent time is changed
		 * 
		 * @return boolean
		 */
		public function isSpentTimeChanged()
		{
			return (bool) ($this->isSpentMonthsChanged() || $this->isSpentWeeksChanged() || $this->isSpentDaysChanged() || $this->isSpentHoursChanged() || $this->isSpentPointsChanged());
		}

		/**
		 * Check to see whether the spent time is merged
		 * 
		 * @return boolean
		 */
		public function isSpentTimeMerged()
		{
			return (bool) ($this->isSpentMonthsMerged() || $this->isSpentWeeksMerged() || $this->isSpentDaysMerged() || $this->isSpentHoursMerged() || $this->isSpentPointsMerged());
		}
		
		/**
		 * Reverts spent time
		 */
		public function revertSpentTime()
		{
			$this->revertSpentMonths();
			$this->revertSpentWeeks();
			$this->revertSpentDays();
			$this->revertSpentHours();
			$this->revertSpentPoints();
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
				if ($retval = B2DB::getTable('TBGIssueAffectsBuildTable')->setIssueAffected($this->getID(), $build->getID()))
				{
					$this->addLogEntry(TBGLogTable::LOG_AFF_ADD, TBGContext::getI18n()->__("'%release_name%' added", array('%release_name%' => $build->getName())));
					$this->addSystemComment(TBGContext::getI18n()->__("Affected releases"), TBGContext::getI18n()->__('[b]%release_name%[/b] has been added to the list of affected releases', array('%release_name%' => $build->getName())), TBGContext::getUser()->getUID());
				}
				return $retval;
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
				if ($retval = B2DB::getTable('TBGIssueAffectsEditionTable')->setIssueAffected($this->getID(), $edition->getID()))
				{
					$this->addLogEntry(TBGLogTable::LOG_AFF_ADD, TBGContext::getI18n()->__("'%edition_name%' added", array('%edition_name%' => $edition->getName())));
					$this->addSystemComment(TBGContext::getI18n()->__("Affected editions"), TBGContext::getI18n()->__('[b]%edition_name%[/b] has been added to the list of affected editions', array('%edition_name%' => $edition->getName())), TBGContext::getUser()->getUID());
				}
				return $retval;
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
				if ($retval = B2DB::getTable('TBGIssueAffectsComponentTable')->setIssueAffected($this->getID(), $component->getID()))
				{
					$this->addLogEntry(TBGLogTable::LOG_AFF_ADD, TBGContext::getI18n()->__("'%component_name%' added", array('%component_name%' => $component->getName())));
					$this->addSystemComment(TBGContext::getI18n()->__("Affected components"), TBGContext::getI18n()->__('[b]%component_name%[/b] has been added to the list of affected components', array('%component_name%' => $component->getName())), TBGContext::getUser()->getUID());
				}
				return $retval;
			}
			return false;
		}
		
		/**
		 * Remove an affected edition, component or version
		 * 
		 * @see removeAffectedEdition()
		 * @see removeAffectedBuild()
		 * @see removeAffectedComponent()
		 * 
		 * @param TBGVersionItem $item The item you want to remove
		 * 
		 * @return boolean
		 */
		public function removeAffectedItem($item)
		{
			switch (get_class($item))
			{
				case 'TBGEdition':
					return $this->removeAffectedEdition($item);
					break;
				case 'TBGBuild':
					return $this->removeAffectedBuild($item);
					break;
				case 'TBGComponent':
					return $this->removeAffectedComponent($item);
					break;
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
			if (B2DB::getTable('TBGIssueAffectsEditionTable')->deleteByIssueIDandEditionID($this->getID(), $item->getID()))
			{
				$aff_name = $item->getName();
				$this->addLogEntry(TBGLogTable::LOG_AFF_DELETE, TBGContext::getI18n()->__("'%item_name%' removed", array('%item_name%' => $item->getName())));
				$this->addSystemComment(TBGContext::getI18n()->__("Affected editions"), TBGContext::getI18n()->__("[s][b]%edition_name%[/b] has been removed from the list of affected editions[/s]", array('%edition_name%' => $item->getName())), TBGContext::getUser()->getUID());
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
			if (B2DB::getTable('TBGIssueAffectsBuildTable')->deleteByIssueIDandBuildID($this->getID(), $item->getID()))
			{
				$aff_name = $item->getName();
				$this->addLogEntry(TBGLogTable::LOG_AFF_DELETE, TBGContext::getI18n()->__("'%item_name%' removed", array('%item_name%' => $item->getName())));
				$this->addSystemComment(TBGContext::getI18n()->__("Affected releases"), TBGContext::getI18n()->__("[s][b]%release_name%[/b] has been removed from the list of affected releases[/s]", array('%release_name%' => $item->getName())), TBGContext::getUser()->getUID());
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
			if (B2DB::getTable('TBGIssueAffectsComponentTable')->deleteByIssueIDandComponentID($this->getID(), $item->getID()))
			{
				$aff_name = $item->getName();
				$this->addLogEntry(TBGLogTable::LOG_AFF_DELETE, TBGContext::getI18n()->__("'%item_name%' removed", array('%item_name%' => $item->getName())));
				$this->addSystemComment(TBGContext::getI18n()->__("Affected components"), TBGContext::getI18n()->__("[s][b]%component_name%[/b] has been removed from the list of affected components[/s]", array('%component_name%' => $item->getName())), TBGContext::getUser()->getUID());
				return true;
			}
			return false;
		}

		/**
		 * Mark an affected edition, build or component as confirmed or unconfirmed
		 * 
		 * @see confirmAffectedEdition()
		 * @see confirmAffectedBuild()
		 * @see confirmAffectedComponent()
		 * 
		 * @param TBGVersionItem $item The item to mark as confirmed/unconfirmed
		 * @param boolean $confirmed[optional] Confirmed or unconfirumed
		 * 
		 * @return boolean
		 */
		public function confirmAffectedItem($item, $confirmed = true)
		{
			switch (get_class($item))
			{
				case 'TBGEdition':
					return $this->confirmAffectedEdition($item, $confirmed);
					break;
				case 'TBGBuild':
					return $this->confirmAffectedBuild($item, $confirmed);
					break;
				case 'TBGComponent':
					return $this->confirmAffectedComponent($item, $confirmed);
					break;
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
			if (B2DB::getTable('TBGIssueAffectsEditionTable')->confirmByIssueIDandEditionID($this->getID(), $item->getID(), $confirmed))
			{
				$this->addLogEntry(TBGLogTable::LOG_AFF_DELETE, TBGContext::getI18n()->__("'%item_name%' confirmed", array('%item_name%' => $item->getName())));
				$this->addSystemComment(TBGContext::getI18n()->__("Affected editions"), TBGContext::getI18n()->__("[b]%edition_name%[/b] has been confirmed for this issue", array('%edition_name%' => $item->getName())), TBGContext::getUser()->getUID());
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
			if (B2DB::getTable('TBGIssueAffectsBuildTable')->confirmByIssueIDandBuildID($this->getID(), $item->getID(), $confirmed))
			{
				$this->addLogEntry(TBGLogTable::LOG_AFF_DELETE, TBGContext::getI18n()->__("'%item_name%' confirmed", array('%item_name%' => $item->getName())));
				$this->addSystemComment(TBGContext::getI18n()->__("Affected releases"), TBGContext::getI18n()->__("[b]%release_name%[/b] has been confirmed for this issue", array('%release_name%' => $item->getName())), TBGContext::getUser()->getUID());
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
			if (B2DB::getTable('TBGIssueAffectsComponentTable')->confirmByIssueIDandComponentID($this->getID(), $item->getID(), $confirmed))
			{
				$this->addLogEntry(TBGLogTable::LOG_AFF_DELETE, TBGContext::getI18n()->__("'%item_name%' confirmed", array('%item_name%' => $item->getName())));
				$this->addSystemComment(TBGContext::getI18n()->__("Affected components"), TBGContext::getI18n()->__("[b]%component_name%[/b] has been confirmed for this issue", array('%component_name%' => $item->getName())), TBGContext::getUser()->getUID());
				return true;
			}
			return false;
		}
		
		/**
		 * Set the status of an affected item for this issue
		 * 
		 * @param TBGVersionItem $item The item to set status for
		 * @param TBGDatatype $status The status to set
		 * 
		 * @return boolean
		 */
		public function setAffectedItemStatus($item, $status)
		{
			switch (get_class($item))
			{
				case 'TBGEdition':
					return $this->setAffectedEditionStatus($item, $status);
					break;
				case 'TBGBuild':
					return $this->setAffectedBuildStatus($item, $status);
					break;
				case 'TBGComponent':
					return $this->setAffectedComponentStatus($item, $status);
					break;
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
			if (B2DB::getTable('TBGIssueAffectsEditionTable')->setStatusByIssueIDandEditionID($this->getID(), $item->getID(), $status->getID()))
			{
				$this->addLogEntry(TBGLogTable::LOG_AFF_DELETE, TBGContext::getI18n()->__("'%item_name%' -> '%status_name%", array('%item_name%' => $item->getName(), '%status_name%' => $status->getName())));
				$this->addSystemComment(TBGContext::getI18n()->__("Affected editions"), TBGContext::getI18n()->__("[b]%edition_name%[/b] has been set to status '%status_name% for this issue", array('%edition_name%' => $item->getName(), '%status_name%' => $status->getName())), TBGContext::getUser()->getUID());
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
			if (B2DB::getTable('TBGIssueAffectsBuildTable')->setStatusByIssueIDandBuildID($this->getID(), $item->getID(), $status->getID()))
			{
				$this->addLogEntry(TBGLogTable::LOG_AFF_DELETE, TBGContext::getI18n()->__("'%item_name%' -> '%status_name%", array('%item_name%' => $item->getName(), '%status_name%' => $status->getName())));
				$this->addSystemComment(TBGContext::getI18n()->__("Affected releases"), TBGContext::getI18n()->__("[b]%release_name%[/b] has been set to status '%status_name% for this issue", array('%release_name%' => $item->getName(), '%status_name%' => $status->getName())), TBGContext::getUser()->getUID());
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
			if (B2DB::getTable('TBGIssueAffectsComponentTable')->setStatusByIssueIDandComponentID($this->getID(), $item->getID(), $status->getID()))
			{
				$this->addLogEntry(TBGLogTable::LOG_AFF_DELETE, TBGContext::getI18n()->__("'%item_name%' -> '%status_name%", array('%item_name%' => $item->getName(), '%status_name%' => $status->getName())));
				$this->addSystemComment(TBGContext::getI18n()->__("Affected components"), TBGContext::getI18n()->__("[b]%component_name%[/b] has been set to status '%status_name% for this issue", array('%component_name%' => $item->getName(), '%status_name%' => $status->getName())), TBGContext::getUser()->getUID());
				return true;
			}
			return false;
		}
		
		/**
		 * Adds a task and returns the task id
		 *
		 * @param string $title
		 * @param string $desc
		 * 
		 * @return TBGTask The task that was created
		 */
		public function addTask($title, $desc)
		{
			$task = TBGTask::createTask($title, $desc, $this->getID());
			
			$this->addLogEntry(TBGLogTable::LOG_TASK_ADD, TBGContext::getI18n()->__("Added task '%task_title%'", array('%task_title%' => $title)));
			$this->addSystemComment(TBGContext::getI18n()->__("Task added"), TBGContext::getI18n()->__("The task '%task_title%' has been added", array('%task_title%' => $title)), TBGContext::getUser()->getUID());
	
			return $task;
		}
	
		/**
		 * Returns userids which have some kind of connection to this issue and should be notified on change
		 *
		 * @return array
		 */
		public function getRelatedUIDs()
		{
			if ($this->_related_users === null)
			{
				$this->_related_users = $this->_getRelatedUsers();
			}
			return $this->_related_users;
		}

		/**
		 * Return a list of users which have some kind of connection to this issue
		 *
		 * @return array
		 */
		public function getRelatedUsers()
		{
			$users = array();
			if (count($this->getRelatedUIDs()) > 0)
			{
				if ($res = B2DB::getTable('TBGUsersTable')->getByUserIDs($this->getRelatedUIDs()))
				{
					while ($row = $res->getNextRow())
					{
						try
						{
							$user = TBGFactory::userLab($row->get(TBGUsersTable::ID), $row);
							$users[$user->getID()] = $user;
						}
						catch (Exception $e) { }
					}
				}
			}

			return $users;
		}
		
		/**
		 * Retrieves all users related to this issue:
		 * 		- Users subscribing to this issue
		 * 		- Issue poster
		 * 		- Issue owner
		 * 		- Issue assignee
		 * 		- Project + edition owner
		 * 		- Project + edition qa
		 * 		- Project + edition + component developers
		 * 
		 * @return array
		 */
		protected function _getRelatedUsers()
		{
			$uids = array();
	
			// Add all users who's marked this issue as interesting
			$uids = array_merge($uids, B2DB::getTable('TBGUserIssuesTable')->getUserIDsByIssueID($this->getID()));
	
			// Add all users from the team owning the issue if valid
			// or add the owning user if a user owns the issue
			if ($this->getOwnerType() == TBGIdentifiableClass::TYPE_TEAM)
			{
				$uids = array_merge($uids, $this->getOwner()->getMemberIDs());
			}
			elseif ($this->getOwnerType() == TBGIdentifiableClass::TYPE_USER)
			{
				$uids[] = $this->getOwner()->getID();
			}

			// Add the poster
			$uids[] = $this->getPostedByID();

			// Add all users from the team assigned to the issue if valid
			// or add the assigned user if a user is assigned to the issue
			if ($this->getAssigneeType() == TBGIdentifiableClass::TYPE_TEAM)
			{
				$uids = array_merge($uids, $this->getAssignee()->getMemberIDs());
			}
			elseif ($this->getAssigneeType() == TBGIdentifiableClass::TYPE_USER)
			{
				$uids[] = $this->getAssignee()->getID();
			}
			
			// Add all users assigned to a project
			$uids = array_merge($uids, $this->getProject()->getAssignedUserIDs());
			
			// Add all users in the team who leads the project, if valid
			// or add the user who leads the project, if valid
			if ($this->getProject()->getLeaderType() == TBGIdentifiableClass::TYPE_TEAM)
			{
				$uids = array_merge($uids, $this->getProject()->getLeadBy()->getMembers());
			}
			elseif ($this->getProject()->getLeaderType() == TBGIdentifiableClass::TYPE_USER)
			{
				$uids[] = $this->getProject()->getLeaderID();
			}
	
			// Same for QA
			if ($this->getProject()->getQaResponsibleType() == TBGIdentifiableClass::TYPE_TEAM)
			{
				$uids = array_merge($uids, $this->getProject()->getQaResponsible()->getMembers());
			}
			elseif ($this->getProject()->getQaResponsibleType() == TBGIdentifiableClass::TYPE_USER)
			{
				$uids[] = $this->getProject()->getQaResponsibleID();
			}
			
			// Add all users relevant for all affected editions
			foreach ($this->getEditions() as $edition_list)
			{
				if ($edition_list['edition']->getLeaderType() == TBGIdentifiableClass::TYPE_TEAM)
				{
					$uids = array_merge($uids, $edition_list['edition']->getLeader()->getMembers());
				}
				elseif ($edition_list['edition']->getLeaderType() == TBGIdentifiableClass::TYPE_USER)
				{
					$uids[] = $edition_list['edition']->getLeaderID();
				}
				
				if ($edition_list['edition']->getQaResponsibleType() == TBGIdentifiableClass::TYPE_TEAM)
				{
					$uids = array_merge($uids, $edition_list['edition']->getQAgetQaResponsible());
				}
				elseif ($edition_list['edition']->getQaResponsibleType() == TBGIdentifiableClass::TYPE_USER)
				{
					$uids[] = $edition_list['edition']->getQaResponsibleID();
				}
				$uids = array_merge($uids, $edition_list['edition']->getAssigneeIDs());
			}
			
			// Add all users relevant for all affected components
			foreach ($this->getComponents() as $component_list)
			{
				$uids = array_merge($uids, $component_list['component']->getAssigneeIDs());
			}
			
			// Add the user who posted the issue
			$uids[] = $this->getPostedBy()->getID();
			
			return array_unique($uids);
		}
	
		/**
		 * Updates the issue's last_updated time to "now"
		 */
		public function updateTime()
		{
			$this->_addChangedProperty('_last_updated', $_SERVER["REQUEST_TIME"]);
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
		public function addLogEntry($change_type, $text = null, $system = false)
		{
			$uid = ($system) ? 0 : TBGContext::getUser()->getUID();
			TBGLogTable::getTable()->createNew($this->getID(), TBGLogTable::TYPE_ISSUE, $change_type, $text, $uid);
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
		public function addSystemComment($title, $text, $uid)
		{
			if (!TBGSettings::isCommentTrailClean())
			{
				$comment = TBGComment::createNew($title, $text, $uid, $this->getID(), TBGComment::TYPE_ISSUE, 'core', true, true, false);
				TBGEvent::createNew('core', 'issue_comment_posted', $this, array('comment' => $comment))->trigger();
				return $comment;
			}
			return false;
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
				$this->_links = B2DB::getTable('TBGLinksTable')->getByIssueID($this->getID());
			}
		}
	
		/**
		 * Remove a link
		 * 
		 * @param integer $link_id The link ID to remove
		 */
		public function removeLink($link_id)
		{
			if ($res = B2DB::getTable('TBGLinksTable')->removeByIssueIDandLinkID($this->getID(), $link_id))
			{
				if (is_array($this->_links) && array_key_exists($link_id, $this->_links))
				{
					unset($this->_links[$link_id]);
				}
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
		
		/**
		 * Populate the files array
		 */
		protected function _populateFiles()
		{
			if ($this->_files === null)
			{
				$this->_files = B2DB::getTable('TBGIssueFilesTable')->getByIssueID($this->getID());
			}
		}
		
		/**
		 * Remove a file
		 * 
		 * @param integer $file_id The file ID
		 * 
		 * @return boolean
		 */
		public function removeFile($file_id)
		{
			if ($res = B2DB::getTable('TBGFilesTable')->removeByIssueIDandFileID($this->getID(), $file_id))
			{
				if (is_array($this->_files) && array_key_exists($file_id, $this->_files))
				{
					unset($this->_files[$file_id]);
				}
				unlink(TBGContext::getIncludePath() . 'files/' . $row->get(TBGFilesTable::FILENAME));
				return true;
			}
			return false;
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
				$this->_comments = TBGComment::getComments($this->getID(), TBGComment::TYPE_ISSUE);
			}
		}
		
		/**
		 * Return the number of comments
		 * 
		 * @return integer
		 */
		public function getCommentCount()
		{
			if ($this->_comments === null)
			{
				return TBGComment::countComments($this->getID(), TBGComment::TYPE_ISSUE);
			}
			else
			{
				return count($this->getComments());
			}
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
		protected function isFieldVisible($fieldname)
		{
			$fields_array = $this->getProject()->getVisibleFieldsArray($this->getIssueType()->getID());
			return array_key_exists($fieldname, $fields_array);
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
			$crit = new B2DBCriteria();
			$crit->addSelectionColumn(TBGLogTable::TIME);
			$crit->addWhere(TBGLogTable::TARGET, $this->_issue_uniqueid);
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
			$crit = new B2DBCriteria();
			$crit->addSelectionColumn(TBGLogTable::TIME);
			$crit->addWhere(TBGLogTable::TARGET, $this->_issue_uniqueid);
			$crit->addWhere(TBGLogTable::TARGET_TYPE, 1);
			$crit->addWhere(TBGLogTable::CHANGE_TYPE, 22);
			$crit->addOrderBy(TBGLogTable::TIME, 'desc');
			$res = TBGLogTable::getTable()->doSelect($crit);
			
			$ret_arr = array();

			if ($res->getNumberOfRows() == 0)
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
						$option_object = null;
						try
						{
							$option_object = TBGFactory::editionLab($this->getCustomField($key));
						}
						catch (Exception $e) {}
						$option_id = ($option_object instanceof TBGEdition) ? $option_object->getID() : null;
						TBGIssueCustomFieldsTable::getTable()->saveIssueCustomFieldValue($option_id, $customdatatype->getID(), $this->getID());
						break;
					default:
						$option_id = ($this->getCustomField($key) instanceof TBGCustomDatatypeOption) ? $this->getCustomField($key)->getID() : null;
						TBGIssueCustomFieldsTable::getTable()->saveIssueCustomFieldValue($option_id, $customdatatype->getID(), $this->getID());
						break;
				}
			}
		}
		
		/**
		 * Save changes made to the issue since last time
		 * 
		 * @return boolean
		 */
		public function save($notify = true, $new = false)
		{
			$comment_lines = array();
			$related_issues_to_save = array();
			$is_saved_estimated = false;
			$is_saved_spent = false;
			$is_saved_assignee = false;
			$is_saved_owner = false;
			$changed_properties = $this->_getChangedProperties();
			
			if (count($changed_properties) == 0) return false;
			
			foreach ($changed_properties as $property => $value)
			{
				$compare_value = (is_object($this->$property)) ? $this->$property->getID() : $this->$property;
				if ($value['original_value'] != $compare_value)
				{
					switch ($property)
					{
						case '_title':
							$this->addLogEntry(TBGLogTable::LOG_ISSUE_UPDATE, TBGContext::getI18n()->__("Title updated"));
							$comment_lines[] = TBGContext::getI18n()->__("This issue's title has been changed");
							break;
						case '_description':
							$this->addLogEntry(TBGLogTable::LOG_ISSUE_UPDATE, TBGContext::getI18n()->__("Description updated"));
							$comment_lines[] = TBGContext::getI18n()->__("This issue's description has been changed");
							break;
						case '_reproduction_steps':
							$this->addLogEntry(TBGLogTable::LOG_ISSUE_REPRODUCABILITY, TBGContext::getI18n()->__("Reproduction steps updated"));
							$comment_lines[] = TBGContext::getI18n()->__("This issue's reproduction steps has been changed");
							break;
						case '_category':
							if ($value['original_value'] != 0)
							{
								$old_name = ($old_item = TBGFactory::TBGCategoryLab($value['original_value'])) ? $old_item->getName() : TBGContext::getI18n()->__('Not determined');
							}
							else
							{
								$old_name = TBGContext::getI18n()->__('Not determined');
							}
							$new_name = ($this->getCategory() instanceof TBGDatatype) ? $this->getCategory()->getName() : TBGContext::getI18n()->__('Not determined');
							
							$this->addLogEntry(TBGLogTable::LOG_ISSUE_CATEGORY, $old_name . ' &rArr; ' . $new_name);
							$comment_lines[] = TBGContext::getI18n()->__("The category has been updated, from '''%previous_category%''' to '''%new_category%'''.", array('%previous_category%' => $old_name, '%new_category%' => $new_name));
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
							$comment_lines[] = TBGContext::getI18n()->__("The triaging criteria 'bug type' has been updated, from '''%previous_name%''' to '''%new_name%'''.", array('%previous_name%' => $old_name, '%new_name%' => $new_name));
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
							$comment_lines[] = TBGContext::getI18n()->__("The triaging criteria 'effect' has been updated, from '''%previous_name%''' to '''%new_name%'''.", array('%previous_name%' => $old_name, '%new_name%' => $new_name));
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
							$comment_lines[] = TBGContext::getI18n()->__("The triaging criteria 'likelihood' has been updated, from '''%previous_name%''' to '''%new_name%'''.", array('%previous_name%' => $old_name, '%new_name%' => $new_name));
							break;
						case '_user_pain':
							$this->addLogEntry(TBGLogTable::LOG_ISSUE_PAIN_CALCULATED, $value['original_value'] . ' &rArr; ' . $value['current_value']);
							$comment_lines[] = TBGContext::getI18n()->__("The calculated user pain has changed, from '''%previous_value%''' to '''%new_value%'''.", array('%previous_value%' => $value['original_value'], '%new_value%' => $value['current_value']));
							break;
						case '_status':
							if ($value['original_value'] != 0)
							{
								$old_name = ($old_item = TBGFactory::TBGStatusLab($value['original_value'])) ? $old_item->getName() : TBGContext::getI18n()->__('Unknown');
							}
							else
							{
								$old_name = TBGContext::getI18n()->__('Not determined');
							}
							$new_name = ($this->getStatus() instanceof TBGDatatype) ? $this->getStatus()->getName() : TBGContext::getI18n()->__('Not determined');
							
							$this->addLogEntry(TBGLogTable::LOG_ISSUE_STATUS, $old_name . ' &rArr; ' . $new_name);
							$comment_lines[] = TBGContext::getI18n()->__("The status has been updated, from '''%previous_status%''' to '''%new_status%'''.", array('%previous_status%' => $old_name, '%new_status%' => $new_name));
							break;
						case '_reproducability':
							if ($value['original_value'] != 0)
							{
								$old_name = ($old_item = TBGFactory::TBGReproducabilityLab($value['original_value'])) ? $old_item->getName() : TBGContext::getI18n()->__('Unknown');
							}
							else
							{
								$old_name = TBGContext::getI18n()->__('Not determined');
							}
							$new_name = ($this->getReproducability() instanceof TBGDatatype) ? $this->getReproducability()->getName() : TBGContext::getI18n()->__('Not determined');
							
							$this->addLogEntry(TBGLogTable::LOG_ISSUE_REPRODUCABILITY, $old_name . ' &rArr; ' . $new_name);
							$comment_lines[] = TBGContext::getI18n()->__("The reproducability has been updated, from '''%previous_reproducability%''' to '''%new_reproducability%'''.", array('%previous_reproducability%' => $old_name, '%new_reproducability%' => $new_name));
							
							break;
						case '_priority':
							if ($value['original_value'] != 0)
							{
								$old_name = ($old_item = TBGFactory::TBGPriorityLab($value['original_value'])) ? $old_item->getName() : TBGContext::getI18n()->__('Unknown');
							}
							else
							{
								$old_name = TBGContext::getI18n()->__('Not determined');
							}
							$new_name = ($this->getPriority() instanceof TBGDatatype) ? $this->getPriority()->getName() : TBGContext::getI18n()->__('Not determined');
							
							$this->addLogEntry(TBGLogTable::LOG_ISSUE_PRIORITY, $old_name . ' &rArr; ' . $new_name);
							$comment_lines[] = TBGContext::getI18n()->__("The priority has been updated, from '''%previous_priority%''' to '''%new_priority%'''.", array('%previous_priority%' => $old_name, '%new_priority%' => $new_name));
							break;
						case '_assignedto':
						case '_assignedtype':
							if (!$is_saved_assignee)
							{
								if ($value['original_value'] != 0)
								{
									if ($this->getChangedPropertyOriginal('_assignedtype') == TBGIdentifiableClass::TYPE_USER)
										$old_identifiable = TBGFactory::userLab($value['original_value']);
									elseif ($this->getChangedPropertyOriginal('_assignedtype') == TBGIdentifiableClass::TYPE_TEAM)
										$old_identifiable = TBGFactory::teamLab($value['original_value']);
									$old_name = ($old_identifiable instanceof TBGIdentifiableClass) ? $old_identifiable->getName() : TBGContext::getI18n()->__('Unknown');
								}
								else
								{
									$old_name = TBGContext::getI18n()->__('Not assigned');
								}
								$new_name = ($this->getAssignee() instanceof TBGIdentifiableClass) ? $this->getAssignee()->getName() : TBGContext::getI18n()->__('Not assigned');
								
								$this->addLogEntry(TBGLogTable::LOG_ISSUE_ASSIGNED, $old_name . ' &rArr; ' . $new_name);
								$comment_lines[] = TBGContext::getI18n()->__("The assignee has been changed, from '''%previous_name%''' to '''%new_name%'''.", array('%previous_name%' => $old_name, '%new_name%' => $new_name));
								$is_saved_assignee = true;
							}
							break;
						case '_postedby':
							$old_identifiable = TBGFactory::userLab($value['original_value']);
							$old_name = ($old_identifiable instanceof TBGIdentifiableClass) ? $old_identifiable->getName() : TBGContext::getI18n()->__('Unknown');
							$new_name = $this->getPostedBy()->getName();
							
							$this->addLogEntry(TBGLogTable::LOG_ISSUE_POSTED, $old_name . ' &rArr; ' . $new_name);
							$comment_lines[] = TBGContext::getI18n()->__("The issue's poster has been changed, from '''%previous_name%''' to '''%new_name%'''.", array('%previous_name%' => $old_name, '%new_name%' => $new_name));
							break;
						case '_being_worked_on_by':
							if ($value['original_value'] != 0)
							{
								$old_identifiable = TBGFactory::userLab($value['original_value']);
								$old_name = ($old_identifiable instanceof TBGIdentifiableClass) ? $old_identifiable->getName() : TBGContext::getI18n()->__('Unknown');
							}
							else
							{
								$old_name = TBGContext::getI18n()->__('Not being worked on');
							}
							$new_name = ($this->getUserWorkingOnIssue() instanceof TBGIdentifiableClass) ? $this->getUserWorkingOnIssue()->getName() : TBGContext::getI18n()->__('Not being worked on');

							$this->addLogEntry(TBGLogTable::LOG_ISSUE_USERS, $old_name . ' &rArr; ' . $new_name);
							$comment_lines[] = TBGContext::getI18n()->__("Information about the user working on this issue has been changed, from '''%previous_name%''' to '''%new_name%'''.", array('%previous_name%' => $old_name, '%new_name%' => $new_name));
							break;
						case '_ownedby':
						case '_ownedtype':
							if (!$is_saved_owner)
							{
								if ($value['original_value'] != 0)
								{
									if ($this->getChangedPropertyOriginal('_ownedtype') == TBGIdentifiableClass::TYPE_USER)
										$old_identifiable = TBGFactory::userLab($value['original_value']);
									elseif ($this->getChangedPropertyOriginal('_ownedtype') == TBGIdentifiableClass::TYPE_TEAM)
										$old_identifiable = TBGFactory::teamLab($value['original_value']);
									$old_name = ($old_identifiable instanceof TBGIdentifiableClass) ? $old_identifiable->getName() : TBGContext::getI18n()->__('Unknown');
								}
								else
								{
									$old_name = TBGContext::getI18n()->__('Not owned by anyone');
								}
								$new_name = ($this->getOwner() instanceof TBGIdentifiableClass) ? $this->getOwner()->getName() : TBGContext::getI18n()->__('Not owned by anyone');
								
								$this->addLogEntry(TBGLogTable::LOG_ISSUE_OWNED, $old_name . ' &rArr; ' . $new_name);
								$comment_lines[] = TBGContext::getI18n()->__("The owner has been changed, from '''%previous_name%''' to '''%new_name%'''.", array('%previous_name%' => $old_name, '%new_name%' => $new_name));
								$is_saved_owner = true;
							}
							break;
						case '_percentcompleted':
							$this->addLogEntry(TBGLogTable::LOG_ISSUE_PERCENT, $value['original_value'] . '% &rArr; ' . $this->getPercentCompleted() . '%');
							$comment_lines[] = TBGContext::getI18n()->__("This issue's progression has been updated to %percent_completed% percent completed.", array('%percent_completed%' => $this->getPercentCompleted()));
							break;
						case '_resolution':
							if ($value['original_value'] != 0)
							{
								$old_name = ($old_item = TBGFactory::TBGResolutionLab($value['original_value'])) ? $old_item->getName() : TBGContext::getI18n()->__('Unknown');
							}
							else
							{
								$old_name = TBGContext::getI18n()->__('Not determined');
							}
							$new_name = ($this->getResolution() instanceof TBGDatatype) ? $this->getResolution()->getName() : TBGContext::getI18n()->__('Not determined');
							
							$this->addLogEntry(TBGLogTable::LOG_ISSUE_RESOLUTION, $old_name . ' &rArr; ' . $new_name);
							$comment_lines[] = TBGContext::getI18n()->__("The resolution has been updated, from '''%previous_resolution%''' to '''%new_resolution%'''.", array('%previous_resolution%' => $old_name, '%new_resolution%' => $new_name));
							break;
						case '_severity':
							if ($value['original_value'] != 0)
							{
								$old_name = ($old_item = TBGFactory::TBGSeverityLab($value['original_value'])) ? $old_item->getName() : TBGContext::getI18n()->__('Unknown');
							}
							else
							{
								$old_name = TBGContext::getI18n()->__('Not determined');
							}
							$new_name = ($this->getSeverity() instanceof TBGDatatype) ? $this->getSeverity()->getName() : TBGContext::getI18n()->__('Not determined');
							
							$this->addLogEntry(TBGLogTable::LOG_ISSUE_SEVERITY, $old_name . ' &rArr; ' . $new_name);
							$comment_lines[] = TBGContext::getI18n()->__("The severity has been updated, from '''%previous_severity%''' to '''%new_severity%'''.", array('%previous_severity%' => $old_name, '%new_severity%' => $new_name));
							break;
						case '_milestone':
							if ($value['original_value'] != 0)
							{
								$old_name = ($old_item = TBGFactory::TBGMilestoneLab($value['original_value'])) ? $old_item->getName() : TBGContext::getI18n()->__('Not determined');
							}
							else
							{
								$old_name = TBGContext::getI18n()->__('Not determined');
							}
							$new_name = ($this->getMilestone() instanceof TBGMilestone) ? $this->getMilestone()->getName() : TBGContext::getI18n()->__('Not determined');
							
							$this->addLogEntry(TBGLogTable::LOG_ISSUE_MILESTONE, $old_name . ' &rArr; ' . $new_name);
							$comment_lines[] = TBGContext::getI18n()->__("The milestone has been updated, from '''%previous_milestone%''' to '''%new_milestone%'''.", array('%previous_milestone%' => $old_name, '%new_milestone%' => $new_name));
							break;
						case '_issuetype':
							if ($value['original_value'] != 0)
							{
								$old_name = ($old_item = TBGFactory::TBGIssuetypeLab($value['original_value'])) ? $old_item->getName() : TBGContext::getI18n()->__('Unknown');
							}
							else
							{
								$old_name = TBGContext::getI18n()->__('Unknown');
							}
							$new_name = ($this->getIssuetype() instanceof TBGIssuetype) ? $this->getIssuetype()->getName() : TBGContext::getI18n()->__('Unknown');
							
							$this->addLogEntry(TBGLogTable::LOG_ISSUE_ISSUETYPE, $old_name . ' &rArr; ' . $new_name);
							$comment_lines[] = TBGContext::getI18n()->__("The issue type has been updated, from '''%previous_type%''' to '''%new_type%'''.", array('%previous_type%' => $old_name, '%new_type%' => $new_name));
							break;
						case '_estimatedmonths':
						case '_estimatedweeks':
						case '_estimateddays':
						case '_estimatedhours':
						case '_estimatedpoints':
							if (!$is_saved_estimated)
							{
								$old_time = array('months' => $this->getChangedPropertyOriginal('_estimatedmonths'),
													'weeks' => $this->getChangedPropertyOriginal('_estimatedweeks'),
													'days' => $this->getChangedPropertyOriginal('_estimateddays'),
													'hours' => $this->getChangedPropertyOriginal('_estimatedhours'),
													'points' => $this->getChangedPropertyOriginal('_estimatedpoints'));

								$old_formatted_time = (array_sum($old_time) > 0) ? $this->getFormattedTime($old_time) : TBGContext::getI18n()->__('Not estimated');
								$new_formatted_time = ($this->hasEstimatedTime()) ? $this->getFormattedTime($this->getEstimatedTime()) : TBGContext::getI18n()->__('Not estimated');
								$this->addLogEntry(TBGLogTable::LOG_ISSUE_TIME_ESTIMATED, $old_formatted_time . ' &rArr; ' . $new_formatted_time);
								$comment_lines[] = TBGContext::getI18n()->__("The issue has been (re-)estimated, from '''%previous_time%''' to '''%new_time%'''.", array('%previous_time%' => $old_formatted_time, '%new_time%' => $new_formatted_time));
								$is_saved_estimated = true;
							}
							break;
						case '_spentmonths':
						case '_spentweeks':
						case '_spentdays':
						case '_spenthours':
						case '_spentpoints':
							if (!$is_saved_spent)
							{
								$old_time = array('months' => $this->getChangedPropertyOriginal('_spentmonths'),
													'weeks' => $this->getChangedPropertyOriginal('_spentweeks'),
													'days' => $this->getChangedPropertyOriginal('_spentdays'),
													'hours' => $this->getChangedPropertyOriginal('_spenthours'),
													'points' => $this->getChangedPropertyOriginal('_spentpoints'));

								$old_formatted_time = (array_sum($old_time) > 0) ? $this->getFormattedTime($old_time) : TBGContext::getI18n()->__('No time spent');
								$new_formatted_time = ($this->hasSpentTime()) ? $this->getFormattedTime($this->getSpentTime()) : TBGContext::getI18n()->__('No time spent');
								$this->addLogEntry(TBGLogTable::LOG_ISSUE_TIME_SPENT, $old_formatted_time . ' &rArr; ' . $new_formatted_time);
								$comment_lines[] = TBGContext::getI18n()->__("Time spent on this issue, from '''%previous_time%''' to '''%new_time%'''.", array('%previous_time%' => $old_formatted_time, '%new_time%' => $new_formatted_time));
								$is_saved_spent = true;
							}
							break;
						case '_state':
							if ($this->isClosed())
							{
								$this->addLogEntry(TBGLogTable::LOG_ISSUE_CLOSE);
								$comment_lines[] = TBGContext::getI18n()->__("This issue has been closed");
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
								$comment_lines[] = TBGContext::getI18n()->__("This issue has been reopened");
							}
							break;
						case '_blocking':
							if ($this->isBlocking())
							{
								$this->addLogEntry(TBGLogTable::LOG_ISSUE_BLOCKED);
								$comment_lines[] = TBGContext::getI18n()->__("This issue is now blocking the next release");
							}
							else
							{
								$this->addLogEntry(TBGLogTable::LOG_ISSUE_UNBLOCKED);
								$comment_lines[] = TBGContext::getI18n()->__("This issue is no longer blocking the next release");
							}
							break;
						default:
							if (substr($property, 0, 12) == '_customfield')
							{
								$key = substr($property, 12);
								$customdatatype = TBGCustomDatatype::getByKey($key);
								
								switch ($customdatatype->getType())
								{
									case TBGCustomDatatype::INPUT_TEXT:
										$new_value = ($this->getCustomField($key) != '') ? $this->getCustomField($key) : TBGContext::getI18n()->__('Unknown');
										$this->addLogEntry(TBGLogTable::LOG_ISSUE_CUSTOMFIELD_CHANGED, $new_value);
										$comment_lines[] = TBGContext::getI18n()->__("The custom field %customfield_name% has been changed to '''%new_value%'''.", array('%customfield_name%' => $customdatatype->getDescription(), '%new_value%' => $new_value));
										break;
									case TBGCustomDatatype::INPUT_TEXTAREA_SMALL:
									case TBGCustomDatatype::INPUT_TEXTAREA_MAIN:
										$new_value = ($this->getCustomField($key) != '') ? $this->getCustomField($key) : TBGContext::getI18n()->__('Unknown');
										$this->addLogEntry(TBGLogTable::LOG_ISSUE_CUSTOMFIELD_CHANGED, $new_value);
										$comment_lines[] = TBGContext::getI18n()->__("The custom field %customfield_name% has been changed.", array('%customfield_name%' => $customdatatype->getDescription()));
										break;
									case TBGCustomDatatype::EDITIONS_CHOICE:
										$old_object = null;
										$new_object = null;
										try
										{
											$old_object = TBGFactory::editionLab($value['original_value']);
										}
										catch (Exception $e) {}
										try
										{
											$new_object = TBGFactory::editionLab($this->getCustomField($key));
										}
										catch (Exception $e) {}
										$old_value = ($old_object instanceof TBGEdition) ? $old_object->getName() : TBGContext::getI18n()->__('Unknown');
										$new_value = ($new_object instanceof TBGEdition) ? $new_object->getName() : TBGContext::getI18n()->__('Unknown');
										$this->addLogEntry(TBGLogTable::LOG_ISSUE_CUSTOMFIELD_CHANGED, $old_value . ' &rArr; ' . $new_value);
										$comment_lines[] = TBGContext::getI18n()->__("The custom field %customfield_name% has been updated, from '''%previous_value%''' to '''%new_value%'''.", array('%customfield_name%' => $customdatatype->getDescription(), '%previous_value%' => $old_value, '%new_value%' => $new_value));
										break;
									default:
										$old_value = ($old_item = TBGCustomDatatypeOption::getByValueAndKey($value['original_value'], $key)) ? $old_item->getName() : TBGContext::getI18n()->__('Unknown');
										$new_value = ($this->getCustomField($key) instanceof TBGCustomDatatypeOption) ? $this->getCustomField($key)->getName() : TBGContext::getI18n()->__('Unknown');
										$this->addLogEntry(TBGLogTable::LOG_ISSUE_CUSTOMFIELD_CHANGED, $old_value . ' &rArr; ' . $new_value);
										$comment_lines[] = TBGContext::getI18n()->__("The custom field %customfield_name% has been updated, from '''%previous_value%''' to '''%new_value%'''.", array('%customfield_name%' => $customdatatype->getDescription(), '%previous_value%' => $old_value, '%new_value%' => $new_value));
										break;
								}
							}
							break;
					}
				}
			}

			$comment = TBGContext::getI18n()->__("The issue was updated with the following change(s):%list_of_changes%", array('%list_of_changes%' => "\n* ".join("\n* ", $comment_lines)));
			
			if ($notify)
			{
				$this->addSystemComment(TBGContext::getI18n()->__('Issue updated'), $comment, TBGContext::getUser()->getUID());
			}

			if ($is_saved_estimated)
			{
				B2DB::getTable('TBGIssueEstimates')->saveEstimate($this->getID(), $this->_estimatedmonths, $this->_estimatedweeks, $this->_estimateddays, $this->_estimatedhours, $this->_estimatedpoints);
			}

			if ($is_saved_spent)
			{
				B2DB::getTable('TBGIssueSpentTimes')->saveSpentTime($this->getID(), $this->_spentmonths, $this->_spentweeks, $this->_spentdays, $this->_spenthours, $this->_spentpoints);
			}

			if (!$new)
			{
				$event = TBGEvent::createNew('core', 'TBGIssue::save', $this, array('changed_properties' => $this->_getChangedProperties(), 'comment' => $comment, 'comment_lines' => $comment_lines, 'notify' => $notify, 'updated_by' => TBGContext::getUser()));
				$event->trigger();
			}

			$this->_clearChangedProperties();
			
			$crit = TBGIssuesTable::getTable()->getCriteria();
			$crit->addUpdate(TBGIssuesTable::TITLE, $this->_title);
			$crit->addUpdate(TBGIssuesTable::LAST_UPDATED, $this->_last_updated);
			$crit->addUpdate(TBGIssuesTable::LONG_DESCRIPTION, $this->_description);
			$crit->addUpdate(TBGIssuesTable::REPRODUCTION, $this->_reproduction_steps);
			$crit->addUpdate(TBGIssuesTable::ISSUE_TYPE, (is_object($this->_issuetype)) ? $this->_issuetype->getID() : $this->_issuetype);
			$crit->addUpdate(TBGIssuesTable::RESOLUTION, (is_object($this->_resolution)) ? $this->_resolution->getID() : $this->_resolution);
			$crit->addUpdate(TBGIssuesTable::STATE, $this->_state);
			$crit->addUpdate(TBGIssuesTable::POSTED_BY, (is_object($this->_postedby)) ? $this->_postedby->getID() : $this->_postedby);
			$crit->addUpdate(TBGIssuesTable::OWNED_BY, (is_object($this->_ownedby)) ? $this->_ownedby->getID() : $this->_ownedby);
			$crit->addUpdate(TBGIssuesTable::OWNED_TYPE, $this->_ownedtype);
			$crit->addUpdate(TBGIssuesTable::ASSIGNED_TO, (is_object($this->_assignedto)) ? $this->_assignedto->getID() : $this->_assignedto);
			$crit->addUpdate(TBGIssuesTable::ASSIGNED_TYPE, $this->_assignedtype);
			$crit->addUpdate(TBGIssuesTable::STATUS, (is_object($this->_status)) ? $this->_status->getID() : $this->_status);
			$crit->addUpdate(TBGIssuesTable::PRIORITY, (is_object($this->_priority)) ? $this->_priority->getID() : $this->_priority);
			$crit->addUpdate(TBGIssuesTable::CATEGORY, (is_object($this->_category)) ? $this->_category->getID() : $this->_category);
			$crit->addUpdate(TBGIssuesTable::REPRODUCABILITY, (is_object($this->_reproducability)) ? $this->_reproducability->getID() : $this->_reproducability);
			$crit->addUpdate(TBGIssuesTable::USER_PAIN, $this->_user_pain);
			$crit->addUpdate(TBGIssuesTable::PAIN_BUG_TYPE, $this->_pain_bug_type);
			$crit->addUpdate(TBGIssuesTable::PAIN_LIKELIHOOD, $this->_pain_likelihood);
			$crit->addUpdate(TBGIssuesTable::PAIN_EFFECT, $this->_pain_effect);
			$crit->addUpdate(TBGIssuesTable::ESTIMATED_MONTHS, $this->_estimatedmonths);
			$crit->addUpdate(TBGIssuesTable::ESTIMATED_WEEKS, $this->_estimatedweeks);
			$crit->addUpdate(TBGIssuesTable::ESTIMATED_DAYS, $this->_estimateddays);
			$crit->addUpdate(TBGIssuesTable::ESTIMATED_HOURS, $this->_estimatedhours);
			$crit->addUpdate(TBGIssuesTable::ESTIMATED_POINTS, $this->_estimatedpoints);
			$crit->addUpdate(TBGIssuesTable::SPENT_MONTHS, $this->_spentmonths);
			$crit->addUpdate(TBGIssuesTable::SPENT_WEEKS, $this->_spentweeks);
			$crit->addUpdate(TBGIssuesTable::SPENT_DAYS, $this->_spentdays);
			$crit->addUpdate(TBGIssuesTable::SPENT_HOURS, $this->_spenthours);
			$crit->addUpdate(TBGIssuesTable::SPENT_POINTS, $this->_spentpoints);
			$crit->addUpdate(TBGIssuesTable::SCRUMCOLOR, $this->_scrumcolor);
			$crit->addUpdate(TBGIssuesTable::PERCENT_COMPLETE, $this->_percentcompleted);
			$crit->addUpdate(TBGIssuesTable::DUPLICATE, (is_object($this->_duplicateof)) ? $this->_duplicateof->getID() : (int) $this->_duplicateof);
			$crit->addUpdate(TBGIssuesTable::DELETED, $this->_deleted);
			$crit->addUpdate(TBGIssuesTable::BLOCKING, $this->_blocking);
			$crit->addUpdate(TBGIssuesTable::VOTES, $this->_votes_total);
			$crit->addUpdate(TBGIssuesTable::USER_WORKING_ON, (is_object($this->_being_worked_on_by)) ? $this->_being_worked_on_by->getID() : $this->_being_worked_on_by);
			$crit->addUpdate(TBGIssuesTable::USER_WORKED_ON_SINCE, $this->_being_worked_on_since);
			$crit->addUpdate(TBGIssuesTable::MILESTONE, (is_object($this->_milestone)) ? $this->_milestone->getID() : $this->_milestone);
			$res = TBGIssuesTable::getTable()->doUpdateById($crit, $this->getID());

			$this->_saveCustomFieldValues();
			$this->getProject()->clearRecentActivities();
			
			foreach (array_keys($related_issues_to_save) as $i_id)
			{
				$related_issue = TBGFactory::TBGIssueLab($i_id);
				$related_issue->save();
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
			if (is_numeric($this->_being_worked_on_by))
			{
				try
				{
					$this->_being_worked_on_by = TBGFactory::userLab($this->_being_worked_on_by);
				}
				catch (Exception $e)
				{
					$this->_being_worked_on_by = null;
				}
			}
	
			return $this->_being_worked_on_by;
		}
		
		/**
		 * Clear the user currently working on this issue
		 * 
		 * @return null
		 */
		public function clearUserWorkingOnIssue()
		{
			$this->_addChangedProperty('_being_worked_on_by', null);
			$this->_being_worked_on_since = null;
		}
		
		/**
		 * Register a user as working on the issue
		 * 
		 * @param TBGUser $user
		 */
		public function startWorkingOnIssue($user)
		{
			$this->_addChangedProperty('_being_worked_on_by', $user->getID());
			$this->_being_worked_on_since = $_SERVER['REQUEST_TIME'];
		}
		
		/**
		 * Stop working on the issue, and save time spent
		 * 
		 * @return null
		 */
		public function stopWorkingOnIssue()
		{
			$time_spent = $_SERVER['REQUEST_TIME'] - $this->_being_worked_on_since;
			$user_working_on_it = $this->getUserWorkingOnIssue();
			$this->clearUserWorkingOnIssue();
			if ($time_spent > 0)
			{
				$weeks_spent = 0;
				$days_spent = 0;
				$hours_spent = 0;
				$time_spent = ceil($time_spent / 3600);
				if ($time_spent >= $this->getProject()->getHoursPerDay())
				{
					$days_spent = 1 + (int) floor($time_spent / 24);
					if ($days_spent >= 7)
					{
						$weeks_spent = floor($days_spent / 7);
						$days_spent -= ($weeks_spent * 7);
					}
				}
				$hours_spent = $time_spent - ($days_spent * 24);
				if ($hours_spent < 0) $hours_spent = 0;
				$this->_addChangedProperty('_spenthours', $this->_spenthours + $hours_spent);
				$this->_addChangedProperty('_spentdays', $this->_spentdays + $days_spent);
				$this->_addChangedProperty('_spentweeks', $this->_spentweeks + $weeks_spent);
			}
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
			return $this->_being_worked_on_since;
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
				$offset = $_SERVER['REQUEST_TIME'] - $this->getPosted();
				$user_pain += floor($offset / 60 / 60 / 24) * 0.1;
			}
			return $user_pain;
		}

		public function getUserPain($real = false)
		{
			return ($real) ? $this->getRealUserPain() : $this->_calculateDatePain();
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

	}
