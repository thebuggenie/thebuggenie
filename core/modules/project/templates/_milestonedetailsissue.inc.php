<?php

/**
 * @var \thebuggenie\core\entities\Issue $issue
 */

?>
<?php $child = (isset($child)) ? $child : false; ?>
<tr class="hover_highlight">
    <td style="font-weight: normal;" class="issue_title_container<?php if ($issue->isClosed()) echo ' faded_out'; ?>">
        <?php if ($child) echo image_tag('icon_tree_child.png', array('style' => 'float: left; margin: 0 5px 0 0;')); ?>
        <?php echo link_tag(make_url('viewissue', array('issue_no' => $issue->getFormattedIssueNo(false), 'project_key' => $issue->getProject()->getKey())), fa_image_tag($issue->getIssueType()->getFontAwesomeIcon(), ['title' => $issue->getIssueType()->getName()]) . $issue->getFormattedTitle(), array('title' => $issue->getFormattedTitle(), 'style' => 'width: ' . (550 - 30*($child)).'px')); ?>
    </td>
    <td class="estimates">
        <span class="<?php if (!$issue->getSpentPoints()): ?> faded_out<?php endif; ?>" id="spent_time_<?php echo $issue->getID(); ?>_points"><?php echo $issue->getSpentPoints(); ?></span> /
        <span class="<?php if (!$issue->getEstimatedPoints()): ?> faded_out<?php endif; ?>" id="estimated_time_<?php echo $issue->getID(); ?>_points"><?php echo $issue->getEstimatedPoints(); ?></span>
    </td>
    <td class="estimates">
        <span class="<?php if (!$issue->getSpentHoursAndMinutes(true, true)): ?> faded_out<?php endif; ?>" id="spent_time_<?php echo $issue->getID(); ?>_hours"><?php echo $issue->getSpentHoursAndMinutes(true, true); ?></span> /
        <span class="<?php if (!$issue->getEstimatedHoursAndMinutes(true, true)): ?> faded_out<?php endif; ?>" id="estimated_time_<?php echo $issue->getID(); ?>_hours"><?php echo $issue->getEstimatedHoursAndMinutes(true, true); ?></span>
    </td>
    <td class="milestone_issue_actions">
        <div>
            <a class="dropper button button-icon button-silver" id="more_actions_<?php echo $issue->getID(); ?>_button"><?php echo fa_image_tag('ellipsis-v', array('title' => __('Show more actions'))); ?></a>
            <?php include_component('main/issuemoreactions', array('issue' => $issue, 'multi' => true)); ?>
        </div>
    </td>
</tr>
<?php if (count($issue->getChildIssues())): ?>
    <?php foreach ($issue->getChildIssues() as $child_issue): ?>
        <?php include_component('project/milestonedetailsissue', array('issue' => $child_issue, 'milestone' => $milestone, 'child' => true)); ?>
    <?php endforeach; ?>
<?php endif; ?>
