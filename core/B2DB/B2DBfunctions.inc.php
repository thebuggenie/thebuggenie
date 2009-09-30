<?php

	/**
	 * B2DB global functions 
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package B2DB
	 * @subpackage core
	 */
	
	/**
	 * autoloads classes in a given directory
	 * @param string $path directory to load
	 * 
	 * @return null
	 */
	function b2db_autoload($path)
	{
		if (file_exists($path . 'generics.class.php'))
		{
			require_once $path . 'generics.class.php';
		}
		$cp_handle = opendir($path);
		while ($classfile = readdir($cp_handle))
		{
			if (strstr($classfile, '.class.php') != '') 
			{ 
				require_once $path . $classfile;
			}
		}
	}

?>