<?php

    // Set the path to The Bug Genie top folder
    $path = realpath(getcwd());
    defined('DS') || define('DS', DIRECTORY_SEPARATOR);
    defined('THEBUGGENIE_SESSION_NAME') || define('THEBUGGENIE_SESSION_NAME', 'THEBUGGENIE');

    // Default behaviour: define the path to thebuggenie as one folder up from this
    defined('THEBUGGENIE_PATH') || define('THEBUGGENIE_PATH', realpath(getcwd() . DS . '..' . DS) . DS);

    // Default behaviour: define the public folder name to "public" (actually autodetect name of current folder)
    defined('THEBUGGENIE_PUBLIC_FOLDER_NAME') || define('THEBUGGENIE_PUBLIC_FOLDER_NAME', substr($path, strrpos($path, DS) + 1));

    // Root installation: http://issues.thebuggenie.com/wiki/TheBugGenie:HowTo:RootDirectoryInstallation
    // ----
    // Don't look one directory up to find the path to the bug genie
    // defined('THEBUGGENIE_PATH') || define('THEBUGGENIE_PATH', realpath(getcwd() . DS) . DS);
    // Don't autodetect the subfolder, but use "" instead, since there is none
    // defined('THEBUGGENIE_PUBLIC_FOLDER_NAME') || define('THEBUGGENIE_PUBLIC_FOLDER_NAME', '');
    // ----

    // Include the "engine" script, which initializes and sets up stuff
    defined('THEBUGGENIE_CORE_PATH') || define('THEBUGGENIE_CORE_PATH', THEBUGGENIE_PATH . 'core' . DS);
    require THEBUGGENIE_CORE_PATH . 'bootstrap.php';

    // Trigger the framework's start function
    if (\thebuggenie\core\framework\Context::isInitialized()) \thebuggenie\core\framework\Context::go();
