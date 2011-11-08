<?php

	// This code requires PHP 5.3 or newer, so if we don't have it - don't continue
	if (PHP_VERSION_ID < 50300)
		die('This software requires PHP 5.3.0 or newer, but you have an older version. Please upgrade.');

	date_default_timezone_set('UTC');
	
	if (!defined('THEBUGGENIE_PATH'))
		throw new Exception('You must define the THEBUGGENIE_PATH constant so we can find the files we need');

	// Load the context class, which controls most of things
	require THEBUGGENIE_CORE_PATH . 'classes' . DS . 'TBGContext.class.php';
	spl_autoload_register(array('TBGContext', 'autoload'));

	TBGContext::addAutoloaderClassPath(THEBUGGENIE_CORE_PATH . 'classes' . DS);
	TBGContext::addAutoloaderClassPath(THEBUGGENIE_CORE_PATH . 'classes' . DS . 'B2DB' . DS);
	TBGContext::autoloadNamespace('b2db', THEBUGGENIE_CORE_PATH . 'B2DB' . DS);

	TBGContext::initialize();
