<?php

	/**
	 * Class used for builds/versions
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage main
	 */

	/**
	 * Class used for builds/versions
	 *
	 * @package thebuggenie
	 * @subpackage main
	 */
	class TBGBuild extends TBGVersionItem 
	{
		/**
		 * This builds edition
		 *
		 * @var TBGEdition
		 */
		protected $_edition = null;

		/**
		 * This builds project
		 *
		 * @var TBGProject
		 */
		protected $_project = null;
		
		/**
		 * Whether this build is released or not
		 * 
		 * @var boolean
		 */
		protected $_isreleased = null;
		
		/**
		 * The builds release date
		 * 
		 * @var integer
		 */
		protected $_release_date = null;
		
		/**
		 * Project builds cache
		 * 
		 * @var array
		 */
		static protected $_project_builds = null;

		/**
		 * Edition builds cache
		 * 
		 * @var array
		 */
		static protected $_edition_builds = null;
		
		/**
		 * Get all builds for a specific project
		 * 
		 * @param integer $project_id The project ID
		 * 
		 * @return array
		 */
		public static function getByProjectID($project_id)
		{
			if (self::$_project_builds === null)
			{
				self::$_project_builds = array();
			}
			if (!array_key_exists($project_id, self::$_project_builds))
			{
				self::$_project_builds[$project_id] = array();
				if ($res = B2DB::getTable('B2tBuilds')->getByProjectID($project_id))
				{
					while ($row = $res->getNextRow())
					{
						$build = TBGFactory::buildLab($row->get(B2tBuilds::ID), $row);
						self::$_project_builds[$project_id][$build->getID()] = $build;
					}
				}
			}
			return self::$_project_builds[$project_id];
		}

		/**
		 * Get all builds for a specific edition
		 * 
		 * @param integer $edition_id The edition ID
		 * 
		 * @return array
		 */
		public static function getByEditionID($edition_id)
		{
			if (self::$_edition_builds === null)
			{
				self::$_edition_builds = array();
			}
			if (!array_key_exists($edition_id, self::$_edition_builds))
			{
				self::$_edition_builds[$edition_id] = array();
				if ($res = B2DB::getTable('B2tBuilds')->getByEditionID($project_id))
				{
					$build = TBGFactory::buildLab($row->get(B2tBuilds::ID), $row);
					self::$_edition_builds[$edition_id][$build->getID()] = $build;
				}
			}
			return self::$_edition_builds[$edition_id];
		}
		
		/**
		 * Create a new build <b>either</b> under a project <b>or</b> under a build and return it
		 * 
		 * @param string $name The name of the build
		 * @param integer $project The ID of the project the build is under
		 * @param integer $edition The ID of the edition the build is under
		 * @param integer $ver_mj Major version number
		 * @param integer $ver_mn Minor version number
		 * @param integer $ver_rev Version revision
		 * @param integer $b_id Optional build id
		 * 
		 * @return TBGBuild
		 */
		public static function createNew($name, $project = null, $edition = null, $ver_mj = 0, $ver_mn = 0, $ver_rev = 1, $b_id = null)
		{
			$b_id = B2DB::getTable('B2tBuilds')->createNew($name, $project, $edition, $ver_mj, $ver_mn, $ver_rev, $b_id);
			
			TBGContext::setPermission("b2buildaccess", $b_id, "core", 0, TBGContext::getUser()->getGroup()->getID(), 0, true);
			
			return TBGFactory::buildLab($b_id);
		}
		
		/**
		 * Constructor function
		 *
		 * @param integer $b_id
		 * @param B2DBRow $row
		 */
		public function __construct($b_id, $row = null)
		{
			if ($row === null)
			{
				$row = B2DB::getTable('B2tBuilds')->getById($b_id);
			}
			if ($row instanceof B2DBRow)
			{
				$this->_name 				= $row->get(B2tBuilds::NAME);
				$this->_itemid 				= $b_id;
				$this->_isdefault 			= (bool) $row->get(B2tBuilds::IS_DEFAULT);
				$this->_isreleased 			= (bool) $row->get(B2tBuilds::RELEASED);
				$this->_locked 				= (bool) $row->get(B2tBuilds::LOCKED);
				$this->_release_date 		= $row->get(B2tBuilds::RELEASE_DATE);
				$this->_version_major 		= $row->get(B2tBuilds::VERSION_MAJOR);
				$this->_version_minor 		= $row->get(B2tBuilds::VERSION_MINOR);
				$this->_version_revision 	= $row->get(B2tBuilds::VERSION_REVISION);
				if ($row->get(B2tBuilds::EDITION))
				{
					$this->_edition = TBGFactory::editionLab($row->get(B2tBuilds::EDITION), $row);
				}
				elseif ($row->get(B2tBuilds::PROJECT))
				{
					$this->_project = TBGFactory::projectLab($row->get(B2tBuilds::PROJECT), $row);
				}
			}
		}
		
		/**
		 * @deprecated
		 */
		public function __toString()
		{
			throw new Exception("Don't print the build object, use the getPrintableName instead");
		}
		
		/**
		 * Returns the name and the version, nicely formatted
		 * 
		 * @return string
		 */
		public function getPrintableName()
		{
			return $this->_name . ' (' . $this->getVersion() . ')';
		}
		
		/**
		 * Returns the edition
		 *
		 * @return TBGEdition
		 */
		public function getEdition()
		{
			return $this->_edition;
		}
		
		/**
		 * Returns the project
		 *
		 * @return TBGProject
		 */
		public function getProject()
		{
			return ($this->_project !== null) ? $this->_project : $this->_edition->getProject();
		}
		
		/**
		 * Whether this build is under an edition
		 * 
		 * @return bool
		 */
		public function isEditionBuild()
		{
			return !is_null($this->_edition);
		}

		/**
		 * Whether this build is under a project
		 * 
		 * @return bool
		 */
		public function isProjectBuild()
		{
			return !is_null($this->_project);
		}
		
		/**
		 * Returns the parent object
		 * 
		 * @return TBGVersionItem
		 */
		public function getParent()
		{
			return ($this->isProjectBuild()) ? $this->getProject() : $this->getEdition();
		}
		
		/**
		 * Set this build as locked or unlocked
		 * 
		 * @param boolean $val Whether it's locked or not
		 */
		public function setLocked($val)
		{
			$this->_locked = (bool) $val;
		}
		
		/**
		 * Set this build as released or unreleased
		 * 
		 * @param boolean $val Whether it's released or not
		 */
		public function setReleased($val)
		{
			$this->_isreleased = (bool) $val;
		}
		
		/**
		 * Set the release date
		 * @param integer $val The date (timestamp) for the release
		 */
		public function setReleaseDate($val = null)
		{
			if ($val === null) $val = $_SERVER["REQUEST_TIME"];
			$this->_release_date = $val;
		}

		/**
		 * Make the build the default for it's edition or project
		 */
		public function setDefault()
		{
			if ($this->isEditionBuild())
			{
				B2DB::getTable('B2tBuilds')->clearDefaultsByEditionID($this->getParent()->getID());
			}
			else
			{
				B2DB::getTable('B2tBuilds')->clearDefaultsByProjectID($this->getParent()->getID());
			}
			$res = B2DB::getTable('B2tBuilds')->setDefaultBuild($this->getID());
			$this->_isdefault = true;
		}
		
		/**
		 * Set the build name
		 *
		 * @param string $name
		 */
		public function setName($name)
		{
			$this->_name = $name;
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
		 * Delete this build
		 */
		public function delete()
		{
			B2DB::getTable('B2tIssueAffectsBuild')->deleteByBuildID($this->getID());
			B2DB::getTable('B2tBuilds')->doDeleteById($this->getID());
		}
		
		/**
		 * Save changes made to this build
		 */
		public function save()
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(B2tBuilds::VERSION_MAJOR, $this->_version_major);
			$crit->addUpdate(B2tBuilds::VERSION_MINOR, $this->_version_minor);
			$crit->addUpdate(B2tBuilds::VERSION_REVISION, $this->_version_revision);
			$crit->addUpdate(B2tBuilds::NAME, $this->_name);
			$crit->addUpdate(B2tBuilds::TIMESTAMP, $_SERVER["REQUEST_TIME"]);
			$crit->addUpdate(B2tBuilds::RELEASE_DATE, $this->_release_date);
			$crit->addUpdate(B2tBuilds::RELEASED, (int) $this->_isreleased);
			$crit->addUpdate(B2tBuilds::LOCKED, (int) $this->_locked);
			
			try
			{
				$res = B2DB::getTable('B2tBuilds')->doUpdateById($crit, $this->getID());
				return true;
			}
			catch (Exception $e)
			{
				return false;
			}
			
		}
		
		/**
		 * Adds this build to all open issues in this edition or project
		 * Returns true if any issues were updated, false if not
		 * 
		 * @param integer $limit_status Limit to only a specific status type
		 * @param integer $limit_category Limit to only a specific category
		 * @param integer $limit_issuetype Limit to only a specific issue type
		 * 
		 * @return boolean
		 */
		public function addToOpenParentIssues($limit_status = null, $limit_category = null, $limit_issuetype = null)
		{
			if ($this->isEditionBuild())
			{
				$res = B2DB::getTable('B2tIssueAffectsEdition')->getOpenAffectedIssuesByEditionID($this->getParent()->getID(), $limit_status, $limit_category, $limit_issuetype);
			}
			else
			{
				$res = B2DB::getTable('B2tIssues')->getOpenAffectedIssuesByProjectID($this->getParent()->getID(), $limit_status, $limit_category, $limit_issuetype);
			}
			
			$retval = false;
			if ($res)
			{
				while ($row = $res->getNextRow())
				{
					$issue_id = $row->get(B2tIssues::ID);
					if (B2DB::getTable('B2tIssueAffectsBuild')->setIssueAffected($issue_id, $this->getID()))
					{
						$retval = true;
					}
				}
			}
			return $retval;
		}
		
		/**
		 * Whether or not the current user can access the build
		 * 
		 * @return boolean
		 */
		public function hasAccess()
		{
			return TBGContext::getUser()->hasPermission('b2buildaccess', $this->getID(), 'core');
		}
		
	}
