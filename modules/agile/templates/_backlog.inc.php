<li class="new_milestone_marker" id="new_backlog_milestone_marker">
    <div class="draggable">
        <div class="milestone_counts_container">
            <table>
                <tr>
                    <td id="new_backlog_milestone_issues_count">0</td>
                    <td id="new_backlog_milestone_points_count" class="issue_estimates estimated_points">0</td>
                    <td id="new_backlog_milestone_hours_count" class="issue_estimates estimated_hours">0</td>
                </tr>
                <tr>
                    <td><?php echo __('Issues'); ?></td>
                    <td class="issue_estimates estimated_points"><?php echo __('Points'); ?></td>
                    <td class="issue_estimates estimated_hours"><?php echo __('Hours'); ?></td>
                </tr>
            </table>
        </div>
        <?php echo javascript_link_tag(__('Create new sprint'), array('class' => 'button button-silver', 'onclick' => "TBG.Main.Helpers.Backdrop.show('".make_url('get_partial_for_backdrop', array('key' => 'agilemilestone', 'project_id' => $board->getProject()->getId(), 'board_id' => $board->getID()))."', TBG.Project.Planning.updateNewMilestoneIssues);")); ?>
    </div>
</li>
<?php foreach ($board->getBacklogSearchObject()->getIssues() as $issue): ?>
    <?php if ($issue->getMilestone() instanceof thebuggenie\core\entities\Milestone) continue; ?>
    <?php if ($issue->isChildIssue()): ?>
        <?php foreach ($issue->getParentIssues() as $parent): ?>
            <?php if ($parent->getIssueType()->getID() != $board->getEpicIssuetypeID()) continue 2; ?>
        <?php endforeach; ?>
        <?php include_component('agile/milestoneissue', array('issue' => $issue, 'board' => $board)); ?>
    <?php else: ?>
        <?php include_component('agile/milestoneissue', array('issue' => $issue, 'board' => $board)); ?>
    <?php endif; ?>
<?php endforeach; ?>
