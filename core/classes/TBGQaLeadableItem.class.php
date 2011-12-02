<?php

	/**
	 * Item class for objects with both QA responsible and Leader properties
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage core
	 */

	/**
	 * Item class for objects with both QA responsible and Leader properties
	 *
	 * @package thebuggenie
	 * @subpackage core
	 */
	class TBGQaLeadableItem extends TBGReleaseableItem
	{

		/**
		 * The lead type for the project, TBGIdentifiableClass::TYPE_USER or TBGIdentifiableClass::TYPE_TEAM
		 *
		 * @var TBGTeam
		 * @Column(type="integer", length=10)
		 * @Relates(class="TBGTeam")
		 */
		protected $_leader_team;

		/**
		 * The lead for the project
		 *
		 * @var TBGUser
		 * @Column(type="integer", length=10)
		 * @Relates(class="TBGUser")
		 */
		protected $_leader_user;

		/**
		 * The QA responsible for the project, TBGIdentifiableClass::TYPE_USER or TBGIdentifiableClass::TYPE_TEAM
		 *
		 * @var TBGTeam
		 * @Column(type="integer", length=10)
		 * @Relates(class="TBGTeam")
		 */
		protected $_qa_responsible_team;

		/**
		 * The QA responsible for the project
		 *
		 * @var TBGUser
		 * @Column(type="integer", length=10)
		 * @Relates(class="TBGUser")
		 */
		protected $_qa_responsible_user;

		public function getLeader()
		{
			$this->_b2dbLazyload('_leader_team');
			$this->_b2dbLazyload('_leader_user');

			if ($this->_leader_team instanceof TBGTeam) {
				return $this->_leader_team;
			} elseif ($this->_leader_user instanceof TBGUser) {
				return $this->_leader_user;
			} else {
				return null;
			}
		}

		public function hasLeader()
		{
			return (bool) ($this->getLeader() instanceof TBGIdentifiable);
		}

		public function setLeader(TBGIdentifiable $leader)
		{
			if ($leader instanceof TBGTeam) {
				$this->_leader_user = null;
				$this->_leader_team = $leader;
			} else {
				$this->_leader_team = null;
				$this->_leader_user = $leader;
			}
		}

		public function clearLeader()
		{
			$this->_leader_team = null;
			$this->_leader_user = null;
		}

		public function getQaResponsible()
		{
			if ($this->_qa_responsible_team !== null) {
				$this->_b2dbLazyload('_qa_responsible_team');
			} elseif ($this->_qa_responsible_user !== null) {
				$this->_b2dbLazyload('_qa_responsible_user');
			}

			if ($this->_qa_responsible_team instanceof TBGTeam) {
				return $this->_qa_responsible_team;
			} elseif ($this->_qa_responsible_user instanceof TBGUser) {
				return $this->_qa_responsible_user;
			} else {
				return null;
			}
		}

		public function hasQaResponsible()
		{
			return (bool) ($this->getQaResponsible() instanceof TBGIdentifiable);
		}

		public function setQaResponsible(TBGIdentifiable $qa_responsible)
		{
			if ($qa_responsible instanceof TBGTeam) {
				$this->_qa_responsible_user = null;
				$this->_qa_responsible_team = $qa_responsible;
			} else {
				$this->_qa_responsible_team = null;
				$this->_qa_responsible_user = $qa_responsible;
			}
		}

		public function clearQaResponsible()
		{
			$this->_qa_responsible_team = null;
			$this->_qa_responsible_user = null;
		}

	}
