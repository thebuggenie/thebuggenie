<?php

    namespace thebuggenie\core\entities;

    use thebuggenie\core\entities\common\QaLeadable;
    use thebuggenie\core\framework;

    /**
     * Edition class
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage main
     */

    /**
     * Edition class
     *
     * @package thebuggenie
     * @subpackage main
     *
     * @Table(name="\thebuggenie\core\entities\tables\Editions")
     */
    class Edition extends QaLeadable
    {

        /**
         * The name of the object
         *
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_name;

        /**
         * The project
         *
         * @var \thebuggenie\core\entities\Project
         * @Column(type="integer", length=10)
         * @Relates(class="\thebuggenie\core\entities\Project")
         */
        protected $_project;

        /**
         * Editions components
         *
         * @var array|\thebuggenie\core\entities\Component
         * @Relates(class="\thebuggenie\core\entities\Component", collection=true, manytomany=true, joinclass="\thebuggenie\core\entities\tables\EditionComponents")
         */
        protected $_components;

        /**
         * Edition builds
         *
         * @var array|\thebuggenie\core\entities\Build
         * @Relates(class="\thebuggenie\core\entities\Build", collection=true, foreign_column="edition")
         */
        protected $_builds;

        /**
         * @Column(type="string", length=200)
         */
        protected $_description;

        /**
         * The editions documentation URL
         *
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_doc_url;

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
                framework\Context::setPermission("canseeedition", $this->getID(), "core", 0, framework\Context::getUser()->getGroup()->getID(), 0, true);
                \thebuggenie\core\framework\Event::createNew('core', 'Edition::createNew', $this)->trigger();
            }
        }

        /**
         * Populates components inside the edition
         *
         * @return void
         */
        protected function _populateComponents()
        {
            if ($this->_components === null)
            {
                $this->_b2dbLazyload('_components');
            }
        }

        /**
         * Returns an array with all components
         *
         * @return array|\thebuggenie\core\entities\Component
         */
        public function getComponents()
        {
            $this->_populateComponents();
            return $this->_components;
        }

        /**
         * Whether or not this edition has a component enabled
         *
         * @param \thebuggenie\core\entities\Component|integer $component The component to check for
         *
         * @return boolean
         */
        public function hasComponent($component)
        {
            $component_id = ($component instanceof \thebuggenie\core\entities\Component) ? $component->getID() : $component;
            return array_key_exists($component_id, $this->getComponents());
        }

        /**
         * Whether this edition has a description set
         *
         * @return string
         */
        public function hasDescription()
        {
            return (bool) $this->getDescription();
        }

        /**
         * Adds an existing component to the edition
         *
         * @param \thebuggenie\core\entities\Component|integer $component
         */
        public function addComponent($component)
        {
            $component_id = ($component instanceof \thebuggenie\core\entities\Component) ? $component->getID() : $component;
            return tables\EditionComponents::getTable()->addEditionComponent($this->getID(), $component_id);
        }

        /**
         * Removes an existing component from the edition
         *
         * @param \thebuggenie\core\entities\Component|integer $component
         */
        public function removeComponent($component)
        {
            $component_id = ($component instanceof \thebuggenie\core\entities\Component) ? $component->getID() : $component;
            tables\EditionComponents::getTable()->removeEditionComponent($this->getID(), $component_id);
        }

        /**
         * Returns the description
         *
         * @return string
         */
        public function getDescription()
        {
            return $this->_description;
        }

        /**
         * Returns the documentation url
         *
         * @return string
         */
        public function getDocumentationURL()
        {
            return $this->_doc_url;
        }

        /**
         * Returns the component specified
         *
         * @param integer $c_id
         *
         * @return \thebuggenie\core\entities\Component
         */
        public function getComponent($c_id)
        {
            $this->_populateComponents();
            if (array_key_exists($c_id, $this->_components))
            {
                return $this->_components[$c_id];
            }

            return null;
        }

        /**
         * Populates builds inside the edition
         *
         * @return void
         */
        protected function _populateBuilds()
        {
            if ($this->_builds === null)
            {
                $this->_b2dbLazyload('_builds');
            }
        }

        /**
         * Returns an array with all builds
         *
         * @return array|\thebuggenie\core\entities\Build
         */
        public function getBuilds()
        {
            $this->_populateBuilds();
            return $this->_builds;
        }

        public function getReleasedBuilds()
        {
            $builds = $this->getBuilds();
            foreach ($builds as $id => $build)
            {
                if (!$build->isReleased()) unset($builds[$id]);
            }

            return $builds;
        }

        /**
         * Returns the parent project
         *
         * @return \thebuggenie\core\entities\Project
         */
        public function getProject()
        {
            return $this->_b2dbLazyload('_project');
        }

        public function setProject($project)
        {
            $this->_project = $project;
        }

        /**
         * Set the edition description
         *
         * @param string $description
         */
        public function setDescription($description)
        {
            $this->_description = $description;
        }

        /**
         * Set the editions documentation url
         *
         * @param string $doc_url
         */
        public function setDocumentationURL($doc_url)
        {
            $this->_doc_url = $doc_url;
        }

        protected function _preDelete()
        {
            tables\EditionComponents::getTable()->deleteByEditionID($this->getID());
        }

        /**
         * Whether or not the current user can access the edition
         *
         * @return boolean
         */
        public function hasAccess()
        {
            return ($this->getProject()->canSeeAllEditions() || framework\Context::getUser()->hasPermission('canseeedition', $this->getID()));
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

    }
