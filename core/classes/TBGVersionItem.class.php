<?php

	/**
	 * Versionable item class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage core
	 */

	/**
	 * Versionable item class
	 *
	 * @package thebuggenie
	 * @subpackage core
	 */
	class TBGVersionItem extends TBGIdentifiableClass
	{
		/**
		 * Major version
		 *
		 * @var integer
		 * @access protected
		 */
		protected $_version_major = 0;
		
		/**
		 * Minor version
		 *
		 * @var integer
		 * @access protected
		 */
		protected $_version_minor = 0;
		
		/**
		 * Revision
		 *
		 * @var integer
		 * @access protected
		 */
		protected $_version_revision = 0;
		
		/**
		 * Is this item the default
		 *
		 * @var boolean
		 * @access protected
		 */
		protected $_isdefault = null;
		
		/**
		 * Item type
		 *
		 * @var integer
		 * @access protected
		 */
		protected $_itemtype = 0;
		
		/**
		 * Whether the item is locked or not
		 *
		 * @var boolean
		 * @access protected
		 */
		protected $_locked = null;
		
		protected $_isreleased = null;
		
		protected $_isplannedreleased = null;
		
		protected $_release_date = 0;
		
		const PROJECT = 1;
		
		const EDITION = 2;
		
		const BUILD = 3;
		
		const COMPONENT = 4;
		
		/**
		 * Invoked when trying to print the object
		 *
		 * @return string
		 */
		public function __toString()
		{
			return $this->_name;
		}
		
		/**
		 * Returns the name of the item
		 *
		 * @return string
		 */
		public function getName()
		{
			return $this->_name;
		}
		
		/**
		 * Returns the id of the item
		 *
		 * @return integer
		 */
		public function getID()
		{
			return $this->_itemid;
		}
		
		/**
		 * Returns the item type of the object
		 *
		 * @return integer
		 * @access public
		 */
		public function getItemType()
		{
			return $this->_itemtype;
		}
		
		/**
		 * Returns whether or not the object is the default selection for this type
		 *
		 * @return boolean
		 * @access public
		 */
		public function isDefault()
		{
			return $this->_isdefault;
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
		 * Returns the complete version number
		 * 
		 * @return string
		 */
		public function getVersion()
		{
			return $this->_version_major . '.' . $this->_version_minor . '.' . $this->_version_revision;
		}
		
		/**
		 * Returns the major version number
		 *
		 * @return integer
		 */
		public function getMajor()
		{
			return $this->_version_major;
		}
		
		/**
		 * Returns the minor version number
		 *
		 * @return integer
		 */
		public function getMinor()
		{
			return $this->_version_minor;
		}
		
		/**
		 * Returns revision number
		 *
		 * @return mixed
		 */
		public function getRevision()
		{
			return $this->_version_revision;
		}

		/**
		 * Is the project released?
		 * 
		 * @return boolean
		 */
		public function isReleased()
		{
			return $this->_isreleased;
		}
		
		public function setReleased($val)
		{
			$this->_isreleased = (bool) $val;
		}

		/**
		 * Is the project planned released?
		 * 
		 * @return boolean
		 */
		public function isPlannedReleased()
		{
			return $this->_isplannedreleased;
		}
		
		public function setPlannedReleased($val)
		{
			$this->_isplannedreleased = (bool) $val;
		}
		
		/**
		 * Returns the (planned/)release date
		 *
		 * @return integer
		 */
		public function getReleaseDate()
		{
			return $this->_release_date;
		}
		
		public function getReleaseDateYear()
		{
			return date("Y", $this->_release_date);
		}
		
		public function getReleaseDateMonth()
		{
			return date("n", $this->_release_date);
		}

		public function getReleaseDateDay()
		{
			return date("j", $this->_release_date);
		}
		
	}
