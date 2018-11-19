<?php foreach ($commits as $commit): ?>
    <?php include_component('livelink/issuecommitbox', array("project" => $project, "commit" => $commit->getCommit())); ?>
<?php endforeach; ?>

