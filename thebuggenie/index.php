<?php

	// Set the path to The Bug Genie top folder
	$path = realpath(getcwd());
	defined('THEBUGGENIE_SESSION_NAME') || define('THEBUGGENIE_SESSION_NAME', 'THEBUGGENIE');
	defined('THEBUGGENIE_PATH') || define('THEBUGGENIE_PATH', realpath(getcwd() . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR);
	defined('THEBUGGENIE_CORE_PATH') || define('THEBUGGENIE_CORE_PATH', THEBUGGENIE_PATH . 'core' . DIRECTORY_SEPARATOR);
	defined('THEBUGGENIE_MODULES_PATH') || define('THEBUGGENIE_MODULES_PATH', THEBUGGENIE_PATH . 'modules' . DIRECTORY_SEPARATOR);
	defined('THEBUGGENIE_PUBLIC_FOLDER_NAME') || define('THEBUGGENIE_PUBLIC_FOLDER_NAME', mb_substr($path, strrpos($path, DIRECTORY_SEPARATOR) + 1));
	
	// Include the "engine" script, which initializes and sets up stuff
	require THEBUGGENIE_CORE_PATH . 'tbg_engine.inc.php';
	
	// Trigger the framework's start function
	TBGContext::go();
