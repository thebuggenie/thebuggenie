<?php

	/**
	 * Scopes table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Scopes table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 */
	class B2tScopes extends B2DBTable 
	{
		
		const B2DBNAME = 'scopes';
		const ID = 'scopes.id';
		const NAME = 'scopes.name';
		const ENABLED = 'scopes.enabled';
		const SHORTNAME = 'scopes.shortname';
		const DESCRIPTION = 'scopes.description';
		const ADMIN = 'scopes.admin';
		const HOSTNAME = 'scopes.hostname';
		
		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			
			parent::_addText(self::NAME, true);
			parent::_addBoolean(self::ENABLED, false);
			parent::_addVarchar(self::SHORTNAME, 45, '');
			parent::_addText(self::DESCRIPTION, false);
			parent::_addInteger(self::ADMIN, 10);
			parent::_addVarchar(self::HOSTNAME, 150, '');
		}

		public function getByHostname($hostname)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::HOSTNAME, $hostname);
			$row = $this->doSelectOne($crit);
			return $row;
		}
		
	}
