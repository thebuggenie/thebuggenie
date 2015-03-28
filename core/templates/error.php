<!DOCTYPE html>
<html>
    <head>
        <style>
            @import url("http://fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,400,700&subset=latin,cyrillic,greek");
            @import url("http://fonts.googleapis.com/css?family=Droid+Sans+Mono&subset=latin,cyrillic,greek");

            body, td, th { padding: 0px; margin: 0px; background-color: #FFF; font-family: 'Open Sans', sans-serif; font-style: normal; font-weight: normal; text-align: left; font-size: 13px; line-height: 1.3; color: #222;}
            h1 { margin: 0; font-size: 19px; padding: 10px;
                background: -moz-linear-gradient(top, #FFFFFF 0%, #F1F1F1 100%); /* FF3.6+ */
                background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#FFFFFF), color-stop(100%,#F1F1F1)); /* Chrome,Safari4+ */
                background: -webkit-linear-gradient(top, #FFFFFF 0%,#F1F1F1 100%); /* Chrome10+,Safari5.1+ */
                background: -o-linear-gradient(top, #FFFFFF 0%,#F1F1F1 100%); /* Opera11.10+ */
                background: -ms-linear-gradient(top, #FFFFFF 0%,#F1F1F1 100%); /* IE10+ */
                filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#FFFFFF', endColorstr='#F1F1F1',GradientType=0 ); /* IE6-9 */
                background: linear-gradient(top, #FFFFFF 0%,#F1F1F1 100%); /* W3C */
                border-bottom: 1px solid #CCC;
                border-top-left-radius: 5px;
                border-top-right-radius: 5px;
            }
            h2 { margin: 0 0 15px 0; font-size: 16px; }
            h3 { margin: 15px 0 0 0; font-size: 14px; }
            input[type="text"], input[type="password"] { float: left; margin-right: 15px; }
            label { float: left; font-weight: bold; margin-right: 5px; display: block; width: 150px; }
            label span { font-weight: normal; color: #888; }
            .rounded_box {background: transparent; margin:0px; border-radius: 5px; border: 1px solid #CCC; box-shadow: 0 0 3px rgba(0, 0, 0, 0.3); padding: 0; }
            .rounded_box h4 { margin-bottom: 0px; margin-top: 7px; font-size: 14px; }
            .error_content { padding: 10px; }
            .description { padding: 3px 3px 3px 0;}
            pre { overflow: scroll; padding: 5px; }
            .command_box { border: 1px dashed #DDD; background-color: #F5F5F5; padding: 4px; font-family: 'Droid Sans Mono', monospace; display: inline-block; margin: 0 5px; }
        </style>
        <!--[if IE]>
            <style>
                body { background-color: #DFDFDF; font-family: sans-serif; font-size: 13px; }
            </style>
        <![endif]-->
    </head>
    <body>
        <div class="rounded_box" style="margin: 30px auto 0 auto; width: 700px;">
            <img style="float: left; margin: 10px;" src="<?php echo \thebuggenie\core\framework\Context::getWebroot(); ?>header.png"><h1>An error occurred in <?php echo \thebuggenie\core\framework\Settings::getSiteHeaderName(); ?></h1>
            <div class="error_content">
                <?php if (isset($exception) && $exception instanceof Exception): ?>
                    <h2><?php echo nl2br($exception->getMessage()); ?></h2>
                    <?php if ($exception instanceof \thebuggenie\core\framework\exceptions\ActionNotFoundException): ?>
                        <h3>Could not find the specified action</h3>
                    <?php elseif ($exception instanceof \thebuggenie\core\framework\exceptions\TemplateNotFoundException): ?>
                        <h3>Could not find the template file for the specified action</h3>
                    <?php elseif ($exception instanceof \thebuggenie\core\framework\exceptions\ConfigurationException): ?>
                        <?php if ($exception->getCode() == \thebuggenie\core\framework\exceptions\ConfigurationException::NO_VERSION_INFO): ?>
                            The version information file <span class="command_box"><?php echo THEBUGGENIE_PATH; ?>installed</span> is present, but file is empty.<br>
                            This file is generated during installation, so this error should not occur.<br>
                            <br>
                            Please reinstall The Bug Genie or file a bug report if you think this is an error.
                        <?php elseif ($exception->getCode() == \thebuggenie\core\framework\exceptions\ConfigurationException::UPGRADE_FILE_MISSING): ?>
                            To enable the upgrade mode, make sure the file <span class="command_box"><?php echo THEBUGGENIE_PATH; ?>upgrade</span> is present<br>
                            Please see the upgrade instructions here: <a href='http://issues.thebuggenie.com/wiki/TheBugGenie%3AFAQ'>thebuggenie.com &raquo; wiki &raquo; FAQ</a> for more information.
                        <?php elseif ($exception->getCode() == \thebuggenie\core\framework\exceptions\ConfigurationException::UPGRADE_REQUIRED): ?>
                            You need to upgrade to this version of The Bug Genie before you can continue.<br>
                            Please see the upgrade instructions here: <a href='http://issues.thebuggenie.com/wiki/TheBugGenie%3AFAQ'>thebuggenie.com &raquo; wiki &raquo; FAQ</a> for more information.
                        <?php elseif ($exception->getCode() == \thebuggenie\core\framework\exceptions\ConfigurationException::NO_B2DB_CONFIGURATION): ?>
                            The database configuration file <span class="command_box"><?php echo THEBUGGENIE_CONFIGURATION_PATH; ?>b2db.yml</span> could not be read.<br>
                            This file is generated during installation, so this error should not occur.<br>
                            <br>
                            Please reinstall The Bug Genie or file a bug report if you think this is an error.
                        <?php else: ?>
                            There is an issue with the configuration. Please see the message above.
                        <?php endif; ?>
                        <br>
                        <br>
                    <?php elseif ($exception instanceof \thebuggenie\core\framework\exceptions\CacheException): ?>
                        <p>
                            <?php if ($exception->getCode() == \thebuggenie\core\framework\exceptions\CacheException::NO_FOLDER): ?>
                                The cache folder <span class="command_box"><?php echo THEBUGGENIE_CACHE_PATH; ?></span> does not exist.<br>
                                Make sure the folder exists and is writable by your web server, then try again.<br>
                            <?php elseif ($exception->getCode() == \thebuggenie\core\framework\exceptions\CacheException::NOT_WRITABLE): ?>
                                Trying to write to the cache folder <span class="command_box"><?php echo THEBUGGENIE_CACHE_PATH; ?></span> failed.<br>
                                Make sure the folder is writable by your web server, then try again.<br>
                            <?php else: ?>
                                A caching error occured.
                            <?php endif; ?>
                        </p>
                    <?php elseif ($exception instanceof \b2db\Exception): ?>
                        <h3>An exception was thrown in the B2DB framework</h3>
                    <?php else: ?>
                        <h3>An unhandled exception occurred:</h3>
                    <?php endif; ?>
                    <?php if (class_exists('\thebuggenie\core\framework\Context') && \thebuggenie\core\framework\Context::isDebugMode()): ?>
                        <span style="color: #55F;"><?php echo $exception->getFile(); ?></span>, line <b><?php echo $exception->getLine(); ?></b>:<br>
                        <i><?php echo $exception->getMessage(); ?></i>
                    <?php endif; ?>
                <?php else: ?>
                    <h2><?php echo nl2br($error); ?></h2>
                    <?php if ($code == 8): ?>
                        <h3>The following notice has stopped further execution:</h3>
                    <?php else: ?>
                        <h3>The following error occured:</h3>
                    <?php endif; ?>
                    <i><?php echo $error; ?></i> in <span style="color: #55F;"><?php echo $file; ?></span>, line <?php echo $line; ?>
                <?php endif; ?>
                <br>
                <?php if (isset($exception) && $exception instanceof \b2db\Exception): ?>
                    <h3>SQL:</h3>
                    <?php echo $exception->getSQL(); ?>
                <?php endif; ?>
                <?php if (class_exists('\thebuggenie\core\framework\Context') && \thebuggenie\core\framework\Context::isDebugMode() && (!isset($exception) || !$exception instanceof \thebuggenie\core\framework\exceptions\ComposerException)): ?>
                    <h3>Stack trace:</h3>
                    <ul>
                        <?php $trace = (isset($exception)) ? $exception->getTrace() : debug_backtrace(); ?>
                        <?php foreach ($trace as $trace_element): ?>
                            <?php if (array_key_exists('class', $trace_element) && $trace_element['class'] == 'thebuggenie\core\framework\Context' && array_key_exists('function', $trace_element) && $trace_element['function'] == 'errorHandler') continue; ?>
                            <li>
                            <?php if (array_key_exists('class', $trace_element)): ?>
                                <strong><?php echo $trace_element['class'].$trace_element['type'].$trace_element['function']; ?>()</strong>
                            <?php elseif (array_key_exists('function', $trace_element)): ?>
                                <strong><?php echo $trace_element['function']; ?>()</strong>
                            <?php else: ?>
                                <strong>unknown function</strong>
                            <?php endif; ?>
                            <br>
                            <?php if (array_key_exists('file', $trace_element)): ?>
                                <span style="color: #55F;"><?php echo $trace_element['file']; ?></span>, line <?php echo $trace_element['line']; ?>
                            <?php else: ?>
                                <span style="color: #C95;">unknown file</span>
                            <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
                <?php if (class_exists('\thebuggenie\core\framework\Context') && class_exists("\thebuggenie\core\framework\Logging") && \thebuggenie\core\framework\Context::isDebugMode() && (!isset($exception) || !$exception instanceof \thebuggenie\core\framework\exceptions\ComposerException)): ?>
                    <h3>Log messages:</h3>
                    <?php foreach (\thebuggenie\core\framework\Logging::getEntries() as $entry): ?>
                        <?php $color = \thebuggenie\core\framework\Logging::getCategoryColor($entry['category']); ?>
                        <?php $lname = \thebuggenie\core\framework\Logging::getLevelName($entry['level']); ?>
                        <div class="log_<?php echo $entry['category']; ?>"><strong><?php echo $lname; ?></strong> <strong style="color: #<?php echo $color; ?>">[<?php echo $entry['category']; ?>]</strong> <span style="color: #555; font-size: 10px; font-style: italic;"><?php echo $entry['time']; ?></span>&nbsp;&nbsp;<?php echo $entry['message']; ?></div>
                    <?php endforeach; ?>
                <?php endif; ?>
                <?php if (class_exists("\b2db\Core") && \thebuggenie\core\framework\Context::isDebugMode() && (!isset($exception) || !$exception instanceof \thebuggenie\core\framework\exceptions\ComposerException)): ?>
                    <h3>SQL queries:</h3>
                        <ol>
                        <?php foreach (\b2db\Core::getSQLHits() as $details): ?>
                            <li>
                                <span class="faded_out dark small"><b>[<?php echo ($details['time'] >= 1) ? round($details['time'], 2) . ' seconds' : round($details['time'] * 1000, 1) . 'ms'; ?>]</b></span>
                                from <b><?php echo $details['filename']; ?>, line <?php echo $details['line']; ?></b>:<br>
                                <span style="font-size: 12px;"><?php echo $details['sql']; ?></span>
                            </li>
                        <?php endforeach; ?>
                        </ol>
                <?php endif; ?>
            </div>
        </div>
    </body>
</html>
