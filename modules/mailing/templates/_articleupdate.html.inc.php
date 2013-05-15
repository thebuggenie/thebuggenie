<?php if ($article instanceof TBGWikiArticle): ?>
	<h3><?php echo $article->getTitle(); ?> updated</h3>
	<h4>The article has been updated by <?php echo $user->getBuddyname(); ?> (<?php echo $user->getUsername(); ?>)</h4>
	<?php if (trim($change_reason) != ''): ?>
		<pre><?php echo $change_reason; ?></pre><br>
	<?php else: ?>
		<div style="color: #AAA;">No change reason provided</div>
	<?php endif; ?>
	<br>
	<div style="color: #888;">
		Show article: <?php echo link_tag($module->generateURL('publish_article', array('article_name' => $article->getTitle()))); ?><br>
		Show changes: <?php echo link_tag($module->generateURL('publish_article_diff', array('article_name' => $article->getTitle(), 'from_revision' => $revision - 1, 'to_revision' => $revision))); ?><br>
		<br>
		You were sent this notification email because you are related to the article mentioned in this email.<br>
		To change when and how often we send these emails, update your account settings: <?php echo link_tag($module->generateURL('account'), $module->generateURL('account')); ?>
	</div>
<?php endif; ?>