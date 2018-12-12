<li class="with-dropdown <?php if (strpos($selected_tab, 'publish_') === 0): ?>selected<?php endif; ?>">
    <?php $dropper_class = (count(\thebuggenie\core\entities\Project::getAllRootProjects())) ? 'dropper' : ''; ?>
    <?php if (!isset($wiki_url)): ?>
        <?= link_tag(((isset($project_url)) ? $project_url : $url), fa_image_tag('newspaper') . \thebuggenie\core\framework\Context::getModule('publish')->getMenuTitle($project instanceof \thebuggenie\core\entities\Project) . fa_image_tag('caret-down', ['class' => 'dropdown-indicator']), ['class' => $dropper_class]); ?>
    <?php else: ?>
        <?= link_tag($wiki_url, \thebuggenie\core\framework\Context::getModule('publish')->getMenuTitle($project instanceof \thebuggenie\core\entities\Project) . fa_image_tag('caret-down', ['class' => 'dropdown-indicator']), ['target' => 'blank', 'class' => $dropper_class]) ?>
    <?php endif; ?>
    <div id="wiki_dropdown_menu" class="tab_menu_dropdown popup_box two-columns wide-right">
        <ul>
            <li class="header"><?= __('Quick links'); ?></li>
            <li><?php echo link_tag(make_url('publish_article', ['article_name' => 'MainPage']), \thebuggenie\core\framework\Context::getModule('publish')->getMenuTitle(false)) ?></li>
            <?php if ($project instanceof \thebuggenie\core\entities\Project): ?>
                <li><?php echo link_tag(make_url('publish_article', ['article_name' => $project->getKey().':MainPage']), \thebuggenie\core\framework\Context::getModule('publish')->getMenuTitle($project instanceof \thebuggenie\core\entities\Project)) ?></li>
            <?php endif; ?>
            <?php if (count(\thebuggenie\core\entities\Project::getAllRootProjects(false)) > (int) ($project instanceof \thebuggenie\core\entities\Project)): ?>
                <li class="header"><?= __('Project wikis'); ?></li>
                <?php foreach (\thebuggenie\core\entities\Project::getAllRootProjects(false) as $root_project): ?>
                    <?php if (!$root_project->hasAccess() || $root_project->isArchived() || (isset($project_url) && $root_project->getID() == $project->getID())) continue; ?>
                    <?php if (!$root_project->hasWikiURL()): ?>
                        <li><?= link_tag(make_url('publish_article', ['article_name' => $root_project->getKey().':MainPage']), image_tag($root_project->getSmallIconName(), ['class' => 'icon'], $root_project->hasSmallIcon()) . $root_project->getName()); ?></li>
                    <?php else: ?>
                        <li><?= link_tag($root_project->getWikiURL(), image_tag($root_project->getSmallIconName(), ['class' => 'icon'], $root_project->hasSmallIcon()) . $root_project->getName(), ['target' => 'blank']) ?></li>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
        <ul>
            <?php $quicksearch_title = __('Find any article (press enter to search)'); ?>
            <?php if (!$project instanceof \thebuggenie\core\entities\Project || $wiki_url): ?>
                <li class="header"><?= __('Global content'); ?></li>
                <li class="form-container">
                    <form action="<?= make_url('publish_find_articles'); ?>" method="get" accept-charset="<?= \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>">
                        <input type="search" name="articlename" placeholder="<?= $quicksearch_title; ?>">
                    </form>
                </li>
            <?php elseif (!isset($wiki_url)): ?>
                <li class="header"><?= __('Project content'); ?></li>
                <li class="form-container">
                    <form action="<?php echo make_url('publish_find_project_articles', ['project_key' => $project->getName()]); ?>" method="get" accept-charset="<?php echo \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>">
                        <input type="search" name="articlename" placeholder="<?php echo $quicksearch_title; ?>">
                    </form>
                </li>
            <?php else: ?>
                <li class="disabled"><?= __('Search disabled on external wiki'); ?></li>
            <?php endif; ?>
        </ul>
    </div>
</li>
