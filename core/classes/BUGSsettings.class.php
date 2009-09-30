<?php

	/**
	 * Settings class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage core
	 */

	/**
	 * Settings class
	 *
	 * @package thebuggenie
	 * @subpackage core
	 */
	final class BUGSsettings
	{
		static protected $sql_type;
		static protected $sql_link;
		static protected $ver_mj;
		static protected $ver_mn;
		static protected $ver_rev;
		static protected $ver_name;
		static protected $db_host;
		static protected $db_uname;
		static protected $db_passwd;
		static protected $db_dbname;
		static protected $theme_name;
		static protected $followups;
		static protected $url_host;
		static protected $url_subdir;
		static protected $returnfromlogin;
		static protected $onlinestate;
		static protected $offlinestate;
		static protected $awaystate;
		static protected $defaultuname;
		static protected $defaultpwd;
		static protected $defaultisguest;
		static protected $defaultgroup;
		static protected $defaultscope;
		static protected $defaultindexshowsavedsearches;
		static protected $defaultindexsavedsearches;
		static protected $indexshowsavedsearches;
		static protected $indexsavedsearches;
		static protected $_settings = null;
		static protected $b2_url;
		static protected $projects = array();
		static protected $editions = array();
		static protected $builds = array();
		static protected $components = array();
	
		public static function loadSettings()
		{
			if (self::$_settings === null)
			{
				BUGSlogging::log('Loading all settings');
				self::$_settings = array();
				self::$ver_mj = 2;
				self::$ver_mn = 1;
				self::$ver_rev = '0 early alpha';
				self::$ver_name = 'Turning point';
				if (self::$_settings = BUGScache::get('settings'))
				{
					BUGSlogging::log('Using cached settings');
				}
				else
				{
					BUGSlogging::log('Settings not cached. Retrieving from database');
					$crit = new B2DBCriteria();
					$crit->addWhere(B2tScopes::ENABLED, 1);
					$crit->addWhere(B2tSettings::SCOPE, BUGScontext::getScope()->getID());
					if ($res = B2DB::getTable('B2tSettings')->doSelect($crit))
					{
						while ($row = $res->getNextRow())
						{
							self::$_settings[$row->get(B2tSettings::MODULE)][$row->get(B2tSettings::NAME)][$row->get(B2tSettings::UID)] = $row->get(B2tSettings::VALUE);
						}
					}
					BUGSlogging::log('Retrieved');
					BUGScache::add('settings', self::$_settings);
				}
			}
		}
		
		public static function loadFixtures($scope = 1)
		{
			$i18n = BUGScontext::getI18n();
			
			$b2_settings = array('theme_name' => 'oxygen', 
								'url_host' => '', 
								'url_subdir' => '', 
								'requirelogin' => 0,
								'defaultisguest' => 1,
								'showloginbox' => 1,
								'allowreg' => 1,
								'returnfromlogin' => 'dashboard',
								'returnfromlogout' => 'home', 
								'defaultuname' => 'guest', 
								'onlinestate' => 1, 
								'offlinestate' => 2,
								'awaystate' => 8, 
								'showprojectsoverview' => 1, 
								'userthemes' => 0, 
								'b2_name' => 'The Bug Genie',
								'b2_tagline' => __('<b>Friendly</b> issue tracking and project management'));
			
			if ($scope == 1)
			{
				$b2_settings['defaultgroup'] = 2;
			}
			
			foreach ($b2_settings as $b2_settings_name => $b2_settings_val)
			{
				self::saveSetting($b2_settings_name, $b2_settings_val, 'core', $scope);
			}
	
			$basecrit = new B2DBCriteria();
			$basecrit->addInsert(B2tPermissionsList::SCOPE, $scope);
		
			$crit = clone $basecrit;
			$crit->addInsert(B2tPermissionsList::LEVELS, 2);
			$crit->addInsert(B2tPermissionsList::APPLIES_TO, 'user');
			$crit->addInsert(B2tPermissionsList::PERMISSION_NAME, 'b2canonlyviewownissues');
			$crit->addInsert(B2tPermissionsList::DESCRIPTION, __('Can only view issues reported by the user'));
			B2DB::getTable('B2tPermissionsList')->doInsert($crit);	
			
			$crit = clone $basecrit;
			$crit->addInsert(B2tPermissionsList::LEVELS, 2);
			$crit->addInsert(B2tPermissionsList::APPLIES_TO, 'general');
			$crit->addInsert(B2tPermissionsList::PERMISSION_NAME, 'b2canreadallcomments');
			$crit->addInsert(B2tPermissionsList::DESCRIPTION, __('Can view comments that are not public'));
			B2DB::getTable('B2tPermissionsList')->doInsert($crit);	
			
			$crit = clone $basecrit;
			$crit->addInsert(B2tPermissionsList::LEVELS, 2);
			$crit->addInsert(B2tPermissionsList::APPLIES_TO, 'general');
			$crit->addInsert(B2tPermissionsList::PERMISSION_NAME, 'b2addlinks');
			$crit->addInsert(B2tPermissionsList::DESCRIPTION, __('Can add links to issue reports'));
			B2DB::getTable('B2tPermissionsList')->doInsert($crit);	
			
			$crit = clone $basecrit;
			$crit->addInsert(B2tPermissionsList::LEVELS, 2);
			$crit->addInsert(B2tPermissionsList::APPLIES_TO, 'general');
			$crit->addInsert(B2tPermissionsList::PERMISSION_NAME, 'b2uploadfiles');
			$crit->addInsert(B2tPermissionsList::DESCRIPTION, __('Can add files to issue reports'));
			B2DB::getTable('B2tPermissionsList')->doInsert($crit);	
			
			$crit = clone $basecrit;
			$crit->addInsert(B2tPermissionsList::LEVELS, 2);
			$crit->addInsert(B2tPermissionsList::APPLIES_TO, 'general');
			$crit->addInsert(B2tPermissionsList::PERMISSION_NAME, 'b2canfindissues');
			$crit->addInsert(B2tPermissionsList::DESCRIPTION, __('Can search for issues'));
			B2DB::getTable('B2tPermissionsList')->doInsert($crit);	
			
			$crit = clone $basecrit;
			$crit->addInsert(B2tPermissionsList::LEVELS, 2);
			$crit->addInsert(B2tPermissionsList::APPLIES_TO, 'projects');
			$crit->addInsert(B2tPermissionsList::PERMISSION_NAME, 'b2canvote');
			$crit->addInsert(B2tPermissionsList::DESCRIPTION, __('Can vote for issues'));
			B2DB::getTable('B2tPermissionsList')->doInsert($crit);	
		
			$crit = clone $basecrit;
			$crit->addInsert(B2tPermissionsList::LEVELS, 2);
			$crit->addInsert(B2tPermissionsList::APPLIES_TO, 'projects');
			$crit->addInsert(B2tPermissionsList::PERMISSION_NAME, 'b2candeleteissues');
			$crit->addInsert(B2tPermissionsList::DESCRIPTION, __('Can delete issues'));
			B2DB::getTable('B2tPermissionsList')->doInsert($crit);	
		
			$crit = clone $basecrit;
			$crit->addInsert(B2tPermissionsList::LEVELS, 2);
			$crit->addInsert(B2tPermissionsList::APPLIES_TO, 'projects');
			$crit->addInsert(B2tPermissionsList::PERMISSION_NAME, 'b2caneditissuefields');
			$crit->addInsert(B2tPermissionsList::DESCRIPTION, __('Can update issue details'));
			B2DB::getTable('B2tPermissionsList')->doInsert($crit);	
			
			$crit = clone $basecrit;
			$crit->addInsert(B2tPermissionsList::LEVELS, 2);
			$crit->addInsert(B2tPermissionsList::APPLIES_TO, 'projects');
			$crit->addInsert(B2tPermissionsList::PERMISSION_NAME, 'b2caneditissueusers');
			$crit->addInsert(B2tPermissionsList::DESCRIPTION, __('Can assign issues'));
			B2DB::getTable('B2tPermissionsList')->doInsert($crit);	
			
			$crit = clone $basecrit;
			$crit->addInsert(B2tPermissionsList::LEVELS, 2);
			$crit->addInsert(B2tPermissionsList::APPLIES_TO, 'projects');
			$crit->addInsert(B2tPermissionsList::PERMISSION_NAME, 'b2caneditissuetext');
			$crit->addInsert(B2tPermissionsList::DESCRIPTION, __('Can edit issue text'));
			B2DB::getTable('B2tPermissionsList')->doInsert($crit);	
			
			$crit = clone $basecrit;
			$crit->addInsert(B2tPermissionsList::LEVELS, 4);
			$crit->addInsert(B2tPermissionsList::APPLIES_TO, 'projects');
			$crit->addInsert(B2tPermissionsList::PERMISSION_NAME, 'b2caneditcomments');
			$crit->addInsert(B2tPermissionsList::DESCRIPTION, __('Can edit all comments'));
			B2DB::getTable('B2tPermissionsList')->doInsert($crit);	
			
			$crit = clone $basecrit;
			$crit->addInsert(B2tPermissionsList::LEVELS, 4);
			$crit->addInsert(B2tPermissionsList::APPLIES_TO, 'projects');
			$crit->addInsert(B2tPermissionsList::PERMISSION_NAME, 'b2canaddcomments');
			$crit->addInsert(B2tPermissionsList::DESCRIPTION, __('Can add comments'));
			B2DB::getTable('B2tPermissionsList')->doInsert($crit);	
			
			$crit = clone $basecrit;
			$crit->addInsert(B2tPermissionsList::LEVELS, 4);
			$crit->addInsert(B2tPermissionsList::APPLIES_TO, 'projects');
			$crit->addInsert(B2tPermissionsList::PERMISSION_NAME, 'b2canviewcomments');
			$crit->addInsert(B2tPermissionsList::DESCRIPTION, __('Can view comments'));
			B2DB::getTable('B2tPermissionsList')->doInsert($crit);	
			
			$crit = clone $basecrit;
			$crit->addInsert(B2tPermissionsList::LEVELS, 2);
			$crit->addInsert(B2tPermissionsList::APPLIES_TO, 'issues');
			$crit->addInsert(B2tPermissionsList::PERMISSION_NAME, 'b2noteditcomments');
			$crit->addInsert(B2tPermissionsList::DESCRIPTION, __('Can not edit comments'));
			B2DB::getTable('B2tPermissionsList')->doInsert($crit);	
			
			$crit = clone $basecrit;
			$crit->addInsert(B2tPermissionsList::LEVELS, 2);
			$crit->addInsert(B2tPermissionsList::APPLIES_TO, 'issues');
			$crit->addInsert(B2tPermissionsList::PERMISSION_NAME, 'b2notaddcomments');
			$crit->addInsert(B2tPermissionsList::DESCRIPTION, __('Can not add comments'));
			B2DB::getTable('B2tPermissionsList')->doInsert($crit);	
			
			$crit = clone $basecrit;
			$crit->addInsert(B2tPermissionsList::LEVELS, 2);
			$crit->addInsert(B2tPermissionsList::APPLIES_TO, 'issues');
			$crit->addInsert(B2tPermissionsList::PERMISSION_NAME, 'b2hidecomments');
			$crit->addInsert(B2tPermissionsList::DESCRIPTION, __('Hide comments'));
			B2DB::getTable('B2tPermissionsList')->doInsert($crit);	
			
			$crit = clone $basecrit;
			$crit->addInsert(B2tPermissionsList::LEVELS, 2);
			$crit->addInsert(B2tPermissionsList::APPLIES_TO, 'issues');
			$crit->addInsert(B2tPermissionsList::PERMISSION_NAME, 'b2cantvote');
			$crit->addInsert(B2tPermissionsList::DESCRIPTION, __('Restrict voting'));
			B2DB::getTable('B2tPermissionsList')->doInsert($crit);	
		
			$crit = clone $basecrit;
			$crit->addInsert(B2tPermissionsList::LEVELS, 2);
			$crit->addInsert(B2tPermissionsList::APPLIES_TO, 'projects');
			$crit->addInsert(B2tPermissionsList::PERMISSION_NAME, 'b2canaddbuilds');
			$crit->addInsert(B2tPermissionsList::DESCRIPTION, __('Can add builds to list of affected builds'));
			B2DB::getTable('B2tPermissionsList')->doInsert($crit);	
			
			$crit = clone $basecrit;
			$crit->addInsert(B2tPermissionsList::LEVELS, 2);
			$crit->addInsert(B2tPermissionsList::APPLIES_TO, 'projects');
			$crit->addInsert(B2tPermissionsList::PERMISSION_NAME, 'b2canaddcomponents');
			$crit->addInsert(B2tPermissionsList::DESCRIPTION, __('Can add components to list of affected components'));
			B2DB::getTable('B2tPermissionsList')->doInsert($crit);	
			
			$crit = new B2DBCriteria();
			$crit->addInsert(B2tUserState::STATE_NAME, __('Available'));
			$crit->addInsert(B2tUserState::SCOPE, $scope);
			$crit->addInsert(B2tUserState::UNAVAILABLE, 0);
			$crit->addInsert(B2tUserState::BUSY, 0);
			$crit->addInsert(B2tUserState::ONLINE, 1);
			$crit->addInsert(B2tUserState::MEETING, 0);
			$crit->addInsert(B2tUserState::ABSENT, 0);
			B2DB::getTable('B2tUserState')->doInsert($crit);
			
			$crit = new B2DBCriteria();
			$crit->addInsert(B2tUserState::STATE_NAME, __('Offline'));
			$crit->addInsert(B2tUserState::SCOPE, $scope);
			$crit->addInsert(B2tUserState::UNAVAILABLE, 1);
			$crit->addInsert(B2tUserState::BUSY, 0);
			$crit->addInsert(B2tUserState::ONLINE, 0);
			$crit->addInsert(B2tUserState::MEETING, 0);
			$crit->addInsert(B2tUserState::ABSENT, 0);
			B2DB::getTable('B2tUserState')->doInsert($crit);
		
			$crit = new B2DBCriteria();
			$crit->addInsert(B2tUserState::STATE_NAME, __('Busy'));
			$crit->addInsert(B2tUserState::SCOPE, $scope);
			$crit->addInsert(B2tUserState::UNAVAILABLE, 0);
			$crit->addInsert(B2tUserState::BUSY, 1);
			$crit->addInsert(B2tUserState::ONLINE, 1);
			$crit->addInsert(B2tUserState::MEETING, 0);
			$crit->addInsert(B2tUserState::ABSENT, 0);
			B2DB::getTable('B2tUserState')->doInsert($crit);
		
			$crit = new B2DBCriteria();
			$crit->addInsert(B2tUserState::STATE_NAME, __('Unavailable'));
			$crit->addInsert(B2tUserState::SCOPE, $scope);
			$crit->addInsert(B2tUserState::UNAVAILABLE, 1);
			$crit->addInsert(B2tUserState::BUSY, 0);
			$crit->addInsert(B2tUserState::ONLINE, 1);
			$crit->addInsert(B2tUserState::MEETING, 0);
			$crit->addInsert(B2tUserState::ABSENT, 0);
			B2DB::getTable('B2tUserState')->doInsert($crit);
			
			$crit = new B2DBCriteria();
			$crit->addInsert(B2tUserState::STATE_NAME, __('In a meeting'));
			$crit->addInsert(B2tUserState::SCOPE, $scope);
			$crit->addInsert(B2tUserState::UNAVAILABLE, 1);
			$crit->addInsert(B2tUserState::BUSY, 1);
			$crit->addInsert(B2tUserState::ONLINE, 1);
			$crit->addInsert(B2tUserState::MEETING, 1);
			$crit->addInsert(B2tUserState::ABSENT, 0);
			B2DB::getTable('B2tUserState')->doInsert($crit);
			
			$crit = new B2DBCriteria();
			$crit->addInsert(B2tUserState::STATE_NAME, __('Coding'));
			$crit->addInsert(B2tUserState::SCOPE, $scope);
			$crit->addInsert(B2tUserState::UNAVAILABLE, 0);
			$crit->addInsert(B2tUserState::BUSY, 1);
			$crit->addInsert(B2tUserState::ONLINE, 1);
			$crit->addInsert(B2tUserState::MEETING, 0);
			$crit->addInsert(B2tUserState::ABSENT, 0);
			B2DB::getTable('B2tUserState')->doInsert($crit);
		
			$crit = new B2DBCriteria();
			$crit->addInsert(B2tUserState::STATE_NAME, __('On coffee break'));
			$crit->addInsert(B2tUserState::SCOPE, $scope);
			$crit->addInsert(B2tUserState::UNAVAILABLE, 1);
			$crit->addInsert(B2tUserState::BUSY, 1);
			$crit->addInsert(B2tUserState::ONLINE, 1);
			$crit->addInsert(B2tUserState::MEETING, 0);
			$crit->addInsert(B2tUserState::ABSENT, 0);
			B2DB::getTable('B2tUserState')->doInsert($crit);
			
			$crit = new B2DBCriteria();
			$crit->addInsert(B2tUserState::STATE_NAME, __('Away'));
			$crit->addInsert(B2tUserState::SCOPE, $scope);
			$crit->addInsert(B2tUserState::UNAVAILABLE, 1);
			$crit->addInsert(B2tUserState::BUSY, 1);
			$crit->addInsert(B2tUserState::ONLINE, 1);
			$crit->addInsert(B2tUserState::MEETING, 0);
			$crit->addInsert(B2tUserState::ABSENT, 1);
			B2DB::getTable('B2tUserState')->doInsert($crit);
			
			$crit = new B2DBCriteria();
			$crit->addInsert(B2tUserState::STATE_NAME, __('On vacation'));
			$crit->addInsert(B2tUserState::SCOPE, $scope);
			$crit->addInsert(B2tUserState::UNAVAILABLE, 1);
			$crit->addInsert(B2tUserState::BUSY, 1);
			$crit->addInsert(B2tUserState::ONLINE, 0);
			$crit->addInsert(B2tUserState::MEETING, 0);
			$crit->addInsert(B2tUserState::ABSENT, 1);
			B2DB::getTable('B2tUserState')->doInsert($crit);
		
			$crit = new B2DBCriteria();
			$crit->addInsert(B2tIssueTypes::NAME, __('Bug report'));
			$crit->addInsert(B2tIssueTypes::SCOPE, $scope);
			$crit->addInsert(B2tIssueTypes::ICON, 'bug_report');
			$crit->addInsert(B2tIssueTypes::DESCRIPTION, __('Have you discovered a bug in the application, or is something not working as expected?'));
			$res = B2DB::getTable('B2tIssueTypes')->doInsert($crit);
			$issue_type_bug_report_id = $res->getInsertID();
			self::saveSetting('defaultissuetypefornewissues', $issue_type_bug_report_id, 'core', $scope);
			
			$crit = new B2DBCriteria();
			$crit->addInsert(B2tIssueTypes::NAME, __('Feature request'));
			$crit->addInsert(B2tIssueTypes::ICON, 'feature_request');
			$crit->addInsert(B2tIssueTypes::DESCRIPTION, __('Are you missing some specific feature, or is your favourite part of the application a bit lacking?'));
			$crit->addInsert(B2tIssueTypes::SCOPE, $scope);
			$res = B2DB::getTable('B2tIssueTypes')->doInsert($crit);
			$issue_type_feature_request_id = $res->getInsertID();
			
			$crit = new B2DBCriteria();
			$crit->addInsert(B2tIssueTypes::NAME, __('Enhancement'));
			$crit->addInsert(B2tIssueTypes::ICON, 'enhancement');
			$crit->addInsert(B2tIssueTypes::DESCRIPTION, __('Have you found something that is working in a way that could be improved?'));
			$crit->addInsert(B2tIssueTypes::SCOPE, $scope);
			$res = B2DB::getTable('B2tIssueTypes')->doInsert($crit);
			$issue_type_enhancement_id = $res->getInsertID();
						
			$crit = new B2DBCriteria();
			$crit->addInsert(B2tIssueTypes::NAME, __('Task'));
			$crit->addInsert(B2tIssueTypes::ICON, 'task');
			$crit->addInsert(B2tIssueTypes::IS_TASK, 1);
			$crit->addInsert(B2tIssueTypes::SCOPE, $scope);
			$res = B2DB::getTable('B2tIssueTypes')->doInsert($crit);
			$issue_type_task_id = $res->getInsertID();
						
			$crit = new B2DBCriteria();
			$crit->addInsert(B2tIssueTypes::NAME, __('User story'));
			$crit->addInsert(B2tIssueTypes::ICON, 'developer_report');
			$crit->addInsert(B2tIssueTypes::DESCRIPTION, __('Doing it Scrum-style. Issue type perfectly suited for entering user stories'));
			$crit->addInsert(B2tIssueTypes::REDIRECT_AFTER_REPORTING, false);
			$crit->addInsert(B2tIssueTypes::SCOPE, $scope);
			$res = B2DB::getTable('B2tIssueTypes')->doInsert($crit);
			$issue_type_user_story_id = $res->getInsertID();
						
			$crit = new B2DBCriteria();
			$crit->addInsert(B2tIssueFields::ISSUETYPE_ID, $issue_type_bug_report_id);
			$crit->addInsert(B2tIssueFields::FIELD_KEY, 'description');
			$crit->addInsert(B2tIssueFields::REPORTABLE, true);
			$crit->addInsert(B2tIssueFields::REQUIRED, true);
			$crit->addInsert(B2tIssueFields::SCOPE, $scope);
			B2DB::getTable('B2tIssueFields')->doInsert($crit);

			$crit = new B2DBCriteria();
			$crit->addInsert(B2tIssueFields::ISSUETYPE_ID, $issue_type_bug_report_id);
			$crit->addInsert(B2tIssueFields::FIELD_KEY, 'reproduction_steps');
			$crit->addInsert(B2tIssueFields::REPORTABLE, true);
			$crit->addInsert(B2tIssueFields::REQUIRED, true);
			$crit->addInsert(B2tIssueFields::SCOPE, $scope);
			B2DB::getTable('B2tIssueFields')->doInsert($crit);

			$crit = new B2DBCriteria();
			$crit->addInsert(B2tIssueFields::ISSUETYPE_ID, $issue_type_bug_report_id);
			$crit->addInsert(B2tIssueFields::FIELD_KEY, 'edition');
			$crit->addInsert(B2tIssueFields::REPORTABLE, true);
			$crit->addInsert(B2tIssueFields::REQUIRED, true);
			$crit->addInsert(B2tIssueFields::SCOPE, $scope);
			B2DB::getTable('B2tIssueFields')->doInsert($crit);
			
			$crit = new B2DBCriteria();
			$crit->addInsert(B2tIssueFields::ISSUETYPE_ID, $issue_type_bug_report_id);
			$crit->addInsert(B2tIssueFields::FIELD_KEY, 'build');
			$crit->addInsert(B2tIssueFields::REPORTABLE, true);
			$crit->addInsert(B2tIssueFields::REQUIRED, true);
			$crit->addInsert(B2tIssueFields::SCOPE, $scope);
			B2DB::getTable('B2tIssueFields')->doInsert($crit);
			
			$crit = new B2DBCriteria();
			$crit->addInsert(B2tIssueFields::ISSUETYPE_ID, $issue_type_bug_report_id);
			$crit->addInsert(B2tIssueFields::FIELD_KEY, 'component');
			$crit->addInsert(B2tIssueFields::REPORTABLE, true);
			$crit->addInsert(B2tIssueFields::SCOPE, $scope);
			B2DB::getTable('B2tIssueFields')->doInsert($crit);

			$crit = new B2DBCriteria();
			$crit->addInsert(B2tIssueFields::ISSUETYPE_ID, $issue_type_bug_report_id);
			$crit->addInsert(B2tIssueFields::FIELD_KEY, 'category');
			$crit->addInsert(B2tIssueFields::REPORTABLE, true);
			$crit->addInsert(B2tIssueFields::SCOPE, $scope);
			B2DB::getTable('B2tIssueFields')->doInsert($crit);

			$crit = new B2DBCriteria();
			$crit->addInsert(B2tIssueFields::ISSUETYPE_ID, $issue_type_bug_report_id);
			$crit->addInsert(B2tIssueFields::FIELD_KEY, 'reproducability');
			$crit->addInsert(B2tIssueFields::REPORTABLE, true);
			$crit->addInsert(B2tIssueFields::SCOPE, $scope);
			B2DB::getTable('B2tIssueFields')->doInsert($crit);

			$crit = new B2DBCriteria();
			$crit->addInsert(B2tIssueFields::ISSUETYPE_ID, $issue_type_bug_report_id);
			$crit->addInsert(B2tIssueFields::FIELD_KEY, 'resolution');
			$crit->addInsert(B2tIssueFields::REPORTABLE, true);
			$crit->addInsert(B2tIssueFields::ADDITIONAL, true);
			$crit->addInsert(B2tIssueFields::SCOPE, $scope);
			B2DB::getTable('B2tIssueFields')->doInsert($crit);
			
			$crit = new B2DBCriteria();
			$crit->addInsert(B2tIssueFields::ISSUETYPE_ID, $issue_type_bug_report_id);
			$crit->addInsert(B2tIssueFields::FIELD_KEY, 'severity');
			$crit->addInsert(B2tIssueFields::REPORTABLE, true);
			$crit->addInsert(B2tIssueFields::ADDITIONAL, true);
			$crit->addInsert(B2tIssueFields::SCOPE, $scope);
			B2DB::getTable('B2tIssueFields')->doInsert($crit);
			
			$crit = new B2DBCriteria();
			$crit->addInsert(B2tIssueFields::ISSUETYPE_ID, $issue_type_bug_report_id);
			$crit->addInsert(B2tIssueFields::FIELD_KEY, 'milestone');
			$crit->addInsert(B2tIssueFields::REPORTABLE, true);
			$crit->addInsert(B2tIssueFields::ADDITIONAL, true);
			$crit->addInsert(B2tIssueFields::SCOPE, $scope);
			B2DB::getTable('B2tIssueFields')->doInsert($crit);
			
			$crit = new B2DBCriteria();
			$crit->addInsert(B2tIssueFields::ISSUETYPE_ID, $issue_type_bug_report_id);
			$crit->addInsert(B2tIssueFields::FIELD_KEY, 'estimated_time');
			$crit->addInsert(B2tIssueFields::REPORTABLE, true);
			$crit->addInsert(B2tIssueFields::ADDITIONAL, true);
			$crit->addInsert(B2tIssueFields::SCOPE, $scope);
			B2DB::getTable('B2tIssueFields')->doInsert($crit);
			
			$crit = new B2DBCriteria();
			$crit->addInsert(B2tIssueFields::ISSUETYPE_ID, $issue_type_bug_report_id);
			$crit->addInsert(B2tIssueFields::FIELD_KEY, 'spent_time');
			$crit->addInsert(B2tIssueFields::SCOPE, $scope);
			B2DB::getTable('B2tIssueFields')->doInsert($crit);
			
			$crit = new B2DBCriteria();
			$crit->addInsert(B2tIssueFields::ISSUETYPE_ID, $issue_type_bug_report_id);
			$crit->addInsert(B2tIssueFields::FIELD_KEY, 'percentcomplete');
			$crit->addInsert(B2tIssueFields::REPORTABLE, true);
			$crit->addInsert(B2tIssueFields::ADDITIONAL, true);
			$crit->addInsert(B2tIssueFields::SCOPE, $scope);
			B2DB::getTable('B2tIssueFields')->doInsert($crit);
			
			$crit = new B2DBCriteria();
			$crit->addInsert(B2tIssueFields::ISSUETYPE_ID, $issue_type_bug_report_id);
			$crit->addInsert(B2tIssueFields::FIELD_KEY, 'priority');
			$crit->addInsert(B2tIssueFields::REPORTABLE, true);
			$crit->addInsert(B2tIssueFields::ADDITIONAL, true);
			$crit->addInsert(B2tIssueFields::SCOPE, $scope);
			B2DB::getTable('B2tIssueFields')->doInsert($crit);
			
			$crit = new B2DBCriteria();
			$crit->addInsert(B2tIssueFields::ISSUETYPE_ID, $issue_type_feature_request_id);
			$crit->addInsert(B2tIssueFields::FIELD_KEY, 'description');
			$crit->addInsert(B2tIssueFields::REPORTABLE, true);
			$crit->addInsert(B2tIssueFields::REQUIRED, true);
			$crit->addInsert(B2tIssueFields::SCOPE, $scope);
			B2DB::getTable('B2tIssueFields')->doInsert($crit);

			$crit = new B2DBCriteria();
			$crit->addInsert(B2tIssueFields::ISSUETYPE_ID, $issue_type_feature_request_id);
			$crit->addInsert(B2tIssueFields::FIELD_KEY, 'milestone');
			$crit->addInsert(B2tIssueFields::REPORTABLE, true);
			$crit->addInsert(B2tIssueFields::ADDITIONAL, true);
			$crit->addInsert(B2tIssueFields::SCOPE, $scope);
			B2DB::getTable('B2tIssueFields')->doInsert($crit);
			
			$crit = new B2DBCriteria();
			$crit->addInsert(B2tIssueFields::ISSUETYPE_ID, $issue_type_feature_request_id);
			$crit->addInsert(B2tIssueFields::FIELD_KEY, 'category');
			$crit->addInsert(B2tIssueFields::REPORTABLE, true);
			$crit->addInsert(B2tIssueFields::ADDITIONAL, true);
			$crit->addInsert(B2tIssueFields::SCOPE, $scope);
			B2DB::getTable('B2tIssueFields')->doInsert($crit);
			
			$crit = new B2DBCriteria();
			$crit->addInsert(B2tIssueFields::ISSUETYPE_ID, $issue_type_feature_request_id);
			$crit->addInsert(B2tIssueFields::FIELD_KEY, 'estimated_time');
			$crit->addInsert(B2tIssueFields::REPORTABLE, true);
			$crit->addInsert(B2tIssueFields::ADDITIONAL, true);
			$crit->addInsert(B2tIssueFields::SCOPE, $scope);
			B2DB::getTable('B2tIssueFields')->doInsert($crit);
			
			$crit = new B2DBCriteria();
			$crit->addInsert(B2tIssueFields::ISSUETYPE_ID, $issue_type_feature_request_id);
			$crit->addInsert(B2tIssueFields::FIELD_KEY, 'spent_time');
			$crit->addInsert(B2tIssueFields::SCOPE, $scope);
			B2DB::getTable('B2tIssueFields')->doInsert($crit);
			
			$crit = new B2DBCriteria();
			$crit->addInsert(B2tIssueFields::ISSUETYPE_ID, $issue_type_feature_request_id);
			$crit->addInsert(B2tIssueFields::FIELD_KEY, 'percent_complete');
			$crit->addInsert(B2tIssueFields::REPORTABLE, true);
			$crit->addInsert(B2tIssueFields::ADDITIONAL, true);
			$crit->addInsert(B2tIssueFields::SCOPE, $scope);
			B2DB::getTable('B2tIssueFields')->doInsert($crit);
			
			$crit = new B2DBCriteria();
			$crit->addInsert(B2tIssueFields::ISSUETYPE_ID, $issue_type_feature_request_id);
			$crit->addInsert(B2tIssueFields::FIELD_KEY, 'priority');
			$crit->addInsert(B2tIssueFields::REPORTABLE, true);
			$crit->addInsert(B2tIssueFields::ADDITIONAL, true);
			$crit->addInsert(B2tIssueFields::SCOPE, $scope);
			B2DB::getTable('B2tIssueFields')->doInsert($crit);
			
			$crit = new B2DBCriteria();
			$crit->addInsert(B2tIssueFields::ISSUETYPE_ID, $issue_type_enhancement_id);
			$crit->addInsert(B2tIssueFields::FIELD_KEY, 'description');
			$crit->addInsert(B2tIssueFields::REPORTABLE, true);
			$crit->addInsert(B2tIssueFields::REQUIRED, true);
			$crit->addInsert(B2tIssueFields::SCOPE, $scope);
			B2DB::getTable('B2tIssueFields')->doInsert($crit);
			
			$crit = new B2DBCriteria();
			$crit->addInsert(B2tIssueFields::ISSUETYPE_ID, $issue_type_enhancement_id);
			$crit->addInsert(B2tIssueFields::FIELD_KEY, 'milestone');
			$crit->addInsert(B2tIssueFields::REPORTABLE, true);
			$crit->addInsert(B2tIssueFields::ADDITIONAL, true);
			$crit->addInsert(B2tIssueFields::SCOPE, $scope);
			B2DB::getTable('B2tIssueFields')->doInsert($crit);
			
			$crit = new B2DBCriteria();
			$crit->addInsert(B2tIssueFields::ISSUETYPE_ID, $issue_type_enhancement_id);
			$crit->addInsert(B2tIssueFields::FIELD_KEY, 'category');
			$crit->addInsert(B2tIssueFields::REPORTABLE, true);
			$crit->addInsert(B2tIssueFields::ADDITIONAL, true);
			$crit->addInsert(B2tIssueFields::SCOPE, $scope);
			B2DB::getTable('B2tIssueFields')->doInsert($crit);
			
			$crit = new B2DBCriteria();
			$crit->addInsert(B2tIssueFields::ISSUETYPE_ID, $issue_type_enhancement_id);
			$crit->addInsert(B2tIssueFields::FIELD_KEY, 'estimated_time');
			$crit->addInsert(B2tIssueFields::REPORTABLE, true);
			$crit->addInsert(B2tIssueFields::ADDITIONAL, true);
			$crit->addInsert(B2tIssueFields::SCOPE, $scope);
			B2DB::getTable('B2tIssueFields')->doInsert($crit);
			
			$crit = new B2DBCriteria();
			$crit->addInsert(B2tIssueFields::ISSUETYPE_ID, $issue_type_enhancement_id);
			$crit->addInsert(B2tIssueFields::FIELD_KEY, 'spent_time');
			$crit->addInsert(B2tIssueFields::SCOPE, $scope);
			B2DB::getTable('B2tIssueFields')->doInsert($crit);
			
			$crit = new B2DBCriteria();
			$crit->addInsert(B2tIssueFields::ISSUETYPE_ID, $issue_type_enhancement_id);
			$crit->addInsert(B2tIssueFields::FIELD_KEY, 'percent_complete');
			$crit->addInsert(B2tIssueFields::REPORTABLE, true);
			$crit->addInsert(B2tIssueFields::ADDITIONAL, true);
			$crit->addInsert(B2tIssueFields::SCOPE, $scope);
			B2DB::getTable('B2tIssueFields')->doInsert($crit);
			
			$crit = new B2DBCriteria();
			$crit->addInsert(B2tIssueFields::ISSUETYPE_ID, $issue_type_enhancement_id);
			$crit->addInsert(B2tIssueFields::FIELD_KEY, 'priority');
			$crit->addInsert(B2tIssueFields::REPORTABLE, true);
			$crit->addInsert(B2tIssueFields::ADDITIONAL, true);
			$crit->addInsert(B2tIssueFields::SCOPE, $scope);
			B2DB::getTable('B2tIssueFields')->doInsert($crit);
						
			$crit = new B2DBCriteria();
			$crit->addInsert(B2tIssueFields::ISSUETYPE_ID, $issue_type_task_id);
			$crit->addInsert(B2tIssueFields::FIELD_KEY, 'description');
			$crit->addInsert(B2tIssueFields::REPORTABLE, true);
			$crit->addInsert(B2tIssueFields::SCOPE, $scope);
			B2DB::getTable('B2tIssueFields')->doInsert($crit);
			
			$crit = new B2DBCriteria();
			$crit->addInsert(B2tIssueFields::ISSUETYPE_ID, $issue_type_task_id);
			$crit->addInsert(B2tIssueFields::FIELD_KEY, 'category');
			$crit->addInsert(B2tIssueFields::REPORTABLE, true);
			$crit->addInsert(B2tIssueFields::ADDITIONAL, true);
			$crit->addInsert(B2tIssueFields::SCOPE, $scope);
			B2DB::getTable('B2tIssueFields')->doInsert($crit);
			
			$crit = new B2DBCriteria();
			$crit->addInsert(B2tIssueFields::ISSUETYPE_ID, $issue_type_task_id);
			$crit->addInsert(B2tIssueFields::FIELD_KEY, 'estimated_time');
			$crit->addInsert(B2tIssueFields::REPORTABLE, true);
			$crit->addInsert(B2tIssueFields::ADDITIONAL, true);
			$crit->addInsert(B2tIssueFields::SCOPE, $scope);
			B2DB::getTable('B2tIssueFields')->doInsert($crit);
			
			$crit = new B2DBCriteria();
			$crit->addInsert(B2tIssueFields::ISSUETYPE_ID, $issue_type_task_id);
			$crit->addInsert(B2tIssueFields::FIELD_KEY, 'spent_time');
			$crit->addInsert(B2tIssueFields::SCOPE, $scope);
			B2DB::getTable('B2tIssueFields')->doInsert($crit);
			
			$crit = new B2DBCriteria();
			$crit->addInsert(B2tIssueFields::ISSUETYPE_ID, $issue_type_task_id);
			$crit->addInsert(B2tIssueFields::FIELD_KEY, 'percent_complete');
			$crit->addInsert(B2tIssueFields::REPORTABLE, true);
			$crit->addInsert(B2tIssueFields::ADDITIONAL, true);
			$crit->addInsert(B2tIssueFields::SCOPE, $scope);
			B2DB::getTable('B2tIssueFields')->doInsert($crit);
			
			$crit = new B2DBCriteria();
			$crit->addInsert(B2tIssueFields::ISSUETYPE_ID, $issue_type_task_id);
			$crit->addInsert(B2tIssueFields::FIELD_KEY, 'priority');
			$crit->addInsert(B2tIssueFields::REPORTABLE, true);
			$crit->addInsert(B2tIssueFields::ADDITIONAL, true);
			$crit->addInsert(B2tIssueFields::SCOPE, $scope);
			B2DB::getTable('B2tIssueFields')->doInsert($crit);			
			
			$crit = new B2DBCriteria();
			$crit->addInsert(B2tIssueFields::ISSUETYPE_ID, $issue_type_user_story_id);
			$crit->addInsert(B2tIssueFields::FIELD_KEY, 'description');
			$crit->addInsert(B2tIssueFields::REPORTABLE, true);
			$crit->addInsert(B2tIssueFields::SCOPE, $scope);
			B2DB::getTable('B2tIssueFields')->doInsert($crit);

			$crit = new B2DBCriteria();
			$crit->addInsert(B2tIssueFields::ISSUETYPE_ID, $issue_type_user_story_id);
			$crit->addInsert(B2tIssueFields::FIELD_KEY, 'percent_complete');
			$crit->addInsert(B2tIssueFields::REPORTABLE, true);
			$crit->addInsert(B2tIssueFields::SCOPE, $scope);
			B2DB::getTable('B2tIssueFields')->doInsert($crit);

			$crit = new B2DBCriteria();
			$crit->addInsert(B2tIssueFields::ISSUETYPE_ID, $issue_type_user_story_id);
			$crit->addInsert(B2tIssueFields::FIELD_KEY, 'category');
			$crit->addInsert(B2tIssueFields::REPORTABLE, true);
			$crit->addInsert(B2tIssueFields::SCOPE, $scope);
			B2DB::getTable('B2tIssueFields')->doInsert($crit);
			
			$crit = new B2DBCriteria();
			$crit->addInsert(B2tIssueFields::ISSUETYPE_ID, $issue_type_user_story_id);
			$crit->addInsert(B2tIssueFields::FIELD_KEY, 'milestone');
			$crit->addInsert(B2tIssueFields::REPORTABLE, true);
			$crit->addInsert(B2tIssueFields::SCOPE, $scope);
			B2DB::getTable('B2tIssueFields')->doInsert($crit);
			
			$crit = new B2DBCriteria();
			$crit->addInsert(B2tIssueFields::ISSUETYPE_ID, $issue_type_user_story_id);
			$crit->addInsert(B2tIssueFields::FIELD_KEY, 'estimated_time');
			$crit->addInsert(B2tIssueFields::REPORTABLE, true);
			$crit->addInsert(B2tIssueFields::SCOPE, $scope);
			B2DB::getTable('B2tIssueFields')->doInsert($crit);
			
			$crit = new B2DBCriteria();
			$crit->addInsert(B2tIssueFields::ISSUETYPE_ID, $issue_type_user_story_id);
			$crit->addInsert(B2tIssueFields::FIELD_KEY, 'spent_time');
			$crit->addInsert(B2tIssueFields::REPORTABLE, true);
			$crit->addInsert(B2tIssueFields::SCOPE, $scope);
			B2DB::getTable('B2tIssueFields')->doInsert($crit);
			
			$crit = new B2DBCriteria();
			$crit->addInsert(B2tIssueFields::ISSUETYPE_ID, $issue_type_user_story_id);
			$crit->addInsert(B2tIssueFields::FIELD_KEY, 'priority');
			$crit->addInsert(B2tIssueFields::REPORTABLE, true);
			$crit->addInsert(B2tIssueFields::SCOPE, $scope);
			B2DB::getTable('B2tIssueFields')->doInsert($crit);
			
			$b2_categories = array();
			$b2_categories[__('General category')] = '';
			
			foreach ($b2_categories as $list_name => $list_data)
			{
				$crit = new B2DBCriteria();
				$crit->addInsert(B2tListTypes::ITEMDATA, $list_data);
				$crit->addInsert(B2tListTypes::ITEMTYPE, 'b2_categories');
				$crit->addInsert(B2tListTypes::NAME, $list_name);
				$crit->addInsert(B2tListTypes::SCOPE, $scope);
				B2DB::getTable('B2tListTypes')->doInsert($crit);
			}
			
			$b2_prioritytypes = array();
			$b2_prioritytypes[__('Critical')] = 1;
			$b2_prioritytypes[__('Needs to be fixed')] = 2;
			$b2_prioritytypes[__('Must fix before next release')] = 3;
			$b2_prioritytypes[__('Low')] = 4;
			$b2_prioritytypes[__('Normal')] = 5;
			
			foreach ($b2_prioritytypes as $list_name => $list_data)
			{
				$crit = new B2DBCriteria();
				$crit->addInsert(B2tListTypes::ITEMDATA, $list_data);
				$crit->addInsert(B2tListTypes::ITEMTYPE, 'b2_prioritytypes');
				$crit->addInsert(B2tListTypes::NAME, $list_name);
				$crit->addInsert(B2tListTypes::SCOPE, $scope);
				B2DB::getTable('B2tListTypes')->doInsert($crit);
			}
			
			$b2_reprotypes = array();
			$b2_reprotypes[__("Can't reproduce")] = '';
			$b2_reprotypes[__('Rarely')] = '';
			$b2_reprotypes[__('Often')] = '';
			$b2_reprotypes[__('Always')] = '';
			
			foreach ($b2_reprotypes as $list_name => $list_data)
			{
				$crit = new B2DBCriteria();
				$crit->addInsert(B2tListTypes::ITEMDATA, $list_data);
				$crit->addInsert(B2tListTypes::ITEMTYPE, 'b2_reprotypes');
				$crit->addInsert(B2tListTypes::NAME, $list_name);
				$crit->addInsert(B2tListTypes::SCOPE, $scope);
				B2DB::getTable('B2tListTypes')->doInsert($crit);
			}
			
			$b2_resolutiontypes = array();
			$b2_resolutiontypes[__("CAN'T REPRODUCE")] = '';
			$b2_resolutiontypes[__("WON'T FIX")] = '';
			$b2_resolutiontypes[__("NOT AN ISSUE")] = '';
			$b2_resolutiontypes[__("WILL FIX IN NEXT RELEASE")] = '';
			$b2_resolutiontypes[__("RESOLVED")] = '';
			$b2_resolutiontypes[__("CAN'T FIX")] = '';
			
			foreach ($b2_resolutiontypes as $list_name => $list_data)
			{
				$crit = new B2DBCriteria();
				$crit->addInsert(B2tListTypes::ITEMDATA, $list_data);
				$crit->addInsert(B2tListTypes::ITEMTYPE, 'b2_resolutiontypes');
				$crit->addInsert(B2tListTypes::NAME, $list_name);
				$crit->addInsert(B2tListTypes::SCOPE, $scope);
				B2DB::getTable('B2tListTypes')->doInsert($crit);
			}
		
			$b2_severitylevels = array();
			$b2_severitylevels[__('Low')] = '';
			$b2_severitylevels[__('Normal')] = '';
			$b2_severitylevels[__('Critical')] = '';
			
			$cc = 0;
			foreach ($b2_severitylevels as $list_name => $list_data)
			{
				$cc++;
				$crit = new B2DBCriteria();
				$crit->addInsert(B2tListTypes::ITEMDATA, $list_data);
				$crit->addInsert(B2tListTypes::ITEMTYPE, 'b2_severitylevels');
				$crit->addInsert(B2tListTypes::NAME, $list_name);
				$crit->addInsert(B2tListTypes::SCOPE, $scope);
				$res = B2DB::getTable('B2tListTypes')->doInsert($crit);
				if ($cc == 3)
				{
					self::saveSetting('defaultseverityfornewissues', $res->getInsertID(), 'core', $scope);
				}
			}
			
			$b2_statustypes = array();
			$b2_statustypes[__('Not reviewed')] = '#FFF';
			$b2_statustypes[__('Collecting information')] = '#C2F533';
			$b2_statustypes[__('Confirmed')] = '#FF55AA';
			$b2_statustypes[__('Not a bug')] = '#44FC1D';
			$b2_statustypes[__('Being worked on')] = '#5C5';
			$b2_statustypes[__('Near completion')] = '#7D3';
			$b2_statustypes[__('Ready for QA')] = '#55C';
			$b2_statustypes[__('Testing / QA')] = '#77C';
			$b2_statustypes[__('Closed')] = '#C2F588';
			$b2_statustypes[__('Postponed')] = '#FA5';
			$b2_statustypes[__('Done')] = '#7D3';
			$b2_statustypes[__('Fixed')] = '#5C5';
			
			foreach ($b2_statustypes as $list_name => $list_data)
			{
				$crit = new B2DBCriteria();
				$crit->addInsert(B2tListTypes::ITEMDATA, $list_data);
				$crit->addInsert(B2tListTypes::ITEMTYPE, 'b2_statustypes');
				$crit->addInsert(B2tListTypes::NAME, $list_name);
				$crit->addInsert(B2tListTypes::SCOPE, $scope);
				B2DB::getTable('B2tListTypes')->doInsert($crit);
			}
			
			$crit = new B2DBCriteria();
			$crit->addInsert(B2tLinks::URL, 'http://www.thebuggenie.com');
			$crit->addInsert(B2tLinks::DESCRIPTION, 'The Bug Genie homepage');
			$crit->addInsert(B2tLinks::LINK_ORDER, 1);
			B2DB::getTable('B2tLinks')->doInsert($crit);

			$crit = new B2DBCriteria();
			$crit->addInsert(B2tLinks::URL, 'http://www.thebuggenie.com/forum');
			$crit->addInsert(B2tLinks::DESCRIPTION, 'The Bug Genie forums');
			$crit->addInsert(B2tLinks::LINK_ORDER, 2);
			B2DB::getTable('B2tLinks')->doInsert($crit);

			$crit = new B2DBCriteria();
			$crit->addInsert(B2tLinks::URL, '');
			$crit->addInsert(B2tLinks::DESCRIPTION, '');
			$crit->addInsert(B2tLinks::LINK_ORDER, 3);
			B2DB::getTable('B2tLinks')->doInsert($crit);
			
			$crit = new B2DBCriteria();
			$crit->addInsert(B2tLinks::URL, 'http://www.thebuggenie.com/b2');
			$crit->addInsert(B2tLinks::DESCRIPTION, 'Online issue tracker');
			$crit->addInsert(B2tLinks::LINK_ORDER, 4);
			B2DB::getTable('B2tLinks')->doInsert($crit);
			
		}
	
		public static function saveSetting($name, $value, $module = 'core', $scope = 0, $uid = 0)
		{
			if ($scope == 0 && $name != 'defaultscope' && $module == 'core')
			{
				if (($scope = BUGScontext::getScope()) instanceof BUGSscope)
				{
					$scope = $scope->getID();
				}
				else
				{
					throw new Exception('No scope loaded, cannot autoload it');
				}
			}
			$crit = new B2DBCriteria();
			$crit->addWhere(B2tSettings::NAME, $name);
			$crit->addWhere(B2tSettings::MODULE, $module);
			$crit->addWhere(B2tSettings::UID, $uid);
			$crit->addWhere(B2tSettings::SCOPE, $scope);
			$res = B2DB::getTable('B2tSettings')->doSelectOne($crit);
			if ($res instanceof B2DBRow)
			{
				$theID = $res->get(B2tSettings::ID);
				$crit2 = new B2DBCriteria();
				$crit2->addWhere(B2tSettings::NAME, $name);
				$crit2->addWhere(B2tSettings::MODULE, $module);
				$crit2->addWhere(B2tSettings::UID, $uid);
				$crit2->addWhere(B2tSettings::SCOPE, $scope);
				$crit2->addWhere(B2tSettings::ID, $theID, B2DBCriteria::DB_NOT_EQUALS);
				$res2 = B2DB::getTable('B2tSettings')->doDelete($crit2);
				$crit = new B2DBCriteria();
				$crit->addUpdate(B2tSettings::NAME, $name);
				$crit->addUpdate(B2tSettings::MODULE, $module);
				$crit->addUpdate(B2tSettings::UID, $uid);
				$crit->addUpdate(B2tSettings::VALUE, $value);
				B2DB::getTable('B2tSettings')->doUpdateById($crit, $theID);
			}
			else
			{
				$crit = new B2DBCriteria();
				$crit->addInsert(B2tSettings::NAME, $name);
				$crit->addInsert(B2tSettings::MODULE, $module);
				$crit->addInsert(B2tSettings::VALUE, $value);
				$crit->addInsert(B2tSettings::SCOPE, $scope);
				$crit->addInsert(B2tSettings::UID, $uid);
				B2DB::getTable('B2tSettings')->doInsert($crit);
			}
			if ($scope != 0 && ((!BUGScontext::getScope() instanceof BUGSscope) || $scope == BUGScontext::getScope()->getID()))
			{
				self::$_settings[$module][$name][$uid] = $value;
			}
			BUGScache::delete('settings');
		}
		
		public static function set($name, $value, $uid = 0, $module = 'core')
		{
			self::$_settings[$module][$name][$uid] = $value;
		}
	
		public static function get($name, $module = 'core', $scope = null, $uid = 0)
		{
			if (BUGScontext::isInstallmode() && !BUGScontext::getScope() instanceof BUGSscope)
			{
				return null;
			}
			if ($scope instanceof BUGSscope)
			{
				throw new Exception('Oops!');
			}
			if (!BUGScontext::getScope() instanceof BUGSscope)
			{
				throw new Exception('BUGS 2 is not installed correctly');
			}
			if ($scope != BUGScontext::getScope()->getID() && $scope !== null)
			{
				$setting = self::_loadSetting($name, $module, $scope);
				return $setting[$uid];
			}
			if (self::$_settings === null)
			{
				self::loadSettings();
			}
			if (!is_array(self::$_settings) || !array_key_exists($module, self::$_settings))
			{
				return null;
			}
			if (!array_key_exists($name, self::$_settings[$module]))
			{
				return null;
				//self::$_settings[$name] = self::_loadSetting($name, $module, BUGScontext::getScope()->getID());
			}
			if ($uid !== 0 && array_key_exists($uid, self::$_settings[$module][$name]))
			{
				return self::$_settings[$module][$name][$uid];
			}
			else
			{
				return self::$_settings[$module][$name][$uid];
			}
		}
		
		public static function getVersion($with_codename = false)
		{
			$retvar = self::$ver_mj . '.' . self::$ver_mn . '.' . self::$ver_rev;
			if ($with_codename) $retvar .= ' ("' . self::$ver_name . '")';
			return $retvar;  
		}
		
		/**
		 * Returns the default scope
		 *
		 * @return BUGSscope
		 */
		public static function getDefaultScope()
		{
			if (self::$defaultscope === null)
			{
				$row = B2DB::getTable('B2tSettings')->getDefaultScope();
				self::$defaultscope = BUGSfactory::scopeLab($row->get(B2tSettings::VALUE));
			}
			return self::$defaultscope;
		}
		
		public static function deleteSetting($name, $module = 'core', $value, $scope = 0, $uid = 0)
		{
			if ($scope == 0)
			{
				$scope = BUGScontext::getScope()->getID();
			}
			$crit = new B2DBCriteria();
			$crit->addWhere(B2tSettings::NAME, $name);
			$crit->addWhere(B2tSettings::MODULE, $module);
			$crit->addWhere(B2tSettings::SCOPE, $scope);
			$crit->addWhere(B2tSettings::UID, $uid);
			if ($value != "")
			{
				$crit->addWhere(B2tSettings::VALUE, $value);
			}
			B2DB::getTable('B2tSettings')->doDelete($crit);
			unset(self::$_settings[$name][$uid]);
		}
	
		private static function _loadSetting($name, $module = 'core', $scope = 0)
		{
			$crit = new B2DBCriteria();
			$crit->addWhere(B2tSettings::NAME, $name);
			$crit->addWhere(B2tSettings::MODULE, $module);
			if ($scope == 0)
			{
				throw new Exception('BUGS has not been correctly installed. Please check that the default scope exists');
			}
			$crit->addWhere(B2tSettings::SCOPE, $scope);
			$res = B2DB::getTable('B2tSettings')->doSelect($crit);
			if ($res->count() > 0)
			{
				$retarr = array();
				while ($row = $res->getNextRow())
				{
					$retarr[$row->get(B2tSettings::UID)] = $row->get(B2tSettings::VALUE);
				}
				return $retarr;
			}
			else
			{
				return null;
			}
		}
		
		public static function isRegistrationAllowed()
		{
			return self::isRegistrationEnabled();
		}
		
		public static function isRegistrationEnabled()
		{
			return (bool) self::get('allowreg');
		}
		
		public static function getLanguage()
		{
			return self::get('language');
		}
		
		public static function getCharset()
		{
			return self::get('charset');
		}
		
		public static function getTBGname()
		{
			return self::get('b2_name');
		}
	
		public static function getTBGtagline()
		{
			return self::get('b2_tagline');
		}
		
		public static function isProjectOverviewEnabled()
		{
			return (bool) self::get('showprojectsoverview');
		}
		
		public static function getThemeName()
		{
			return self::get('theme_name');
		}
		
		public static function isUserThemesEnabled()
		{
			return (bool) self::get('user_themes');
		}
		
		public static function isCommentTrailClean()
		{
			return (bool) self::get('cleancomments');
		}
		
		public static function isLoginRequired()
		{
			return (bool) self::get('requirelogin');
		}
		
		public static function showLoginBox()
		{
			return (bool) self::get('showloginbox');
		}
		
		public static function isLoginBoxVisible()
		{
			return self::showLoginBox();
		}
		
		public static function isDefaultUserGuest()
		{
			return (bool) self::get('defaultisguest');
		}
		
		public static function getDefaultUsername()
		{
			return self::get('defaultuname');
		}
		
		public static function allowRegistration()
		{
			return self::isRegistrationAllowed();
		}
		
		public static function getRegistrationDomainWhitelist()
		{
			return self::get('limit_registration');
		}
		
		public static function getDefaultGroup()
		{
			try
			{
				return BUGSfactory::groupLab(self::get('defaultgroup'));
			}
			catch (Exception $e)
			{
				return null;
			}
		}
		
		public static function getLoginReturnRoute()
		{
			return self::get('returnfromlogin');
		}
		
		public static function getLogoutReturnRoute()
		{
			return self::get('returnfromlogout');
		}
		
		public static function getOnlineState()
		{
			try
			{
				return BUGSfactory::userstateLab(self::get('onlinestate'));
			}
			catch (Exception $e)
			{
				return null;
			}
		}
	
		public static function getOfflineState()
		{
			try
			{
				return BUGSfactory::userstateLab(self::get('offlinestate'));
			}
			catch (Exception $e)
			{
				return null;
			}
		}
		
		public static function getAwayState()
		{
			try
			{
				return BUGSfactory::userstateLab(self::get('awaystate'));
			}
			catch (Exception $e)
			{
				return null;
			}
		}
		
		public static function getURLhost()
		{
			return self::get('url_host');
		}
		
		public static function getURLsubdir()
		{
			return self::get('url_subdir');
		}
		
		public static function getLocalPath()
		{
			return self::get('local_path');
		}
		
		public static function getGMToffset()
		{
			return self::get('server_timezone');
		}
		
		public static function getUserTimezone()
		{
			return self::get('timezone', 'core', null, BUGScontext::getUser()->getID());
		}
		
		public static function getAuthenticationMethod()
		{
			return self::get('authentication_method');
		}
		
		public static function isUploadsEnabled()
		{
			return (bool) self::get('enable_uploads');
		}
		
	}
