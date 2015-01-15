<tr class="hover_highlight" id="scrum_story_task_<?php echo $task->getID(); ?>">
    <td>&nbsp;</td>
    <td style="padding: 3px 0 3px 10px; font-size: 12px; <?php if ($task->isClosed()): ?> text-decoration: line-through; <?php endif; ?>"<?php if ($task->isClosed()): ?> class="faded_out"<?php endif; ?>><?php echo link_tag(make_url('viewissue', array('issue_no' => $task->getFormattedIssueNo(false), 'project_key' => $task->getProject()->getKey())), $task->getTitle()); ?></td>
    <td class="estimates faded_out">-</td>
    <td class="estimates<?php if ($task->isClosed()): ?> faded_out<?php endif; ?>" <?php if ($task->isClosed()): ?>style="text-decoration: line-through;" <?php endif; ?>id="scrum_story_<?php echo $task->getID(); ?>_hours"><?php echo $task->getEstimatedHours(); ?></td>
    <?php if ($can_estimate): ?>
        <td style="padding: 3px;">
            <div style="position: relative; text-align: center;" class="scrum_sprint_details_actions">
                <?php include_component('project/quickestimate', array('issue' => $task, 'show_hours' => true)); ?>
                <a href="javascript:void(0);" class="img" onclick="$('scrum_story_<?php echo $task->getID(); ?>_estimation').toggle();" alt="<?php echo __('Change estimate'); ?>" title="<?php echo __('Change estimate'); ?>"><?php echo image_tag('scrum_estimate.png'); ?></a>
            </div>
        </td>
    <?php endif; ?>
</tr>