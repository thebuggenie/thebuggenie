<?php $child = (isset($child)) ? $child : false; ?>
<tr class="hover_highlight">
    <td style="font-weight: normal;" class="issue_title_container<?php if ($issue->isClosed()) echo ' faded_out'; ?>">
        <?php if ($child) echo image_tag('icon_tree_child.png', array('style' => 'float: left; margin: 0 5px 0 0;')); ?>
        <?php echo image_tag($issue->getIssueType()->getIcon() . '_tiny.png', array('style' => 'float: left; margin: 2px 5px -2px 0;', 'title' => $issue->getIssueType()->getName())); ?>
        <?php echo link_tag(make_url('viewissue', array('issue_no' => $issue->getFormattedIssueNo(false), 'project_key' => $issue->getProject()->getKey())), $issue->getFormattedTitle(), array('title' => $issue->getFormattedTitle(), 'style' => 'width: ' . (550 - 30*($child)).'px')); ?>
    </td>
    <td class="estimates">
        <span class="<?php if (!$issue->getSpentPoints()): ?> faded_out<?php endif; ?>" id="spent_time_<?php echo $issue->getID(); ?>_points"><?php echo $issue->getSpentPoints(); ?></span> /
        <span class="<?php if (!$issue->getEstimatedPoints()): ?> faded_out<?php endif; ?>" id="estimated_time_<?php echo $issue->getID(); ?>_points"><?php echo $issue->getEstimatedPoints(); ?></span>
    </td>
    <td class="estimates">
        <span class="<?php if (!$issue->getSpentHours()): ?> faded_out<?php endif; ?>" id="spent_time_<?php echo $issue->getID(); ?>_hours"><?php echo $issue->getSpentHours(); ?></span> /
        <span class="<?php if (!$issue->getEstimatedHours()): ?> faded_out<?php endif; ?>" id="estimated_time_<?php echo $issue->getID(); ?>_hours"><?php echo $issue->getEstimatedHours(); ?></span>
    </td>
    <td class="milestone_issue_actions">
        <div>
            <a class="dropper button button-icon button-silver" id="more_actions_<?php echo $issue->getID(); ?>_button"><?php echo image_tag('action_dropdown_small.png', array('title' => __('Show more actions'))); ?></a>
            <?php include_component('main/issuemoreactions', array('issue' => $issue, 'multi' => true)); ?>
        </div>
    </td>
</tr>
<?php if (count($issue->getChildIssues())): ?>
    <?php foreach ($issue->getChildIssues() as $child_issue): ?>
        <?php include_component('project/milestonedetailsissue', array('issue' => $child_issue, 'milestone' => $milestone, 'child' => true)); ?>
    <?php endforeach; ?>
<?php endif; ?>
