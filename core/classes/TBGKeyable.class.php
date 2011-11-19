<?php

	/**
	 * Generic keyable class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage main
	 */

	/**
	 * Generic keyable class
	 *
	 * @package thebuggenie
	 * @subpackage main
	 */
	abstract class TBGKeyable extends TBGIdentifiableScopedClass
	{

		/**
		 * The key for this item
		 *
		 * @var string
		 * @Column(type="string", length=100)
		 */
		protected $_key = null;

		protected function _generateKey()
		{
			if ($this->_key === null)
				$this->_key = preg_replace("/[^0-9a-zA-Z]/i", '', mb_strtolower($this->getName()));
		}
		
		public function getKey()
		{
			$this->_generateKey();
			return $this->_key;
		}		

		public function setKey($key)
		{
			$this->_key = $key;
		}

	}
