<?php $c = 0; ?>
<?php foreach ($milestone->getIssues() as $issue): ?>
    <?php if ($issue->hasAccess()): ?>
        <div class="roadmap_issue<?php if ($issue->isClosed()): ?> faded_out issue_closed<?php elseif ($issue->isBlocking()): ?> blocking<?php endif; ?>">
            <div class="issue_status"><div style="border: 1px solid #AAA; background-color: <?php echo ($issue->getStatus() instanceof \thebuggenie\core\entities\Status) ? $issue->getStatus()->getColor() : '#FFF'; ?>; font-size: 1px; width: 13px; height: 13px;" title="<?php echo ($issue->getStatus() instanceof \thebuggenie\core\entities\Status) ? $issue->getStatus()->getName() : ''; ?>">&nbsp;</div></div>
            <?php if ($issue->isAssigned()): ?>
                <span class="faded_out">
                    <?php if ($issue->getAssignee() instanceof \thebuggenie\core\entities\User): ?>
                        (<?php echo __('Assigned to %assignee', array('%assignee' => get_component_html('main/userdropdown', array('user' => $issue->getAssignee(), 'show_avatar' => false)))); ?>)
                    <?php else: ?>
                        (<?php echo __('Assigned to %assignee', array('%assignee' => get_component_html('main/teamdropdown', array('team' => $issue->getAssignee())))); ?>)
                    <?php endif; ?>
                </span>
            <?php endif; ?>
            <?php echo link_tag(make_url('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())), $issue->getFormattedIssueNo(true, true) . ' - <span class="issue_title">' . $issue->getTitle() . '</span>'); ?>
            <?php if ($milestone->isSprint()): ?>
                <div class="issue_points"><?php echo __('%pts points', array('%pts' => $issue->getEstimatedPoints())); ?></div>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <?php $c++; ?>
    <?php endif; ?>
<?php endforeach; ?>
<?php if ($c > 0): ?>
<span class="faded_out"><?php echo __('This milestone also includes issues you do not have access to'); ?></span>
<?php endif; ?>
