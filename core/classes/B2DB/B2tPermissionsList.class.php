<?php

	/**
	 * Permissions list table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Permissions list table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 */
	class B2tPermissionsList extends B2DBTable 
	{
		
		const B2DBNAME = 'bugs2_permissionslist';
		const ID = 'bugs2_permissionslist.id';
		const SCOPE = 'bugs2_permissionslist.scope';
		const PERMISSION_NAME = 'bugs2_permissionslist.permission_name';
		const LEVELS = 'bugs2_permissionslist.levels';
		const DESCRIPTION = 'bugs2_permissionslist.description';
		const APPLIES_TO = 'bugs2_permissionslist.applies_to';
		
		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			
			parent::_addVarchar(self::PERMISSION_NAME, 100);
			parent::_addInteger(self::LEVELS, 3);
			parent::_addVarchar(self::DESCRIPTION, 200, '');
			parent::_addVarchar(self::APPLIES_TO, 100);
			parent::_addForeignKeyColumn(self::SCOPE, B2DB::getTable('B2tScopes'), B2tScopes::ID);
		}
		
		public function getByAppliesTo($applies_to)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::APPLIES_TO, $applies_to);
			$res = $this->doSelect($crit);
			return $res;
		}
		
		public function getAll()
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::SCOPE, BUGScontext::getScope()->getID());
			$res = $this->doSelect($crit);
			return $res;
		}

		public function loadFixtures($scope)
		{
			$basecrit = $this->getCriteria();
			$basecrit->addInsert(self::SCOPE, $scope);
			$i18n = BUGScontext::getI18n();

			$crit = clone $basecrit;
			$crit->addInsert(self::LEVELS, 2);
			$crit->addInsert(self::APPLIES_TO, 'user');
			$crit->addInsert(self::PERMISSION_NAME, 'b2canonlyviewownissues');
			$crit->addInsert(self::DESCRIPTION, $i18n->__('Can only view issues reported by the user'));
			$this->doInsert($crit);

			$crit = clone $basecrit;
			$crit->addInsert(self::LEVELS, 2);
			$crit->addInsert(self::APPLIES_TO, 'general');
			$crit->addInsert(self::PERMISSION_NAME, 'b2canreadallcomments');
			$crit->addInsert(self::DESCRIPTION, $i18n->__('Can view comments that are not public'));
			$this->doInsert($crit);

			$crit = clone $basecrit;
			$crit->addInsert(self::LEVELS, 2);
			$crit->addInsert(self::APPLIES_TO, 'general');
			$crit->addInsert(self::PERMISSION_NAME, 'b2addlinks');
			$crit->addInsert(self::DESCRIPTION, $i18n->__('Can add links to issue reports'));
			$this->doInsert($crit);

			$crit = clone $basecrit;
			$crit->addInsert(self::LEVELS, 2);
			$crit->addInsert(self::APPLIES_TO, 'general');
			$crit->addInsert(self::PERMISSION_NAME, 'b2uploadfiles');
			$crit->addInsert(self::DESCRIPTION, $i18n->__('Can add files to issue reports'));
			$this->doInsert($crit);

			$crit = clone $basecrit;
			$crit->addInsert(self::LEVELS, 2);
			$crit->addInsert(self::APPLIES_TO, 'general');
			$crit->addInsert(self::PERMISSION_NAME, 'b2canfindissues');
			$crit->addInsert(self::DESCRIPTION, $i18n->__('Can search for issues'));
			$this->doInsert($crit);

			$crit = clone $basecrit;
			$crit->addInsert(self::LEVELS, 2);
			$crit->addInsert(self::APPLIES_TO, 'projects');
			$crit->addInsert(self::PERMISSION_NAME, 'b2canvote');
			$crit->addInsert(self::DESCRIPTION, $i18n->__('Can vote for issues'));
			$this->doInsert($crit);

			$crit = clone $basecrit;
			$crit->addInsert(self::LEVELS, 2);
			$crit->addInsert(self::APPLIES_TO, 'projects');
			$crit->addInsert(self::PERMISSION_NAME, 'b2candeleteissues');
			$crit->addInsert(self::DESCRIPTION, $i18n->__('Can delete issues'));
			$this->doInsert($crit);

			$crit = clone $basecrit;
			$crit->addInsert(self::LEVELS, 2);
			$crit->addInsert(self::APPLIES_TO, 'projects');
			$crit->addInsert(self::PERMISSION_NAME, 'b2caneditissuefields');
			$crit->addInsert(self::DESCRIPTION, $i18n->__('Can update issue details'));
			$this->doInsert($crit);

			$crit = clone $basecrit;
			$crit->addInsert(self::LEVELS, 2);
			$crit->addInsert(self::APPLIES_TO, 'projects');
			$crit->addInsert(self::PERMISSION_NAME, 'b2caneditissueusers');
			$crit->addInsert(self::DESCRIPTION, $i18n->__('Can assign issues'));
			$this->doInsert($crit);

			$crit = clone $basecrit;
			$crit->addInsert(self::LEVELS, 2);
			$crit->addInsert(self::APPLIES_TO, 'projects');
			$crit->addInsert(self::PERMISSION_NAME, 'b2caneditissuetext');
			$crit->addInsert(self::DESCRIPTION, $i18n->__('Can edit issue text'));
			$this->doInsert($crit);

			$crit = clone $basecrit;
			$crit->addInsert(self::LEVELS, 4);
			$crit->addInsert(self::APPLIES_TO, 'projects');
			$crit->addInsert(self::PERMISSION_NAME, 'b2caneditcomments');
			$crit->addInsert(self::DESCRIPTION, $i18n->__('Can edit all comments'));
			$this->doInsert($crit);

			$crit = clone $basecrit;
			$crit->addInsert(self::LEVELS, 4);
			$crit->addInsert(self::APPLIES_TO, 'projects');
			$crit->addInsert(self::PERMISSION_NAME, 'b2canaddcomments');
			$crit->addInsert(self::DESCRIPTION, $i18n->__('Can add comments'));
			$this->doInsert($crit);

			$crit = clone $basecrit;
			$crit->addInsert(self::LEVELS, 4);
			$crit->addInsert(self::APPLIES_TO, 'projects');
			$crit->addInsert(self::PERMISSION_NAME, 'b2canviewcomments');
			$crit->addInsert(self::DESCRIPTION, $i18n->__('Can view comments'));
			$this->doInsert($crit);

			$crit = clone $basecrit;
			$crit->addInsert(self::LEVELS, 2);
			$crit->addInsert(self::APPLIES_TO, 'issues');
			$crit->addInsert(self::PERMISSION_NAME, 'b2noteditcomments');
			$crit->addInsert(self::DESCRIPTION, $i18n->__('Can not edit comments'));
			$this->doInsert($crit);

			$crit = clone $basecrit;
			$crit->addInsert(self::LEVELS, 2);
			$crit->addInsert(self::APPLIES_TO, 'issues');
			$crit->addInsert(self::PERMISSION_NAME, 'b2notaddcomments');
			$crit->addInsert(self::DESCRIPTION, $i18n->__('Can not add comments'));
			$this->doInsert($crit);

			$crit = clone $basecrit;
			$crit->addInsert(self::LEVELS, 2);
			$crit->addInsert(self::APPLIES_TO, 'issues');
			$crit->addInsert(self::PERMISSION_NAME, 'b2hidecomments');
			$crit->addInsert(self::DESCRIPTION, $i18n->__('Hide comments'));
			$this->doInsert($crit);

			$crit = clone $basecrit;
			$crit->addInsert(self::LEVELS, 2);
			$crit->addInsert(self::APPLIES_TO, 'issues');
			$crit->addInsert(self::PERMISSION_NAME, 'b2cantvote');
			$crit->addInsert(self::DESCRIPTION, $i18n->__('Restrict voting'));
			$this->doInsert($crit);

			$crit = clone $basecrit;
			$crit->addInsert(self::LEVELS, 2);
			$crit->addInsert(self::APPLIES_TO, 'projects');
			$crit->addInsert(self::PERMISSION_NAME, 'b2canaddbuilds');
			$crit->addInsert(self::DESCRIPTION, $i18n->__('Can add builds to list of affected builds'));
			$this->doInsert($crit);

			$crit = clone $basecrit;
			$crit->addInsert(self::LEVELS, 2);
			$crit->addInsert(self::APPLIES_TO, 'projects');
			$crit->addInsert(self::PERMISSION_NAME, 'b2canaddcomponents');
			$crit->addInsert(self::DESCRIPTION, $i18n->__('Can add components to list of affected components'));
			$this->doInsert($crit);
		}
		
	}
