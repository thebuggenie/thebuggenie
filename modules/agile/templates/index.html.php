<?php

    $tbg_response->addBreadcrumb(__('Planning'), make_url('agile_index', array('project_key' => $selected_project->getKey())));
    $tbg_response->setTitle(__('"%project_name" project planning', array('%project_name' => $selected_project->getName())));
    include_component('project/projectheader', array('selected_project' => $selected_project, 'subpage' => __('Manage agile boards')));

?>
<div id="project_boards" class="project_info_container">
    <div class="project_boards_list" id="boards_list_container">
        <h3><?php echo __('Public project boards'); ?></h3>
        <ul id="agileboards_project">
            <?php foreach ($project_boards as $board): ?>
                <?php include_component('agile/boardbox', compact('board')); ?>
            <?php endforeach; ?>
            <?php if ($tbg_user->canManageProject($selected_project)): ?>
                <li id="add_board_project_link" class="add_board_container" onclick="TBG.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'agileboard', 'project_id' => $selected_project->getID(), 'is_private' => 0)); ?>');">+</li>
            <?php endif; ?>
        </ul>
        <h3><?php echo __('Private project boards'); ?></h3>
        <ul id="agileboards_user">
            <?php foreach ($user_boards as $board): ?>
                <?php include_component('agile/boardbox', compact('board')); ?>
            <?php endforeach; ?>
            <li id="add_board_user_link" class="add_board_container" onclick="TBG.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'agileboard', 'project_id' => $selected_project->getID(), 'is_private' => 1)); ?>');">+</li>
        </ul>
    </div>
    <br style="clear: both;">
</div>
