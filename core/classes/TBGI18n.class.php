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
	class TBGI18n
	{

		protected $_strings = array();
		
		protected $_missing_strings = array();
		
		protected $_language = null;
		
		protected $_charset = 'utf-8';
		
		public function __construct($language)
		{
			if (!file_exists($this->getStringsFilename($language)))
			{
				throw new Exception('The selected language is not available');
			}
			$this->_language = $language;
		}

		public function getStringsFilename($language = null)
		{
			$language = ($language === null) ? $this->_language : $language;
			return TBGContext::getIncludePath() . 'i18n' . DIRECTORY_SEPARATOR . $language . DIRECTORY_SEPARATOR . 'strings.inc.php';
		}
		
		public function initialize()
		{
			$filename = TBGContext::getIncludePath() . 'i18n' . DIRECTORY_SEPARATOR . $this->_language . DIRECTORY_SEPARATOR . 'initialize.inc.php';
			if (file_exists($filename))
			{
				include $filename;
			}
			$this->loadStrings();
		}
		
		public function setLanguage($language)
		{
			if ($language != $this->_language)
			{
				$this->_language = $language;
				$this->initialize();
			}
		}
		
		public function getMissingStrings()
		{
			return $this->_missing_strings;
		}

		public function addMissingStringsToStringsFile()
		{
			foreach ($this->getMissingStrings() as $string => $truth)
			{
				if (strpos($string, '"') !== false && strpos($string, "'") !== false)
				{
					$string = str_replace('"', '\"', $string);
					file_put_contents($this->getStringsFilename(), "\n\t".'$strings["'.$string.'"] = "'.$string."\";", FILE_APPEND);
				}
				elseif (strpos($string, "'") !== false)
				{
					file_put_contents($this->getStringsFilename(), "\n\t".'$strings["'.$string.'"] = "'.$string."\";", FILE_APPEND);
				}
				else
				{
					file_put_contents($this->getStringsFilename(), "\n\t".'$strings[\''.$string.'\'] = \''.$string."';", FILE_APPEND);
				}
			}

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
				if (TBGSettings::get($topic, 'help'))
				{
					return TBGSettings::get($topic, 'help');
				}
				$filename = TBGContext::getIncludePath() . 'i18n/' . $this->getCurrentLanguage() . "/help/$topic.inc.php";
				if (file_exists($filename))
				{
					return file_get_contents($filename);
				}
			}
			else
			{
				$filename = TBGContext::getIncludePath() . 'i18n/' . $this->getCurrentLanguage() . "/help/$module/$topic.inc.php";
				if (file_exists($filename))
				{
					return file_get_contents($filename);
				}
				$filename = TBGContext::getIncludePath() . "modules/$module/help/$topic.inc.php";
				if (file_exists($filename))
				{
					return file_get_contents($filename);
				}
			}
			return false;
		}
		
		public function getCharset()
		{
			if (TBGContext::isInstallmode()) return $this->_charset;
			return (TBGSettings::get('charset') != '') ? TBGSettings::get('charset') : $this->_charset;
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
			if (!$strings = TBGCache::get($strings_key))
			{
				TBGLogging::log('Loading strings from file', 'i18n');
				$filename = '';
				if ($module !== null)
				{
					if (file_exists(TBGContext::getIncludePath() . 'i18n' . DIRECTORY_SEPARATOR . $this->_language . DIRECTORY_SEPARATOR . "{$module}.inc.php"))
					{
						$filename = TBGContext::getIncludePath() . 'i18n' . DIRECTORY_SEPARATOR . $this->_language . DIRECTORY_SEPARATOR . "{$module}.inc.php";
					}
					else
					{
						$filename = TBGContext::getIncludePath() . 'modules' . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . 'i18n' . DIRECTORY_SEPARATOR . $this->_language . DIRECTORY_SEPARATOR . "{$module}.inc.php";
					}
				}
				else
				{
					$filename = $this->getStringsFilename();
				}
				//echo $filename;
				if (file_exists($filename))
				{
					$strings = array();
					require $filename;
					TBGCache::add($strings_key, $strings);
				}
				else
				{
					$message = 'Could not find language file ' . $filename;
					TBGLogging::log($message, 'i18n', TBGLogging::LEVEL_NOTICE);
				}
			}
			else
			{
				TBGLogging::log('Using cached strings', 'i18n');
			}
			$this->addStrings($strings);
		}
		
		public function addString($key, $translation)
		{
			$this->_strings[$key] = $translation;
		}
		
		public function addStrings($strings)
		{
			if (is_array($strings))
			{
				foreach ($strings as $key => $translation)
				{
					$this->_strings[$key] = $translation;
				}
			}
		}
		
		public static function getLanguages()
		{
			$retarr = array();
			$cp_handle = opendir(TBGContext::getIncludePath() . 'i18n');
			while ($classfile = readdir($cp_handle))
			{
				if (strstr($classfile, '.') == '') 
				{ 
					$retarr[$classfile] = file_get_contents(TBGContext::getIncludePath() . 'i18n/' . $classfile . '/language');
				}
			}
			
			return $retarr;
		}

		public function hasTranslatedTemplate($template, $is_component = false)
		{
			$basepath = TBGContext::getIncludePath() . 'i18n' . DIRECTORY_SEPARATOR . $this->getCurrentLanguage() . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR;
			if (strpos($template, '/'))
			{
				$templateinfo = explode('/', $template);
				$module = $templateinfo[0];
				$templatefile = ($is_component) ? '_' . $templateinfo[1] . '.inc.php' : $templateinfo[1] . '.' . TBGContext::getRequest()->getRequestedFormat() . '.php';
			}
			else
			{
				$module = TBGContext::getRouting()->getCurrentRouteModule();
				$templatefile = ($is_component) ? '_' . $template . '.inc.php' : $template . '.' . TBGContext::getRequest()->getRequestedFormat() . '.php';
			}
			if (file_exists($basepath . $module . DIRECTORY_SEPARATOR . $templatefile))
			{
				return $basepath . $module . DIRECTORY_SEPARATOR . $templatefile;
			}
			return false;
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
				//TBGLogging::log('The text "' . $text . '" does not exist in list of translated strings.', 'i18n');
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
