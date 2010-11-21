<?php

	/**
	 * Issue types table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 ** @version 3.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Issue types table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 */
	class TBGIssueTypesTable extends TBGB2DBTable 
	{

		const B2DBNAME = 'issuetypes';
		const ID = 'issuetypes.id';
		const SCOPE = 'issuetypes.scope';
		const NAME = 'issuetypes.name';
		const DESCRIPTION = 'issuetypes.description';
		const APPLIES_TO = 'issuetypes.applies_to';
		const ICON = 'issuetypes.itemdata';
		const TASK = 'issuetypes.task';
		const REPORTABLE = 'issuetypes.reportable';
		const REDIRECT_AFTER_REPORTING = 'issuetypes.redirect_after_reporting';

		/**
		 * Return an instance of this table
		 *
		 * @return TBGIssueTypesTable
		 */
		public static function getTable()
		{
			return B2DB::getTable('TBGIssueTypesTable');
		}
		
		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addVarchar(self::NAME, 50);
			parent::_addInteger(self::APPLIES_TO, 10);
			parent::_addVarchar(self::ICON, 30, 'bug_report');
			parent::_addText(self::DESCRIPTION, false);
			parent::_addBoolean(self::TASK);
			parent::_addBoolean(self::REPORTABLE, true);
			parent::_addBoolean(self::REDIRECT_AFTER_REPORTING, true);
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
		}

		public function createNew($name, $icon = 'bug_report')
		{
			$crit = $this->getCriteria();
			$crit->addInsert(self::NAME, $name);
			$crit->addInsert(self::SCOPE, TBGContext::getScope()->getID());
			$crit->addInsert(self::ICON, $icon);
			$crit->addInsert(self::DESCRIPTION, $name);
			$res = $this->doInsert($crit);

			return $res;
		}

		public function getBugReportTypeIDs()
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::ICON, 'bug_report');
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$res = $this->doSelect($crit);

			$retarr = array();
			if ($res)
			{
				while ($row = $res->getNextRow())
				{
					$retarr[] = $row->get(self::ID);
				}
			}

			return $retarr;
		}

	}
