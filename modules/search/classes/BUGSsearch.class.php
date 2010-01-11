<?php

	class BUGSsearch extends TBGModule 
	{
		
		public function __construct($m_id, $res = null)
		{
			parent::__construct($m_id, $res);
			$this->_module_menu_title = TBGContext::getI18n()->__("Find issues");
			$this->_module_config_title = TBGContext::getI18n()->__("Search");
			$this->_module_config_description = TBGContext::getI18n()->__('Configure the search module, including OpenSearch from this section.');
			$this->_module_version = "1.0";
			if ($this->_enabled)
			{
				$this->addAvailablePermission('b2cancreatepublicsearches', 'Can create public searches', 1);
				$this->addAvailablePermission('b2searchmaster', 'Search administrator', 1);
				$this->addAvailableListener('core', 'header_ends', 'OpenSearch integration, read more <a href="http://en.wikipedia.org/wiki/Opensearch" target="_blank">here</a>');			
				$this->addAvailableListener('core', 'header_right', 'Header "Quick search"');
				$this->addAvailableListener('core', 'index_right_middle_bottom', 'Frontpage searches');
				$this->addAvailableListener('core', 'index_left_myissues', 'Frontpage "My issues"');
				$this->addAvailableListener('core', 'useractions_bottom', '"More issue by this user" in user drop-down menu');
				$this->addAvailableListener('core', 'viewissue_top', '"View issue" top navigation bar');
				$this->addAvailableListener('core', 'viewissue_left_middle_top', '"View issue" left-hand link to own issues');
				$this->addAvailableListener('core', 'TBGProject::createNew', 'Automatically set up quick-searches for new projects');
			}
		}
		
		public static function install($scope = null)
		{
  			if ($scope === null)
  			{
  				$scope = TBGContext::getScope()->getID();
  			}
  			TBGLogging::log("Installing search module", 'search');
  			$module = parent::_install('search', 
  									  'Search', 
  									  'Enables search functionality',
  									  'BUGSsearch',
  									  true, true, false,
  									  '1.0',
  									  true,
  									  $scope);
			$module->enableSection('core', 'header_ends', $scope);
			$module->enableSection('core', 'header_right', $scope);
			$module->enableSection('core', 'index_right_middle_bottom', $scope);
			$module->enableSection('core', 'index_left_myissues', $scope);
			$module->enableSection('core', 'index_left_myassignedissues', $scope);
			$module->enableSection('core', 'useractions_bottom', $scope);
			$module->enableSection('core', 'viewissue_top', $scope);
			$module->enableSection('core', 'viewissue_left_middle_top', $scope);
			$module->enableSection('core', 'TBGProject::createNew', $scope);
			$module->setPermission(0, 0, 0, true, $scope);
			TBGContext::setPermission('b2searchmaster', 1, 'search', 0, 1, 0, true, $scope);
			$module->saveSetting('indexshowsavedsearches', 1);
			$module->saveSetting('indexsearches', '1');
			$module->saveSetting('enable_opensearch', 1);
			$module->saveSetting('opensearch_title', 'The Bug Genie');
			$module->saveSetting('opensearch_longname', 'Search The Bug Genie');
			$module->saveSetting('opensearch_description', 'Search The Bug Genie issues');
			$module->saveSetting('opensearch_contact', 'support@thebuggenie.net');
			$module->saveSetting('frontpagelayout', 0);
			$module->saveSetting('showindexsummary', 0);
			$module->saveSetting('showindexsummarydetails', 0);
			$module->saveSetting('indexshowtitle', 0);

			if ($scope == TBGContext::getScope()->getID())
			{
				B2DB::getTable('B2tSavedSearches')->create();
				B2DB::getTable('B2tSavedSearchOrder')->create();
				B2DB::getTable('B2tSearchFields')->create();
				B2DB::getTable('B2tSearchFilters')->create();
				B2DB::getTable('B2tSearchLayoutFields')->create();
				B2DB::getTable('B2tSearchLayouts')->create();
			}
			
			try
			{
				self::loadFixtures($scope, $module);
			}
			catch (Exception $e)
			{
				throw $e;
			}
			TBGLogging::log('done (installing search module)', 'search');
		}
		
		static function loadFixtures($scope, $module)
		{
			try
			{
				$searchfilters = array();
				$searchfilters[] = array('B2tIssues', 1, '', 'State', 'Filter based on issue state', 'B2tIssues::STATE', '', '', '', '', 1, '', '', 0, 0, 0, '', 0, '');
				$searchfilters[] = array('B2tIssues', 2, 'B2tProjects', 'Project', 'Filter based on which project the issue applies to', 'B2tIssues::PROJECT_ID', 'B2tProjects::ID', 'B2tProjects::NAME', '', '', 0, '', '', 0, 0, 0, '', 0, '');
				$searchfilters[] = array('B2tIssues', 2, 'B2tListTypes', 'Status', 'Filter based on the issue status', 'B2tIssues::STATUS', 'B2tListTypes::ID', 'B2tListTypes::NAME', 'B2tListTypes::ITEMTYPE', 'b2_statustypes', 0, '', '', 0, 0, 0, '', 0, '');
				$searchfilters[] = array('B2tIssues', 2, 'B2tListTypes', 'Category', 'Filter based on the issue category', 'B2tIssues::CATEGORY', 'B2tListTypes::ID', 'B2tListTypes::NAME', 'B2tListTypes::ITEMTYPE', 'b2_categories', 0, '', '', 0, 0, 0, '', 0, '');
				$searchfilters[] = array('B2tIssues', 2, 'B2tUsers', 'Reported by', 'Filter based on the user which reported the issue', 'B2tIssues::POSTED_BY', 'B2tUsers::ID', 'B2tUsers::BUDDYNAME', '', '', 0, '', '', 0, 0, 0, '', 0, '');
				$searchfilters[] = array('B2tIssues', 2, 'B2tUsers', 'Assigned to user', 'Filter based on which user the issue is currently assigned to', 'B2tIssues::ASSIGNED_TO', 'B2tUsers::ID', 'B2tUsers::BUDDYNAME', '', '', 0, '1', 'B2tIssues::ASSIGNED_TYPE', 0, 0, 0, '', 0, '');
				$searchfilters[] = array('B2tIssues', 2, 'B2tUsers', 'Owned by', 'Filter based on which user owns the issue', 'B2tIssues::OWNED_BY', 'B2tUsers::ID', 'B2tUsers::BUDDYNAME', '', '', 0, '', '', 0, 0, 0, '', 0, '');
				$searchfilters[] = array('B2tIssues', 2, 'B2tTeams', 'Assigned to team', 'Filter based on which team the issue is currently assigned to', 'B2tIssues::ASSIGNED_TO', 'B2tTeams::ID', 'B2tTeams::TEAMNAME', '', '', 0, '2', 'B2tIssues::ASSIGNED_TYPE', 0, 0, 0, '', 0, '');
				$searchfilters[] = array('B2tIssues', 3, '', 'Percent complete', 'Filter based on how complete an issue is', 'B2tIssues::PERCENT_COMPLETE', '', '', '', '', 0, '', '', 3, 1, 0, '', 0, '');
				$searchfilters[] = array('B2tIssues', 2, 'B2tListTypes', 'Resolution', 'Filter based on the resolution of an issue', 'B2tIssues::RESOLUTION', 'B2tListTypes::ID', 'B2tListTypes::NAME', 'B2tListTypes::ITEMTYPE', 'b2_resolutiontypes', 0, '', '', 0, 0, 1, 'Undetermined', 0, '');
				$searchfilters[] = array('B2tIssues', 2, 'B2tIssueTypes', 'Issue type', 'Filter based on the issue type', 'B2tIssues::ISSUE_TYPE', 'B2tIssueTypes::ID', 'B2tIssueTypes::NAME', '', '', 0, '', '', 0, 0, 0, '', 0, '');
				$searchfilters[] = array('B2tIssueAffectsEdition', 2, 'B2tEditions', 'Edition', 'Filter based on which edition the issue applies to', 'B2tIssueAffectsEdition::EDITION', 'B2tEditions::ID', 'B2tEditions::NAME', '', '', 0, '', '', 0, 0, 0, '', 0, 'B2tIssueAffectsEdition::ISSUE');
				$searchfilters[] = array('B2tIssues', 2, 'B2tListTypes', 'Priority', 'Filter based on the issue priority', 'B2tIssues::PRIORITY', 'B2tListTypes::ID', 'B2tListTypes::NAME', 'B2tListTypes::ITEMTYPE', 'b2_prioritytypes', 0, '', '', 0, 0, 1, 'Undetermined', 0, '');
				$searchfilters[] = array('B2tIssueAffectsBuild', 2, 'B2tBuilds', 'Build', 'Filter based on which build the issue applies to', 'B2tIssueAffectsBuild::BUILD', 'B2tBuilds::ID', 'B2tBuilds::NAME', '', '', 0, '', '', 0, 0, 0, '', 0, 'B2tIssueAffectsBuild::ISSUE');
				$searchfilters[] = array('B2tIssueAffectsComponent', 2, 'B2tComponents', 'Component', 'Filter based on which component the issue applies to', 'B2tIssueAffectsComponent::COMPONENT', 'B2tComponents::ID', 'B2tComponents::NAME', '', '', 0, '', '', 0, 0, 0, '', 0, 'B2tIssueAffectsComponent::ISSUE');
				$searchfilters[] = array('B2tIssues', 4, '', 'Last updated', 'Filter based on when an issue was last updated', 'B2tIssues::LAST_UPDATED', '', '', '', '', 0, '', '', 0, 0, 0, '', 0, '');
				$searchfilters[] = array('B2tIssues', 5, '', 'Blocks the next release', 'Filter based on whether the issue blocks the next release or not', 'B2tIssues::BLOCKING', '', '', '', '', 1, '', '', 0, 0, 0, '', 0, '');
	
				foreach ($searchfilters as $searchfilter)
				{
					$crit = new B2DBCriteria();
					$crit->addInsert(B2tSearchFilters::FILTER_TABLE, array_shift($searchfilter));
					$crit->addInsert(B2tSearchFilters::FILTER_TYPE, array_shift($searchfilter));
					$crit->addInsert(B2tSearchFilters::VALUES_FROM, array_shift($searchfilter));
					$crit->addInsert(B2tSearchFilters::SHORT_NAME, array_shift($searchfilter));
					$crit->addInsert(B2tSearchFilters::DESCRIPTION, array_shift($searchfilter));
					$crit->addInsert(B2tSearchFilters::FILTER_FIELD, array_shift($searchfilter));
					$crit->addInsert(B2tSearchFilters::VALUE_FROM_FIELD, array_shift($searchfilter));
					$crit->addInsert(B2tSearchFilters::NAME_FROM_FIELD, array_shift($searchfilter));
					$crit->addInsert(B2tSearchFilters::FROM_TBL_CRIT_FIELD, array_shift($searchfilter));
					$crit->addInsert(B2tSearchFilters::FROM_TBL_CRIT_VALUE, array_shift($searchfilter));
					$crit->addInsert(B2tSearchFilters::FILTER_UNIQUE, array_shift($searchfilter));
					$crit->addInsert(B2tSearchFilters::REQ_VALUE, array_shift($searchfilter));
					$crit->addInsert(B2tSearchFilters::REQ_VALUE_FIELD, array_shift($searchfilter));
					$crit->addInsert(B2tSearchFilters::VALUE_LENGTH, array_shift($searchfilter));
					$crit->addInsert(B2tSearchFilters::VALUE_TYPE, array_shift($searchfilter));
					$crit->addInsert(B2tSearchFilters::INCLUDES_NOTSET, array_shift($searchfilter));
					$crit->addInsert(B2tSearchFilters::NOTSET_DESCRIPTION, array_shift($searchfilter));
					$crit->addInsert(B2tSearchFilters::NOTSET_VALUE, array_shift($searchfilter));
					$crit->addInsert(B2tSearchFilters::JOIN_ISSUES_ON, array_shift($searchfilter));
					$res = B2DB::getTable('B2tSearchFilters')->doInsert($crit);
				}
	
				$searchlayouts = array();
				$searchlayouts[] = 'Pretty search results';
				$searchlayouts[] = 'Standard list with percentage';
				$searchlayouts[] = 'Short list with results';
				$searchlayouts[] = 'Standard list with status text';
				$searchlayouts[] = 'Standard list with description';
				
				$layout_ids = array();
				foreach ($searchlayouts as $searchlayout)
				{
					$crit = new B2DBCriteria();
					$crit->addInsert(B2tSearchLayouts::NAME, $searchlayout);
					$crit->addInsert(B2tSearchLayouts::SCOPE, $scope);
					$res = B2DB::getTable('B2tSearchLayouts')->doInsert($crit);
					$layout_ids[] = $res->getInsertID();
				}
				
				$searchlayoutfields = array();
				
				$layout_1 = array_shift($layout_ids);
				$searchlayoutfields[] = array('B2tIssues::ISSUE_NO', 0, 50, 0, '0', 1, 			$layout_1, 1, 1, 0, 2, 'center', 0);
				$searchlayoutfields[] = array('B2tIssues::TITLE', 0, 0, 20, '0', 2,				$layout_1, 2, 1, 2, 0, 'left', 0);
				$searchlayoutfields[] = array('B2tIssues::LAST_UPDATED', 0, 230, 0, '0', 3, 	$layout_1, 3, 1, 0, 2, 'right', 1);
				$searchlayoutfields[] = array('B2tComments::ID', 0, 20, 30, '1', 4,				$layout_1, 1, 1, 0, 2, 'left', 0);
				$searchlayoutfields[] = array('B2tComments::ID', 0, 15, 30, '0', 5,				$layout_1, 1, 1, 0, 2, 'left', 0);
				$searchlayoutfields[] = array('B2tIssues::STATUS', 0, 0, 0, '0', 1, 			$layout_1, 3, 2, 0, 0, 'left', 0);
				$searchlayoutfields[] = array('B2tIssues::SEVERITY', 0, 0, 0, '0', 2, 			$layout_1, 1, 2, 0, 0, 'left', 1);
				
				$layout_2 = array_shift($layout_ids);
				$searchlayoutfields[] = array('B2tIssues::STATUS', 0, 20, 30, '0', 1, 				$layout_2, 1, 1, 0, 0, 'left', 0);
				$searchlayoutfields[] = array('B2tIssues::ISSUE_NO', 0, 50, 30, '0', 2, 			$layout_2, 1, 1, 0, 0, 'left', 0);
				$searchlayoutfields[] = array('B2tIssues::TITLE', 0, 0, 30, '0', 3, 				$layout_2, 2, 1, 0, 0, 'left', 0);
				$searchlayoutfields[] = array('B2tIssues::ASSIGNED_TO', 0, 130, 30, '0', 4, 		$layout_2, 1, 1, 0, 0, 'left', 1);
				$searchlayoutfields[] = array('B2tIssues::SEVERITY', 0, 80, 30, '0', 5, 			$layout_2, 1, 1, 0, 0, 'left', 0);
				$searchlayoutfields[] = array('B2tIssues::PERCENT_COMPLETE', 0, 100, 30, '0', 6, 	$layout_2, 1, 1, 0, 0, 'center', 0);
				$searchlayoutfields[] = array('B2tIssues::LAST_UPDATED', 0, 50, 30, '0', 7, 		$layout_2, 2, 1, 0, 0, 'center', 0);
				$searchlayoutfields[] = array('B2tComments::ID', 0, 20, 30, '1', 8, 				$layout_2, 1, 1, 0, 0, 'left', 0);
				$searchlayoutfields[] = array('B2tComments::ID', 0, 15, 30, '0', 9, 				$layout_2, 1, 1, 0, 0, 'left', 0);
				
				$layout_3 = array_shift($layout_ids);
				$searchlayoutfields[] = array('B2tIssues::STATUS', 0, 20, 30, '0', 1, 				$layout_3, 1, 1, 0, 0, 'left', 0);
				$searchlayoutfields[] = array('B2tIssues::ISSUE_NO', 0, 50, 30, '0', 2, 			$layout_3, 1, 1, 0, 0, 'left', 0);
				$searchlayoutfields[] = array('B2tIssues::TITLE', 0, 0, 30, '0', 3, 				$layout_3, 2, 1, 0, 0, 'left', 0);
				$searchlayoutfields[] = array('B2tIssues::LAST_UPDATED', 0, 50, 30, '0', 4, 		$layout_3, 2, 1, 0, 0, 'center', 0);
				$searchlayoutfields[] = array('B2tComments::ID', 0, 20, 30, '1', 5, 				$layout_3, 1, 1, 0, 0, 'left', 0);
				$searchlayoutfields[] = array('B2tComments::ID', 0, 15, 30, '0', 6, 				$layout_3, 1, 1, 0, 0, 'left', 0);
				
				$layout_4 = array_shift($layout_ids);
				$searchlayoutfields[] = array('B2tIssues::ISSUE_NO', 0, 50, 30, '0', 1, 	$layout_4, 1, 1, 0, 0, 'left', 0);
				$searchlayoutfields[] = array('B2tIssues::TITLE', 0, 0, 30, '0', 2, 		$layout_4, 2, 1, 0, 0, 'left', 0);
				$searchlayoutfields[] = array('B2tIssues::ASSIGNED_TO', 0, 130, 30, '0', 3, $layout_4, 1, 1, 0, 0, 'left', 1);
				$searchlayoutfields[] = array('B2tIssues::SEVERITY', 0, 80, 30, '0', 4, 	$layout_4, 1, 1, 0, 0, 'left', 0);
				$searchlayoutfields[] = array('B2tIssues::STATUS', 0, 120, 30, '0', 5, 		$layout_4, 3, 1, 0, 0, 'left', 1);
				$searchlayoutfields[] = array('B2tIssues::LAST_UPDATED', 0, 50, 3, '0', 6, 	$layout_4, 2, 1, 0, 0, 'center', 0);
				$searchlayoutfields[] = array('B2tComments::ID', 0, 20, 30, '1', 7, 		$layout_4, 1, 1, 0, 0, 'left', 0);
				$searchlayoutfields[] = array('B2tComments::ID', 0, 15, 30, '0', 8, 		$layout_4, 1, 1, 0, 0, 'left', 0);
				
				$layout_5 = array_shift($layout_ids);
				$searchlayoutfields[] = array('B2tIssues::ISSUE_NO', 0, 50, 30, '0', 1, 	$layout_5, 1, 1, 0, 0, 'left', 0);
				$searchlayoutfields[] = array('B2tIssues::TITLE', 0, 0, 30, '0', 2, 		$layout_5, 2, 1, 0, 0, 'left', 0);
				$searchlayoutfields[] = array('B2tIssues::ASSIGNED_TO', 0, 130, 30, '0', 3, $layout_5, 1, 1, 0, 0, 'left', 1);
				$searchlayoutfields[] = array('B2tIssues::SEVERITY', 0, 80, 30, '0', 4, 	$layout_5, 1, 1, 0, 0, 'left', 0);
				$searchlayoutfields[] = array('B2tIssues::STATUS', 0, 120, 30, '0', 5, 		$layout_5, 3, 1, 0, 0, 'left', 1);
				$searchlayoutfields[] = array('B2tIssues::LAST_UPDATED', 0, 50, 30, '0', 6, $layout_5, 2, 1, 0, 0, 'center', 0);
				$searchlayoutfields[] = array('B2tComments::ID', 0, 20, 30, '1', 7, 		$layout_5, 1, 1, 0, 0, 'left', 0);
				$searchlayoutfields[] = array('B2tComments::ID', 0, 15, 30, '0', 8, 		$layout_5, 1, 1, 0, 0, 'left', 0);
				$searchlayoutfields[] = array('B2tIssues::DESCRIPTION', 0, 0, 0, '0', 9, 	$layout_5, 1, 2, 8, 0, 'left', 0);
	
				foreach ($searchlayoutfields as $searchlayoutfield)
				{
					$crit = new B2DBCriteria();
					$crit->addInsert(B2tSearchLayoutFields::FIELD, array_shift($searchlayoutfield));
					$crit->addInsert(B2tSearchLayoutFields::LENGTH, array_shift($searchlayoutfield));
					$crit->addInsert(B2tSearchLayoutFields::WIDTH, array_shift($searchlayoutfield));
					$crit->addInsert(B2tSearchLayoutFields::HEIGHT, array_shift($searchlayoutfield));
					$crit->addInsert(B2tSearchLayoutFields::ICON, array_shift($searchlayoutfield));
					$crit->addInsert(B2tSearchLayoutFields::ORDER, array_shift($searchlayoutfield));
					$crit->addInsert(B2tSearchLayoutFields::LAYOUT, array_shift($searchlayoutfield));
					$crit->addInsert(B2tSearchLayoutFields::FIELD_TYPE, array_shift($searchlayoutfield));
					$crit->addInsert(B2tSearchLayoutFields::ROW, array_shift($searchlayoutfield));
					$crit->addInsert(B2tSearchLayoutFields::SPAN_COLS, array_shift($searchlayoutfield));
					$crit->addInsert(B2tSearchLayoutFields::SPAN_ROWS, array_shift($searchlayoutfield));
					$crit->addInsert(B2tSearchLayoutFields::ALIGN, array_shift($searchlayoutfield));
					$crit->addInsert(B2tSearchLayoutFields::INCLUDE_DESC, array_shift($searchlayoutfield));
					$crit->addInsert(B2tSearchLayoutFields::SCOPE, $scope);
					$res = B2DB::getTable('B2tSearchLayoutFields')->doInsert($crit);
				}
				
				$module->saveSetting('defaultsearchlayout', $layout_4);
				$values = array();
				$values[] = array('value' => TBGIssue::STATE_OPEN, 'operator' => 'B2DBCriteria::DB_EQUALS', 'filter' => 1);
				self::createSearch('All open issues by project', $layout_4, 0, 1, 1, 'project', $scope, $values);
				
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}
		
		public function getCommentAccess($target_type, $target_id, $type = 'view')
		{
			
		}
		
		public function uninstall($scope)
		{
			B2DB::getTable('B2tSavedSearches')->drop();
			B2DB::getTable('B2tSavedSearchOrder')->drop();
			B2DB::getTable('B2tSearchFields')->drop();
			B2DB::getTable('B2tSearchFilters')->drop();
			B2DB::getTable('B2tSearchLayoutFields')->drop();
			B2DB::getTable('B2tSearchLayouts')->drop();
			parent::uninstall($scope);
		}
				
		public static function createSearch($name, $layout, $applies_to, $is_public, $uid, $groupby, $scope, $values)
		{
			$crit = new B2DBCriteria();
			$crit->addInsert(B2tSavedSearches::NAME, $name);
			$crit->addInsert(B2tSavedSearches::LAYOUT, $layout);
			$crit->addInsert(B2tSavedSearches::SCOPE, $scope);
			$crit->addInsert(B2tSavedSearches::APPLIES_TO, $applies_to);
			$crit->addInsert(B2tSavedSearches::IS_PUBLIC, $is_public);
			$crit->addInsert(B2tSavedSearches::UID, $uid);
			$crit->addInsert(B2tSavedSearches::GROUPBY, $groupby);
			$res = B2DB::getTable('B2tSavedSearches')->doInsert($crit);
			
			$s_id = $res->getInsertID();
			
			foreach ($values as $value)
			{
				$crit = new B2DBCriteria();
				$crit->addInsert(B2tSearchFields::VALUE, $value['value']);
				$crit->addInsert(B2tSearchFields::OPERATOR, $value['operator']);
				$crit->addInsert(B2tSearchFields::SEARCH, $s_id);
				$crit->addInsert(B2tSearchFields::SCOPE, $scope);
				$crit->addInsert(B2tSearchFields::FILTER_ID, $value['filter']);
				$res = B2DB::getTable('B2tSearchFields')->doInsert($crit);
			}
		}
		
		/**
		 * Returns all applies search filters
		 *
		 * @param integer $sid ID of saved search
		 * @param boolean $overridepermissions
		 * 
		 * @return array
		 */	
		public function getSearchFields($sid = 0, $overridepermissions = false)
		{
			$searchfields = array();
			
			if ($sid != 0)
			{
				$crit = new B2DBCriteria();
				$crit->addWhere(B2tSearchFields::SEARCH, $sid);
				$resultset = B2DB::getTable('B2tSearchFields')->doSelect($crit);
				while ($row = $resultset->getNextRow())
				{
					$searchfields[] = array('filter_table' => $row->get(B2tSearchFilters::FILTER_TABLE), "id" => $row->get(B2tSearchFilters::ID), "filter_field" => $row->get(B2tSearchFilters::FILTER_FIELD), "value" => $row->get(B2tSearchFields::VALUE), "operator" => $row->get(B2tSearchFields::OPERATOR) , "filter_type" => $row->get(B2tSearchFilters::FILTER_TYPE), "values_from" => $row->get(B2tSearchFilters::VALUES_FROM), "value_from_field" => $row->get(B2tSearchFilters::VALUE_FROM_FIELD), "name_from_field" => $row->get(B2tSearchFilters::NAME_FROM_FIELD), "from_tbl_crit_field" => $row->get(B2tSearchFilters::FROM_TBL_CRIT_FIELD), "from_tbl_crit_value" => $row->get(B2tSearchFilters::FROM_TBL_CRIT_VALUE), "req_value" => $row->get(B2tSearchFilters::REQ_VALUE), "req_value_field" => $row->get(B2tSearchFilters::REQ_VALUE_FIELD), "value_length" => $row->get(B2tSearchFilters::VALUE_LENGTH), "value_type" => $row->get(B2tSearchFilters::VALUE_TYPE), "includes_notset" => $row->get(B2tSearchFilters::INCLUDES_NOTSET), "notset_description" => $row->get(B2tSearchFilters::NOTSET_DESCRIPTION), "notset_value" => $row->get(B2tSearchFilters::NOTSET_VALUE), "description" => $row->get(B2tSearchFilters::DESCRIPTION), 'join_issues_on' => $row->get(B2tSearchFilters::JOIN_ISSUES_ON));
				}
			}
			else
			{
				$searchfields = (isset($_SESSION['searchfields'])) ? $_SESSION['searchfields'] : array();
			}
			return $searchfields;
		}
	
		public function doSearch($search_row, $overridepermissions = false, $frommodule = false, $issaved = false, $simplesearch = false)
		{
			$this->log('performing search');
			$groupby = '';
			if ($search_row instanceof B2DBRow)
			{ 
				if ($overridepermissions || (($search_row->get(B2tSavedSearches::IS_PUBLIC) == 1 || $search_row->get(B2tSavedSearches::UID) == TBGContext::getUser()->getUID()) && (TBGContext::getUser()->hasPermission('b2projectaccess', $search_row->get(B2tSavedSearches::APPLIES_TO), 'core') == true || $search_row->get(B2tSavedSearches::APPLIES_TO) == 0)))
				{
					$searchtitle = $search_row->get(B2tSavedSearches::NAME);
					if ($search_row->get(B2tSavedSearches::GROUPBY) != '')
					{
						$groupby = $search_row->get(B2tSavedSearches::GROUPBY);
					}
				}
			}
			else
			{
				$searchtitle = "Custom search";
				$groupby = (isset($_SESSION['groupby'])) ? $_SESSION['groupby'] : 0;
			}
			
			$this->log('building search criteria');
			$searchfields = $this->getSearchFields($search_row->get(B2tSavedSearches::ID));
	
			$crit = new B2DBCriteria();
			$crit->setDistinct();
			$crit->setFromTable(B2DB::getTable('B2tIssues'));
			$crit->addJoin(B2DB::getTable('B2tComments'), B2tComments::ID, B2tIssues::ID, array(array(B2tComments::TARGET_TYPE, 1)));
			$crit->addJoin(B2DB::getTable('B2tIssueAffectsEdition'), B2tIssueAffectsEdition::ISSUE, B2tIssues::ID);
			$crit->addJoin(B2DB::getTable('B2tIssueAffectsComponent'), B2tIssueAffectsComponent::ISSUE, B2tIssues::ID);
			$crit->addJoin(B2DB::getTable('B2tIssueAffectsBuild'), B2tIssueAffectsBuild::ISSUE, B2tIssues::ID);
			
			$crit->addWhere(B2tIssues::DELETED, 0);
			
			if ($groupby != '')
			{
				switch ($groupby)
				{
					case 'milestone':
						$crit->addOrderBy(B2tIssues::MILESTONE, 'desc');
						break;
					case 'project':
						$crit->addOrderBy(B2tProjects::NAME, 'asc');
						break;
					case 'issuetype':
						$crit->addOrderBy(B2tIssues::ISSUE_TYPE, 'asc');
						break;
					case 'severity':
						$crit->addOrderBy(B2tIssues::SEVERITY, 'asc');
						break;
					case 'state':
						$crit->addOrderBy(B2tIssues::STATE, 'asc');
						break;
					case 'priority':
						$crit->addOrderBy(B2tIssues::PRIORITY, 'asc');
						break;
					case 'component':
						$crit->addOrderBy(B2tIssueAffectsComponent::COMPONENT, 'asc');
						break;
					case 'edition':
						$crit->addOrderBy(B2tIssueAffectsEdition::EDITION, 'asc');
						break;
					case 'assignee':
						$crit->addOrderBy(B2tIssues::ASSIGNED_TYPE, 'asc');
						$crit->addOrderBy(B2tIssues::ASSIGNED_TO);
						break;
				}
			}
			if (TBGContext::getRequest()->getParameter('sort_column') && defined(TBGContext::getRequest()->getParameter('sort_column')))
			{
				if (TBGContext::getRequest()->getParameter('sort') == 'asc')
				{
					$crit->addOrderBy(constant(TBGContext::getRequest()->getParameter('sort_column')), 'asc');
				}
				else
				{
					$crit->addOrderBy(constant(TBGContext::getRequest()->getParameter('sort_column')), 'desc');
				}
			}
			else
			{
				$crit->addOrderBy(B2tIssues::LAST_UPDATED, 'desc');
			}
			
			foreach ($searchfields as $aSearchField)
			{
				if ($aSearchField['filter_table'] != 'B2tIssues')
				{
					$crit->addJoin(B2DB::getTable($aSearchField['filter_table']), constant($aSearchField['join_issues_on']), B2tIssues::ID);
				}
				$crit->addWhere(constant($aSearchField['filter_field']), $aSearchField['value'], constant($aSearchField['operator']));
				if ($aSearchField['req_value'] != "")
				{
					$crit->addWhere(constant($aSearchField['req_value_field']), $aSearchField['req_value']);
				}
			}
			if ($simplesearch)
			{
				$firstarg = true;
				foreach ($_SESSION['simplefilters'] as $aSearchField)
				{
					if ($firstarg == false)
					{
						$critn->addOr($aSearchField['filter_field'], $aSearchField['value'], $aSearchField['operator']);
					}
					else
					{
						$critn = $crit->returnCriterion($aSearchField['filter_field'], $aSearchField['value'], $aSearchField['operator']);
					}
					$firstarg = false;
				}
				$crit->addWhere($critn);
			}
			$crit->addWhere(B2tIssues::SCOPE, TBGContext::getScope()->getID());
			$this->log('done');
			
			$this->log('retrieving search results from database');
			$resultset = B2DB::getTable('B2tIssues')->doSelect($crit);
			$this->log('done');
			
			$searchresults = array();
			$this->log('building resultset');
			if ($resultset)
			{
				while ($row = $resultset->getNextRow())
				{
					$theissue = TBGFactory::TBGIssueLab($row->get(B2tIssues::ID), $row);
					if ($theissue->hasAccess())
					{
						$searchresults[$theissue->getID()] = $theissue;
					}
				}
			}
			else
			{
				$this->log('no results');
			}
			$this->log('done');
			return array('issues' => $searchresults, 'groupby' => $groupby);
		}
		
		public function getLayoutFromSearch($s_id)
		{
			$searchlayout = B2DB::getTable('B2tSavedSearches')->doSelectById($s_id)->get(B2tSavedSearches::LAYOUT);
			if ($searchlayout == 0)
			{
				$searchlayout = $this->getSetting('defaultsearchlayout');
			}
			return $searchlayout;
		}
	
		public function getTitleFromSearch($s_id)
		{
			return B2DB::getTable('B2tSavedSearches')->doSelectById($s_id, null)->get(B2tSavedSearches::NAME);
		}

		/**
		 * Returns the number of layouts
		 *
		 * @return integer
		 */
		public function getNumberOfLayouts()
		{
			$crit = new B2DBCriteria();
			$crit->addWhere(B2tSearchLayouts::SCOPE, TBGContext::getScope()->getID());
			return B2DB::getTable('B2tSearchLayouts')->doCount($crit);
		}
		
		public function getLayouts()
		{
			$crit = new B2DBCriteria();
			$crit->addWhere(B2tSearchLayouts::SCOPE, TBGContext::getScope()->getID());
			$results = B2DB::getTable('B2tSearchLayouts')->doSelect($crit);
			$layouts = array();
			
			while ($results->next())
			{
				$layouts[] = array('id' => $results->get(B2tSearchLayouts::ID), 'name' => $results->get(B2tSearchLayouts::NAME));
			}
			
			return $layouts;
		}
	
		public function getFilters()
		{
			$resultset = B2DB::getTable('B2tSearchFilters')->doSelect();
			return $resultset->getAllRows();
		}
	
		public function isPublic($sid)
		{
			$crit = new B2DBCriteria();
			$row = B2DB::getTable('B2tSavedSearches')->doSelectById($sid, $crit);
			return $row->get(B2tSavedSearches::IS_PUBLIC);
		}
	
		public function getSavedSearchUid($sid)
		{
			$row = B2DB::getTable('B2tSavedSearches')->doSelectById($sid, null);
			return $row->get(B2tSavedSearches::UID);
		}
		
		public function getSavedSearchName($sid)
		{
			$row = B2DB::getTable('B2tSavedSearches')->doSelectById($sid, null);
			if ($row instanceof B2DBRow)
			{
				return $row->get(B2tSavedSearches::NAME);
			}
			else
			{
				return false;
			}
		}
		
		public function getSavedSearchPublic($sid)
		{
			$row = B2DB::getTable('B2tSavedSearches')->doSelectById($sid, null);
			return $row->get(B2tSavedSearches::IS_PUBLIC);
		}
		
		public function getSearchAppliesTo($sid)
		{
			$crit = new B2DBCriteria();
			$row = B2DB::getTable('B2tSavedSearches')->doSelectById($sid, $crit);
			return $row->get(B2tSavedSearches::APPLIES_TO);
		}
	
		public function createScope($oldScope, $newScope)
		{
			$crit = new B2DBCriteria();
			$crit->addWhere(B2tSearchLayouts::SCOPE, $oldScope);
			$results = B2DB::getTable('B2tSearchLayouts')->doSelect($crit);
			
			while ($results->next())
			{
				$inscrit = new B2DBCriteria();
				$inscrit->addInsert(B2tSearchLayouts::NAME, $results->get(B2tSearchLayouts::NAME));
				$inscrit->addInsert(B2tSearchLayouts::SCOPE, $newScope);
				$layout_id = B2DB::getTable('B2tSearchLayouts')->doInsert($inscrit)->getInsertID();
				
				$selcrit = new B2DBCriteria();
				$selcrit->addWhere(B2tSearchLayoutFields::LAYOUT, $results->get(B2tSearchLayouts::ID));
				$fld_res = B2DB::getTable('B2tSearchLayoutFields')->doSelect($selcrit);
				while ($fld_res->next())
				{
					$inscrit = new B2DBCriteria();
					$inscrit->addInsert(B2tSearchLayoutFields::ALIGN, $fld_res->get(B2tSearchLayoutFields::ALIGN));
					$inscrit->addInsert(B2tSearchLayoutFields::FIELD, $fld_res->get(B2tSearchLayoutFields::FIELD));
					$inscrit->addInsert(B2tSearchLayoutFields::FIELD_TYPE , $fld_res->get(B2tSearchLayoutFields::FIELD_TYPE));
					$inscrit->addInsert(B2tSearchLayoutFields::HEIGHT , $fld_res->get(B2tSearchLayoutFields::HEIGHT));
					$inscrit->addInsert(B2tSearchLayoutFields::ICON , $fld_res->get(B2tSearchLayoutFields::ICON));
					$inscrit->addInsert(B2tSearchLayoutFields::INCLUDE_DESC , $fld_res->get(B2tSearchLayoutFields::INCLUDE_DESC));
					$inscrit->addInsert(B2tSearchLayoutFields::LENGTH , $fld_res->get(B2tSearchLayoutFields::LENGTH));
					$inscrit->addInsert(B2tSearchLayoutFields::ORDER , $fld_res->get(B2tSearchLayoutFields::ORDER));
					$inscrit->addInsert(B2tSearchLayoutFields::ROW , $fld_res->get(B2tSearchLayoutFields::ROW));
					$inscrit->addInsert(B2tSearchLayoutFields::SCOPE , $newScope);
					$inscrit->addInsert(B2tSearchLayoutFields::LAYOUT , $layout_id);
					$inscrit->addInsert(B2tSearchLayoutFields::SPAN_COLS , $fld_res->get(B2tSearchLayoutFields::SPAN_COLS));
					$inscrit->addInsert(B2tSearchLayoutFields::SPAN_ROWS , $fld_res->get(B2tSearchLayoutFields::SPAN_ROWS));
					$inscrit->addInsert(B2tSearchLayoutFields::WIDTH , $fld_res->get(B2tSearchLayoutFields::WIDTH));
					B2DB::getTable('B2tSearchLayoutFields')->doInsert($inscrit);
				}
			}
			TBGSettings::saveSetting('defaultsearchlayout', TBGSettings::get('defaultsearchlayout'), 'search', $newScope);
			return true;
		}
		
		public function deleteScope($oldScope)
		{
			$crit = new B2DBCriteria();
			$crit->addWhere(B2tSearchLayoutFields::SCOPE, $oldScope);
			B2DB::getTable('B2tSearchLayoutFields')->doDelete($crit);
			$crit = new B2DBCriteria();
			$crit->addWhere(B2tSearchFields::SCOPE, $oldScope);
			B2DB::getTable('B2tSearchFields')->doDelete($crit);
			$crit = new B2DBCriteria();
			$crit->addWhere(B2tSearchLayouts::SCOPE, $oldScope);
			B2DB::getTable('B2tSearchLayouts')->doDelete($crit);
			$crit = new B2DBCriteria();
			$crit->addWhere(B2tSavedSearches::SCOPE, $oldScope);
			B2DB::getTable('B2tSavedSearches')->doDelete($crit);
			
			return true;
		}
		
		public function presentResultsHTML($l_id, $searchresult, $s_id = 0, $frommodule = false, $searchtitle = 'CUSTOM SEARCH', $groupby = '', $showtitle = true)
		{
			$searchtitle = ($searchtitle != '') ? $searchtitle : 'CUSTOM SEARCH';
			$crit = new B2DBCriteria();
			$crit->addWhere(B2tSearchLayoutFields::LAYOUT, $l_id);
			$crit->addOrderBy(B2tSearchLayoutFields::ROW);
			$crit->addOrderBy(B2tSearchLayoutFields::ORDER);
			
			$resultset = B2DB::getTable('B2tSearchLayoutFields')->doSelect($crit);
			
			$layoutfields = array();
	
			while ($row = $resultset->getNextRow())
			{
				$layoutfields[] = array('field' => $row->get(B2tSearchLayoutFields::FIELD), 'length' => $row->get(B2tSearchLayoutFields::LENGTH), 'width' => $row->get(B2tSearchLayoutFields::WIDTH), 'height' => $row->get(B2tSearchLayoutFields::HEIGHT), 'icon' => $row->get(B2tSearchLayoutFields::ICON), 'order' => $row->get(B2tSearchLayoutFields::ORDER), 'search' => $row->get(B2tSearchLayoutFields::LAYOUT), 'field_type' => $row->get(B2tSearchLayoutFields::FIELD_TYPE), 'row' => $row->get(B2tSearchLayoutFields::ROW), 'span_cols' => $row->get(B2tSearchLayoutFields::SPAN_COLS), 'span_rows' => $row->get(B2tSearchLayoutFields::SPAN_ROWS), 'align' => $row->get(B2tSearchLayoutFields::ALIGN), 'include_desc' => $row->get(B2tSearchLayoutFields::INCLUDE_DESC));
			}
	
			if ($showtitle)
			{
				echo '<div class="searchtitleheader">' . $searchtitle;
				if (!($bugs_response->getPage() == 'index' && $this->getSetting('showindexsummary') == 0))
				{
					echo '&nbsp;&nbsp;&nbsp;<span style="display: inline;">(' . TBGContext::getI18n()->__('%number_of% matches', array('%number_of%' => count($searchresult))) . ')</span>';
				}
				echo '</div>';
			}
			echo '<table style="table-layout: fixed; width: 100%; background-color: #FFF;" cellpadding=0 cellspacing=0 class="bug_list">' . "\n";
	
			$crit = new B2DBCriteria();
			$crit->addOrderBy(B2tSearchLayoutFields::ROW, 'desc');
			$crit->addWhere(B2tSearchLayoutFields::LAYOUT, $l_id);
			$layoutrows = B2DB::getTable('B2tSearchLayoutFields')->doSelectOne($crit)->get(B2tSearchLayoutFields::ROW);
				
			$search_hits = 0;
			$header_cc = 0;
			$group_cc = 0;
			$prev_groupby = null;
			$this_groupby = null;
			foreach ($searchresult as $theIssue)
			{
				if ($groupby != '')
				{
					switch ($groupby)
					{
						case 'milestone':
							if ($theIssue->getMilestone() instanceof TBGMilestone)
							{
								$this_groupby = $theIssue->getMilestone()->getID();
							}
							else
							{
								$this_groupby = 0;
							}
							if ($this_groupby != 0)
							{
								$grouptitle = TBGContext::getI18n()->__('Issues targetted for %milestone%', array('%milestone%' => $theIssue->getMilestone()));
							}
							else
							{
								$grouptitle = TBGContext::getI18n()->__('Issues not targetted for any milestones'); 
							}
							break;
						case 'project':
							$this_groupby = $theIssue->getProject()->getID();
							$grouptitle = TBGContext::getI18n()->__('Issues for %item%', array('%item%' => $theIssue->getProject()->getName()));
							break;
						case 'edition':
							if ($theIssue->getEdition() instanceof TBGEdition)
							{
								$this_groupby = $theIssue->getEdition()->getID();
								$grouptitle = TBGContext::getI18n()->__('Issues for %item%', array('%item%' => $theIssue->getEdition()->getName()));
							}
							else
							{
								$this_groupby = 0;
								$grouptitle = TBGContext::getI18n()->__('Unknown edition');
							}
							break;
						case 'component':
							if ($theIssue->getComponent() instanceof TBGComponent)
							{
								$this_groupby = $theIssue->getComponent()->getID();
								$grouptitle = TBGContext::getI18n()->__('Issues for %item%', array('%item%' => $theIssue->getComponent()->getName()));
							}
							else
							{
								$this_groupby = 0;
								$grouptitle = TBGContext::getI18n()->__('Unknown component');
							}
							break;
						case 'issuetype':
							$this_groupby = $theIssue->getIssueType()->getID();
							$grouptitle = $theIssue->getIssueType()->getName();
							break;
						case 'state':
							$this_groupby = $theIssue->getState();
							$grouptitle = ($theIssue->getState() == TBGIssue::STATE_OPEN) ? TBGContext::getI18n()->__('Open issues') : TBGContext::getI18n()->__('Closed issues');
							break;
						case 'severity':
							if ($theIssue->getSeverity()->getID() != 0)
							{
								$this_groupby = $theIssue->getSeverity()->getID();
								$grouptitle = $theIssue->getSeverity()->getName();
							}
							else
							{
								$this_groupby = 0;
								$grouptitle = TBGContext::getI18n()->__('Isses without any severity set');
							}
							break;
						case 'priority':
							if ($theIssue->getPriority()->getID() != 0)
							{
								$this_groupby = $theIssue->getPriority()->getID();
								$grouptitle = $theIssue->getPriority()->getName();
							}
							else
							{
								$this_groupby = 0;
								$grouptitle = TBGContext::getI18n()->__('Isses that have not been prioritized');
							}
							break;
						case 'assignee':
							if ($theIssue->getAssignee() != null)
							{
								$this_groupby = $theIssue->getAssignee()->getID();
								$grouptitle = TBGContext::getI18n()->__('Issues assigned to %assignee%', array('%assignee%' => $theIssue->getAssignee()));
							}
							else
							{
								$this_groupby = 0;
								$grouptitle = TBGContext::getI18n()->__('Unassigned issues');
							}
							break;
						default:
							break;
					}
					if ($prev_groupby !== $this_groupby)
					{
						echo "</table>\n";
						if ($group_cc > 0 && (!($bugs_response->getPage() == 'index' && $this->getSetting('showindexsummarydetails') == 0)))
						{
							echo '<div style="color: #AAA; font-size: 12px; border-bottom: 1px solid #DDD;">' . TBGContext::getI18n()->__('%count% matches in this group', array('%count%' => $group_cc)) . '</div>';
						}
						echo '<table style="table-layout: fixed; width: 100%; background-color: #FFF;" cellpadding=0 cellspacing=0 class="bug_list">' . "\n";
						if ($search_hits > 0) 
						{
							echo '<tr style="background-color: #FFF; " class="search_results">' . "\n";
							echo '<td style="padding-top: 10px;">&nbsp;</td>' . "\n";
							echo "</tr>\n";
						}
						echo "<tr>\n";
						echo '<td class="searchgroupheader">';
				
						print "<b>" . $grouptitle . "</b>";
				
						echo "</td>\n";
						echo "</tr>\n";
						echo "</table>\n";
						echo '<table style="table-layout: fixed; width: 100%; background-color: #FFF;" cellpadding=0 cellspacing=0 class="bug_list">' . "\n";
						$prev_groupby = $this_groupby;
						$header_cc = 0;
						$newgroup = true;
						$group_cc = 0;
					}
					else
					{
						$newgroup = false;
					}
				}
				if ($header_cc == 0)
				{
					$Rcc = 1;
					while ($Rcc <= $layoutrows)
					{
						echo "<tr>\n";
						foreach ($layoutfields as $aLayoutField)
						{
							if ($aLayoutField['row'] == $Rcc)
							{
								echo '<td class="searchtitlecolumns" style="padding: 0px; text-align: ' . $aLayoutField['align'] . '; padding-left: 2px; padding-right: 2px; width: ';
								echo ($aLayoutField['width'] == "0") ? "auto" : $aLayoutField['width'] . 'px';
								echo '; font-weight: bold; font-size: 10px; border-bottom: ';
								echo ($Rcc + $aLayoutField['span_rows'] == $layoutrows) ? '1px solid #E5E5E5;' : '0px;';
								echo (TBGContext::getRequest()->getParameter('sort_column') == $aLayoutField['field']) ? ' border-bottom: 2px solid #CCC;' : '';
								echo '"';
								if ($aLayoutField['span_cols'] > 0)
								{
									 echo ' colspan=' . $aLayoutField['span_cols'];
								}
								if ($aLayoutField['span_rows'] > 0)
								{
									 echo ' rowspan=' . $aLayoutField['span_rows'];
								}
								echo '>';
								if (TBGContext::getRequest()->getParameter('sort_column') == $aLayoutField['field'])
								{
									if (TBGContext::getRequest()->getParameter('sort') == 'desc')
									{
										echo image_tag('sort_desc.png', ' align="left"');
									}
									else
									{
										echo image_tag('sort_asc.png', ' align="left"');
									}
								}
								if ($bugs_response->getPage() == 'search')
								{
									echo '<a href="' . TBGContext::getTBGPath() . 'modules/search/search.php?perform_search=true';
									echo (TBGContext::getRequest()->getParameter('s_id')) ? '&amp;saved_search=true&amp;s_id=' . TBGContext::getRequest()->getParameter('s_id') : '&amp;custom_search=true';
									echo (TBGContext::getRequest()->getParameter('sort') == 'asc') ? '&amp;sort=desc' : '&amp;sort=asc';
									echo '&amp;sort_column=' . $aLayoutField['field'] . '">';
								}
								switch($aLayoutField['field'])
								{
									case 'B2tIssues::STATUS':
										if ($aLayoutField['field_type'] > 1)
										{
											echo TBGContext::getI18n()->__('Status');
										}
										else
										{
											echo '<div style="font-size: 9px;">' . TBGContext::getI18n()->__('Stat') . '</div>';
										}
										break;
									case 'B2tIssues::ISSUE_NO':
										echo '<div style="font-size: 9px;">' . TBGContext::getI18n()->__('Issue #') . '</div>';
										break;
									case 'B2tIssues::TITLE':
										echo TBGContext::getI18n()->__('Title');
										break;
									case 'B2tIssueAffectsComponent::COMPONENT':
										echo TBGContext::getI18n()->__('Component(s)');
										break;
									case 'B2tIssues::ASSIGNED_TO':
										echo TBGContext::getI18n()->__('Assignee');
										break;
									case 'B2tIssues::SEVERITY':
										echo TBGContext::getI18n()->__('Severity');
										break;
									case 'B2tIssues::LAST_UPDATED':
										echo '<div style="font-size: 9px;">' . TBGContext::getI18n()->__('Updated') . '</div>';
										break;
									case 'B2tMilestones::NAME':
										echo TBGContext::getI18n()->__('Milestone');
										break;
									case 'B2tIssues::PERCENT_COMPLETE':
										echo TBGContext::getI18n()->__('% complete');
										break;
									case 'B2tIssues::DESCRIPTION':
										echo TBGContext::getI18n()->__('Description');
										break;
									case 'B2tComments::ID':
										echo ($bugs_response->getPage() == 'search') ? '</a>&nbsp;<a>' : '&nbsp;';
										break;
									default:
										echo '';
								}
								if ($bugs_response->getPage() == 'search')
								{
									echo '</a>';
								}
								echo "</td>\n";
							}
						}
						echo "</tr>\n";
						$Rcc++;
					}
				}
				$search_hits++;
				$header_cc = ($header_cc < 14) ? $header_cc + 1 : 0;
				$group_cc++;
				$Rcc = 1;
				while ($Rcc <= $layoutrows)
				{
					echo '<tr class="issue_';
					echo ($theIssue->getState() == TBGIssue::STATE_OPEN) ? 'open' : 'closed';
					echo ($theIssue->isBlocking()) ? ' issue_blocker' : ' search_results';
					echo "\">\n";
					foreach ($layoutfields as $aLayoutField)
					{
						if ($aLayoutField['row'] == $Rcc)
						{
							switch($aLayoutField['field'])
							{
								case 'B2tIssues::STATUS': # "status":
									echo '<td style="padding: 0px; text-align: ' . $aLayoutField['align'] . '; padding-left: 3px; width: ';
									echo ($aLayoutField['width'] == "0") ? "auto" : $aLayoutField['width'] . 'px';
									echo '; height: ';
									echo ($aLayoutField['height'] == "0") ? "auto" : $aLayoutField['height'] . 'px';
									echo '; border-bottom: ';
									echo ($Rcc + $aLayoutField['span_rows'] == $layoutrows) ? '1px solid #DDD' : '0px';
									echo ';"';
									if ($aLayoutField['span_cols'] > 0)
									{
										 echo ' colspan=' . $aLayoutField['span_cols'];
									}
									if ($aLayoutField['span_rows'] > 0)
									{
										 echo ' rowspan=' . $aLayoutField['span_rows'];
									}
									echo '>';
									if ($aLayoutField['field_type'] == 1)
									{
										echo '<div class="status_box" style="width: 15px; height: 15px; font-size: 2px; background-color: ' . $theIssue->getStatus()->getColor() . ';" title="' . $theIssue->getStatus() . '">&nbsp;</div>';
									}
									elseif ($aLayoutField['field_type'] == 2)
									{
										echo '<b>';
										echo ($aLayoutField['include_desc'] == 1) ? TBGContext::getI18n()->__('Stat') . ':&nbsp;' : '';
										echo '</b>';
										echo $theIssue->getStatus();
									}
									elseif ($aLayoutField['field_type'] == 3)
									{
										echo '<table cellpadding=0 cellspacing=0 style="width: 100%;">';
										echo '<tr>';
										echo '<td style="width: 20px;"><div class="status_box" style="background-color: ';
										echo ($theIssue->getStatus() !== null) ? $theIssue->getStatus()->getColor() : '#FFF';
										echo '; font-size: 1px; width: 13px; height: 13px;">&nbsp;</div></td>';
										echo '<td style="width: auto; padding: 2px;';
										echo '">';
										echo ($theIssue->getStatus() !== null) ? $theIssue->getStatus() : __('Not determined');
										echo '</td>';
										echo '</tr>';
										echo '</table>';
									}
									elseif ($aLayoutField['field_type'] == 4)
									{
										echo '<b>';
										echo ($aLayoutField['include_desc'] == 1) ? 'Stat:&nbsp;' : '';
										echo '</b>';
										echo '<span style="font-weight: bold; color: ';
										if ($theIssue->getStatus()->getColor() != '#FFF')
										{
											echo $theIssue->getStatus()->getColor() . '">';
										}
										else
										{
											echo '#BBB;">';
										}
										echo $theIssue->getStatus();
										echo '</span>';
									}
									echo '</td>';
									break;
								case 'B2tIssues::ISSUE_NO': # "issue_no":
									echo '<td class="search_column_issue_no" style="padding: 0px; padding-left: 2px; text-align: ' . $aLayoutField['align'] . '; width: ';
									echo ($aLayoutField['width'] == "0") ? "auto" : $aLayoutField['width'] . 'px';
									echo '; height: ';
									echo ($aLayoutField['height'] == "0") ? "auto" : $aLayoutField['height'] . 'px';
									echo '; border-bottom: ';
									echo ($Rcc + $aLayoutField['span_rows'] >= $layoutrows) ? '1px solid #DDD' : '0px';
									echo ';"';
									if ($aLayoutField['span_cols'] > 0)
									{
										 echo ' colspan=' . $aLayoutField['span_cols'];
									}
									if ($aLayoutField['span_rows'] > 0)
									{
										 echo ' rowspan=' . $aLayoutField['span_rows'];
									}
									echo '>';
									if ($aLayoutField['field_type'] == 1)
									{
										echo '<a href="';
										echo ($frommodule) ? "../../" : "";
										echo 'viewissue.php?issue_no=' . $theIssue->getFormattedIssueNo(true);
										if ($s_id != 0) echo '&amp;search_queue=' . $s_id;
										echo '"';
										echo ($frommodule) ? ' target="_blank"' : '';
										echo '>' . $theIssue->getFormattedIssueNo() . '</a>';
									}
									elseif ($aLayoutField['field_type'] == 2)
									{
										echo $theIssue->getFormattedIssueNo();
									}
									echo '</td>';
									break;
								case 'B2tIssues::PERCENT_COMPLETE':
									echo '<td style="padding: 5px; text-align: ' . $aLayoutField['align'] . '; width: ';
									echo ($aLayoutField['width'] == "0") ? "auto" : $aLayoutField['width'] . 'px';
									echo '; height: ';
									echo ($aLayoutField['height'] == "0") ? "auto" : $aLayoutField['height'] . 'px';
									echo '; border-bottom: ';
									echo ($Rcc + $aLayoutField['span_rows'] == $layoutrows) ? '1px solid #DDD' : '0px';
									echo ';"';
									if ($aLayoutField['span_cols'] > 0)
									{
										 echo ' colspan=' . $aLayoutField['span_cols'];
									}
									if ($aLayoutField['span_rows'] > 0)
									{
										 echo ' rowspan=' . $aLayoutField['span_rows'];
									}
									echo '>';
									echo bugs_printPercentBar($theIssue->getPercentCompleted(), 12);
									echo '</td>';
									break;
								case 'B2tIssues::TITLE': # "issue_title":
									echo '<td class="search_column_title" style="padding: 0px; text-align: ' . $aLayoutField['align'] . '; width: ';
									echo ($aLayoutField['width'] == "0") ? "auto" : $aLayoutField['width'] . 'px';
									echo '; height: ';
									echo ($aLayoutField['height'] == "0") ? "auto" : $aLayoutField['height'] . 'px';
									echo '; border-bottom: ';
									echo ($Rcc + $aLayoutField['span_rows'] == $layoutrows) ? '1px solid #DDD' : '0px';
									echo ';"';
									if ($aLayoutField['span_cols'] > 0)
									{
										 echo ' colspan=' . $aLayoutField['span_cols'];
									}
									if ($aLayoutField['span_rows'] > 0)
									{
										 echo ' rowspan=' . $aLayoutField['span_rows'];
									}
									echo '>';
									echo ($aLayoutField['include_desc'] == 1) ? '<b>' . TBGContext::getI18n()->__('Title') . ':&nbsp;</b>' : '';
									if ($aLayoutField['field_type'] == 1)
									{
										echo '<a href="';
										echo ($frommodule) ? "../../" : "";
										echo 'viewissue.php?issue_no=' . $theIssue->getFormattedIssueNo(true) . '"';
										echo ($frommodule) ? ' target="_blank"' : '';
										echo '><b>' . $theIssue->getTitle() . '</b></a>';
									}
									elseif ($aLayoutField['field_type'] == 2)
									{
										print '<b>' . $theIssue->getTitle() . '</b>';
									}
									echo '</td>';
									break;
								case 'B2tIssueAffectsComponent::COMPONENT': # "component":
									echo '<td style="padding: 0px; text-align: ' . $aLayoutField['align'] . '; width: ';
									echo ($aLayoutField['width'] == "0") ? "auto" : $aLayoutField['width'] . 'px';
									echo '; height: ';
									echo ($aLayoutField['height'] == "0") ? "auto" : $aLayoutField['height'] . 'px';
									echo '; border-bottom: ';
									echo ($Rcc + $aLayoutField['span_rows'] == $layoutrows) ? '1px solid #DDD' : '0px';
									echo ';"';
									if ($aLayoutField['span_cols'] > 0)
									{
										 echo ' colspan=' . $aLayoutField['span_cols'];
									}
									if ($aLayoutField['span_rows'] > 0)
									{
										 echo ' rowspan=' . $aLayoutField['span_rows'];
									}
									echo ' class="small">';
									echo ($aLayoutField['include_desc'] == 1) ? '<b>' . TBGContext::getI18n()->__('Comp') . ':&nbsp;</b>' : '';
									$firstComponent = true;
									foreach ($theIssue->getComponents() as $anAffects)
									{
										if (!$firstComponent)
										{
											echo '<br>';
										}
										echo $anAffects['component'];
										$firstComponent = false;
									}
									echo '</td>';
									break;
								case 'B2tIssues::CATEGORY': # "category":
									echo '<td style="padding: 0px; text-align: ' . $aLayoutField['align'] . '; width: ';
									echo ($aLayoutField['width'] == "0") ? "auto" : $aLayoutField['width'] . 'px';
									echo '; height: ';
									echo ($aLayoutField['height'] == "0") ? "auto" : $aLayoutField['height'] . 'px';
									echo '; border-bottom: ';
									echo ($Rcc + $aLayoutField['span_rows'] == $layoutrows) ? '1px solid #DDD' : '0px';
									echo ';"';
									if ($aLayoutField['span_cols'] > 0)
									{
										 echo ' colspan=' . $aLayoutField['span_cols'];
									}
									if ($aLayoutField['span_rows'] > 0)
									{
										 echo ' rowspan=' . $aLayoutField['span_rows'];
									}
									echo ' class="small">';
									echo ($aLayoutField['include_desc'] == 1) ? '<b>' . TBGContext::getI18n()->__('Cat') . ':&nbsp;</b>' : '';
									echo $theIssue->getCategory()->getName() . '&nbsp;';
									echo '</td>';
									break;
								case 'B2tIssues::SEVERITY': # "severity":
									echo '<td style="padding: 0px; text-align: ' . $aLayoutField['align'] . '; width: ';
									echo ($aLayoutField['width'] == "0") ? "auto" : $aLayoutField['width'] . 'px';
									echo '; height: ';
									echo ($aLayoutField['height'] == "0") ? "auto" : $aLayoutField['height'] . 'px';
									echo '; border-bottom: ';
									echo ($Rcc + $aLayoutField['span_rows'] == $layoutrows) ? '1px solid #DDD' : '0px';
									echo ';"';
									if ($aLayoutField['span_cols'] > 0)
									{
										 echo ' colspan=' . $aLayoutField['span_cols'];
									}
									if ($aLayoutField['span_rows'] > 0)
									{
										 echo ' rowspan=' . $aLayoutField['span_rows'];
									}
									echo ' class="small">';
									echo '<div';
									echo ($theIssue->getSeverity() === null) ? ' class="issue_not_assigned"' : '';
									echo '>';
									echo ($aLayoutField['include_desc'] == 1) ? '<b>' . TBGContext::getI18n()->__('Sev') . ':&nbsp;</b>' : '';
									echo ($theIssue->getSeverity() instanceof TBGDatatype) ? $theIssue->getSeverity()->getName() : __('Not determined'); 
									echo '&nbsp;';
									echo '</div>';
									echo '</td>';
									break;
								case 'B2tIssues::ASSIGNED_TO':
									echo '<td style="padding: 0px; text-align: ' . $aLayoutField['align'] . '; width: ';
									echo ($aLayoutField['width'] == "0") ? "auto" : $aLayoutField['width'] . 'px';
									echo '; height: ';
									echo ($aLayoutField['height'] == "0") ? "auto" : $aLayoutField['height'] . 'px';
									echo '; border-bottom: ';
									echo ($Rcc + $aLayoutField['span_rows'] == $layoutrows) ? '1px solid #DDD' : '0px';
									echo ';"';
									if ($aLayoutField['span_cols'] > 0)
									{
										 echo ' colspan=' . $aLayoutField['span_cols'];
									}
									if ($aLayoutField['span_rows'] > 0)
									{
										 echo ' rowspan=' . $aLayoutField['span_rows'];
									}
									echo ' class="small">';
									if ($aLayoutField['include_desc'] == 1)
									{
										if ($theIssue->getAssigneeType() == TBGIdentifiableClass::TYPE_USER)
										{
											echo '<table>';
											echo bugs_userDropdown($theIssue->getAssignee());
											echo '</table>';
										}
										elseif ($theIssue->getAssigneeType() == TBGIdentifiableClass::TYPE_TEAM)
										{
											echo '<table>';
											echo bugs_teamDropdown($theIssue->getAssignee());
											echo '</table>';
										}
										else
										{
											echo '<div class="issue_not_assigned">' . TBGContext::getI18n()->__('Not assigned') . '</div>';
										}
									}
									else
									{
										if ($theIssue->getAssigneeType() == TBGIdentifiableClass::TYPE_USER || $theIssue->getAssigneeType() == TBGIdentifiableClass::TYPE_TEAM)
										{
											echo $theIssue->getAssignee();
										}
										else
										{
											echo '<div class="issue_not_assigned">' . TBGContext::getI18n()->__('Not assigned') . '</div>';
										}
									}
									echo '</td>';
									break;
								case 'B2tIssues::OWNER':
									echo '<td style="padding: 0px; text-align: ' . $aLayoutField['align'] . '; width: ';
									echo ($aLayoutField['width'] == "0") ? "auto" : $aLayoutField['width'] . 'px';
									echo '; height: ';
									echo ($aLayoutField['height'] == "0") ? "auto" : $aLayoutField['height'] . 'px';
									echo '; border-bottom: ';
									echo ($Rcc + $aLayoutField['span_rows'] == $layoutrows) ? '1px solid #DDD' : '0px';
									echo ';';
									echo ($theIssue->isBlocking()) ? ' color: #F44;' : '';
									echo '"';
									if ($aLayoutField['span_cols'] > 0)
									{
										 echo ' colspan=' . $aLayoutField['span_cols'];
									}
									if ($aLayoutField['span_rows'] > 0)
									{
										 echo ' rowspan=' . $aLayoutField['span_rows'];
									}
									echo ' class="small">';
									if ($aLayoutField['include_desc'] == 1)
									{
										if ($theIssue->getOwnerType() == TBGIdentifiableClass::TYPE_USER)
										{
											echo '<table>';
											echo bugs_userDropdown($theIssue->getOwner());
											echo '</table>';
										}
										elseif ($theIssue->getOwnerType() == TBGIdentifiableClass::TYPE_TEAM)
										{
											echo '<table>';
											echo bugs_teamDropdown($theIssue->getOwner()->getID());
											echo '</table>';
										}
										else
										{
											echo '<div class="issue_not_assigned">' . TBGContext::getI18n()->__('Not owned'). '</div>';
										}
									}
									else
									{
										if ($theIssue->getOwnerType() == TBGIdentifiableClass::TYPE_USER || $theIssue->getOwnerType() == TBGIdentifiableClass::TYPE_TEAM)
										{
											echo $theIssue->getOwner();
										}
										else
										{
											echo '<div class="issue_not_assigned">' . TBGContext::getI18n()->__('Not owned'). '</div>';
										}
									}
									echo '</td>';
									break;
								case 'B2tIssues::SEVERITY': # "category":
									echo '<td style="padding: 0px; text-align: ' . $aLayoutField['align'] . '; width: ';
									echo ($aLayoutField['width'] == "0") ? "auto" : $aLayoutField['width'] . 'px';
									echo '; height: ';
									echo ($aLayoutField['height'] == "0") ? "auto" : $aLayoutField['height'] . 'px';
									echo '; border-bottom: ';
									echo ($Rcc + $aLayoutField['span_rows'] == $layoutrows) ? '1px solid #DDD' : '0px';
									echo ';"';
									if ($aLayoutField['span_cols'] > 0)
									{
										 echo ' colspan=' . $aLayoutField['span_cols'];
									}
									if ($aLayoutField['span_rows'] > 0)
									{
										 echo ' rowspan=' . $aLayoutField['span_rows'];
									}
									echo ' class="small">';
									echo ($aLayoutField['include_desc'] == 1) ? '<b>' . TBGContext::getI18n()->__('Sev') . ':&nbsp;</b>' : '';
									echo ($theIssue->getSeverity()->getID() != '') ? $theIssue->getSeverity()->getName() : '<span style="color: #AAA;">' . $theIssue->getSeverity()->getName() . '</span>';
									echo '&nbsp;';
									echo '</td>';
									break;
								case 'B2tMilestones::NAME': # "milestone":
									echo '<td style="padding: 0px; text-align: ' . $aLayoutField['align'] . '; width: ';
									echo ($aLayoutField['width'] == "0") ? "auto" : $aLayoutField['width'] . 'px';
									echo '; height: ';
									echo ($aLayoutField['height'] == "0") ? "auto" : $aLayoutField['height'] . 'px';
									echo '; border-bottom: ';
									echo ($Rcc + $aLayoutField['span_rows'] == $layoutrows) ? '1px solid #DDD' : '0px';
									echo ';"';
									if ($aLayoutField['span_cols'] > 0)
									{
										 echo ' colspan=' . $aLayoutField['span_cols'];
									}
									if ($aLayoutField['span_rows'] > 0)
									{
										 echo ' rowspan=' . $aLayoutField['span_rows'];
									}
									echo ' class="small">';
									echo ($theIssue->getMilestone() instanceof TBGMilestone) ? $theIssue->getMilestone()->getName() : '<span style="color: #AAA;">' . TBGContext::getI18n()->__('No milestone set') . '</span>';
									echo '&nbsp;';
									echo '</td>';
									break;
								case 'B2tIssues::DESCRIPTION': # "description":
									echo '<td style="padding: 2px; text-align: ' . $aLayoutField['align'] . '; width: ';
									echo ($aLayoutField['width'] == "0") ? "auto" : $aLayoutField['width'] . 'px';
									echo '; height: ';
									echo ($aLayoutField['height'] == "0") ? "auto" : $aLayoutField['height'] . 'px';
									echo '; border-bottom: ';
									echo ($Rcc + $aLayoutField['span_rows'] == $layoutrows) ? '1px solid #DDD' : '0px';
									echo ';"';
									if ($aLayoutField['span_cols'] > 0)
									{
										 echo ' colspan=' . $aLayoutField['span_cols'];
									}
									if ($aLayoutField['span_rows'] > 0)
									{
										 echo ' rowspan=' . $aLayoutField['span_rows'];
									}
									echo ' class="small">';
									echo ucfirst(bugs_BBDecode($theIssue->getDescription()));
									echo '</td>';
									break;
								case 'B2tIssues::LAST_UPDATED': # "issue_last_update":
									echo '<td style="padding: 0px; text-align: ' . $aLayoutField['align'] . '; width: ';
									echo ($aLayoutField['width'] == "0") ? "auto" : $aLayoutField['width'] . 'px';
									echo '; height: ';
									echo ($aLayoutField['height'] == "0") ? "auto" : $aLayoutField['height'] . 'px';
									echo '; border-bottom: ';
									echo ($Rcc + $aLayoutField['span_rows'] >= $layoutrows) ? '1px solid #DDD' : '0px';
									echo ';"';
									if ($aLayoutField['span_cols'] > 0)
									{
										 echo ' colspan=' . $aLayoutField['span_cols'];
									}
									if ($aLayoutField['span_rows'] > 0)
									{
										 echo ' rowspan=' . $aLayoutField['span_rows'];
									}
									echo ' class="small">';
									echo ($aLayoutField['include_desc'] == 1) ? '<b>' . TBGContext::getI18n()->__('Upd') . ':&nbsp;</b>' : '';
									if ($aLayoutField['field_type'] == 1)
									{
										echo bugs_formatTime($theIssue->getLastUpdatedTime(), 14);
									}
									elseif ($aLayoutField['field_type'] == 2)
									{
										echo str_replace(', ', '<br>', bugs_formatTime($theIssue->getLastUpdatedTime(), 12));
									}
									elseif ($aLayoutField['field_type'] == 3)
									{
										echo bugs_formatTime($theIssue->getLastUpdatedTime(), 12);
									}
									echo '&nbsp;';
									echo '</td>';
									break;
								case 'B2tIssues::POSTED': # "issue_reg_date":
									echo '<td style="padding: 0px; text-align: ' . $aLayoutField['align'] . '; width: ';
									echo ($aLayoutField['width'] == "0") ? "auto" : $aLayoutField['width'] . 'px';
									echo '; height: ';
									echo ($aLayoutField['height'] == "0") ? "auto" : $aLayoutField['height'] . 'px';
									echo '; border-bottom: ';
									echo ($Rcc + $aLayoutField['span_rows'] == $layoutrows) ? '1px solid #DDD' : '0px';
									echo ';"';
									if ($aLayoutField['span_cols'] > 0)
									{
										 echo ' colspan=' . $aLayoutField['span_cols'];
									}
									if ($aLayoutField['span_rows'] > 0)
									{
										 echo ' rowspan=' . $aLayoutField['span_rows'];
									}
									echo ' class="small">';
									if ($aLayoutField['field_type'] == 1)
									{
										echo bugs_formatTime($theIssue->getPosted(), 14);
									}
									elseif ($aLayoutField['field_type'] == 2)
									{
										echo str_replace(', ', '<br>', bugs_formatTime($theIssue->getPosted(), 12));
									}
									elseif ($aLayoutField['field_type'] == 3)
									{
										echo bugs_formatTime($theIssue->getPosted(), 12);
									}
									echo '&nbsp;';
									echo '</td>';
									break;
								case 'B2tComments::ID': # "comments":
									if ($aLayoutField['icon'] == 1)
									{
										echo '<td style="padding: 0px; text-align: ' . $aLayoutField['align'] . '; padding-left: 3px; width: 20px; border-bottom: ';
										echo ($Rcc + $aLayoutField['span_rows'] >= $layoutrows) ? '1px solid #DDD' : '0px';
										echo ';"';
										if ($aLayoutField['span_cols'] > 0)
										{
											 echo ' colspan=' . $aLayoutField['span_cols'];
										}
										if ($aLayoutField['span_rows'] > 0)
										{
											 echo ' rowspan=' . $aLayoutField['span_rows'];
										}
										echo '>' . image_tag('comments.png') . '</td>';
									}
									else
									{
										echo '<td style="padding: 0px; text-align: ' . $aLayoutField['align'] . '; width: ';
										echo ($aLayoutField['width'] == "0") ? "auto" : $aLayoutField['width'] . 'px';
										echo '; height: ';
										echo ($aLayoutField['height'] == "0") ? "auto" : $aLayoutField['height'] . 'px';
										echo '; border-bottom: ';
										echo ($Rcc + $aLayoutField['span_rows'] >= $layoutrows) ? '1px solid #DDD;' : '0px;';
										echo ';"';
										if ($aLayoutField['span_cols'] > 0)
										{
											 echo ' colspan=' . $aLayoutField['span_cols'];
										}
										if ($aLayoutField['span_rows'] > 0)
										{
											 echo ' rowspan=' . $aLayoutField['span_rows'];
										}
										echo '>' . $theIssue->getCommentCount() . '</td>';
									}
									break;
							}
							echo "\n";
						}
					}
					echo "</tr>\n";
					$Rcc++;
				}
			}
			echo "</table>\n";
			if (!($bugs_response->getPage() == 'index' && $this->getSetting('showindexsummarydetails') == 0))
			{
				echo '<div style="color: #AAA; font-size: 12px; border-bottom: 1px solid #DDD;">' . TBGContext::getI18n()->__('%count% matches in this group', array('%count%' => $group_cc)) . '</div>';
			}
		}
		
		
		public function presentResultsJSON($result)
		{
			
		}
		
		public function getSavedSearches($applies_to = 0)
		{
			$crit = new B2DBCriteria();
			$ctn = $crit->returnCriterion(B2tSavedSearches::IS_PUBLIC, 1);
			$ctn->addOr(B2tSavedSearches::UID, TBGContext::getUser()->getUID());
			$crit->addWhere($ctn);
			$crit->addWhere(B2tSavedSearches::APPLIES_TO, $applies_to);
			$crit->addWhere(B2tSavedSearches::SCOPE, TBGContext::getScope()->getID());
			$crit->addOrderBy(B2tSavedSearches::ID, B2DBCriteria::SORT_ASC);
			$res = B2DB::getTable('B2tSavedSearches')->doSelect($crit);
			
			$searches = array();
			while ($row = $res->getNextRow())
			{
				$searches[] = array('id' => $row->get(B2tSavedSearches::ID), 'name' => $row->get(B2tSavedSearches::NAME));
			}
			return $searches;
		}
		
		public function section_openSearch()
		{
			if ($this->hasAccess())
			{
				print '<link rel="search" type="application/opensearchdescription+xml" title="' . TBGSettings::getTBGname() . '" href="' . TBGSettings::get('url_host') . '' . TBGSettings::get('url_subdir') . 'modules/search/opensearch.xml.php">';
			}
		}
		
		public function section_quickSearch()
		{
			if ($this->hasAccess())
			{
				?>

				<?php
			}
		}
		
		public function section_viewissueTop($theIssue)
		{
			$url_options = '';
			if (TBGContext::getRequest()->getParameter('search_queue')) 
			{
				$_SESSION['search_queue'] = TBGContext::getRequest()->getParameter('search_queue');
				$url_options = '&amp;search_queue=' . TBGContext::getRequest()->getParameter('search_queue');
			}
			
			?>
			<div style="width: auto; padding: 5px; padding-bottom: 0px;">
			<table style="border: 0px; width: 100%; padding: 2px;">
			<tr>
			<td style="width: 20px;" valign="middle"><a href="modules/search/search.php?previous_issue=true&amp;open=true&amp;current=<?php echo $theIssue->getFormattedIssueNo(true) . $url_options; ?>" class="image"><?php echo image_tag('search_go_prev2.png', '', TBGContext::getI18n()->__('Previous open issue'), TBGContext::getI18n()->__('Previous open issue')); ?></a></td>
			<td style="width: 160px;" valign="middle"><a href="modules/search/search.php?previous_issue=true&amp;open=true&amp;current=<?php echo $theIssue->getFormattedIssueNo(true) . $url_options; ?>"><b><?php echo TBGContext::getI18n()->__('Go to previous open issue'); ?></b></a></td>
			<td style="width: 20px;" valign="middle"><a href="modules/search/search.php?previous_issue=true&amp;current=<?php echo $theIssue->getFormattedIssueNo(true) . $url_options; ?>" class="image"><?php echo image_tag('search_go_prev.png', '', TBGContext::getI18n()->__('Previous issue'), TBGContext::getI18n()->__('Previous issue')); ?></a></td>
			<td style="width: 110px;" valign="middle"><a href="modules/search/search.php?previous_issue=true&amp;current=<?php echo $theIssue->getFormattedIssueNo(true) . $url_options; ?>"><?php echo TBGContext::getI18n()->__('Go to previous issue'); ?></a></td>
			<td style="text-align: center;">&nbsp;</td>
			<td style="width: 110px; text-align: right;" valign="middle"><a href="modules/search/search.php?next_issue=true&amp;current=<?php echo $theIssue->getFormattedIssueNo(true) . $url_options; ?>"><?php echo TBGContext::getI18n()->__('Go to next issue'); ?></a></td>
			<td style="width: 20px;" valign="middle"><a href="modules/search/search.php?next_issue=true&amp;current=<?php echo $theIssue->getFormattedIssueNo(true) . $url_options; ?>" class="image"><?php echo image_tag('search_go_next.png', '', TBGContext::getI18n()->__('Next issue'), TBGContext::getI18n()->__('Next issue')); ?></a></td>
			<td style="width: 140px; text-align: right;" valign="middle"><a href="modules/search/search.php?next_issue=true&amp;open=true&amp;current=<?php echo $theIssue->getFormattedIssueNo(true) . $url_options; ?>"><b><?php echo TBGContext::getI18n()->__('Go to next open issue'); ?></b></a></td>
			<td style="width: 20px;" valign="middle"><a href="modules/search/search.php?next_issue=true&amp;open=true&amp;current=<?php echo $theIssue->getFormattedIssueNo(true) . $url_options; ?>" class="image"><?php echo image_tag('search_go_next2.png', '', TBGContext::getI18n()->__('Next open issue'), TBGContext::getI18n()->__('Next open issue')); ?></a></td>
			</tr>
			</table>
			<?php
	
				if (TBGContext::getRequest()->getParameter('search_message'))
				{
					?>
					<div style="padding: 5px; border: 0px; background-color: #FFF;">
					<?php
					switch (TBGContext::getRequest()->getParameter('search_message'))
					{
						case 1:
							echo '<b>' . TBGContext::getI18n()->__('This is the last issue in this queue') . '</b>';
							break;
						case 2:
							echo '<b>' . TBGContext::getI18n()->__('This is the last open issue in this queue') . '</b>';
							break;
						case 3:
							echo '<b>' . TBGContext::getI18n()->__('This is the first issue in this queue') . '</b>';
							break;
						case 4:
							echo '<b>' . TBGContext::getI18n()->__('This is the first open issue in this queue') . '</b>';
							break;
					}
					?>
					</div>
					<?php
				}
	
			?>
			</div>
			<?php
		}
		
		public function section_viewissueLeftMiddleTop()
		{
			?>
			<div style="margin-top: 10px; margin-bottom: 0px; border: 0px;">
				<div style="border-bottom: 1px solid #DDD; padding: 3px; font-size: 12px;">
				<b><?php echo TBGContext::getI18n()->__('Reports'); ?></b>
				</div>
				<div class="issuedetailscontentsleft" style="padding-top: 5px; padding-bottom: 5px;" id="svn_checkins">
					<table cellpadding=0 cellspacing=0>
					<?php if (TBGUser::isThisGuest() == false): ?>
						<tr>
						<td class="imgtd"><?php echo image_tag('assigned_bugs.png'); ?></td>
						<td><a href="<?php print TBGContext::getTBGPath(); ?>modules/search/search.php?custom_search=true&amp;clear_filters=true&amp;perform_search=true&amp;groupby=state&amp;add_filter=5&amp;filter_cc=0&amp;operator=B2DBCriteria::DB_EQUALS&amp;value=<?php print TBGContext::getUser()->getUID(); ?>"><?php echo TBGContext::getI18n()->__('View issues filed by you'); ?></a></td>
						</tr>
					<?php endif; ?>
					</table>
				</div>
			</div>
			<?php
		}
		
		public function section_useractionsBottom($vars)
		{
			return '<div style="padding: 2px;"><a target="_blank" href="' . TBGContext::getTBGPath() . 'modules/search/search.php?custom_search=true&amp;clear_filters=true&amp;groupby=state&amp;perform_search=true&amp;add_filter=5&amp;filter_cc=0&amp;operator=B2DBCriteria::DB_EQUALS&amp;value=' . $vars['user']->getID() . '" onclick="' . $vars['closemenustring'] . '">' . TBGContext::getI18n()->__('View more issues from this user') . '</a></div>';			
		}
		
		public function section_indexLeftMyIssues($issues)
		{
			if (!TBGContext::getUser()->isGuest() && $issues > 0)
			{
				?>
				<tr>
				<td class="imgtd"><?php echo image_tag('assigned_bugs.png'); ?></td>
				<td><a target="_blank" href="<?php echo TBGContext::getTBGPath(); ?>modules/search/search.php?custom_search=true&amp;clear_filters=true&amp;perform_search=true&amp;groupby=state&amp;add_filter=5&amp;filter_cc=0&amp;operator=B2DBCriteria::DB_EQUALS&amp;value=<?php echo TBGContext::getUser()->getUID(); ?>"><?php echo TBGContext::getI18n()->__('View all issues filed by you'); ?></a></td>
				</tr>
				<?php
			}			
		}
		
		public function section_indexRightMiddleBottom()
		{
			$showsearches = $this->getSetting('indexshowsavedsearches');
			if ($showsearches == 1)
			{
				$frontpagelayout = $this->getSetting('frontpagelayout');
				$savedsearches = $this->getSetting('indexsearches');
				$searches = array();
				$searches = explode(";", $savedsearches);
				foreach($searches as $aSearch)
				{
					$search = B2DB::getTable('B2tSavedSearches')->doSelectById($aSearch);
					if ($search instanceof B2DBRow && (TBGContext::getUser()->hasPermission("b2projectaccess", $search->get(B2tSavedSearches::APPLIES_TO), "core") || $search->get(B2tSavedSearches::APPLIES_TO) == 0))
					{
						echo '<br>';
						$this->log("running search with id $aSearch");
						$searchresults = $this->doSearch($search, false, true, false, false);
						
						if (count($searchresults['issues']) > 0)
						{
							$this->log('presenting results');
							if ($frontpagelayout == 0)
							{
								$thelayout = $search->get(B2tSavedSearches::LAYOUT);
							}
							else
							{
								$thelayout = $frontpagelayout;
							}
							$showtitle = ($this->getSetting('indexshowtitle') == 0) ? false : true;
							$this->presentResultsHTML($thelayout, $searchresults['issues'], $aSearch, false, $search->get(B2tSavedSearches::NAME), $searchresults['groupby'], $showtitle);
						}
						else
						{
							$this->log('no results to present');
						}
						$this->log('...ok');
					}
				}
			}
		}
		
		public function section_TBGProject_createNew($theProject)
		{
			$layouts = $this->getLayouts();
			$scope = TBGContext::getScope()->getID();

			$values = array();
			$values[] = array('value' => $theProject->getID(), 'operator' => 'B2DBCriteria::DB_EQUALS', 'filter' => 2);
			
			$values2 = $values;
			$values2[] = array('value' => TBGIssue::STATE_CLOSED, 'operator' => 'B2DBCriteria::DB_EQUALS', 'filter' => 1);

			$values3 = $values;
			$values3[] = array('value' => TBGIssue::STATE_OPEN, 'operator' => 'B2DBCriteria::DB_EQUALS', 'filter' => 1);
			
			self::createSearch(__('All issues for %project%', array('%project%' => $theProject->getName())), $layouts[3]['id'], $theProject->getID(), 1, 1, 'state', $scope, $values);
			self::createSearch(__('Open issues for %project%', array('%project%' => $theProject->getName())), $layouts[3]['id'], $theProject->getID(), 1, 1, '', $scope, $values3);
			self::createSearch(__('Closed issues for %project%', array('%project%' => $theProject->getName())), $layouts[3]['id'], $theProject->getID(), 1, 1, '', $scope, $values2);
			
			self::createSearch(__('All issues (%project%) by milestone', array('%project%' => $theProject->getName())), $layouts[3]['id'], $theProject->getID(), 1, 1, 'milestone', $scope, $values);
			self::createSearch(__('Open issues (%project%) by milestone', array('%project%' => $theProject->getName())), $layouts[3]['id'], $theProject->getID(), 1, 1, 'milestone', $scope, $values3);
			self::createSearch(__('Closed issues (%project%) by milestone', array('%project%' => $theProject->getName())), $layouts[3]['id'], $theProject->getID(), 1, 1, 'milestone', $scope, $values2);
			
			self::createSearch(__('All issues (%project%) by assignee', array('%project%' => $theProject->getName())), $layouts[3]['id'], $theProject->getID(), 1, 1, 'assignee', $scope, $values);
			self::createSearch(__('Open issues (%project%) by assignee', array('%project%' => $theProject->getName())), $layouts[3]['id'], $theProject->getID(), 1, 1, 'assignee', $scope, $values3);
			self::createSearch(__('Closed issues (%project%) by assignee', array('%project%' => $theProject->getName())), $layouts[3]['id'], $theProject->getID(), 1, 1, 'assignee', $scope, $values2);
		}

		public function enableSection($module, $identifier, $scope)
		{
			$function_name = '';
			switch ($module . '_' . $identifier)
			{
				case 'core_header_ends':
					$function_name = 'section_openSearch';
					break;
				case 'core_header_right':
					$function_name = 'section_quickSearch';
					break;
				case 'core_viewissue_top':
					$function_name = 'section_viewissueTop';
					break;
				case 'core_viewissue_left_middle_top':
					$function_name = 'section_viewissueLeftMiddleTop';
					break;
				case 'core_useractions_bottom':
					$function_name = 'section_useractionsBottom';
					break;
				case 'core_index_left_myissues':
					$function_name = 'section_indexLeftMyIssues';
					break;
				case 'core_index_right_middle_bottom':
					$function_name = 'section_indexRightMiddleBottom';
					break;
				case 'core_TBGProject::createNew':
					$function_name = 'section_TBGProject_createNew';
					break;
			}
			if ($function_name != '') parent::registerPermanentTriggerListener($module, $identifier, $function_name, $scope);
		}
		
	}

?>