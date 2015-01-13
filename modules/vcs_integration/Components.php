<?php

    namespace thebuggenie\modules\vcs_integration;

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
    class Components extends \TBGActionComponent
    {

        public function componentCommitbackdrop()
        {
            $this->commit = TBGVCSIntegrationCommitsTable::getTable()->selectById($this->commit_id);
            $this->projectId = $this->commit->getProject()->getId();
        }

    }
