<?php

	// Set the path to The Bug Genie top folder
	define ('THEBUGGENIE_PATH', realpath(getcwd() . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR);

	// Include the "engine" script, which initializes and sets up stuff
	require THEBUGGENIE_PATH . 'core/tbg_engine.inc.php';
	// Trigger the framework's start function
	TBGContext::go();
