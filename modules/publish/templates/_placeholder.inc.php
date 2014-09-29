<div class="article_placeholder">
    <?php echo __('This article has not been created yet.'); ?>
    <?php if (!isset($nocreate) || $nocreate == false): ?>
        <?php echo __('You can create this article by clicking %create_this_article below.', array('%create_this_article' => '<b>'.__('Create this article').'</b>')); ?>
    <?php endif; ?>
</div>
