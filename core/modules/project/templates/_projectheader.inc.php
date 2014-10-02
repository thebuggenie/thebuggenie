<div class="project_header">
    <?php if ($tbg_response->getPage() == 'project_summary'): ?>
        <div class="project_header_right button-group">
            <?php TBGEvent::createNew('core', 'project_header_buttons')->trigger(); ?>
            <?php if ($selected_project->hasDownloads() && $tbg_response->getPage() != 'project_releases'): ?>
                <?php echo link_tag(make_url('project_releases', array('project_key' => $selected_project->getKey())), image_tag('icon_download.png').__('Download'), array('class' => 'button button-orange')); ?>
            <?php endif; ?>
            <?php if ($selected_project->hasParent()): ?>
                <?php echo link_tag(make_url('project_dashboard', array('project_key' => $selected_project->getParent()->getKey())), image_tag($selected_project->getParent()->getSmallIconName(), array('style' => 'width: 16px; height: 16px;'), $selected_project->getParent()->hasSmallIcon()) . __('Up to %parent', array('%parent' => $selected_project->getParent()->getName())), array('class' => 'button button-silver')); ?>
            <?php endif; ?>
        </div>
    <?php elseif (in_array($tbg_response->getPage(), array('project_planning_board', 'project_planning_board_whiteboard'))): ?>
        <div class="project_header_right button-group inset">
            <?php echo link_tag(make_url('project_planning_board', array('project_key' => $board->getProject()->getKey(), 'board_id' => $board->getID())), __('Planning'), array('class' => 'button'.(($tbg_response->getPage() == 'project_planning_board') ? ' button-pressed' : ''))); ?>
            <?php echo link_tag(make_url('project_planning_board_whiteboard', array('project_key' => $board->getProject()->getKey(), 'board_id' => $board->getID())), __('Whiteboard'), array('class' => 'button'.(($tbg_response->getPage() == 'project_planning_board_whiteboard') ? ' button-pressed' : ''))); ?>
            <?php if ($tbg_response->getPage() == 'project_planning_board'): ?>
                <a href="javascript:void(0);" class="planning_board_settings_gear" onclick="TBG.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'agileboard', 'project_id' => $board->getProject()->getID(), 'board_id' => $board->getID())); ?>');" title="<?php echo __('Edit this board'); ?>"><?php echo image_tag('icon-mono-settings.png'); ?></a>
            <?php else: ?>
                <?php echo image_tag('icon-mono-settings.png', array('class' => 'dropper dropdown_link planning_board_settings_gear')); ?>
                <ul class="more_actions_dropdown popup_box">
                    <li><?php echo javascript_link_tag(__('Manage columns'), array('onclick' => "TBG.Project.Planning.Whiteboard.toggleEditMode();")); ?></li>
                    <li><a href="javascript:void(0);" onclick="TBG.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'agileboard', 'project_id' => $board->getProject()->getID(), 'board_id' => $board->getID())); ?>');" title="<?php echo __('Edit this board'); ?>"><?php echo __('Edit this board'); ?></a></li>
                </ul>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    <div class="project_header_left">
        <?php echo image_tag($selected_project->getLargeIconName(), array('class' => 'project_header_logo'), $selected_project->hasLargeIcon()); ?>
        <div id="project_name">
            <span id="project_name_span">
                <?php echo $selected_project->getName(); ?>
            </span>
            <span id="project_sub_page"><?php echo $subpage; ?></span>
        </div>
        <?php if ($tbg_response->getPage() == 'project_dashboard' && $tbg_user->canEditProjectDetails($selected_project)): ?>
            <div class="project_header_right button-group">
                <a href="javascript:void(0);" class="button button-silver" onclick="$$('.dashboard').each(function (elm) { elm.toggleClassName('editable');});$(this).toggleClassName('button-pressed');"><?php echo __('Edit dashboard'); ?></a>
            </div>
        <?php endif; ?>
    </div>
</div>
