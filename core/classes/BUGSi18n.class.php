<?php

	/**
	 * I18n class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage core
	 */

	/**
	 * I18n class
	 *
	 * @package thebuggenie
	 * @subpackage core
	 */
	class BUGSi18n
	{

		protected $_strings = array();
		
		protected $_missing_strings = array();
		
		protected $_language = null;
		
		protected $_charset = 'utf-8';
		
		public function __construct($language)
		{
			if (!file_exists(BUGScontext::getIncludePath() . 'i18n/' . $language . '/general.inc.php'))
			{
				throw new Exception('The selected language is not available');
			}
			$this->_language = $language;
		}
		
		public function initialize()
		{
			$this->loadStrings();
		}
		
		public function setLanguage($language)
		{
			$this->_language = $language;
			$this->loadStrings();
		}
		
		public function getMissingStrings()
		{
			return $this->_missing_strings;
		}
		
		public function setCharset($charset)
		{
			$this->_charset = $charset;
		}

		public function getCurrentLanguage()
		{
			return $this->_language;
		}
		
		public function loadHelpTopic($topic, $module = '')
		{
			if ($module == '')
			{
				if (BUGSsettings::get($topic, 'help'))
				{
					return BUGSsettings::get($topic, 'help');
				}
				$filename = BUGScontext::getIncludePath() . 'i18n/' . $this->getCurrentLanguage() . "/help/$topic.inc.php";
				if (file_exists($filename))
				{
					return file_get_contents($filename);
				}
			}
			else
			{
				$filename = BUGScontext::getIncludePath() . 'i18n/' . $this->getCurrentLanguage() . "/help/$module/$topic.inc.php";
				if (file_exists($filename))
				{
					return file_get_contents($filename);
				}
				$filename = BUGScontext::getIncludePath() . "modules/$module/help/$topic.inc.php";
				if (file_exists($filename))
				{
					return file_get_contents($filename);
				}
			}
			return false;
		}
		
		public function getCharset()
		{
			return (BUGSsettings::get('charset') != '') ? BUGSsettings::get('charset') : $this->_charset;
		}
		
		public function getLangCharset()
		{
			return $this->_charset;
		}
		
		public function loadModuleStrings($module)
		{
			$this->loadStrings($module);
		}
		
		protected function loadStrings($module = null)
		{
			$strings_key = ($module !== null) ? 'i18n_strings' : "i18n_strings_{$module}";
			if (!$strings = BUGScache::get($strings_key))
			{
				BUGSlogging::log('Loading strings from file', 'i18n');
				$filename = '';
				if ($module !== null)
				{
					if (file_exists(BUGScontext::getIncludePath() . 'i18n' . DIRECTORY_SEPARATOR . $this->_language . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . 'general.inc.php'))
					{
						$filename = BUGScontext::getIncludePath() . 'i18n' . DIRECTORY_SEPARATOR . $this->_language . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . 'general.inc.php';
					}
					else
					{
						$filename = BUGScontext::getIncludePath() . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . 'i18n' . DIRECTORY_SEPARATOR . $this->_language . DIRECTORY_SEPARATOR . 'general.inc.php';
					}
				}
				else
				{
					$filename = BUGScontext::getIncludePath() . 'i18n' . DIRECTORY_SEPARATOR . $this->_language . DIRECTORY_SEPARATOR . 'general.inc.php';
				}
				//echo $filename;
				if (file_exists($filename))
				{
					$strings = array();
					require $filename;
					BUGScache::add($strings_key, $strings);
				}
				else
				{
					$message = 'Could not find language file ' . $filename;
					BUGSlogging::log($message, 'i18n');
					throw new Exception($message);
				}
			}
			else
			{
				BUGSlogging::log('Using cached strings', 'i18n');
			}
			$this->addStrings($strings);
		}
		
		public function addString($key, $translation)
		{
			$this->_strings[$key] = $translation;
		}
		
		public function addStrings($strings)
		{
			foreach ($strings as $key => $translation)
			{
				$this->_strings[$key] = $translation;
			}
		}
		
		public static function getLanguages()
		{
			$retarr = array();
			$cp_handle = opendir(BUGScontext::getIncludePath() . 'i18n');
			while ($classfile = readdir($cp_handle))
			{
				if (strstr($classfile, '.') == '') 
				{ 
					$retarr[$classfile] = file_get_contents(BUGScontext::getIncludePath() . 'i18n/' . $classfile . '/language');
				}
			}
			
			return $retarr;
		}
		
		public function __($text, $replacements = array())
		{
			if (isset($this->_strings[$text]))
			{
				$retstring = $this->_strings[$text];
			}
			else
			{
				$retstring = $text;
				//BUGSlogging::log('The text "' . $text . '" does not exist in list of translated strings.', 'i18n');
				$this->_missing_strings[$text] = true;
			}
			if (!empty($replacements))
			{
				$tmp = array();
				foreach ($replacements as $key => $value)
				{
        			$tmp[$key] = $value;
    				$retstring = str_replace(array_keys($tmp), array_values($tmp), $retstring);
				}
			}
			return $retstring;
		}
		
	}
