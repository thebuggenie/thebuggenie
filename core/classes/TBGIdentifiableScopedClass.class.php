<?php

	/**
	 * An identifiable class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage core
	 */

	/**
	 * An identifiable class
	 *
	 * @package thebuggenie
	 * @subpackage core
	 */
	abstract class TBGIdentifiableScopedClass extends \b2db\Saveable implements TBGIdentifiable
	{
		
		/**
		 * The id for this item, usually identified by a record in the database
		 *
		 * @var integer
		 * @Id
		 * @Column(type="integer", not_null=true, auto_increment=1, length=10, unsigned=true)
		 */
		protected $_id;

		/**
		 * The related scope
		 *
		 * @var integer
		 * @Column(type="integer", length=10)
		 * @Relates(class="TBGScope")
		 */
		protected $_scope;

		/**
		 * Set the scope this item is in
		 *
		 * @param TBGScope $scope
		 */
		public function setScope($scope)
		{
			$this->_scope = $scope;
		}

		/**
		 * Retrieve the scope this item is in
		 *
		 * @return TBGScope
		 */
		public function getScope()
		{
			if (!$this->_scope instanceof TBGScope)
				$this->_b2dbLazyload('_scope');

			return $this->_scope;
		}
		
		/**
		 * Return the items id
		 * 
		 * @return integer
		 */
		public function getID()
		{
			return (integer) $this->_id;
		}

		/**
		 * Set the items id
		 *
		 * @param integer $id
		 */
		public function setID($id)
		{
			$this->_id = (integer) $id;
		}

		protected function _preSave($is_new)
		{
			if ($is_new && $this->_scope === null)
				$this->_scope = TBGContext::getScope();
		}

	}
