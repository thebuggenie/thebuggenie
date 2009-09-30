<?php

	/**
	 * Edition components table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Edition components table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 */
	class B2tEditionComponents extends B2DBTable 
	{

		const B2DBNAME = 'bugs2_editioncomponents';
		const ID = 'bugs2_editioncomponents.id';
		const SCOPE = 'bugs2_editioncomponents.scope';
		const EDITION = 'bugs2_editioncomponents.edition';
		const COMPONENT = 'bugs2_editioncomponents.component';

		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addForeignKeyColumn(self::EDITION, B2DB::getTable('B2tEditions'), B2tEditions::ID);
			parent::_addForeignKeyColumn(self::COMPONENT, B2DB::getTable('B2tComponents'), B2tComponents::ID);
			parent::_addForeignKeyColumn(self::SCOPE, B2DB::getTable('B2tScopes'), B2tScopes::ID);
		}
		
	}
