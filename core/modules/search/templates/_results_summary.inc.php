<div class="results_summary">
    <div class="summary_header"><?php echo __('In the group above (on this page)'); ?></div>
    <?php echo __('Total number of issues: %number', array('%number' => '<span class="issue_count">'.$current_count.'</span>')); ?><br>
    <?php echo __('Total estimated effort: %details', array('%details' => '<span class="issue_estimated_time_summary">'.\thebuggenie\core\entities\Issue::getFormattedTime($current_estimated_time, false).'</span>')); ?><br>
    <?php echo __('Total current effort: %details', array('%details' => '<span class="issue_spent_time_summary">'.\thebuggenie\core\entities\Issue::getFormattedTime($current_spent_time, false).'</span>')); ?><br>
</div>
