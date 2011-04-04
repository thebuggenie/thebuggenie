<?php

	/**
	 * CLI command class, publish -> import
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage publish
	 */

	/**
	 * CLI command class, publish -> import
	 *
	 * @package thebuggenie
	 * @subpackage publish
	 */
	class CliPublishImport extends TBGCliCommand
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
			TBGEvent::listen('publish', 'fixture_article_loaded', array($this, 'listenPublishFixtureArticleCreated'));
			$overwrite = (bool) ($this->getProvidedArgument('overwrite', 'no') == 'yes');
			
			TBGPublish::getModule()->loadFixturesArticles(TBGContext::getScope()->getID(), $overwrite);
		}

		public function listenPublishFixtureArticleCreated(TBGEvent $event)
		{
			$this->cliEcho(($event->getParameter('imported')) ? "Importing " : "Skipping ");
			$this->cliEcho($event->getSubject()."\n", 'white', 'bold');
		}

	}