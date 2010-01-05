<<?php ?>?xml version="1.0" encoding="<?php echo BUGScontext::getI18n()->getCharset(); ?>" ?>
<rss version="2.0">
	<channel>
		<title><?php echo BUGSsettings::getTBGname() . ' ~ '. __('%project_name% project timeline', array('%project_name%' => BUGScontext::getCurrentProject()->getName())); ?></title>
		<link><?php echo make_url('project_timeline', array('project_key' => BUGScontext::getCurrentProject()->getKey()), false); ?></link>
		<description> </description>
		<language><?php echo BUGScontext::getI18n()->getCurrentLanguage(); ?></language>
		<image>
			<url><?php print BUGScontext::getTBGPath(); ?>themes/<?php print BUGSsettings::getThemeName(); ?>/favicon.png</url>
			<title><?php echo BUGSsettings::getTBGname() . ' ~ '. __('%project_name% project timeline', array('%project_name%' => BUGScontext::getCurrentProject()->getName())); ?></title>
			<link><?php echo make_url('project_timeline', array('project_key' => BUGScontext::getCurrentProject()->getKey()), false); ?></link>
		</image>
<?php foreach ($recent_activities as $timestamp => $activities): ?>
<?php foreach ($activities as $activity): ?>
<?php if ($activity['target_type'] == 1 && ($issue = BUGSfactory::BUGSissueLab($activity['target'])) && $issue instanceof BUGSissue): ?>

		<item>
			<title><?php

					switch ($activity['change_type'])
					{
						case B2tLog::LOG_ISSUE_CREATED:
							echo __('Issue created');
							break;
						case B2tLog::LOG_ISSUE_CLOSE:
							echo __('Issue closed %text%', array('%text%' => $activity['text']));
							break;
						case B2tLog::LOG_ISSUE_REOPEN:
							echo __('Issue reopened');
							break;
						case B2tLog::LOG_ISSUE_UPDATE:
							echo $activity['text'];
							break;
						case B2tLog::LOG_ISSUE_CATEGORY:
							echo __('Category changed: %text%', array('%text%' => $activity['text']));
							break;
						case B2tLog::LOG_ISSUE_CUSTOMFIELD_CHANGED:
							echo __('Custom field changed: %text%', array('%text%' => $activity['text']));
							break;
						case B2tLog::LOG_ISSUE_STATUS:
							echo __('Status changed: %text%', array('%text%' => $activity['text']));
							break;
						case B2tLog::LOG_ISSUE_REPRODUCABILITY:
							echo __('Reproducability changed: %text%', array('%text%' => $activity['text']));
							break;
						case B2tLog::LOG_ISSUE_PRIORITY:
							echo __('Priority changed: %text%', array('%text%' => $activity['text']));
							break;
						case B2tLog::LOG_ISSUE_SEVERITY:
							echo __('Severity changed: %text%', array('%text%' => $activity['text']));
							break;
						case B2tLog::LOG_ISSUE_RESOLUTION:
							echo __('Resolution changed: %text%', array('%text%' => $activity['text']));
							break;
						case B2tLog::LOG_ISSUE_PERCENT:
							echo __('Percent completed: %text%', array('%text%' => $activity['text']));
							break;
						case B2tLog::LOG_ISSUE_MILESTONE:
							echo __('Target milestone changed: %text%', array('%text%' => $activity['text']));
							break;
						case B2tLog::LOG_ISSUE_ISSUETYPE:
							echo __('Issue type changed: %text%', array('%text%' => $activity['text']));
							break;
						case B2tLog::LOG_ISSUE_TIME_ESTIMATED:
							echo __('Estimation changed: %text%', array('%text%' => $activity['text']));
							break;
						case B2tLog::LOG_ISSUE_TIME_SPENT:
							echo __('Time spent: %text%', array('%text%' => $activity['text']));
							break;
						case B2tLog::LOG_ISSUE_ASSIGNED:
							echo __('Assignee changed: %text%', array('%text%' => $activity['text']));
							break;
						case B2tLog::LOG_ISSUE_OWNED:
							echo __('Owner changed: %text%', array('%text%' => $activity['text']));
							break;
						case B2tLog::LOG_ISSUE_POSTED:
							echo __('Posted by changed: %text%', array('%text%' => $activity['text']));
							break;
						default:
							break;
					}

				?>: <?php echo $issue->getFormattedIssueNo(true) . ' - ' . $issue->getTitle(); ?></title>
			<description><?php echo strip_tags($issue->getDescription()); ?></description>
			<pubdate><?php echo bugs_formatTime($issue->getLastUpdatedTime(), 21); ?></pubdate>
			<link><?php echo make_url('viewissue', array('issue_no' => $issue->getFormattedIssueNo(), 'project_key' => $issue->getProject()->getKey()), false); ?></link>
			<guid><?php echo make_url('viewissue', array('issue_no' => $issue->getFormattedIssueNo(), 'project_key' => $issue->getProject()->getKey()), false); ?></guid>
		</item>
		
<?php endif; ?>
<?php endforeach; ?>
<?php endforeach; ?>

	</channel>
</rss>