* <?php echo $article->getTitle(); ?> *

A comment has been posted:

* Comment by <?php echo $comment->getPostedBy()->getBuddyname(); ?> (<?php echo $comment->getPostedBy()->getUsername(); ?>) *
<?php echo tbg_parse_text($comment->getContent()); ?>

Show article: <?php echo $module->generateURL('publish_article', array('article_name' => $article->getTitle()))."\n"; ?>
Show comment: <?php echo $module->generateURL('publish_article', array('article_name' => $article->getTitle())).'#comment_'.$comment->getID()."\n"; ?>

You were sent this notification email because you are related to the article mentioned in this email.
To change when and how often we send these emails, update your account settings: <?php echo link_tag($module->generateURL('account'), $module->generateURL('account')); ?>