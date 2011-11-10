<?php

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
	abstract class TBGIdentifiableScopedClass extends TBGScopedClass implements TBGIdentifiable
	{
		
		/**
		 * The id for this item, usually identified by a record in the database
		 *
		 * @var integer
		 * @Id
		 * @Column(type="integer")
		 */
		protected $_id;
		
		/**
		 * The name of the object
		 *
		 * @var string
		 * @Column(type="string")
		 */
		protected $_name;
		
		/**
		 * Return the items id
		 * 
		 * @return integer
		 */
		public function getID()
		{
			return (int) $this->_id;
		}

		/**
		 * Set the items id
		 *
		 * @param integer $id
		 */
		public function setID($id)
		{
			$this->_id = $id;
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

		/**
		 * Set the edition name
		 *
		 * @param string $name
		 */
		public function setName($name)
		{
			$this->_name = $name;
		}

		/**
		 * Returns whether or not the object is locked
		 *
		 * @return boolean
		 * @access public
		 */
		public function isLocked()
		{
			return $this->_locked;
		}

		/**
		 * Set if the edition is locked
		 *
		 * @param boolean $locked[optional]
		 */
		public function setLocked($locked = true)
		{
			$this->_locked = (bool) $locked;
		}

		public function lock()
		{
			$this->setLocked(true);
		}

		public function unlock()
		{
			$this->setLocked(false);
		}

	}
