<?php

	/**
	 * Content parser common interface
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.3
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage core
	 */

	/**
	 * Common content parser interface to be implemented by all custom content parsers
	 *
	 * @package thebuggenie
	 * @subpackage core
	 */
	interface TBGContentParser
	{
		
		/**
		 * Returns an array of mentioned users
		 * 
		 * @return array|TBGUser
		 */
		public function getMentions();
		
		/**
		 * Whether there are mentioned users in this content
		 * 
		 * @return boolean
		 */
		public function hasMentions();
		
	}

