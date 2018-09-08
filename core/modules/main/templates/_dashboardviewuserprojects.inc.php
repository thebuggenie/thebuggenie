<?php

    use thebuggenie\core\framework;

?>
<?php if (count($tbg_user->getAssociatedProjects()) > 0): ?>
    <ul id="associated_projects">
        <?php foreach ($tbg_user->getAssociatedProjects() as $project): ?>
            <?php if ($project->isDeleted()): continue; endif; ?>
            <li style="text-align: right;">
                <div style="padding: 0 5px;">
                    <div class="project_name">
                        <?php echo link_tag(make_url('project_dashboard', array('project_key' => $project->getKey())), $project->getName(), array('style' => 'font-weight: normal; font-size: 1.2em;')); ?>
                    </div>
                    <div style="float: right;" class="button-group">
                        <?php foreach ($links as $link): ?>
                            <?php echo link_tag(str_replace('%25project_key%25', $project->getKey(), $link['url']), $link['text'], array('class' => 'button button-silver')); ?>
                        <?php endforeach; ?>
                    </div>
                    <br style="clear: both;">
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <div class="no-items no-projects">
        <?= fa_image_tag('star-half-o'); ?>
        <span><?php echo __('You are not associated with any projects'); ?></span>
        <?php if ($tbg_user->canAccessConfigurationPage(framework\Settings::CONFIGURATION_SECTION_PROJECTS) && framework\Context::getScope()->hasProjectsAvailable()): ?>
            <button class="button button-silver" onclick="TBG.Main.Helpers.Backdrop.show('<?= make_url('get_partial_for_backdrop', ['key' => 'project_config']); ?>');"><?= __('Create project'); ?></button>
        <?php endif; ?>
    </div>
<?php endif; ?>
