<?php if ($tbg_user->hasProjectPageAccess('project_planning', $project) || $tbg_user->hasProjectPageAccess('project_only_planning', $project)): ?>
    <li class="with-dropdown <?php if (in_array($tbg_response->getPage(), array('project_planning', 'agile_board', 'agile_whiteboard'))): ?> selected<?php endif; ?>">
        <?php echo link_tag(make_url('agile_index', array('project_key' => $project->getKey())), fa_image_tag('trophy') . __('Agile') . fa_image_tag('caret-down', ['class' => 'dropdown-indicator']), ['class' => 'dropper']); ?>
        <ul class="tab_menu_dropdown popup_box">
            <li><?php echo link_tag(make_url('agile_index', array('project_key' => $project->getKey())), fa_image_tag('cog') . __('Manage boards'), ((in_array($tbg_response->getPage(), array('project_planning'))) ? array('class' => 'selected') : array())); ?></li>
            <li class="header"><?php echo __('Project boards'); ?></li>
            <?php if (count($boards)): ?>
                <?php foreach ($boards as $board): ?>
                    <li><a href="<?php echo make_url((!$tbg_user->hasProjectPageAccess('project_planning', $project) && $tbg_user->hasProjectPageAccess('project_only_planning', $project) ? 'agile_board' : 'agile_whiteboard'), array('project_key' => $board->getProject()->getKey(), 'board_id' => $board->getID())); ?>" class="<?php if ($tbg_request['board_id'] == $board->getID()) echo ' selected'; ?>"><?php echo $board->getName(); ?></a></li>
                <?php endforeach; ?>
            <?php else: ?>
                <li class="disabled"><?php echo __('No project boards available'); ?></li>
            <?php endif; ?>
        </ul>
    </li>
<?php endif; ?>
