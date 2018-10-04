<?php

namespace thebuggenie\core\modules\livelink;

use thebuggenie\core\framework\Request;

interface ConnectorProvider
{

    /**
     * @return BaseConnector
     */
    public function getConnector();

    public function postImportProject(Request $request);

}