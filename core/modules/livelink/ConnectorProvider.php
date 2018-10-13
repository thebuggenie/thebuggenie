<?php

namespace thebuggenie\core\modules\livelink;

use thebuggenie\core\entities\Project;
use thebuggenie\core\framework\Request;

interface ConnectorProvider
{

    public function getName();

    /**
     * @return BaseConnector
     */
    public function getConnector();

    public function postConnectorSettings(Request $request);

    public function removeConnectorSettings(Request $request);

    public function getInputOptionsForProjectEdit(Request $request);

    public function getImportDisplayNameForProjectEdit(Request $request);

    public function getImportProjectNameForProjectEdit(Request $request);

    public function saveProjectConnectorSettings(Request $request, Project $project, $secret);

    public function webhook(Request $request, Project $project);

    public function importProject(Project $project);

}