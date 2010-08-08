<?php

	class searchActionComponents extends TBGActionComponent
	{

		public function componentPagination()
		{
			$this->currentpage = ceil($this->offset / $this->ipp) + 1;
			$this->pagecount = ceil($this->resultcount / $this->ipp);
			$this->filters = serialize($this->filters);
			$this->route = (TBGContext::isProjectContext()) ? TBGContext::getRouting()->generate('project_search_paginated', array('project_key' => TBGContext::getCurrentProject()->getKey())) : TBGContext::getRouting()->generate('search_paginated');
		}

		public function componentFilter()
		{
			$i18n = TBGContext::getI18n();
			$this->selected_value = (isset($this->selected_value)) ? $this->selected_value : 0;
			$this->selected_operator = (isset($this->selected_operator)) ? $this->selected_operator : '=';

			$filters = array();
			$filters['status'] = array('description' => $i18n->__('Status'), 'options' => TBGStatus::getAll());
			$filters['category'] = array('description' => $i18n->__('Category'), 'options' => TBGCategory::getAll());
			$filters['priority'] = array('description' => $i18n->__('Priority'), 'options' => TBGPriority::getAll());
			$filters['severity'] = array('description' => $i18n->__('Severity'), 'options' => TBGSeverity::getAll());
			$filters['reproducability'] = array('description' => $i18n->__('Reproducability'), 'options' => TBGReproducability::getAll());
			$filters['resolution'] = array('description' => $i18n->__('Resolution'), 'options' => TBGResolution::getAll());
			$filters['issue_type'] = array('description' => $i18n->__('Issue type'), 'options' => TBGIssuetype::getAll());
			$this->filters = $filters;

		}

		public function componentResults_normal()
		{
		}

		public function componentResults_todo()
		{
		}

	}