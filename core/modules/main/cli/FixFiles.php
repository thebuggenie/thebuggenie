<?php

    namespace thebuggenie\core\modules\main\cli;

    /**
     * CLI command class, main -> fix_files
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage core
     */
    use thebuggenie\core\entities\File;
    use thebuggenie\core\entities\tables\Files;

    /**
     * CLI command class, main -> fix_files
     *
     * @package thebuggenie
     * @subpackage core
     */
    class FixFiles extends \thebuggenie\core\framework\cli\Command
    {

        protected function _setup()
        {
            $this->_command_name = 'fix_files';
            $this->_description = "Removes any lingering uploaded files (not attached to issues or articles)";
        }

        public function do_execute()
        {
            if (\thebuggenie\core\framework\Context::isInstallmode())
            {
                $this->cliEcho("The Bug Genie is not installed\n", 'red');
            }
            else
            {
                $this->cliEcho("Finding files to remove\n", 'white', 'bold');
                $files = Files::getTable()->getUnattachedFiles();
                $ignore_files = array();

                if (\thebuggenie\core\framework\Settings::isUsingCustomFavicon()) $ignore_files[] = \thebuggenie\core\framework\Settings::getFaviconID();
                if (\thebuggenie\core\framework\Settings::isUsingCustomHeaderIcon()) $ignore_files[] = \thebuggenie\core\framework\Settings::getHeaderIconID();

                $files = array_diff($files, $ignore_files);
                $this->cliEcho("Found " . count($files) . " files\n", 'white');
                foreach ($files as $file_id) {
                    $file = Files::getTable()->selectById($file_id);
                    $this->cliEcho('Deleting file ' . $file_id . "\n");
                    $file->delete();
                }
                $this->cliEcho("All " . count($files) . " files removed successfully!\n\n", 'white', 'bold');;
            }
        }

    }
