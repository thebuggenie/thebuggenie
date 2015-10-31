<?php

    define('DS', DIRECTORY_SEPARATOR);
    define('THEBUGGENIE_PATH', realpath(dirname(__FILE__) . DS . '..' . DS) . DS);
    define('THEBUGGENIE_CORE_PATH', THEBUGGENIE_PATH . 'core' . DS);
    define('THEBUGGENIE_VENDOR_PATH', THEBUGGENIE_PATH . 'vendor' . DS);

    gc_enable();
    date_default_timezone_set('UTC');

    define('THEBUGGENIE_CACHE_PATH', THEBUGGENIE_PATH . 'cache' . DS);
    define('THEBUGGENIE_CONFIGURATION_PATH', THEBUGGENIE_CORE_PATH . 'config' . DS);
    define('THEBUGGENIE_INTERNAL_MODULES_PATH', THEBUGGENIE_CORE_PATH . 'modules' . DS);
    define('THEBUGGENIE_MODULES_PATH', THEBUGGENIE_PATH . 'modules' . DS);

    require_once THEBUGGENIE_PATH . 'tests' . DS . 'b2dbmock.php';
