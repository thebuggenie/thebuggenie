<?php foreach ($links as $link) include_component('vcs_integration/issuecommitbox', array("projectId" => $projectId, "commit" => $link->getCommit())); ?>

