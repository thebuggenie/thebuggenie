<!DOCTYPE html>
<html>
    <head>
        <style>
            @import url('https://fonts.googleapis.com/css?family=Fira+Mono:400,500,700|Source+Sans+Pro:400,400i,600,600i&subset=cyrillic,cyrillic-ext,latin-ext');

            body, td, th { padding: 0; margin: 0; background-color: #FFF; font-family: 'Source Sans Pro', 'Open Sans', sans-serif; font-style: normal; font-weight: normal; text-align: left; font-size: 14px; line-height: 1.3; color: #222;}
            h1 {
                margin: 0 10px;
                font-size: 1.6em;
                padding: 10px 0;
                background: transparent;
                border-bottom: 1px solid rgba(0, 0, 0, 0.1);
                border-top-left-radius: 2px;
                border-top-right-radius: 2px;
                font-weight: normal;
                text-transform: uppercase;
                vertical-align: middle;
            }
            h1 img {
                display: inline-block;
                margin-right: 10px;
                vertical-align: middle;
                height: 24px;
            }
            h1 span {
                vertical-align: middle;
            }
            h2 { margin: 0 0 15px 0; font-size: 1.3em; }
            h3 { margin: 15px 0 0 0; font-size: 1.1em; }
            input[type="text"], input[type="password"] { float: left; margin-right: 15px; }
            label { float: left; font-weight: 600; margin-right: 5px; display: block; width: 150px; }
            label span { font-weight: normal; color: #888; }
            .rounded_box {
                background: transparent;
                margin: 0;
                border-radius: 2px;
                border: 1px solid rgba(0, 0, 0, 0.1);
                box-shadow: 0 0 8px rgba(0, 0, 0, 0.2);
                padding: 0;
            }
            .rounded_box h4 { margin-bottom: 0; margin-top: 7px; font-size: 14px; }
            .error_content { padding: 10px; }
            .description { padding: 3px 3px 3px 0;}
            pre { overflow: scroll; padding: 5px; }
            .command_box { border: 1px dashed #DDD; background-color: #F5F5F5; padding: 4px; font-family: 'Fira Mono', monospace; font-size: 0.9em; display: inline-block; margin: 0 5px; }
            .stacktrace { font-family: 'Fira Mono', monospace; font-size: 0.9em; }
            .filename { color: #55F; font-size: 0.9em; font-family: 'Fira Mono', monospace; }
            .exception-message { font-size: 1em; font-family: 'Fira Mono', monospace; }
        </style>
        <!--[if IE]>
            <style>
                body { background-color: #DFDFDF; font-family: sans-serif; font-size: 13px; }
            </style>
        <![endif]-->
    </head>
    <body>
        <div class="rounded_box" style="margin: 30px auto 0 auto; width: 700px;">
            <h1><img src="<?= \thebuggenie\core\framework\Context::getWebroot(); ?>images/logo_48.png"><span>An error occurred in <?= \thebuggenie\core\framework\Settings::getSiteHeaderName(); ?></span></h1>
            <div class="error_content">
                <?php if (isset($exception)): ?>
                    <h2><?= nl2br($exception->getMessage()); ?></h2>
                    <?php if ($exception instanceof \thebuggenie\core\framework\exceptions\ActionNotFoundException): ?>
                        <h3>Could not find the specified action</h3>
                    <?php elseif ($exception instanceof \thebuggenie\core\framework\exceptions\TemplateNotFoundException): ?>
                        <h3>Could not find the template file for the specified action</h3>
                    <?php elseif ($exception instanceof \thebuggenie\core\framework\exceptions\ConfigurationException): ?>
                        <?php if ($exception->getCode() == \thebuggenie\core\framework\exceptions\ConfigurationException::NO_VERSION_INFO): ?>
                            The version information file <span class="command_box"><?= THEBUGGENIE_PATH; ?>installed</span> is present, but file is empty.<br>
                            This file is generated during installation, so this error should not occur.<br>
                            <br>
                            Please reinstall The Bug Genie or file a bug report if you think this is an error.
                        <?php elseif ($exception->getCode() == \thebuggenie\core\framework\exceptions\ConfigurationException::UPGRADE_FILE_MISSING): ?>
                            To enable the upgrade mode, make sure the file <span class="command_box"><?= THEBUGGENIE_PATH; ?>upgrade</span> is present<br>
                            Please see the upgrade instructions here: <a href='https://issues.thebuggenie.com/wiki/TheBugGenie%3AFAQ'>thebuggenie.com &raquo; wiki &raquo; FAQ</a> for more information.
                        <?php elseif ($exception->getCode() == \thebuggenie\core\framework\exceptions\ConfigurationException::UPGRADE_REQUIRED): ?>
                            You need to upgrade to this version of The Bug Genie before you can continue.<br>
                            Please see the upgrade instructions here: <a href='https://issues.thebuggenie.com/wiki/TheBugGenie%3AFAQ'>thebuggenie.com &raquo; wiki &raquo; FAQ</a> for more information.
                        <?php elseif ($exception->getCode() == \thebuggenie\core\framework\exceptions\ConfigurationException::NO_B2DB_CONFIGURATION): ?>
                            The database configuration file <span class="command_box"><?= THEBUGGENIE_CONFIGURATION_PATH; ?>b2db.yml</span> could not be read.<br>
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
                                The cache folder <span class="command_box"><?= THEBUGGENIE_CACHE_PATH; ?></span> does not exist.<br>
                                Make sure the folder exists and is writable by your web server, then try again.<br>
                                <br>
                                Running the following command may resolve this issue:<div class="command_box">mkdir -p <?= THEBUGGENIE_CACHE_PATH; ?></div>
                            <?php elseif ($exception->getCode() == \thebuggenie\core\framework\exceptions\CacheException::NOT_WRITABLE): ?>
                                Trying to write to the cache folder <span class="command_box"><?= THEBUGGENIE_CACHE_PATH; ?></span> failed.<br>
                                Make sure the folder is writable by your web server, then try again.<br>
                                <br>
                                Running the following command may resolve this issue:
                                <div class="command_box">chown &lt;web_server_user&gt;:&lt;web_server_user&gt; <?= THEBUGGENIE_CACHE_PATH; ?></div> (where &lt;web_server_user&gt; is the user and group your web server is running at).
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
                        <span class="filename"><?= $exception->getFile(); ?></span>, line <b><?= $exception->getLine(); ?></b>:<br>
                        <span class="exception-message"><?= $exception->getMessage(); ?></span>
                    <?php endif; ?>
                <?php else: ?>
                    <h2><?= nl2br($error); ?></h2>
                    <?php if ($code == 8): ?>
                        <h3>The following notice has stopped further execution:</h3>
                    <?php else: ?>
                        <h3>The following error occured:</h3>
                    <?php endif; ?>
                    <span class="exception-message"><?= $error; ?></span> in <span class="filename"><?= $file; ?></span>, line <?= $line; ?>
                <?php endif; ?>
                <br>
                <?php if (isset($exception) && $exception instanceof \b2db\Exception): ?>
                    <h3>SQL:</h3>
                    <?= $exception->getSQL(); ?>
                <?php endif; ?>
                <?php if (class_exists('\thebuggenie\core\framework\Context') && \thebuggenie\core\framework\Context::isDebugMode() && (!isset($exception) || (!$exception instanceof \thebuggenie\core\framework\exceptions\ComposerException && !$exception instanceof \thebuggenie\core\framework\exceptions\CacheException))): ?>
                    <h3>Stack trace:</h3>
                    <ul class="stacktrace">
                        <?php $trace = (isset($exception)) ? $exception->getTrace() : debug_backtrace(); ?>
                        <?php foreach ($trace as $trace_element): ?>
                            <?php if (array_key_exists('class', $trace_element) && $trace_element['class'] == 'thebuggenie\core\framework\Context' && array_key_exists('function', $trace_element) && $trace_element['function'] == 'errorHandler') continue; ?>
                            <li>
                            <?php if (array_key_exists('class', $trace_element)): ?>
                                <strong><?= $trace_element['class'].$trace_element['type'].$trace_element['function']; ?>()</strong>
                            <?php elseif (array_key_exists('function', $trace_element)): ?>
                                <strong><?= $trace_element['function']; ?>()</strong>
                            <?php else: ?>
                                <strong>unknown function</strong>
                            <?php endif; ?>
                            <br>
                            <?php if (array_key_exists('file', $trace_element)): ?>
                                <span class="filename"><?= $trace_element['file']; ?></span>, line <?= $trace_element['line']; ?>
                            <?php else: ?>
                                <span style="color: #C95;">unknown file</span>
                            <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
                <?php if (class_exists('\thebuggenie\core\framework\Context') && class_exists('\thebuggenie\core\framework\Logging') && \thebuggenie\core\framework\Context::isDebugMode() && (!isset($exception) || (!$exception instanceof \thebuggenie\core\framework\exceptions\ComposerException && !$exception instanceof \thebuggenie\core\framework\exceptions\CacheException))): ?>
                    <h3>Log messages:</h3>
                    <?php foreach (\thebuggenie\core\framework\Logging::getEntries() as $entry): ?>
                        <?php $color = \thebuggenie\core\framework\Logging::getCategoryColor($entry['category']); ?>
                        <?php $lname = \thebuggenie\core\framework\Logging::getLevelName($entry['level']); ?>
                        <div class="log_<?= $entry['category']; ?>"><strong style="font-size: 0.9em; font-family: 'Fira Mono', monospace;"><?= $lname; ?></strong> <strong style="color: #<?= $color; ?>; font-size: 0.9em; font-family: 'Fira Mono', monospace;">[<?= $entry['category']; ?>]</strong> <span style="color: #555; font-size: 0.8em; font-family: 'Fira Mono', monospace;"><?= $entry['time']; ?></span>&nbsp;&nbsp;<?= $entry['message']; ?></div>
                    <?php endforeach; ?>
                <?php endif; ?>
                <?php if (class_exists('\b2db\Core') && \thebuggenie\core\framework\Context::isDebugMode() && (!isset($exception) || !$exception instanceof \thebuggenie\core\framework\exceptions\ComposerException)): ?>
                    <?php if (count(\b2db\Core::getSQLHits())): ?>
                        <h3>SQL queries:</h3>
                        <ol>
                        <?php foreach (\b2db\Core::getSQLHits() as $details): ?>
                            <li>
                                <span class="faded_out dark small"><b>[<?= ($details['time'] >= 1) ? round($details['time'], 2) . ' seconds' : round($details['time'] * 1000, 1) . 'ms'; ?>]</b></span>
                                from <b><?= $details['filename']; ?>, line <?= $details['line']; ?></b>:<br>
                                <span style="font-size: 12px;"><?= $details['sql']; ?></span>
                            </li>
                        <?php endforeach; ?>
                        </ol>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </body>
</html>
