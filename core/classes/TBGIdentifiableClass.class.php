<?php

	/**
	 * An identifiable class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
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
	abstract class TBGIdentifiableClass implements TBGIdentifiable
	{
		
		const TYPE_USER = 1;
		const TYPE_TEAM = 2;
	
		/**
		 * The id for this item, usually identified by a record in the database
		 *
		 * @var integer
		 */
		protected $_itemid;
		
		/**
		 * The name of the object
		 *
		 * @var string
		 */
		protected $_name;
		
		/**
		 * The item type (if needed)
		 *
		 * @var string|integer
		 */
		protected $_itemtype;
		
		/**
		 * Return the items id
		 * 
		 * @return integer
		 */
		public function getID()
		{
			return $this->_itemid;
		}

		/**
		 * Return the items name
		 * 
		 * @return string
		 */
		public function getName()
		{
			return $this->_name;
		}
		
		public function getType()
		{
			return 0;
		}
		
	}
