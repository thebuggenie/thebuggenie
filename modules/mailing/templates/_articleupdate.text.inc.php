* <?php echo __('%article updated', array('%article' => $article->getTitle())); ?> *

<?php echo __('The article has been updated by %name', array('%name' => $user->getNameWithUsername())); ?>

<?php echo (trim($change_reason) != '') ? '"'.$change_reason.'"' : '(' . __('No change reason provided') .')'; echo "\n"; ?>

<?php echo __('Show article:') . ' ' . $module->generateURL('publish_article', array('article_name' => $article->getTitle()))."\n"; ?>
<?php echo __('Show changes:') . ' ' . $module->generateURL('publish_article_diff', array('article_name' => $article->getTitle(), 'from_revision' => $revision - 1, 'to_revision' => $revision))."\n"; ?>


<?php echo __('You were sent this notification email because you are related to the article mentioned in this email.')?>

<?php echo __('To change when and how often we send these emails, update your account settings:') . ' '. $module->generateURL('account'); ?>
