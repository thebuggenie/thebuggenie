<?php

    namespace thebuggenie\core\modules\livelink\cli;

    use thebuggenie\core\entities\tables\Branches;
    use thebuggenie\core\entities\tables\Commits;
    use thebuggenie\core\entities\tables\LivelinkImports;
    use thebuggenie\core\framework\Context;
    use thebuggenie\core\framework\Settings;
    use thebuggenie\core\modules\livelink\Livelink;
    use thebuggenie\modules\mailing\Mailing;

    /**
     * CLI command class, livelink -> import
     *
     * @author Philip Kent <kentphilip@gmail.com>
     * @version 3.2
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage vcs_integration
     */

    /**
     * CLI command class, livelink -> import
     *
     * @package thebuggenie
     * @subpackage vcs_integration
     */
    class Import extends \thebuggenie\core\framework\cli\Command
    {

        protected function _setup()
        {
            $this->_command_name = 'import';
            $this->_description = "Import a project from an external repository";
        }

        public function do_execute()
        {
            /* Prepare variables */            
            try
            {
//                Commits::getTable()->create();
//                Branches::getTable()->create();
                $imports = LivelinkImports::getTable()->getPending();
                Mailing::getModule()->temporarilyDisable();

                $current = 1;

                foreach ($imports as $import) {
                    $this->cliEcho("Running import {$current} of ".count($imports)."\n");
                    $this->cliEcho("---------\n");
                    $this->cliEcho("Importing project ".$import->getProject()->getName()." in scope " . $import->getScope()->getID() . "\n");
                    $current += 1;
                    Context::setScope($import->getScope());
                    Context::switchUserContext($import->getUser());
                    Livelink::getModule()->performImport($import);

                    $this->cliEcho("Done!\n\n", 'white', 'bold');

                    $import->setCompletedAt(NOW);
                    $import->save();
                }

                Mailing::getModule()->removeTemporarilyDisable();
            }
            catch (\Exception $e)
            {
                if (isset($import)) {
                    $import->setCompletedAt(NOW);
                    $import->save();
                }

                throw $e;
            }
            
        }
    }
