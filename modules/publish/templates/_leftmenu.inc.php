<?php include_component('main/menulinks', array('links' => $links, 'target_type' => 'wiki', 'target_id' => $links_target_id, 'title' => __('Wiki menu'))); ?>
<?php if ($article instanceof \thebuggenie\modules\publish\entities\Article && $article->getID()): ?>
    <?php include_component('publish/whatlinkshere', compact('article')); ?>
    <?php include_component('publish/tools', compact('special', 'article')); ?>
<?php endif; ?>
<?php include_component('publish/latestArticles'); ?>
