* <?php echo $issue->getFormattedTitle(true); ?> *
Updated by <?php echo $updated_by->getBuddyname(); ?> (<?php echo $updated_by->getUsername(); ?>)
(created by <?php echo $issue->getPostedBy()->getBuddyname(); ?> / <?php echo $issue->getPostedBy()->getUsername(); ?>)

<?php if (isset($comment) && $comment instanceof TBGComment): ?>
* Comment by <?php echo $comment->getPostedBy()->getBuddyname(); ?> (<?php echo $comment->getPostedBy()->getUsername(); ?>) *
<?php echo tbg_parse_text($comment->getContent()); ?>
<?php endif; ?>

<?php if (count($log_items)): ?>
* Changes *
	<?php foreach ($log_items as $item): ?>
		<?php

			switch ($item->getChangeType())
			{
				case TBGLogTable::LOG_ISSUE_CREATED:
				case TBGLogTable::LOG_COMMENT:
					break;
				case TBGLogTable::LOG_ISSUE_CLOSE:
					echo ' * ' . __('Issue closed');
					break;
				case TBGLogTable::LOG_ISSUE_REOPEN:
					echo ' * ' . __('Issue reopened');
					break;
				case TBGLogTable::LOG_ISSUE_UPDATE:
					echo ' * ' . $item->getText();
					break;
				case TBGLogTable::LOG_ISSUE_PAIN_BUG_TYPE:
					echo ' * ' . __('Triaged bug type: %text%', array('%text%' => $item->getText()));
					break;
				case TBGLogTable::LOG_ISSUE_PAIN_LIKELIHOOD:
					echo ' * ' . __('Triaged likelihood: %text%', array('%text%' => $item->getText()));
					break;
				case TBGLogTable::LOG_ISSUE_PAIN_EFFECT:
					echo ' * ' . __('Triaged effect: %text%', array('%text%' => $item->getText()));
					break;
				case TBGLogTable::LOG_ISSUE_PAIN_CALCULATED:
					echo ' * ' . __('Calculated user pain: %text%', array('%text%' => $item->getText()));
					break;
				case TBGLogTable::LOG_ISSUE_CATEGORY:
					echo ' * ' . __('Category changed: %text%', array('%text%' => $item->getText()));
					break;
				case TBGLogTable::LOG_ISSUE_CUSTOMFIELD_CHANGED:
					echo ' * ' . __('Custom field changed: %text%', array('%text%' => $item->getText()));
					break;
				case TBGLogTable::LOG_ISSUE_STATUS:
					echo ' * ' . __('Status changed: %text%', array('%text%' => $item->getText()));
					break;
				case TBGLogTable::LOG_ISSUE_REPRODUCABILITY:
					echo ' * ' . __('Reproducability changed: %text%', array('%text%' => $item->getText()));
					break;
				case TBGLogTable::LOG_ISSUE_PRIORITY:
					echo ' * ' . __('Priority changed: %text%', array('%text%' => $item->getText()));
					break;
				case TBGLogTable::LOG_ISSUE_SEVERITY:
					echo ' * ' . __('Severity changed: %text%', array('%text%' => $item->getText()));
					break;
				case TBGLogTable::LOG_ISSUE_RESOLUTION:
					echo ' * ' . __('Resolution changed: %text%', array('%text%' => $item->getText()));
					break;
				case TBGLogTable::LOG_ISSUE_PERCENT:
					echo ' * ' . __('Percent completed: %text%', array('%text%' => $item->getText()));
					break;
				case TBGLogTable::LOG_ISSUE_MILESTONE:
					echo ' * ' . __('Target milestone changed: %text%', array('%text%' => $item->getText()));
					break;
				case TBGLogTable::LOG_ISSUE_ISSUETYPE:
					echo ' * ' . __('Issue type changed: %text%', array('%text%' => $item->getText()));
					break;
				case TBGLogTable::LOG_ISSUE_TIME_ESTIMATED:
					echo ' * ' . __('Estimation changed: %text%', array('%text%' => $item->getText()));
					break;
				case TBGLogTable::LOG_ISSUE_TIME_SPENT:
					echo ' * ' . __('Time spent: %text%', array('%text%' => $item->getText()));
					break;
				case TBGLogTable::LOG_ISSUE_ASSIGNED:
					echo ' * ' . __('Assignee changed: %text%', array('%text%' => $item->getText()));
					break;
				case TBGLogTable::LOG_ISSUE_OWNED:
					echo ' * ' . __('Owner changed: %text%', array('%text%' => $item->getText()));
					break;
				case TBGLogTable::LOG_ISSUE_POSTED:
					echo ' * ' . __('Posted by changed: %text%', array('%text%' => $item->getText()));
					break;
				default:
					if (!$item->getText())
					{
						echo ' * ' .__('Issue updated');
					}
					else
					{
						echo ' * ' .$item->getText();
					}
					break;
			}

		?>
	<?php endforeach; ?>
<?php endif; ?>

Show issue: <?php echo $module->generateURL('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())); ?>
<?php if (isset($comment) && $comment instanceof TBGComment): ?>
Show comment: <?php echo link_tag($module->generateURL('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())).'#comment_'.$comment->getID()); ?>
<?php endif; ?>
Show <?php echo $issue->getProject()->getName(); ?> project dashboard: <?php echo $module->generateURL('project_dashboard', array('project_key' => $issue->getProject()->getKey())); ?>

You were sent this notification email because you are related to the issue mentioned in this email.
To change when and how often we send these emails, update your account settings: <?php echo link_tag($module->generateURL('account'), $module->generateURL('account')); ?>