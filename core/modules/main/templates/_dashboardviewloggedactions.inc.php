<?php if (count($actions) > 0): ?>
    <table cellpadding=0 cellspacing=0 class="recent_activities">
        <?php $prev_date = null; ?>
        <?php $prev_timestamp = null; ?>
        <?php $prev_issue = null; ?>
        <?php foreach ($actions as $action): ?>
            <?php $date = tbg_formatTime($action['timestamp'], 5); ?>
            <?php if ($date != $prev_date): ?>
                <tr>
                    <td class="latest_action_dates" colspan="2"><?php echo $date; ?></td>
                </tr>
            <?php endif; ?>
            <?php include_component('main/logitem', array('log_action' => $action, 'include_project' => true, 'include_issue_title' => !($prev_timestamp == $action['timestamp'] && $prev_issue == $action['target']))); ?>
            <?php $prev_date = $date; ?>
            <?php $prev_timestamp = $action['timestamp']; ?>
            <?php $prev_issue = $action['target']; ?>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <div class="faded_out" style="padding: 5px 5px 10px 5px;"><?php echo __("You haven't done anything recently"); ?></div>
<?php endif; ?>
