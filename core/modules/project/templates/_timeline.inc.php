<table cellpadding=0 cellspacing=0 class="recent_activities">
    <?php foreach ($activities as $timestamp => $activities): ?>
        <?php $date = tbg_formatTime($timestamp, 5); ?>
            <?php if ($date != $prev_date): ?>
            <tr>
                <td class="latest_action_dates" colspan="2"><?php echo tbg_formatTime($timestamp, 5); ?></td>
            </tr>
        <?php endif; ?>
        <?php foreach ($activities as $activity): ?>
            <?php if ($activity['change_type'] == 'build_release'): ?>
                <tr>
                    <td class="imgtd" style="padding-top: 10px;"><?php echo image_tag('icon_build.png'); ?></td>
                    <td style="clear: both; padding-top: 10px;">
                        <span class="time"><?php echo tbg_formatTime($timestamp, 19); ?></span>&nbsp;<b><?php echo $activity['info']; ?></b><br><i><?php echo __('New version released'); ?></i>
                    </td>
                </tr>
            <?php elseif ($activity['change_type'] == 'sprint_start'): ?>
                <tr>
                    <td class="imgtd" style="padding-top: 10px;"><?php echo image_tag('icon_sprint.png'); ?></td>
                    <td style="clear: both; padding-top: 10px;">
                        <span class="time"><?php echo tbg_formatTime($timestamp, 19); ?></span>&nbsp;<b><?php echo $activity['info']; ?></b><br><i><?php echo __('A new sprint has started'); ?></i>
                    </td>
                </tr>
            <?php elseif ($activity['change_type'] == 'sprint_end'): ?>
                <tr>
                    <td class="imgtd" style="padding-top: 10px;"><?php echo image_tag('icon_sprint.png'); ?></td>
                    <td style="clear: both; padding-top: 10px;">
                        <span class="time"><?php echo tbg_formatTime($timestamp, 19); ?></span>&nbsp;<b><?php echo $activity['info']; ?></b><br><i><?php echo __('The sprint has ended'); ?></i>
                    </td>
                </tr>
            <?php elseif ($activity['change_type'] == 'milestone_release'): ?>
                <tr>
                    <td class="imgtd" style="padding-top: 10px;"><?php echo image_tag('icon_milestone.png'); ?></td>
                    <td style="clear: both; padding-top: 10px;">
                        <span class="time"><?php echo tbg_formatTime($timestamp, 19); ?></span>&nbsp;<b><?php echo $activity['info']; ?></b><br><i><?php echo __('A new milestone has been reached'); ?></i>
                    </td>
                </tr>
            <?php else: ?>
                <?php include_component('main/logitem', array('log_action' => $activity, 'include_time' => true, 'include_user' => true, 'extra_padding' => true, 'include_details' => true, 'include_issue_title' => !($prev_timestamp == $activity['timestamp'] && $prev_issue == $activity['target']))); ?>
            <?php endif; ?>
            <?php $prev_timestamp = $timestamp; ?>
        <?php endforeach; ?>
        <?php $prev_date = $date; ?>
    <?php endforeach; ?>
</table>
