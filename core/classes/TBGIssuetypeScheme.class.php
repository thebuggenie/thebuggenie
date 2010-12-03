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
	 */
	class TBGIssuetypeScheme extends TBGIdentifiableClass
	{

		static protected $_b2dbtablename = 'TBGIssuetypeSchemesTable';
		
		protected static $_schemes = null;

		protected $_issuetypes = null;

		/**
		 * The issuetype description
		 *
		 * @var string
		 */
		protected $_description = null;

		/**
		 * Return all issuetypes in the system
		 *
		 * @return array An array of TBGIssuetype objects
		 */
		public static function getAll()
		{
			if (self::$_schemes === null)
			{
				self::$_schemes = array();
				if ($res = TBGIssuetypeSchemesTable::getTable()->getAll())
				{
					while ($row = $res->getNextRow())
					{
						self::$_schemes[$row->get(TBGIssuetypeSchemesTable::ID)] = TBGContext::factory()->TBGIssuetypeScheme($row->get(TBGIssuetypeSchemesTable::ID), $row);
					}
				}
			}
			return self::$_schemes;
		}

		public static function loadFixtures(TBGScope $scope)
		{
			$scheme = new TBGIssuetypeScheme();
			$scheme->setScope($scope->getID());
			$scheme->setName("Default issuetype scheme");
			$scheme->setDescription("This is the default issuetype scheme. It is used by all projects with no specific issuetype scheme selected. This scheme cannot be edited or removed.");
			$scheme->save();
			
			foreach (TBGIssuetype::getAll() as $issuetype)
			{
				$scheme->setIssuetypeEnabled($issuetype);
			}
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
			return ($this->getID() == 1);
		}

		protected function _populateAssociatedIssuetypes()
		{
			if ($this->_issuetypes === null)
			{
				$this->_issuetypes = TBGIssuetypeSchemeLinkTable::getTable()->getByIssuetypeSchemeID($this->getID());
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
			if ($this->_issuetypes !== null)
			{
				$this->_issuetypes[$issuetype->getID()] = $issuetype;
			}
		}
		
		public function setIssuetypeDisabled(TBGIssuetype $issuetype)
		{
			$this->setIssuetypeEnabled($issuetype, false);
		}

		public function isSchemeAssociatedWithIssuetype(TBGIssuetype $issuetype)
		{
			$this->_populateAssociatedIssuetypes();
			return array_key_exists($issuetype->getID(), $this->_issuetypes);
		}

		/**
		 * Get all steps in this issuetype
		 *
		 * @return array An array of TBGIssuetypeStep objects
		 */
		public function getIssuetypes(TBGIssuetype $issuetype)
		{
			$this->_populateAssociatedIssuetypes();
			return $this->_issuetypes;
		}

	}
