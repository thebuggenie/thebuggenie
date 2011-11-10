<?php

	use b2db\Saveable;

	/**
	 * An identifiable class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage core
	 */

	/**
	 * An identifiable class
	 *
	 * @package thebuggenie
	 * @subpackage core
	 */
	abstract class TBGIdentifiableTypeClass extends TBGIdentifiableScopedClass implements TBGTypeable
	{
		
		const TYPE_USER = 1;
		const TYPE_TEAM = 2;
		const TYPE_CLIENT = 3;
	
		/**
		 * The item type (if needed)
		 *
		 * @var string|integer
		 */
		protected $_itemtype;

		public function getType()
		{
			return 0;
		}

	}
