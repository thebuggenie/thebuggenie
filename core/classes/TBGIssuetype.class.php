<?php

	/**
	 * Issue type class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage core
	 */

	/**
	 * Issue type class
	 *
	 * @package thebuggenie
	 * @subpackage core
	 *
	 * @Table(name="TBGIssueTypesTable")
	 */
	class TBGIssuetype extends TBGKeyable
	{
		
		/**
		 * The name of the object
		 *
		 * @var string
		 * @Column(type="string", length=200)
		 */
		protected $_name;

		/**
		 * If true, is the default issue type when promoting tasks to issues
		 *
		 * @var boolean
		 * @access protected
		 */
		protected $_task = false;

		/**
		 * @Column(type="string", length=100)
		 */
		protected $_icon;

		/**
		 * @Column(type="text")
		 */
		protected $_description;
		
		static $_issuetypes = null;

		public static function loadFixtures(TBGScope $scope)
		{
			$scope_id = $scope->getID();
			
			$bug_report = new TBGIssuetype();
			$bug_report->setName('Bug report');
			$bug_report->setIcon('bug_report');
			$bug_report->setScope($scope_id);
			$bug_report->setDescription('Have you discovered a bug in the application, or is something not working as expected?');
			$bug_report->save();
			TBGSettings::saveSetting('defaultissuetypefornewissues', $bug_report->getID(), 'core', $scope_id);
			TBGSettings::saveSetting('issuetype_bug_report', $bug_report->getID(), 'core', $scope_id);

			$feature_request = new TBGIssuetype();
			$feature_request->setName('Feature request');
			$feature_request->setIcon('feature_request');
			$feature_request->setDescription('Are you missing some specific feature, or is your favourite part of the application a bit lacking?');
			$feature_request->setScope($scope_id);
			$feature_request->save();
			TBGSettings::saveSetting('issuetype_feature_request', $feature_request->getID(), 'core', $scope_id);

			$enhancement = new TBGIssuetype();
			$enhancement->setName('Enhancement');
			$enhancement->setIcon('enhancement');
			$enhancement->setDescription('Have you found something that is working in a way that could be improved?');
			$enhancement->setScope($scope_id);
			$enhancement->save();
			TBGSettings::saveSetting('issuetype_enhancement', $enhancement->getID(), 'core', $scope_id);

			$task = new TBGIssuetype();
			$task->setName('Task');
			$task->setIcon('task');
			$task->setIsTask();
			$task->setScope($scope_id);
			$task->save();
			TBGSettings::saveSetting('issuetype_task', $task->getID(), 'core', $scope_id);

			$user_story = new TBGIssuetype();
			$user_story->setName('User story');
			$user_story->setIcon('developer_report');
			$user_story->setDescription('Doing it Agile-style. Issue type perfectly suited for entering user stories');
			$user_story->setScope($scope_id);
			$user_story->save();
			TBGSettings::saveSetting('issuetype_user_story', $user_story->getID(), 'core', $scope_id);

			$idea = new TBGIssuetype();
			$idea->setName('Idea');
			$idea->setIcon('idea');
			$idea->setDescription('Express yourself - share your ideas with the rest of the team!');
			$idea->setScope($scope_id);
			$idea->save();
			TBGSettings::saveSetting('issuetype_idea', $idea->getID(), 'core', $scope_id);

			return array($bug_report->getID(), $feature_request->getID(), $enhancement->getID(), $task->getID(), $user_story->getID(), $idea->getID());
		}
		
		/**
		 * Returns an array of issue types
		 *
		 * @param integer $scope_id  The ID number of the scope to load issue types from
		 * @return array
		 */
		public static function getAll()
		{
			if (self::$_issuetypes === null)
			{
				self::$_issuetypes = TBGIssueTypesTable::getTable()->getAll();
			}
			return self::$_issuetypes;
		}

		/**
		 * Return an array of available icons
		 *
		 * @return array
		 */
		public static function getIcons()
		{
			$i18n = TBGContext::getI18n();
			$icons = array();
			$icons['bug_report'] = $i18n->__('Bug report');
			$icons['documentation_request'] = $i18n->__('Documentation request');
			$icons['enhancement'] = $i18n->__('Enhancement');
			$icons['feature_request'] = $i18n->__('Feature request');
			$icons['idea'] = $i18n->__('Idea');
			$icons['support_request'] = $i18n->__('Support request');
			$icons['task'] = $i18n->__('Task');
			$icons['developer_report'] = $i18n->__('User story');
			
			return $icons;
		}

		public static function getIssuetypeByKeyish($key)
		{
			foreach (self::getAll() as $issuetype)
			{
				if ($issuetype->getKey() == str_replace(array(' ', '/'), array('', ''), mb_strtolower($key)))
				{
					return $issuetype;
				}
			}
			return null;
		}

		/**
		 * Returns whether or not this issue type is the default for promoting tasks to issues
		 *
		 * @return boolean
		 */
		public function isTask()
		{
			return (bool) $this->_task;
		}
		
		public function setIsTask($val = true)
		{
			$this->_task = (bool) $val;
		}

		public function getIcon()
		{
			return $this->_icon;
		}

		public function setIcon($icon)
		{
			$this->_icon = $icon;
		}
		
		public function getDescription()
		{
			return $this->_description;
		}

		public function setDescription($description)
		{
			$this->_description = $description;
		}

		static function getTask()
		{
			try
			{
				$crit = new \b2db\Criteria();
				$crit->addWhere(TBGIssueTypesTable::ICON, 'task');
				$crit->addWhere(TBGIssueTypesTable::SCOPE, TBGContext::getScope()->getID());
				$row = TBGIssueTypesTable::getTable()->doSelectOne($crit);
				if ($row instanceof \b2db\Row)
				{
					return TBGContext::factory()->TBGIssuetype($row->get(TBGIssueTypesTable::ID), $row);
				}
				else
				{
					throw new Exception("Couldn't find any 'task' issue types");
				}
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}
		
		protected function _preDelete()
		{
			TBGIssuetypeSchemeLinkTable::getTable()->deleteByIssuetypeID($this->getID());
			TBGVisibleIssueTypesTable::getTable()->deleteByIssuetypeID($this->getID());
		}

		protected function _postSave($is_new)
		{
			TBGCache::delete(TBGCache::KEY_TEXTPARSER_ISSUE_REGEX);
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
			$this->_generateKey();
		}

		public function isAssociatedWithAnySchemes()
		{
			return (bool) TBGIssuetypeSchemeLinkTable::getTable()->countByIssuetypeID($this->getID());
		}

	}
	
