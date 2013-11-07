<?php

defined('DS') || define('DS', DIRECTORY_SEPARATOR);
defined('THEBUGGENIE_PATH') || define('THEBUGGENIE_PATH', realpath(getcwd() . DS) . DS);
defined('THEBUGGENIE_CORE_PATH') || define('THEBUGGENIE_CORE_PATH', THEBUGGENIE_PATH . 'core' . DS);

require THEBUGGENIE_CORE_PATH . 'bootstrap.php';

class TBGBuildsTable
{
	const B2DBNAME = 'builds';
	const ID = 'builds.id';
	const SCOPE = 'builds.scope';
	const NAME = 'builds.name';
	const VERSION_MAJOR = 'builds.version_major';
	const VERSION_MINOR = 'builds.version_minor';
	const VERSION_REVISION = 'builds.version_revision';
	const EDITION = 'builds.edition';
	const TIMESTAMP = 'builds.timestamp';
	const RELEASE_DATE = 'builds.release_date';
	const IS_DEFAULT = 'builds.is_default';
	const LOCKED = 'builds.locked';
	const PROJECT = 'builds.project';
	const RELEASED = 'builds.released';
}

class Row
{
	public function get($fieldname)
	{
		return $fieldname;
	}
}

/**
 * Test class for TBGBuild
 */
class TBGBuildTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @covers TBGBuild::getID
	 * @depends testConstruct
	 */
	public function testGetID(TBGBuild $build)
	{
		$this->assertEquals(1, $build->getID());
	}

	/**
	 * @covers TBGBuild::getVersion
	 * @covers TBGBuild::getName
	 * @covers TBGBuild::getPrintableName
	 * @depends testConstruct
	 */
	public function testGetPrintableName(TBGBuild $build)
	{
		$this->assertEquals(TBGBuildsTable::NAME . ' (' . TBGBuildsTable::VERSION_MAJOR . '.' . TBGBuildsTable::VERSION_MINOR . '.' . TBGBuildsTable::VERSION_REVISION . ')', $build->getPrintableName());
	}

}
