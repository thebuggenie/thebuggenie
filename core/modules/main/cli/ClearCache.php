<?php

    namespace thebuggenie\core\modules\main\cli;

    /**
     * CLI command class, main -> clear_cache
     *
     * @package thebuggenie
     * @subpackage core
     */
    use \thebuggenie\core\framework\Context;

    /**
     * CLI command class, main -> clear_cache
     *
     * @package thebuggenie
     * @subpackage core
     */
    class ClearCache extends \thebuggenie\core\framework\cli\Command
    {

        protected function _setup()
        {
            $this->_command_name = 'clear_cache';
            $this->_description = "Clears cache";
            parent::_setup();
        }

        public function do_execute()
        {
            $cache = Context::getCache();
            $cache_reflection = new \ReflectionClass($cache);

            $cache->clearCacheKeys(
                array_flip(array_filter(array_flip($cache_reflection->getConstants()), function ($cache_constant) use ($cache)
                {
                    return strpos($cache_constant, $cache::CLEAR_CACHE_KEY) === 0;
                })),
                true,
                true
            );

            $this->cliEcho("Cache cleared\n\n", 'white', 'bold');
        }

    }
