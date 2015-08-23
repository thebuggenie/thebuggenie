<?php

    $tbg_response->addBreadcrumb(__('Project files'), make_url('project_files', array('project_key' => $selected_project->getKey())));
    $tbg_response->setTitle(__('"%project_name" files', array('%project_name' => $selected_project->getName())));

?>
