<?php if ($issue instanceof TBGIssue && $comment instanceof TBGComment): ?>
	<h3>
		<?php echo $issue->getFormattedTitle(true); ?><br>
		<span style="font-size: 0.8em; font-weight: normal;">Created by <?php echo $issue->getPostedBy()->getBuddyname(); ?> (<?php echo $issue->getPostedBy()->getUsername(); ?>)</span>
	</h3>
	<br>
	<h4>Comment by <?php echo $comment->getPostedBy()->getBuddyname(); ?> (<?php echo $comment->getPostedBy()->getUsername(); ?>)</h4>
	<p><?php echo tbg_parse_text($comment->getContent()); ?></p>
	<br>
	<div style="color: #888;">
		Show issue: <?php echo link_tag($module->generateURL('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo()))); ?><br>
		Show comment: <?php echo link_tag($module->generateURL('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())).'#comment_'.$comment->getID()); ?><br>
		Show <?php echo $issue->getProject()->getName(); ?> project dashboard: <?php echo link_tag($module->generateURL('project_dashboard', array('project_key' => $issue->getProject()->getKey()))); ?><br>
		<br>
		You were sent this notification email because you are related to the issue mentioned in this email.<br>
		To change when and how often we send these emails, update your account settings: <?php echo link_tag($module->generateURL('account'), $module->generateURL('account')); ?>
	</div>
<?php endif; ?>