<?php

	/**
	 * Ownable item class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage core
	 */

	/**
	 * Ownable item class
	 *
	 * @package thebuggenie
	 * @subpackage core
	 */
	class TBGOwnableItem extends TBGIdentifiableScopedClass
	{

		/**
		 * The project owner if team
		 *
		 * @var TBGTeam
		 * @Column(type="integer", length=10)
		 * @Relates(class="TBGTeam")
		 */
		protected $_owner_team;

		/**
		 * The project owner if user
		 *
		 * @var TBGUser
		 * @Column(type="integer", length=10)
		 * @Relates(class="TBGUser")
		 */
		protected $_owner_user;

		public function getOwner()
		{
			$this->_b2dbLazyload('_owner_team');
			$this->_b2dbLazyload('_owner_user');

			if ($this->_owner_team instanceof TBGTeam) {
				return $this->_owner_team;
			} elseif ($this->_owner_user instanceof TBGUser) {
				return $this->_owner_user;
			} else {
				return null;
			}
		}

		public function hasOwner()
		{
			return (bool) ($this->getOwner() instanceof TBGIdentifiable);
		}

		public function setOwner(TBGIdentifiable $owner)
		{
			if ($owner instanceof TBGTeam) {
				$this->_owner_user = null;
				$this->_owner_team = $owner;
			} else {
				$this->_owner_team = null;
				$this->_owner_user = $owner;
			}
		}

		public function clearOwner()
		{
			$this->_owner_team = null;
			$this->_owner_user = null;
		}

	}