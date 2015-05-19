* <?php echo $article->getTitle(); ?> *

<?php echo __('A comment has been posted:'); ?>


* <?php echo __('Comment by %name', array('%name' => $comment->getPostedBy()->getNameWithUsername())); ?> *
<?php echo $comment->getContent(); ?>


<?php echo __('Show article:') . ' ' . $module->generateURL('publish_article', array('article_name' => $article->getTitle()))."\n"; ?>
<?php echo __('Show comment:') . ' ' . $module->generateURL('publish_article', array('article_name' => $article->getTitle())).'#comment_'.$comment->getID()."\n"; ?>

<?php echo __('You were sent this notification email because you are related to the article mentioned in this email.')."\n"; ?>
<?php echo __('To change when and how often we send these emails, update your account settings:') . ' ' . $module->generateURL('account'); ?>
