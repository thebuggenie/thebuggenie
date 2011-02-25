<?php

	/**
	 * Scopes table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 ** @version 3.0
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
	class TBGScopesTable extends TBGB2DBTable 
	{
		
		const B2DBNAME = 'scopes';
		const ID = 'scopes.id';
		const ENABLED = 'scopes.enabled';
		const DESCRIPTION = 'scopes.description';
		const NAME = 'scopes.name';
		const ADMINISTRATOR = 'scopes.administrator';
		
		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addBoolean(self::ENABLED, false);
			parent::_addText(self::DESCRIPTION, false);
			parent::_addText(self::NAME, false);
			parent::_addInteger(self::ADMINISTRATOR, 10);
		}

		public function getByHostname($hostname)
		{
			$crit = $this->getCriteria();
			$crit->addJoin(TBGScopeHostnamesTable::getTable(), TBGScopeHostnamesTable::SCOPE_ID, self::ID);
			$crit->addWhere(TBGScopeHostnamesTable::HOSTNAME, $hostname);
			$row = $this->doSelectOne($crit);
			return $row;
		}

		public function getDefault()
		{
			return $this->doSelectById(1);
		}

	}
