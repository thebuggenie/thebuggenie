<?php

	/**
	 * Versionable item class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
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
		 * Release the edition
		 *
		 * @uses self::setReleased()
		 */
		public function release()
		{
			$this->setReleased(true);
		}

		public function retract()
		{
			$this->setReleased(false);
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
		 * Set the version
		 *
		 * @param integer $ver_mj Major version number
		 * @param integer $ver_mn Minor version number
		 * @param integer $ver_rev Version revision
		 */
		public function setVersion($ver_mj, $ver_mn, $ver_rev)
		{
			$ver_mj = ((int) $ver_mj > 0) ? (int) $ver_mj : 0;
			$ver_mn = ((int) $ver_mn > 0) ? (int) $ver_mn : 0;
			$ver_rev = ((int) $ver_rev > 0) ? (int) $ver_rev : 0;

			$this->_version_major = $ver_mj;
			$this->_version_minor = $ver_mn;
			$this->_version_revision = $ver_rev;
		}

		/**
		 * Set the major version number
		 *
		 * @param $ver_mj
		 */
		public function setVersionMajor($ver_mj)
		{
			$ver_mj = ((int) $ver_mj > 0) ? (int) $ver_mj : 0;
			$this->_version_major = $ver_mj;
		}

		/**
		 * Set the minor version number
		 *
		 * @param $ver_mn
		 */
		public function setVersionMinor($ver_mn)
		{
			$ver_mn = ((int) $ver_mn > 0) ? (int) $ver_mn : 0;
			$this->_version_minor = $ver_mn;
		}

		/**
		 * Set the version revision number
		 *
		 * @param $ver_rev
		 */
		public function setVersionRevision($ver_rev)
		{
			$ver_rev = ((int) $ver_rev > 0) ? (int) $ver_rev : 0;
			$this->_version_revision = $ver_rev;
		}

		/**
		 * Returns the major version number
		 *
		 * @return integer
		 */
		public function getVersionMajor()
		{
			return $this->_version_major;
		}
		
		/**
		 * Returns the minor version number
		 *
		 * @return integer
		 */
		public function getVersionMinor()
		{
			return $this->_version_minor;
		}
		
		/**
		 * Returns revision number
		 *
		 * @return mixed
		 */
		public function getVersionRevision()
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
		
		public function setReleased($val = true)
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
		 * Set the release date
		 *
		 * @param integer $release_date
		 */
		public function setReleaseDate($release_date = null)
		{
			if ($release_date === null) $release_date = NOW;
			$this->_release_date = $release_date;
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
		
		public function getReleaseDateHour()
		{
			return date("h", $this->_release_date);
		}
		
		public function getReleaseDateMinute()
		{
			return date("i", $this->_release_date);
		}
		
		public function getReleaseDateAMPM()
		{
			return date("A", $this->_release_date);
		}
		
	}
