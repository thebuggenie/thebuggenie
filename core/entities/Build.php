<?php

    namespace thebuggenie\core\entities;

    use thebuggenie\core\entities\common\Releaseable;
    use thebuggenie\core\framework;

    /**
     * Class used for builds/versions
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage main
     */

    /**
     * Class used for builds/versions
     *
     * @package thebuggenie
     * @subpackage main
     *
     * @Table(name="\thebuggenie\core\entities\tables\Builds")
     */
    class Build extends Releaseable
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
         * @var \thebuggenie\core\entities\Edition
         * @Column(type="integer", length=10)
         * @Relates(class="\thebuggenie\core\entities\Edition")
         */
        protected $_edition = null;

        /**
         * This builds project
         *
         * @var \thebuggenie\core\entities\Project
         * @Column(type="integer", length=10)
         * @Relates(class="\thebuggenie\core\entities\Project")
         */
        protected $_project = null;

        /**
         * This builds milestone, if any
         *
         * @var \thebuggenie\core\entities\Milestone
         * @Column(type="integer", length=10)
         * @Relates(class="\thebuggenie\core\entities\Milestone")
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
         * @var \thebuggenie\core\entities\File
         * @Column(type="integer", length=10)
         * @Relates(class="\thebuggenie\core\entities\File")
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

        /**
         * Number of closed issues affected by this release
         *
         * @var integer
         */
        protected $_num_issues_closed = null;

        /**
         * Number of issues affected by this release
         *
         * @var integer
         */
        protected $_num_issues = null;

        protected function _postSave($is_new)
        {
            if ($is_new)
            {
                framework\Context::setPermission("canseebuild", $this->getID(), "core", 0, framework\Context::getUser()->getGroup()->getID(), 0, true);
                \thebuggenie\core\framework\Event::createNew('core', 'Build::_postSave', $this)->trigger();
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
         * @return \thebuggenie\core\entities\Edition
         */
        public function getEdition()
        {
            return $this->_b2dbLazyload('_edition');
        }

        public function getEditionID()
        {
            return ($this->_edition instanceof \thebuggenie\core\entities\Edition) ? $this->_edition->getID() : (int) $this->_edition;
        }

        public function setEdition(\thebuggenie\core\entities\Edition $edition)
        {
            $this->_edition = $edition;
        }

        /**
         * Returns the project
         *
         * @return \thebuggenie\core\entities\Project
         */
        public function getProject()
        {
            $this->_b2dbLazyload('_project');
            return $this->_project;
        }

        public function setProject(\thebuggenie\core\entities\Project $project)
        {
            $this->_project = $project;
        }

        /**
         * Returns the milestone
         *
         * @return \thebuggenie\core\entities\Milestone
         */
        public function getMilestone()
        {
            return $this->_b2dbLazyload('_milestone');
        }

        public function setMilestone(\thebuggenie\core\entities\Milestone $milestone)
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
            return (bool) $this->_edition;
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
         * @return \thebuggenie\core\entities\ReleaseableItem
         */
        public function getParent()
        {
            return ($this->isProjectBuild()) ? $this->getProject() : $this->getEdition();
        }

        /**
         * Delete this build
         */
        protected function _preDelete()
        {
            tables\IssueAffectsBuild::getTable()->deleteByBuildID($this->getID());
        }

        /**
         * Whether or not the current user can access the build
         *
         * @return boolean
         */
        public function hasAccess()
        {
            return (($this->getProject() instanceof \thebuggenie\core\entities\Project && $this->getProject()->canSeeAllBuilds()) || framework\Context::getUser()->hasPermission('canseebuild', $this->getID()));
        }

        /**
         * Return the file associated with this build, if any
         *
         * @return \thebuggenie\core\entities\File
         */
        public function getFile()
        {
            return $this->_b2dbLazyload('_file_id');
        }

        /**
         * Set the file associated with this build
         *
         * @param \thebuggenie\core\entities\File $file
         */
        public function setFile(\thebuggenie\core\entities\File $file)
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
            return (bool) ($this->getFile() instanceof \thebuggenie\core\entities\File);
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
            return (bool) ($this->getFile() instanceof \thebuggenie\core\entities\File || $this->_file_url != '');
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
            $versions = array($this->_version_major, $this->_version_minor);

            if ($this->_version_revision != 0) $versions[] = $this->_version_revision;

            return join('.', $versions);
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
            $this->_version_major = ($ver_mj) ? $ver_mj : 0;
            $this->_version_minor = ($ver_mn) ? $ver_mn : 0;
            $this->_version_revision = ($ver_rev) ? $ver_rev : 0;
        }

        /**
         * Set the major version number
         *
         * @param $ver_mj
         */
        public function setVersionMajor($ver_mj)
        {
            $this->_version_major = ($ver_mj) ? $ver_mj : 0;
        }

        /**
         * Set the minor version number
         *
         * @param $ver_mn
         */
        public function setVersionMinor($ver_mn)
        {
            $this->_version_minor = ($ver_mn) ? $ver_mn : 0;
        }

        /**
         * Set the version revision number
         *
         * @param $ver_rev
         */
        public function setVersionRevision($ver_rev)
        {
            $this->_version_revision = ($ver_rev) ? $ver_rev : 0;
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
         * @param boolean $locked [optional]
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

        public static function listen_thebuggenie_core_entities_File_hasAccess(\thebuggenie\core\framework\Event $event)
        {
            $file = $event->getSubject();
            $builds = self::getB2DBTable()->getByFileID($file->getID());
            foreach ($builds as $build)
            {
                if ($build->hasAccess())
                {
                    $event->setReturnValue(true);
                    $event->setProcessed();
                    break;
                }
            }
        }

        protected function _populateIssueCounts()
        {
            if ($this->_num_issues === null)
            {
                list($this->_num_issues, $this->_num_issues_closed) = tables\IssueAffectsBuild::getTable()->getCountsForBuild($this->getID());
            }
        }

        public function getNumberOfAffectedIssues()
        {
            $this->_populateIssueCounts();
            return $this->_num_issues;
        }

        public function getNumberOfClosedIssues()
        {
            $this->_populateIssueCounts();
            return $this->_num_issues_closed;
        }

        public function getPercentComplete()
        {
            if ($this->getNumberOfAffectedIssues() == 0)
            {
                $pct = 0;
            }
            else
            {
                $multiplier = 100 / $this->getNumberOfAffectedIssues();
                $pct = $this->getNumberOfClosedIssues() * $multiplier;
            }

            return $pct;
        }

    }
