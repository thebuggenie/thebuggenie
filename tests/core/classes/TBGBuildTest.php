<?php

require THEBUGGENIE_PATH . 'tests/core/classes/B2DB.class.php';
require THEBUGGENIE_CORE_PATH . 'classes/generics.class.php';
require THEBUGGENIE_CORE_PATH . 'classes/TBGIdentifiableClass.class.php';
require THEBUGGENIE_CORE_PATH . 'classes/TBGReleaseableItem.class.php';
require THEBUGGENIE_CORE_PATH . 'classes/TBGBuild.class.php';

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
	 * @covers TBGBuild::__construct
	 */
	public function testConstruct()
	{
		$build = new TBGBuild(1);
		$this->assertInstanceOf('TBGBuild', $build);

		return $build;
	}

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