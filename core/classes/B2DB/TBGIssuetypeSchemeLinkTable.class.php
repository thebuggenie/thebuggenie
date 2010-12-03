<?php

	/**
	 * Link table between workflow and issue type
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 ** @version 3.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Link table between workflow and issue type
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 */
	class TBGIssuetypeSchemeLinkTable extends TBGB2DBTable
	{

		const B2DBNAME = 'issuetype_scheme_link';
		const ID = 'issuetype_scheme_link.id';
		const SCOPE = 'issuetype_scheme_link.scope';
		const ISSUETYPE_SCHEME_ID = 'issuetype_scheme_link.issuetype_scheme_id';
		const ISSUETYPE_ID = 'issuetype_scheme_link.issutype_id';

		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
			parent::_addForeignKeyColumn(self::ISSUETYPE_SCHEME_ID, TBGIssuetypeSchemesTable::getTable(), TBGIssuetypeSchemesTable::ID);
			parent::_addForeignKeyColumn(self::ISSUETYPE_ID, TBGIssueTypesTable::getTable(), TBGIssueTypesTable::ID);
		}

		public function getByIssuetypeSchemeID($workflow_scheme_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::ISSUETYPE_SCHEME_ID, $workflow_scheme_id);
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());

			$return_array = array();
			if ($res = $this->doSelect($crit))
			{
				while ($row = $res->getNextRow())
				{
					$return_array[$row->get(self::ISSUETYPE_ID)] = TBGContext::factory()->TBGIssuetype($row->get(self::WORKFLOW_ID), $row);
				}
			}

			return $return_array;
		}
		
		public function associateIssuetypeWithScheme($issuetype_id, $scheme_id)
		{
			$crit = $this->getCriteria();
			$crit->addInsert(self::SCOPE, TBGContext::getScope()->getID());
			$crit->addInsert(self::ISSUETYPE_ID, $issuetype_id);
			$crit->addInsert(self::ISSUETYPE_SCHEME_ID, $scheme_id);
			$this->doInsert($crit);
		}
		
		public function unAssociateIssuetypeWithScheme($issuetype_id, $scheme_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$crit->addWhere(self::ISSUETYPE_ID, $issuetype_id);
			$crit->addWhere(self::ISSUETYPE_SCHEME_ID, $scheme_id);
			$this->doDelete($crit);
		}

	}
