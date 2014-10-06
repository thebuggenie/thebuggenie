<?php

    namespace thebuggenie\core\entities;

    /**
     * Agile board swimlane class
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
     * @package thebuggenie
     * @subpackage main
     */

    /**
     * Agile board swimlane class
     *
     * @package thebuggenie
     * @subpackage main
     */
    class BoardSwimlane
    {

        /**
         * The identifiable objects for this swimlane
         *
         * @var array|\TBGIdentifiable
         */
        protected $_identifiables;

        /**
         * @var \thebuggenie\core\entities\AgileBoard
         */
        protected $_board;

        /**
         * Milestone
         * @var \TBGMilestone
         */
        protected $_milestone;

        /**
         * Cached search object
         * @var \TBGSavedSearch
         */
        protected $_search_object;

        protected $_name;

        public function getIdentifiables()
        {
            return $this->_identifiables;
        }

        public function getBoard()
        {
            return $this->_board;
        }

        public function setIdentifiables($identifiables)
        {
            $this->_identifiables = (is_array($identifiables)) ? $identifiables : array($identifiables);
        }

        public function setBoard(\thebuggenie\core\entities\AgileBoard $board)
        {
            $this->_board = $board;
        }

        public function setMilestone(\TBGMilestone $milestone)
        {
            $this->_milestone = $milestone;
        }

        /**
         * @return \TBGMilestone
         */
        public function getMilestone()
        {
            return $this->_milestone;
        }

        public function getName()
        {
            if ($this->_name === null)
            {
                $names = array();
                foreach ($this->_identifiables as $identifiable)
                {
                    $names[] = ($identifiable instanceof \TBGIdentifiable) ? $identifiable->getName() : \TBGContext::getI18n()->__('Unknown / not set');
                }
                $this->_name = join(', ', $names);
            }

            return $this->_name;
        }

        public function hasIdentifiables()
        {
            foreach ($this->_identifiables as $identifiable)
            {
                if ($identifiable instanceof \TBGIdentifiable) return true;
            }

            return false;
        }

        protected function _setupSearchObject()
        {
            if ($this->_search_object === null)
            {
                $this->_search_object = new \TBGSavedSearch();
                $this->_search_object->setFilter('project_id', \TBGSearchFilter::createFilter('project_id', array('o' => '=', 'v' => $this->getBoard()->getProject()->getID())));
                $this->_search_object->setFilter('milestone', \TBGSearchFilter::createFilter('milestone', array('o' => '=', 'v' => $this->getMilestone()->getID())));
                $this->_search_object->setFilter('state', \TBGSearchFilter::createFilter('state', array('o' => '=', 'v' => array(\TBGIssue::STATE_CLOSED, \TBGIssue::STATE_OPEN))));
                $this->_search_object->setFilter('issuetype', \TBGSearchFilter::createFilter('issuetype', array('o' => '!=', 'v' => $this->getBoard()->getEpicIssuetypeID())));
                if ($this->getBoard()->usesSwimlanes())
                {
                    $values = array();
                    foreach ($this->_identifiables as $identifiable) $values[] = ($identifiable instanceof \TBGIdentifiable) ? $identifiable->getID() : $identifiable;
                    $this->_search_object->setFilter($this->getBoard()->getSwimlaneIdentifier(), \TBGSearchFilter::createFilter($this->getBoard()->getSwimlaneIdentifier(), array('o' => '=', 'v' => $values)));
                }
                $this->_search_object->setSortFields(array('issues.milestone_order' => \b2db\Criteria::SORT_ASC));
            }
        }

        public function getIssues()
        {
            if (!$this->getBoard()->usesSwimlanes() || in_array($this->getBoard()->getSwimlaneType(), array(AgileBoard::SWIMLANES_EXPEDITE, AgileBoard::SWIMLANES_GROUPING)))
            {
                $this->_setupSearchObject();
                return $this->_search_object->getIssues();
            }
            else
            {
                return $this->getIdentifierIssue()->getChildIssues();
            }
        }

        /**
         * @return \TBGIssue
         */
        public function getIdentifierIssue()
        {
            return current($this->_identifiables);
        }

    }
