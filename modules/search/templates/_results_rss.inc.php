<<?php ?>?xml version="1.0" encoding="<?php echo TBGContext::getI18n()->getCharset(); ?>" ?>
<rss version="2.0">
	<channel>
		<title><?php echo TBGSettings::getTBGname() . ' ~ '. $searchtitle; ?></title>
		<link><?php echo make_url('home', array(), false); ?></link>
		<description> </description>
		<language><?php echo TBGContext::getI18n()->getCurrentLanguage(); ?></language>
		<image>
			<url><?php print TBGContext::getTBGPath(); ?>themes/<?php print TBGSettings::getThemeName(); ?>/favicon.png</url>
			<title><?php echo TBGSettings::getTBGname() . ' ~ '. $searchtitle; ?></title>
			<link><?php echo make_url('home', array(), false); ?></link>
		</image>
<?php foreach ($issues as $issue): ?>
		
		<item>
			<title><?php echo $issue->getFormattedIssueNo(true) . ' - ' . strip_tags($issue->getTitle()); ?></title>
			<description><?php echo strip_tags($issue->getDescription()); ?></description>
			<pubdate><?php echo tbg__formatTime($issue->getLastUpdatedTime(), 21); ?></pubdate>
			<link><?php echo make_url('viewissue', array('issue_no' => $issue->getFormattedIssueNo(), 'project_key' => $issue->getProject()->getKey()), false); ?></link>
			<guid><?php echo make_url('viewissue', array('issue_no' => $issue->getFormattedIssueNo(), 'project_key' => $issue->getProject()->getKey()), false); ?></guid>
		</item>
<?php endforeach; ?>
		
	</channel>
</rss>