<?php if ($action['target_type'] == 1 && ($theIssue = TBGFactory::TBGIssueLab($action['target'])) && $theIssue instanceof TBGIssue): ?>
	<tr>
		<td class="imgtd"><?php echo image_tag($theIssue->getIssueType()->getIcon() . '_tiny.png'); ?></td>
		<td style="padding-bottom: <?php if (isset($extra_padding) && $extra_padding == true): ?>10<?php else: ?>5<?php endif; ?>px;">
			<?php if (isset($include_time) && $include_time == true): ?><span class="time"><?php echo tbg_formatTime($action['timestamp'], 19); ?></span>&nbsp;<?php endif; ?>
			<?php if (isset($include_project) && $include_project == true): ?><span class="faded_medium smaller"><?php echo link_tag(make_url('project_dashboard', array('project_key' => $theIssue->getProject()->getKey())), '['.$theIssue->getProject()->getKey().']'); ?></span><?php endif; ?>
			<?php echo link_tag(make_url('viewissue', array('project_key' => $theIssue->getProject()->getKey(), 'issue_no' => $theIssue->getFormattedIssueNo())), $theIssue->getFormattedTitle(true), array('class' => (($action['change_type'] == TBGLogTable::LOG_ISSUE_CLOSE) ? 'issue_closed' : 'issue_open'))); ?>
			<?php if (isset($include_user) && $include_user == true): ?>
				<br>
				<span class="user"><?php if (($user = TBGFactory::userLab($action['user_id'])) instanceof TBGUser): ?><?php echo $user->getUsername(); ?><?php else: ?><span class="faded"><?php echo __('Unknown user'); ?></span><?php endif; ?>:</span>
			<?php else: ?>
				<br>
			<?php endif; ?>
			<?php

				switch ($action['change_type'])
				{
					case TBGLogTable::LOG_ISSUE_CREATED:
						echo '<i>' . __('Issue created') . '</i>';
						echo '<div class="timeline_inline_details">'.$theIssue->getDescription().'</div>';
						break;
					case TBGLogTable::LOG_ISSUE_CLOSE:
						echo '<span class="issue_closed"><i>' . __('Issue closed %text%', array('%text%' => $action['text'])) . '</i></span>';
						break;
					case TBGLogTable::LOG_ISSUE_REOPEN:
						echo '<i>' . __('Issue reopened') . '</i>';
						break;
					case TBGLogTable::LOG_ISSUE_UPDATE:
						echo '<i>' . $action['text'] . '</i>';
						break;
					case TBGLogTable::LOG_ISSUE_PAIN_BUG_TYPE:
						echo '<i>' . __('Triaged bug type: %text%', array('%text%' => $action['text'])) . '</i>';
						break;
					case TBGLogTable::LOG_ISSUE_PAIN_LIKELIHOOD:
						echo '<i>' . __('Triaged likelihood: %text%', array('%text%' => $action['text'])) . '</i>';
						break;
					case TBGLogTable::LOG_ISSUE_PAIN_EFFECT:
						echo '<i>' . __('Triaged effect: %text%', array('%text%' => $action['text'])) . '</i>';
						break;
					case TBGLogTable::LOG_ISSUE_PAIN_CALCULATED:
						echo '<i>' . __('Calculated user pain: %text%', array('%text%' => $action['text'])) . '</i>';
						break;
					case TBGLogTable::LOG_ISSUE_CATEGORY:
						echo '<i>' . __('Category changed: %text%', array('%text%' => $action['text'])) . '</i>';
						break;
					case TBGLogTable::LOG_ISSUE_CUSTOMFIELD_CHANGED:
						echo '<i>' . __('Custom field changed: %text%', array('%text%' => $action['text'])) . '</i>';
						break;
					case TBGLogTable::LOG_ISSUE_STATUS:
						echo '<i>' . __('Status changed: %text%', array('%text%' => $action['text'])) . '</i>';
						break;
					case TBGLogTable::LOG_ISSUE_REPRODUCABILITY:
						echo '<i>' . __('Reproducability changed: %text%', array('%text%' => $action['text'])) . '</i>';
						break;
					case TBGLogTable::LOG_ISSUE_PRIORITY:
						echo '<i>' . __('Priority changed: %text%', array('%text%' => $action['text'])) . '</i>';
						break;
					case TBGLogTable::LOG_ISSUE_SEVERITY:
						echo '<i>' . __('Severity changed: %text%', array('%text%' => $action['text'])) . '</i>';
						break;
					case TBGLogTable::LOG_ISSUE_RESOLUTION:
						echo '<i>' . __('Resolution changed: %text%', array('%text%' => $action['text'])) . '</i>';
						break;
					case TBGLogTable::LOG_ISSUE_PERCENT:
						echo '<i>' . __('Percent completed: %text%', array('%text%' => $action['text'])) . '</i>';
						break;
					case TBGLogTable::LOG_ISSUE_MILESTONE:
						echo '<i>' . __('Target milestone changed: %text%', array('%text%' => $action['text'])) . '</i>';
						break;
					case TBGLogTable::LOG_ISSUE_ISSUETYPE:
						echo '<i>' . __('Issue type changed: %text%', array('%text%' => $action['text'])) . '</i>';
						break;
					case TBGLogTable::LOG_ISSUE_TIME_ESTIMATED:
						echo '<i>' . __('Estimation changed: %text%', array('%text%' => $action['text'])) . '</i>';
						break;
					case TBGLogTable::LOG_ISSUE_TIME_SPENT:
						echo '<i>' . __('Time spent: %text%', array('%text%' => $action['text'])) . '</i>';
						break;
					case TBGLogTable::LOG_ISSUE_ASSIGNED:
						echo '<i>' . __('Assignee changed: %text%', array('%text%' => $action['text'])) . '</i>';
						break;
					case TBGLogTable::LOG_ISSUE_OWNED:
						echo '<i>' . __('Owner changed: %text%', array('%text%' => $action['text'])) . '</i>';
						break;
					case TBGLogTable::LOG_ISSUE_POSTED:
						echo '<i>' . __('Posted by changed: %text%', array('%text%' => $action['text'])) . '</i>';
						break;
					default:
						break;
				}

			?>
		</td>
	</tr>
<?php endif; ?>