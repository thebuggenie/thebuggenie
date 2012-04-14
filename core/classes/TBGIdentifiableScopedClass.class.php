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
	abstract class TBGIdentifiableScopedClass extends TBGIdentifiableClass
	{
		
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
		
		protected function _preSave($is_new)
		{
			if ($is_new && $this->_scope === null)
				$this->_scope = TBGContext::getScope();
		}

	}
