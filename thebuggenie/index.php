<?php

	define ('BUGS2_INCLUDE_PATH', realpath(getcwd() . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR);
	
	require BUGS2_INCLUDE_PATH . 'core/tbg_engine.inc.php';
	BUGScontext::go();
