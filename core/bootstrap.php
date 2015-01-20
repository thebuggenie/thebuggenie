<?php

    // This code requires PHP 5.3 or newer, so if we don't have it - don't continue
    if (PHP_VERSION_ID < 50300)
        die('This software requires PHP 5.3.0 or newer, but you have an older version. Please upgrade.');

    gc_enable();
    date_default_timezone_set('UTC');

    if (!defined('THEBUGGENIE_PATH'))
        die('You must define the THEBUGGENIE_PATH constant so we can find the files we need');

    defined('DS') || define('DS', DIRECTORY_SEPARATOR);

    defined('THEBUGGENIE_VENDOR_PATH') || define('THEBUGGENIE_VENDOR_PATH', THEBUGGENIE_PATH . 'vendor' . DS);
    if (!file_exists(THEBUGGENIE_VENDOR_PATH . 'autoload.php')) {
        include THEBUGGENIE_CORE_PATH . 'templates' . DS . 'composer.error.php';
        die();
    }
    require THEBUGGENIE_VENDOR_PATH . 'autoload.php';

    defined('THEBUGGENIE_CACHE_PATH') || define('THEBUGGENIE_CACHE_PATH', THEBUGGENIE_PATH . 'cache' . DS);
    defined('THEBUGGENIE_CONFIGURATION_PATH') || define('THEBUGGENIE_CONFIGURATION_PATH', THEBUGGENIE_CORE_PATH . 'config' . DS);
    defined('THEBUGGENIE_INTERNAL_MODULES_PATH') || define('THEBUGGENIE_INTERNAL_MODULES_PATH', THEBUGGENIE_CORE_PATH . 'modules' . DS);
    defined('THEBUGGENIE_MODULES_PATH') || define('THEBUGGENIE_MODULES_PATH', THEBUGGENIE_PATH . 'modules' . DS);

    \thebuggenie\core\framework\Context::initialize();
