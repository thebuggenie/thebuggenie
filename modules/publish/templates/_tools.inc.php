<?php

    $article_name = ($article instanceof \thebuggenie\modules\publish\entities\Article) ? $article->getName() : $article;

?>
<div class="container_div toggled<?php if (isset($special) && $special) echo ' visible'; ?>">
    <div class="header" onclick="$(this).up().toggleClassName('visible');">
        <?php echo __('Wiki tools'); ?>
    </div>
    <div class="toggle_info"><?php echo __('Click the header to show / hide'); ?></div>
    <div class="content">
        <ul class="article_list special_pages">
            <?php if (!isset($special) || !$special): ?>
                <li>
                    <?php echo image_tag('news_item.png', array('style' => 'float: left;'), false, 'publish'); ?>
                    <?php echo link_tag(make_url('publish_special_whatlinkshere', array('linked_article_name' => $article_name)), __('What links here')); ?>
                </li>
            <?php endif; ?>
            <?php if (\thebuggenie\core\framework\Context::isProjectContext()): ?>
                <li>
                    <?php echo image_tag('news_item.png', array('style' => 'float: left;'), false, 'publish'); ?>
                    <?php echo link_tag(make_url('publish_article', array('article_name' => 'Special:'.ucfirst(mb_strtolower(\thebuggenie\core\framework\Context::getCurrentProject()->getKey())).':SpecialPages')), __('Project special pages')); ?>
                </li>
            <?php endif; ?>
            <li>
                <?php echo image_tag('news_item.png', array('style' => 'float: left;'), false, 'publish'); ?>
                <?php echo link_tag(make_url('publish_article', array('article_name' => 'Special:SpecialPages')), __('Special pages')); ?>
            </li>
        </ul>
    </div>
</div>
