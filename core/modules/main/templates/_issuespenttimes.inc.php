<div class="backdrop_box large" id="viewissue_add_item_div">
    <div class="backdrop_detail_header">
        <span><?= __('Issue time tracking - time spent'); ?></span>
        <?= javascript_link_tag(fa_image_tag('plus'), array('onclick' => "$('time_spent_{$issue->getID()}_form').toggle();if ($('time_spent_{$issue->getID()}_form').visible()) { $('issue_{$issue->getID()}_timeentry').focus(); }", 'class' => 'add_link')); ?>
        <a href="javascript:void(0);" class="closer" onclick="TBG.Main.Helpers.Backdrop.reset();"><?= fa_image_tag('times'); ?></a>
    </div>
    <div id="backdrop_detail_content" class="backdrop_detail_content">
        <div class="lightyellowbox issue_timespent_form" id="time_spent_<?= $issue->getID(); ?>_form" style="<?php if (!isset($initial_view) || $initial_view != 'entry') echo 'display: none;'; ?>">
            <form action="<?= make_url('issue_edittimespent', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'entry_id' => 0)); ?>" onsubmit="TBG.Issues.editTimeEntry(this);return false;">
                <?php include_component('main/issuespenttimeentry', array('issue' => $issue, 'save' => true)); ?>
            </form>
        </div>
        <p>
            <?= __('The list below shows all time logged against this issue so far.'); ?>
        </p>
        <table id="timespent_list">
            <thead>
                <tr>
                    <th style="width: 100px;"><?= __('Date'); ?></th>
                    <th style="width: 100px;"><?= __('Activity'); ?></th>
                    <th style="width: auto;"><?= __('Logged by'); ?></th>
                    <th style="width: 150px; text-align: right;"><?= __('Time logged'); ?></th>
                    <th style="width: 150px; text-align: right;"><?= __('Actions'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($issue->getSpentTimes() as $spent_time): ?>
                    <tr id="issue_spenttime_<?= $spent_time->getID(); ?>">
                        <td><?= tbg_formatTime($spent_time->getEditedAt(), 14); ?></td>
                        <td style="font-size: 0.9em;"><?= ($spent_time->getActivityType() instanceof \thebuggenie\core\entities\ActivityType) ? $spent_time->getActivityType()->getName() : '-'; ?></td>
                        <td><?= include_component('main/userdropdown', array('user' => $spent_time->getUser())); ?></td>
                        <td style="text-align: right;"><?= \thebuggenie\core\entities\Issue::getFormattedTime($spent_time->getSpentTime(true, true)); ?></td>
                        <td style="text-align: right;" <?php if ($spent_time->getComment()): ?>rowspan="2"<?php endif; ?>>
                            <a href="javascript:void(0);" class="icon-link" onclick="TBG.Main.Helpers.Backdrop.show('<?= make_url('get_partial_for_backdrop', array('key' => 'issue_spenttime', 'entry_id' => $spent_time->getID())); ?>');"><?= fa_image_tag('edit'); ?></a>
                            <a href="javascript:void(0);" class="icon-link" onclick="TBG.Main.Helpers.Dialog.show('<?= __('Do you really want to remove this time entry?'); ?>', '<?= __('Removing this entry will change the number of points, minutes, hours, days, weeks or months spent on this issue.'); ?>', {yes: {click: function() {TBG.Issues.deleteTimeEntry('<?= make_url('issue_deletetimespent', array('project_key' => $spent_time->getIssue()->getProject()->getKey(), 'issue_id' => $spent_time->getIssueID(), 'entry_id' => $spent_time->getID())); ?>', <?= $spent_time->getID(); ?>); }}, no: { click: TBG.Main.Helpers.Dialog.dismiss }});return false;"><?= fa_image_tag('times', ['class' => 'delete']); ?></a>
                        </td>
                    </tr>
                    <?php if ($spent_time->getComment()): ?>
                        <tr id="issue_spenttime_<?= $spent_time->getID(); ?>_comment">
                            <td>&nbsp;</td>
                            <td colspan="3" class="faded_out" style="font-size: 0.9em; font-style: italic;">
                                <?= htmlentities($spent_time->getComment(), ENT_COMPAT, \thebuggenie\core\framework\Context::getI18n()->getCharset()); ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php if (isset($initial_view) && $initial_view == 'entry'): ?>
<script>
    $('issue_<?= $issue->getID(); ?>_timeentry').focus();
</script>
<?php endif; ?>
