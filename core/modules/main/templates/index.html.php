<?php 

    $tbg_response->setTitle(__('Frontpage'));
    $tbg_response->addBreadcrumb(__('Frontpage'), make_url('home'));

?>
<?php if ($show_project_config_link && $show_project_list): ?>
    <?php if ($project_count == 1): ?>
        <?php include_component('main/hideableInfoBoxModal', array('key' => 'index_single_project_mode', 'title' => __('Only using The Bug Genie to track issues for one project?'), 'template' => 'main/intro_index_single_tracker')); ?>
    <?php elseif ($project_count == 0): ?>
        <?php include_component('main/hideableInfoBoxModal', array('key' => 'index_no_projects', 'title' => __('Get started using The Bug Genie'), 'template' => 'main/intro_index_no_projects')); ?>
    <?php endif; ?>
<?php endif; ?>
<table style="margin-top: 0px; table-layout: fixed; width: 100%;" cellpadding=0 cellspacing=0>
    <tr>
        <td class="side_bar">
            <?php include_component('main/menulinks', array('links' => $links, 'target_type' => 'main_menu', 'target_id' => 0, 'title' => __('Quick links'))); ?>
            <?php \thebuggenie\core\framework\Event::createNew('core', 'index_left')->trigger(); ?>
        </td>
        <td class="main_area frontpage">
            <?php \thebuggenie\core\framework\Event::createNew('core', 'index_right_top')->trigger(); ?>
            <?php if ($show_project_list): ?>
                <div class="project_overview">
                    <div class="header">
                        <?php echo __('Projects'); ?>
                        <div class="dropper_container">
                            <a href="javascript:void(0);" class="dropper dynamic_menu_link"><?php echo image_tag('icon-mono-settings.png'); ?></a>
                            <ul class="more_actions_dropdown popup_box">
                                <?php if ($show_project_config_link): ?>
                                    <li><?php echo link_tag(make_url('configure_projects'), __('Manage projects')); ?></li>
                                <?php endif; ?>
                                <li><a href="javascript:void(0);" onclick="TBG.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'archived_projects')); ?>');"><?php echo __('Show archived projects'); ?></a></li>
                            </ul>
                        </div>
                    </div>
                    <?php if ($project_count > 0): ?>
                        <ul class="project_list simple_list">
                        <?php foreach ($projects as $project): ?>
                            <li><?php include_component('project/overview', array('project' => $project)); ?></li>
                        <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="content">
                            <?php echo __('There are no top-level projects'); ?>.
                            <?php if ($show_project_config_link): ?>
                                <?php echo link_tag(make_url('configure_projects'), __('Go to project management').' &gt;&gt;'); ?>
                            <?php else: ?>
                                <?php echo __('Projects can only be created by an administrator'); ?>.
                            <?php endif; ?>
                        </p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            <?php \thebuggenie\core\framework\Event::createNew('core', 'index_right_bottom')->trigger(); ?>
        </td>
    </tr>
</table>
