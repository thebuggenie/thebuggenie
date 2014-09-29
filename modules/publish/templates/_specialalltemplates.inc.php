<div class="article">
    <div class="header">Special:<?php echo ($projectnamespace != '') ? "<span class='faded_out'>{$projectnamespace}</span>" : ''; ?>All Templates</div>
    <?php if (TBGContext::isProjectContext()): ?>
        <div class="greybox" style="margin: 15px;">
            <?php echo __('Note: This page lists all templates for "%project_name". For a list of global templates, see %all_templates', array('%project_name' => TBGContext::getCurrentProject()->getName(), '%all_templates' => link_tag(make_url('publish_article', array('article_name' => "Special:AllTemplates")), 'Special:AllTemplates'))); ?>
        </div>
    <?php endif; ?>
    <p>
        <?php echo __('Below is a listing of all templates.'); ?>
    </p>
    <?php include_template('publish/articleslist', compact('articles')); ?>
</div>
