<?php

	// Set the path to The Bug Genie top folder
	$path = realpath(getcwd());
	defined('DS') || define('DS', DIRECTORY_SEPARATOR);
	defined('THEBUGGENIE_SESSION_NAME') || define('THEBUGGENIE_SESSION_NAME', 'THEBUGGENIE');
	defined('THEBUGGENIE_PATH') || define('THEBUGGENIE_PATH', realpath(getcwd() . DS . '..' . DS) . DS);
	defined('THEBUGGENIE_CORE_PATH') || define('THEBUGGENIE_CORE_PATH', THEBUGGENIE_PATH . 'core' . DS);
	defined('THEBUGGENIE_MODULES_PATH') || define('THEBUGGENIE_MODULES_PATH', THEBUGGENIE_PATH . 'modules' . DS);
	defined('THEBUGGENIE_PUBLIC_FOLDER_NAME') || define('THEBUGGENIE_PUBLIC_FOLDER_NAME', mb_substr($path, strrpos($path, DS) + 1));
	defined('B2DB_BASEPATH') || define ('B2DB_BASEPATH', THEBUGGENIE_CORE_PATH . 'B2DB' . DS);
	defined('B2DB_CACHEPATH') || define ('B2DB_CACHEPATH', THEBUGGENIE_CORE_PATH . 'cache' . DS . 'B2DB' . DS);
	
	// Include the "engine" script, which initializes and sets up stuff
	require THEBUGGENIE_CORE_PATH . 'bootstrap.php';
	
	// Trigger the framework's start function
	TBGContext::go();
