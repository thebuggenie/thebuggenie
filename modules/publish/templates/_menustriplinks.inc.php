<li class="with-dropdown <?php if (strpos($selected_tab, 'publish_') === 0): ?>selected<?php endif; ?>">
    <div class="menuitem_container">
        <?php if (!isset($wiki_url)): ?>
            <?php echo link_tag(((isset($project_url)) ? $project_url : $url), fa_image_tag('newspaper-o', array(), false, 'publish') . \thebuggenie\core\framework\Context::getModule('publish')->getMenuTitle($project instanceof \thebuggenie\core\entities\Project)); ?>
        <?php else: ?>
            <?php echo link_tag($wiki_url, \thebuggenie\core\framework\Context::getModule('publish')->getMenuTitle($project instanceof \thebuggenie\core\entities\Project), array('target' => 'blank')) ?>
        <?php endif; ?>
        <?php if (count(\thebuggenie\core\entities\Project::getAll())): ?>
            <?php echo javascript_link_tag(image_tag('tabmenu_dropdown.png', array('class' => 'menu_dropdown'))); ?>
        <?php endif; ?>
    </div>
    <?php if (count(\thebuggenie\core\entities\Project::getAll())): ?>
        <div id="wiki_dropdown_menu" class="tab_menu_dropdown">
            <?php if ($project instanceof \thebuggenie\core\entities\Project): ?>
            <div class="header"><?php echo $project->getName(); ?></div>
                <?php if (!isset($wiki_url)): ?>
                    <?php echo link_tag($project_url, __('Project wiki frontpage')); ?>
                    <?php $quicksearch_title = __('Find project article (press enter to search)'); ?>
                    <div style="font-weight: normal; margin: 0 0 15px 5px; padding: 0 10px 0 0;">
                        <form action="<?php echo make_url('publish_find_project_articles', array('project_key' => $project->getName())); ?>" method="get" accept-charset="<?php echo \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>">
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
            <?php if (count(\thebuggenie\core\entities\Project::getAll()) > (int) ($project instanceof \thebuggenie\core\entities\Project)): ?>
                <div class="header"><?php echo __('Project wikis'); ?></div>
                <?php foreach (\thebuggenie\core\entities\Project::getAll() as $project): ?>
                    <?php if (!$project->hasAccess() || (isset($project_url) && $project->getID() == $project->getID())) continue; ?>
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
