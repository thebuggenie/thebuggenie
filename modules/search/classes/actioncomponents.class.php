<?php

	class searchActionComponents extends BUGSactioncomponent
	{

		public function componentPagination()
		{
			$this->currentpage = ceil($this->offset / $this->ipp) + 1;
			$this->pagecount = ceil($this->resultcount / $this->ipp);
			$this->filters = serialize($this->filters);
			$this->route = (BUGScontext::isProjectContext()) ? BUGScontext::getRouting()->generate('project_search_paginated', array('project_key' => BUGScontext::getCurrentProject()->getKey())) : BUGScontext::getRouting()->generate('search_paginated');
		}

		public function componentFilter()
		{
			$this->selected_value = (isset($this->selected_value)) ? $this->selected_value : 0;
			$this->selected_operator = (isset($this->selected_operator)) ? $this->selected_operator : '=';

			$filters = array();
			$filters['status'] = array('description' => __('Status'), 'options' => BUGSstatus::getAll());
			$filters['category'] = array('description' => __('Category'), 'options' => BUGScategory::getAll());
			$filters['priority'] = array('description' => __('Priority'), 'options' => BUGSpriority::getAll());
			$filters['severity'] = array('description' => __('Severity'), 'options' => BUGSseverity::getAll());
			$filters['reproducability'] = array('description' => __('Reproducability'), 'options' => BUGSreproducability::getAll());
			$filters['resolution'] = array('description' => __('Resolution'), 'options' => BUGSresolution::getAll());
			$filters['issue_type'] = array('description' => __('Issue type'), 'options' => BUGSissuetype::getAll());
			$this->filters = $filters;

		}

		public function componentResults_normal()
		{
		}

		public function componentResults_todo()
		{
		}

		public function componentResults_rss()
		{
			$this->getResponse()->setContentType('application/xml');
			$this->getResponse()->setDecoration(BUGSresponse::DECORATE_NONE);
		}
	}