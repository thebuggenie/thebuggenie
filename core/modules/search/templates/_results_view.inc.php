<?php if ($resultcount > 0): ?>
    <table cellpadding=0 cellspacing=0 class="dashboard_view_issues">
    <?php foreach ($issues as $issue): ?>
        <tr class="<?php if ($issue->isClosed()): ?>issue_closed<?php else: ?>issue_open<?php endif; ?> <?php if ($issue->isBlocking()): ?>issue_blocking<?php endif; ?>">
            <td>
                <div class="issue_link">
                    <?php echo image_tag($issue->getIssueType()->getIcon() . '_small.png'); ?>
                    <?php echo link_tag(make_url('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())), $issue->getFormattedTitle(true, false), array('style' => 'text-overflow: ellipsis;')); ?>
                </div>
                <div class="status_badge" style="background-color: <?php echo ($issue->getStatus() instanceof \thebuggenie\core\entities\Datatype) ? $issue->getStatus()->getColor() : '#FFF'; ?>;color: <?php echo ($issue->getStatus() instanceof \thebuggenie\core\entities\Datatype) ? $issue->getStatus()->getTextColor() : '#333'; ?>;" id="status_<?php echo $issue->getID(); ?>_color">
                    <span id="status_content"><?php echo ($issue->getStatus() instanceof \thebuggenie\core\entities\Datatype) ? __($issue->getStatus()->getName()) : __('Unknown'); ?></span>
                </div>
                <span class="secondary">
                    <?php echo __('Last updated: %updated_at', array('%updated_at' => tbg_formatTime($issue->getLastUpdatedTime(), 12))); ?>
                    <?php echo image_tag('icon_comments.png') . $issue->countUserComments(); ?>
                    <?php echo image_tag('icon_attached_information.png') . $issue->countFiles(); ?>
                </span>
            </td>
            <td>
                <?php if ($issue->isAssigned()): ?>
                    <?php if ($issue->getAssignee() instanceof \thebuggenie\core\entities\User): ?>
                        <?php echo include_component('main/userdropdown', array('user' => $issue->getAssignee(), 'size' => 'large', 'userstate' => false, 'displayname' => '')); ?>
                    <?php else: ?>
                        <?php echo include_component('main/teamdropdown', array('team' => $issue->getAssignee(), 'size' => 'large', 'displayname' => '')); ?>
                    <?php endif; ?>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </table>
<?php else: ?>
    <div class="faded_out" style="padding: 5px 5px 10px 5px;"><?php echo __('No issues in this list'); ?></div>
<?php endif; ?>
