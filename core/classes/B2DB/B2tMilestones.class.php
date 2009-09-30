<?php

	/**
	 * Milestones table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Milestones table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 */
	class B2tMilestones extends B2DBTable 
	{

		const B2DBNAME = 'bugs2_milestones';
		const ID = 'bugs2_milestones.id';
		const SCOPE = 'bugs2_milestones.scope';
		const NAME = 'bugs2_milestones.name';
		const PROJECT = 'bugs2_milestones.project';
		const VISIBLE = 'bugs2_milestones.visible';
		const DESCRIPTION = 'bugs2_milestones.description';
		const REACHED = 'bugs2_milestones.reached';
		const SCHEDULED = 'bugs2_milestones.scheduled';
		
		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addVarchar(self::NAME, 100);
			parent::_addBoolean(self::VISIBLE, true);
			parent::_addText(self::DESCRIPTION, false);
			parent::_addInteger(self::REACHED, 10);
			parent::_addInteger(self::SCHEDULED, 10);
			parent::_addForeignKeyColumn(self::PROJECT, B2DB::getTable('B2tProjects'), B2tProjects::ID);
			parent::_addForeignKeyColumn(self::SCOPE, B2DB::getTable('B2tScopes'), B2tScopes::ID);
		}
		
		public function createNew($name, $project_id)
		{
			$crit = $this->getCriteria();
			$crit->addInsert(self::NAME, $name);
			$crit->addInsert(self::PROJECT, $project_id);
			$crit->addInsert(self::SCOPE, BUGScontext::getScope()->getID());
			$res = $this->doInsert($crit);
			
			return $res->getInsertID();
		}
		
		public function getByProjectID($project_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::PROJECT, $project_id);
			$crit->addOrderBy(self::SCHEDULED, B2DBCriteria::SORT_ASC);
			$res = $this->doSelect($crit);
			return $res;
		}
		
		public function setReached($milestone_id)
		{
			$crit = $this->getCriteria();
			$crit->addUpdate(self::REACHED, $_SERVER["REQUEST_TIME"]);
			$this->doUpdateById($crit, $milestone_id);
		}
		
	}
