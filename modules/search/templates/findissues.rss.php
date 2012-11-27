<<?php ?>?xml version="1.0" encoding="<?php echo TBGContext::getI18n()->getCharset(); ?>" ?>
<rss version="2.0">
	<channel>
		<title><?php echo TBGSettings::getTBGname() . ' ~ '. $searchtitle; ?></title>
		<link><?php echo make_url('home', array(), false); ?></link>
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
			<title><?php echo TBGSettings::getTBGname() . ' ~ '. $searchtitle; ?></title>
			<link><?php echo make_url('home', array(), false); ?></link>
		</image>
<?php if ($issues != false): ?>
<?php foreach ($issues as $issue): ?>
		
		<item>
			<title><?php echo $issue->getFormattedIssueNo(true) . ' - ' . strip_tags($issue->getTitle()); ?></title>
			<?php if ($issue->getDescription() == ''): ?>
			<description><?php echo __('Nothing entered.'); ?></description>
			<?php else: ?>
			<description><![CDATA[<?php echo strip_tags($issue->getDescription()); ?>]]></description>
			<?php endif; ?>
			<pubDate><?php echo tbg_formatTime($issue->getLastUpdatedTime(), 21); ?></pubDate>
			<link><?php echo make_url('viewissue', array('issue_no' => $issue->getFormattedIssueNo(), 'project_key' => $issue->getProject()->getKey()), false); ?></link>
			<guid><?php echo make_url('viewissue', array('issue_no' => $issue->getFormattedIssueNo(), 'project_key' => $issue->getProject()->getKey()), false); ?></guid>
		</item>
<?php endforeach; ?>
<?php endif; ?>
	</channel>
</rss>