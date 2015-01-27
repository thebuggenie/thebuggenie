<?php

    namespace thebuggenie\core\entities;

    use thebuggenie\core\entities\common\IdentifiableScoped;
    use thebuggenie\core\framework;

    /**
     * Issuetype scheme class
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.0
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage core
     */

    /**
     * Issuetype scheme class
     *
     * @package thebuggenie
     * @subpackage core
     *
     * @Table(name="\thebuggenie\core\entities\tables\IssuetypeSchemes")
     */
    class IssuetypeScheme extends IdentifiableScoped
    {

        protected static $_schemes = null;

        /**
         * @Column(type="string", length=200)
         */
        protected $_name;

        protected $_visiblefields = array();

        /**
         * Issue type details
         * @var array
         */
        protected $_issuetypedetails = null;

        /**
         * Number of projects using this issue type scheme
         *
         * @var integer
         */
        protected $_number_of_projects = null;
        
        /**
         * The issuetype description
         *
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_description = null;

        protected static function _populateSchemes()
        {
            if (self::$_schemes === null)
            {
                self::$_schemes = tables\IssuetypeSchemes::getTable()->getAll();
            }
        }

        protected function _postSave($is_new)
        {
            framework\Context::getCache()->delete(framework\Cache::KEY_TEXTPARSER_ISSUE_REGEX);
        }
        
        /**
         * Return all issuetypes in the system
         *
         * @return array An array of Issuetype objects
         */
        public static function getAll()
        {
            self::_populateSchemes();
            return self::$_schemes;
        }
        
        public static function loadFixtures(\thebuggenie\core\entities\Scope $scope)
        {
            $scheme = new IssuetypeScheme();
            $scheme->setScope($scope->getID());
            $scheme->setName("Default issuetype scheme");
            $scheme->setDescription("This is the default issuetype scheme. It is used by all projects with no specific issuetype scheme selected. This scheme cannot be edited or removed.");
            $scheme->save();

            framework\Settings::saveSetting(framework\Settings::SETTING_DEFAULT_ISSUETYPESCHEME, $scheme->getID(), 'core', $scope->getID());
            
            foreach (Issuetype::getAll() as $issuetype)
            {
                $scheme->setIssuetypeEnabled($issuetype);
                if ($issuetype->getIcon() == 'developer_report')
                {
                    $scheme->setIssuetypeRedirectedAfterReporting($issuetype, false);
                }
                if (in_array($issuetype->getIcon(), array('task', 'developer_report', 'idea')))
                {
                    $scheme->setIssuetypeReportable($issuetype, false);
                }
            }
            
            return $scheme;
        }
        
        /**
         * Returns the issuetypes description
         *
         * @return string
         */
        public function getDescription()
        {
            return $this->_description;
        }
        
        /**
         * Set the issuetypes description
         *
         * @param string $description
         */
        public function setDescription($description)
        {
            $this->_description = $description;
        }

        /**
         * Whether this is the builtin issuetype that cannot be
         * edited or removed
         *
         * @return boolean
         */
        public function isCore()
        {
            return ($this->getID() == framework\Settings::getCoreIssuetypeScheme()->getID());
        }

        protected function _populateAssociatedIssuetypes()
        {
            if ($this->_issuetypedetails === null)
            {
                $this->_issuetypedetails = tables\IssuetypeSchemeLink::getTable()->getByIssuetypeSchemeID($this->getID());
            }
        }
        
        public function setIssuetypeEnabled(Issuetype $issuetype, $enabled = true)
        {
            if ($enabled)
            {
                if (!$this->isSchemeAssociatedWithIssuetype($issuetype))
                {
                    tables\IssuetypeSchemeLink::getTable()->associateIssuetypeWithScheme($issuetype->getID(), $this->getID());
                }
            }
            else
            {
                tables\IssuetypeSchemeLink::getTable()->unAssociateIssuetypeWithScheme($issuetype->getID(), $this->getID());
            }
            $this->_issuetypedetails = null;
        }
        
        public function setIssuetypeDisabled(Issuetype $issuetype)
        {
            $this->setIssuetypeEnabled($issuetype, false);
        }

        public function isSchemeAssociatedWithIssuetype(Issuetype $issuetype)
        {
            $this->_populateAssociatedIssuetypes();
            return array_key_exists($issuetype->getID(), $this->_issuetypedetails);
        }
        
        public function isIssuetypeReportable(Issuetype $issuetype)
        {
            $this->_populateAssociatedIssuetypes();
            if (!$this->isSchemeAssociatedWithIssuetype($issuetype)) return false;
            return (bool) $this->_issuetypedetails[$issuetype->getID()]['reportable'];
        }

        public function isIssuetypeRedirectedAfterReporting(Issuetype $issuetype)
        {
            $this->_populateAssociatedIssuetypes();
            if (!$this->isSchemeAssociatedWithIssuetype($issuetype)) return false;
            return (bool) $this->_issuetypedetails[$issuetype->getID()]['redirect'];
        }
        
        public function setIssuetypeRedirectedAfterReporting(Issuetype $issuetype, $val = true)
        {
            tables\IssuetypeSchemeLink::getTable()->setIssuetypeRedirectedAfterReportingForScheme($issuetype->getID(), $this->getID(), $val);
            if (array_key_exists($issuetype->getID(), $this->_visiblefields))
            {
                $this->_visiblefields[$issuetype->getID()]['redirect'] = $val;
            }
        }

        public function setIssuetypeReportable(Issuetype $issuetype, $val = true)
        {
            tables\IssuetypeSchemeLink::getTable()->setIssuetypeReportableForScheme($issuetype->getID(), $this->getID(), $val);
            if (array_key_exists($issuetype->getID(), $this->_visiblefields))
            {
                $this->_visiblefields[$issuetype->getID()]['reportable'] = $val;
            }
        }

        /**
         * Get all steps in this issuetype
         *
         * @return array An array of Issuetype objects
         */
        public function getIssuetypes()
        {
            $this->_populateAssociatedIssuetypes();
            $retarr = array();
            foreach ($this->_issuetypedetails as $key => $details)
            {
                $retarr[$key] = $details['issuetype'];
            }
            return $retarr;
        }

        public function getReportableIssuetypes()
        {
            $issuetypes = $this->getIssuetypes();
            foreach ($issuetypes as $key => $issuetype)
            {
                if ($this->isIssuetypeReportable($issuetype)) continue;
                unset($issuetypes[$key]);
            }
            return $issuetypes;
        }
        
        protected function _preDelete()
        {
            tables\IssueFields::getTable()->deleteByIssuetypeSchemeID($this->getID());
            tables\IssuetypeSchemeLink::getTable()->deleteByIssuetypeSchemeID($this->getID());
            tables\Projects::getTable()->updateByIssuetypeSchemeID($this->getID());
        }

        protected function _populateVisibleFieldsForIssuetype(Issuetype $issuetype)
        {
            if (!array_key_exists($issuetype->getID(), $this->_visiblefields))
            {
                $this->_visiblefields[$issuetype->getID()] = tables\IssueFields::getTable()->getSchemeVisibleFieldsArrayByIssuetypeID($this->getID(), $issuetype->getID());
            }
        }

        public function getVisibleFields()
        {
            $fields = array();
            $types = $this->getIssuetypes();
            foreach ($types as $type) {
                $this->_populateVisibleFieldsForIssuetype($type);
                $fields = array_merge($fields, $this->_visiblefields[$type->getID()]);
            }
            ksort($fields);
            return $fields;
        }

        public function getVisibleFieldsForIssuetype(Issuetype $issuetype)
        {
            $this->_populateVisibleFieldsForIssuetype($issuetype);
            return $this->_visiblefields[$issuetype->getID()];
        }
        
        public function clearAvailableFieldsForIssuetype(Issuetype $issuetype)
        {
            tables\IssueFields::getTable()->deleteBySchemeIDandIssuetypeID($this->getID(), $issuetype->getID());
        }

        public function setFieldAvailableForIssuetype(Issuetype $issuetype, $key, $details = array())
        {
            tables\IssueFields::getTable()->addFieldAndDetailsBySchemeIDandIssuetypeID($this->getID(), $issuetype->getID(), $key, $details);
        }
        
        public function isInUse()
        {
            if ($this->_number_of_projects === null)
            {
                $this->_number_of_projects = tables\Projects::getTable()->countByIssuetypeSchemeID($this->getID());
            }
            return (bool) $this->_number_of_projects;
        }
        
        public function getNumberOfProjects()
        {
            return $this->_number_of_projects;
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
