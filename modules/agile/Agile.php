<?php

    namespace thebuggenie\modules\agile;

    use thebuggenie\core\framework;

    /**
     * Agile module
     *
     * @author
     * @version 0.1
     * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
     * @package agile
     * @subpackage core
     */

    /**
     * Agile module
     *
     * @package agile
     * @subpackage core
     *
     * @Table(name="\thebuggenie\core\entities\tables\Modules")
     */
    class Agile extends \thebuggenie\core\entities\Module
    {

        const VERSION = '1.0';

        protected $_name = 'agile';
        protected $_longname = 'Agile';
        protected $_module_config_title = 'Agile';
        protected $_module_config_description = 'Agile - planning and whiteboard for agile teams';
        protected $_description = 'Agile - planning and whiteboard for agile teams';
        
        protected function _initialize()
        {
        }

        protected function _addAvailablePermissions()
        {
        }

        protected function _addListeners()
        {
        }

        protected function _install($scope)
        {
            if ($scope == framework\Settings::getDefaultScopeID())
            {
            }
        }

        protected function _loadFixtures($scope)
        {
        }

        protected function _uninstall()
        {
            if (framework\Context::getScope()->isDefault())
            {
            }
        }

        /**
         * User dashboard project list buttons listener
         *
         * @Listener(module="core", identifier="main\Components::DashboardViewUserProjects::links")
         *
         * @param \thebuggenie\core\framework\Event $event
         */
        public function userDashboardProjectButtonLinks(framework\Event $event)
        {
            $routing = framework\Context::getRouting();
            $i18n = framework\Context::getI18n();
            $event->addToReturnList(array('url' => $routing->generate('agile_index', array('project_key' => '%project_key%')), 'text' => $i18n->__('Planning')));
        }

        /**
         * Header "Agile" menu and board list
         *
         * @Listener(module="core", identifier="project/templates/projectheader")
         *
         * @param \thebuggenie\core\framework\Event $event
         */
        public function projectHeaderLinks(framework\Event $event)
        {
            $board = entities\AgileBoard::getB2DBTable()->selectById(framework\Context::getRequest()->getParameter('board_id'));
            if ($board instanceof entities\AgileBoard)
            {
                framework\ActionComponent::includeComponent('agile/projectheaderstriplinks', array('project' => $event->getSubject(), 'board' => $board));
            }
        }

        /**
         * Listen to milestone save event and return correct agile component
         *
         * @Listener(module="project", identifier="runMilestone::post")
         *
         * @param \thebuggenie\core\framework\Event $event
         */
        public function milestoneSave(framework\Event $event)
        {
            $board = entities\AgileBoard::getB2DBTable()->selectById(framework\Context::getRequest()->getParameter('board_id'));
            if ($board instanceof entities\AgileBoard)
            {
                $component = framework\Action::returnComponentHTML('agile/milestonebox', array('milestone' => $event->getSubject(), 'board' => $board, 'include_counts' => true));
                $event->setReturnValue($component);
                $event->setProcessed();
            }
        }

        /**
         * Header "Agile" menu and board list
         *
         * @Listener(module="core", identifier="templates/headermainmenu::projectmenulinks")
         *
         * @param \thebuggenie\core\framework\Event $event
         */
        public function headerMenuProjectLinks(framework\Event $event)
        {
            if (framework\Context::isProjectContext())
            {
                $boards = \thebuggenie\modules\agile\entities\AgileBoard::getB2DBTable()->getAvailableProjectBoards(framework\Context::getUser()->getID(), framework\Context::getCurrentProject()->getID());
                framework\ActionComponent::includeComponent('agile/headermenuprojectlinks', array('project' => $event->getSubject(), 'boards' => $boards));
            }
        }

        /**
         * @Listener(module='core', identifier='get_backdrop_partial')
         * @param \thebuggenie\core\framework\Event $event
         */
        public function listen_get_backdrop_partial(framework\Event $event)
        {
            $request = framework\Context::getRequest();
            $options = array();

            switch ($event->getSubject())
            {
                case 'agileboard':
                    $template_name = 'agile/editagileboard';
                    $board = ($request['board_id']) ? entities\tables\AgileBoards::getTable()->selectById($request['board_id']) : new entities\AgileBoard();
                    if (!$board->getID())
                    {
                        $board->setAutogeneratedSearch(\thebuggenie\core\entities\SavedSearch::PREDEFINED_SEARCH_PROJECT_OPEN_ISSUES);
                        $board->setTaskIssuetype(framework\Settings::get('issuetype_task'));
                        $board->setEpicIssuetype(framework\Settings::get('issuetype_epic'));
                        $board->setIsPrivate($request->getParameter('is_private', true));
                        $board->setProject($request['project_id']);
                    }
                    $options['board'] = $board;
                    break;
                case 'milestone_finish':
                    $template_name = 'agile/milestonefinish';
                    $options['project'] = \thebuggenie\core\entities\tables\Projects::getTable()->selectById($request['project_id']);
                    $options['board'] = entities\tables\AgileBoards::getTable()->selectById($request['board_id']);
                    $options['milestone'] = \thebuggenie\core\entities\tables\Milestones::getTable()->selectById($request['milestone_id']);
                    if (!$options['milestone']->hasReachedDate()) $options['milestone']->setReachedDate(time());
                    break;
                case 'agilemilestone':
                    $template_name = 'agile/milestone';
                    $options['project'] = \thebuggenie\core\entities\tables\Projects::getTable()->selectById($request['project_id']);
                    $options['board'] = entities\tables\AgileBoards::getTable()->selectById($request['board_id']);
                    if ($request->hasParameter('milestone_id'))
                        $options['milestone'] = \thebuggenie\core\entities\tables\Milestones::getTable()->selectById($request['milestone_id']);
                    break;
                default:
                    return;
            }
            
            foreach ($options as $key => $value)
            {
                $event->addToReturnList($value, $key);
            }
            $event->setReturnValue($template_name);
            $event->setProcessed();
        }

    }

