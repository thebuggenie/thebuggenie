<?php if ($article instanceof \thebuggenie\modules\publish\entities\Article && $comment instanceof \thebuggenie\core\entities\Comment): ?>
    <h3><?php echo $article->getTitle(); ?></h3>
    <br>
    <h4><?php echo __('Comment by %name', array('%name' => $comment->getPostedBy()->getNameWithUsername())); ?></h4>
    <p><?php echo $comment->getParsedContent(); ?></p>
    <br>
    <div style="color: #888;">
        <?php echo __('Show article:') . ' ' . link_tag($module->generateURL('publish_article', array('article_name' => $article->getTitle()))); ?><br>
        <?php echo __('Show comment:') . ' ' . link_tag($module->generateURL('publish_article', array('article_name' => $article->getTitle())).'#comment_'.$comment->getID()); ?><br>
        <br>
        <?php echo __('You were sent this notification email because you are related to the article mentioned in this email.')?><br>
        <?php echo __('To change when and how often we send these emails, update your account settings:') . ' ' . link_tag($module->generateURL('account'), $module->generateURL('account')); ?>
    </div>
<?php endif; ?>
