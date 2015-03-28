<div class="article">
    <div class="header">Special:WhatLinksHere &rArr; <?php echo $linked_article_name; ?></div>
    <p>
        <?php echo __('Below is a listing of all pages that links to this pages.'); ?>
    </p>
    <?php include_component('publish/articleslist', array('articles' => $articles, 'include_redirects' => false)); ?>
</div>
