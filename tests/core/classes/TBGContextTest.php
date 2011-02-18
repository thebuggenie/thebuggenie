<?php

	require THEBUGGENIE_CORE_PATH . 'classes/TBGContext.class.php';

	class TBGContextTest extends PHPUnit_Framework_TestCase
	{

		/**
		 * @covers TBGContext::isInstallmode
		 * @covers TBGContext::checkInstallMode
		 */
		public function testInstallMode()
		{
			TBGContext::checkInstallMode();
			if (file_exists(THEBUGGENIE_PATH . 'installed'))
			{
				$this->assertFalse(TBGContext::isInstallmode());
			}
			else
			{
				$this->assertTrue(TBGContext::isInstallmode());
			}
		}

	}

