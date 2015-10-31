<?php

    $children = $main_article->getChildArticles();
    $is_parent = in_array($main_article->getID(), $parents);
    $is_selected = $main_article->getID() == $article->getID() || ($main_article->isRedirect() && $main_article->getRedirectArticleName() == $article->getTitle());

    $is_first = $first;
    $first = false;

    $project_key = (\thebuggenie\core\framework\Context::isProjectContext()) ? \thebuggenie\core\framework\Context::getCurrentProject()->getKey() . ':' : '';
//    $article_name = (strpos(mb_strtolower($main_article->getTitle()), 'category:') !== false) ? substr($main_article->getTitle(), 9+mb_strlen($project_key)) : substr($main_article->getTitle(), mb_strlen($project_key));

?>
<li class="<?php echo (isset($level) && $level >= 1) ? 'child' : 'parent'; ?> <?php if ($is_parent && !$is_selected) echo 'parent'; ?> <?php if ($is_selected) echo 'selected'; ?> level_<?php echo $level; ?>" id="article_sidebar_link_<?php echo $article->getID(); ?>">
    <?php if (isset($level) && $level >= 1) echo image_tag('icon_tree_child.png', array('class' => 'branch')); ?>
    <?php if ($is_first && $main_article->getArticleType() == \thebuggenie\modules\publish\entities\Article::TYPE_MANUAL): ?>
        <?php echo image_tag('icon-article-type-manual.small.png'); ?>
    <?php else: ?>
        <?php echo (!empty($children)) ? image_tag('icon_folder.png', array(), false, 'publish') : image_tag('icon_article.png', array(), false, 'publish'); ?>
    <?php endif; ?>
    <?php echo link_tag(make_url('publish_article', array('article_name' => $main_article->getName())), $main_article->getManualName()); ?>
    <?php if ($is_parent || $is_selected): ?>
        <ul>
            <?php foreach ($children as $child_article): ?>
                <?php include_component('publish/manualsidebarlink', array('parents' => $parents, 'first' => $first, 'article' => $article, 'main_article' => $child_article, 'level' => $level + 1)); ?>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</li>
