<?php \thebuggenie\core\framework\Context::loadLibrary('publish/publish'); ?>
<div class="article syntax_<?php echo \thebuggenie\core\framework\Settings::getSyntaxClass($article->getContentSyntax()); ?>">
    <?php if ($show_title): ?>
        <?php include_component('publish/header', array('article_name' => $article->getName(), 'article' => $article, 'show_actions' => $show_actions, 'mode' => $mode, 'embedded' => $embedded)); ?>
    <?php endif; ?>
    <?php if ($show_details && $show_article): ?>
        <div class="details">
            <?php if (isset($redirected_from)): ?>
                <div class="redirected_from">&rarr; <?php echo __('Redirected from %article_name', array('%article_name' => link_tag(make_url('publish_article_edit', array('article_name' => $redirected_from)), $redirected_from))); ?></div>
            <?php endif; ?>
            <?php echo __('Last updated at %time, by %user', array('%time' => tbg_formatTime($article->getPostedDate(), 3), '%user' => '<b>'.(($article->getAuthor() instanceof \thebuggenie\core\entities\common\Identifiable) ? '<a href="javascript:void(0);" onclick="TBG.Main.Helpers.Backdrop.show(\'' . make_url('get_partial_for_backdrop', array('key' => 'usercard', 'user_id' => $article->getAuthor()->getID())) . '\');" class="faded_out">' . $article->getAuthor()->getName() . '</a>' : __('System')).'</b>')); ; ?>
        </div>
    <?php endif; ?>
    <?php if ($show_article): ?>
        <div class="content"><?php echo $article->getParsedContent(array('embedded' => $embedded, 'article' => $article)); ?></div>
    <?php endif; ?>
</div>
<?php if ($article->isCategory() && !$embedded && $show_category_contains): ?>
    <br style="clear: both;">
    <div style="margin: 15px 5px 5px 5px; clear: both;">
        <?php if (count($article->getSubCategories()) > 0): ?>
            <div class="header"><?php echo __('Subcategories'); ?></div>
            <ul class="category_list">
                <?php foreach ($article->getSubCategories() as $subcategory): ?>
                    <li><?php echo link_tag(make_url('publish_article', array('article_name' => $subcategory->getName())), $subcategory->getCategoryName()); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <div class="faded_out"><?php echo __("This category doesn't have any subcategories"); ?></div>
        <?php endif; ?>
    </div>
    <br style="clear: both;">
    <div style="margin: 15px 5px 5px 5px;">
        <?php if (count($article->getCategoryArticles()) > 0): ?>
            <div class="header"><?php echo __('Pages in this category'); ?></div>
            <ul class="category_list">
                <?php foreach ($article->getCategoryArticles() as $categoryarticle): ?>
                    <li><?php echo link_tag(make_url('publish_article', array('article_name' => $categoryarticle->getName())), $categoryarticle->getSpacedName()); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <div class="faded_out"><?php echo __('There are no pages in this category'); ?></div>
        <?php endif; ?>
    </div>
    <br style="clear: both;">
<?php endif; ?>
<?php if (!$embedded && $show_article && count($article->getCategories()) > 0 && $article->getContentSyntax() == \thebuggenie\core\framework\Settings::SYNTAX_MW): ?>
    <br style="clear: both;">
    <div class="greybox categories">
        <?php echo __('Categories:'); ?>
        <?php $category_links = array(); ?>
        <?php foreach ($article->getCategories() as $category): ?>
            <?php $category_links[] = link_tag(make_url('publish_article', array('article_name' => 'Category:'.$category)), $category); ?>
        <?php endforeach; ?>
        <?php echo join(', ', $category_links); ?>
    </div>
<?php endif; ?>
