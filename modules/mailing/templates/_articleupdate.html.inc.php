<?php if ($article instanceof \thebuggenie\modules\publish\entities\Article): ?>
    <h3><?php echo __('%article updated', array('%article' => $article->getTitle())); ?></h3>
    <h4><?php echo __('The article has been updated by %name', array('%name' => $user->getNameWithUsername())); ?></h4>
    <?php if (trim($change_reason) != ''): ?>
        <pre><?php echo $change_reason; ?></pre><br>
    <?php else: ?>
        <div style="color: #AAA;"><?php echo __('No change reason provided');?></div>
    <?php endif; ?>
    <br>
    <div style="color: #888;">
        <?php echo __('Show article:') . ' ' . link_tag($module->generateURL('publish_article', array('article_name' => $article->getTitle()))); ?><br>
        <?php echo __('Show changes:') . ' ' . link_tag($module->generateURL('publish_article_diff', array('article_name' => $article->getTitle(), 'from_revision' => $revision - 1, 'to_revision' => $revision))); ?><br>
        <br>
        <?php echo __('You were sent this notification email because you are related to the article mentioned in this email.'); ?><br>
        <?php echo __('To change when and how often we send these emails, update your account settings:') . ' ' . link_tag($module->generateURL('account'), $module->generateURL('account')); ?>
    </div>
<?php endif; ?>
