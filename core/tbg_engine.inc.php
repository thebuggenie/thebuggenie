<?php

	date_default_timezone_set('UTC');
	defined('DS') || define('DS', DIRECTORY_SEPARATOR);
	
	// The time the script was loaded
	$starttime = explode(' ', microtime());
	define('NOW', $starttime[1]);

	if (!defined('THEBUGGENIE_PATH'))
		throw new Exception('You must define the THEBUGGENIE_PATH constant so we can find the files we need');

	// Load the context class, which controls most of things
	require THEBUGGENIE_CORE_PATH . 'classes' . DS . 'TBGContext.class.php';
	
	// Set up error and exception handling
	set_exception_handler(array('TBGContext', 'exceptionHandler'));
	set_error_handler(array('TBGContext', 'errorHandler'));
	error_reporting(E_ALL | E_NOTICE | E_STRICT);
	
	spl_autoload_register(array('TBGContext', 'autoload'));

	// Load the logging class so we can log stuff
	require THEBUGGENIE_CORE_PATH . 'classes' . DS . 'TBGLogging.class.php';

	// This code requires PHP 5.3 or newer, so if we don't have it - complain
	if (PHP_VERSION_ID < 50300)
	{
		throw new Exception('This software requires PHP 5.3.0 or newer, but you have an older version. Please upgrade');
	}

	// Start loading The Bug Genie
	try
	{
		// Set the start time
		TBGContext::setLoadStart($starttime[1] + $starttime[0]);
		TBGLogging::log('Initializing Caspar framework');
		TBGLogging::log('PHP_SAPI says "' . PHP_SAPI . '"');

		if (!isset($argc) && !ini_get('session.auto_start'))
		{
			session_name(THEBUGGENIE_SESSION_NAME);
			session_start();
		}

		// Add classpath so we can find the TBG* classes
		TBGContext::addAutoloaderClassPath(THEBUGGENIE_CORE_PATH . 'classes' . DS);
		TBGContext::autoloadNamespace('thebuggenie', THEBUGGENIE_CORE_PATH . 'classes' . DS);
		TBGContext::autoloadNamespace('b2db', THEBUGGENIE_CORE_PATH . 'B2DB' . DS);

		TBGCache::checkEnabled();
		TBGLogging::log((TBGCache::isEnabled()) ? 'APC cache is enabled' : 'APC cache is not enabled');
		
		TBGLogging::log('Loading B2DB');
		try
		{
			TBGLogging::log('Adding B2DB classes to autoload path');
			define ('B2DB_BASEPATH', THEBUGGENIE_CORE_PATH . 'B2DB' . DS);
			define ('B2DB_CACHEPATH', THEBUGGENIE_CORE_PATH . 'cache' . DS . 'B2DB' . DS);
//			TBGContext::addAutoloaderClassPath(THEBUGGENIE_CORE_PATH . 'B2DB' . DS . 'classes' . DS);
			TBGLogging::log('...done (Adding B2DB classes to autoload path)');

			TBGLogging::log('Initializing B2DB');
			if (!isset($argc)) \b2db\Core::setHTMLException(true);
			\b2db\Core::initialize(THEBUGGENIE_CORE_PATH . 'b2db_bootstrap.inc.php');
			TBGLogging::log('...done (Initializing B2DB)');
			
			if (\b2db\Core::isInitialized())
			{
				TBGLogging::log('Database connection details found, connecting');
				\b2db\Core::doConnect();
				TBGLogging::log('...done (Database connection details found, connecting)');
				TBGLogging::log('Adding core table classpath to autoload path');
				TBGContext::addAutoloaderClassPath(THEBUGGENIE_CORE_PATH . 'classes' . DS . 'B2DB' . DS);
			}
			
		}
		catch (Exception $e)
		{
			TBGContext::exceptionHandler($e);
		}
		TBGLogging::log('...done');
		
		TBGLogging::log('Initializing context');
		TBGContext::initialize();
		TBGLogging::log('...done');
		
		//require THEBUGGENIE_CORE_PATH . 'common_functions.inc.php';
		require THEBUGGENIE_CORE_PATH . 'geshi/geshi.php';
		
		TBGLogging::log('Caspar framework loaded');
	}
	catch (Exception $e)
	{
		if (!isset($argc))
		{
			TBGContext::exceptionHandler($e);
			exit();
		}
		else
		{
			throw $e;
		}
	}
	
