<div id="tab_<?php echo $id ?>_pane"<?php if ($default_displayed !== true): ?> style="display: none;"<?php endif;?>>
    <?php if (isset($link)): echo $link; endif; ?>
    <?php if (count($issues) > 0): ?>
        <table cellpadding=0 cellspacing=0 class="recent_activities" style="margin-top: 5px;">
        <?php foreach ($issues as $issue): ?>
            <?php if ($issue->isDeleted()): continue; endif; ?>
            <tr>
                <td class="imgtd"><?php echo image_tag($issue->getIssueType()->getIcon() . '_tiny.png'); ?></td>
                <td>
                    <?php echo link_tag(make_url('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())), '<b>' . $issue->getFormattedIssueNo(true) . ' - ' . $issue->getTitle() . '</b>', array('class' => (($issue->isClosed()) ? 'issue_closed' : 'issue_open'))); ?><br>
                    <span class="faded_out dark recent_activities_details">
                        <?php echo tbg_formatTime($issue->getPosted(), 20); ?>,
                        <strong><?php echo ($issue->getStatus() instanceof \thebuggenie\core\entities\Datatype) ? $issue->getStatus()->getName() : __('Status not determined'); ?></strong>
                        <?php if ($issue->isClosed() && is_object($issue->getResolution())): ?>
                        , <?php echo $issue->getResolution()->getName(); ?>
                        <?php endif; ?>
                    </span>
                </td>
            </tr>
        <?php endforeach; ?>
        </table>
    <?php else: ?>
        <div class="faded_out dark" style="padding: 5px; font-size: 12px;"><?php echo __($empty); ?></div>
    <?php endif; ?>
</div>