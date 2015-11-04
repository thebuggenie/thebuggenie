<?php if ($tbg_user->hasProjectPageAccess('project_planning', $project) || $tbg_user->hasProjectPageAccess('project_only_planning', $project)): ?>
    <li class="button-dropdown cf">
        <?php echo link_tag(make_url('agile_index', array('project_key' => $project->getKey())), __('Agile'), array('class' => 'button button-silver righthugging')); ?>
        <a class="dropper button button-silver lefthugging" style="font-size: 0.9em;" href="javascript:void(0);">&#x25BC;</a>
        <ul class="more_actions_dropdown popup_box" style="position: absolute; margin-top: 25px; display: none;">
        <?php if (count($boards)): ?>
            <?php foreach ($boards as $board): ?>
                <li><a href="<?php echo make_url((!$tbg_user->hasProjectPageAccess('project_planning', $project) && $tbg_user->hasProjectPageAccess('project_only_planning', $project) ? 'agile_board' : 'agile_whiteboard'), array('project_key' => $board->getProject()->getKey(), 'board_id' => $board->getID())); ?>" class="<?php if ($tbg_request['board_id'] == $board->getID()) echo ' selected'; ?>"><?php echo $board->getName(); ?></a></li>
            <?php endforeach; ?>
        <?php else: ?>
            <li><div class="disabled"><?php echo __('No project boards available'); ?></div></li>
        <?php endif; ?>
        </ul>
    </li>
<?php endif; ?>
