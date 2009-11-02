<?php

	/**
	 * actions for the search module
	 */
	class searchActions extends BUGSaction
	{
		
		/**
		 * Performs quicksearch
		 * 
		 * @param BUGSrequest $request The request object
		 */		
		public function runQuickSearch($request)
		{
			$this->searchterm = $request->getParameter('searchfor');
		}
		
	}