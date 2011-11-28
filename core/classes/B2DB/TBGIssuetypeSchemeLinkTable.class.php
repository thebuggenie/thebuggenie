<?php

	use b2db\Core,
		b2db\Criteria,
		b2db\Criterion;

	/**
	 * Link table between issue type scheme and issue type
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Link table between issue type scheme and issue type
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 *
	 * @Table(name="issuetype_scheme_link")
	 */
	class TBGIssuetypeSchemeLinkTable extends TBGB2DBTable
	{

		const B2DB_TABLE_VERSION = 1;
		const B2DBNAME = 'issuetype_scheme_link';
		const ID = 'issuetype_scheme_link.id';
		const SCOPE = 'issuetype_scheme_link.scope';
		const ISSUETYPE_SCHEME_ID = 'issuetype_scheme_link.issuetype_scheme_id';
		const ISSUETYPE_ID = 'issuetype_scheme_link.issuetype_id';
		const REPORTABLE = 'issuetype_scheme_link.reportable';
		const REDIRECT_AFTER_REPORTING = 'issuetype_scheme_link.redirect_after_reporting';

		public function _initialize()
		{
			parent::_setup(self::B2DBNAME, self::ID);
			parent::_addForeignKeyColumn(self::ISSUETYPE_SCHEME_ID, TBGIssuetypeSchemesTable::getTable(), TBGIssuetypeSchemesTable::ID);
			parent::_addForeignKeyColumn(self::ISSUETYPE_ID, TBGIssueTypesTable::getTable(), TBGIssueTypesTable::ID);
			parent::_addBoolean(self::REPORTABLE, true);
			parent::_addBoolean(self::REDIRECT_AFTER_REPORTING, true);
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
		}

		public function _setupIndexes()
		{
			$this->_addIndex('issuetypescheme_scope', array(self::ISSUETYPE_SCHEME_ID, self::SCOPE));
		}

		public function getByIssuetypeSchemeID($issuetype_scheme_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::ISSUETYPE_SCHEME_ID, $issuetype_scheme_id);
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());

			$return_array = array();
			if ($res = $this->doSelect($crit))
			{
				while ($row = $res->getNextRow())
				{
					try
					{
						$i_id = $row->get(self::ISSUETYPE_ID);
						$issuetype = TBGContext::factory()->TBGIssuetype($i_id, $row);
					}
					catch (Exception $e)
					{
						$this->deleteByIssuetypeID($i_id);
					}
					$return_array[$row->get(self::ISSUETYPE_ID)] = array('reportable' => (bool) $row->get(self::REPORTABLE), 'redirect' => (bool) $row->get(self::REDIRECT_AFTER_REPORTING), 'issuetype' => $issuetype);
				}
			}

			return $return_array;
		}
		
		public function deleteByIssuetypeSchemeID($scheme_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::ISSUETYPE_SCHEME_ID, $scheme_id);
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$res = $this->doDelete($crit);
		}
		
		public function deleteByIssuetypeID($type_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::ISSUETYPE_ID, $type_id);
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$res = $this->doDelete($crit);
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
		
		public function setIssuetypeRedirectedAfterReportingForScheme($issuetype_id, $issuetype_scheme_id, $redirected = true)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::ISSUETYPE_SCHEME_ID, $issuetype_scheme_id);
			$crit->addWhere(self::ISSUETYPE_ID, $issuetype_id);
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$crit->addUpdate(self::REDIRECT_AFTER_REPORTING, $redirected);
			$this->doUpdate($crit);
		}
		
		public function setIssuetypeReportableForScheme($issuetype_id, $issuetype_scheme_id, $reportable = true)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::ISSUETYPE_SCHEME_ID, $issuetype_scheme_id);
			$crit->addWhere(self::ISSUETYPE_ID, $issuetype_id);
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$crit->addUpdate(self::REPORTABLE, $reportable);
			$this->doUpdate($crit);
		}

		public function countByIssuetypeID($issuetype_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::ISSUETYPE_ID, $issuetype_id);
			return $this->count($crit);
		}

	}
