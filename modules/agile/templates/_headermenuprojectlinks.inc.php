<?php if ($tbg_user->hasProjectPageAccess('project_planning', $project) || $tbg_user->hasProjectPageAccess('project_only_planning', $project)): ?>
    <li class="with-dropdown <?php if (in_array($tbg_response->getPage(), array('project_planning', 'agile_board', 'agile_whiteboard'))): ?> selected<?php endif; ?>">
        <div class="menuitem_container">
            <?php echo link_tag(make_url('agile_index', array('project_key' => $project->getKey())), fa_image_tag('trophy') . __('Agile')); ?>
            <?php echo javascript_link_tag(image_tag('tabmenu_dropdown.png', array('class' => 'menu_dropdown'))); ?>
        </div>
        <div class="tab_menu_dropdown">
            <?php echo link_tag(make_url('agile_index', array('project_key' => $project->getKey())), __('Manage boards'), ((in_array($tbg_response->getPage(), array('project_planning'))) ? array('class' => 'selected') : array())); ?>
            <div class="header"><?php echo __('Project boards'); ?></div>
            <?php if (count($boards)): ?>
                <?php foreach ($boards as $board): ?>
                    <a href="<?php echo make_url((!$tbg_user->hasProjectPageAccess('project_planning', $project) && $tbg_user->hasProjectPageAccess('project_only_planning', $project) ? 'agile_board' : 'agile_whiteboard'), array('project_key' => $board->getProject()->getKey(), 'board_id' => $board->getID())); ?>" class="<?php if ($tbg_request['board_id'] == $board->getID()) echo ' selected'; ?>"><?php echo $board->getName(); ?></a>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="disabled"><?php echo __('No project boards available'); ?></div>
            <?php endif; ?>
        </div>
    </li>
<?php endif; ?>
