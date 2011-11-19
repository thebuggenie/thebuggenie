<?php

	use b2db\Core,
		b2db\Criteria,
		b2db\Criterion;

	/**
	 * Issuetype schemes table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Issuetype schemes table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 *
	 * @Table(name="issuetype_schemes")
	 * @Entity(class="TBGIssuetypeScheme")
	 */
	class TBGIssuetypeSchemesTable extends TBGB2DBTable
	{

		const B2DB_TABLE_VERSION = 1;
		const B2DBNAME = 'issuetype_schemes';
		const ID = 'issuetype_schemes.id';
		const SCOPE = 'issuetype_schemes.scope';
		const NAME = 'issuetype_schemes.name';
		const DESCRIPTION = 'issuetype_schemes.description';

//		public function __construct()
//		{
//			parent::__construct(self::B2DBNAME, self::ID);
//			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
//			parent::_addVarchar(self::NAME, 200);
//			parent::_addText(self::DESCRIPTION, false);
//		}

		public function getAll()
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$crit->addOrderBy(self::ID, Criteria::SORT_ASC);

			return $this->select($crit);
		}

	}