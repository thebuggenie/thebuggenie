<?php

	// Set the path to The Bug Genie top folder
	$path = realpath(getcwd());
	defined('DS') || define('DS', DIRECTORY_SEPARATOR);
	defined('THEBUGGENIE_SESSION_NAME') || define('THEBUGGENIE_SESSION_NAME', 'THEBUGGENIE');

	// Default behaviour: define the path to thebuggenie as one folder up from this
	defined('THEBUGGENIE_PATH') || define('THEBUGGENIE_PATH', realpath(getcwd() . DS . '..' . DS) . DS);

	// Default behavious: define the public folder name to "thebuggenie" (actually autodetect name of current folder)
	defined('THEBUGGENIE_PUBLIC_FOLDER_NAME') || define('THEBUGGENIE_PUBLIC_FOLDER_NAME', mb_substr($path, strrpos($path, DS) + 1));

	// Root installation: http://issues.thebuggenie.com/wiki/TheBugGenie:HowTo:RootDirectoryInstallation
	// ----
	// Don't look one directory up to find the path to the bug genie
	// defined('THEBUGGENIE_PATH') || define('THEBUGGENIE_PATH', realpath(getcwd() . DS) . DS);
	// Don't autodetect the subfolder, but use "" instead, since there is none
	// defined('THEBUGGENIE_PUBLIC_FOLDER_NAME') || define('THEBUGGENIE_PUBLIC_FOLDER_NAME', '');
	// ----

	defined('THEBUGGENIE_CORE_PATH') || define('THEBUGGENIE_CORE_PATH', THEBUGGENIE_PATH . 'core' . DS);
	defined('THEBUGGENIE_MODULES_PATH') || define('THEBUGGENIE_MODULES_PATH', THEBUGGENIE_PATH . 'modules' . DS);
	defined('B2DB_BASEPATH') || define ('B2DB_BASEPATH', THEBUGGENIE_CORE_PATH . 'B2DB' . DS);
	defined('B2DB_CACHEPATH') || define ('B2DB_CACHEPATH', THEBUGGENIE_CORE_PATH . 'cache' . DS . 'B2DB' . DS);
	defined('GESHI_ROOT') || define('GESHI_ROOT', THEBUGGENIE_CORE_PATH . 'geshi' . DS);
	
	// Include the "engine" script, which initializes and sets up stuff
	require THEBUGGENIE_CORE_PATH . 'bootstrap.php';
	
	// Trigger the framework's start function
	TBGContext::go();
