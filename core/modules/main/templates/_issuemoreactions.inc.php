<?php if (isset($dynamic) && $dynamic == true): ?>
    <ul class="more_actions_dropdown popup_box dynamic_menu" data-menu-url="<?php echo (isset($board)) ? make_url('issue_moreactions', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'board_id' => $board->getID())) : make_url('issue_moreactions', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID())); ?>">
        <li class="spinning"><?php echo image_tag('spinning_32.gif'); ?></li>
    </ul>
<?php else: ?>
    <ul class="more_actions_dropdown popup_box">
        <?php if (!$issue->getProject()->isArchived() && $issue->canEditIssueDetails()): ?>
            <?php if (!isset($multi) || !$multi): ?>
                <li class="header"><?php echo __('Workflow transition actions'); ?></li>
                <?php if ($issue->isWorkflowTransitionsAvailable()): ?>
                    <?php foreach ($issue->getAvailableWorkflowTransitions() as $transition): ?>
                        <li>
                            <?php if ($transition->hasTemplate()): ?>
                                <?php echo javascript_link_tag($transition->getName(), array('onclick' => "TBG.Main.Helpers.Backdrop.show('".make_url('get_partial_for_backdrop', array('key' => 'workflow_transition', 'transition_id' => $transition->getID()))."&project_key=".$issue->getProject()->getKey()."&issue_id=".$issue->getID()."');")); ?>
                            <?php else: ?>
                                <?php echo javascript_link_tag(image_tag('spinning_16.gif', array('style' => 'display: none;', 'id' => 'transition_working_'.$transition->getID().'_indicator')).$transition->getName(), array('onclick' => "TBG.Search.interactiveWorkflowTransition('".make_url('transition_issues', array('project_key' => $issue->getProject()->getKey(), 'transition_id' => $transition->getID()))."&issue_ids[]=".$issue->getID()."', ".$transition->getID().");")); ?>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            <?php endif; ?>
            <?php if (!isset($multi) || !$multi): ?>
                <li class="header"><?php echo __('Additional actions available'); ?></li>
            <?php endif; ?>
            <?php if ($issue->canEditMilestone()): ?>
                <?php if ($issue->isOpen()): ?>
                    <li id="more_actions_mark_notblocking_link_<?php echo $issue->getID(); ?>"<?php if (!$issue->isBlocking()): ?> style="display: none;"<?php endif; ?>><?php echo javascript_link_tag(image_tag('icon_unblock.png').__("Mark as not blocking the next release"), array('onclick' => "TBG.Issues.toggleBlocking('".make_url('unblock', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getId()))."', ".$issue->getID().");")); ?></li>
                    <li id="more_actions_mark_blocking_link_<?php echo $issue->getID(); ?>"<?php if ($issue->isBlocking()): ?> style="display: none;"<?php endif; ?>><?php echo javascript_link_tag(image_tag('icon_block.png').__("Mark as blocking the next release"), array('onclick' => "TBG.Issues.toggleBlocking('".make_url('block', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getId()))."', ".$issue->getID().");")); ?></li>
                <?php else: ?>
                    <li id="more_actions_mark_notblocking_link_<?php echo $issue->getID(); ?>"<?php if (!$issue->isBlocking()): ?> style="display: none;"<?php endif; ?> class="disabled"><a href="javascript:void(0);"><?php echo image_tag('icon_unblock.png').__("Mark as not blocking the next release"); ?></a><div class="tooltip rightie"><?php echo __('This action is not available when this issue is closed'); ?></div></li>
                    <li id="more_actions_mark_blocking_link_<?php echo $issue->getID(); ?>"<?php if ($issue->isBlocking()): ?> style="display: none;"<?php endif; ?> class="disabled"><a href="javascript:void(0);"><?php echo image_tag('icon_block.png').__("Mark as blocking the next release"); ?></a><div class="tooltip rightie"><?php echo __('This action is not available when this issue is closed'); ?></div></li>
                <?php endif; ?>
                <li class="separator"></li>
            <?php endif; ?>
            <?php if ((!isset($multi) || !$multi) && $issue->isUpdateable() && $issue->canAttachLinks()): ?>
                <?php if ($issue->canAttachLinks()): ?>
                    <li><a href="javascript:void(0);" id="attach_link_button" onclick="TBG.Main.Profile.clearPopupsAndButtons();TBG.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'attachlink', 'issue_id' => $issue->getID())); ?>');"><?php echo image_tag('action_add_link.png').__('Attach a link'); ?></a></li>
                <?php endif; ?>
            <?php endif; ?>
            <?php if ($issue->isUpdateable() && \thebuggenie\core\framework\Settings::isUploadsEnabled() && $issue->canAttachFiles()): ?>
                <?php if (\thebuggenie\core\framework\Settings::isUploadsEnabled() && $issue->canAttachFiles()): ?>
                    <li><a href="javascript:void(0);" id="attach_file_button" onclick="TBG.Main.Profile.clearPopupsAndButtons();TBG.Main.showUploader('<?php echo make_url('get_partial_for_backdrop', array('key' => 'uploader', 'mode' => 'issue', 'issue_id' => $issue->getID())); ?>');"><?php echo image_tag('action_add_file.png').__('Attach a file'); ?></a></li>
                <?php else: ?>
                    <li class="disabled"><a href="javascript:void(0);" id="attach_file_button" onclick="TBG.Main.Helpers.Message.error('<?php echo __('File uploads are not enabled'); ?>', '<?php echo __('Before you can upload attachments, file uploads needs to be activated'); ?>');"><?php echo image_tag('action_add_file.png').__('Attach a file'); ?></a></li>
                <?php endif; ?>
            <?php endif; ?>
            <?php if ($issue->isUpdateable()): ?>
                <?php if ($issue->canEditAffectedComponents() || $issue->canEditAffectedBuilds() || $issue->canEditAffectedEditions()): ?>
                    <li><a id="affected_add_button" href="javascript:void(0);" onclick="TBG.Main.Profile.clearPopupsAndButtons();TBG.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'issue_add_item', 'issue_id' => $issue->getID())); ?>');"><?php echo image_tag('action_add_affected.png').__('Add affected item'); ?></a></li>
                <?php else: ?>
                    <li class="disabled"><a id="affected_add_button" href="javascript:void(0);" onclick="TBG.Main.Helpers.Message.error('<?php echo __('You are not allowed to add an item to this list'); ?>');"><?php echo image_tag('action_add_affected.png').__('Add affected item'); ?></a></li>
                <?php endif; ?>
            <?php elseif ($issue->canEditAffectedComponents() || $issue->canEditAffectedBuilds() || $issue->canEditAffectedEditions()): ?>
                <li class="disabled"><a href="javascript:void(0);"><?php echo image_tag('action_add_affected.png').__("Add affected item"); ?></a><div class="tooltip rightie"><?php echo __('This action is not available when this issue is closed'); ?></div></li>
            <?php endif; ?>
            <li class="separator"></li>
            <?php if ($issue->isUpdateable()): ?>
                <?php if ($issue->canAddRelatedIssues() && $tbg_user->canReportIssues($issue->getProject())): ?>
                    <?php if (isset($board)): ?>
                        <?php if (!$board->getTaskIssuetypeID()): ?>
                            <li><?php echo javascript_link_tag(image_tag('icon_new_related_issue.png').__('Create a new child issue'), array('onclick' => "TBG.Main.Profile.clearPopupsAndButtons();TBG.Main.Helpers.Backdrop.show('".make_url('get_partial_for_backdrop', array('key' => 'reportissue', 'project_id' => $issue->getProject()->getId(), 'parent_issue_id' => $issue->getID()))."');", 'title' => __('Create a new child issue'))); ?></li>
                        <?php elseif ($issue->getIssuetype()->getID() != $board->getTaskIssuetypeID()): ?>
                            <li><?php echo javascript_link_tag(image_tag('icon_new_related_issue.png').__('Add a new task'), array('onclick' => "TBG.Main.Helpers.Backdrop.show('".make_url('get_partial_for_backdrop', array('key' => 'reportissue', 'project_id' => $issue->getProject()->getId(), 'parent_issue_id' => $issue->getID(), 'issuetype_id' => $board->getTaskIssuetypeID(), 'lock_issuetype' => 1))."');", 'title' => __('Add a new task'))); ?></li>
                        <?php endif; ?>
                    <?php else: ?>
                        <li><?php echo javascript_link_tag(image_tag('icon_new_related_issue.png').__('Create a new related issue'), array('onclick' => "TBG.Main.Profile.clearPopupsAndButtons();TBG.Main.Helpers.Backdrop.show('".make_url('get_partial_for_backdrop', array('key' => 'reportissue', 'project_id' => $issue->getProject()->getId(), 'parent_issue_id' => $issue->getID()))."');", 'title' => __('Create a new child issue'))); ?></li>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if ($issue->canAddRelatedIssues()): ?>
                    <li><a href="javascript:void(0)" id="relate_to_existing_issue_button" onclick="TBG.Main.Profile.clearPopupsAndButtons();TBG.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'relate_issue', 'issue_id' => $issue->getID())); ?>');"><?php echo image_tag('action_add_related.png').__('Add an existing issue as a child issue'); ?></a></li>
                <?php endif; ?>
            <?php else: ?>
                <?php if ($issue->canAddRelatedIssues() && $tbg_user->canReportIssues($issue->getProject())): ?>
                    <li class="disabled"><a href="javascript:void(0);"><?php echo image_tag('icon_new_related_issue.png').__("Create a new related issue"); ?></a><div class="tooltip rightie"><?php echo __('This action is not available at this stage in the workflow'); ?></div></li>
                <?php endif; ?>
                <?php if ($issue->canAddRelatedIssues()): ?>
                    <li class="disabled"><a href="javascript:void(0);"><?php echo image_tag('action_add_related.png').__("Relate to an existing issue"); ?></a><div class="tooltip rightie"><?php echo __('This action is not available at this stage in the workflow'); ?></div></li>
                <?php endif; ?>
            <?php endif; ?>
            <?php if (!isset($times) || $times): ?>
                <li class="separator"></li>
                <?php if ($issue->canEditEstimatedTime()): ?>
                    <?php if ($issue->isUpdateable()): ?>
                        <li><a href="javascript:void(0);" onclick="TBG.Main.Profile.clearPopupsAndButtons();$('estimated_time_<?php echo $issue->getID(); ?>_change').toggle('block');" title="<?php echo ($issue->hasEstimatedTime()) ? __('Change estimate') : __('Estimate this issue'); ?>"><?php echo image_tag('icon_estimated_time.png').(($issue->hasEstimatedTime()) ? __('Change estimate') : __('Estimate this issue')); ?></a></li>
                    <?php else: ?>
                        <li class="disabled"><a href="javascript:void(0);"><?php echo image_tag('icon_estimated_time.png').__("Change estimate"); ?></a><div class="tooltip rightie"><?php echo __('This action is not available at this stage in the workflow'); ?></div></li>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endif; ?>
            <?php if ($issue->canEditSpentTime()): ?>
                <li><a href="javascript:void(0)" onclick="TBG.Main.Profile.clearPopupsAndButtons();TBG.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'issue_spenttimes', 'issue_id' => $issue->getID(), 'initial_view' => 'entry')); ?>');"><?php echo image_tag('icon_time.png').__('Log time spent'); ?></a></li>
            <?php endif; ?>
            <?php if ($issue->canEditAccessPolicy()): ?>
                <li class="separator"></li>
                <li><a href="javascript:void(0)" onclick="TBG.Main.Profile.clearPopupsAndButtons();TBG.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'issue_permissions', 'issue_id' => $issue->getID())); ?>');"><?php echo image_tag('action_update_access_policy.png').__("Update issue access policy"); ?></a></li>
                <li><a href="javascript:void(0)" onclick="TBG.Main.Profile.clearPopupsAndButtons();TBG.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'issue_subscribers', 'issue_id' => $issue->getID())); ?>');"><?php echo image_tag('star_list_small.png').__("Manage issue subscribers"); ?></a></li>
            <?php endif; ?>
            <?php if ($issue->canEditIssueDetails()): ?>
                <li><a href="javascript:void(0)" onclick="TBG.Main.Profile.clearPopupsAndButtons();TBG.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'move_issue', 'issue_id' => $issue->getID(), 'multi' => (int) (isset($multi) && $multi))); ?>');"><?php echo image_tag('icon_move.png').__("Move issue to another project"); ?></a></li>
            <?php endif; ?>
            <?php if ($issue->canDeleteIssue()): ?>
                <li class="separator"></li>
                <li><a href="javascript:void(0)" onclick="TBG.Main.Profile.clearPopupsAndButtons();TBG.Main.Helpers.Dialog.show('<?php echo __('Permanently delete this issue?'); ?>', '<?php echo __('Are you sure you wish to delete this issue? It will remain in the database for your records, but will not be accessible via The Bug Genie.'); ?>', {yes: {href: '<?php echo make_url('deleteissue', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID())); ?><?php if (isset($_SERVER['HTTP_REFERER'])): ?>?referrer=<?php echo $_SERVER['HTTP_REFERER']; ?><?php echo $issue->getMilestone() ? '#roadmap_milestone_' . $issue->getMilestone()->getID() : ''; endif; ?>' }, no: {click: TBG.Main.Helpers.Dialog.dismiss}});"><?php echo image_tag('icon_delete.png').__("Permanently delete this issue"); ?></a></li>
            <?php endif; ?>
        <?php else: ?>
            <li class="disabled"><a href="#"><?php echo __('No additional actions available'); ?></a></li>
        <?php endif; ?>
    </ul>
    <?php if (!isset($times) || $times): ?>
        <?php if ($issue->canEditEstimatedTime()): ?>
            <?php if (isset($board)): ?>
                <?php include_component('main/issueestimator', array('issue' => $issue, 'field' => 'estimated_time', 'instant_save' => true, 'board' => $board)); ?>
            <?php else: ?>
                <?php include_component('main/issueestimator', array('issue' => $issue, 'field' => 'estimated_time', 'instant_save' => true)); ?>
            <?php endif; ?>
        <?php endif; ?>
    <?php endif; ?>
<?php endif; ?>
