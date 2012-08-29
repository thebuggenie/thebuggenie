<?php if ($issue instanceof TBGIssue): ?>
	<h3>
		<?php echo $issue->getFormattedTitle(true); ?><br>
		<span style="font-size: 0.8em; font-weight: normal;">Updated by <?php echo $updated_by->getBuddyname(); ?> (<?php echo $updated_by->getUsername(); ?>)</span><br>
		<span style="font-size: 0.8em; color: #AAA; font-weight: normal;">Created by <?php echo $issue->getPostedBy()->getBuddyname(); ?> (<?php echo $issue->getPostedBy()->getUsername(); ?>)</span>
	</h3>
	<?php if (isset($comment) && $comment instanceof TBGComment): ?>
		<h4>Comment by <?php echo $comment->getPostedBy()->getBuddyname(); ?> (<?php echo $comment->getPostedBy()->getUsername(); ?>)</h4>
		<p><?php echo tbg_parse_text($comment->getContent()); ?></p>
		<br>
	<?php endif; ?>
	<?php if (count($log_items)): ?>
		<h4>Changes</h4>
		<ul>
			<?php foreach ($log_items as $item): ?>
				<li>
				<?php

					switch ($item->getChangeType())
					{
						case TBGLogTable::LOG_ISSUE_CREATED:
						case TBGLogTable::LOG_COMMENT:
							break;
						case TBGLogTable::LOG_ISSUE_CLOSE:
							echo '<i>' . __('Issue closed') . '</i>';
							break;
						case TBGLogTable::LOG_ISSUE_REOPEN:
							echo '<i>' . __('Issue reopened') . '</i>';
							break;
						case TBGLogTable::LOG_ISSUE_UPDATE:
							echo '<i>' . $item->getText() . '</i>';
							break;
						case TBGLogTable::LOG_ISSUE_PAIN_BUG_TYPE:
							echo '<i>' . __('Triaged bug type: %text%', array('%text%' => $item->getText())) . '</i>';
							break;
						case TBGLogTable::LOG_ISSUE_PAIN_LIKELIHOOD:
							echo '<i>' . __('Triaged likelihood: %text%', array('%text%' => $item->getText())) . '</i>';
							break;
						case TBGLogTable::LOG_ISSUE_PAIN_EFFECT:
							echo '<i>' . __('Triaged effect: %text%', array('%text%' => $item->getText())) . '</i>';
							break;
						case TBGLogTable::LOG_ISSUE_PAIN_CALCULATED:
							echo '<i>' . __('Calculated user pain: %text%', array('%text%' => $item->getText())) . '</i>';
							break;
						case TBGLogTable::LOG_ISSUE_CATEGORY:
							echo '<i>' . __('Category changed: %text%', array('%text%' => $item->getText())) . '</i>';
							break;
						case TBGLogTable::LOG_ISSUE_CUSTOMFIELD_CHANGED:
							echo '<i>' . __('Custom field changed: %text%', array('%text%' => $item->getText())) . '</i>';
							break;
						case TBGLogTable::LOG_ISSUE_STATUS:
							echo '<i>' . __('Status changed: %text%', array('%text%' => $item->getText())) . '</i>';
							break;
						case TBGLogTable::LOG_ISSUE_REPRODUCABILITY:
							echo '<i>' . __('Reproducability changed: %text%', array('%text%' => $item->getText())) . '</i>';
							break;
						case TBGLogTable::LOG_ISSUE_PRIORITY:
							echo '<i>' . __('Priority changed: %text%', array('%text%' => $item->getText())) . '</i>';
							break;
						case TBGLogTable::LOG_ISSUE_SEVERITY:
							echo '<i>' . __('Severity changed: %text%', array('%text%' => $item->getText())) . '</i>';
							break;
						case TBGLogTable::LOG_ISSUE_RESOLUTION:
							echo '<i>' . __('Resolution changed: %text%', array('%text%' => $item->getText())) . '</i>';
							break;
						case TBGLogTable::LOG_ISSUE_PERCENT:
							echo '<i>' . __('Percent completed: %text%', array('%text%' => $item->getText())) . '</i>';
							break;
						case TBGLogTable::LOG_ISSUE_MILESTONE:
							echo '<i>' . __('Target milestone changed: %text%', array('%text%' => $item->getText())) . '</i>';
							break;
						case TBGLogTable::LOG_ISSUE_ISSUETYPE:
							echo '<i>' . __('Issue type changed: %text%', array('%text%' => $item->getText())) . '</i>';
							break;
						case TBGLogTable::LOG_ISSUE_TIME_ESTIMATED:
							echo '<i>' . __('Estimation changed: %text%', array('%text%' => $item->getText())) . '</i>';
							break;
						case TBGLogTable::LOG_ISSUE_TIME_SPENT:
							echo '<i>' . __('Time spent: %text%', array('%text%' => $item->getText())) . '</i>';
							break;
						case TBGLogTable::LOG_ISSUE_ASSIGNED:
							echo '<i>' . __('Assignee changed: %text%', array('%text%' => $item->getText())) . '</i>';
							break;
						case TBGLogTable::LOG_ISSUE_OWNED:
							echo '<i>' . __('Owner changed: %text%', array('%text%' => $item->getText())) . '</i>';
							break;
						case TBGLogTable::LOG_ISSUE_POSTED:
							echo '<i>' . __('Posted by changed: %text%', array('%text%' => $item->getText())) . '</i>';
							break;
						default:
							if (!$item->getText())
							{
								echo '<i>' .__('Issue updated') . '</i>';
							}
							else
							{
								echo '<i>' .$item->getText() . '</i>';
							}
							break;
					}

				?>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
	<br>
	<div style="color: #888;">
		Show issue: <?php echo link_tag($module->generateURL('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo()))); ?><br>
		<?php if (isset($comment) && $comment instanceof TBGComment): ?>
			Show comment: <?php echo link_tag($module->generateURL('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())).'#comment_'.$comment->getID()); ?><br>
		<?php endif; ?>
		Show <?php echo $issue->getProject()->getName(); ?> project dashboard: <?php echo link_tag($module->generateURL('project_dashboard', array('project_key' => $issue->getProject()->getKey()))); ?><br>
		<br>
		You were sent this notification email because you are related to the issue mentioned in this email.<br>
		To change when and how often we send these emails, update your account settings: <?php echo link_tag($module->generateURL('account'), $module->generateURL('account')); ?>
	</div>
<?php endif; ?>