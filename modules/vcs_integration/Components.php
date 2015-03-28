<?php

    namespace thebuggenie\modules\vcs_integration;

    use thebuggenie\core\framework;

    /**
     * Module action components, vcs_integration
     *
     * @author Philip Kent <kentphilip@gmail.com>
     * @version 2.0
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage vcs_integration
     */

    /**
     * Module action components, vcs_integration
     *
     * @package thebuggenie
     * @subpackage vcs_integration
     */
    class Components extends framework\ActionComponent
    {

        public function componentCommitbackdrop()
        {
            $this->commit = entities\tables\Commits::getTable()->selectById($this->commit_id);
            $this->projectId = $this->commit->getProject()->getId();
        }

    }
