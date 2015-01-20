<?php

    namespace thebuggenie\modules\publish\cli;

    /**
     * CLI command class, publish -> import
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage publish
     */

    /**
     * CLI command class, publish -> import
     *
     * @package thebuggenie
     * @subpackage publish
     */
    class Import extends \thebuggenie\core\framework\cli\Command
    {

        protected function _setup()
        {
            $this->_command_name = 'import_articles';
            $this->_description = "Imports all articles from the fixtures folder";
            $this->addOptionalArgument('overwrite', "Set to 'yes' to overwrite existing articles");
        }

        public function do_execute()
        {
            $this->cliEcho("Importing articles ... \n", 'white', 'bold');
            \thebuggenie\core\framework\Event::listen('publish', 'fixture_article_loaded', array($this, 'listenPublishFixtureArticleCreated'));
            $overwrite = (bool) ($this->getProvidedArgument('overwrite', 'no') == 'yes');
            
            \thebuggenie\core\framework\Context::getModule('publish')->loadFixturesArticles(\thebuggenie\core\framework\Context::getScope()->getID(), $overwrite);
        }

        public function listenPublishFixtureArticleCreated(\thebuggenie\core\framework\Event $event)
        {
            $this->cliEcho(($event->getParameter('imported')) ? "Importing " : "Skipping ");
            $this->cliEcho($event->getSubject()."\n", 'white', 'bold');
        }

    }
