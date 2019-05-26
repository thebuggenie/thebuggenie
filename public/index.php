<?php

    // Set the path to The Bug Genie top folder
    $path = realpath(getcwd());
    defined('THEBUGGENIE_SESSION_NAME') || define('THEBUGGENIE_SESSION_NAME', 'THEBUGGENIE');

    // Default behaviour: define the path to thebuggenie as one folder up from this
    defined('THEBUGGENIE_PATH') || define('THEBUGGENIE_PATH', realpath(getcwd() . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR);

    // Default behaviour: define the public folder name to "public" (actually autodetect name of current folder)
    defined('THEBUGGENIE_PUBLIC_FOLDER_NAME') || define('THEBUGGENIE_PUBLIC_FOLDER_NAME', substr($path, strrpos($path, DIRECTORY_SEPARATOR) + 1));

    // Root installation: https://issues.thebuggenie.com/wiki/TheBugGenie:HowTo:RootDirectoryInstallation
    // ----
    // Don't look one directory up to find the path to the bug genie
    // defined('THEBUGGENIE_PATH') || define('THEBUGGENIE_PATH', realpath(getcwd() . DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR);
    // Don't autodetect the subfolder, but use "" instead, since there is none
    // defined('THEBUGGENIE_PUBLIC_FOLDER_NAME') || define('THEBUGGENIE_PUBLIC_FOLDER_NAME', '');
    // ----

    // Include the "engine" script, which initializes and sets up stuff
    if (!file_exists(THEBUGGENIE_PATH . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php')) {
        include THEBUGGENIE_PATH . 'core' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'composer.error.php';
        die();
    }
    require THEBUGGENIE_PATH . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

    \thebuggenie\core\framework\Context::bootstrap();
    \thebuggenie\core\framework\Context::go();
