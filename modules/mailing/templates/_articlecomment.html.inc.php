<?php if ($article instanceof TBGWikiArticle && $comment instanceof TBGComment): ?>
	<h3><?php echo $article->getTitle(); ?></h3>
	<br>
	<h4>Comment by <?php echo $comment->getPostedBy()->getBuddyname(); ?> (<?php echo $comment->getPostedBy()->getUsername(); ?>)</h4>
	<p><?php echo tbg_parse_text($comment->getContent()); ?></p>
	<br>
	<div style="color: #888;">
		Show article: <?php echo link_tag($module->generateURL('publish_article', array('article_name' => $article->getTitle()))); ?><br>
		Show comment: <?php echo link_tag($module->generateURL('publish_article', array('article_name' => $article->getTitle())).'#comment_'.$comment->getID()); ?><br>
		<br>
		You were sent this notification email because you are related to the article mentioned in this email.<br>
		To change when and how often we send these emails, update your account settings: <?php echo link_tag($module->generateURL('account'), $module->generateURL('account')); ?>
	</div>
<?php endif; ?>