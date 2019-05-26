<?php

    /** @var \thebuggenie\core\entities\LogItem[] $log_items */

?>
<div class="dashboard_logged_actions">
    <?php if (count($log_items) > 0): ?>
        <table cellpadding=0 cellspacing=0 class="recent_activities">
            <?php $prev_date = null; ?>
            <?php $prev_timestamp = null; ?>
            <?php $prev_issue = null; ?>
            <?php foreach ($log_items as $log_item): ?>
                <?php $date = tbg_formatTime($log_item->getTime(), 5); ?>
                <?php if ($date != $prev_date): ?>
                    <tr>
                        <td class="latest_action_dates_cell" colspan="2">
                            <div class="latest_action_dates"><?php echo $date; ?></div>
                        </td>
                    </tr>
                <?php endif; ?>
                <?php include_component('main/logitem', array('item' => $log_item, 'include_project' => true, 'include_issue_title' => !($prev_timestamp == $log_item->getTime() && $prev_issue == $log_item->getTarget()), 'include_time' => true)); ?>
                <?php $prev_date = $date; ?>
                <?php $prev_timestamp = $log_item->getTime(); ?>
                <?php $prev_issue = $log_item->getTarget(); ?>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <div class="faded_out" style="padding: 5px 5px 10px 5px;"><?php echo __("Changes to issues, commits and other actions show up here"); ?></div>
    <?php endif; ?>
</div>
