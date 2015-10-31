<?php if ($tbg_user->hasProjectPageAccess('project_planning', $project)): ?>
    <li<?php if (in_array($tbg_response->getPage(), array('project_planning', 'agile_board', 'agile_whiteboard'))): ?> class="selected"<?php endif; ?>>
        <div class="menuitem_container">
            <?php echo link_tag(make_url('agile_index', array('project_key' => $project->getKey())), image_tag('icon_agile.png') . __('Agile')); ?>
            <?php echo javascript_link_tag(image_tag('tabmenu_dropdown.png', array('class' => 'menu_dropdown'))); ?>
        </div>
        <div class="tab_menu_dropdown">
            <?php echo link_tag(make_url('agile_index', array('project_key' => $project->getKey())), __('Manage boards'), ((in_array($tbg_response->getPage(), array('project_planning'))) ? array('class' => 'selected') : array())); ?>
            <div class="header"><?php echo __('Project boards'); ?></div>
            <?php if (count($boards)): ?>
                <?php foreach ($boards as $board): ?>
                    <a href="<?php echo make_url('agile_board', array('project_key' => $board->getProject()->getKey(), 'board_id' => $board->getID())); ?>" class="<?php if ($tbg_request['board_id'] == $board->getID()) echo ' selected'; ?>"><?php echo $board->getName(); ?></a>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="disabled"><?php echo __('No project boards available'); ?></div>
            <?php endif; ?>
        </div>
    </li>
<?php endif; ?>
