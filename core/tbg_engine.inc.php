<?php

	$starttime = explode(' ', microtime());
	error_reporting(E_ALL | E_STRICT);

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
		
		if (TBGContext::getEnvironment() == TBGContext::ENV_CLI)
		{
			$trace_elements = null;
			if ($exception instanceof Exception)
			{
				if ($exception instanceof TBGActionNotFoundException)
				{
					echo format_text("Could not find the specified action", 'white', 'bold');
				}
				elseif ($exception instanceof TBGTemplateNotFoundException)
				{
					echo format_text("Could not find the template file for the specified action", 'white', 'bold');
				}
				elseif ($exception instanceof B2DBException)
				{
					echo format_text("An exception was thrown in the B2DB framework", 'white', 'bold');
				}
				else
				{
					echo format_text("An unhandled exception occurred:", 'white', 'bold');
				}
				echo "\n".format_text($exception->getMessage(), 'red', 'bold')."\n";
				echo "\n";
				echo format_text('Stack trace').":\n";
				$trace_elements = $exception->getTrace();
			}
			else
			{
				if ($exception['code'] == 8)
				{
					echo format_text('The following notice has stopped further execution:', 'white', 'bold');
				}
				else
				{
					echo format_text('The following error occured:', 'white', 'bold');
				}
				echo "\n";
				echo "\n";
				echo format_text($title, 'red', 'bold');
				echo "\n";
				echo format_text('occured in')."\n";
				echo format_text($exception['file'].', line '.$exception['line'], 'blue', 'bold');
				echo "\n";
				echo "\n";
				echo format_text("Backtrace:", 'white', 'bold')."\n";
				$trace_elements = debug_backtrace();
			}
			foreach ($trace_elements as $trace_element)
			{
				if (array_key_exists('class', $trace_element))
				{
					echo format_text($trace_element['class'].$trace_element['type'].$trace_element['function'].'()');
				}
				elseif (array_key_exists('function', $trace_element))
				{
					echo format_text($trace_element['function'].'()');
				}
				else
				{
					echo format_text('unknown function');
				}
				echo "\n";
				if (array_key_exists('file', $trace_element))
				{
					echo format_text($trace_element['file'].', line '.$trace_element['line'], 'blue', 'bold');
				}
				else
				{
					echo format_text('unknown file', 'red', 'bold');
				}
				echo "\n";
			}
			echo "\n";
			die();
		}

		echo "
		<style>
		body { background-color: #DFDFDF; font-family: \"Droid Sans\", \"Trebuchet MS\", \"Liberation Sans\", \"Nimbus Sans L\", \"Luxi Sans\", Verdana, sans-serif; font-size: 13px; }
		h1 { margin: 5px 0 15px 0; font-size: 18px; }
		h2 { margin: 15px 0 0 0; font-size: 15px; }
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
		<div class=\"rounded_box white\" style=\"margin: 30px auto 0 auto; width: 700px;\">
			<b class=\"xtop\"><b class=\"xb1\"></b><b class=\"xb2\"></b><b class=\"xb3\"></b><b class=\"xb4\"></b></b>
			<div class=\"xboxcontent\" style=\"vertical-align: middle; padding: 10px 10px 10px 15px;\">
			<img style=\"float: left; margin-right: 10px;\" src=\"".TBGContext::getTBGPath()."messagebox_warning.png\"><h1>{$title}</h1>";
			if ($exception instanceof Exception)
			{
				if ($exception instanceof TBGActionNotFoundException)
				{
					echo "<h2>Could not find the specified action</h2>";
				}
				elseif ($exception instanceof TBGTemplateNotFoundException)
				{
					echo "<h2>Could not find the template file for the specified action</h2>";
				}
				elseif ($exception instanceof B2DBException)
				{
					echo "<h2>An exception was thrown in the B2DB framework</h2>";
				}
				else
				{
					echo "<h2>An unhandled exception occurred:</h2>";
				}
				echo "<i>".$exception->getMessage()."</i><br>
				<h2>Stack trace:</h2>
				<ul>";
				foreach ($exception->getTrace() as $trace_element)
				{
					echo '<li>';
					if (array_key_exists('class', $trace_element))
					{
						echo '<strong>'.$trace_element['class'].$trace_element['type'].$trace_element['function'].'()</strong><br>';
					}
					elseif (array_key_exists('function', $trace_element))
					{
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
			else
			{
				echo '<h2>';
				if ($exception['code'] == 8)
				{
					echo 'The following notice has stopped further execution:';
				}
				else
				{
					echo 'The following error occured:';
				}
				echo '</h2>';
				echo "$title</i><br>
				<h2>Error information:</h2>
				<ul>
					<li>";
					echo '<span style="color: #55F;">'.$exception['file'].'</span>, line '.$exception['line'];
				echo "</li>
				</ul>";
				echo "<h2>Backtrace:</h2>
				<ol>";
				foreach (debug_backtrace() as $trace_element)
				{
					echo '<li>';
					if (array_key_exists('class', $trace_element))
					{
						echo '<strong>'.$trace_element['class'].$trace_element['function'].'()</strong><br>';
					}
					elseif (array_key_exists('function', $trace_element))
					{
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
			echo "<h2>Log messages:</h2>";
			foreach (TBGLogging::getEntries() as $entry)
			{
				$color = TBGLogging::getCategoryColor($entry['category']);
				$lname = TBGLogging::getLevelName($entry['level']);
				echo "<div class=\"log_{$entry['category']}\"><strong>{$lname}</strong> <strong style=\"color: #{$color}\">[{$entry['category']}]</strong> <span style=\"color: #555; font-size: 10px; font-style: italic;\">{$entry['time']}</span>&nbsp;&nbsp;{$entry['message']}</div>";
			}
			echo "</div>
			<b class=\"xbottom\"><b class=\"xb4\"></b><b class=\"xb3\"></b><b class=\"xb2\"></b><b class=\"xb1\"></b></b>
		</div>
		";
		die();
	}
	
	function b2_error_handler($code, $error, $file, $line_number)
	{
		if ($code == 2 && strpos(basename($file), 'Services_Yadis_') !== false) return;
		tbg_exception($error, array('code' => $code, 'file' => $file, 'line' => $line_number));
	}
	
	set_error_handler('b2_error_handler');

	if (!defined('THEBUGGENIE_PATH'))
	{
		tbg_msgbox(true, 'THEBUGGENIE_PATH not defined', 'You must define the THEBUGGENIE_PATH constant so we can find the files we need');
	}

	session_name("THEBUGGENIE");
	session_start();

	/**
	 * Magic autoload function to make sure classes are autoloaded when used
	 * 
	 * @param $classname
	 */
	function __autoload($classname)
	{
		$classes = TBGContext::getClasspaths();
		
		if (isset($classes[$classname]))
		{
			require $classes[$classname];
		}
	}	

	// Start loading The Bug Genie
	try
	{
		// Set the default timezone
		date_default_timezone_set('Europe/London');
		
		// Load the context class, which controls most of things
		require THEBUGGENIE_PATH . 'core/classes/TBGContext.class.php';

		// Load the logging class so we can log stuff
		require THEBUGGENIE_PATH . 'core/classes/TBGLogging.class.php';

		// Set the environment to cli if it is
		if (isset($argc))
		{
			TBGContext::setEnvironment(TBGContext::ENV_CLI);
		}

		// Set the start time
		TBGContext::setLoadStart($starttime[1] + $starttime[0]);
		TBGLogging::log('Initializing B2 framework');
		
		// Set the include path
		TBGContext::setIncludePath(THEBUGGENIE_PATH);

		// Add classpath so we can find the BUGS* classes
		TBGContext::addClasspath(THEBUGGENIE_PATH . 'core/classes/');
		TBGLogging::log((TBGCache::isEnabled()) ? 'Cache is enabled' : 'Cache is not enabled');
		
		TBGLogging::log('Loading B2DB');
		try
		{
			TBGLogging::log('Adding B2DB classes to autoload path');
			define ('B2DB_BASEPATH', THEBUGGENIE_PATH . 'core/B2DB/');
			TBGContext::addClasspath(THEBUGGENIE_PATH . 'core/B2DB/classes/');
			TBGLogging::log('...done (Adding B2DB classes to autoload path)');

			TBGLogging::log('Initializing B2DB');
			if (!isset($argc)) BaseB2DB::setHTMLException(true);
			BaseB2DB::initialize();
			TBGLogging::log('...done (Initializing B2DB)');
			
			if (class_exists('B2DB'))
			{
				TBGLogging::log('Database connection details found, connecting');
				B2DB::doConnect();
				TBGLogging::log('...done (Database connection details found, connecting)');
				TBGLogging::log('Adding B2DB table classpath to autoload path');
				TBGContext::addClasspath(THEBUGGENIE_PATH . 'core/classes/B2DB/');
			}
			
		}
		catch (Exception $e)
		{
			tbg_exception('Could not load and initiate the B2DB subsystem', $e);
			exit();
		}
		TBGLogging::log('...done');
		
		TBGLogging::log('Initializing context');
		TBGContext::initialize();
		TBGLogging::log('...done');
		
		require THEBUGGENIE_PATH . 'core/common_functions.inc.php';
		require THEBUGGENIE_PATH . 'core/geshi/geshi.php';
		
		TBGLogging::log('B2 framework loaded');
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
	