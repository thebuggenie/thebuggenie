<div class="header <?php if (!$milestone->getID()) echo 'backlog'; ?>" id="milestone_<?php echo $milestone->getID(); ?>_header">
    <div class="milestone_basic_container">
        <span class="milestone_name milestone_virtual_status"><?php include_component('project/milestonevirtualstatusdetails', array('milestone' => $milestone)); ?></span>
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
                        <?php echo $milestone->countOpenIssues(); ?><?php if ($milestone->countClosedIssues() > 0) echo ' ('.$milestone->countIssues().')'; ?>
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
        <?php if ($tbg_user->canEditProjectDetails(\thebuggenie\core\framework\Context::getCurrentProject())): ?>
            <div class="settings_container">
                <?php echo image_tag('icon-mono-settings.png', array('class' => 'dropper dropdown_link')); ?>
                <ul class="popup_box milestone_moreactions more_actions_dropdown" id="milestone_<?php echo $milestone->getID(); ?>_moreactions" style="display: none;">
                    <li><?php echo javascript_link_tag(__('Edit'), array('onclick' => "TBG.Main.Helpers.Backdrop.show('".make_url('get_partial_for_backdrop', array('key' => 'milestone', 'project_id' => $milestone->getProject()->getId(), 'milestone_id' => $milestone->getID()))."');")); ?></li>
                    <li class="separator"></li>
                    <li><?php echo javascript_link_tag(__('Delete'), array('onclick' => "TBG.Main.Helpers.Dialog.show('".__('Do you really want to delete this milestone?')."', '".__('Removing this milestone will unassign all issues from this milestone and remove it from all available lists. This action cannot be undone.')."', {yes: {click: function() { TBG.Project.Milestone.remove('".make_url('project_milestone', array('project_key' => $milestone->getProject()->getKey(), 'milestone_id' => $milestone->getID()))."', ".$milestone->getID()."); } }, no: {click: TBG.Main.Helpers.Dialog.dismiss} });")); ?></li>
                </ul>
            </div>
        <?php endif; ?>
        <div class="button-group" style="float: right;">
            <?php if ($milestone->getID()): ?>
                <?php echo link_tag(make_url('project_issues', array('project_key' => \thebuggenie\core\framework\Context::getCurrentProject()->getKey(), 'search' => true, 'fs[milestone]' => array('o' => '=', 'v' => $milestone->getId())))."?sortfields=issues.last_updated=asc", __('Show issues'), array('class' => 'button button-silver', 'title' => __('Show issues'))); ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    <div class="milestone_description">
        <?php echo $milestone->getDescription(); ?>
    </div>
</div>
