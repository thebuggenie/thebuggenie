<?php

    namespace thebuggenie\modules\agile;

    use thebuggenie\core\framework;

    /**
     * action components for the agile module
     */
    class Components extends framework\ActionComponent
    {

        public function componentEditAgileBoard()
        {
            $i18n = framework\Context::getI18n();
            $this->autosearches = array(
                \thebuggenie\core\entities\SavedSearch::PREDEFINED_SEARCH_PROJECT_OPEN_ISSUES => $i18n->__('Project open issues (recommended)'),
                \thebuggenie\core\entities\SavedSearch::PREDEFINED_SEARCH_PROJECT_OPEN_ISSUES_INCLUDING_SUBPROJECTS => $i18n->__('Project open issues (including subprojects)'),
                \thebuggenie\core\entities\SavedSearch::PREDEFINED_SEARCH_PROJECT_CLOSED_ISSUES => $i18n->__('Project closed issues'),
                \thebuggenie\core\entities\SavedSearch::PREDEFINED_SEARCH_PROJECT_CLOSED_ISSUES_INCLUDING_SUBPROJECTS => $i18n->__('Project closed issues (including subprojects)'),
                \thebuggenie\core\entities\SavedSearch::PREDEFINED_SEARCH_PROJECT_REPORTED_THIS_MONTH => $i18n->__('Project issues reported last month'),
                \thebuggenie\core\entities\SavedSearch::PREDEFINED_SEARCH_PROJECT_WISHLIST => $i18n->__('Project wishlist')
            );
            $this->savedsearches = \thebuggenie\core\entities\tables\SavedSearches::getTable()->getAllSavedSearchesByUserIDAndPossiblyProjectID(framework\Context::getUser()->getID(), $this->board->getProject()->getID());
            $this->issuetypes = $this->board->getProject()->getIssuetypeScheme()->getIssuetypes();
            $this->swimlane_groups = array(
                'priority' => $i18n->__('Issue priority'),
                'severity' => $i18n->__('Issue severity'),
                'category' => $i18n->__('Issue category'),
            );
            $this->priorities = \thebuggenie\core\entities\Priority::getAll();
            $this->severities = \thebuggenie\core\entities\Severity::getAll();
            $this->categories = \thebuggenie\core\entities\Category::getAll();
            $fakecolumn = new entities\BoardColumn();
            $fakecolumn->setBoard($this->board);
            $this->fakecolumn = $fakecolumn;
        }

        public function componentEditBoardColumn()
        {
            $this->statuses = \thebuggenie\core\entities\Status::getAll();
        }

        public function componentMilestoneBox()
        {
            $this->include_counts = (isset($this->include_counts)) ? $this->include_counts : false;
            $this->include_buttons = (isset($this->include_buttons)) ? $this->include_buttons : true;
        }

        public function componentBoardSwimlane()
        {
            $this->issues = $this->swimlane->getIssues();
        }

        public function componentBoardColumnheader()
        {
            $this->statuses = \thebuggenie\core\entities\Status::getAll();
        }

        public function componentWhiteboardTransitionSelector()
        {
            foreach ($this->board->getColumns() as $column)
            {
                if ($column->hasIssue($this->issue))
                {
                    $this->current_column = $column;
                    break;
                }
            }

            $transition_ids = array();
            $same_transition_statuses = array();

            foreach ($this->transitions as $status_id => $transitions)
            {
                foreach ($transitions as $transition)
                {
                    if (in_array($transition->getID(), $transition_ids))
                    {
                        $same_transition_statuses[] = $status_id;
                    }
                    else
                    {
                        $transition_ids[] = $transition->getID();
                    }
                }
            }

            $this->same_transition_statuses = $same_transition_statuses;
            $this->statuses_occurred = array_fill_keys($this->statuses, 0);
        }

        public function componentColorpicker()
        {
            $this->colors = array('#E20700', '#6094CF', '#37A42B', '#E3AA00', '#FFE955', '#80B5FF', '#80FF80', '#00458A', '#8F6A32', '#FFF');
        }

        public function componentMilestone()
        {
            if (!isset($this->milestone))
            {
                $this->milestone = new \thebuggenie\core\entities\Milestone();
                $this->milestone->setProject($this->project);
            }
        }

    }

