<?php

	/**
	 * CLI command class, publish -> export
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage publish
	 */

	/**
	 * CLI command class, publish -> export
	 *
	 * @package thebuggenie
	 * @subpackage publish
	 */
	class CliPublishExtract extends TBGCliCommand
	{

		protected function _setup()
		{
			$this->_command_name = 'extract_articles';
			$this->_description = "Extracts all articles from the database";
			$this->addOptionalArgument('overwrite', "Set to 'yes' to overwrite existing articles");
		}

		public function do_execute()
		{
			$this->cliEcho("Extracting articles ... \n", 'white', 'bold');
			$articles = TBGArticlesTable::getTable()->getAllArticles();

			$this->cliEcho("Articles found: ");
			$this->cliEcho(count($articles)."\n", 'green', 'bold');

			foreach ($articles as $article_id => $article)
			{
				$filename = THEBUGGENIE_MODULES_PATH . 'publish' . DS . 'fixtures' . DS . urlencode($article->getName());
				if (!file_exists($filename) || $this->getProvidedArgument('overwrite', 'no') == 'yes')
				{
					$this->cliEcho("Saving ");
					file_put_contents($filename, $article->getContent());
				}
				else
				{
					$this->cliEcho("Skipping ");
				}
				$this->cliEcho($article->getName()."\n", 'white', 'bold');
			}
		}

	}