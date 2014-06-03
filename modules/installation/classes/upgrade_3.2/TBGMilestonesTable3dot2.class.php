<?php

	use b2db\Core,
		b2db\Criteria,
		b2db\Criterion;

	/**
	 * Milestones table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.2
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
	 * @Entity(class="TBGMilestone3dot2")
	 */
	class TBGMilestonesTable3dot2 extends TBGB2DBTable
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

	}
