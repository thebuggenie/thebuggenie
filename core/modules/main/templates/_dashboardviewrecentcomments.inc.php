<div class="dashboard_recent_comments">
    <?php if (count($comments)): ?>
        <table cellpadding=0 cellspacing=0 class="recent_activities">
            <?php $prev_date = null; ?>
            <?php foreach ($comments as $comment): ?>
                <?php $date = tbg_formatTime($comment->getPosted(), 5); ?>
                <?php if ($date != $prev_date): ?>
                    <tr>
                        <td class="latest_action_dates_cell" colspan="2">
                            <div class="latest_action_dates"><?php echo $date; ?></div>
                        </td>
                    </tr>
                <?php endif; ?>
                <?php include_component('main/commentitem', array('comment' => $comment, 'include_project' => true, 'include_time' => true)); ?>
                <?php $prev_date = $date; ?>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <div class="faded_out" style="padding: 5px 5px 10px 5px;"><?php echo __('No issues recently commented'); ?></div>
    <?php endif; ?>
</div>
