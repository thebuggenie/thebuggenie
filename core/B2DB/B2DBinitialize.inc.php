<?php

	/**
	 * B2DB initialization script
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package B2DB
	 * @subpackage core
	 */
	
	if (!defined('B2DB_BASEPATH'))
	{
		throw new B2DBException('The constant B2DB_BASEPATH must be defined. B2DB_BASEPATH should be the full system path to B2DB');
	}
	
	try
	{
		if (file_exists(B2DB_BASEPATH . 'sql_parameters.inc.php'))
		{
			/**
			 * This is the parameter file that contains the database connection details
			 */ 
			require B2DB_BASEPATH . 'sql_parameters.inc.php';
		}
		
		if (BaseB2DB::getDBtype() != '')
		{
			$b2db_engine_path = '';
			if (file_exists(B2DB_BASEPATH . BaseB2DB::getDBtype() . '/classes/B2DB.class.php'))
			{
				$b2db_engine_path = B2DB_BASEPATH . BaseB2DB::getDBtype();
			}
			else
			{
				$b2db_engine_path = B2DB_BASEPATH . 'PDO';
			}
			
			b2db_autoload($b2db_engine_path . '/classes/');
			
			if (!defined('B2DB_DONTINITIALIZE'))
			{
				require $b2db_engine_path . '/dbinitialize.inc.php';
			}
		}
	}
	catch (Exception $e)
	{
		throw $e;
	}

?>