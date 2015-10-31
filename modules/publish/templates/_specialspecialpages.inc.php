<div class="article">
    <div class="header"><?php echo __('Wiki special pages'); ?></div>
    <?php if (\thebuggenie\core\framework\Context::isProjectContext()): ?>
        <div class="greybox" style="margin: 15px 0;">
            <?php echo __('Note: This page lists all project-specific special pages for "%project_name". For a list of global special pages, see %special_pages', array('%project_name' => \thebuggenie\core\framework\Context::getCurrentProject()->getName(), '%special_pages' => link_tag(make_url('publish_article', array('article_name' => "Special:SpecialPages")), __('Special pages')))); ?>
        </div>
    <?php endif; ?>
    <p>
        <?php echo __('This is a list of all the "special pages" available in The Bug Genie wiki. These are generated automatically and cannot be edited via the builtin wiki-editor.'); ?>
    </p>
    <h3><?php echo __('Automatic pages'); ?></h3>
    <ul class="category_list">
    <?php if (!\thebuggenie\core\framework\Context::isProjectContext()): ?>
        <li><?php echo link_tag(make_url('publish_article', array('article_name' => "FrontpageArticle")), __('Frontpage article'), array('title' => "FrontpageArticle")); ?></li>
        <li><?php echo link_tag(make_url('publish_article', array('article_name' => "FrontpageLeftMenu")), __('Frontpage left menu footer'), array('title' => "FrontpageLeftMenu")); ?></li>
        <li><?php echo link_tag(make_url('publish_article', array('article_name' => "LoginIntro")), __('Login introduction header'), array('title' => "LoginIntro")); ?></li>
        <li><?php echo link_tag(make_url('publish_article', array('article_name' => "OpenidIntro")), __('OpenID login introduction header'), array('title' => "OpenidIntro")); ?></li>
        <li><?php echo link_tag(make_url('publish_article', array('article_name' => "RegistrationIntro")), __('Registration introduction header'), array('title' => "RegistrationIntro")); ?></li>
        <li><?php echo link_tag(make_url('publish_article', array('article_name' => "ForgottenPasswordIntro")), __('Forgotten password introduction header'), array('title' => "ForgottenPasswordIntro")); ?></li>
    <?php else: ?>
        <li><?php echo link_tag(make_url('publish_article', array('article_name' => \thebuggenie\core\framework\Context::getCurrentProject()->getKey().":MainPage")), __('Project wiki frontpage'), array('title' => \thebuggenie\core\framework\Context::getCurrentProject()->getKey().":MainPage")); ?></li>
    <?php endif; ?>
    </ul>
    <br style="clear: both;">
    <br style="clear: both;">
    <h3><?php echo __('Wiki maintenance'); ?></h3>
    <ul class="category_list">
        <li><?php echo link_tag(make_url('publish_article', array('article_name' => "Special:{$projectnamespace}DeadEndPages")), __('Dead end pages'), array('title' => "Special:{$projectnamespace}DeadEndPages")); ?></li>
        <li><?php echo link_tag(make_url('publish_article', array('article_name' => "Special:{$projectnamespace}UncategorizedPages")), __('Uncategorized pages'), array('title' => "Special:{$projectnamespace}UncategorizedPages")); ?></li>
        <li><?php echo link_tag(make_url('publish_article', array('article_name' => "Special:{$projectnamespace}OrphanedPages")), __('Orphaned pages'), array('title' => "Special:{$projectnamespace}OrphanedPages")); ?></li>
        <li><?php echo link_tag(make_url('publish_article', array('article_name' => "Special:{$projectnamespace}UncategorizedCategories")), __('Uncategorized categories'), array('title' => "Special:{$projectnamespace}UncategorizedCategories")); ?></li>
    </ul>
    <br style="clear: both;">
    <br style="clear: both;">
    <h3><?php echo __('Page lists'); ?></h3>
    <ul class="category_list">
        <li><?php echo link_tag(make_url('publish_article', array('article_name' => "Special:{$projectnamespace}AllPages")), __('All pages'), array('title' => "Special:{$projectnamespace}AllPages")); ?></li>
        <li><?php echo link_tag(make_url('publish_article', array('article_name' => "Special:{$projectnamespace}AllCategories")), __('All categories'), array('title' => "Special:{$projectnamespace}AllCategories")); ?></li>
        <li><?php echo link_tag(make_url('publish_article', array('article_name' => "Special:{$projectnamespace}AllTemplates")), __('All templates'), array('title' => "Special:{$projectnamespace}AllTemplates")); ?></li>
    </ul>
</div>
