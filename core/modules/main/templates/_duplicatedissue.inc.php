<li class="hover_highlight<?php if ($issue->isClosed()): ?> closed<?php endif; ?> duplicatedissue" id="duplicated_issue_<?php echo $issue->getID(); ?>">
    <?php echo javascript_link_tag(image_tag('action_delete.png'), array('class' => 'removelink', 'onclick' => "TBG.Main.Helpers.Dialog.show('".__('Remove issue duplicate of issue %itemname?', array('%itemname' => $issue->getFormattedIssueNo(true)))."', '".__('Please confirm that you want to remove this item from the list of duplicate issues of this issue')."', {yes: {click: function() {TBG.Issues.removeDuplicated('".make_url('viewissue_remove_duplicated_issue', array('project_key' => $duplicated_issue->getProject()->getKey(), 'issue_id' => $duplicated_issue->getID(), 'duplicated_issue_id' => $issue->getID()))."', ".$issue->getID().");TBG.Main.Helpers.Dialog.dismiss();}}, no: {click: TBG.Main.Helpers.Dialog.dismiss}});")); ?>
    <span class="issue_state <?php echo $issue->isClosed() ? 'closed' : 'open'; ?>"><?php echo $issue->isClosed() ? __('Closed') : __('Open'); ?></span>
    <div class="status_badge" style="background-color: <?php echo ($issue->getStatus() instanceof \thebuggenie\core\entities\Datatype) ? $issue->getStatus()->getColor() : '#FFF'; ?>;" title="<?php echo ($issue->getStatus() instanceof \thebuggenie\core\entities\Datatype) ? __($issue->getStatus()->getName()) : __('Status not determined'); ?>"><?php echo ($issue->getStatus() instanceof \thebuggenie\core\entities\Datatype) ? $issue->getStatus()->getName() : __('Unknown'); ?></div>
    <a href="<?php echo make_url('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())); ?>"><?php echo __('%issuetype %issue_no', array('%issuetype' => (($issue->hasIssueType()) ? $issue->getIssueType()->getName() : __('Unknown issuetype')), '%issue_no' => $issue->getFormattedIssueNo(true))); ?>
    <br style="clear: both;">
    <span title="<?php echo tbg_decodeUTF8($issue->getTitle()); ?>"><?php echo tbg_decodeUTF8($issue->getTitle()); ?></span></a>
    <?php if ($issue->isAssigned()): ?>
        <div class="faded_out">
            <?php if ($issue->getAssignee() instanceof \thebuggenie\core\entities\User): ?>
                (<?php echo __('Assigned to %assignee', array('%assignee' => get_component_html('main/userdropdown', array('user' => $issue->getAssignee(), 'show_avatar' => true)))); ?>)
            <?php else: ?>
                (<?php echo __('Assigned to %assignee', array('%assignee' => get_component_html('main/teamdropdown', array('team' => $issue->getAssignee())))); ?>)
            <?php endif; ?>
        </div>
    <?php endif; ?>
</li>
