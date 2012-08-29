* <?php echo $issue->getFormattedTitle(true); ?> *
Created by <?php echo $issue->getPostedBy()->getBuddyname(); ?> (<?php echo $issue->getPostedBy()->getUsername(); ?>)

* Description: *
<?php echo tbg_parse_text($issue->getDescription()); ?>

<?php if ($issue->getReproductionSteps()): ?>
	* Reproduction steps: *
	<?php echo tbg_parse_text($issue->getReproductionSteps()); ?>
<?php endif; ?>

Show issue: <?php echo $module->generateURL('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())); ?>
Show <?php echo $issue->getProject()->getName(); ?> project dashboard: <?php echo $module->generateURL('project_dashboard', array('project_key' => $issue->getProject()->getKey())); ?>

You were sent this notification email because you are related to the issue mentioned in this email.
To change when and how often we send these emails, update your account settings: <?php echo link_tag($module->generateURL('account'), $module->generateURL('account')); ?>