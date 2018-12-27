<?php

    namespace thebuggenie\core\entities;

    use thebuggenie\core\entities\common\Keyable;
    use thebuggenie\core\framework;

    /**
     * Issue type class
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage core
     */

    /**
     * Issue type class
     *
     * @package thebuggenie
     * @subpackage core
     *
     * @Table(name="\thebuggenie\core\entities\tables\IssueTypes")
     */
    class Issuetype extends Keyable
    {

        const TYPE_BUG = 'bug_report';
        const TYPE_DOCUMENTATION = 'documentation_request';
        const TYPE_SUPPORT = 'support_request';
        const TYPE_FEATURE = 'feature_request';
        const TYPE_ENHANCEMENT = 'enhancement';
        const TYPE_EPIC = 'epic';
        const TYPE_USER_STORY = 'developer_report';
        const TYPE_TASK = 'task';
        const TYPE_IDEA = 'idea';

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

        public static function loadFixtures(Scope $scope)
        {
            $scope_id = $scope->getID();

            $bug_report = new Issuetype();
            $bug_report->setName('Bug report');
            $bug_report->setIcon('bug_report');
            $bug_report->setScope($scope);
            $bug_report->setDescription('Have you discovered a bug in the application, or is something not working as expected?');
            $bug_report->save();
            framework\Settings::saveSetting(framework\Settings::SETTING_ISSUETYPE_BUG_REPORT, $bug_report->getID(), 'core', $scope_id);

            $feature_request = new Issuetype();
            $feature_request->setName('Feature request');
            $feature_request->setIcon('feature_request');
            $feature_request->setDescription('Are you missing some specific feature, or is your favourite part of the application a bit lacking?');
            $feature_request->setScope($scope);
            $feature_request->save();
            framework\Settings::saveSetting(framework\Settings::SETTING_ISSUETYPE_FEATURE_REQUEST, $feature_request->getID(), 'core', $scope_id);

            $enhancement = new Issuetype();
            $enhancement->setName('Enhancement');
            $enhancement->setIcon('enhancement');
            $enhancement->setDescription('Have you found something that is working in a way that could be improved?');
            $enhancement->setScope($scope);
            $enhancement->save();
            framework\Settings::saveSetting(framework\Settings::SETTING_ISSUETYPE_ENHANCEMENT, $enhancement->getID(), 'core', $scope_id);

            $task = new Issuetype();
            $task->setName('Task');
            $task->setIcon('task');
            $task->setIsTask();
            $task->setScope($scope);
            $task->save();
            framework\Settings::saveSetting(framework\Settings::SETTING_ISSUETYPE_TASK, $task->getID(), 'core', $scope_id);

            $user_story = new Issuetype();
            $user_story->setName('User story');
            $user_story->setIcon('developer_report');
            $user_story->setDescription('Doing it Agile-style. Issue type perfectly suited for entering user stories');
            $user_story->setScope($scope);
            $user_story->save();
            framework\Settings::saveSetting(framework\Settings::SETTING_ISSUETYPE_USER_STORY, $user_story->getID(), 'core', $scope_id);

            $epic = new Issuetype();
            $epic->setName('Epic');
            $epic->setIcon('epic');
            $epic->setDescription('Issue type suited for entering epics');
            $epic->setScope($scope);
            $epic->save();
            framework\Settings::saveSetting(framework\Settings::SETTING_ISSUETYPE_EPIC, $epic->getID(), 'core', $scope_id);

            $idea = new Issuetype();
            $idea->setName('Idea');
            $idea->setIcon('idea');
            $idea->setDescription('Express yourself - share your ideas with the rest of the team!');
            $idea->setScope($scope);
            $idea->save();
            framework\Settings::saveSetting(framework\Settings::SETTING_ISSUETYPE_IDEA, $idea->getID(), 'core', $scope_id);

            return [$bug_report->getID(), $feature_request->getID(), $enhancement->getID(), $task->getID(), $user_story->getID(), $idea->getID(), $epic->getID()];
        }

        public static function getDefaultItems($scope)
        {
            $bug_report = static::getOrCreateByKeyish($scope, 'bugreport', 'Bug report');
            $feature_request = static::getOrCreateByKeyish($scope, 'featurerequest', 'Feature request');
            $enhancement = static::getOrCreateByKeyish($scope, 'enhancement', 'Enhancement');
            $task = static::getOrCreateByKeyish($scope, 'task', 'Task');
            $user_story = static::getOrCreateByKeyish($scope, 'userstory', 'User story');
            $idea = static::getOrCreateByKeyish($scope, 'idea', 'Idea');
            $epic = static::getOrCreateByKeyish($scope, 'epic', 'Epic');

            return [$bug_report->getID(), $feature_request->getID(), $enhancement->getID(), $task->getID(), $user_story->getID(), $idea->getID(), $epic->getID()];
        }

        /**
         * Returns an array of issue types
         *
         * @return Issuetype[]
         */
        public static function getAll()
        {
            if (self::$_issuetypes === null)
            {
                self::$_issuetypes = self::getB2DBTable()->getAll();
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
            $i18n = framework\Context::getI18n();
            $icons = array();
            $icons['bug_report'] = $i18n->__('Bug report');
            $icons['documentation_request'] = $i18n->__('Documentation request');
            $icons['enhancement'] = $i18n->__('Enhancement');
            $icons['feature_request'] = $i18n->__('Feature request');
            $icons['idea'] = $i18n->__('Idea');
            $icons['epic'] = $i18n->__('Epic');
            $icons['support_request'] = $i18n->__('Support request');
            $icons['task'] = $i18n->__('Task');
            $icons['developer_report'] = $i18n->__('User story');

            return $icons;
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

        public function getType()
        {
            return $this->_icon;
        }

        public function setType($type)
        {
            $this->_icon = $type;
        }

        public function getIcon()
        {
            return $this->getType();
        }

        public function setIcon($icon)
        {
            $this->setType($icon);
        }

        public function getFontAwesomeIcon()
        {
            switch ($this->getType()) {
                case self::TYPE_BUG:
                    return 'bug';
                case self::TYPE_DOCUMENTATION:
                    return 'pen-alt';
                case self::TYPE_ENHANCEMENT:
                    return 'arrow-up';
                case self::TYPE_EPIC:
                    return 'bookmark';
                case self::TYPE_FEATURE:
                    return 'certificate';
                case self::TYPE_IDEA:
                    return 'lightbulb';
                case self::TYPE_SUPPORT:
                    return 'headset';
                case self::TYPE_TASK:
                    return 'check';
                case self::TYPE_USER_STORY:
                    return 'user-edit';
            }
        }

        public function getDescription()
        {
            return $this->_description;
        }

        public function setDescription($description)
        {
            $this->_description = $description;
        }

        protected function _preDelete()
        {
            tables\IssuetypeSchemeLink::getTable()->deleteByIssuetypeID($this->getID());
            tables\VisibleIssueTypes::getTable()->deleteByIssuetypeID($this->getID());
        }

        protected function _postSave($is_new)
        {
            framework\Context::getCache()->delete(framework\Cache::KEY_TEXTPARSER_ISSUE_REGEX);
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
            return (bool) tables\IssuetypeSchemeLink::getTable()->countByIssuetypeID($this->getID());
        }
        
        public function toJSON($detailed = true)
        {
            $json = array(
                'id' => $this->getID(),
                'key' => $this->getKey(),
                'name' => $this->getName(),
                'icon' => $this->getIcon(),
                'type' => $this->getType(),
                'is_task' => $this->isTask(),
                'description' => $this->getDescription()
            );

            return $json;
        }

    }

