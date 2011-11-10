<?php

	use b2db\Saveable;

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
	abstract class TBGScopedClass extends Saveable
	{
		
		/**
		 * The related scope
		 *
		 * @var integer
		 * @Column(type="integer")
		 * @Relates(class="TBGScope")
		 */
		protected $_scope;
		
		public function setScope($scope)
		{
			if ($scope instanceof TBGScope)
			{
				$scope = $scope->getID();
			}
			$this->_scope = $scope;
		}
		
		public function getScope()
		{
			if (!$this->_scope instanceof TBGScope)
			{
				$this->_scope = TBGContext::factory()->TBGScope($this->_scope);
			}
			return $this->_scope;
		}
		
	}
