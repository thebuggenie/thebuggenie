<?php

	use b2db\Core,
		b2db\Criteria,
		b2db\Criterion;

	/**
	 * Milestones table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Milestones table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 *
	 * @Table(name="milestones")
	 * @Entity(class="TBGMilestone")
	 */
	class TBGMilestonesTable extends TBGB2DBTable 
	{

		const B2DB_TABLE_VERSION = 1;
		const B2DBNAME = 'milestones';
		const ID = 'milestones.id';
		const SCOPE = 'milestones.scope';
		const NAME = 'milestones.name';
		const PROJECT = 'milestones.project';
		const DESCRIPTION = 'milestones.description';
		const MILESTONE_TYPE = 'milestones.itemtype';
		const REACHED = 'milestones.reacheddate';
		const STARTING = 'milestones.startingdate';
		const SCHEDULED = 'milestones.scheduleddate';

//		public function __construct()
//		{
//			parent::__construct(self::B2DBNAME, self::ID);
//			parent::_addVarchar(self::NAME, 100);
//			parent::_addText(self::DESCRIPTION, false);
//			parent::_addInteger(self::REACHED, 10);
//			parent::_addInteger(self::MILESTONE_TYPE, 2);
//			parent::_addInteger(self::STARTING, 10);
//			parent::_addInteger(self::SCHEDULED, 10);
//			parent::_addForeignKeyColumn(self::PROJECT, TBGProjectsTable::getTable(), TBGProjectsTable::ID);
//			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
//		}
		
		public function getAllByProjectID($project_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::PROJECT, $project_id);
			$crit->addOrderBy(self::STARTING, Criteria::SORT_ASC);
			$crit->addOrderBy(self::SCHEDULED, Criteria::SORT_ASC);
			$crit->addOrderBy(self::NAME, Criteria::SORT_ASC);
			$res = $this->doSelect($crit);
			return $res;
		}

		public function getMilestonesByProjectID($project_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::PROJECT, $project_id);
			$crit->addWhere(self::MILESTONE_TYPE, TBGMilestone::TYPE_REGULAR);
			$crit->addOrderBy(self::SCHEDULED, Criteria::SORT_ASC);
			$res = $this->doSelect($crit);
			return $res;
		}

		public function getSprintsByProjectID($project_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::PROJECT, $project_id);
			$crit->addWhere(self::MILESTONE_TYPE, TBGMilestone::TYPE_SCRUMSPRINT);
			$crit->addOrderBy(self::SCHEDULED, Criteria::SORT_ASC);
			$res = $this->doSelect($crit);
			return $res;
		}

		public function setReached($milestone_id)
		{
			$crit = $this->getCriteria();
			$crit->addUpdate(self::REACHED, NOW);
			$this->doUpdateById($crit, $milestone_id);
		}

		public function clearReached($milestone_id)
		{
			$crit = $this->getCriteria();
			$crit->addUpdate(self::REACHED, null);
			$this->doUpdateById($crit, $milestone_id);
		}

	}
