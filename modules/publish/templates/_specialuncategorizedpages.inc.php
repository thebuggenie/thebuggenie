<div class="article">
    <div class="header">Special:<?php echo ($projectnamespace != '') ? "<span class='faded_out'>{$projectnamespace}</span>" : ''; ?>Uncategorized Pages</div>
    <?php if (\thebuggenie\core\framework\Context::isProjectContext()): ?>
        <div class="greybox" style="margin: 15px 0;">
            <?php echo __('Note: This page lists all articles without categories in "%project_name". For a list of global articles with no categories, see %uncategorized_pages', array('%project_name' => \thebuggenie\core\framework\Context::getCurrentProject()->getName(), '%uncategorized_pages' => link_tag(make_url('publish_article', array('article_name' => "Special:UncategorizedPages")), 'Special:UncategorizedPages'))); ?>
        </div>
    <?php endif; ?>
    <p>
        <?php echo __('Below is a listing of pages that have no categories.'); ?>
    </p>
    <?php include_component('publish/articleslist', array('articles' => $articles, 'include_redirects' => false)); ?>
</div>
