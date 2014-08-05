<?php

	/**
	 * Common interface for objects providing a list of related users
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.3
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage core
	 */

	/**
	 * Common interface for objects providing a list of related users
	 *
	 * @package thebuggenie
	 * @subpackage core
	 */
	interface TBGMentionableProvider
	{
		
		/**
		 * Returns an array of users
		 * 
		 * @return array|TBGUser
		 */
		public function getMentionableUsers();
		
	}

