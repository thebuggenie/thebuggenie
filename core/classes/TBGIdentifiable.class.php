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
	 * Identifiable interface
	 *
	 * @package thebuggenie
	 * @subpackage core
	 */
	interface TBGIdentifiable
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

	}
	