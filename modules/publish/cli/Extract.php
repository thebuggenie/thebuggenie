<?php

    namespace thebuggenie\modules\publish\cli;

    /**
     * CLI command class, publish -> export
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage publish
     */

    /**
     * CLI command class, publish -> export
     *
     * @package thebuggenie
     * @subpackage publish
     */
    class Extract extends \thebuggenie\core\framework\cli\Command
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
            $articles = tables\Articles::getTable()->getAllArticles();

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
