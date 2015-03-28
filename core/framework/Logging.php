<?php

    namespace thebuggenie\core\framework;

    /**
     * Logging class
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage core
     */

    /**
     * Logging class
     *
     * @package thebuggenie
     * @subpackage core
     */
    class Logging
    {
        const LEVEL_INFO = 1;
        const LEVEL_NOTICE = 5;
        const LEVEL_WARNING = 10;
        const LEVEL_WARNING_RISK = 15;
        const LEVEL_FATAL = 20;
        
        protected static $_logging_enabled = true;
        
        protected static $_logfile;
        
        protected static $_logonajaxcalls = true;
        
        protected static $_entries = array();
        
        protected static $_categorized_entries = array();

        protected static $_loglevel = 1;

        protected static $_cli_log_to_screen_in_debug_mode = false;

        /**
         * Log a message to the logger
         *
         * @param string $message The message to log
         * @param string $category [optional] The message category (default "main")
         * @param integer $level [optional] The loglevel
         */
        public static function log($message, $category = 'main', $level = 1)
        {
            if (!self::$_logging_enabled) return false;
            if (self::$_loglevel > $level) return false;
            if (self::$_cli_log_to_screen_in_debug_mode && Context::isCLI() && Context::isDebugMode() && class_exists('\thebuggenie\core\framework\cli\Command'))
            {
                \thebuggenie\core\framework\cli\Command::cli_echo(mb_strtoupper(self::getLevelName($level)), 'white', 'bold');
                \thebuggenie\core\framework\cli\Command::cli_echo(" [{$category}] ", 'green', 'bold');
                \thebuggenie\core\framework\cli\Command::cli_echo("$message\n");
            }
            if (self::$_logonajaxcalls || Context::getRequest()->isAjaxCall())
            {
                if (self::$_logfile !== null)
                {
                    file_put_contents(self::$_logfile, mb_strtoupper(self::getLevelName($level)) . " [{$category}] {$message}\n", FILE_APPEND);
                }
                $time_msg = (Context::isDebugMode()) ? (($load_time = Context::getLoadtime()) >= 1) ? round($load_time, 2) . ' seconds' : round(($load_time * 1000), 3) . ' ms' : '';
                
                self::$_entries[] = array('category' => $category, 'time' => $time_msg, 'message' => $message, 'level' => $level);
                self::$_categorized_entries[$category][] = array('time' => $time_msg, 'message' => $message, 'level' => $level);
            }
        }

        /**
         * Get the level name for a given level
         *
         * @param integer $level
         *
         * @return string
         */
        public static function getLevelName($level)
        {
            switch ($level)
            {
                case self::LEVEL_INFO:
                    return 'info';
                case self::LEVEL_NOTICE:
                    return 'notice';
                case self::LEVEL_WARNING:
                    return 'warning';
                case self::LEVEL_WARNING_RISK:
                    return 'risk';
                case self::LEVEL_FATAL:
                    return 'fatal';
                default:
                    return 'unknown';
            }
        }
        
        public static function setLogFilePath($filename)
        {
            if (!file_exists($filename))
            {
                throw new \Exception('Invalid log filename');
            }
            self::$_logfile = $filename;
        }

        /**
         * Return the color assigned to a specific category
         *
         * @param string $category
         *
         * @return string
         */
        public static function getCategoryColor($category)
        {
            switch ($category)
            {
                case 'main':
                    return "55C";
                case 'B2DB':
                    return "33B";
                case 'routing':
                    return "5C5";
                case 'i18n':
                    return "A83";
                case 'cache':
                    return "8A3";
                case 'search':
                    return "2FA";
                case 'publish':
                    return "A79";
                default: 
                    return "999";
            }
        }

        /**
         * Get current logged entries
         *
         * @return array
         */
        public static function getEntries()
        {
            return self::$_entries;
        }

        /**
         * Get complete log entries for a specific category
         *
         * @param string $category
         * @param integer $min_level [optional]
         * 
         * @return array
         */
        public static function getEntriesForCategory($category, $min_level = 1)
        {
            $retval = array();
            foreach (self::$_entries as $entry)
            {
                if ($entry['category'] == $category && $entry['level'] >= $min_level)
                {
                    $retval[] = $entry;
                }
            }
            return $retval;
        }
        
        /**
         * Get log messages for a specific category
         *
         * @param string $category
         * @param integer $min_level [optional]
         *
         * @return array
         */
        public static function getMessagesForCategory($category, $min_level = 1)
        {
            $retval = array();
            foreach (self::$_entries as $entry)
            {
                if ($entry['category'] == $category && $entry['level'] >= $min_level)
                {
                    $retval[] = $entry['message'];
                }
            }
            return $retval;
        }

        /**
         * Return whether logging is enabled
         *
         * @return boolean
         */
        public static function isEnabled()
        {
            return self::$_logging_enabled;
        }

        public static function setCLIDebug($value = true)
        {
            self::$_cli_log_to_screen_in_debug_mode = $value;
        }
        
    }
    
