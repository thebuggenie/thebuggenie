* <?php echo $article->getTitle(); ?> updated *

The article has been updated by <?php echo $user->getBuddyname(); ?> (<?php echo $user->getUsername(); ?>):
"<?php echo $change_reason; ?>"

Show article: <?php echo $module->generateURL('publish_article', array('article_name' => $article->getTitle()))."\n"; ?>
Show changes: <?php echo $module->generateURL('publish_article_diff', array('article_name' => $article->getTitle(), 'from_revision' => $revision - 1, 'to_revision' => $revision))."\n"; ?>

You were sent this notification email because you are related to the article mentioned in this email.
To change when and how often we send these emails, update your account settings: <?php echo link_tag($module->generateURL('account'), $module->generateURL('account')); ?>