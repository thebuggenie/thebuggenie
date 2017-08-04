<?php if ($tbg_user->hasProjectPageAccess('project_planning', $project) || $tbg_user->hasProjectPageAccess('project_only_planning', $project)): ?>
    <div class="button-dropdown agile-dropdown">
        <?php echo link_tag(make_url('agile_index', array('project_key' => $project->getKey())), __('Agile'), array('class' => 'button button-silver righthugging')); ?>
        <a class="dropper button button-silver lefthugging" href="javascript:void(0);"><?= fa_image_tag('caret-down'); ?></a>
        <ul class="more_actions_dropdown popup_box" style="position: absolute; margin-top: 25px; display: none;">
        <?php if (count($boards)): ?>
            <?php foreach ($boards as $board): ?>
                <li><a href="<?php echo make_url((!$tbg_user->hasProjectPageAccess('project_planning', $project) && $tbg_user->hasProjectPageAccess('project_only_planning', $project) ? 'agile_board' : 'agile_whiteboard'), array('project_key' => $board->getProject()->getKey(), 'board_id' => $board->getID())); ?>" class="<?php if ($tbg_request['board_id'] == $board->getID()) echo ' selected'; ?>"><?php echo $board->getName(); ?></a></li>
            <?php endforeach; ?>
        <?php else: ?>
            <li class="disabled"><a href="javascript:void(0);"><?php echo __('No project boards available'); ?></a></li>
        <?php endif; ?>
        </ul>
    </div>
<?php endif; ?>
