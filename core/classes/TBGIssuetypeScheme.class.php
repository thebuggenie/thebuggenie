<?php

	/**
	 * Issuetype scheme class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage core
	 */

	/**
	 * Issuetype scheme class
	 *
	 * @package thebuggenie
	 * @subpackage core
	 *
	 * @Table(name="TBGIssuetypeSchemesTable")
	 */
	class TBGIssuetypeScheme extends TBGIdentifiableScopedClass
	{

		protected static $_schemes = null;

		/**
		 * @Column(type="string", length=200)
		 */
		protected $_name;

		protected $_visiblefields = array();
		
		protected $_issuetypedetails = null;
		
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
				self::$_schemes = TBGIssuetypeSchemesTable::getTable()->getAll();
			}
		}

		protected function _postSave($is_new)
		{
			TBGCache::delete(TBGCache::KEY_TEXTPARSER_ISSUE_REGEX);
		}
		
		/**
		 * Return all issuetypes in the system
		 *
		 * @return array An array of TBGIssuetype objects
		 */
		public static function getAll()
		{
			self::_populateSchemes();
			return self::$_schemes;
		}
		
		public static function loadFixtures(TBGScope $scope)
		{
			$scheme = new TBGIssuetypeScheme();
			$scheme->setScope($scope->getID());
			$scheme->setName("Default issuetype scheme");
			$scheme->setDescription("This is the default issuetype scheme. It is used by all projects with no specific issuetype scheme selected. This scheme cannot be edited or removed.");
			$scheme->save();

			TBGSettings::saveSetting(TBGSettings::SETTING_DEFAULT_ISSUETYPESCHEME, $scheme->getID());
			
			foreach (TBGIssuetype::getAll() as $issuetype)
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
			return ($this->getID() == TBGSettings::getCoreIssuetypeScheme()->getID());
		}

		protected function _populateAssociatedIssuetypes()
		{
			if ($this->_issuetypedetails === null)
			{
				$this->_issuetypedetails = TBGIssuetypeSchemeLinkTable::getTable()->getByIssuetypeSchemeID($this->getID());
			}
		}
		
		public function setIssuetypeEnabled(TBGIssuetype $issuetype, $enabled = true)
		{
			if ($enabled)
			{
				if (!$this->isSchemeAssociatedWithIssuetype($issuetype))
				{
					TBGIssuetypeSchemeLinkTable::getTable()->associateIssuetypeWithScheme($issuetype->getID(), $this->getID());
				}
			}
			else
			{
				TBGIssuetypeSchemeLinkTable::getTable()->unAssociateIssuetypeWithScheme($issuetype->getID(), $this->getID());
			}
			$this->_issuetypedetails = null;
		}
		
		public function setIssuetypeDisabled(TBGIssuetype $issuetype)
		{
			$this->setIssuetypeEnabled($issuetype, false);
		}

		public function isSchemeAssociatedWithIssuetype(TBGIssuetype $issuetype)
		{
			$this->_populateAssociatedIssuetypes();
			return array_key_exists($issuetype->getID(), $this->_issuetypedetails);
		}
		
		public function isIssuetypeReportable(TBGIssuetype $issuetype)
		{
			$this->_populateAssociatedIssuetypes();
			if (!$this->isSchemeAssociatedWithIssuetype($issuetype)) return false;
			return (bool) $this->_issuetypedetails[$issuetype->getID()]['reportable'];
		}

		public function isIssuetypeRedirectedAfterReporting(TBGIssuetype $issuetype)
		{
			$this->_populateAssociatedIssuetypes();
			if (!$this->isSchemeAssociatedWithIssuetype($issuetype)) return false;
			return (bool) $this->_issuetypedetails[$issuetype->getID()]['redirect'];
		}
		
		public function setIssuetypeRedirectedAfterReporting(TBGIssuetype $issuetype, $val = true)
		{
			TBGIssuetypeSchemeLinkTable::getTable()->setIssuetypeRedirectedAfterReportingForScheme($issuetype->getID(), $this->getID(), $val);
			if (array_key_exists($issuetype->getID(), $this->_visiblefields))
			{
				$this->_visiblefields[$issuetype->getID()]['redirect'] = $val;
			}
		}

		public function setIssuetypeReportable(TBGIssuetype $issuetype, $val = true)
		{
			TBGIssuetypeSchemeLinkTable::getTable()->setIssuetypeReportableForScheme($issuetype->getID(), $this->getID(), $val);
			if (array_key_exists($issuetype->getID(), $this->_visiblefields))
			{
				$this->_visiblefields[$issuetype->getID()]['reportable'] = $val;
			}
		}

		/**
		 * Get all steps in this issuetype
		 *
		 * @return array An array of TBGIssuetype objects
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
			TBGIssueFieldsTable::getTable()->deleteByIssuetypeSchemeID($this->getID());
			TBGIssuetypeSchemeLinkTable::getTable()->deleteByIssuetypeSchemeID($this->getID());
			TBGProjectsTable::getTable()->updateByIssuetypeSchemeID($this->getID());
		}

		protected function _populateVisibleFieldsForIssuetype(TBGIssuetype $issuetype)
		{
			if (!array_key_exists($issuetype->getID(), $this->_visiblefields))
			{
				$this->_visiblefields[$issuetype->getID()] = TBGIssueFieldsTable::getTable()->getSchemeVisibleFieldsArrayByIssuetypeID($this->getID(), $issuetype->getID());
			}
		}

		public function getVisibleFieldsForIssuetype(TBGIssuetype $issuetype)
		{
			$this->_populateVisibleFieldsForIssuetype($issuetype);
			return $this->_visiblefields[$issuetype->getID()];
		}
		
		public function clearAvailableFieldsForIssuetype(TBGIssuetype $issuetype)
		{
			TBGIssueFieldsTable::getTable()->deleteBySchemeIDandIssuetypeID($this->getID(), $issuetype->getID());
		}

		public function setFieldAvailableForIssuetype(TBGIssuetype $issuetype, $key, $details = array())
		{
			TBGIssueFieldsTable::getTable()->addFieldAndDetailsBySchemeIDandIssuetypeID($this->getID(), $issuetype->getID(), $key, $details);
		}
		
		public function isInUse()
		{
			if ($this->_number_of_projects === null)
			{
				$this->_number_of_projects = TBGProjectsTable::getTable()->countByIssuetypeSchemeID($this->getID());
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
