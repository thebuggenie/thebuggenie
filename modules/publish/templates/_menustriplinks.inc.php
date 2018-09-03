<li class="with-dropdown <?php if (strpos($selected_tab, 'publish_') === 0): ?>selected<?php endif; ?>">
    <?php $dropper_class = (count(\thebuggenie\core\entities\Project::getAllRootProjects())) ? 'dropper' : ''; ?>
    <?php if (!isset($wiki_url)): ?>
        <?php echo link_tag(((isset($project_url)) ? $project_url : $url), fa_image_tag('newspaper-o', array(), false, 'publish') . \thebuggenie\core\framework\Context::getModule('publish')->getMenuTitle($project instanceof \thebuggenie\core\entities\Project) . fa_image_tag('caret-down', ['class' => 'dropdown-indicator']), ['class' => $dropper_class]); ?>
    <?php else: ?>
        <?php echo link_tag($wiki_url, \thebuggenie\core\framework\Context::getModule('publish')->getMenuTitle($project instanceof \thebuggenie\core\entities\Project) . fa_image_tag('caret-down', ['class' => 'dropdown-indicator']), ['target' => 'blank', 'class' => $dropper_class]) ?>
    <?php endif; ?>
    <?php if (count(\thebuggenie\core\entities\Project::getAllRootProjects())): ?>
        <ul id="wiki_dropdown_menu" class="tab_menu_dropdown popup_box">
            <?php if ($project instanceof \thebuggenie\core\entities\Project): ?>
                <li class="header"><?php echo $project->getName(); ?></li>
                <?php if (!isset($wiki_url)): ?>
                    <li><?php echo link_tag($project_url, __('Project wiki frontpage')); ?></li>
                    <?php $quicksearch_title = __('Find project article (press enter to search)'); ?>
                    <li class="form-container">
                        <form action="<?php echo make_url('publish_find_project_articles', array('project_key' => $project->getName())); ?>" method="get" accept-charset="<?php echo \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>">
                            <input type="search" name="articlename" placeholder="<?php echo $quicksearch_title; ?>">
                        </form>
                    </li>
                <?php else: ?>
                    <li><?php echo link_tag($wiki_url, __('Project wiki frontpage'), array('target' => 'blank')) ?></li>
                <?php endif; ?>
            <?php endif; ?>
            <li class="header"><?php echo __('Global content'); ?></li>
            <?php $quicksearch_title = __('Find any article (press enter to search)'); ?>
            <li class="form-container">
                <form action="<?php echo make_url('publish_find_articles'); ?>" method="get" accept-charset="<?php echo \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>">
                    <input type="search" name="articlename" placeholder="<?php echo $quicksearch_title; ?>">
                </form>
            </li>
            <?php if (count(\thebuggenie\core\entities\Project::getAllRootProjects()) > (int) ($project instanceof \thebuggenie\core\entities\Project)): ?>
                <li class="header"><?php echo __('Project wikis'); ?></li>
                <?php foreach (\thebuggenie\core\entities\Project::getAllRootProjects() as $project): ?>
                    <?php if (!$project->hasAccess() || $project->isArchived() || (isset($project_url) && $project->getID() == $project->getID())) continue; ?>
                    <?php if (!$project->hasWikiURL()): ?>
                        <li><?php echo link_tag(make_url('publish_article', array('article_name' => $project->getName().':MainPage')), $project->getName()); ?></li>
                    <?php else: ?>
                        <li><?php echo link_tag($project->getWikiURL(), $project->getName(), array('target' => 'blank')) ?></li>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    <?php endif; ?>
</li>
