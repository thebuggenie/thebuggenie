<?php

    namespace thebuggenie\core\entities;

    use thebuggenie\core\framework,
        thebuggenie\core\entities\common\Changeable,
        thebuggenie\core\helpers\Attachable,
        thebuggenie\core\helpers\MentionableProvider;

    /**
     * Issue class
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage main
     */

    /**
     * Issue class
     *
     * @package thebuggenie
     * @subpackage main
     *
     * @method boolean isTitleChanged() Whether the title is changed or not
     * @method boolean isSpentTimeChanged() Whether the spent_time is changed or not
     *
     * @Table(name="\thebuggenie\core\entities\tables\Issues")
     */
    class Issue extends Changeable implements MentionableProvider, Attachable
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
         * @Column(type="string", name="shortname", length=255)
         */
        protected $_shortname;

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
         * @var \thebuggenie\core\entities\Issuetype
         * @Column(type="integer", length=10)
         * @Relates(class="\thebuggenie\core\entities\Issuetype")
         */
        protected $_issuetype;

        /**
         * The project which this issue affects
         *
         * @var \thebuggenie\core\entities\Project
         * @access protected
         * @Column(type="integer", length=10)
         * @Relates(class="\thebuggenie\core\entities\Project")
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
         * The syntax used for this issue's long description
         *
         * @var integer
         * @Column(type="integer", length=2, default=1)
         */
        protected $_description_syntax;

        /**
         * This issues reproduction steps
         *
         * @var string
         * @Column(type="text")
         */
        protected $_reproduction_steps;

        /**
         * The syntax used for this issue's reproduction steps
         *
         * @var integer
         * @Column(type="integer", length=2, default=1)
         */
        protected $_reproduction_steps_syntax;

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
         * @var \thebuggenie\core\entities\User
         * @Column(type="integer", length=10)
         * @Relates(class="\thebuggenie\core\entities\User")
         */
        protected $_posted_by;

        /**
         * The project assignee if team
         *
         * @var \thebuggenie\core\entities\Team
         * @Column(type="integer", length=10)
         * @Relates(class="\thebuggenie\core\entities\Team")
         */
        protected $_assignee_team;

        /**
         * The project assignee if user
         *
         * @var \thebuggenie\core\entities\User
         * @Column(type="integer", length=10)
         * @Relates(class="\thebuggenie\core\entities\User")
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
         * @var \thebuggenie\core\entities\Resolution
         * @Column(type="integer", length=10)
         * @Relates(class="\thebuggenie\core\entities\Resolution")
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
         * @var \thebuggenie\core\entities\Category
         * @Column(type="integer", length=10)
         * @Relates(class="\thebuggenie\core\entities\Category")
         */
        protected $_category;

        /**
         * The status
         *
         * @var \thebuggenie\core\entities\Status
         * @Column(type="integer", length=10)
         * @Relates(class="\thebuggenie\core\entities\Status")
         */
        protected $_status;

        /**
         * The prioroty
         *
         * @var \thebuggenie\core\entities\Priority
         * @Column(type="integer", length=10)
         * @Relates(class="\thebuggenie\core\entities\Priority")
         */
        protected $_priority;

        /**
         * The reproducability
         *
         * @var \thebuggenie\core\entities\Reproducability
         * @Column(type="integer", length=10)
         * @Relates(class="\thebuggenie\core\entities\Reproducability")
         */
        protected $_reproducability;

        /**
         * The severity
         *
         * @var \thebuggenie\core\entities\Severity
         * @Column(type="integer", length=10)
         * @Relates(class="\thebuggenie\core\entities\Severity")
         */
        protected $_severity;

        /**
         * The scrum color
         *
         * @var string
         * @Column(type="string", length=7, default="#FFFFFF")
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
         * @var \thebuggenie\core\entities\User
         * @Column(type="integer", length=10)
         * @Relates(class="\thebuggenie\core\entities\User")
         */
        protected $_being_worked_on_by_user;

        /**
         * When the last user started working on the issue
         *
         * @var integer
         * @Column(type="integer", length=10)
         * @Relates(class="\thebuggenie\core\entities\User")
         */
        protected $_being_worked_on_by_user_since;

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
         * Milestone sorting order for this issue
         *
         * @var integer
         * @Column(type="integer", length=10)
         */
        protected $_milestone_order = null;

        /**
         * The issue this issue is a duplicate of
         *
         * @var \thebuggenie\core\entities\Issue
         * @Column(type="integer", length=10)
         * @Relates(class="\thebuggenie\core\entities\Issue")
         */
        protected $_duplicate_of;

        /**
         * The milestone this issue is assigned to
         *
         * @var \thebuggenie\core\entities\Milestone
         * @Column(type="integer", length=10)
         * @Relates(class="\thebuggenie\core\entities\Milestone")
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
         * @var array|\thebuggenie\core\entities\Issue
         * @Relates(class="\thebuggenie\core\entities\Issue", collection=true, foreign_column="duplicate_of")
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
         * @var \thebuggenie\core\entities\WorkflowStep
         * @Column(type="integer", length=10)
         * @Relates(class="\thebuggenie\core\entities\WorkflowStep")
         */
        protected $_workflow_step_id;

        /**
         * An array of \thebuggenie\core\entities\Comments
         *
         * @var array
         * @Relates(class="\thebuggenie\core\entities\Comment", collection=true, foreign_column="target_id")
         */
        protected $_comments;

        /**
         * An array of \thebuggenie\core\entities\IssueSpentTimes
         *
         * @var array
         * @Relates(class="\thebuggenie\core\entities\IssueSpentTime", collection=true, foreign_column="issue_id")
         */
        protected $_spent_times;

        protected $_num_comments;

        protected $_num_user_comments;

        protected $_custom_populated = false;

        protected $_log_items_added = array();

        protected $_save_comment = '';

        protected $_can_permission_cache = array();

        protected $_editable;

        protected $_updateable;

        /**
         * Array of users that are subscribed to this issue
         *
         * @var array
         * @Relates(class="\thebuggenie\core\entities\User", collection=true, manytomany=true, joinclass="\thebuggenie\core\entities\tables\UserIssues")
         */
        protected $_subscribers = null;

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
            return tables\Issues::getTable()->getCountsByProjectID($project_id);
        }

        public static function getPainTypesOrLabel($type, $id = null)
        {
            $i18n = framework\Context::getI18n();

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
            $effects[2] = $i18n->__("A Pain - users won't like this once they notice it / a moderate number of users won't buy");
            $effects[1] = $i18n->__('Nuisance - not a big deal but noticeable / extremely unlikely to affect sales');

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
            return tables\Issues::getTable()->getCountsByProjectIDandIssuetype($project_id, $issuetype_id);
        }

        /**
         * Count the number of open and closed issues for a specific project id
         * and milestone id
         *
         * @param integer $project_id The project ID
         * @param integer $milestone_id The milestone ID
         *
         * @return array
         */
        public static function getIssueCountsByProjectIDandMilestone($project_id, $milestone_id)
        {
            return tables\Issues::getTable()->getCountsByProjectIDandMilestone($project_id, $milestone_id);
        }

        /**
         * Returns a \thebuggenie\core\entities\Issue from an issue no
         *
         * @param string $issue_number An integer or issue number
         *
         * @return \thebuggenie\core\entities\Issue
         */
        public static function getIssueFromLink($issue_number)
        {
            $project = framework\Context::getCurrentProject();
            $found_issue = null;
            $issue_no = self::extractIssueNoFromNumber($issue_number);
            if (is_numeric($issue_no))
            {
                try
                {
                    if (!$project instanceof \thebuggenie\core\entities\Project) return null;
                    if ($project->usePrefix()) return null;
                    $found_issue = tables\Issues::getTable()->getByProjectIDAndIssueNo($project->getID(), (integer) $issue_no);
                }
                catch (\Exception $e)
                {
                    throw $e;
                }
            }
            else
            {
                $issue_no = explode('-', mb_strtoupper($issue_no));
                \thebuggenie\core\framework\Logging::log('exploding');
                if (count($issue_no) == 2 && ($found_issue = tables\Issues::getTable()->getByPrefixAndIssueNo($issue_no[0], $issue_no[1])) instanceof \thebuggenie\core\entities\Issue)
                {
                    if (!$found_issue->getProject()->usePrefix()) return null;
                }
                \thebuggenie\core\framework\Logging::log('exploding done');
            }

            return ($found_issue instanceof \thebuggenie\core\entities\Issue) ? $found_issue : null;
        }

        /**
         * Extract issue no from issue integer or string with prefix '#'.
         *
         * @param string $issue_number An integer or issue number
         *
         * @return string
         */
        public static function extractIssueNoFromNumber($issue_number)
        {
            $issue_no = mb_strtolower(trim($issue_number));
            if (mb_strpos($issue_no, ' ') !== false)
            {
                $issue_no = mb_substr($issue_no, strrpos($issue_no, ' ') + 1);
            }
            if (mb_substr($issue_no, 0, 1) == '#') $issue_no = mb_substr($issue_no, 1);

            return $issue_no;
        }

        public static function findIssues($filters = array(), $results_per_page = 30, $offset = 0, $groupby = null, $grouporder = null, $sortfields = array(tables\Issues::LAST_UPDATED => 'asc'))
        {
            $issues = array();
            list ($rows, $count, $ids) = tables\Issues::getTable()->findIssues($filters, $results_per_page, $offset, $groupby, $grouporder, $sortfields);
            if ($rows)
            {
                if (framework\Context::isProjectContext())
                {
                    framework\Context::getCurrentProject()->preloadValues();
                }
                tables\IssueCustomFields::getTable()->preloadValuesByIssueIDs($ids);
                tables\IssueAffectsBuild::getTable()->preloadValuesByIssueIDs($ids);
                tables\IssueAffectsEdition::getTable()->preloadValuesByIssueIDs($ids);
                tables\IssueAffectsComponent::getTable()->preloadValuesByIssueIDs($ids);
                tables\Comments::getTable()->preloadIssueCommentCounts($ids);
                tables\IssueFiles::getTable()->preloadIssueFileCounts($ids);
                $user_ids = array();
                foreach ($rows as $key => $row)
                {
                    try
                    {
                        $issue = new Issue($row->get(tables\Issues::ID), $row);
                        $user_ids[$row['issues.posted_by']] = true;
                        $issues[] = $issue;
                        unset($rows[$key]);
                    }
                    catch (\Exception $e) {}
                }
                if (count($user_ids))
                {
                    tables\Users::getTable()->preloadUsers(array_keys($user_ids));
                }
                foreach ($issues as $key => $issue)
                {
                    if (!$issue->hasAccess() || $issue->getProject()->isDeleted())
                    {
                        unset($issues[$key]);
                    }
                }
                tables\IssueCustomFields::getTable()->clearPreloadedValues();
                tables\IssueAffectsBuild::getTable()->clearPreloadedValues();
                tables\IssueAffectsEdition::getTable()->clearPreloadedValues();
                tables\IssueAffectsComponent::getTable()->clearPreloadedValues();
                tables\Comments::getTable()->clearPreloadedIssueCommentCounts();
                tables\IssueFiles::getTable()->clearPreloadedIssueFileCounts();
            }
            return array($issues, $count);
        }

        public static function findIssuesByText($text, $project = null)
        {
            $issue = self::getIssueFromLink($text);
            if ($issue instanceof \thebuggenie\core\entities\Issue)
                return array(array($issue), 1);

            $filters = array('text' => SearchFilter::createFilter('text', array('v' => $text, 'o' => '=')));
            if ($project instanceof \thebuggenie\core\entities\Project)
            {
                $filters['project_id'] = SearchFilter::createFilter('project_id', array('v' => $project->getID(), 'o' => '='));
            }
            return self::findIssues($filters);
        }

        /**
         * Runs one or more regular expressions against a supplied text, extracts
         * issue numbers from it, and then obtains corresponding issues. The
         * function will also obtain information about transitions (if this was
         * specified in the text). This data can be used for transitioning the
         * issues through a workflow.
         *
         * Once the function finishes processing, it will return an array of format:
         *
         * array('issues' => tbg_issues, 'transitions' => transitions).
         *
         * tbgissues is an array consisting of \thebuggenie\core\entities\Issue instances.
         *
         * transitions is an array containing transition arrays. The transition
         * arrays are accessed with issue numbers as keys (e.g. 'PREFIX-1',
         * 'PREFIX-5' or '2', '3' etc). Each transition array has the following
         * format:
         *
         * array(0 => command, 1 => parameters)
         *
         * command is a string representing the transision command (for example
         * 'Resolve issue') from the workflow definition. parameters is an array
         * that contains parameters and their values that should be passed to the
         * transition step:
         *
         * array( 'PARAM1' => 'VALUE1', 'PARAM2' => 'VALUE2', ...)
         *
         *
         * @param string $text Text that should be parsed for issue numbers and transitions.
         *
         * @return An array with two elements, one denoting the matched issues, one
         * denoting the transitions for issues. These elements can be accessed using
         * keys 'issues', and 'transitions'. The key 'issues' can be used for
         * accessing an array made-up of \thebuggenie\core\entities\Issue instances. The key 'transitions'
         * can be used for accessing an array containing transition information
         * about each issue. The 'transitions' array uses issue numbers as keys,
         * and contains ordered transition information (see above for detailed
         * description of format).
         */
        public static function getIssuesFromTextByRegex($text)
        {
            $issue_match_regexes = \thebuggenie\core\helpers\TextParser::getIssueRegex();
            $issue_numbers = array(); // Issue numbers
            $issues = array(); // Issue objects
            $transitions = array(); // Transition information

            // Iterate over all regular expressions that should be used for
            // issue/transition matching in commit message.
            foreach ($issue_match_regexes as $issue_match_regex)
            {
                $matched_issue_data = array(); // All data from regexp

                // If any match is found using the current regular expression, extract
                // the information.
                if (preg_match_all($issue_match_regex, $text, $matched_issue_data))
                {

                    // Identified issues are kept inside of named regex group.
                    foreach ($matched_issue_data["issues"] as $key => $issue_number)
                    {
                        // Get the matched transitions for the issue.
                        $matched_issue_transitions = $matched_issue_data["transitions"][$key];

                        // Create an empty array to store transitions for an issue. Don't
                        // overwrite it. Use issue number as key for transitions.
                        if (!array_key_exists($issue_number, $transitions))
                        {
                            $transitions[$issue_number] = array();
                        }

                        // Add the transition information (if any) for an issue.
                        if ($matched_issue_transitions )
                        {
                            // Parse the transition information. Each transition string is in
                            // format:
                            // 'TRANSITION1: PARAM1_1=VALUE1_1 PARAM1_2=VALUE1_2; TRANSITION2: PARAM2_1=VALUE2_1 PARAM2_2=VALUE2_2'
                            foreach (explode("; ", $matched_issue_transitions) as $transition)
                            {
                                // Split command from its parameters.
                                $transition_data = explode(": ", $transition);
                                $transition_command = $transition_data[0];
                                // Set-up array that will contain parameters
                                $transition_parameters = array();

                                // Process parameters if they were present.
                                if (count($transition_data) == 2)
                                {
                                    // Split into induvidual parameters.
                                    foreach (explode(" ", $transition_data[1]) as $parameter)
                                    {
                                        // Only process proper parameters (of format 'PARAM=VALUE')
                                        if (mb_strpos($parameter, '='))
                                        {
                                            list($param_key, $param_value) = explode('=', $parameter);
                                            $transition_parameters[$param_key] = $param_value;
                                        }
                                    }
                                }
                                // Append the transition information for the current issue number.
                                $transitions[$issue_number][] = array($transition_command, $transition_parameters);
                            }
                        }

                        // Add the issue number to the list.
                        $issue_numbers[] = $issue_number;
                    }

                }
            }

            // Make sure that each issue gets procssed only once for a single commit
            // (avoid duplication of commits).
            $unique_issue_numbers = array_unique($issue_numbers);

            // Fetch all issues affected by the commit.
            foreach ($unique_issue_numbers as $issue_no)
            {
                $issue = Issue::getIssueFromLink($issue_no);
                if ($issue instanceof \thebuggenie\core\entities\Issue) $issues[] = $issue;
            }

            // Return array consisting out of two arrays - one with \thebuggenie\core\entities\Issue
            // instances, and the second one with transition information for those
            // issues.
            return array("issues" => $issues, "transitions" => $transitions);
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
            $this->_num_user_comments = tables\Comments::getTable()->getPreloadedIssueCommentCount($this->_id);
            $this->_num_files = tables\IssueFiles::getTable()->getPreloadedIssueFileCount($this->_id);
//            if ($this->isDeleted())
//            {
//                throw new \Exception(framework\Context::geti18n()->__('This issue has been deleted'));
//            }
        }

        /**
         * Print the issue number and title nicely formatted
         *
         * @param boolean $link_formatted [optional] Whether to include the # if it's only numeric (default false)
         *
         * @return string
         */
        public function getFormattedTitle($link_formatted = false, $include_issuetype = true)
        {
            return $this->getFormattedIssueNo($link_formatted, $include_issuetype) . ' - ' . $this->getTitle();
        }

        public function getAccessList()
        {
            $permissions = tables\Permissions::getTable()->getByPermissionTargetIDAndModule('canviewissue', $this->getID());
            return $permissions;
        }

        /**
         * Whether or not the current user can access the issue
         *
         * @return boolean
         */
        public function hasAccess($target_user = null)
        {
            \thebuggenie\core\framework\Logging::log('checking access to issue ' . $this->getFormattedIssueNo());
            $i_id = $this->getID();
            $user = ($target_user === null) ? framework\Context::getUser() : $target_user;
            if (!$user->isGuest() && $user->isAuthenticated())
            {
                $specific_access = $user->hasPermission("canviewissue", $i_id, 'core');
                if ($specific_access !== null)
                {
                    \thebuggenie\core\framework\Logging::log('done checking, returning specific access ' . (($specific_access) ? 'allowed' : 'denied'));
                    return $specific_access;
                }
                if ($this->getPostedByID() == $user->getID())
                {
                    \thebuggenie\core\framework\Logging::log('done checking, allowed since this user posted it');
                    return true;
                }
                if ($this->getOwner() instanceof \thebuggenie\core\entities\User && $this->getOwner()->getID() == $user->getID())
                {
                    \thebuggenie\core\framework\Logging::log('done checking, allowed since this user owns it');
                    return true;
                }
                if ($this->getAssignee() instanceof \thebuggenie\core\entities\User && $this->getAssignee()->getID() == $user->getID())
                {
                    \thebuggenie\core\framework\Logging::log('done checking, allowed since this user is assigned to it');
                    return true;
                }
                if ($user->hasPermission('canseegroupissues', 0, 'core') &&
                    $this->getPostedBy() instanceof \thebuggenie\core\entities\User &&
                    $this->getPostedBy()->getGroupID() == $user->getGroupID())
                {
                    \thebuggenie\core\framework\Logging::log('done checking, allowed since this user is in same group as user that posted it');
                    return true;
                }
                if ($user->hasPermission('canseeallissues', 0, 'core') === false)
                {
                    \thebuggenie\core\framework\Logging::log('done checking, not allowed to access issues not posted by themselves');
                    return false;
                }
            }
            if ($this->getCategory() instanceof \thebuggenie\core\entities\Category)
            {
                if (!$this->getCategory()->hasAccess())
                {
                    \thebuggenie\core\framework\Logging::log('done checking, not allowed to access issues in this category');
                    return false;
                }
            }
            if ($this->getProject()->hasAccess())
            {
                \thebuggenie\core\framework\Logging::log('done checking, can access project');
                return true;
            }
            \thebuggenie\core\framework\Logging::log('done checking, denied');
            return false;
        }

        public function setProject($project)
        {
            $this->_project_id = $project;
        }

        /**
         * Returns the project for this issue
         *
         * @return \thebuggenie\core\entities\Project
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
            return ($project instanceof \thebuggenie\core\entities\Project) ? $project->getID() : null;
        }

        /**
         * Return the issues current step in the workflow
         *
         * @return \thebuggenie\core\entities\WorkflowStep
         */
        public function getWorkflowStep()
        {
            return $this->_b2dbLazyload('_workflow_step_id');
        }

        /**
         * Return the current workflow
         * 
         * @return Workflow
         */
        public function getWorkflow()
        {
            return $this->getProject()->getWorkflowScheme()->getWorkflowForIssuetype($this->getIssueType());
        }

        public function setWorkflowStep(\thebuggenie\core\entities\WorkflowStep $step)
        {
            $this->_addChangedProperty('_workflow_step_id', $step->getID());
        }

        /**
         * Returns an array of workflow transitions
         * 
         * @return array|\thebuggenie\core\entities\WorkflowTransition
         */
        public function getAvailableWorkflowTransitions()
        {
            return ($this->getWorkflowStep() instanceof \thebuggenie\core\entities\WorkflowStep) ? $this->getWorkflowStep()->getAvailableTransitionsForIssue($this) : array();
        }

        /**
         * Returns an array of workflow transitions
         *
         * @return array|\thebuggenie\core\entities\WorkflowTransition
         */
        public function getAvailableWorkflowStatusIDsAndTransitions()
        {
            $status_ids = array();
            $transitions = array();
            $available_statuses = Status::getAll();
            $rule_status_valid = false;

            foreach ($this->getAvailableWorkflowTransitions() as $transition)
            {
                if ($transition->getOutgoingStep()->hasLinkedStatus())
                {
                    $status_ids[] = $transition->getOutgoingStep()->getLinkedStatusID();

                    if (! isset($transitions[$transition->getOutgoingStep()->getLinkedStatusID()]))
                        $transitions[$transition->getOutgoingStep()->getLinkedStatusID()] = array();

                    $transitions[$transition->getOutgoingStep()->getLinkedStatusID()][] = $transition;
                }
                elseif ($transition->hasPostValidationRule(WorkflowTransitionValidationRule::RULE_STATUS_VALID))
                {
                    $values = explode(',', $transition->getPostValidationRule(WorkflowTransitionValidationRule::RULE_STATUS_VALID)->getRuleValue());

                    foreach ($values as $value)
                    {
                        if (! array_key_exists($value, $available_statuses)) continue;
                        if (! $rule_status_valid) $rule_status_valid = true;
                        if (! isset($transitions[$value])) $transitions[$value] = array();

                        $transitions[$value][] = $transition;
                        $status_ids[] = $value;
                    }
                }
            }

            return array($status_ids, $transitions, $rule_status_valid);
        }

        /**
         * Get current available statuses
         *
         * @return array|\thebuggenie\core\entities\Status
         */
        public function getAvailableStatuses()
        {
            $statuses = array();
            $available_statuses = Status::getAll();
            foreach ($this->getAvailableWorkflowTransitions() as $transition)
            {
                if ($transition->getOutgoingStep()->hasLinkedStatus())
                {
                    if ($status = $transition->getOutgoingStep()->getLinkedStatus())
                    {
                        $statuses[$status->getID()] = $status;
                    }
                }
                elseif ($transition->hasPostValidationRule(WorkflowTransitionValidationRule::RULE_STATUS_VALID))
                {
                    $values = explode(',', $transition->getPostValidationRule(WorkflowTransitionValidationRule::RULE_STATUS_VALID)->getRuleValue());
                    foreach ($values as $value)
                    {
                        if (array_key_exists($value, $available_statuses))
                        {
                            $statuses[$value] = $available_statuses[$value];
                        }
                    }
                }
            }

            return $statuses;
        }

        protected function _initializeCustomfields()
        {
            foreach (CustomDatatype::getAll() as $key => $customdatatype)
            {
                $var_name = "_customfield".$key;
                $this->$var_name = null;
            }
            if ($rows = tables\IssueCustomFields::getTable()->getAllValuesByIssueID($this->getID()))
            {
                foreach ($rows as $row)
                {
                    $datatype = CustomDatatype::getB2DBTable()->selectById($row->get(tables\IssueCustomFields::CUSTOMFIELDS_ID));
                    if ($datatype instanceof CustomDatatype)
                    {
                        $var_name = "_customfield".$datatype->getKey();

                        if ($datatype->hasCustomOptions())
                        {
                            $option = tables\CustomFieldOptions::getTable()->selectById((int) $row->get(tables\IssueCustomFields::CUSTOMFIELDOPTION_ID));
                            if ($option instanceof \thebuggenie\core\entities\CustomDatatypeOption)
                            {
                                $this->$var_name = $option;
                            }
                        }
                        else if($datatype->hasPredefinedOptions())
                        {
                            $this->$var_name = $row->get(tables\IssueCustomFields::CUSTOMFIELDOPTION_ID);
                        }
                        else
                        {
                            $this->$var_name = $row->get(tables\IssueCustomFields::OPTION_VALUE);
                        }
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

                if ($res = tables\IssueAffectsEdition::getTable()->getByIssueID($this->getID()))
                {
                    foreach($res as $row)
                    {
                        try
                        {
                            $edition = tables\Editions::getTable()->selectById((int) $row->get(tables\IssueAffectsEdition::EDITION), null, null);
                            if ($edition instanceof Edition) {
                                $status_id = $row->get(tables\IssueAffectsEdition::STATUS);
                                $this->_editions[$row->get(tables\IssueAffectsEdition::ID)] = array(
                                    'edition' => $edition,
                                    'status' => ($status_id) ? Status::getB2DBTable()->selectById((int) $status_id) : null,
                                    'confirmed' => (bool) $row->get(tables\IssueAffectsEdition::CONFIRMED),
                                    'a_id' => $row->get(tables\IssueAffectsEdition::ID));
                            }
                        }
                        catch (\Exception $e) {}
                    }
                }

                if ($res = tables\IssueAffectsBuild::getTable()->getByIssueID($this->getID()))
                {
                    foreach($res as $row)
                    {
                        try
                        {
                            $build = tables\Builds::getTable()->selectById((int) $row->get(tables\IssueAffectsBuild::BUILD), null, null);
                            if ($build instanceof Build) {
                                $status_id = $row->get(tables\IssueAffectsBuild::STATUS);
                                $this->_builds[$row->get(tables\IssueAffectsBuild::ID)] = array(
                                    'build' => $build,
                                    'status' => ($status_id) ? Status::getB2DBTable()->selectById((int) $status_id) : null,
                                    'confirmed' => (bool) $row->get(tables\IssueAffectsBuild::CONFIRMED),
                                    'a_id' => $row->get(tables\IssueAffectsBuild::ID));
                            }
                        }
                        catch (\Exception $e) { }
                    }
                }

                if ($res = tables\IssueAffectsComponent::getTable()->getByIssueID($this->getID()))
                {
                    foreach($res as $row)
                    {
                        try
                        {
                            $component = tables\Components::getTable()->selectById((int) $row->get(tables\IssueAffectsComponent::COMPONENT), null, null);
                            if ($component instanceof Component) {
                                $status_id = $row->get(tables\IssueAffectsComponent::STATUS);
                                $this->_components[$row->get(tables\IssueAffectsComponent::ID)] = array(
                                    'component' => $component,
                                    'status' => ($status_id) ? Status::getB2DBTable()->selectById((int) $status_id) : null,
                                    'confirmed' => (bool) $row->get(tables\IssueAffectsComponent::CONFIRMED),
                                    'a_id' => $row->get(tables\IssueAffectsComponent::ID));
                            }
                        }
                        catch (\Exception $e) { }
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
            return ($this->getDuplicateOf() instanceof \thebuggenie\core\entities\Issue) ? true : false;
        }

        /**
         * Mark this issue as a duplicate of another issue
         *
         * @param integer $d_id Issue ID for the duplicated issue
         */
        public function setDuplicateOf($d_id)
        {
            tables\Issues::getTable()->setDuplicate($this->getID(), $d_id);
            if ($d_id)
            {
                tables\UserIssues::getTable()->copyStarrers($this->getID(), $d_id);
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
         * @return \thebuggenie\core\entities\Issue
         */
        public function getDuplicateOf()
        {
            return $this->_b2dbLazyload('_duplicate_of');
        }

        /**
         * Returns an array of all issues which are duplicates of this one
         *
         * @return array|\thebuggenie\core\entities\Issue
         */
        public function getDuplicateIssues()
        {
            $this->_populateDuplicateIssues();
            return $this->_duplicate_issues;
        }

        public function hasDuplicateIssues()
        {
            return (bool) $this->getNumberOfDuplicateIssues();
        }

        public function getNumberOfDuplicateIssues()
        {
            return count($this->getDuplicateIssues());
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
         * @param boolean $locked [optional]
         */
        public function setLocked($locked = true)
        {
            $this->_locked = (bool) $locked;
        }

        public function isEditable()
        {
            if ($this->_editable !== null) return $this->_editable;

            if ($this->getProject()->isArchived()) $this->_editable = false;
            else $this->_editable = ($this->isOpen() && ($this->getProject()->canChangeIssuesWithoutWorkingOnThem() || ($this->getWorkflowStep() instanceof \thebuggenie\core\entities\WorkflowStep && $this->getWorkflowStep()->isEditable())));

            return $this->_editable;
        }

        public function isUpdateable()
        {
            if ($this->_updateable !== null) return $this->_updateable;

            if ($this->getProject()->isArchived()) $this->_updateable = false;
            else $this->_updateable = ($this->isOpen() && ($this->getProject()->canChangeIssuesWithoutWorkingOnThem() || !$this->getWorkflowStep() instanceof \thebuggenie\core\entities\WorkflowStep || !$this->getWorkflowStep()->isClosed()));

            return $this->_updateable;
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
            if (framework\Context::getUser()->isGuest()) return false;
            if (isset($this->_can_permission_cache[$key])) return $this->_can_permission_cache[$key];
            $retval = ($this->isInvolved() && !$exclusive) ? $this->getProject()->permissionCheck($key.'own', true) : null;
            $retval = ($retval !== null) ? $retval : $this->getProject()->permissionCheck($key, !$this->isInvolved());

            $this->_can_permission_cache[$key] = $retval;
            return $retval;
        }

        public function isWorkflowTransitionsAvailable()
        {
            if ($this->getProject()->isArchived()) return false;
            return (bool) $this->_permissionCheck('caneditissue', true);
        }

        public function isInvolved()
        {
            $user_id = framework\Context::getUser()->getID();
            return (bool) ($this->getPostedByID() == $user_id || ($this->isAssigned() && $this->getAssignee()->getID() == $user_id && $this->getAssignee() instanceof \thebuggenie\core\entities\User) || ($this->isOwned() && $this->getOwner()->getID() == $user_id && $this->getOwner() instanceof \thebuggenie\core\entities\User));
        }

        /**
         * Return if the user can edit title
         *
         * @return boolean
         */
        public function canEditAccessPolicy()
        {
            $retval = $this->_permissionCheck('canlockandeditlockedissues', true);
            $retval = ($retval !== null) ? $retval : \thebuggenie\core\framework\Settings::isPermissive();

            return $retval;
        }

        /**
         * Check whether or not this user can edit issue details
         *
         * @return boolean
         */
        public function canEditIssueDetails()
        {
            static $retval = null;
            if ($retval !== null) return $retval;

            $retval = $this->_permissionCheck('caneditissuebasic');
            $retval = ($retval === null) ? ($this->isInvolved() || $this->_permissionCheck('cancreateandeditissues')) : $retval;
            $retval = ($retval === null) ? $this->_permissionCheck('caneditissue', true) : $retval;

            $retval = ($retval !== null) ? $retval : \thebuggenie\core\framework\Settings::isPermissive();

            return $retval;
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

            return ($retval !== null) ? $retval : \thebuggenie\core\framework\Settings::isPermissive();
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

            return ($retval !== null) ? $retval : \thebuggenie\core\framework\Settings::isPermissive();
        }

        /**
         * Return if the user can edit shortname
         *
         * @return boolean
         */
        public function canEditShortname()
        {
            $retval = $this->_permissionCheck('caneditissueshortname');
            $retval = ($retval === null) ? $this->canEditIssueDetails() : $retval;

            return ($retval !== null) ? $retval : \thebuggenie\core\framework\Settings::isPermissive();
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

            return ($retval !== null) ? $retval : \thebuggenie\core\framework\Settings::isPermissive();
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
            if (isset($this->_can_permission_cache[$permission])) return $this->_can_permission_cache[$permission];

            $retval = $this->_permissionCheck($permission);
            $retval = ($retval === null) ? $this->canEditIssue() : $retval;

            if ($retval === null)
            {
                $retval = ($fallback !== null) ? $fallback : \thebuggenie\core\framework\Settings::isPermissive();
            }

            $this->_can_permission_cache[$permission] = $retval;
            return $retval;
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
            static $retval = null;
            if ($retval !== null) return $retval;

            $retval = $this->_permissionCheck('cancloseissues');
            $retval = ($retval === null) ? $this->_permissionCheck('canclosereopenissues') : $retval;
            $retval = ($retval === null) ? $this->canEditIssue() : $retval;
            $retval = ($retval !== null) ? $retval : \thebuggenie\core\framework\Settings::isPermissive();

            return $retval;
        }

        /**
         * Return if the user can close the issue
         *
         * @return boolean
         */
        public function canReopenIssue()
        {
            static $retval = null;
            if ($retval !== null) return $retval;

            $retval = $this->_permissionCheck('canreopenissues');
            $retval = ($retval === null) ? $this->_permissionCheck('canclosereopenissues') : $retval;
            $retval = ($retval === null) ? $this->canEditIssue() : $retval;
            $retval = ($retval !== null) ? $retval : \thebuggenie\core\framework\Settings::isPermissive();

            return $retval;
        }

        protected function _dualPermissionsCheck($permission_1, $permission_2)
        {
            $retval = $this->_permissionCheck($permission_1);
            $retval = ($retval === null) ? $this->_permissionCheck($permission_2) : $retval;

            return ($retval !== null) ? $retval : \thebuggenie\core\framework\Settings::isPermissive();
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
            if (isset($this->_can_permission_cache[$permission])) return $this->_can_permission_cache[$permission];
            $retval = $this->_permissionCheck($permission);
            $retval = ($retval === null) ? $this->canAddExtraInformation() : $retval;

            $this->_can_permission_cache[$permission] = $retval;
            return ($retval !== null) ? $retval : \thebuggenie\core\framework\Settings::isPermissive();
        }

        /**
         * Return if the user can post comments on this issue
         *
         * @return boolean
         */
        public function canPostComments()
        {
            static $retval = null;
            if ($retval !== null) return $retval;

            $retval = $this->_dualPermissionsCheck('canpostcomments', 'canpostandeditcomments');

            return $retval;
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
         * @param boolean $link_formatted [optional] Whether to include the # if it's only numeric (default false)
         *
         * @return string
         */
        public function getFormattedIssueNo($link_formatted = false, $include_issuetype = false)
        {
            try
            {
                $issuetype_description = ($this->getIssueType() instanceof \thebuggenie\core\entities\Issuetype && $include_issuetype) ? $this->getIssueType()->getName().' ' : '';
            }
            catch (\Exception $e)
            {
                $issuetype_description = framework\Context::getI18n()->__('Unknown issuetype') . ' ';
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
         * @return \thebuggenie\core\entities\Issuetype
         */
        public function getIssueType()
        {
            return $this->_b2dbLazyload('_issuetype');
        }

        public function hasIssueType()
        {
            try
            {
                return ($this->getIssueType() instanceof \thebuggenie\core\entities\Issuetype);
            }
            catch (\Exception $e)
            {
                return false;
            }
        }

        /**
         * Set the issue no
         *
         * @param integer $no
         */
        public function setIssueNo($no)
        {
            $this->_issue_no = $no;
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
         * @see Issue::setPosted()
         * @param integer $time
         */
        public function setCreatedAt($time)
        {
            $this->setPosted($time);
        }

        /**
         * Returns the issue status
         *
         * @return \thebuggenie\core\entities\Status
         */
        public function getStatus()
        {
            return $this->_b2dbLazyload('_status');
        }

        /**
         * Returns the editions for this issue
         *
         * @return array Returns an array with 'edition' (\thebuggenie\core\entities\Edition), 'status' (\thebuggenie\core\entities\Datatype), 'confirmed' (boolean) and 'a_id'
         */
        public function getEditions()
        {
            $this->_populateAffected();
            return $this->_editions;
        }

        public function isEditionAffected(\thebuggenie\core\entities\Edition $edition)
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

        /**
         * Return the first affected edition, if any
         *
         * @return Edition
         */
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
         * @return array Returns an array with 'build' (\thebuggenie\core\entities\Build), 'status' (\thebuggenie\core\entities\Datatype), 'confirmed' (boolean) and 'a_id'
         */
        public function getBuilds()
        {
            $this->_populateAffected();
            return $this->_builds;
        }

        public function isAffectingBuilds()
        {
            $builds = $this->getBuilds();
            return (bool) count($builds);
        }

        public function isBuildAffected(\thebuggenie\core\entities\Build $build)
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

        /**
         * Return the first affected build, if any
         *
         * @return Build
         */
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
         * @return array Returns an array with 'component' (\thebuggenie\core\entities\Component), 'status' (\thebuggenie\core\entities\Datatype), 'confirmed' (boolean) and 'a_id'
         */
        public function getComponents()
        {
            $this->_populateAffected();
            return $this->_components;
        }

        public function isAffectingComponents()
        {
            $components = $this->getComponents();
            return (bool) count($components);
        }

        public function isComponentAffected(\thebuggenie\core\entities\Component $component)
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

        /**
         * Return the first affected component, if any
         *
         * @return Component
         */
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
        public static function getFormattedTime($time, $strict = true)
        {
            $values = array();
            $i18n = framework\Context::getI18n();
            if (!is_array($time)) throw new \Exception("That's not a valid time");
            if (array_key_exists('months', $time) && $time['months'] > 0)
            {
                $values[] = ($time['months'] == 1) ? $i18n->__('1 month') : $i18n->__('%number_of months', array('%number_of' => $time['months']));
            }
            if (array_key_exists('weeks', $time) && $time['weeks'] > 0)
            {
                $values[] = ($time['weeks'] == 1) ? $i18n->__('1 week') : $i18n->__('%number_of weeks', array('%number_of' => $time['weeks']));
            }
            if (array_key_exists('days', $time) && ($time['days'] > 0 || !$strict))
            {
                $values[] = ($time['days'] == 1) ? $i18n->__('1 day') : $i18n->__('%number_of days', array('%number_of' => $time['days']));
            }
            if (array_key_exists('hours', $time) && ($time['hours'] > 0 || !$strict))
            {
                $values[] = ($time['hours'] == 1) ? $i18n->__('1 hour') : $i18n->__('%number_of hours', array('%number_of' => $time['hours']));
            }
            $retval = join(', ', $values);

            if (array_key_exists('points', $time) && ($time['points'] > 0 || !$strict))
            {
                if (!empty($values))
                {
                    $retval .= ' / ';
                }
                $retval .= ($time['points'] == 1) ? $i18n->__('1 point') : $i18n->__('%number_of points', array('%number_of' => $time['points']));
            }

            return ($retval != '') ? $retval : $i18n->__('No time');
        }

        /**
         * Attach a link to the issue
         *
         * @param string $url The url of the link
         * @param string $description [optional] a description
         */
        public function attachLink($url, $description = null)
        {
            $link_id = tables\Links::getTable()->addLinkToIssue($this->getID(), $url, $description);
            return $link_id;
        }

        /**
         * Attach a file to the issue
         *
         * @param \thebuggenie\core\entities\File $file The file to attach
         */
        public function attachFile(\thebuggenie\core\entities\File $file, $file_comment = '', $file_description = '')
        {
            $existed = !tables\IssueFiles::getTable()->addByIssueIDandFileID($this->getID(), $file->getID());
            if (!$existed)
            {
                $comment = new \thebuggenie\core\entities\Comment();
                $comment->setPostedBy(framework\Context::getUser()->getID());
                $comment->setTargetID($this->getID());
                $comment->setTargetType(Comment::TYPE_ISSUE);
                if ($file_comment)
                {
                    $comment->setContent(framework\Context::getI18n()->__('A file was uploaded. %link_to_file This comment was attached: %comment', array('%comment' => "\n\n".$file_comment, '%link_to_file' => "[[File:{$file->getOriginalFilename()}|thumb|{$file_description}]]")));
                }
                else
                {
                    $comment->setContent(framework\Context::getI18n()->__('A file was uploaded. %link_to_file', array('%link_to_file' => "[[File:{$file->getOriginalFilename()}|thumb|{$file_description}]]")));
                }
                $comment->save();
                if ($this->_files !== null)
                {
                    $this->_files[$file->getID()] = $file;
                }
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

                if ($res = tables\IssueRelations::getTable()->getRelatedIssues($this->getID()))
                {
                    while ($row = $res->getNextRow())
                    {
                        try
                        {
                            if ($row->get(tables\IssueRelations::PARENT_ID) == $this->getID())
                            {
                                $issue = new \thebuggenie\core\entities\Issue($row->get(tables\IssueRelations::CHILD_ID));
                                $this->_child_issues[$row->get(tables\IssueRelations::ID)] = $issue;
                            }
                            else
                            {
                                $issue = new \thebuggenie\core\entities\Issue($row->get(tables\IssueRelations::PARENT_ID));
                                $this->_parent_issues[$row->get(tables\IssueRelations::ID)] = $issue;
                            }
                        }
                        catch (\Exception $e)
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
                $this->_b2dbLazyload('_duplicate_issues');
                foreach ($this->_duplicate_issues as $issue_id => $issue)
                {
                    if (!$issue->hasAccess()) unset($this->_duplicate_issues[$issue_id]);
                }
            }
        }

        /**
         * Return issues relating to this
         *
         * @return array|\thebuggenie\core\entities\Issue
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

        public function hasParentIssuetype($issuetype)
        {
            $issuetype_id = ($issuetype instanceof \thebuggenie\core\entities\Issuetype) ? $issuetype->getID() : $issuetype;

            if (! count($this->getParentIssues())) return false;

            foreach ($this->getParentIssues() as $issue)
            {
                if ($issue->getIssueType()->getID() != $issuetype_id) return false;
            }

            return true;
        }

        /**
         * Return related issues
         *
         * @return array|\thebuggenie\core\entities\Issue
         */
        public function getChildIssues()
        {
            $this->_populateRelatedIssues();
            return $this->_child_issues;
        }

        public function hasChildIssues()
        {
            return (bool) $this->countChildIssues();
        }

        public function countChildIssues()
        {
            if ($this->_child_issues !== null)
            {
                return count($this->_child_issues);
            }
            else
            {
                return tables\IssueRelations::getTable()->countChildIssues($this->getID());
            }
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
                if ($res = tables\Votes::getTable()->getByIssueId($this->getID()))
                {
                    while ($row = $res->getNextRow())
                    {
                        $this->_votes[$row->get(tables\Votes::UID)] = $row->get(tables\Votes::VOTE);
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

            if (($user_id == \thebuggenie\core\framework\Settings::getDefaultUserID() && \thebuggenie\core\framework\Settings::isDefaultUserGuest()) || !$this->getProject()->canVoteOnIssues())
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
            $user_id = framework\Context::getUser()->getID();
            if (!$this->hasUserVoted($user_id, $up))
            {
                tables\Votes::getTable()->addByUserIdAndIssueId($user_id, $this->getID(), $up);
                $this->_votes[$user_id] = ($up) ? 1 : -1;
                $this->_votes_total = array_sum($this->_votes);
                tables\Issues::getTable()->saveVotesTotalForIssueID($this->_votes_total, $this->getID());
                return true;
            }
            else
            {
                return false;
            }
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
                if ($resultset = tables\IssueTags::getTable()->getByIssueID($this->getID()))
                {
                    while ($row = $resultset->getNextRow())
                    {
                        $this->_tags[$row->get(tables\IssueTags::ID)] = $row->get(tables\IssueTags::TAG_NAME);
                    }
                }
            }

            return $this->_tags;
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
            return htmlentities($this->_title, ENT_COMPAT, framework\Context::getI18n()->getCharset());
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
                throw new \Exception("Can't set an empty title");
            }
            $this->_addChangedProperty('_title', $title);
        }

        /**
         * Returns the issue shortname
         *
         * @return string
         */
        public function getShortname()
        {
            return htmlentities($this->_shortname, ENT_COMPAT, framework\Context::getI18n()->getCharset());
        }

        /**
         * Returns the issue shortname
         *
         * @return string
         */
        public function getRawShortname()
        {
            return $this->_shortname;
        }

        /**
         * Set the shortname
         *
         * @param string $shortname The new shortname to set
         */
        public function setShortname($shortname)
        {
            $this->_addChangedProperty('_shortname', $shortname);
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

        public function getParsedDescription($options)
        {
            return $this->_getParsedText($this->getDescription(), $this->getDescriptionSyntax(), $options);
        }

        /**
         * Returns the description syntax
         *
         * @return integer
         */
        public function getDescriptionSyntax()
        {
            return $this->_description_syntax;
        }

        protected function _getParsedText($text, $syntax, $options = array())
        {
            switch ($syntax)
            {
                default:
                case \thebuggenie\core\framework\Settings::SYNTAX_PT:
                    $options = array('plain' => true);
                case \thebuggenie\core\framework\Settings::SYNTAX_MW:
                    $wiki_parser = new \thebuggenie\core\helpers\TextParser($text);
                    foreach ($options as $option => $value)
                    {
                        $wiki_parser->setOption($option, $value);
                    }
                    $text = $wiki_parser->getParsedText();
                    break;
                case \thebuggenie\core\framework\Settings::SYNTAX_MD:
                    $parser = new \thebuggenie\core\helpers\TextParserMarkdown();
                    $text = $parser->transform($text);
                    break;
            }

            return $text;
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
         * Return whether or not this issue has a shortname set
         *
         * @return boolean
         */
        public function hasShortname()
        {
            return (bool) (trim($this->getShortname()) != '');
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
         * Set the description syntax
         *
         * @param integer $syntax
         */
        public function setDescriptionSyntax($syntax)
        {
            if (!is_numeric($syntax)) $syntax = \thebuggenie\core\framework\Settings::getSyntaxValue($syntax);

            $this->_addChangedProperty('_description_syntax', $syntax);
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

        public function getParsedReproductionSteps($options)
        {
            return $this->_getParsedText($this->getReproductionSteps(), $this->getReproductionStepsSyntax(), $options);
        }

        /**
         * Returns the issues reproduction steps syntax
         *
         * @return integer
         */
        public function getReproductionStepsSyntax()
        {
            return $this->_reproduction_steps_syntax;
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
         * Set the reproduction steps syntax
         *
         * @param integer $syntax
         */
        public function setReproductionStepsSyntax($syntax)
        {
            if (!is_numeric($syntax)) $syntax = \thebuggenie\core\framework\Settings::getSyntaxValue($syntax);

            $this->_addChangedProperty('_reproduction_steps_syntax', $syntax);
        }

        /**
         * Returns the category
         *
         * @return \thebuggenie\core\entities\Category
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
         * @return \thebuggenie\core\entities\Reproducability
         */
        public function getReproducability()
        {
            return $this->_b2dbLazyload('_reproducability');
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
         * @return \thebuggenie\core\entities\Priority
         */
        public function getPriority()
        {
            return $this->_b2dbLazyload('_priority');
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
            foreach (CustomDatatype::getAll() as $key => $customdatatype)
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
                $customtype = CustomDatatype::getByKey($key);
                if ($customtype->getType() == CustomDatatype::CALCULATED_FIELD)
                {
                    $result = null;
                    $options = $customtype->getOptions();
                    if (!empty($options)) {
                        $formula = array_pop($options)->getValue();

                        preg_match_all('/{([[:alnum:]]+)}/', $formula, $matches);

                        $hasValues = false;
                        for($i=0; $i<count($matches[0]); $i++) {
                            $value = $this->getCustomField($matches[1][$i]);
                            if ($value instanceof \thebuggenie\core\entities\CustomDatatypeOption) {
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
                                $m = new \Webit\Util\EvalMath\EvalMath();
                                $m->suppress_errors = true;
                                $result = $m->evaluate($formula);
                                if (!empty($m->last_error)) {
                                    $result = $m->last_error;
                                } else {
                                    $result = round($result, 2);
                                }
                            } catch (\Exception $e) {
                                $result = 'N/A';
                            }
                        }
                    }
                    return $result;
                }
                elseif ($this->$var_name && $customtype->hasCustomOptions() && !$this->$var_name instanceof \thebuggenie\core\entities\CustomDatatypeOption)
                {
                    $this->$var_name = tables\CustomFieldOptions::getTable()->selectById($this->$var_name);
                }
                elseif ($this->$var_name && $customtype->hasPredefinedOptions() && !$this->$var_name instanceof \thebuggenie\core\entities\common\Identifiable)
                {
                    try
                    {
                        switch ($customtype->getType())
                        {
                            case CustomDatatype::EDITIONS_CHOICE:
                                $this->$var_name = tables\Editions::getTable()->selectById($this->$var_name);
                                break;
                            case CustomDatatype::COMPONENTS_CHOICE:
                                $this->$var_name = tables\Components::getTable()->selectById($this->$var_name);
                                break;
                            case CustomDatatype::RELEASES_CHOICE:
                                $this->$var_name = tables\Builds::getTable()->selectById($this->$var_name);
                                break;
                            case CustomDatatype::MILESTONE_CHOICE:
                                $this->$var_name = tables\Milestones::getTable()->selectById($this->$var_name);
                                break;
                            case CustomDatatype::CLIENT_CHOICE:
                                $this->$var_name = tables\Clients::getTable()->selectById($this->$var_name);
                                break;
                            case CustomDatatype::USER_CHOICE:
                                $this->$var_name = tables\Users::getTable()->selectById($this->$var_name);
                                break;
                            case CustomDatatype::TEAM_CHOICE:
                                $this->$var_name = tables\Teams::getTable()->selectById($this->$var_name);
                                break;
                            case CustomDatatype::STATUS_CHOICE:
                                $this->$var_name = Status::getB2DBTable()->selectById($this->$var_name);
                                break;
                        }
                    }
                    catch (\Exception $e) { }
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
         * Returns the agile board color
         *
         * @return string
         */
        public function getAgileColor()
        {
            return $this->_scrumcolor;
        }

        public function getAgileTextColor()
        {
            if (!\thebuggenie\core\framework\Context::isCLI())
            {
                \thebuggenie\core\framework\Context::loadLibrary('ui');
            }

            $rgb = hex2rgb($this->_scrumcolor);

            if (! $rgb) return '#333';

            return 0.299*$rgb['red'] + 0.587*$rgb['green'] + 0.114*$rgb['blue'] > 170 ? '#333' : '#FFF';
        }

        /**
         * Set the agile board color for this issue
         *
         * @param integer $color The color to change to
         */
        public function setAgileColor($color)
        {
            $this->_addChangedProperty('_scrumcolor', $color);
        }

        /**
         * Returns the assigned milestone if any
         *
         * @return \thebuggenie\core\entities\Milestone
         */
        public function getMilestone()
        {
            return $this->_b2dbLazyload('_milestone');
        }

        /**
         * Set the milestone
         *
         * @param integer|Milestone $milestone_id The milestone id to assign
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
            if ($row = tables\IssueRelations::getTable()->getIssueRelation($this->getID(), $issue_id))
            {
                $related_issue = \thebuggenie\core\entities\Issue::getB2DBTable()->selectById($issue_id);
                $relation_id = $row->get(tables\IssueRelations::ID);
                if ($row->get(tables\IssueRelations::PARENT_ID) == $this->getID())
                {
                    $this->_removeChildIssue($related_issue, $relation_id);
                }
                else
                {
                    $this->_removeParentIssue($related_issue, $relation_id);
                }
                $last_updated = time();
                $this->touch($last_updated);
                $related_issue->touch($last_updated);
                tables\IssueRelations::getTable()->doDeleteById($relation_id);
            }
        }

        /**
         * Removes a parent issue
         *
         * @see removeDependantIssue()
         *
         * @param \thebuggenie\core\entities\Issue $related_issue The issue to remove relations from
         * @param integer $relation_id The relation id to delete
         */
        protected function _removeParentIssue($related_issue, $relation_id)
        {
            $this->addLogEntry(tables\Log::LOG_ISSUE_DEPENDS, framework\Context::getI18n()->__('This issue no longer depends on the solution of issue %issue_no', array('%issue_no' => $related_issue->getFormattedIssueNo())), $related_issue->getID(), 0);
            $related_issue->addLogEntry(tables\Log::LOG_ISSUE_DEPENDS, framework\Context::getI18n()->__('Issue %issue_no no longer depends on the solution of this issue', array('%issue_no' => $this->getFormattedIssueNo())), $this->getID(), 0);
            $related_issue->calculateTime();

            if ($this->_parent_issues !== null && array_key_exists($relation_id, $this->_parent_issues))
            {
                unset($this->_parent_issues[$relation_id]);
            }
        }

        /**
         * Removes a child issue
         *
         * @see removeDependantIssue()
         *
         * @param \thebuggenie\core\entities\Issue $related_issue The issue to remove relations from
         * @param integer $relation_id The relation id to delete
         */
        protected function _removeChildIssue($related_issue, $relation_id)
        {
            $this->addLogEntry(tables\Log::LOG_ISSUE_DEPENDS, framework\Context::getI18n()->__('Issue %issue_no no longer depends on the solution of this issue', array('%issue_no' => $related_issue->getFormattedIssueNo())), $this->getID(), 0);
            $related_issue->addLogEntry(tables\Log::LOG_ISSUE_DEPENDS, framework\Context::getI18n()->__('This issue no longer depends on the solution of issue %issue_no', array('%issue_no' => $this->getFormattedIssueNo())), $related_issue->getID(), 0);
            $this->calculateTime();

            if ($this->_child_issues !== null && array_key_exists($relation_id, $this->_child_issues))
            {
                unset($this->_child_issues[$relation_id]);
            }
        }

        /**
         * Add a related issue
         *
         * @param \thebuggenie\core\entities\Issue $related_issue
         *
         * @return boolean
         */
        public function addParentIssue(\thebuggenie\core\entities\Issue $related_issue)
        {
            if (!$row = tables\IssueRelations::getTable()->getIssueRelation($this->getID(), $related_issue->getID()))
            {
                tables\IssueRelations::getTable()->addParentIssue($this->getID(), $related_issue->getID());
                $this->_parent_issues = null;

                $related_issue->addLogEntry(tables\Log::LOG_ISSUE_DEPENDS, framework\Context::getI18n()->__('This %this_issuetype now depends on the solution of %issuetype %issue_no', array('%this_issuetype' => $related_issue->getIssueType()->getName(), '%issuetype' => $this->getIssueType()->getName(), '%issue_no' => $this->getFormattedIssueNo())));
                $this->addLogEntry(tables\Log::LOG_ISSUE_DEPENDS, framework\Context::getI18n()->__('%issuetype %issue_no now depends on the solution of this %this_issuetype', array('%this_issuetype' => $this->getIssueType()->getName(), '%issuetype' => $related_issue->getIssueType()->getName(), '%issue_no' => $related_issue->getFormattedIssueNo())));
                $related_issue->calculateTime();
                $related_issue->save();

                return true;
            }
            return false;
        }

        /**
         * Add a related issue
         *
         * @param \thebuggenie\core\entities\Issue $related_issue
         *
         * @return boolean
         */
        public function addChildIssue(\thebuggenie\core\entities\Issue $related_issue, $epic = false)
        {
            if (!$row = tables\IssueRelations::getTable()->getIssueRelation($this->getID(), $related_issue->getID()))
            {
                if (! $epic && ! $this->getMilestone() instanceof Milestone && $related_issue->getMilestone() instanceof Milestone)
                {
                    $related_issue->removeMilestone();
                    $related_issue->save();
                }
                else if ($this->getMilestone() instanceof Milestone)
                {
                    $related_issue->setMilestone($this->getMilestone()->getID());
                    $related_issue->save();
                }

                $res = tables\IssueRelations::getTable()->addChildIssue($this->getID(), $related_issue->getID());
                $this->_child_issues = null;

                $related_issue->addLogEntry(tables\Log::LOG_ISSUE_DEPENDS, framework\Context::getI18n()->__('%issuetype %issue_no now depends on the solution of this %this_issuetype', array('%this_issuetype' => $related_issue->getIssueType()->getName(), '%issuetype' => $this->getIssueType()->getName(), '%issue_no' => $this->getFormattedIssueNo())));
                $this->addLogEntry(tables\Log::LOG_ISSUE_DEPENDS, framework\Context::getI18n()->__('This %this_issuetype now depends on the solution of %issuetype %issue_no', array('%this_issuetype' => $this->getIssueType()->getName(), '%issuetype' => $related_issue->getIssueType()->getName(), '%issue_no' => $related_issue->getFormattedIssueNo())));
                $this->calculateTime();
                $this->save();
                $last_updated = time();
                $this->touch($last_updated);
                $related_issue->touch($last_updated);

                return true;
            }
            return false;
        }

        public function calculateTime()
        {
            $estimated_times = array('months' => 0, 'weeks' => 0, 'days' => 0, 'hours' => 0, 'points' => 0);
            $spent_times = array('months' => 0, 'weeks' => 0, 'days' => 0, 'hours' => 0, 'points' => 0);
            foreach ($this->getChildIssues() as $issue)
            {
                foreach ($issue->getEstimatedTime() as $key => $value) $estimated_times[$key] += $value;
                foreach ($issue->getSpentTime() as $key => $value) $spent_times[$key] += $value;
            }

            $spent_times['hours'] *= 100;

            $this->setEstimatedTime($estimated_times);
            $this->setSpentTime($spent_times);
        }

        /**
         * Return the poster
         *
         * @return \thebuggenie\core\entities\User
         */
        public function getPostedBy()
        {
            $this->_posted_by = $this->_b2dbLazyload('_posted_by');
            return $this->_posted_by;
        }

        /**
         * Whether or not the issue is posted by someone
         *
         * @return boolean
         */
        public function isPostedBy()
        {
            return (bool) ($this->getPostedBy() instanceof \thebuggenie\core\entities\common\Identifiable);
        }

        /**
         * Return the poster id
         *
         * @return integer
         */
        public function getPostedByID()
        {
            $poster = $this->getPostedBy();
            return ($poster instanceof \thebuggenie\core\entities\common\Identifiable) ? $poster->getID() : null;
        }

        /**
         * Set issue poster
         *
         * @param \thebuggenie\core\entities\common\Identifiable $poster The user/team you want to have posted the issue
         */
        public function setPostedBy(\thebuggenie\core\entities\common\Identifiable $poster)
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

        public function getEstimatedPercentCompleted()
        {
            if ($this->getEstimatedPoints() > 0)
            {
                $estimated = $this->getEstimatedPoints();
                $spent = $this->getSpentPoints();
            }
            else
            {
                $estimated = $this->getEstimatedHours();
                $estimated += $this->getEstimatedDays() * 8;
                $estimated += $this->getEstimatedWeeks() * 8 * 5;
                $estimated += $this->getEstimatedMonths() * 8 * 22;

                $spent = $this->getSpentHours();
                $spent += $this->getSpentDays() * 8;
                $spent += $this->getSpentWeeks() * 8 * 5;
                $spent += $this->getSpentMonths() * 8 * 22;
            }
            if ($estimated <= 0) return 0;

            $multiplier = 100 / $estimated;
            $pct = $spent * $multiplier;

            return ($pct <= 100) ? $pct : 100;
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
         * @return \thebuggenie\core\entities\Resolution
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
         * @return \thebuggenie\core\entities\Severity
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
        public static function convertFancyStringToTime($string)
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
                            $retarr['hours'] = trim($time_parts[0]);
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
            elseif (is_array($time))
            {
                foreach ($time as $key => $value)
                {
                    $this->_addChangedProperty('_estimated_'.$key, $value);
                }
            }
            else
            {
                $time = self::convertFancyStringToTime($time);
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
            return (bool) ($this->getOwner() instanceof \thebuggenie\core\entities\common\Identifiable);
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
            return (bool) ($this->getAssignee() instanceof \thebuggenie\core\entities\common\Identifiable);
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
            return array('months' => (int) $this->_spent_months, 'weeks' => (int) $this->_spent_weeks, 'days' => (int) $this->_spent_days, 'hours' => round($this->_spent_hours / 100, 2), 'points' => (int) $this->_spent_points);
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
            return (int) round($this->_spent_hours / 100, 2);
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

        public function setSpentTime($time)
        {
            if (is_array($time))
            {
                foreach ($time as $key => $value)
                {
                    $this->_addChangedProperty('_spent_'.$key, $value);
                }
            }
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

        public function touch($last_updated = null)
        {
            tables\Issues::getTable()->touchIssue($this->getID(), $last_updated);
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
         * @param \thebuggenie\core\entities\Build $build The build to add
         *
         * @return boolean
         */
        public function addAffectedBuild($build)
        {
            if ($this->getProject() && $this->getProject()->isBuildsEnabled())
            {
                $retval = tables\IssueAffectsBuild::getTable()->setIssueAffected($this->getID(), $build->getID());
                if ($retval !== false)
                {
                    $this->touch();
                    $this->addLogEntry(tables\Log::LOG_AFF_ADD, framework\Context::getI18n()->__("'%release_name' added", array('%release_name' => $build->getName())));
                    return array('a_id' => $retval, 'build' => $build, 'confirmed' => 0, 'status' => null);
                }
                foreach ($this->getChildIssues() as $issue)
                {
                    $issue->addAffectedBuild($build);
                }
            }
            return false;
        }

        /**
         * Add an edition to the list of affected editions
         *
         * @param \thebuggenie\core\entities\Edition $edition The edition to add
         *
         * @return boolean
         */
        public function addAffectedEdition($edition)
        {
            if ($this->getProject() && $this->getProject()->isEditionsEnabled())
            {
                $retval = tables\IssueAffectsEdition::getTable()->setIssueAffected($this->getID(), $edition->getID());
                if ($retval !== false)
                {
                    $this->touch();
                    $this->addLogEntry(tables\Log::LOG_AFF_ADD, framework\Context::getI18n()->__("'%edition_name' added", array('%edition_name' => $edition->getName())));
                    return array('a_id' => $retval, 'edition' => $edition, 'confirmed' => 0, 'status' => null);
                }
            }
            return false;
        }

        /**
         * Add a component to the list of affected components
         *
         * @param \thebuggenie\core\entities\Component $component The component to add
         *
         * @return boolean
         */
        public function addAffectedComponent($component)
        {
            if ($this->getProject() && $this->getProject()->isComponentsEnabled())
            {
                $retval = tables\IssueAffectsComponent::getTable()->setIssueAffected($this->getID(), $component->getID());
                if ($retval !== false)
                {
                    $this->touch();
                    $this->addLogEntry(tables\Log::LOG_AFF_ADD, framework\Context::getI18n()->__("'%component_name' added", array('%component_name' => $component->getName())));
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
         * @param \thebuggenie\core\entities\Edition $item The edition to remove
         *
         * @return boolean
         */
        public function removeAffectedEdition($item)
        {
            if (tables\IssueAffectsEdition::getTable()->deleteByIssueIDandEditionID($this->getID(), $item->getID()))
            {
                $this->touch();
                $this->addLogEntry(tables\Log::LOG_AFF_DELETE, framework\Context::getI18n()->__("'%item_name' removed", array('%item_name' => $item->getName())));
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
         * @param \thebuggenie\core\entities\Build $item The build to remove
         *
         * @return boolean
         */
        public function removeAffectedBuild($item)
        {
            if (tables\IssueAffectsBuild::getTable()->deleteByIssueIDandBuildID($this->getID(), $item->getID()))
            {
                $this->touch();
                $this->addLogEntry(tables\Log::LOG_AFF_DELETE, framework\Context::getI18n()->__("'%item_name' removed", array('%item_name' => $item->getName())));
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
         * @param \thebuggenie\core\entities\Component $item The component to remove
         *
         * @return boolean
         */
        public function removeAffectedComponent($item)
        {
            if (tables\IssueAffectsComponent::getTable()->deleteByIssueIDandComponentID($this->getID(), $item->getID()))
            {
                $this->touch();
                $this->addLogEntry(tables\Log::LOG_AFF_DELETE, framework\Context::getI18n()->__("'%item_name' removed", array('%item_name' => $item->getName())));
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
         * @param \thebuggenie\core\entities\Edition $item The edition to remove
         * @param boolean $confirmed [optional] Whether it's confirmed or not
         *
         * @return boolean
         */
        public function confirmAffectedEdition($item, $confirmed = true)
        {
            if (tables\IssueAffectsEdition::getTable()->confirmByIssueIDandEditionID($this->getID(), $item->getID(), $confirmed))
            {
                $this->touch();
                if ($confirmed)
                {
                    $this->addLogEntry(tables\Log::LOG_AFF_UPDATE, framework\Context::getI18n()->__("'%edition' is now confirmed for this issue", array('%edition' => $item->getName())));
                }
                else
                {
                    $this->addLogEntry(tables\Log::LOG_AFF_UPDATE, framework\Context::getI18n()->__("'%edition' is now unconfirmed for this issue", array('%edition' => $item->getName())));
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
         * @param \thebuggenie\core\entities\Build $item The build to remove
         * @param boolean $confirmed [optional] Whether it's confirmed or not
         *
         * @return boolean
         */
        public function confirmAffectedBuild($item, $confirmed = true)
        {
            if (tables\IssueAffectsBuild::getTable()->confirmByIssueIDandBuildID($this->getID(), $item->getID(), $confirmed))
            {
                $this->touch();
                if ($confirmed)
                {
                    $this->addLogEntry(tables\Log::LOG_AFF_UPDATE, framework\Context::getI18n()->__("'%build' is now confirmed for this issue", array('%build' => $item->getName())));
                }
                else
                {
                    $this->addLogEntry(tables\Log::LOG_AFF_UPDATE, framework\Context::getI18n()->__("'%build' is now unconfirmed for this issue", array('%build' => $item->getName())));
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
         * @param \thebuggenie\core\entities\Component $item The component to remove
         * @param boolean $confirmed [optional] Whether it's confirmed or not
         *
         * @return boolean
         */
        public function confirmAffectedComponent($item, $confirmed = true)
        {
            if (tables\IssueAffectsComponent::getTable()->confirmByIssueIDandComponentID($this->getID(), $item->getID(), $confirmed))
            {
                $this->touch();
                if ($confirmed)
                {
                    $this->addLogEntry(tables\Log::LOG_AFF_UPDATE, framework\Context::getI18n()->__("'%component' is now confirmed for this issue", array('%component' => $item->getName())));
                }
                else
                {
                    $this->addLogEntry(tables\Log::LOG_AFF_UPDATE, framework\Context::getI18n()->__("'%component' is now unconfirmed for this issue", array('%component' => $item->getName())));
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
         * @param \thebuggenie\core\entities\Edition $item The edition to set status for
         * @param \thebuggenie\core\entities\Datatype $status The status to set
         *
         * @return boolean
         */
        public function setAffectedEditionStatus($item, $status)
        {
            if (tables\IssueAffectsEdition::getTable()->setStatusByIssueIDandEditionID($this->getID(), $item->getID(), $status->getID()))
            {
                $this->touch();
                $this->addLogEntry(tables\Log::LOG_AFF_DELETE, framework\Context::getI18n()->__("'%item_name' -> '%status_name", array('%item_name' => $item->getName(), '%status_name' => $status->getName())));
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
         * @param \thebuggenie\core\entities\Build $item The build to set status for
         * @param \thebuggenie\core\entities\Datatype $status The status to set
         *
         * @return boolean
         */
        public function setAffectedBuildStatus($item, $status)
        {
            if (tables\IssueAffectsBuild::getTable()->setStatusByIssueIDandBuildID($this->getID(), $item->getID(), $status->getID()))
            {
                $this->touch();
                $this->addLogEntry(tables\Log::LOG_AFF_DELETE, framework\Context::getI18n()->__("'%item_name' -> '%status_name", array('%item_name' => $item->getName(), '%status_name' => $status->getName())));
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
         * @param \thebuggenie\core\entities\Component $item The component to set status for
         * @param \thebuggenie\core\entities\Datatype $status The status to set
         *
         * @return boolean
         */
        public function setAffectedComponentStatus($item, $status)
        {
            if (tables\IssueAffectsComponent::getTable()->setStatusByIssueIDandComponentID($this->getID(), $item->getID(), $status->getID()))
            {
                $this->touch();
                $this->addLogEntry(tables\Log::LOG_AFF_DELETE, framework\Context::getI18n()->__("'%item_name' -> '%status_name", array('%item_name' => $item->getName(), '%status_name' => $status->getName())));
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
        public function addLogEntry($change_type, $text = null, $previous_value = null, $current_value = null, $system = false, $time = null)
        {
            $uid = ($system) ? 0 : framework\Context::getUser()->getID();
            $log_item = new LogItem();
            $log_item->setChangeType($change_type);
            $log_item->setText($text);
            if ($time !== null) $log_item->setTime($time);
            if ($previous_value !== null) $log_item->setPreviousValue($previous_value);
            if ($current_value !== null) $log_item->setCurrentValue($current_value);
            $log_item->setTargetType(tables\Log::TYPE_ISSUE);
            $log_item->setTarget($this->getID());
            $log_item->setUser($uid);
            $log_item->save();
            $this->_log_items_added[$log_item->getID()] = $log_item;
            return $log_item;
        }

        /**
         * Adds a system comment
         *
         * @param string $text Comment text
         * @param integer $uid The user ID that posted the comment
         *
         * @return \thebuggenie\core\entities\Comment
         */
        public function addSystemComment($text, $uid)
        {
            $comment = new Comment();
            $comment->setContent($text);
            $comment->setPostedBy($uid);
            $comment->setTargetID($this->getID());
            $comment->setTargetType(Comment::TYPE_ISSUE);
            $comment->setSystemComment();
            if (!\thebuggenie\core\framework\Settings::isCommentTrailClean())
            {
                $comment->save();
            }
            return $comment;
        }

        /**
         * Return an array with all the links:
         *         'id' => array('url', 'description')
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
                $this->_links = tables\Links::getTable()->getByIssueID($this->getID());
            }
        }

        /**
         * Remove a link
         *
         * @param integer $link_id The link ID to remove
         */
        public function removeLink($link_id)
        {
            if ($res = tables\Links::getTable()->removeByIssueIDandLinkID($this->getID(), $link_id))
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
                $this->_files = File::getByIssueID($this->getID());
            }
        }

        /**
         * Return an array with all files attached to this issue
         *
         * @return array|File
         */
        public function getFiles()
        {
            $this->_populateFiles();
            return $this->_files;
        }

        public function countAttachments()
        {
            return $this->countFiles();
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
                    $this->_num_files = File::countByIssueID($this->getID());
                }
            }

            return $this->_num_files;
        }

        /**
         * Return a file by the filename if it is attached to this issue
         *
         * @param string $filename The original filename to match against
         *
         * @return \thebuggenie\core\entities\File
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
         * @param \thebuggenie\core\entities\File $file The file to be removed
         *
         * @return boolean
         */
        public function detachFile(File $file)
        {
            tables\IssueFiles::getTable()->removeByIssueIDandFileID($this->getID(), $file->getID());
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
                $this->_log_entries = tables\Log::getTable()->getByIssueID($this->getID());
            }
        }

        /**
         * Mark issue as blocking or not blocking
         *
         * @param boolean $blocking [optional] Whether it's blocking or not
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
         * Retrieve all spent times for this issue
         *
         * @return array|\thebuggenie\core\entities\IssueSpentTime
         */
        public function getSpentTimes()
        {
            $this->_populateSpentTimes();
            return $this->_spent_times;
        }

        /**
         * Populate comments array
         */
        protected function _populateSpentTimes()
        {
            if ($this->_spent_times === null)
            {
                $this->_b2dbLazyload('_spent_times');
            }
        }

        /**
         * Retrieve all comments for this issue
         *
         * @return array|Comment
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
                $this->_num_user_comments = Comment::countComments($this->getID(), Comment::TYPE_ISSUE, false);
            }

            return (int) $this->_num_user_comments;
        }

        public function isReproductionStepsChanged()
        {
            return $this->isReproduction_StepsChanged() || $this->isReproduction_Steps_SyntaxChanged();
        }

        public function isDescriptionChanged()
        {
            return $this->_isPropertyChanged('_description') || $this->isDescription_SyntaxChanged();
        }

        public function isShortnameChanged()
        {
            return $this->_isPropertyChanged('_shortname');
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
            catch (\Exception $e)
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
         * Return whether or not the "owned by" field is visible
         *
         * @return boolean
         */
        public function isOwnedByVisible()
        {
            return (bool) ($this->isFieldVisible('owned_by') || $this->isOwned());
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
         * Return whether or not the "shortname" field is visible
         *
         * @return boolean
         */
        public function isShortnameVisible()
        {
            return (bool) ($this->isFieldVisible('shortname') || $this->getShortname() != '');
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
            return (bool) ($this->isFieldVisible('category') || $this->getCategory() instanceof Datatype);
        }

        /**
         * Return whether or not the "resolution" field is visible
         *
         * @return boolean
         */
        public function isResolutionVisible()
        {
            return (bool) ($this->isFieldVisible('resolution') || $this->getResolution() instanceof Datatype);
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
            return (bool) ($this->isFieldVisible('reproducability') || $this->getReproducability() instanceof Datatype);
        }

        /**
         * Return whether or not the "severity" field is visible
         *
         * @return boolean
         */
        public function isSeverityVisible()
        {
            return (bool) ($this->isFieldVisible('severity') || $this->getSeverity() instanceof Datatype);
        }

        /**
         * Return whether or not the "priority" field is visible
         *
         * @return boolean
         */
        public function isPriorityVisible()
        {
            return (bool) ($this->isFieldVisible('priority') || $this->getPriority() instanceof  Datatype);
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
            return (bool) ($this->isFieldVisible('milestone') || $this->getMilestone() instanceof \thebuggenie\core\entities\Milestone);
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
            $crit->addSelectionColumn(tables\Log::TIME);
            $crit->addWhere(tables\Log::TARGET, $this->_id);
            $crit->addWhere(tables\Log::TARGET_TYPE, 1);
            $crit->addWhere(tables\Log::CHANGE_TYPE, 14);
            $crit->addOrderBy(tables\Log::TIME, 'desc');
            $res = tables\Log::getTable()->doSelect($crit);

            $ret_arr = array();

            $row = $res->getNextRow();
            return($row->get(tables\Log::TIME));
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
            $crit->addSelectionColumn(tables\Log::TIME);
            $crit->addWhere(tables\Log::TARGET, $this->_id);
            $crit->addWhere(tables\Log::TARGET_TYPE, 1);
            $crit->addWhere(tables\Log::CHANGE_TYPE, 22);
            $crit->addOrderBy(tables\Log::TIME, 'desc');
            $res = tables\Log::getTable()->doSelect($crit);

            $ret_arr = array();

            if (count($res) == 0)
            {
                return false;
            }

            $row = $res->getNextRow();
            return($row->get(tables\Log::TIME));
        }

        protected function _saveCustomFieldValues()
        {
            foreach (CustomDatatype::getAll() as $key => $customdatatype)
            {
                switch ($customdatatype->getType())
                {
                    case CustomDatatype::INPUT_TEXT:
                    case CustomDatatype::INPUT_TEXTAREA_SMALL:
                    case CustomDatatype::INPUT_TEXTAREA_MAIN:
                    case CustomDatatype::DATE_PICKER:
                        $option_id = $this->getCustomField($key);
                        tables\IssueCustomFields::getTable()->saveIssueCustomFieldValue($option_id, $customdatatype->getID(), $this->getID());
                        break;
                    case CustomDatatype::EDITIONS_CHOICE:
                    case CustomDatatype::COMPONENTS_CHOICE:
                    case CustomDatatype::RELEASES_CHOICE:
                    case CustomDatatype::MILESTONE_CHOICE:
                    case CustomDatatype::STATUS_CHOICE:
                    case CustomDatatype::USER_CHOICE:
                    case CustomDatatype::TEAM_CHOICE:
                    case CustomDatatype::CLIENT_CHOICE:
                        $option_object = null;
                        try
                        {
                            switch ($customdatatype->getType())
                            {
                                case CustomDatatype::EDITIONS_CHOICE:
                                case CustomDatatype::COMPONENTS_CHOICE:
                                case CustomDatatype::RELEASES_CHOICE:
                                case CustomDatatype::MILESTONE_CHOICE:
                                case CustomDatatype::CLIENT_CHOICE:
                                case CustomDatatype::STATUS_CHOICE:
                                case CustomDatatype::USER_CHOICE:
                                case CustomDatatype::TEAM_CHOICE:
                                    $option_object = $this->getCustomField($key);
                                    break;
                            }
                        }
                        catch (\Exception $e) {}
                        $option_id = (is_object($option_object)) ? $option_object->getID() : null;
                        tables\IssueCustomFields::getTable()->saveIssueCustomFieldOption($option_id, $customdatatype->getID(), $this->getID());
                        break;
                    default:
                        $option_id = ($this->getCustomField($key) instanceof \thebuggenie\core\entities\CustomDatatypeOption) ? $this->getCustomField($key)->getID() : null;
                        tables\IssueCustomFields::getTable()->saveIssueCustomFieldOption($option_id, $customdatatype->getID(), $this->getID());
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
                    $this->_issue_no = tables\Issues::getTable()->getNextIssueNumberForProductID($this->getProject()->getID());

                if (!$this->_posted) $this->_posted = NOW;
                if (!$this->_last_updated) $this->_last_updated = NOW;
                if (!$this->_posted_by) $this->_posted_by = framework\Context::getUser();

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
                    $original_value = $value['original_value'];
                    if ($original_value != $compare_value)
                    {
                        switch ($property)
                        {
                            case '_title':
                                $this->addLogEntry(tables\Log::LOG_ISSUE_UPDATE_TITLE, framework\Context::getI18n()->__("Title updated"), $original_value, $compare_value);
                                break;
                            case '_shortname':
                                $this->addLogEntry(tables\Log::LOG_ISSUE_UPDATE_SHORTNAME, framework\Context::getI18n()->__("Issue label updated"), $original_value, $compare_value);
                                break;
                            case '_description':
                                $this->addLogEntry(tables\Log::LOG_ISSUE_UPDATE_DESCRIPTION, framework\Context::getI18n()->__("Description updated"), $original_value, $compare_value);
                                break;
                            case '_reproduction_steps':
                                $this->addLogEntry(tables\Log::LOG_ISSUE_UPDATE_REPRODUCTIONSTEPS, framework\Context::getI18n()->__("Reproduction steps updated"), $original_value, $compare_value);
                                break;
                            case '_category':
                                if ($original_value != 0)
                                {
                                    $old_name = ($old_item = \thebuggenie\core\entities\Category::getB2DBTable()->selectById($original_value)) ? $old_item->getName() : framework\Context::getI18n()->__('Not determined');
                                }
                                else
                                {
                                    $old_name = framework\Context::getI18n()->__('Not determined');
                                }
                                $new_name = ($this->getCategory() instanceof Datatype) ? $this->getCategory()->getName() : framework\Context::getI18n()->__('Not determined');

                                $this->addLogEntry(tables\Log::LOG_ISSUE_CATEGORY, $old_name . ' &rArr; ' . $new_name, $original_value, $compare_value);
                                break;
                            case '_pain_bug_type':
                                if ($original_value != 0)
                                {
                                    $old_name = ($old_item = self::getPainTypesOrLabel('pain_bug_type', $original_value)) ? $old_item : framework\Context::getI18n()->__('Not determined');
                                }
                                else
                                {
                                    $old_name = framework\Context::getI18n()->__('Not determined');
                                }
                                $new_name = ($new_item = self::getPainTypesOrLabel('pain_bug_type', $value['current_value'])) ? $new_item : framework\Context::getI18n()->__('Not determined');

                                $this->addLogEntry(tables\Log::LOG_ISSUE_PAIN_BUG_TYPE, $old_name . ' &rArr; ' . $new_name, $original_value, $compare_value);
                                break;
                            case '_pain_effect':
                                if ($original_value != 0)
                                {
                                    $old_name = ($old_item = self::getPainTypesOrLabel('pain_effect', $original_value)) ? $old_item : framework\Context::getI18n()->__('Not determined');
                                }
                                else
                                {
                                    $old_name = framework\Context::getI18n()->__('Not determined');
                                }
                                $new_name = ($new_item = self::getPainTypesOrLabel('pain_effect', $value['current_value'])) ? $new_item : framework\Context::getI18n()->__('Not determined');

                                $this->addLogEntry(tables\Log::LOG_ISSUE_PAIN_EFFECT, $old_name . ' &rArr; ' . $new_name, $original_value, $compare_value);
                                break;
                            case '_pain_likelihood':
                                if ($original_value != 0)
                                {
                                    $old_name = ($old_item = self::getPainTypesOrLabel('pain_likelihood', $original_value)) ? $old_item : framework\Context::getI18n()->__('Not determined');
                                }
                                else
                                {
                                    $old_name = framework\Context::getI18n()->__('Not determined');
                                }
                                $new_name = ($new_item = self::getPainTypesOrLabel('pain_likelihood', $value['current_value'])) ? $new_item : framework\Context::getI18n()->__('Not determined');

                                $this->addLogEntry(tables\Log::LOG_ISSUE_PAIN_LIKELIHOOD, $old_name . ' &rArr; ' . $new_name, $original_value, $compare_value);
                                break;
                            case '_user_pain':
                                $this->addLogEntry(tables\Log::LOG_ISSUE_PAIN_CALCULATED, $original_value . ' &rArr; ' . $value['current_value']);
                                break;
                            case '_status':
                                if ($original_value != 0)
                                {
                                    $old_name = ($old_item = \thebuggenie\core\entities\Status::getB2DBTable()->selectById($original_value)) ? $old_item->getName() : framework\Context::getI18n()->__('Unknown');
                                }
                                else
                                {
                                    $old_name = framework\Context::getI18n()->__('Not determined');
                                }
                                $new_name = ($this->getStatus() instanceof Datatype) ? $this->getStatus()->getName() : framework\Context::getI18n()->__('Not determined');

                                $this->addLogEntry(tables\Log::LOG_ISSUE_STATUS, $old_name . ' &rArr; ' . $new_name, $original_value, $compare_value);
                                break;
                            case '_reproducability':
                                if ($original_value != 0)
                                {
                                    $old_name = ($old_item = \thebuggenie\core\entities\Reproducability::getB2DBTable()->selectById($original_value)) ? $old_item->getName() : framework\Context::getI18n()->__('Unknown');
                                }
                                else
                                {
                                    $old_name = framework\Context::getI18n()->__('Not determined');
                                }
                                $new_name = ($this->getReproducability() instanceof Datatype) ? $this->getReproducability()->getName() : framework\Context::getI18n()->__('Not determined');

                                $this->addLogEntry(tables\Log::LOG_ISSUE_REPRODUCABILITY, $old_name . ' &rArr; ' . $new_name, $original_value, $compare_value);
                                break;
                            case '_priority':
                                if ($original_value != 0)
                                {
                                    $old_name = ($old_item = \thebuggenie\core\entities\Priority::getB2DBTable()->selectById($original_value)) ? $old_item->getName() : framework\Context::getI18n()->__('Unknown');
                                }
                                else
                                {
                                    $old_name = framework\Context::getI18n()->__('Not determined');
                                }
                                $new_name = ($this->getPriority() instanceof Datatype) ? $this->getPriority()->getName() : framework\Context::getI18n()->__('Not determined');

                                $this->addLogEntry(tables\Log::LOG_ISSUE_PRIORITY, $old_name . ' &rArr; ' . $new_name, $original_value, $compare_value);
                                break;
                            case '_assignee_team':
                            case '_assignee_user':
                                if (!$is_saved_assignee)
                                {
                                    $new_name = ($this->getAssignee() instanceof \thebuggenie\core\entities\common\Identifiable) ? $this->getAssignee()->getName() : framework\Context::getI18n()->__('Not assigned');

                                    if ($this->getAssignee() instanceof \thebuggenie\core\entities\User)
                                    {
                                        $this->startWorkingOnIssue($this->getAssignee());
                                    }

                                    $this->addLogEntry(tables\Log::LOG_ISSUE_ASSIGNED, $new_name);
                                    $is_saved_assignee = true;
                                }
                                break;
                            case '_posted_by':
                                $old_identifiable = ($original_value) ? \thebuggenie\core\entities\User::getB2DBTable()->selectById($original_value) : framework\Context::getI18n()->__('Unknown');
                                $old_name = ($old_identifiable instanceof \thebuggenie\core\entities\User) ? $old_identifiable->getName() : framework\Context::getI18n()->__('Unknown');
                                $new_name = $this->getPostedBy()->getName();

                                $this->addLogEntry(tables\Log::LOG_ISSUE_POSTED, $old_name . ' &rArr; ' . $new_name, $original_value, $compare_value);
                                break;
                            case '_being_worked_on_by_user':
                                if ($original_value != 0)
                                {
                                    $old_identifiable = \thebuggenie\core\entities\User::getB2DBTable()->selectById($original_value);
                                    $old_name = ($old_identifiable instanceof \thebuggenie\core\entities\User) ? $old_identifiable->getName() : framework\Context::getI18n()->__('Unknown');
                                }
                                else
                                {
                                    $old_name = framework\Context::getI18n()->__('Not being worked on');
                                }
                                $new_name = ($this->getUserWorkingOnIssue() instanceof \thebuggenie\core\entities\User) ? $this->getUserWorkingOnIssue()->getName() : framework\Context::getI18n()->__('Not being worked on');

                                $this->addLogEntry(tables\Log::LOG_ISSUE_USERS, $old_name . ' &rArr; ' . $new_name, $original_value, $compare_value);
                                break;
                            case '_owner_team':
                            case '_owner_user':
                                if (!$is_saved_owner)
                                {
                                    $new_name = ($this->getOwner() instanceof \thebuggenie\core\entities\common\Identifiable) ? $this->getOwner()->getName() : framework\Context::getI18n()->__('Not owned by anyone');

                                    $this->addLogEntry(tables\Log::LOG_ISSUE_OWNED, $new_name);
                                    $is_saved_owner = true;
                                }
                                break;
                            case '_percent_complete':
                                $this->addLogEntry(tables\Log::LOG_ISSUE_PERCENT, $original_value . '% &rArr; ' . $this->getPercentCompleted() . '', $original_value, $compare_value);
                                break;
                            case '_resolution':
                                if ($original_value != 0)
                                {
                                    $old_name = ($old_item = \thebuggenie\core\entities\Resolution::getB2DBTable()->selectById($original_value)) ? $old_item->getName() : framework\Context::getI18n()->__('Unknown');
                                }
                                else
                                {
                                    $old_name = framework\Context::getI18n()->__('Not determined');
                                }
                                $new_name = ($this->getResolution() instanceof Datatype) ? $this->getResolution()->getName() : framework\Context::getI18n()->__('Not determined');

                                $this->addLogEntry(tables\Log::LOG_ISSUE_RESOLUTION, $old_name . ' &rArr; ' . $new_name, $original_value, $compare_value);
                                break;
                            case '_severity':
                                if ($original_value != 0)
                                {
                                    $old_name = ($old_item = \thebuggenie\core\entities\Severity::getB2DBTable()->selectById($original_value)) ? $old_item->getName() : framework\Context::getI18n()->__('Unknown');
                                }
                                else
                                {
                                    $old_name = framework\Context::getI18n()->__('Not determined');
                                }
                                $new_name = ($this->getSeverity() instanceof Datatype) ? $this->getSeverity()->getName() : framework\Context::getI18n()->__('Not determined');

                                $this->addLogEntry(tables\Log::LOG_ISSUE_SEVERITY, $old_name . ' &rArr; ' . $new_name, $original_value, $compare_value);
                                break;
                            case '_milestone':
                                if ($original_value != 0)
                                {
                                    $old_name = ($old_item = \thebuggenie\core\entities\Milestone::getB2DBTable()->selectById($original_value)) ? $old_item->getName() : framework\Context::getI18n()->__('Not determined');
                                }
                                else
                                {
                                    $old_name = framework\Context::getI18n()->__('Not determined');
                                }
                                $new_name = ($this->getMilestone() instanceof \thebuggenie\core\entities\Milestone) ? $this->getMilestone()->getName() : framework\Context::getI18n()->__('Not determined');

                                $this->addLogEntry(tables\Log::LOG_ISSUE_MILESTONE, $old_name . ' &rArr; ' . $new_name, $original_value, $compare_value);
                                $this->_milestone_order = 0;
                                break;
                            case '_issuetype':
                                if ($original_value != 0)
                                {
                                    $old_name = ($old_item = Issuetype::getB2DBTable()->selectById($original_value)) ? $old_item->getName() : framework\Context::getI18n()->__('Unknown');
                                }
                                else
                                {
                                    $old_name = framework\Context::getI18n()->__('Unknown');
                                }
                                $new_name = ($this->getIssuetype() instanceof \thebuggenie\core\entities\Issuetype) ? $this->getIssuetype()->getName() : framework\Context::getI18n()->__('Unknown');

                                $this->addLogEntry(tables\Log::LOG_ISSUE_ISSUETYPE, $old_name . ' &rArr; ' . $new_name, $original_value, $compare_value);
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

                                    $old_formatted_time = (array_sum($old_time) > 0) ? Issue::getFormattedTime($old_time) : framework\Context::getI18n()->__('Not estimated');
                                    $new_formatted_time = ($this->hasEstimatedTime()) ? Issue::getFormattedTime($this->getEstimatedTime()) : framework\Context::getI18n()->__('Not estimated');
                                    $this->addLogEntry(tables\Log::LOG_ISSUE_TIME_ESTIMATED, $old_formatted_time . ' &rArr; ' . $new_formatted_time, serialize($old_time), serialize($this->getEstimatedTime()));
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
                                                        'hours' => round($this->getChangedPropertyOriginal('_spent_hours') / 100, 2),
                                                        'points' => $this->getChangedPropertyOriginal('_spent_points'));

                                    $old_formatted_time = (array_sum($old_time) > 0) ? Issue::getFormattedTime($old_time) : framework\Context::getI18n()->__('No time spent');
                                    $new_formatted_time = ($this->hasSpentTime()) ? Issue::getFormattedTime($this->getSpentTime()) : framework\Context::getI18n()->__('No time spent');
                                    $this->addLogEntry(tables\Log::LOG_ISSUE_TIME_SPENT, $old_formatted_time . ' &rArr; ' . $new_formatted_time, serialize($old_time), serialize($this->getSpentTime()));
                                    $is_saved_spent = true;
                                }
                                break;
                            case '_state':
                                if ($this->isClosed())
                                {
                                    $this->addLogEntry(tables\Log::LOG_ISSUE_CLOSE);
                                    if ($this->getMilestone() instanceof \thebuggenie\core\entities\Milestone)
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
                                        $this->getMilestone()->save();
                                    }
                                }
                                else
                                {
                                    $this->addLogEntry(tables\Log::LOG_ISSUE_REOPEN);
                                }
                                break;
                            case '_blocking':
                                if ($this->isBlocking())
                                {
                                    $this->addLogEntry(tables\Log::LOG_ISSUE_BLOCKED);
                                }
                                else
                                {
                                    $this->addLogEntry(tables\Log::LOG_ISSUE_UNBLOCKED);
                                }
                                break;
                            default:
                                if (mb_substr($property, 0, 12) == '_customfield')
                                {
                                    $key = mb_substr($property, 12);
                                    $customdatatype = CustomDatatype::getByKey($key);

                                    switch ($customdatatype->getType())
                                    {
                                        case CustomDatatype::INPUT_TEXT:
                                            $new_value = ($this->getCustomField($key) != '') ? $this->getCustomField($key) : framework\Context::getI18n()->__('Unknown');
                                            $this->addLogEntry(tables\Log::LOG_ISSUE_CUSTOMFIELD_CHANGED, $key . ': ' . $new_value, $original_value, $compare_value);
                                            break;
                                        case CustomDatatype::INPUT_TEXTAREA_SMALL:
                                        case CustomDatatype::INPUT_TEXTAREA_MAIN:
                                            $new_value = ($this->getCustomField($key) != '') ? $this->getCustomField($key) : framework\Context::getI18n()->__('Unknown');
                                            $this->addLogEntry(tables\Log::LOG_ISSUE_CUSTOMFIELD_CHANGED, $key . ': ' . $new_value, $original_value, $compare_value);
                                            break;
                                        case CustomDatatype::EDITIONS_CHOICE:
                                        case CustomDatatype::COMPONENTS_CHOICE:
                                        case CustomDatatype::RELEASES_CHOICE:
                                        case CustomDatatype::MILESTONE_CHOICE:
                                        case CustomDatatype::STATUS_CHOICE:
                                        case CustomDatatype::TEAM_CHOICE:
                                        case CustomDatatype::USER_CHOICE:
                                        case CustomDatatype::CLIENT_CHOICE:
                                            $old_object = null;
                                            $new_object = null;
                                            try
                                            {
                                                switch ($customdatatype->getType())
                                                {
                                                    case CustomDatatype::EDITIONS_CHOICE:
                                                        $old_object = Edition::getB2DBTable()->selectById($original_value);
                                                        break;
                                                    case CustomDatatype::COMPONENTS_CHOICE:
                                                        $old_object = Component::getB2DBTable()->selectById($original_value);
                                                        break;
                                                    case CustomDatatype::RELEASES_CHOICE:
                                                        $old_object = Build::getB2DBTable()->selectById($original_value);
                                                        break;
                                                    case CustomDatatype::MILESTONE_CHOICE:
                                                        $old_object = Milestone::getB2DBTable()->selectById($original_value);
                                                        break;
                                                    case CustomDatatype::STATUS_CHOICE:
                                                        $old_object = Status::getB2DBTable()->selectById($original_value);
                                                        break;
                                                    case CustomDatatype::TEAM_CHOICE:
                                                        $old_object = Team::getB2DBTable()->selectById($original_value);
                                                        break;
                                                    case CustomDatatype::USER_CHOICE:
                                                        $old_object = User::getB2DBTable()->selectById($original_value);
                                                        break;
                                                    case CustomDatatype::CLIENT_CHOICE:
                                                        $old_object = Client::getB2DBTable()->selectById($original_value);
                                                        break;
                                                }
                                            }
                                            catch (\Exception $e) {}
                                            try
                                            {
                                                switch ($customdatatype->getType())
                                                {
                                                    case CustomDatatype::EDITIONS_CHOICE:
                                                    case CustomDatatype::COMPONENTS_CHOICE:
                                                    case CustomDatatype::RELEASES_CHOICE:
                                                    case CustomDatatype::MILESTONE_CHOICE:
                                                    case CustomDatatype::STATUS_CHOICE:
                                                    case CustomDatatype::TEAM_CHOICE:
                                                    case CustomDatatype::USER_CHOICE:
                                                    case CustomDatatype::CLIENT_CHOICE:
                                                        $new_object = $this->getCustomField($key);
                                                        break;
                                                }
                                            }
                                            catch (\Exception $e) {}
                                            $old_value = (is_object($old_object)) ? $old_object->getName() : framework\Context::getI18n()->__('Unknown');
                                            $new_value = (is_object($new_object)) ? $new_object->getName() : framework\Context::getI18n()->__('Unknown');
                                            $this->addLogEntry(tables\Log::LOG_ISSUE_CUSTOMFIELD_CHANGED, $key . ': ' . $old_value . ' &rArr; ' . $new_value, $original_value, $compare_value);
                                            break;
                                        default:
                                            $old_item = null;
                                            try
                                            {
                                                $old_item = ($original_value) ? new CustomDatatypeOption($original_value) : null;
                                            }
                                            catch (\Exception $e) {}
                                            $old_value = ($old_item instanceof \thebuggenie\core\entities\CustomDatatypeOption) ? $old_item->getName() : framework\Context::getI18n()->__('Unknown');
                                            $new_value = ($this->getCustomField($key) instanceof \thebuggenie\core\entities\CustomDatatypeOption) ? $this->getCustomField($key)->getName() : framework\Context::getI18n()->__('Unknown');
                                            $this->addLogEntry(tables\Log::LOG_ISSUE_CUSTOMFIELD_CHANGED, $key . ': ' . $old_value . ' &rArr; ' . $new_value, $original_value, $compare_value);
                                            break;
                                    }
                                }
                                break;
                        }
                    }
                }

                if ($is_saved_estimated)
                {
                    tables\IssueEstimates::getTable()->saveEstimate($this->getID(), $this->_estimated_months, $this->_estimated_weeks, $this->_estimated_days, $this->_estimated_hours, $this->_estimated_points);
                }

            }

            return $related_issues_to_save;
        }

        protected function _addNotification($type, $user, $updated_by)
        {
            $notification = new Notification();
            $notification->setTarget($this);
            $notification->setNotificationType($type);
            $notification->setTriggeredByUser($updated_by);
            $notification->setUser($user);
            $notification->save();
        }

        /**
         * Returns an array with everyone related to this project
         *
         * @return array|\thebuggenie\core\entities\User
         */
        public function getRelatedUsers()
        {
            $uids = array();
            $teams = array();

            // Add the poster
            $uids[$this->getPostedByID()] = $this->getPostedByID();

            // Add all users from the team owning the issue if valid
            // or add the owning user if a user owns the issue
            if ($this->getOwner() instanceof \thebuggenie\core\entities\Team)
            {
                $teams[$this->getOwner()] = $this->getOwner();
            }
            elseif ($this->getOwner() instanceof \thebuggenie\core\entities\User)
            {
                $uids[$this->getOwner()->getID()] = $this->getOwner()->getID();
            }

            // Add all users from the team assigned to the issue if valid
            // or add the assigned user if a user is assigned to the issue
            if ($this->getAssignee() instanceof \thebuggenie\core\entities\Team)
            {
                $teams[$this->getAssignee()->getID()] = $this->getAssignee();
            }
            elseif ($this->getAssignee() instanceof \thebuggenie\core\entities\User)
            {
                $uids[$this->getAssignee()->getID()] = $this->getAssignee()->getID();
            }

            // Add all users in the team who leads the project, if valid
            // or add the user who leads the project, if valid
            if ($this->getProject()->getLeader() instanceof \thebuggenie\core\entities\Team)
            {
                $teams[$this->getProject()->getLeader()->getID()] = $this->getProject()->getLeader();
            }
            elseif ($this->getProject()->getLeader() instanceof \thebuggenie\core\entities\User)
            {
                $uids[$this->getProject()->getLeader()->getID()] = $this->getProject()->getLeader()->getID();
            }

            // Same for QA
            if ($this->getProject()->getQaResponsible() instanceof \thebuggenie\core\entities\Team)
            {
                $teams[$this->getProject()->getQaResponsible()->getID()] = $this->getProject()->getQaResponsible();
            }
            elseif ($this->getProject()->getQaResponsible() instanceof \thebuggenie\core\entities\User)
            {
                $uids[$this->getProject()->getQaResponsible()->getID()] = $this->getProject()->getQaResponsible()->getID();
            }

            foreach ($this->getProject()->getAssignedTeams() as $team)
            {
                $teams[$team->getID()] = $team;
            }
            foreach ($this->getProject()->getAssignedUsers() as $member)
            {
                $uids[$member->getID()] = $member->getID();
            }

            // Add all users relevant for all affected editions
            foreach ($this->getEditions() as $edition_list)
            {
                if ($edition_list['edition']->getLeader() instanceof \thebuggenie\core\entities\Team)
                {
                    $teams[$edition_list['edition']->getLeaderID()] = $edition_list['edition']->getLeader();
                }
                elseif ($edition_list['edition']->getLeader() instanceof \thebuggenie\core\entities\User)
                {
                    $uids[$edition_list['edition']->getLeaderID()] = $edition_list['edition']->getLeaderID();
                }
                if ($edition_list['edition']->getQaResponsible() instanceof \thebuggenie\core\entities\Team)
                {
                    $teams[$edition_list['edition']->getQaResponsibleID()] = $edition_list['edition']->getQaResponsible();
                }
                elseif ($edition_list['edition']->getQaResponsible() instanceof \thebuggenie\core\entities\User)
                {
                    $uids[$edition_list['edition']->getQaResponsibleID()] = $edition_list['edition']->getQaResponsibleID();
                }
            }

            foreach ($teams as $team)
            {
                foreach ($team->getMembers() as $user)
                {
                    $uids[$user->getID()] = $user->getID();
                }
            }

            if (isset($uids[framework\Context::getUser()->getID()])) unset($uids[framework\Context::getUser()->getID()]);
            $users = tables\Users::getTable()->getByUserIDs($uids);
            return $users;
        }

        protected function _addUpdateNotifications($updated_by)
        {
            $uids = tables\UserIssues::getTable()->getUserIDsByIssueID($this->getID());
            $users = tables\Users::getTable()->getByUserIDs($uids);

            foreach ($users as $user)
            {
                $this->_addNotification(Notification::TYPE_ISSUE_UPDATED, $user, $updated_by);
            }
        }

        protected function _addCreateNotifications($updated_by)
        {
            foreach ($this->getRelatedUsers() as $user)
            {
                $this->_addNotification(Notification::TYPE_ISSUE_CREATED, $user, $updated_by);
            }
        }

        public function triggerSaveEvent($comment, $updated_by)
        {
            $log_items = $this->_log_items_added;
            if ($comment instanceof \thebuggenie\core\entities\Comment && count($log_items))
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
            $this->_addUpdateNotifications($updated_by);
            $event = \thebuggenie\core\framework\Event::createNew('core', 'thebuggenie\core\entities\Issue::save', $this, compact('comment', 'log_items', 'updated_by'));
            $event->trigger();
        }

        protected function _postSave($is_new)
        {
            $this->_saveCustomFieldValues();

            if (!$is_new)
            {
                $related_issues_to_save = $this->_processChanges();
                $comment = (isset($this->_save_comment)) ? $this->_save_comment : $this->addSystemComment('', framework\Context::getUser()->getID());

                $this->triggerSaveEvent($comment, framework\Context::getUser());

                if (count($related_issues_to_save))
                {
                    foreach (array_keys($related_issues_to_save) as $i_id)
                    {
                        $related_issue = \thebuggenie\core\entities\Issue::getB2DBTable()->selectById((int) $i_id);
                        $related_issue->save();
                    }
                }
            }
            else
            {
                $this->addLogEntry(tables\Log::LOG_ISSUE_CREATED, null, false, $this->getPosted());
                $this->_addCreateNotifications($this->getPostedBy());
                \thebuggenie\core\framework\Event::createNew('core', 'thebuggenie\core\entities\Issue::createNew', $this)->trigger();
            }

            if (in_array(\thebuggenie\core\framework\Settings::getUserSetting(\thebuggenie\core\framework\Settings::SETTINGS_USER_SUBSCRIBE_CREATED_UPDATED_COMMENTED_ISSUES, framework\Context::getUser()->getID()), array(null, true)))
            {
                $this->addSubscriber(framework\Context::getUser()->getID());
            }

            $this->_clearChangedProperties();
            $this->_log_items_added = array();
            $this->getProject()->clearRecentActivities();

            if ($this->isChildIssue() && ($this->hasEstimatedTime() || $this->hasSpentTime()))
            {
                foreach ($this->getParentIssues() as $issue)
                {
                    $issue->calculateTime();
                    $issue->save();
                }
            }

            if ($this->getMilestone() instanceof \thebuggenie\core\entities\Milestone)
            {
                $this->getMilestone()->updateStatus();
                                $this->getMilestone()->save();
            }

            return true;
        }

        public function saveSpentTime()
        {
            $spent_times = array('months', 'weeks', 'days', 'hours', 'points');
            $spent_times_changed_items = array();
            $changed_properties = $this->_getChangedProperties();

            foreach ($spent_times as $spent_time_unit)
            {
                $property = '_spent_'.$spent_time_unit;

                if (! $this->_isPropertyChanged($property)) continue;

                $spent_times_changed_items[$property] = $changed_properties[$property];
                unset($changed_properties[$property]);
            }

            foreach ($changed_properties as $property => $property_values)
            {
                $this->_revertPropertyChange($property);
            }

            $this->_changed_items = array();
            $this->save();

            foreach ($changed_properties as $property => $property_values)
            {
                $this->_addChangedProperty($property, $property_values['current_value']);
            }
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
         * @return \thebuggenie\core\entities\User
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
         * @param \thebuggenie\core\entities\User $user
         */
        public function startWorkingOnIssue(User $user)
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
            return ($this->getUserWorkingOnIssue() instanceof \thebuggenie\core\entities\User) ? true : false;
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
                'title' => $this->getRawTitle(),
                'posted_by' => ($this->getPostedBy() instanceof \thebuggenie\core\entities\common\Identifiable) ? $this->getPostedBy()->toJSON() : null,
                'assignee' => ($this->getAssignee() instanceof \thebuggenie\core\entities\common\Identifiable) ? $this->getAssignee()->toJSON() : null,
                'status' => ($this->getStatus() instanceof \thebuggenie\core\entities\common\Identifiable) ? $this->getStatus()->toJSON() : null,
            );

            $fields = $this->getProject()->getVisibleFieldsArray($this->getIssueType());

            foreach ($fields as $field => $details)
            {
                $identifiable = true;
                switch ($field)
                {
                    case 'shortname':
                    case 'description':
                    case 'votes':
                        $identifiable = false;
                    case 'resolution':
                    case 'priority':
                    case 'severity':
                    case 'category':
                    case 'reproducability':
                        $method = 'get'.ucfirst($field);
                        $value = $this->$method();
                        break;
                    case 'milestone':
                        $method = 'get'.ucfirst($field);
                        $value = $this->$method();
                        if (is_numeric($value) && $value == 0) {
                            $value = new Milestone();
                            $value->setID(0);
                        }
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
                if (isset($value))
                {
                    if ($identifiable)
                        $return_values[$field] = ($value instanceof \thebuggenie\core\entities\common\Identifiable) ? $value->toJSON() : null;
                    else
                        $return_values[$field] = $value;
                }

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

        /**
         * Return the currently assigned user or team
         *
         * @return common\Identifiable
         */
        public function getAssignee()
        {
            $this->_b2dbLazyload('_assignee_team');
            $this->_b2dbLazyload('_assignee_user');

            if ($this->_assignee_team instanceof \thebuggenie\core\entities\Team) {
                return $this->_assignee_team;
            } elseif ($this->_assignee_user instanceof \thebuggenie\core\entities\User) {
                return $this->_assignee_user;
            } else {
                return null;
            }
        }

        public function hasAssignee()
        {
            return (bool) ($this->getAssignee() instanceof \thebuggenie\core\entities\common\Identifiable);
        }

        public function setAssignee(\thebuggenie\core\entities\common\Identifiable $assignee)
        {
            if ($assignee instanceof \thebuggenie\core\entities\Team) {
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

        /**
         * Return the current owner
         *
         * @return common\Identifiable
         */
        public function getOwner()
        {
            $this->_b2dbLazyload('_owner_team');
            $this->_b2dbLazyload('_owner_user');

            if ($this->_owner_team instanceof \thebuggenie\core\entities\Team) {
                return $this->_owner_team;
            } elseif ($this->_owner_user instanceof \thebuggenie\core\entities\User) {
                return $this->_owner_user;
            } else {
                return null;
            }
        }

        public function hasOwner()
        {
            return (bool) ($this->getOwner() instanceof \thebuggenie\core\entities\common\Identifiable);
        }

        public function setOwner(\thebuggenie\core\entities\common\Identifiable $owner)
        {
            if ($owner instanceof \thebuggenie\core\entities\Team) {
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

        /**
         * Return an arary of subscribed users
         *
         * @return array|User
         */
        public function getSubscribers()
        {
            $this->_b2dbLazyload('_subscribers');
            return $this->_subscribers;
        }

        public function addSubscriber($user_id)
        {
            tables\UserIssues::getTable()->addStarredIssue($user_id, $this->getID());
        }

        /**
         * Return an array of users available for mention autocompletion
         *
         * @return array|User
         */
        public function getMentionableUsers()
        {
            $users = array();
            foreach ($this->getRelatedUsers() as $user)
            {
                $users[$user->getID()] = $user;
            }
            foreach ($this->getComments() as $comment)
            {
                $users[$comment->getPostedBy()->getID()] = $comment->getPostedBy();
                foreach ($comment->getMentions() as $user)
                {
                    $users[$user->getID()] = $user;
                }
            }

            return $users;
        }

        public function getMilestoneOrder()
        {
            return $this->_milestone_order;
        }

    }
