<?php

	/**
	 * Userstate table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 ** @version 3.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Userstate table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 */
	class TBGUserStateTable extends B2DBTable 
	{

		const B2DBNAME = 'userstate';
		const ID = 'userstate.id';
		const SCOPE = 'userstate.scope';
		const STATE_NAME = 'userstate.state_name';
		const UNAVAILABLE = 'userstate.unavailable';
		const BUSY = 'userstate.busy';
		const ONLINE = 'userstate.online';
		const MEETING = 'userstate.meeting';
		const COLOR = 'userstate.color';
		const ABSENT = 'userstate.absent';

		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			
			parent::_addVarchar(self::STATE_NAME, 100);
			parent::_addBoolean(self::UNAVAILABLE);
			parent::_addBoolean(self::BUSY);
			parent::_addBoolean(self::ONLINE);
			parent::_addBoolean(self::MEETING);
			parent::_addBoolean(self::ABSENT);
			parent::_addVarchar(self::COLOR, 7, '');
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
		}

		public function loadFixtures($scope)
		{
			$i18n = TBGContext::getI18n();

			$crit = $this->getCriteria();
			$crit->addInsert(self::STATE_NAME, 'Available');
			$crit->addInsert(self::SCOPE, $scope);
			$crit->addInsert(self::UNAVAILABLE, 0);
			$crit->addInsert(self::BUSY, 0);
			$crit->addInsert(self::ONLINE, 1);
			$crit->addInsert(self::MEETING, 0);
			$crit->addInsert(self::ABSENT, 0);
			$this->doInsert($crit);

			$crit = $this->getCriteria();
			$crit->addInsert(self::STATE_NAME, 'Offline');
			$crit->addInsert(self::SCOPE, $scope);
			$crit->addInsert(self::UNAVAILABLE, 1);
			$crit->addInsert(self::BUSY, 0);
			$crit->addInsert(self::ONLINE, 0);
			$crit->addInsert(self::MEETING, 0);
			$crit->addInsert(self::ABSENT, 0);
			$this->doInsert($crit);

			$crit = $this->getCriteria();
			$crit->addInsert(self::STATE_NAME, 'Busy');
			$crit->addInsert(self::SCOPE, $scope);
			$crit->addInsert(self::UNAVAILABLE, 0);
			$crit->addInsert(self::BUSY, 1);
			$crit->addInsert(self::ONLINE, 1);
			$crit->addInsert(self::MEETING, 0);
			$crit->addInsert(self::ABSENT, 0);
			$this->doInsert($crit);

			$crit = $this->getCriteria();
			$crit->addInsert(self::STATE_NAME, 'Unavailable');
			$crit->addInsert(self::SCOPE, $scope);
			$crit->addInsert(self::UNAVAILABLE, 1);
			$crit->addInsert(self::BUSY, 0);
			$crit->addInsert(self::ONLINE, 1);
			$crit->addInsert(self::MEETING, 0);
			$crit->addInsert(self::ABSENT, 0);
			$this->doInsert($crit);

			$crit = $this->getCriteria();
			$crit->addInsert(self::STATE_NAME, 'In a meeting');
			$crit->addInsert(self::SCOPE, $scope);
			$crit->addInsert(self::UNAVAILABLE, 1);
			$crit->addInsert(self::BUSY, 1);
			$crit->addInsert(self::ONLINE, 1);
			$crit->addInsert(self::MEETING, 1);
			$crit->addInsert(self::ABSENT, 0);
			$this->doInsert($crit);

			$crit = $this->getCriteria();
			$crit->addInsert(self::STATE_NAME, 'Coding');
			$crit->addInsert(self::SCOPE, $scope);
			$crit->addInsert(self::UNAVAILABLE, 0);
			$crit->addInsert(self::BUSY, 1);
			$crit->addInsert(self::ONLINE, 1);
			$crit->addInsert(self::MEETING, 0);
			$crit->addInsert(self::ABSENT, 0);
			$this->doInsert($crit);

			$crit = $this->getCriteria();
			$crit->addInsert(self::STATE_NAME, 'On coffee break');
			$crit->addInsert(self::SCOPE, $scope);
			$crit->addInsert(self::UNAVAILABLE, 1);
			$crit->addInsert(self::BUSY, 1);
			$crit->addInsert(self::ONLINE, 1);
			$crit->addInsert(self::MEETING, 0);
			$crit->addInsert(self::ABSENT, 0);
			$this->doInsert($crit);

			$crit = $this->getCriteria();
			$crit->addInsert(self::STATE_NAME, 'Away');
			$crit->addInsert(self::SCOPE, $scope);
			$crit->addInsert(self::UNAVAILABLE, 1);
			$crit->addInsert(self::BUSY, 1);
			$crit->addInsert(self::ONLINE, 1);
			$crit->addInsert(self::MEETING, 0);
			$crit->addInsert(self::ABSENT, 1);
			$this->doInsert($crit);

			$crit = $this->getCriteria();
			$crit->addInsert(self::STATE_NAME, 'On vacation');
			$crit->addInsert(self::SCOPE, $scope);
			$crit->addInsert(self::UNAVAILABLE, 1);
			$crit->addInsert(self::BUSY, 1);
			$crit->addInsert(self::ONLINE, 0);
			$crit->addInsert(self::MEETING, 0);
			$crit->addInsert(self::ABSENT, 1);
			$this->doInsert($crit);
		}
		
	}
