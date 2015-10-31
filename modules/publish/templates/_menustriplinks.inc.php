<li<?php if (strpos($selected_tab, 'publish_') === 0): ?> class="selected"<?php endif; ?>>
    <div class="menuitem_container">
        <?php if (!isset($wiki_url)): ?>
            <?php echo link_tag(((isset($project_url)) ? $project_url : $url), image_tag('tab_publish.png', array(), false, 'publish') . \thebuggenie\core\framework\Context::getModule('publish')->getMenuTitle()); ?>
        <?php else: ?>
            <?php echo link_tag($wiki_url, \thebuggenie\core\framework\Context::getModule('publish')->getMenuTitle(), array('target' => 'blank')) ?>
        <?php endif; ?>
        <?php if (count(\thebuggenie\core\entities\Project::getAll())): ?>
            <?php echo javascript_link_tag(image_tag('tabmenu_dropdown.png', array('class' => 'menu_dropdown'))); ?>
        <?php endif; ?>
    </div>
    <?php if (count(\thebuggenie\core\entities\Project::getAll())): ?>
        <div id="wiki_dropdown_menu" class="tab_menu_dropdown">
            <?php if (\thebuggenie\core\framework\Context::isProjectContext()): ?>
            <div class="header"><?php echo \thebuggenie\core\framework\Context::getCurrentProject()->getName(); ?></div>
                <?php if (!isset($wiki_url)): ?>
                    <?php echo link_tag($project_url, __('Project wiki frontpage')); ?>
                    <?php $quicksearch_title = __('Find project article (press enter to search)'); ?>
                    <div style="font-weight: normal; margin: 0 0 15px 5px;">
                        <form action="<?php echo make_url('publish_find_project_articles', array('project_key' => \thebuggenie\core\framework\Context::getCurrentProject()->getName())); ?>" method="get" accept-charset="<?php echo \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>">
                            <input type="search" name="articlename" placeholder="<?php echo $quicksearch_title; ?>">
                        </form>
                    </div>
                <?php else: ?>
                    <?php echo link_tag($wiki_url, __('Project wiki frontpage'), array('target' => 'blank')) ?>
                <?php endif; ?>
            <?php endif; ?>
            <div class="header"><?php echo __('Global content'); ?></div>
            <?php echo link_tag($url, \thebuggenie\core\framework\Context::getModule('publish')->getMenuTitle(false)); ?>
            <?php $quicksearch_title = __('Find any article (press enter to search)'); ?>
            <div style="font-weight: normal; margin: 0 0 15px 5px;">
                <form action="<?php echo make_url('publish_find_articles'); ?>" method="get" accept-charset="<?php echo \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>">
                    <input type="search" name="articlename" placeholder="<?php echo $quicksearch_title; ?>">
                </form>
            </div>
            <?php if (count(\thebuggenie\core\entities\Project::getAll()) > (int) \thebuggenie\core\framework\Context::isProjectContext()): ?>
                <div class="header"><?php echo __('Project wikis'); ?></div>
                <?php foreach (\thebuggenie\core\entities\Project::getAll() as $project): ?>
                    <?php if (!$project->hasAccess() || (isset($project_url) && $project->getID() == \thebuggenie\core\framework\Context::getCurrentProject()->getID())) continue; ?>
                    <?php if (!$project->hasWikiURL()): ?>
                        <?php echo link_tag(make_url('publish_article', array('article_name' => $project->getName().':MainPage')), $project->getName()); ?>
                    <?php else: ?>
                        <?php echo link_tag($project->getWikiURL(), $project->getName(), array('target' => 'blank')) ?>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</li>
