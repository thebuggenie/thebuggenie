<?php

	//require THEBUGGENIE_CORE_PATH . 'classes/TBGContext.class.php';

	class TBGContextTest extends PHPUnit_Framework_TestCase
	{

		/**
		 * @covers \thebuggenie\core\framework\Context::isInstallmode
		 * @covers \thebuggenie\core\framework\Context::checkInstallMode
		 */
		public function testInstallMode()
		{
			\thebuggenie\core\framework\Context::checkInstallMode();
			if (file_exists(THEBUGGENIE_PATH . 'installed'))
			{
				$this->assertFalse(\thebuggenie\core\framework\Context::isInstallmode());
			}
			else
			{
				$this->assertTrue(\thebuggenie\core\framework\Context::isInstallmode());
			}
		}

	}

