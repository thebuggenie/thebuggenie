<?php

	use b2db\Core,
		b2db\Criteria,
		b2db\Criterion;

	/**
	 * Articles <-> Files table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Articles <-> Files table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 */
	class TBGArticleFilesTable extends TBGB2DBTable
	{

		const B2DB_TABLE_VERSION = 1;
		const B2DBNAME = 'articlefiles';
		const ID = 'articlefiles.id';
		const SCOPE = 'articlefiles.scope';
		const UID = 'articlefiles.uid';
		const ATTACHED_AT = 'articlefiles.attached_at';
		const FILE_ID = 'articlefiles.file_id';
		const ARTICLE_ID = 'articlefiles.article_id';

		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addForeignKeyColumn(self::UID, TBGUsersTable::getTable(), TBGUsersTable::ID);
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
			parent::_addForeignKeyColumn(self::ARTICLE_ID, TBGArticlesTable::getTable(), TBGArticlesTable::ID);
			parent::_addForeignKeyColumn(self::FILE_ID, TBGFilesTable::getTable(), TBGFilesTable::ID);
			parent::_addInteger(self::ATTACHED_AT, 10);
		}

		public function addByArticleIDandFileID($article_id, $file_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::ARTICLE_ID, $article_id);
			$crit->addWhere(self::FILE_ID, $file_id);
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			if ($this->doCount($crit) == 0)
			{
				$crit = $this->getCriteria();
				$crit->addInsert(self::SCOPE, TBGContext::getScope()->getID());
				$crit->addInsert(self::ATTACHED_AT, time());
				$crit->addInsert(self::ARTICLE_ID, $article_id);
				$crit->addInsert(self::FILE_ID, $file_id);
				$this->doInsert($crit);
			}
		}

		public function getByArticleID($article_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::ARTICLE_ID, $article_id);
			$res = $this->doSelect($crit);
			
			$ret_arr = array();

			if ($res)
			{
				while ($row = $res->getNextRow())
				{
					try
					{
						$file = TBGContext::factory()->TBGFile($row->get(TBGFilesTable::ID), $row);
						$file->setUploadedAt($row->get(self::ATTACHED_AT));
						$ret_arr[$row->get(TBGFilesTable::ID)] = $file;
					}
					catch (Exception $e)
					{
						$this->doDeleteById($row->get(self::ID));
					}
				}
			}
			
			return $ret_arr;
		}

		public function removeByArticleIDandFileID($article_id, $file_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::ARTICLE_ID, $article_id);
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			if ($res = $this->doSelectById($file_id, $crit))
			{
				$this->doDelete($crit);
			}
			return $res;
		}
		
	}
