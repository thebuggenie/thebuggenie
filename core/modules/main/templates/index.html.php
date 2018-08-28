<?php 

    $tbg_response->setTitle(__('Frontpage'));
    $tbg_response->addBreadcrumb(__('Frontpage'), make_url('home'));

/**
 * @var \thebuggenie\core\entities\User $tbg_user
 * @var \thebuggenie\core\helpers\Pagination $active_pagination
 * @var \thebuggenie\core\helpers\Pagination $archived_pagination
 * @var \thebuggenie\core\entities\Project[] $active_projects
 * @var \thebuggenie\core\entities\Project[] $archived_projects
 * @var int $active_project_count
 * @var int $archived_project_count
 * @var bool $show_project_config_link
 * @var bool $show_project_list
 */

?>
<?php if ($show_project_config_link && $show_project_list): ?>
    <?php if ($active_project_count == 1): ?>
        <?php include_component('main/hideableInfoBoxModal', array('key' => 'index_single_project_mode', 'title' => __('Only using The Bug Genie to track issues for one project?'), 'template' => 'main/intro_index_single_tracker')); ?>
    <?php endif; ?>
<?php endif; ?>
<table cellpadding=0 cellspacing=0 id="main-table">
    <tr>
        <td class="side_bar">
            <?php include_component('main/menulinks', array('links' => $links, 'target_type' => 'main_menu', 'target_id' => 0, 'title' => __('Quick links'))); ?>
            <?php \thebuggenie\core\framework\Event::createNew('core', 'index_left')->trigger(); ?>
        </td>
        <td class="main_area frontpage">
            <?php \thebuggenie\core\framework\Event::createNew('core', 'index_right_top')->trigger(); ?>
            <?php if ($show_project_list): ?>
                <div class="project_overview">
                    <div class="tab_menu inset">
                        <ul id="frontpage_projects_list_tabs">
                            <?php /* <li id="tab_starred"><a onclick="TBG.Main.Helpers.tabSwitcher('tab_starred', 'frontpage_projects_list_tabs', true);" href="javascript:void(0);"><?= fa_image_tag('star-half-o') . __('Starred projects'); ?></a></li> */ ?>
                            <li id="tab_active" class="<?= ($project_list_mode == 'active') ? 'selected' : ''; ?>"><a onclick="TBG.Main.Helpers.tabSwitcher('tab_active', 'frontpage_projects_list_tabs', true);" href="javascript:void(0);"><?= fa_image_tag('diamond') . __('Active projects'); ?></a></li>
                            <li id="tab_archived" class="<?= ($project_list_mode == 'archived') ? 'selected' : ''; ?>"><a onclick="TBG.Main.Helpers.tabSwitcher('tab_archived', 'frontpage_projects_list_tabs', true);" href="javascript:void(0);"><?= fa_image_tag('archive') . __('Archived projects'); ?></a></li>
                            <?php if ($tbg_user->isAuthenticated()): ?>
                                <li class="right">
                                    <?= link_tag(make_url('configure_projects'), fa_image_tag('cog'), ['class' => 'button-icon']); ?>
                                    <button class="button button-silver project-quick-edit" onclick="TBG.Main.Helpers.Backdrop.show('<?= make_url('get_partial_for_backdrop', ['key' => 'project_config']); ?>');"><?= __('Create project'); ?></button>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    <div id="frontpage_projects_list_tabs_panes">
                        <div id="tab_active_pane" style="<?= ($project_list_mode != 'active') ? 'display: none;' : ''; ?>">
                            <?php if ($active_project_count > 0): ?>
                                <ul class="project_list simple_list">
                                    <?php foreach ($active_projects as $project): ?>
                                        <li><?php include_component('project/overview', array('project' => $project)); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                                <?php if ($active_pagination->getTotalPages() > 1): ?>
                                    <?php include_component('main/pagination', ['pagination' => $active_pagination]); ?>
                                <?php endif; ?>
                            <?php else: ?>
                                <div class="onboarding large">
                                    <?= image_tag('onboard_noprojects.png'); ?>
                                    <div class="helper-text">
                                        <?php if ($show_project_config_link): ?>
                                            <?= __('There are no projects. Get started by clicking the "%create_project" button', ['%create_project' => __('Create project')]); ?>
                                        <?php else: ?>
                                            <?= __("You don't have access to any projects yet."); ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div id="tab_archived_pane" style="<?= ($project_list_mode != 'archived') ? 'display: none;' : ''; ?>">
                            <?php if ($archived_project_count > 0): ?>
                                <ul class="project_list simple_list">
                                    <?php foreach ($archived_projects as $project): ?>
                                        <li><?php include_component('project/overview', array('project' => $project)); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                                <?php if ($archived_pagination->getTotalPages() > 1): ?>
                                    <?php include_component('main/pagination', ['pagination' => $archived_pagination]); ?>
                                <?php endif; ?>
                            <?php else: ?>
                                <div class="onboarding large">
                                    <?= image_tag('onboard_noprojects.png'); ?>
                                    <div class="helper-text">
                                        <?php if ($show_project_config_link): ?>
                                            <?= __('There are no projects. Get started by clicking the "%create_project" button', ['%create_project' => __('Create project')]); ?>
                                        <?php else: ?>
                                            <?= __("You don't have access to any projects yet."); ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <?php \thebuggenie\core\framework\Event::createNew('core', 'index_right_bottom')->trigger(); ?>
        </td>
    </tr>
</table>
<script type="text/javascript">
    require(['domReady', 'thebuggenie/tbg', 'prototype'], function (domReady, TBG, prototype) {
        domReady(function () {
        });
    });
</script>