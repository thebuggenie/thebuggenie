<?php if (in_array($tbg_response->getPage(), array('agile_board', 'agile_whiteboard'))): ?>
    <?php if ($tbg_response->getPage() != 'agile_board'): ?>
        <?php if ($tbg_user->canManageProject(\thebuggenie\core\framework\Context::getCurrentProject())): ?>
            <div class="project_header_right button-group inset">
                <?php echo image_tag('icon-mono-settings.png', array('class' => 'dropper dropdown_link planning_board_settings_gear')); ?>
                <ul class="more_actions_dropdown popup_box">
                    <li><?php echo javascript_link_tag(__('Manage columns'), array('onclick' => "TBG.Project.Planning.Whiteboard.toggleEditMode();")); ?></li>
                    <li><a href="javascript:void(0);" onclick="TBG.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'agileboard', 'project_id' => $board->getProject()->getID(), 'board_id' => $board->getID())); ?>');" title="<?php echo __('Edit this board'); ?>"><?php echo __('Edit this board'); ?></a></li>
                </ul>
            </div>
        <?php endif; ?>
    <?php endif; ?>
    <div class="project_header_right button-group inset">
        <?php echo link_tag(make_url('agile_board', array('project_key' => $board->getProject()->getKey(), 'board_id' => $board->getID())), __('Planning'), array('class' => 'button'.(($tbg_response->getPage() == 'agile_board') ? ' button-pressed' : ''))); ?>
        <?php echo link_tag(make_url('agile_whiteboard', array('project_key' => $board->getProject()->getKey(), 'board_id' => $board->getID())), __('Whiteboard'), array('class' => 'button'.(($tbg_response->getPage() == 'agile_whiteboard') ? ' button-pressed' : ''))); ?>
        <?php if ($tbg_response->getPage() == 'agile_board'): ?>
            <?php if ($tbg_user->canManageProject(\thebuggenie\core\framework\Context::getCurrentProject())): ?>
                <a href="javascript:void(0);" class="planning_board_settings_gear" onclick="TBG.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'agileboard', 'project_id' => $board->getProject()->getID(), 'board_id' => $board->getID())); ?>');" title="<?php echo __('Edit this board'); ?>"><?php echo image_tag('icon-mono-settings.png'); ?></a>
            <?php endif; ?>
        <?php endif; ?>
    </div>
<?php endif; ?>
