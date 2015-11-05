<table cellpadding=0 cellspacing=0 class="recent_activities">
    <?php foreach ($activities as $timestamp => $activities): ?>
        <?php $date = tbg_formatTime($timestamp, 5); ?>
            <?php if ($date != $prev_date): ?>
            <tr>
                <td class="latest_action_dates_cell" colspan="2">
                    <div class="latest_action_dates"><?php echo tbg_formatTime($timestamp, 5); ?></div>
                </td>
            </tr>
        <?php endif; ?>
        <?php $prev_issue = isset($prev_issue) ? $prev_issue : null; ?>
        <?php foreach ($activities as $activity): ?>
            <?php if ($activity['change_type'] == 'build_release'): ?>
                <tr>
                    <td class="imgtd" style="position: absolute; margin-top: -1px; margin-left: 4px;"><?php echo image_tag('icon_build.png'); ?></td>
                    <td style="clear: both;">
                        <span class="time"><?php echo tbg_formatTime($timestamp, 19); ?></span>&nbsp;<span style="font-size: 1.1em"><?php echo $activity['info']; ?></span><br><span style="display: inline-block; margin-top: 4px; margin-bottom: 15px;"><i><?php echo __('New version released'); ?></i></span>
                    </td>
                </tr>
            <?php elseif ($activity['change_type'] == 'sprint_start'): ?>
                <tr>
                    <td class="imgtd" style="position: absolute; margin-top: -1px; margin-left: 4px;"><?php echo image_tag('icon_sprint.png'); ?></td>
                    <td style="clear: both;">
                        <span class="time"><?php echo tbg_formatTime($timestamp, 19); ?></span>&nbsp;<span style="font-size: 1.1em"><?php echo $activity['info']; ?></span><br><span style="display: inline-block; margin-top: 4px; margin-bottom: 15px;"><i><?php echo __('A new sprint has started'); ?></i></span>
                    </td>
                </tr>
            <?php elseif ($activity['change_type'] == 'sprint_end'): ?>
                <tr>
                    <td class="imgtd" style="position: absolute; margin-top: -1px; margin-left: 4px;"><?php echo image_tag('icon_sprint.png'); ?></td>
                    <td style="clear: both;">
                        <span class="time"><?php echo tbg_formatTime($timestamp, 19); ?></span>&nbsp;<span style="font-size: 1.1em"><?php echo $activity['info']; ?></span><br><span style="display: inline-block; margin-top: 4px; margin-bottom: 15px;"><i><?php echo __('The sprint has ended'); ?></i></span>
                    </td>
                </tr>
            <?php elseif ($activity['change_type'] == 'milestone_release'): ?>
                <tr>
                    <td class="imgtd" style="position: absolute; margin-top: -1px; margin-left: 4px;"><?php echo image_tag('icon_milestone.png'); ?></td>
                    <td style="clear: both;">
                        <span class="time"><?php echo tbg_formatTime($timestamp, 19); ?></span>&nbsp;<span style="font-size: 1.1em"><?php echo $activity['info']; ?></span><br><span style="display: inline-block; margin-top: 4px; margin-bottom: 15px;"><i><?php echo __('A new milestone has been reached'); ?></i></span>
                    </td>
                </tr>
            <?php else: ?>
                <?php include_component('main/logitem', array('log_action' => $activity, 'include_time' => true, 'include_user' => true, 'include_details' => true, 'include_issue_title' => !($prev_timestamp == $activity['timestamp'] && $prev_issue == $activity['target']))); ?>
            <?php endif; ?>
            <?php $prev_timestamp = $timestamp; ?>
            <?php $prev_issue = $activity['target']; ?>
        <?php endforeach; ?>
        <?php $prev_date = $date; ?>
    <?php endforeach; ?>
</table>
