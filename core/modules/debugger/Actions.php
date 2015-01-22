<?php

    namespace thebuggenie\core\modules\debugger;

    use thebuggenie\core\framework;

    /**
     * actions for the debugger module
     */
    class Actions extends framework\Action
    {

        public function runIndex(framework\Request $request)
        {
            $this->getResponse()->setDecoration(\thebuggenie\core\framework\Response::DECORATE_NONE);
            $this->tbg_summary = framework\Context::getDebugData($request['debug_id']);
        }

    }
