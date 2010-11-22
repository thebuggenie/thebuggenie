<?php

	/**
	 * List of interfaces
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 ** @version 3.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage core
	 */
	
	/**
	 * Assignable interface
	 * 
	 * @package thebuggenie
	 * @subpackage core
	 */
	interface TBGAssignable extends TBGIdentifiable
	{
		/**
		 * Returns the assigned type
		 * 
		 * @return integer
		 */
		public function getAssignedType();
		
		/**
		 * Returns the assignee
		 *
		 * @return TBGIdentifiable
		 */
		public function getAssignee();
		
	}
	