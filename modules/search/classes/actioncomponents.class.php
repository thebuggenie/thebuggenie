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
		}

		public function componentResults_normal()
		{

		}

		public function componentResults_rss()
		{
			$this->getResponse()->setContentType('application/xml');
			$this->getResponse()->setDecoration(BUGSresponse::DECORATE_NONE);
		}
	}