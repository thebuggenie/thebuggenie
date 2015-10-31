<div class="article">
    <div class="header">Special:<?php echo ($projectnamespace != '') ? "<span class='faded_out'>{$projectnamespace}</span>" : ''; ?>All Categories</div>
    <?php if (\thebuggenie\core\framework\Context::isProjectContext()): ?>
        <div class="greybox" style="margin: 15px 0;">
            <?php echo __('Note: This page lists all categories for "%project_name". For a list of global categories, see %all_categories', array('%project_name' => \thebuggenie\core\framework\Context::getCurrentProject()->getName(), '%all_categories' => link_tag(make_url('publish_article', array('article_name' => "Special:AllCategories")), 'Special:AllCategories'))); ?>
        </div>
    <?php endif; ?>
    <p>
        <?php echo __('Below is a listing of all categories.'); ?>
    </p>
    <?php include_component('publish/articleslist', compact('articles')); ?>
</div>
