<?php

	date_default_timezone_set('UTC');
	defined('DS') || define('DS', DIRECTORY_SEPARATOR);
	
	// The time the script was loaded
	$starttime = explode(' ', microtime());
	define('NOW', $starttime[1]);

	/**
	 * Displays a nicely formatted exception message
	 *  
	 * @param string $title
	 * @param Exception $exception
	 */
	function tbg_exception($title, $exception)
	{
		if (TBGContext::getRequest() instanceof TBGRequest && TBGContext::getRequest()->isAjaxCall())
		{
			TBGContext::getResponse()->ajaxResponseText(404, $title);
		}
		$ob_status = ob_get_status();
		if (!empty($ob_status) && $ob_status['status'] != PHP_OUTPUT_HANDLER_END)
		{
			ob_end_clean();
		}
		
		if (TBGContext::isCLI())
		{
			$trace_elements = null;
			if ($exception instanceof Exception)
			{
				if ($exception instanceof TBGActionNotFoundException)
				{
					TBGCliCommand::cli_echo("Could not find the specified action\n", 'white', 'bold');
				}
				elseif ($exception instanceof TBGTemplateNotFoundException)
				{
					TBGCliCommand::cli_echo("Could not find the template file for the specified action\n", 'white', 'bold');
				}
				elseif ($exception instanceof \b2db\Exception)
				{
					TBGCliCommand::cli_echo("An exception was thrown in the B2DB framework\n", 'white', 'bold');
				}
				else
				{
					TBGCliCommand::cli_echo("An unhandled exception occurred:\n", 'white', 'bold');
				}
				echo TBGCliCommand::cli_echo($exception->getMessage(), 'red', 'bold')."\n";
				echo "\n";
				TBGCliCommand::cli_echo('Stack trace').":\n";
				$trace_elements = $exception->getTrace();
			}
			else
			{
				if ($exception['code'] == 8)
				{
					TBGCliCommand::cli_echo('The following notice has stopped further execution:', 'white', 'bold');
				}
				else
				{
					TBGCliCommand::cli_echo('The following error occured:', 'white', 'bold');
				}
				echo "\n";
				echo "\n";
				TBGCliCommand::cli_echo($title, 'red', 'bold');
				echo "\n";
				TBGCliCommand::cli_echo("occured in\n");
				TBGCliCommand::cli_echo($exception['file'].', line '.$exception['line'], 'blue', 'bold');
				echo "\n";
				echo "\n";
				TBGCliCommand::cli_echo("Backtrace:\n", 'white', 'bold');
				$trace_elements = debug_backtrace();
			}
			foreach ($trace_elements as $trace_element)
			{
				if (array_key_exists('class', $trace_element))
				{
					TBGCliCommand::cli_echo($trace_element['class'].$trace_element['type'].$trace_element['function'].'()');
				}
				elseif (array_key_exists('function', $trace_element))
				{
					if (in_array($trace_element['function'], array('tbg_error_handler', 'tbg_exception'))) continue;
					TBGCliCommand::cli_echo($trace_element['function'].'()');
				}
				else
				{
					TBGCliCommand::cli_echo('unknown function');
				}
				echo "\n";
				if (array_key_exists('file', $trace_element))
				{
					TBGCliCommand::cli_echo($trace_element['file'].', line '.$trace_element['line'], 'blue', 'bold');
				}
				else
				{
					TBGCliCommand::cli_echo('unknown file', 'red', 'bold');
				}
				echo "\n";
			}
			if (class_exists('B2DB'))
			{
				echo "\n";
				TBGCliCommand::cli_echo("SQL queries:\n", 'white', 'bold');
				try
				{
					$cc = 1;
					foreach (\b2db\Core::getSQLHits() as $details)
					{
						TBGCliCommand::cli_echo("(".$cc++.") [");
						$str = ($details['time'] >= 1) ? round($details['time'], 2) . ' seconds' : round($details['time'] * 1000, 1) . 'ms';
						TBGCliCommand::cli_echo($str);
						TBGCliCommand::cli_echo("] from ");
						TBGCliCommand::cli_echo($details['filename'], 'blue');
						TBGCliCommand::cli_echo(", line ");
						TBGCliCommand::cli_echo($details['line'], 'white', 'bold');
						TBGCliCommand::cli_echo(":\n");
						TBGCliCommand::cli_echo("{$details['sql']}\n");
					}
					echo "\n";
				}
				catch (Exception $e)
				{
					TBGCliCommand::cli_echo("Could not generate query list (there may be no database connection)", "red", "bold");
				}
			}
			echo "\n";
			die();
		}

		echo "
		<!DOCTYPE html PUBLIC \"-//W3C//DTD HTML 4.01//EN\" \"http://www.w3.org/TR/html4/strict.dtd\">
		<html>
		<head>
		<style>
		body { background-color: #DFDFDF; font-family: \"Droid Sans\", \"Trebuchet MS\", \"Liberation Sans\", \"Nimbus Sans L\", \"Luxi Sans\", Verdana, sans-serif; font-size: 13px; }
		h1 { margin: 5px 0 0 0; font-size: 19px; }
		h2 { margin: 0 0 15px 0; font-size: 16px; }
		h3 { margin: 15px 0 0 0; font-size: 14px; }
		input[type=\"text\"], input[type=\"password\"] { float: left; margin-right: 15px; }
		label { float: left; font-weight: bold; margin-right: 5px; display: block; width: 150px; }
		label span { font-weight: normal; color: #888; }
		.rounded_box {background: transparent; margin:0px;}
		.rounded_box h4 { margin-bottom: 0px; margin-top: 7px; font-size: 14px; }
		.xtop, .xbottom {display:block; background:transparent; font-size:1px;}
		.xb1, .xb2, .xb3, .xb4 {display:block; overflow:hidden;}
		.xb1, .xb2, .xb3 {height:1px;}
		.xb2, .xb3, .xb4 {background:#F9F9F9; border-left:1px solid #CCC; border-right:1px solid #CCC;}
		.xb1 {margin:0 5px; background:#CCC;}
		.xb2 {margin:0 3px; border-width:0 2px;}
		.xb3 {margin:0 2px;}
		.xb4 {height:2px; margin:0 1px;}
		.xboxcontent {display:block; background:#F9F9F9; border:0 solid #CCC; border-width:0 1px; padding: 0 5px 0 5px;}
		.xboxcontent table td.description { padding: 3px 3px 3px 0;}
		.white .xb2, .white .xb3, .white .xb4 { background: #FFF; border-color: #CCC; }
		.white .xb1 { background: #CCC; }
		.white .xboxcontent { background: #FFF; border-color: #CCC; }
		pre { overflow: scroll; padding: 5px; }
		</style>
		<!--[if IE]>
		<style>
		body { background-color: #DFDFDF; font-family: sans-serif; font-size: 13px; }
		</style>
		<![endif]-->
		</head>
		<body>
		<div class=\"rounded_box white\" style=\"margin: 30px auto 0 auto; width: 700px;\">
			<b class=\"xtop\"><b class=\"xb1\"></b><b class=\"xb2\"></b><b class=\"xb3\"></b><b class=\"xb4\"></b></b>
			<div class=\"xboxcontent\" style=\"vertical-align: middle; padding: 10px 10px 10px 15px;\">
			<img style=\"float: left; margin-right: 10px;\" src=\"".TBGContext::getTBGPath()."header.png\"><h1>An error occured in The Bug Genie</h1>";
			echo "<h2>{$title}</h2>";
			$report_description = null;
			if ($exception instanceof Exception)
			{
				if ($exception instanceof TBGActionNotFoundException)
				{
					echo "<h3>Could not find the specified action</h3>";
					$report_description = "Could not find the specified action";
				}
				elseif ($exception instanceof TBGTemplateNotFoundException)
				{
					echo "<h3>Could not find the template file for the specified action</h3>";
					$report_description = "Could not find the template file for the specified action";
				}
				elseif ($exception instanceof \b2db\Exception)
				{
					echo "<h3>An exception was thrown in the B2DB framework</h3>";
					$report_description = "An exception was thrown in the B2DB framework";
				}
				else
				{
					echo "<h3>An unhandled exception occurred:</h3>";
					$report_description = "An unhandled exception occurred";
				}
				$report_description .= "\n" . $exception->getMessage();
				echo "<i>".$exception->getMessage()."</i><br>";
				if (class_exists("TBGContext") && TBGContext::isDebugMode())
				{
					echo "<h3>Stack trace:</h3>
					<ul>";
					//echo '<pre>';var_dump($exception->getTrace());die();
					foreach ($exception->getTrace() as $trace_element)
					{
						echo '<li>';
						if (array_key_exists('class', $trace_element))
						{
							echo '<strong>'.$trace_element['class'].$trace_element['type'].$trace_element['function'].'()</strong><br>';
						}
						elseif (array_key_exists('function', $trace_element))
						{
							if (!in_array($trace_element['function'], array('tbg_error_handler', 'tbg_exception')))
								echo '<strong>'.$trace_element['function'].'()</strong><br>';
						}
						else
						{
							echo '<strong>unknown function</strong><br>';
						}
						if (array_key_exists('file', $trace_element))
						{
							echo '<span style="color: #55F;">'.$trace_element['file'].'</span>, line '.$trace_element['line'];
						}
						else
						{
							echo '<span style="color: #C95;">unknown file</span>';
						}
						echo '</li>';
					}
					echo "</ul>";
				}
			}
			else
			{
				echo '<h3>';
				if ($exception['code'] == 8)
				{
					echo 'The following notice has stopped further execution:';
					$report_description = 'The following notice has stopped further execution: ';
				}
				else
				{
					echo 'The following error occured:';
					$report_description = 'The following error occured: ';
				}
				echo '</h3>';
				$report_description .= $title;
				echo "$title</i><br>
				<h3>Error information:</h3>
				<ul>
					<li>";
					echo '<span style="color: #55F;">'.$exception['file'].'</span>, line '.$exception['line'];
				echo "</li>
				</ul>";
				if (class_exists("TBGContext") && TBGContext::isDebugMode())
				{
					echo "<h3>Backtrace:</h3>
					<ol>";
					foreach (debug_backtrace() as $trace_element)
					{
						echo '<li>';
						if (array_key_exists('class', $trace_element))
						{
							echo '<strong>'.$trace_element['class'].$trace_element['type'].$trace_element['function'].'()</strong><br>';
						}
						elseif (array_key_exists('function', $trace_element))
						{
							if (in_array($trace_element['function'], array('tbg_error_handler', 'tbg_exception'))) continue;
							echo '<strong>'.$trace_element['function'].'()</strong><br>';
						}
						else
						{
							echo '<strong>unknown function</strong><br>';
						}
						if (array_key_exists('file', $trace_element))
						{
							echo '<span style="color: #55F;">'.$trace_element['file'].'</span>, line '.$trace_element['line'];
						}
						else
						{
							echo '<span style="color: #C95;">unknown file</span>';
						}
						echo '</li>';
					}
					echo "</ol>";
				}
			}
			if (class_exists("TBGContext") && TBGContext::isDebugMode())
			{
				echo "<h3>Log messages:</h3>";
				foreach (TBGLogging::getEntries() as $entry)
				{
					$color = TBGLogging::getCategoryColor($entry['category']);
					$lname = TBGLogging::getLevelName($entry['level']);
					echo "<div class=\"log_{$entry['category']}\"><strong>{$lname}</strong> <strong style=\"color: #{$color}\">[{$entry['category']}]</strong> <span style=\"color: #555; font-size: 10px; font-style: italic;\">{$entry['time']}</span>&nbsp;&nbsp;{$entry['message']}</div>";
				}
			}
			if (class_exists("B2DB") && TBGContext::isDebugMode())
			{
				echo "<h3>SQL queries:</h3>";
				try
				{
					echo "<ol>";
					foreach (\b2db\Core::getSQLHits() as $details)
					{
						echo "<li>
							<b>
							<span class=\"faded_out dark small\">[";
						echo ($details['time'] >= 1) ? round($details['time'], 2) . ' seconds' : round($details['time'] * 1000, 1) . 'ms'; 
						echo "]</span> </b> from <b>{$details['filename']}, line {$details['line']}</b>:<br>
							<span style=\"font-size: 12px;\">{$details['sql']}</span>
						</li>";
					}
					echo "</ol>";
				}
				catch (Exception $e)
				{
					echo '<span style="color: red;">Could not generate query list (there may be no database connection)</span>';
				}
			}
			echo "</div>
			<b class=\"xbottom\"><b class=\"xb4\"></b><b class=\"xb3\"></b><b class=\"xb2\"></b><b class=\"xb1\"></b></b>
		</div>";
		if (class_exists("TBGContext") && !TBGContext::isDebugMode())
		{
			echo "<div style=\"text-align: left; margin: 35px auto 0 auto; width: 700px; font-size: 13px;\">
				<div class=\"rounded_box white\" style=\"margin-bottom: 10px; text-align: right; color: #111;\">
					<b class=\"xtop\"><b class=\"xb1\"></b><b class=\"xb2\"></b><b class=\"xb3\"></b><b class=\"xb4\"></b></b>
					<div class=\"xboxcontent\">
						<div style=\"text-align: left;\">
							<h2 style=\"padding-top: 10px; margin-bottom: 5px;\">Reporting this issue</h2>
							Please report this error in the bug tracker by pressing the button below. This will file an automatic bug report and open it in a new window.<br><br>
							No login is required - but if you have a username and password entering it below will post the issue with your username, allowing you to follow its progress.
						</div>
						<br>
						<form action=\"http://thebuggenie.com/thebuggenie/thebuggenie/issues/new/bugreport\" target=\"_new\" method=\"post\">
							<label for=\"username\">Username <span>(optional)</span></label>
							<input type=\"text\" name=\"tbg3_username\" id=\"username\">
							<br style=\"clear: both;\">
							<label for=\"password\">Password <span>(optional)</span></label>
							<input type=\"password\" name=\"tbg3_password\" id=\"password\">
							<br>
							<input type=\"hidden\" name=\"category_id\" value=\"34\">
							<input type=\"hidden\" name=\"title\" value=\"".htmlentities($title)."\">
							<input type=\"hidden\" name=\"description\" value=\"".htmlentities($report_description)."\n\n\">";
							echo "<input type=\"hidden\" name=\"reproduction_steps\" value=\"PHP_SAPI: ".PHP_SAPI."<br>PHP_VERSION: ".PHP_VERSION."\n\n'''Backtrace''':<br>";
							if ($exception instanceof TBGException)
							{
								foreach ($exception->getTrace() as $trace_element)
								{
									if (array_key_exists('class', $trace_element))
									{
										echo "'''{$trace_element['class']}{$trace_element['type']}{$trace_element['function']}()'''\n";
									}
									elseif (array_key_exists('function', $trace_element))
									{
										if (in_array($trace_element['function'], array('tbg_error_handler', 'tbg_exception'))) continue;
										echo "'''{$trace_element['function']}()'''\n";
									}
									else
									{
										echo "'''unknown function'''\n";
									}
									if (array_key_exists('file', $trace_element))
									{
										echo 'in '.str_replace(THEBUGGENIE_PATH, '<installpath>/', $trace_element['file']).', line '.$trace_element['line'];
									}
									else
									{
										echo 'in an unknown file';
									}
									echo "<br>";
								}
							}
							else
							{
								foreach (debug_backtrace() as $trace_element)
								{
									if (array_key_exists('class', $trace_element))
									{
										echo "'''{$trace_element['class']}{$trace_element['type']}{$trace_element['function']}()'''\n";
									}
									elseif (array_key_exists('function', $trace_element))
									{
										if (in_array($trace_element['function'], array('tbg_error_handler', 'tbg_exception'))) continue;
										echo "'''{$trace_element['function']}()'''\n";
									}
									else
									{
										echo "'''unknown function'''\n";
									}
									if (array_key_exists('file', $trace_element))
									{
										echo 'in '.str_replace(THEBUGGENIE_PATH, '<installpath>/', $trace_element['file']).', line '.$trace_element['line'];
									}
									else
									{
										echo 'in an unknown file';
									}
									echo "<br>";
								}
							}
							echo "\n\n\">";
		echo "					
								<input type=\"submit\" value=\"Submit details for reporting\" style=\"font-size: 16px; font-weight: normal; padding: 5px; margin: 10px 0;\">
								<div style=\"font-size: 15px; font-weight: bold; padding: 0 5px 10px 0;\">Thank you for helping us improve The Bug Genie!</div>
							</form>
						</div>
						<b class=\"xbottom\"><b class=\"xb4\"></b><b class=\"xb3\"></b><b class=\"xb2\"></b><b class=\"xb1\"></b></b>
					</div>";
					if (TBGLogging::isEnabled())
					{
						echo "<h3 style=\"margin-top: 50px;\">Log messages (may contain useful information, but will not be submitted):</h3>";
						foreach (TBGLogging::getEntries() as $entry)
						{
							$color = TBGLogging::getCategoryColor($entry['category']);
							$lname = TBGLogging::getLevelName($entry['level']);
							echo "<div class=\"log_{$entry['category']}\"><strong>{$lname}</strong> <strong style=\"color: #{$color}\">[{$entry['category']}]</strong> <span style=\"color: #555; font-size: 10px; font-style: italic;\">{$entry['time']}</span>&nbsp;&nbsp;{$entry['message']}</div>";
						}
					}
		}
echo "
			</div>
		</body>
		</html>
		";
		die();
	}
	
	function tbg_error_handler($code, $error, $file, $line_number)
	{
		throw new Exception($error, $code);
		//tbg_exception($error, array('code' => $code, 'file' => $file, 'line' => $line_number));
	}
	
//	/**
//	 * Magic autoload function to make sure classes are autoloaded when used
//	 *
//	 * @param $classname
//	 */
//	function __autoload($classname)
//	{
//		foreach (TBGContext::getClasspaths() as $path)
//		{
//			if (file_exists($path . $classname . '.class.php'))
//			{
//				require $path . $classname . '.class.php';
//				break;
//			}
//		}
//	}

	
	// Set up error and exception handling
	set_error_handler('tbg_error_handler');
	set_exception_handler('tbg_exception');
	error_reporting(E_ALL | E_NOTICE | E_STRICT);
	
	if (!defined('THEBUGGENIE_PATH'))
		throw new Exception('You must define the THEBUGGENIE_PATH constant so we can find the files we need');

	// Load the context class, which controls most of things
	require THEBUGGENIE_CORE_PATH . 'classes' . DS . 'TBGContext.class.php';
	
	spl_autoload_register(array('TBGContext', 'autoload'));

	// Load the logging class so we can log stuff
	require THEBUGGENIE_CORE_PATH . 'classes' . DS . 'TBGLogging.class.php';

	// This code requires PHP 5.3 or newer, so if we don't have it - complain
	if (PHP_VERSION_ID < 50300)
	{
		$e = new Exception('This software requires PHP 5.3.0 or newer, but you have an older version. Please upgrade');
		return tbg_exception('Startup error', $e);
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
			tbg_exception('Could not load and initiate the B2DB subsystem', $e);
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
			tbg_exception('Exception caught', $e);
			exit();
		}
		else
		{
			throw $e;
		}
	}
	
