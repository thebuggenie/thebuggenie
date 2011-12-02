<?php

	/**
	 * Class used for builds/versions
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage main
	 */

	/**
	 * Class used for builds/versions
	 *
	 * @package thebuggenie
	 * @subpackage main
	 *
	 * @Table(name="TBGBuildsTable")
	 */
	class TBGBuild extends TBGReleaseableItem 
	{
		
		/**
		 * The name of the object
		 *
		 * @var string
		 * @Column(type="string", length=200)
		 */
		protected $_name;

		/**
		 * This builds edition
		 *
		 * @var TBGEdition
		 * @Column(type="integer", length=10)
		 * @Relates(class="TBGEdition")
		 */
		protected $_edition = null;

		/**
		 * This builds project
		 *
		 * @var TBGProject
		 * @Column(type="integer", length=10)
		 * @Relates(class="TBGProject")
		 */
		protected $_project = null;
		
		/**
		 * This builds milestone, if any
		 *
		 * @var TBGMilestone
		 * @Column(type="integer", length=10)
		 * @Relates(class="TBGMilestone")
		 */
		protected $_milestone = null;
		
		/**
		 * Whether this build is active or not
		 * 
		 * @var boolean
		 * @Column(type="boolean", name="locked")
		 */
		protected $_isactive = null;
		
		/**
		 * An attached file, if exists
		 * 
		 * @var TBGFile
		 * @Column(type="integer", length=10)
		 * @Relates(class="TBGFile")
		 */
		protected $_file_id = null;
		
		/**
		 * An url to download this releases file, if any
		 * 
		 * @var string
		 * @Column(type="string", length=255)
		 */
		protected $_file_url = null;

		/**
		 * Major version
		 *
		 * @var integer
		 * @access protected
		 * @Column(type="integer", length=5)
		 */
		protected $_version_major = 0;

		/**
		 * Minor version
		 *
		 * @var integer
		 * @access protected
		 * @Column(type="integer", length=5)
		 */
		protected $_version_minor = 0;

		/**
		 * Revision
		 *
		 * @var integer
		 * @access protected
		 * @Column(type="string", length=30)
		 */
		protected $_version_revision = 0;

		/**
		 * Whether the item is locked or not
		 *
		 * @var boolean
		 * @access protected
		 * @Column(type="boolean")
		 */
		protected $_locked;

		protected function _postSave($is_new)
		{
			if ($is_new)
			{
				TBGContext::setPermission("canseebuild", $this->getID(), "core", 0, TBGContext::getUser()->getGroup()->getID(), 0, true);
				TBGEvent::createNew('core', 'TBGBuild::_postSave', $this)->trigger();
			}
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
		
		public function getEditionID()
		{
			return ($this->getEdition() instanceof TBGEdition) ? $this->getEdition()->getID() : 0;
		}

		public function setEdition(TBGEdition $edition)
		{
			$this->_edition = $edition;
		}
		
		/**
		 * Returns the project
		 *
		 * @return TBGProject
		 */
		public function getProject()
		{
			$this->_b2dbLazyload('_project');
			return $this->_project;
		}

		public function setProject(TBGProject $project)
		{
			$this->_project = $project;
		}
		
		/**
		 * Returns the milestone
		 *
		 * @return TBGMilestone
		 */
		public function getMilestone()
		{
			return $this->_b2dbLazyload('_milestone');
		}

		public function setMilestone(TBGMilestone $milestone)
		{
			$this->_milestone = $milestone;
		}
		
		public function clearMilestone()
		{
			$this->_milestone = null;
		}
		
		public function clearEdition()
		{
			$this->_edition = null;
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
		 * @return TBGReleaseableItem
		 */
		public function getParent()
		{
			return ($this->isProjectBuild()) ? $this->getProject() : $this->getEdition();
		}
		
		/**
		 * Make the build the default for it's edition or project
		 */
		public function setDefault()
		{
			if ($this->isEditionBuild())
			{
				\b2db\Core::getTable('TBGBuildsTable')->clearDefaultsByEditionID($this->getParent()->getID());
			}
			else
			{
				\b2db\Core::getTable('TBGBuildsTable')->clearDefaultsByProjectID($this->getParent()->getID());
			}
			$res = \b2db\Core::getTable('TBGBuildsTable')->setDefaultBuild($this->getID());
			$this->_isdefault = true;
		}
		
		/**
		 * Delete this build
		 */
		protected function _preDelete()
		{
			\b2db\Core::getTable('TBGIssueAffectsBuildTable')->deleteByBuildID($this->getID());
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
				$res = \b2db\Core::getTable('TBGIssueAffectsEditionTable')->getOpenAffectedIssuesByEditionID($this->getParent()->getID(), $limit_status, $limit_category, $limit_issuetype);
			}
			else
			{
				$res = TBGIssuesTable::getTable()->getOpenAffectedIssuesByProjectID($this->getParent()->getID(), $limit_status, $limit_category, $limit_issuetype);
			}
			
			$retval = false;
			if ($res)
			{
				while ($row = $res->getNextRow())
				{
					$issue_id = $row->get(TBGIssuesTable::ID);
					if (\b2db\Core::getTable('TBGIssueAffectsBuildTable')->setIssueAffected($issue_id, $this->getID()))
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
			return (($this->getProject() instanceof TBGProject && $this->getProject()->canSeeAllBuilds()) || TBGContext::getUser()->hasPermission('canseebuild', $this->getID()));
		}

		/**
		 * Return the file associated with this build, if any
		 * 
		 * @return TBGFile
		 */
		public function getFile()
		{
			return $this->_b2dbLazyload('_file_id');
		}
		
		/**
		 * Set the file associated with this build
		 * 
		 * @param TBGFile $file 
		 */
		public function setFile(TBGFile $file)
		{
			$this->_file_id = $file;
		}
		
		public function clearFile()
		{
			$this->_file_id = null;
		}
		
		/**
		 * Return whether this build has a file associated to it
		 * 
		 * @return boolean
		 */
		public function hasFile()
		{
			return (bool) ($this->getFile() instanceof TBGFile);
		}
		
		/**
		 * Return the file download url for this build
		 * 
		 * @return string
		 */
		public function getFileURL()
		{
			return $this->_file_url;
		}
		
		/**
		 * Set the file download url for this build
		 * 
		 * @param string $file_url 
		 */
		public function setFileURL($file_url)
		{
			$this->_file_url = $file_url;
		}
		
		/**
		 * Return whether this build has a file url
		 * 
		 * @return boolean
		 */
		public function hasFileURL()
		{
			return (bool) ($this->_file_url != '');
		}
		
		/**
		 * Whether this build has any download associated with it
		 * 
		 * @return boolean
		 */
		public function hasDownload()
		{
			return (bool) ($this->getFile() instanceof TBGFile || $this->_file_url != '');
		}
		
		public function isArchived()
		{
			return $this->isLocked();
		}
		
		public function isActive()
		{
			return !$this->isLocked();
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
		 * Returns whether or not this item is locked
		 *
		 * @return boolean
		 * @access public
		 */
		public function isLocked()
		{
			return $this->_locked;
		}

		/**
		 * Specify whether or not this item is locked
		 *
		 * @param boolean $locked[optional]
		 */
		public function setLocked($locked = true)
		{
			$this->_locked = (bool) $locked;
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

	}
