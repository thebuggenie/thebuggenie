<div class="container_div toggled<?php if (count($whatlinkshere) == 0) echo ' visible'; ?>">
    <div class="header" onclick="if ($('nothing_links_here') == undefined) $(this).up().toggleClassName('visible');">
        <?php echo __('Links to this article'); ?>
    </div>
    <div class="toggle_info"><?php echo __('Click the header to show / hide'); ?></div>
    <div class="content">
        <?php if (count($whatlinkshere) > 0): ?>
            <ul class="article_list">
                <?php foreach ($whatlinkshere as $linking_article): ?>
                    <li>
                        <?php echo image_tag('news_item_medium.png', array('style' => 'float: left;'), false, 'publish'); ?>
                        <?php echo link_tag(make_url('publish_article', array('article_name' => $linking_article->getName())), get_spaced_name($linking_article->getTitle())); ?>
                        <br>
                        <span><?php echo __('%time, by %user', array('%time' => tbg_formatTime($linking_article->getPostedDate(), 3), '%user' => '<b>'.(($linking_article->getAuthor() instanceof \thebuggenie\core\entities\common\Identifiable) ? '<a href="javascript:void(0);" onclick="TBG.Main.Helpers.Backdrop.show(\'' . make_url('get_partial_for_backdrop', array('key' => 'usercard', 'user_id' => $linking_article->getAuthor()->getID())) . '\');">' . $linking_article->getAuthor()->getName() . '</a>' : __('System')).'</b>')); ; ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <div id="nothing_links_here" class="no_items"><?php echo __("No other articles links to this article"); ?></div>
        <?php endif; ?>
    </div>
</div>
