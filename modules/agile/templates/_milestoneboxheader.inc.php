<?php use thebuggenie\modules\agile\entities\AgileBoard; ?>
<div class="header <?php if (!$milestone->getID()) echo 'backlog'; ?>" id="milestone_<?php echo $milestone->getID(); ?>_header">
    <div class="milestone_basic_container">
        <?php if (isset($board)): ?>
        <a href="<?php echo make_url('agile_whiteboard', array('project_key' => $board->getProject()->getKey(), 'board_id' => $board->getID())); ?>#<?php echo $milestone->getID(); ?>"><span class="milestone_name"><?php echo $milestone->getName(); ?></span></a>
        <?php else: ?>
            <span class="milestone_name milestone_virtual_status"><?php include_component('project/milestonevirtualstatusdetails', array('milestone' => $milestone)); ?></span>
        <?php endif; ?>
        <dl class="info">
            <?php if ($milestone->getID()): ?>
                <dt><?php echo __('Start date'); ?></dt>
                <dd><?php echo ($milestone->getStartingDate()) ? tbg_formatTime($milestone->getStartingDate(), 22, true, true) : '-'; ?></dd>
                <dt><?php echo __('End date'); ?></dt>
                <dd><?php echo ($milestone->getScheduledDate()) ? tbg_formatTime($milestone->getScheduledDate(), 22, true, true) : '-'; ?></dd>
            <?php endif; ?>
        </dl>
        <?php if ($milestone->getID() && isset($board)): ?>
            <div class="milestone_percentage">
                <div class="filler" id="milestone_<?php echo $milestone->getID(); ?>_percentage_filler" style="<?php if ($include_counts) echo 'width: '. $milestone->getPercentComplete() . '%'; ?>"></div>
            </div>
        <?php endif; ?>
    </div>
    <div class="milestone_counts_container">
        <table>
            <tr>
                <td id="milestone_<?php echo $milestone->getID(); ?>_issues_count">
                    <?php if ($include_counts): ?>
                        <?php echo $milestone->countOpenIssues(); ?><?php if ($milestone->countClosedIssues() > 0) echo ' ('.$milestone->countClosedIssues().')'; ?>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
                <td id="milestone_<?php echo $milestone->getID(); ?>_points_count" class="issue_estimates"><?php echo ($include_counts) ? $milestone->getPointsEstimated() : '-'; ?></td>
                <td id="milestone_<?php echo $milestone->getID(); ?>_hours_count" class="issue_estimates"><?php echo ($include_counts) ? $milestone->getHoursEstimated() : '-'; ?></td>
            </tr>
            <tr>
                <td><?php echo __('Issues'); ?></td>
                <td class="issue_estimates"><?php echo __('Points'); ?></td>
                <td class="issue_estimates"><?php echo __('Hours'); ?></td>
            </tr>
        </table>
    </div>
    <?php if ($include_buttons): ?>
        <div class="settings_container">
            <?php echo image_tag('icon-mono-settings.png', array('class' => 'dropper dropdown_link')); ?>
            <ul class="popup_box milestone_moreactions more_actions_dropdown" id="milestone_<?php echo $milestone->getID(); ?>_moreactions" style="display: none;">
                <li><?php echo link_tag(make_url('project_milestone_details', array('project_key' => $milestone->getProject()->getKey(), 'milestone_id' => $milestone->getID())), __('Show overview')); ?></li>
                <?php if ($tbg_user->canEditProjectDetails(\thebuggenie\core\framework\Context::getCurrentProject())): ?>
                    <li class="separator"></li>
                    <li><?php echo javascript_link_tag(__('Mark as finished'), array('onclick' => "TBG.Main.Helpers.Backdrop.show('".make_url('get_partial_for_backdrop', array('key' => 'milestone_finish', 'project_id' => $milestone->getProject()->getId(), 'milestone_id' => $milestone->getID(), 'board_id' => isset($board) ? $board->getID() : ''))."');")); ?></li>
                    <li class="separator"></li>
                    <li><?php echo javascript_link_tag(__('Edit'), array('onclick' => "TBG.Main.Helpers.Backdrop.show('".make_url('get_partial_for_backdrop', array('key' => 'agilemilestone', 'project_id' => $milestone->getProject()->getId(), 'milestone_id' => $milestone->getID(), 'board_id' => isset($board) ? $board->getID() : ''))."');")); ?></li>
                    <li><?php
                        if (isset($board))
                        {
                            switch ($board->getType())
                            {
                                case AgileBoard::TYPE_GENERIC:
                                    echo javascript_link_tag(__('Delete'), array('onclick' => "TBG.Main.Helpers.Dialog.show('".__('Do you really want to delete this milestone?')."', '".__('Removing this milestone will unassign all issues from this milestone and remove it from all available lists. This action cannot be undone.')."', {yes: {click: function() { TBG.Project.Milestone.remove('".make_url('agile_milestone', array('project_key' => $milestone->getProject()->getKey(), 'milestone_id' => $milestone->getID()))."', ".$milestone->getID()."); } }, no: {click: TBG.Main.Helpers.Dialog.dismiss} });"));
                                    break;
                                case AgileBoard::TYPE_SCRUM:
                                case AgileBoard::TYPE_KANBAN:
                                    echo javascript_link_tag(__('Delete'), array('onclick' => "TBG.Main.Helpers.Dialog.show('".__('Do you really want to delete this sprint?')."', '".__('Deleting this sprint will remove all issues in this sprint and put them in the backlog. This action cannot be undone.')."', {yes: {click: function() { TBG.Project.Milestone.remove('".make_url('agile_milestone', array('project_key' => $milestone->getProject()->getKey(), 'milestone_id' => $milestone->getID()))."', ".$milestone->getID()."); } }, no: {click: TBG.Main.Helpers.Dialog.dismiss} });"));
                                    break;
                            }
                        }
                    ?></li>
                <?php endif; ?>
            </ul>
        </div>
        <div class="button-group" style="float: right;">
            <?php if ($milestone->getID()): ?>
                <button class="button button-silver toggle-issues" onclick="TBG.Project.Planning.toggleMilestoneIssues(<?php echo $milestone->getID(); ?>);"><?php echo image_tag('spinning_16.gif').__('Show issues'); ?></button>
            <?php endif; ?>
            <button class="button button-silver" onclick="TBG.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'reportissue', 'project_id' => $milestone->getProject()->getId(), 'milestone_id' => $milestone->getID(), 'board_id' => isset($board) ? $board->getID() : '')); ?>');"><?php echo __('Add issue'); ?></button>
        </div>
    <?php endif; ?>
    <?php echo image_tag('spinning_20.gif', array('id' => 'milestone_'.$milestone->getID().'_issues_indicator', 'class' => 'milestone_issues_indicator', 'style' => 'display: none;')); ?>
</div>
