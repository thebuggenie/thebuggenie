<?php

	/**
	 * Files table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Files table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 */
	class TBGFilesTable extends B2DBTable 
	{

		const B2DBNAME = 'files';
		const ID = 'files.id';
		const SCOPE = 'files.scope';
		const UID = 'files.uid';
		const UPLOADED_AT = 'files.uploaded_at';
		const REAL_FILENAME = 'files.real_filename';
		const ORIGINAL_FILENAME = 'files.original_filename';
		const CONTENT_TYPE = 'files.content_type';
		const CONTENT = 'files.content';
		const DESCRIPTION = 'files.description';

		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addForeignKeyColumn(self::UID, B2DB::getTable('TBGUsersTable'), TBGUsersTable::ID);
			parent::_addForeignKeyColumn(self::SCOPE, B2DB::getTable('TBGScopesTable'), TBGScopesTable::ID);
			parent::_addVarchar(self::REAL_FILENAME, 250);
			parent::_addVarchar(self::CONTENT_TYPE, 250);
			parent::_addVarchar(self::ORIGINAL_FILENAME, 250);
			parent::_addInteger(self::UPLOADED_AT, 10);
			parent::_addBlob(self::CONTENT);
			parent::_addText(self::DESCRIPTION, false);
		}
		
		public function saveFile($real_filename, $original_filename, $content_type, $description = null, $content = null)
		{
			$crit = $this->getCriteria();
			$crit->addInsert(self::UID, TBGContext::getUser()->getUID());
			$crit->addInsert(self::REAL_FILENAME, $real_filename);
			$crit->addInsert(self::ORIGINAL_FILENAME, $original_filename);
			$crit->addInsert(self::CONTENT_TYPE, $content_type);
			$crit->addInsert(self::SCOPE, TBGContext::getScope()->getID());
			if ($description !== null)
			{
				$crit->addInsert(self::DESCRIPTION, $description);
			}
			if ($content !== null)
			{
				$crit->addInsert(self::CONTENT, $content);
			}
			$res = $this->doInsert($crit);

			return $res->getInsertID();
		}

	}
