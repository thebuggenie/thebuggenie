<?php if ($action['target_type'] == 1 && ($theIssue = BUGSfactory::BUGSissueLab($action['target'])) && $theIssue instanceof BUGSissue): ?>
	<tr>
		<td class="imgtd"><?php echo image_tag($theIssue->getIssueType()->getIcon() . '_tiny.png'); ?></td>
		<td style="padding-bottom: <?php if (isset($extra_padding) && $extra_padding == true): ?>10<?php else: ?>5<?php endif; ?>px;">
			<?php if (isset($include_time) && $include_time == true): ?><span class="time"><?php echo bugs_formatTime($action['timestamp'], 19); ?></span>&nbsp;<?php endif; ?>
			<?php echo link_tag(make_url('viewissue', array('project_key' => $theIssue->getProject()->getKey(), 'issue_no' => $theIssue->getFormattedIssueNo())), $theIssue->getFormattedIssueNo(true) . ' - ' . $theIssue->getTitle(), array('class' => (($action['change_type'] == B2tLog::LOG_ISSUE_CLOSE) ? 'issue_closed' : 'issue_open'))); ?>
			<?php

				switch ($action['change_type'])
				{
					case B2tLog::LOG_ISSUE_CREATED:
						echo '<br><i>' . __('Issue created') . '</i>';
						break;
					case B2tLog::LOG_ISSUE_CLOSE:
						echo '<br><span class="issue_closed"><i>' . __('Issue closed %text%', array('%text%' => $action['text'])) . '</i></span>';
						break;
					case B2tLog::LOG_ISSUE_REOPEN:
						echo '<br><i>' . __('Issue reopened') . '</i>';
						break;
					case B2tLog::LOG_ISSUE_UPDATE:
						echo '<br><i>' . $action['text'] . '</i>';
						break;
					case B2tLog::LOG_ISSUE_CATEGORY:
						echo '<br><i>' . __('Category changed: %text%', array('%text%' => $action['text'])) . '</i>';
						break;
					case B2tLog::LOG_ISSUE_CUSTOMFIELD_CHANGED:
						echo '<br><i>' . __('Custom field changed: %text%', array('%text%' => $action['text'])) . '</i>';
						break;
					case B2tLog::LOG_ISSUE_STATUS:
						echo '<br><i>' . __('Status changed: %text%', array('%text%' => $action['text'])) . '</i>';
						break;
					case B2tLog::LOG_ISSUE_REPRODUCABILITY:
						echo '<br><i>' . __('Reproducability changed: %text%', array('%text%' => $action['text'])) . '</i>';
						break;
					case B2tLog::LOG_ISSUE_PRIORITY:
						echo '<br><i>' . __('Priority changed: %text%', array('%text%' => $action['text'])) . '</i>';
						break;
					case B2tLog::LOG_ISSUE_SEVERITY:
						echo '<br><i>' . __('Severity changed: %text%', array('%text%' => $action['text'])) . '</i>';
						break;
					case B2tLog::LOG_ISSUE_RESOLUTION:
						echo '<br><i>' . __('Resolution changed: %text%', array('%text%' => $action['text'])) . '</i>';
						break;
					case B2tLog::LOG_ISSUE_PERCENT:
						echo '<br><i>' . __('Percent completed: %text%', array('%text%' => $action['text'])) . '</i>';
						break;
					case B2tLog::LOG_ISSUE_MILESTONE:
						echo '<br><i>' . __('Target milestone changed: %text%', array('%text%' => $action['text'])) . '</i>';
						break;
					case B2tLog::LOG_ISSUE_ISSUETYPE:
						echo '<br><i>' . __('Issue type changed: %text%', array('%text%' => $action['text'])) . '</i>';
						break;
					case B2tLog::LOG_ISSUE_TIME_ESTIMATED:
						echo '<br><i>' . __('Estimation changed: %text%', array('%text%' => $action['text'])) . '</i>';
						break;
					case B2tLog::LOG_ISSUE_TIME_SPENT:
						echo '<br><i>' . __('Time spent: %text%', array('%text%' => $action['text'])) . '</i>';
						break;
					case B2tLog::LOG_ISSUE_ASSIGNED:
						echo '<br><i>' . __('Assignee changed: %text%', array('%text%' => $action['text'])) . '</i>';
						break;
					case B2tLog::LOG_ISSUE_OWNED:
						echo '<br><i>' . __('Owner changed: %text%', array('%text%' => $action['text'])) . '</i>';
						break;
					case B2tLog::LOG_ISSUE_POSTED:
						echo '<br><i>' . __('Posted by changed: %text%', array('%text%' => $action['text'])) . '</i>';
						break;
					default:
						break;
				}

			?>
		</td>
	</tr>
<?php endif; ?>