<?php

namespace thebuggenie\core\modules\livelink;

use thebuggenie\core\framework\Request;

interface ConnectorProvider
{

    /**
     * @return BaseConnector
     */
    public function getConnector();

    public function postConnectorSettings(Request $request);

    public function removeConnectorSettings(Request $request);

    public function getInputOptionsForProjectEdit(Request $request);

    public function getImportDisplayNameForProjectEdit(Request $request);

    public function getImportProjectNameForProjectEdit(Request $request);

}