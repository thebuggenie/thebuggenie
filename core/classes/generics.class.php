<?php

	/**
	 * List of interfaces
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage core
	 */
	
	/**
	 * Identifiable interface
	 * 
	 * @package thebuggenie
	 * @subpackage core
	 */
	interface BUGSidentifiable
	{
		/**
		 * Returns the id of the item
		 * 
		 * @return integer
		 *
		 */
		public function getID();
		
		/**
		 * Returns the name of the item
		 * 
		 * @return string
		 *
		 */
		public function getName();
		
		/**
		 * Returns the type of object
		 * 
		 * @return integer
		 */
		public function getType();
		
		/**
		 * Invoked when trying to print the item directly
		 * 
		 * @return string
		 *
		 */
		public function __toString();
		
	}
	
	/**
	 * Assignable interface
	 * 
	 * @package thebuggenie
	 * @subpackage core
	 */
	interface BUGSassignable extends BUGSidentifiable
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
		 * @return BUGSidentifiable
		 */
		public function getAssignee();
		
	}
	