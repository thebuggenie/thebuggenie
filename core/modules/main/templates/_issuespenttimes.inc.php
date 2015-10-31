<div class="backdrop_box large" id="viewissue_add_item_div">
    <div class="backdrop_detail_header">
        <?php echo javascript_link_tag(__('Add time spent'), array('onclick' => "$('time_spent_{$issue->getID()}_form').toggle();if ($('time_spent_{$issue->getID()}_form').visible()) { $('issue_{$issue->getID()}_timeentry').focus(); }", 'style' => 'float: right;', 'class' => 'button button-silver')); ?>
        <?php echo __('Issue time tracking - time spent'); ?>
    </div>
    <div id="backdrop_detail_content" class="backdrop_detail_content">
        <div class="lightyellowbox issue_timespent_form" id="time_spent_<?php echo $issue->getID(); ?>_form" style="<?php if (!isset($initial_view) || $initial_view != 'entry') echo 'display: none;'; ?>">
            <form action="<?php echo make_url('issue_edittimespent', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'entry_id' => 0)); ?>" onsubmit="TBG.Issues.editTimeEntry(this);return false;">
                <?php include_component('main/issuespenttimeentry', array('issue' => $issue, 'save' => true)); ?>
            </form>
        </div>
        <p>
            <?php echo __('The list below shows all time logged against this issue so far.'); ?>
        </p>
        <table id="timespent_list" style="margin-top: 15px; width: 100%;">
            <thead>
                <tr>
                    <th style="width: 100px;"><?php echo __('Date'); ?></th>
                    <th style="width: 100px;"><?php echo __('Activity'); ?></th>
                    <th style="width: auto;"><?php echo __('Logged by'); ?></th>
                    <th style="width: 150px; text-align: right;"><?php echo __('Time logged'); ?></th>
                    <th style="width: 150px; text-align: right;"><?php echo __('Actions'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($issue->getSpentTimes() as $spent_time): ?>
                    <tr id="issue_spenttime_<?php echo $spent_time->getID(); ?>">
                        <td><?php echo tbg_formatTime($spent_time->getEditedAt(), 20); ?></td>
                        <td style="font-size: 0.9em;"><?php echo ($spent_time->getActivityType() instanceof \thebuggenie\core\entities\ActivityType) ? $spent_time->getActivityType()->getName() : '-'; ?></td>
                        <td><?php echo include_component('main/userdropdown', array('user' => $spent_time->getUser())); ?></td>
                        <td style="text-align: right;"><?php echo \thebuggenie\core\entities\Issue::getFormattedTime($spent_time->getSpentTime()); ?></td>
                        <td style="text-align: right;" class="button-group" <?php if ($spent_time->getComment()): ?>rowspan="2"<?php endif; ?>>
                            <a href="javascript:void(0);" style="float: none;" class="button button-silver last" onclick="TBG.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'issue_spenttime', 'entry_id' => $spent_time->getID())); ?>');"><?php echo __('Edit'); ?></a>
                            <a href="javascript:void(0);" style="float: right;" class="button button-silver first" onclick="TBG.Main.Helpers.Dialog.show('<?php echo __('Do you really want to remove this time entry?'); ?>', '<?php echo __('Removing this entry will change the number of points, hours, days, weeks or months spent on this issue.'); ?>', {yes: {click: function() {TBG.Issues.deleteTimeEntry('<?php echo make_url('issue_deletetimespent', array('project_key' => $spent_time->getIssue()->getProject()->getKey(), 'issue_id' => $spent_time->getIssueID(), 'entry_id' => $spent_time->getID())); ?>', <?php echo $spent_time->getID(); ?>); }}, no: { click: TBG.Main.Helpers.Dialog.dismiss }});return false;"><?php echo __('Remove'); ?></a>
                        </td>
                    </tr>
                    <?php if ($spent_time->getComment()): ?>
                        <tr id="issue_spenttime_<?php echo $spent_time->getID(); ?>_comment">
                            <td>&nbsp;</td>
                            <td colspan="3" class="faded_out" style="font-size: 0.9em; font-style: italic;">
                                <?php echo htmlentities($spent_time->getComment(), ENT_COMPAT, \thebuggenie\core\framework\Context::getI18n()->getCharset()); ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="backdrop_detail_footer">
        <a href="javascript:void(0);" onclick="TBG.Main.Helpers.Backdrop.reset();"><?php echo __('Done'); ?></a>
    </div>
</div>
<?php if (isset($initial_view) && $initial_view == 'entry'): ?>
<script>
    $('issue_<?php echo $issue->getID(); ?>_timeentry').focus();
</script>
<?php endif; ?>
