<?php

	/**
	 * I18n class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 ** @version 3.0
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
		
		protected $_datetime_formats = array();
		
		public function __construct($language)
		{
			if (!file_exists($this->getStringsFilename($language)))
			{
				TBGLogging::log('Selected language not available, trying "en_US" as a last attempt', 'i18n', TBGLogging::LEVEL_NOTICE);
				$this->_language = 'en_US';
				if (!file_exists($this->getStringsFilename($this->_language)))
				{
					throw new Exception('The selected language is not available');
				}
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
			if (file_exists(TBGContext::getIncludePath() . 'modules' . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . 'i18n' . DIRECTORY_SEPARATOR . $this->_language . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . $templatefile))
			{
				return TBGContext::getIncludePath() . 'modules' . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . 'i18n' . DIRECTORY_SEPARATOR . $this->_language . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . $templatefile;
			}
			elseif (file_exists(TBGContext::getIncludePath() . 'i18n' . DIRECTORY_SEPARATOR . $this->getCurrentLanguage() . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . $templatefile))
			{
				return TBGContext::getIncludePath() . 'i18n' . DIRECTORY_SEPARATOR . $this->getCurrentLanguage() . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . $templatefile;
			}
			return false;
		}

		public function __($text, $replacements = array(), $html_decode = false)
		{
			if (isset($this->_strings[$text]))
			{
				$retstring = $this->_strings[$text];
			}
			else
			{
				$retstring = $text;
				TBGLogging::log('The text "' . $text . '" does not exist in list of translated strings.', 'i18n');
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
			if ($html_decode) {
				$retstring = html_entity_decode($retstring);
			}
			return $retstring;
		}
		
		/** 
		 * Set local date and time formats
		 * 
		 * @param $formats array list of applicable formats for this local
		 * 
		 */
		public function setDateTimeFormats($formats)
		{
			if(is_array($formats))
			{
				$this->_datetime_formats = $formats;
			}
		}
		
		/** 
		 * Return localized date and time format
		 * @see http://php.net/manual/en/function.date.php
		 * 
		 * @param $id integer ID of format
		 * 
		 * @return string
		 * 
		 */
		public function getDateTimeFormat($id)
		{
			if(array_key_exists($id, $this->_datetime_formats))
			{
				 return $this->_datetime_formats[$id];
			}
			switch ($id)
			{
				case 1 : // 14:45 - Thu Dec 30, 2010
					$format = '%H:%M - %a %b %d, %Y';
					break;
				case 2 : // 14:45 - Thu 30.m, 2010
					$format = '%H:%M - %a %d.m, %Y';
					break;
				case 3 : // Thu Dec 30 14:45
					$format = '%a %b %d %H:%M';
					break;
				case 4 : // Dec 30 14:45
					$format = '%b %d %H:%M';
					break;
				case 5 : // December 30, 2010
					$format = '%B %d, %Y';
					break;
				case 6 : // December 30, 2010 (14:45)
					$format = '%B %d, %Y (%H:%M)';
					break;
				case 7 : // Thursday 30 December, 2010 (14:45)
					$format = '%A %d %B, %Y (%H:%M)';
					break;
				case 8 : // Dec 30, 2010 14:45
					$format = '%b %d, %Y %H:%M';
					break;
				case 9 : // Dec 30, 2010 - 14:45
					$format = '%b %d, %Y - %H:%M';
					break;
				case 10 : // Dec 30, 2010 (14:45)
					$format = '%b %d, %Y (%H:%M)';
					break;
				case 11 : // December
					$format = '%B';
					break;
				case 12 : // Dec 30
					$format = '%b %d';
					break;
				case 13 : // Thu
					$format = '%a';
					break;
				case 14 : // 14:45
					$format = '%H:%M';
					break;
				case 15 : // Dec 30, 2010
					$format = '%b %d, %Y';
					break;
				case 16 : // 14h 45m
					$format = '%Gh %im';
					break;
				case 17 : // Thu, 30 December 2010 14:45:45 GMT
					$format = '%a, %d %b %Y %H:%M:%S GMT';
					break;
				default : // local server setting
					$format = '%c';
			}
			return $format;
		}		
		
	}
