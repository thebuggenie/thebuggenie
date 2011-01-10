Bonjour %user_buddyname%,
<?php echo $issue->getIssuetype()->getName(); ?> <?php echo $issue->getFormattedTitle(true); ?> a &eacute;t&eacute; mise &agrave; jour par <?php echo $updated_by->getName(); ?>.

* Les informations suivantes ont &eacute;t&eacute; modifi&eacute;es : *
<?php foreach ($comment_lines as $comment_line): ?>
    <?php echo $comment_line; ?>
<?php endforeach; ?>

---
Afficher la demande : <?php echo make_url('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo()), false); ?>
Affiche le tableau de bord du projet <?php echo $issue->getProject()->getName(); ?> : <?php echo make_url('project_dashboard', array('project_key' => $issue->getProject()->getKey()), false); ?>