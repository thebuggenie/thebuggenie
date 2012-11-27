<<?php ?>?xml version="1.0" encoding="<?php echo TBGContext::getI18n()->getCharset(); ?>" ?>
<rss version="2.0">
	<channel>
		<title><?php echo TBGSettings::getTBGname() . ' ~ '. __('%project_name% project timeline', array('%project_name%' => TBGContext::getCurrentProject()->getName())); ?></title>
		<link><?php echo make_url('project_timeline', array('project_key' => TBGContext::getCurrentProject()->getKey()), false); ?></link>
		<description><?php echo strip_tags(TBGSettings::getTBGname()); ?></description>
		<language><?php echo (mb_strtolower(str_replace('_', '-', TBGContext::getI18n()->getCurrentLanguage()))); ?></language>
		<image>
		<?php if (TBGSettings::isUsingCustomHeaderIcon() == '2'): ?>
			<url><?php echo TBGSettings::getHeaderIconURL(); ?></url>
		<?php elseif (TBGSettings::isUsingCustomHeaderIcon() == '1'): ?>
			<url><?php echo TBGContext::getUrlHost().TBGContext::getTBGPath().'header.png'; ?></url>
		<?php else: ?>
			<url><?php echo image_url('logo_24.png', false, null, false); ?></url>
		<?php endif; ?>
			<title><?php echo TBGSettings::getTBGname() . ' ~ '. __('%project_name% project timeline', array('%project_name%' => TBGContext::getCurrentProject()->getName())); ?></title>
			<link><?php echo make_url('project_timeline', array('project_key' => TBGContext::getCurrentProject()->getKey()), false); ?></link>
		</image>
<?php foreach ($recent_activities as $timestamp => $activities): ?>
<?php foreach ($activities as $activity): ?>
<?php if (array_key_exists('target_type', $activity) && $activity['target_type'] == 1 && ($issue = TBGContext::factory()->TBGIssue($activity['target'])) && $issue instanceof TBGIssue): ?>
<?php if ($issue->isDeleted()): continue; endif; ?>
		<item>
			<title><![CDATA[
				<?php
					$activity['text'] = str_replace("&rArr;", '->', html_entity_decode($activity['text']));
					switch ($activity['change_type'])
					{
						case TBGLogTable::LOG_ISSUE_CREATED:
							echo __('Issue created');
							break;
						case TBGLogTable::LOG_ISSUE_CLOSE:
							echo __('Issue closed %text%', array('%text%' => $activity['text']));
							break;
						case TBGLogTable::LOG_ISSUE_REOPEN:
							echo __('Issue reopened');
							break;
						case TBGLogTable::LOG_ISSUE_UPDATE:
							echo $activity['text'];
							break;
						case TBGLogTable::LOG_ISSUE_CATEGORY:
							echo __('Category changed: %text%', array('%text%' => $activity['text']));
							break;
						case TBGLogTable::LOG_ISSUE_CUSTOMFIELD_CHANGED:
							echo __('Custom field changed: %text%', array('%text%' => $activity['text']));
							break;
						case TBGLogTable::LOG_ISSUE_STATUS:
							echo __('Status changed: %text%', array('%text%' => $activity['text']));
							break;
						case TBGLogTable::LOG_ISSUE_REPRODUCABILITY:
							echo __('Reproducability changed: %text%', array('%text%' => $activity['text']));
							break;
						case TBGLogTable::LOG_ISSUE_PRIORITY:
							echo __('Priority changed: %text%', array('%text%' => $activity['text']));
							break;
						case TBGLogTable::LOG_ISSUE_SEVERITY:
							echo __('Severity changed: %text%', array('%text%' => $activity['text']));
							break;
						case TBGLogTable::LOG_ISSUE_RESOLUTION:
							echo __('Resolution changed: %text%', array('%text%' => $activity['text']));
							break;
						case TBGLogTable::LOG_ISSUE_PERCENT:
							echo __('Percent completed: %text%', array('%text%' => $activity['text']));
							break;
						case TBGLogTable::LOG_ISSUE_MILESTONE:
							echo __('Target milestone changed: %text%', array('%text%' => $activity['text']));
							break;
						case TBGLogTable::LOG_ISSUE_ISSUETYPE:
							echo __('Issue type changed: %text%', array('%text%' => $activity['text']));
							break;
						case TBGLogTable::LOG_ISSUE_TIME_ESTIMATED:
							echo __('Estimation changed: %text%', array('%text%' => $activity['text']));
							break;
						case TBGLogTable::LOG_ISSUE_TIME_SPENT:
							echo __('Time spent: %text%', array('%text%' => $activity['text']));
							break;
						case TBGLogTable::LOG_ISSUE_ASSIGNED:
							echo __('Assignee changed: %text%', array('%text%' => $activity['text']));
							break;
						case TBGLogTable::LOG_ISSUE_OWNED:
							echo __('Owner changed: %text%', array('%text%' => $activity['text']));
							break;
						case TBGLogTable::LOG_ISSUE_POSTED:
							echo __('Posted by changed: %text%', array('%text%' => $activity['text']));
							break;
						default:
							if (empty($activity['text']))
							{
								echo __('Issue updated');
							}
							else
							{
								echo $activity['text'];
							}
							break;
					}

				?>: <?php echo $issue->getFormattedIssueNo(true) . ' - ' . $issue->getTitle(); ?>]]></title>
			<description><![CDATA[<?php echo strip_tags($issue->getDescription()); ?>]]></description>
			<pubDate><?php echo tbg_formatTime($issue->getLastUpdatedTime(), 21); ?></pubDate>
			<link><?php echo make_url('viewissue', array('issue_no' => $issue->getFormattedIssueNo(), 'project_key' => $issue->getProject()->getKey()), false); ?></link>
			<guid isPermaLink="false"><?php echo sha1($timestamp.$activity['text']); ?></guid>
		</item>
		
<?php endif; ?>
<?php endforeach; ?>
<?php endforeach; ?>

	</channel>
</rss>