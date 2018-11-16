<?php foreach ($links as $link) include_component('livelink/issuecommitbox', array("projectId" => $projectId, "commit" => $link->getCommit())); ?>

