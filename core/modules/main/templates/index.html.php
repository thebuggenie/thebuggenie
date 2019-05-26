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
<div class="side_bar">
    <?php include_component('main/menulinks', array('links' => $links, 'target_type' => 'main_menu', 'target_id' => 0, 'title' => __('Quick links'))); ?>
    <?php \thebuggenie\core\framework\Event::createNew('core', 'index_left')->trigger(); ?>
</div>
<div class="main_area frontpage">
    <?php \thebuggenie\core\framework\Event::createNew('core', 'index_right_top')->trigger(); ?>
    <?php if ($show_project_list): ?>
        <?php include_component('main/projectlist', ['list_mode' => 'all', 'admin' => false]); ?>
    <?php endif; ?>
    <?php \thebuggenie\core\framework\Event::createNew('core', 'index_right_bottom')->trigger(); ?>
</div>
