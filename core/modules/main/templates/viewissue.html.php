<?php if ($issue instanceof \thebuggenie\core\entities\Issue): ?>
    <?php

        $tbg_response->addBreadcrumb(__('Issues'), make_url('project_issues', array('project_key' => \thebuggenie\core\framework\Context::getCurrentProject()->getKey())));
        $tbg_response->addBreadcrumb($issue->getFormattedIssueNo(true, true), make_url('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())));
        $tbg_response->setTitle('['.(($issue->isClosed()) ? mb_strtoupper(__('Closed')) : mb_strtoupper(__('Open'))) .'] ' . $issue->getFormattedIssueNo(true) . ' - ' . tbg_decodeUTF8($issue->getTitle()));

    ?>
    <?php \thebuggenie\core\framework\Event::createNew('core', 'viewissue_top', $issue)->trigger(); ?>
    <div id="issuetype_indicator_fullpage" style="display: none;" class="fullpage_backdrop">
        <div style="position: absolute; top: 45%; left: 40; z-index: 100001; color: #FFF; font-size: 15px; font-weight: bold;">
            <?php echo image_tag('spinning_32.gif'); ?><br>
            <?php echo __('Please wait while updating issue type'); ?>...
        </div>
    </div>
    <div id="issue_<?php echo $issue->getID(); ?>" class="viewissue_container <?php if ($issue->isBlocking()) echo ' blocking'; ?>">
        <div id="viewissue_header_container">
            <table cellpadding=0 cellspacing=0 class="title_area">
                <tr>
                    <td class="issue_navigation" id="go_previous_open_issue">
                        <?php echo link_tag(make_url('previousopenissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())), image_tag('previous_open_issue.png'), array('class' => 'image')); ?>
                        <div class="tooltip from-above leftie">
                            <?php echo __('Go to the previous open issue'); ?>
                        </div>
                    </td>
                    <td class="issue_navigation" id="go_previous_issue">
                        <?php echo link_tag(make_url('previousissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())), image_tag('previous_issue.png'), array('class' => 'image')); ?>
                        <div class="tooltip from-above leftie">
                            <?php echo __('Go to the previous issue (open or closed)'); ?>
                        </div>
                    </td>
                    <td class="title_left_images">
                        <?php if ($tbg_user->isGuest()): ?>
                            <?php echo image_tag('star_faded.png', array('id' => 'issue_favourite_faded_'.$issue->getId())); ?>
                            <div class="tooltip from-above leftie">
                                <?php echo __('Please log in to bookmark issues'); ?>
                            </div>
                        <?php else: ?>
                            <div class="tooltip from-above leftie">
                                <?php echo __('Click the star to toggle whether you want to be notified whenever this issue updates or changes'); ?><br>
                                <br>
                                <?php echo __('If you have the proper permissions, you can manage issue subscribers via the "%more_actions" button to the right.', array('%more_actions' => __('More actions'))); ?>
                            </div>
                            <?php echo image_tag('spinning_20.gif', array('id' => 'issue_favourite_indicator_'.$issue->getId(), 'style' => 'display: none;')); ?>
                            <?php echo image_tag('star_faded.png', array('id' => 'issue_favourite_faded_'.$issue->getId(), 'style' => 'cursor: pointer;'.(($tbg_user->isIssueStarred($issue->getID())) ? 'display: none;' : ''), 'onclick' => "TBG.Issues.toggleFavourite('".make_url('toggle_favourite_issue', array('issue_id' => $issue->getID(), 'user_id' => $tbg_user->getID()))."', ".$issue->getID().");")); ?>
                            <?php echo image_tag('star.png', array('id' => 'issue_favourite_normal_'.$issue->getId(), 'style' => 'cursor: pointer;'.((!$tbg_user->isIssueStarred($issue->getID())) ? 'display: none;' : ''), 'onclick' => "TBG.Issues.toggleFavourite('".make_url('toggle_favourite_issue', array('issue_id' => $issue->getID(), 'user_id' => $tbg_user->getID()))."', ".$issue->getID().");")); ?>
                        <?php endif; ?>
                    </td>
                    <td class="title_left_images">
                        <?php echo image_tag((($issue->hasIssueType()) ? $issue->getIssueType()->getIcon() : 'icon_unknown') . '_small.png', array('id' => 'issuetype_image')); ?>
                    </td>
                    <td id="title_field" class="<?php if ($issue->isTitleChanged()): ?>issue_detail_changed<?php endif; ?><?php if (!$issue->isTitleMerged()): ?> issue_detail_unmerged<?php endif; ?> hoverable">
                        <div class="viewissue_title">
                            <span class="faded_out" id="title_header">
                                <?php include_component('issueparent_crumbs', array('issue' => $issue)); ?>
                            </span>
                            <span id="issue_title">
                                <?php if ($issue->isEditable() && $issue->canEditTitle()): ?>
                                    <?php echo image_tag('icon_edit.png', array('class' => 'dropdown', 'id' => 'title_edit', 'onclick' => "$('title_field').toggleClassName('editing');$('title_change').show(); $('title_name').hide(); $('no_title').hide();")); ?>
                                    <a class="undo" href="javascript:void(0);" onclick="TBG.Issues.Field.revert('<?php echo make_url('issue_revertfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'title')); ?>', 'title');" title="<?php echo __('Undo this change'); ?>"><?php echo image_tag('undo.png', array('class' => 'undo')); ?></a>
                                    <?php echo image_tag('spinning_16.gif', array('style' => 'display: none; float: left; margin-right: 5px;', 'id' => 'title_undo_spinning')); ?>
                                <?php endif; ?>
                                <span id="title_content">
                                    <span class="faded_out" id="no_title" <?php if ($issue->getTitle() != ''):?> style="display: none;" <?php endif; ?>><?php echo __('Nothing entered.'); ?></span>
                                    <span id="title_name" title="<?php echo tbg_decodeUTF8($issue->getTitle()); ?>">
                                        <?php echo tbg_decodeUTF8($issue->getTitle()); ?>
                                    </span>
                                </span>
                            </span>
                            <?php if ($issue->isEditable() && $issue->canEditTitle()): ?>
                            <span id="title_change" style="display: none;">
                                <form id="title_form" action="<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'title')); ?>" method="post" onSubmit="TBG.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'title')) ?>', 'title'); return false;">
                                    <input type="text" name="value" value="<?php echo $issue->getTitle(); ?>"><span class="title_form_save_container"><?php echo __('%cancel or %save', array('%save' => '<input type="submit" class="button button-silver" value="'.__('Save').'">', '%cancel' => '<a href="#" onclick="$(\'title_field\').toggleClassName(\'editing\');$(\'title_change\').hide(); $(\'title_name\').show(); return false;">'.__('cancel').'</a>')); ?></span>
                                </form>
                                <?php echo image_tag('spinning_16.gif', array('style' => 'display: none; float: left; margin-right: 5px;', 'id' => 'title_spinning')); ?>
                                <span id="title_change_error" class="error_message" style="display: none;"></span>
                            </span>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td style="width: 100px; text-align: right;<?php if (!$issue->isVotesVisible()): ?> display: none;<?php endif; ?>" id="votes_additional"<?php if ($issue->isVotesVisible()): ?> class="visible"<?php endif; ?>>
                        <div id="viewissue_votes">
                            <table align="right">
                                <tr>
                                    <td id="vote_down">
                                        <?php $vote_down_options = ($issue->getProject()->isArchived() || $issue->hasUserVoted($tbg_user, false)) ? 'display: none;' : ''; ?>
                                        <?php $vote_down_faded_options = ($vote_down_options == '') ? 'display: none;' : ''; ?>
                                        <?php echo javascript_link_tag(image_tag('action_vote_minus.png'), array('onclick' => "TBG.Issues.voteDown('".make_url('issue_vote', array('issue_id' => $issue->getID(), 'vote' => 'down'))."');", 'id' => 'vote_down_link', 'class' => 'image', 'style' => $vote_down_options)); ?>
                                        <?php echo image_tag('spinning_16.gif', array('id' => 'vote_down_indicator', 'style' => 'display: none;')); ?>
                                        <?php echo image_tag('action_vote_minus_faded.png', array('id' => 'vote_down_faded', 'style' => $vote_down_faded_options)); ?>
                                    </td>
                                    <td class="votes">
                                        <div id="issue_votes"><?php echo $issue->getVotes(); ?></div>
                                        <div class="votes_header"><?php echo __('Votes'); ?></div>
                                    </td>
                                    <td id="vote_up">
                                        <?php $vote_up_options = ($issue->getProject()->isArchived() || $issue->hasUserVoted($tbg_user, true)) ? 'display: none;' : ''; ?>
                                        <?php $vote_up_faded_options = ($vote_up_options == '') ? 'display: none;' : ''; ?>
                                        <?php echo javascript_link_tag(image_tag('action_vote_plus.png'), array('onclick' => "TBG.Issues.voteUp('".make_url('issue_vote', array('issue_id' => $issue->getID(), 'vote' => 'up'))."');", 'id' => 'vote_up_link', 'class' => 'image', 'style' => $vote_up_options)); ?>
                                        <?php echo image_tag('spinning_16.gif', array('id' => 'vote_up_indicator', 'style' => 'display: none;')); ?>
                                        <?php echo image_tag('action_vote_plus_faded.png', array('id' => 'vote_up_faded', 'style' => $vote_up_faded_options)); ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </td>
                    <td style="width: 80px;<?php if (!$issue->isUserPainVisible()): ?> display: none;<?php endif; ?>" id="user_pain_additional"<?php if ($issue->isVotesVisible()): ?> class="visible"<?php endif; ?>>
                        <div title="<?php echo __('This is the user pain value for this issue'); ?>" id="viewissue_triaging">
                            <div class="user_pain" id="issue_user_pain"><?php echo $issue->getUserPain(); ?></div>
                            <div class="user_pain_calculated" id="issue_user_pain_calculated"><?php echo $issue->getUserPainDiffText(); ?></div>
                        </div>
                    </td>
                    <td class="issue_navigation" id="go_next_issue">
                        <?php echo link_tag(make_url('nextissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())), image_tag('next_issue.png'), array('class' => 'image')); ?>
                        <div class="tooltip from-above rightie">
                            <?php echo __('Go to the next issue (open or closed)'); ?>
                        </div>
                    </td>
                    <td class="issue_navigation" id="go_next_open_issue">
                        <?php echo link_tag(make_url('nextopenissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())), image_tag('next_open_issue.png'), array('class' => 'image')); ?>
                        <div class="tooltip from-above rightie">
                            <?php echo __('Go to the next open issue'); ?>
                        </div>
                    </td>
                </tr>
            </table>
            <div id="issue_info_container">
                <div class="issue_info error<?php if (isset($issue_unsaved)): ?> active<?php endif; ?>" id="viewissue_unsaved"<?php if (!isset($issue_unsaved)): ?> style="display: none;"<?php endif; ?>>
                    <div class="header"><?php echo __('Could not save your changes'); ?></div>
                </div>
                <div class="issue_info error<?php if ($issue->hasMergeErrors()): ?> active<?php endif; ?>" id="viewissue_merge_errors"<?php if (!$issue->hasMergeErrors()): ?> style="display: none;"<?php endif; ?>>
                    <div class="header"><?php echo __('This issue has been changed since you started editing it'); ?></div>
                    <div class="content"><?php echo __('Data that has been changed is highlighted in red below. Undo your changes to see the updated information'); ?></div>
                </div>

                <div class="issue_info important" id="viewissue_changed" <?php if (!$issue->hasUnsavedChanges()): ?>style="display: none;"<?php endif; ?>>
                    <form action="<?php echo make_url('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())); ?>" method="post">
                        <div class="buttons">
                            <input class="button button-silver" type="submit" value="<?php echo __('Save changes'); ?>">
                            <button class="button button-silver" onclick="$('comment_add_button').hide(); $('comment_add').show();$('comment_save_changes').checked = true;$('comment_bodybox').focus();return false;"><?php echo __('Add comment and save changes'); ?></button>
                        </div>
                        <input type="hidden" name="issue_action" value="save">
                    </form>
                    <?php echo __("You have changed this issue, but haven't saved your changes yet. To save it, press the %save_changes button to the right", array('%save_changes' => '<b>' . __("Save changes") . '</b>')); ?>
                </div>
                <?php if (isset($error) && $error): ?>
                    <div class="issue_info error" id="viewissue_error">
                        <?php if ($error == 'transition_error'): ?>
                            <div class="header"><?php echo __('There was an error trying to move this issue to the next step in the workflow'); ?></div>
                            <div class="content" style="text-align: left;">
                                <?php echo __('The following actions could not be performed because of missing or invalid values: %list', array('%list' => '')); ?><br>
                                <ul>
                                    <?php foreach (\thebuggenie\core\framework\Context::getMessageAndClear('issue_workflow_errors') as $error_field): ?>
                                        <li><?php

                                            switch ($error_field)
                                            {
                                                case \thebuggenie\core\entities\WorkflowTransitionValidationRule::RULE_MAX_ASSIGNED_ISSUES:
                                                    echo __('Could not assign issue to the selected user because this users assigned issues limit is reached');
                                                    break;
                                                case \thebuggenie\core\entities\WorkflowTransitionValidationRule::RULE_PRIORITY_VALID:
                                                    echo __('Could not set priority');
                                                    break;
                                                case \thebuggenie\core\entities\WorkflowTransitionValidationRule::RULE_REPRODUCABILITY_VALID:
                                                    echo __('Could not set reproducability');
                                                    break;
                                                case \thebuggenie\core\entities\WorkflowTransitionValidationRule::RULE_RESOLUTION_VALID:
                                                    echo __('Could not set resolution');
                                                    break;
                                                case \thebuggenie\core\entities\WorkflowTransitionValidationRule::RULE_STATUS_VALID:
                                                    echo __('Could not set status');
                                                    break;
                                                case \thebuggenie\core\entities\WorkflowTransitionAction::ACTION_ASSIGN_ISSUE:
                                                    echo __('Could not assign issue to the any user or team because none were provided');
                                                    break;
                                                case \thebuggenie\core\entities\WorkflowTransitionAction::ACTION_SET_MILESTONE:
                                                    echo __('Could not assign the issue to a milestone because none was provided');
                                                    break;
                                                case \thebuggenie\core\entities\WorkflowTransitionAction::ACTION_SET_PRIORITY:
                                                    echo __('Could not set issue priority because none was provided');
                                                    break;
                                                case \thebuggenie\core\entities\WorkflowTransitionAction::ACTION_SET_REPRODUCABILITY:
                                                    echo __('Could not set issue reproducability because none was provided');
                                                    break;
                                                case \thebuggenie\core\entities\WorkflowTransitionAction::ACTION_SET_RESOLUTION:
                                                    echo __('Could not set issue resolution because none was provided');
                                                    break;
                                                case \thebuggenie\core\entities\WorkflowTransitionAction::ACTION_SET_STATUS:
                                                    echo __('Could not set issue status because none was provided');
                                                    break;
                                                default:
                                                    echo $error_field;
                                                    break;
                                            }

                                        ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php else: ?>
                            <div class="header"><?php echo __('There was an error trying to save changes to this issue'); ?></div>
                            <div class="content">
                                <?php if (isset($workflow_error) && $workflow_error): ?>
                                    <?php echo __('No workflow step matches this issue after changes are saved. Please either use the workflow action buttons, or make sure your changes are valid within the current project workflow for this issue type.'); ?>
                                <?php else: ?>
                                    <?php echo $error; ?>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                <?php if (isset($issue_saved)): ?>
                    <div class="issue_info successful" id="viewissue_saved">
                        <?php echo __('Your changes have been saved'); ?>
                        <div class="buttons">
                            <button class="button button-silver" onclick="$('viewissue_saved').hide();"><?php echo __('OK'); ?></button>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if (isset($issue_message)): ?>
                    <div class="issue_info successful" id="viewissue_saved">
                        <?php echo $issue_message; ?>
                        <div class="buttons">
                            <button class="button button-silver" onclick="$('viewissue_saved').hide();"><?php echo __('OK'); ?></button>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if (isset($issue_file_uploaded)): ?>
                    <div class="issue_info successful" id="viewissue_saved">
                        <?php echo __('The file was attached to this issue'); ?>
                        <div class="buttons">
                            <button class="button button-silver" onclick="$('viewissue_saved').hide();"><?php echo __('OK'); ?></button>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if ($issue->isBeingWorkedOn() && $issue->isOpen()): ?>
                    <div class="issue_info information" id="viewissue_being_worked_on">
                        <?php if ($issue->getUserWorkingOnIssue()->getID() == $tbg_user->getID()): ?>
                            <?php echo __('You have been working on this issue since %time', array('%time' => tbg_formatTime($issue->getWorkedOnSince(), 6))); ?>
                        <?php elseif ($issue->getAssignee() instanceof \thebuggenie\core\entities\Team): ?>
                            <?php echo __('%teamname has been working on this issue since %time', array('%teamname' => $issue->getAssignee()->getName(), '%time' => tbg_formatTime($issue->getWorkedOnSince(), 6))); ?>
                        <?php else: ?>
                            <?php echo __('%user has been working on this issue since %time', array('%user' => $issue->getUserWorkingOnIssue()->getNameWithUsername(), '%time' => tbg_formatTime($issue->getWorkedOnSince(), 6))); ?>
                        <?php endif; ?>
                        <div class="buttons">
                            <button class="button button-silver" onclick="$('viewissue_being_worked_on').hide();"><?php echo __('OK'); ?></button>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="issue_info error" id="blocking_div"<?php if (!$issue->isBlocking()): ?> style="display: none;"<?php endif; ?>>
                    <?php echo __('This issue is blocking the next release'); ?>
                </div>
                <?php if ($issue->isDuplicate()): ?>
                    <div class="issue_info information" id="viewissue_duplicate">
                        <?php echo image_tag('icon_info.png', array('style' => 'float: left; margin: 0 5px 0 5px;')); ?>
                        <?php echo __('This issue is a duplicate of issue %link_to_duplicate_issue', array('%link_to_duplicate_issue' => link_tag(make_url('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getDuplicateOf()->getFormattedIssueNo())), $issue->getDuplicateOf()->getFormattedIssueNo(true)) . ' - "' . $issue->getDuplicateOf()->getTitle() . '"')); ?>
                    </div>
                <?php endif; ?>
                <?php if ($issue->isClosed()): ?>
                    <div class="issue_info information" id="viewissue_closed">
                        <?php echo image_tag('icon_info.png', array('style' => 'float: left; margin: 0 5px 0 5px;')); ?>
                        <?php echo __('This issue has been closed with status "%status_name" and resolution "%resolution".', array('%status_name' => (($issue->getStatus() instanceof \thebuggenie\core\entities\Status) ? $issue->getStatus()->getName() : __('Not determined')), '%resolution' => (($issue->getResolution() instanceof \thebuggenie\core\entities\Resolution) ? $issue->getResolution()->getName() : __('Not determined')))); ?>
                    </div>
                <?php endif; ?>
                <?php if ($issue->getProject()->isArchived()): ?>
                    <div class="issue_info important" id="viewissue_archived">
                        <?php echo image_tag('icon_important.png', array('style' => 'float: left; margin: 0 5px 0 5px;')); ?>
                        <?php echo __('The project this issue belongs to has been archived, and so this issue is now read only'); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div id="workflow_actions">
            <ul class="workflow_actions simple_list">
                <?php if ($issue->isWorkflowTransitionsAvailable()): ?>
                    <?php $cc = 1; $num_transitions = count($issue->getAvailableWorkflowTransitions()); ?>
                    <?php foreach ($issue->getAvailableWorkflowTransitions() as $transition): ?>
                        <li class="workflow">
                            <div class="tooltip from-above rightie">
                                <?php echo $transition->getDescription(); ?>
                            </div>
                            <?php if ($transition->hasTemplate()): ?>
                                <input class="button button-silver<?php if ($cc == 1): ?> first<?php endif; if ($cc == $num_transitions): ?> last<?php endif; ?>" type="button" value="<?php echo $transition->getName(); ?>" onclick="TBG.Issues.showWorkflowTransition(<?php echo $transition->getID(); ?>);">
                            <?php else: ?>
                                <form action="<?php echo make_url('transition_issue', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'transition_id' => $transition->getID())); ?>" method="post">
                                    <input type="submit" class="button button-silver<?php if ($cc == 1): ?> first<?php endif; if ($cc == $num_transitions): ?> last<?php endif; ?>" value="<?php echo $transition->getName(); ?>">
                                </form>
                            <?php endif; ?>
                        </li>
                        <?php $cc++; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
                <li class="more_actions">
                    <input class="dropper button button-silver first last" id="more_actions_<?php echo $issue->getID(); ?>_button" type="button" value="<?php echo ($issue->isWorkflowTransitionsAvailable()) ? __('More actions') : __('Actions'); ?>">
                    <?php include_component('main/issuemoreactions', array('issue' => $issue, 'times' => false)); ?>
                </li>
            </ul>
        </div>
        <div id="viewissue_left_box_top">
            <div id="issue_view">
                <div id="issue_main_container">
                    <div class="issue_main" id="issue_main">
                        <?php \thebuggenie\core\framework\Event::createNew('core', 'viewissue_right_top', $issue)->trigger(); ?>
                        <fieldset id="description_field"<?php if (!$issue->isDescriptionVisible()): ?> style="display: none;"<?php endif; ?> class="viewissue_description<?php if ($issue->isDescriptionChanged()): ?> issue_detail_changed<?php endif; ?><?php if (!$issue->isDescriptionMerged()): ?> issue_detail_unmerged<?php endif; ?> hoverable">
                            <legend id="description_header">
                                <?php if ($issue->isEditable() && $issue->canEditDescription()): ?>
                                    <a href="javascript:void(0);" onclick="TBG.Issues.Field.revert('<?php echo make_url('issue_revertfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'description')); ?>', 'description');" title="<?php echo __('Undo this change'); ?>"><?php echo image_tag('undo.png', array('class' => 'undo')); ?></a> <?php echo image_tag('spinning_16.gif', array('style' => 'display: none; float: left; margin-right: 5px;', 'id' => 'description_undo_spinning')); ?>
                                    <?php echo image_tag('icon_edit.png', array('class' => 'dropdown', 'id' => 'description_edit', 'onclick' => "$('description_edit').show('inline'); $('description_change').show(); $('description_name').hide(); $('no_description').hide();", 'title' => __('Click here to edit description'))); ?>
                                <?php endif; ?>
                                <?php echo __('Description'); ?>
                            </legend>
                            <div id="description_content">
                                <div class="faded_out" id="no_description" <?php if ($issue->getDescription() != ''):?> style="display: none;" <?php endif; ?>><?php echo __('Nothing entered.'); ?></div>
                                <div id="description_name" class="issue_inline_description">
                                    <?php if ($issue->getDescription()): ?>
                                        <?php echo $issue->getParsedDescription(array('issue' => $issue)); ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php if ($issue->isEditable() && $issue->canEditDescription()): ?>
                                <div id="description_change" style="display: none;" class="editor_container">
                                    <form id="description_form" action="<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'description')); ?>" method="post" onSubmit="TBG.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'description')) ?>', 'description'); return false;">
                                        <?php include_component('main/textarea', array('area_name' => 'value', 'target_type' => 'issue', 'target_id' => $issue->getID(), 'area_id' => 'description_form_value', 'syntax' => \thebuggenie\core\framework\Settings::getSyntaxClass($issue->getDescriptionSyntax()), 'height' => '250px', 'width' => '100%', 'value' => htmlentities($issue->getDescription(), ENT_COMPAT, \thebuggenie\core\framework\Context::getI18n()->getCharset()))); ?>
                                        <div class="textarea_save_container">
                                            <?php echo __('%cancel or %save', array('%save' => '<input class="button button-silver" type="submit" value="'.__('Save').'">', '%cancel' => javascript_link_tag(__('Cancel'), array('onclick' => "$('description_edit').style.display = '';$('description_change').hide();".(($issue->getDescription() != '') ? "$('description_name').show();" : "$('no_description').show();")."return false;")))); ?>
                                        </div>
                                    </form>
                                    <?php echo image_tag('spinning_16.gif', array('style' => 'display: none; float: left; margin-right: 5px;', 'id' => 'description_spinning')); ?>
                                    <div id="description_change_error" class="error_message" style="display: none;"></div>
                                </div>
                            <?php endif; ?>
                        </fieldset>
                        <fieldset id="reproduction_steps_field"<?php if (!$issue->isReproductionStepsVisible()): ?> style="display: none;"<?php endif; ?> class="hoverable<?php if ($issue->isReproduction_StepsChanged()): ?> issue_detail_changed<?php endif; ?><?php if (!$issue->isReproduction_StepsMerged()): ?> issue_detail_unmerged<?php endif; ?>">
                            <legend id="reproduction_steps_header">
                                <?php if ($issue->isEditable() && $issue->canEditReproductionSteps()): ?>
                                    <a href="javascript:void(0);" onclick="TBG.Issues.Field.revert('<?php echo make_url('issue_revertfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'reproduction_steps')); ?>', 'reproduction_steps');" title="<?php echo __('Undo this change'); ?>"><?php echo image_tag('undo.png', array('class' => 'undo')); ?></a> <?php echo image_tag('spinning_16.gif', array('style' => 'display: none; float: left; margin-right: 5px;', 'id' => 'reproduction_steps_undo_spinning')); ?>
                                    <?php echo image_tag('icon_edit.png', array('class' => 'dropdown', 'id' => 'reproduction_steps_edit', 'onclick' => "$('reproduction_steps_change').show(); $('reproduction_steps_name').hide(); $('no_reproduction_steps').hide();", 'title' => __('Click here to edit reproduction steps'))); ?>
                                <?php endif; ?>
                                <?php echo __('Steps to reproduce this issue'); ?>
                            </legend>
                            <div id="reproduction_steps_content">
                                <div class="faded_out" id="no_reproduction_steps" <?php if ($issue->getReproductionSteps() != ''):?> style="display: none;" <?php endif; ?>><?php echo __('Nothing entered.'); ?></div>
                                <div id="reproduction_steps_name" class="issue_inline_description">
                                    <?php if ($issue->getReproductionSteps()): ?>
                                        <?php echo $issue->getParsedReproductionSteps(array('issue' => $issue)); ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php if ($issue->isEditable() && $issue->canEditReproductionSteps()): ?>
                                <div id="reproduction_steps_change" style="display: none;" class="editor_container">
                                    <form id="reproduction_steps_form" action="<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'reproduction_steps')); ?>" method="post" onSubmit="TBG.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'reproduction_steps')) ?>', 'reproduction_steps'); return false;">
                                        <?php include_component('main/textarea', array('area_name' => 'value', 'target_type' => 'issue', 'target_id' => $issue->getID(), 'area_id' => 'reproduction_steps_form_value', 'syntax' => \thebuggenie\core\framework\Settings::getSyntaxClass($issue->getReproductionStepsSyntax()), 'height' => '250px', 'width' => '100%', 'value' => htmlentities($issue->getReproductionSteps(), ENT_COMPAT, \thebuggenie\core\framework\Context::getI18n()->getCharset()))); ?>
                                        <div class="textarea_save_container">
                                            <?php echo __('%cancel or %save', array('%save' => '<input class="button button-silver" type="submit" value="'.__('Save').'">', '%cancel' => javascript_link_tag(__('Cancel'), array('onclick' => "$('reproduction_steps_change').hide();".(($issue->getReproductionSteps() != '') ? "$('reproduction_steps_name').show();" : "$('no_reproduction_steps').show();")."return false;")))); ?>
                                        </div>
                                    </form>
                                    <?php echo image_tag('spinning_16.gif', array('style' => 'display: none; float: left; margin-right: 5px;', 'id' => 'reproduction_steps_spinning')); ?>
                                    <div id="reproduction_steps_change_error" class="error_message" style="display: none;"></div>
                                </div>
                            <?php endif; ?>
                        </fieldset>
                        <?php include_component('main/issuemaincustomfields', array('issue' => $issue)); ?>
                        <?php \thebuggenie\core\framework\Event::createNew('core', 'viewissue_right_bottom', $issue)->trigger(); ?>
                        <fieldset class="comments" id="viewissue_comments_container">
                            <legend class="viewissue_comments_header">
                                <?php echo __('Comments (%count)', array('%count' => '<span id="viewissue_comment_count">'.$issue->countUserComments().'</span>')); ?>
                                <div class="dropper_container">
                                    <?php echo image_tag('icon-mono-settings.png', array('class' => 'dropper')); ?>
                                    <ul class="more_actions_dropdown dropdown_box popup_box leftie">
                                        <li><a href="javascript:void(0);" id="comments_show_system_comments_toggle" onclick="$$('#comments_box .system_comment').each(function (elm) { $(elm).toggle(); });" /><?php echo __('Toggle system-generated comments'); ?></a></li>
                                    </ul>
                                </div>
                                <ul class="simple_list button_container" id="add_comment_button_container">
                                    <li id="comment_add_button"><input class="button button-silver first last" type="button" onclick="TBG.Main.Comment.showPost();" value="<?php echo __('Post comment'); ?>"></li>
                                </ul>
                            </legend>
                            <div id="viewissue_comments">
                                <?php include_component('main/comments', array('target_id' => $issue->getID(), 'mentionable_target_type' => 'issue', 'target_type' => \thebuggenie\core\entities\Comment::TYPE_ISSUE, 'show_button' => false, 'comment_count_div' => 'viewissue_comment_count', 'save_changes_checked' => $issue->hasUnsavedChanges(), 'issue' => $issue, 'forward_url' => make_url('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo()), false))); ?>
                            </div>
                        </fieldset>
                        <fieldset class="viewissue_history">
                            <legend class="viewissue_history_header">
                                <?php echo __('History'); ?>
                                <?php echo image_tag('spinning_16.gif', array('style' => 'display: none;', 'id' => 'viewissue_log_loading_indicator')); ?>
                                <div class="button_container" id="viewissue_history_button_container">
                                    <input class="button button-silver first last" type="button" onclick="TBG.Issues.showLog('<?php echo make_url('issue_log', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID())); ?>');" value="<?php echo __('Show issue history'); ?>">
                                </div>
                            </legend>
                            <div id="viewissue_log_items"></div>
                        </fieldset>
                        <?php \thebuggenie\core\framework\Event::createNew('core', 'viewissue_before_tabs', $issue)->trigger(); ?>
                        <div id="viewissue_panes">
                            <?php \thebuggenie\core\framework\Event::createNew('core', 'viewissue_after_tabs', $issue)->trigger(); ?>
                        </div>
                    </div>
                </div>
                <div id="issue_details_container">
                    <div id="issue_details">
                        <div class="collapser_link" onclick="$('issue_details_container').toggleClassName('collapsed');$('issue_main_container').toggleClassName('uncollapsed');">
                            <a href="javascript:void(0);" class="image">
                                <?php echo image_tag('sidebar_collapse.png', array('class' => 'collapser')); ?>
                                <?php echo image_tag('sidebar_expand.png', array('class' => 'expander')); ?>
                            </a>
                        </div>
                        <div class="issue_details_fieldsets_wrapper"><?php include_component('main/issuedetailslisteditable', array('issue' => $issue)); ?></div>
                        <div style="clear: both; margin-bottom: 5px;"> </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include_component('main/issue_workflow_transition', compact('issue')); ?>
    <?php if ($tbg_user->isViewissueTutorialEnabled()): ?>
        <?php include_component('main/tutorial_viewissue', compact('issue')); ?>
    <?php endif; ?>
<?php elseif (isset($issue_deleted)): ?>
    <div class="greenbox" id="issue_deleted_message">
        <div class="header"><?php echo __("This issue has been deleted"); ?></div>
        <div class="content"><?php echo __("This message will disappear when you reload the page."); ?></div>
    </div>
<?php else: ?>
    <div class="redbox" id="notfound_error">
        <div class="header"><?php echo __("This issue can not be displayed"); ?></div>
        <div class="content"><?php echo __("This issue either does not exist, has been deleted or you do not have permission to view it."); ?></div>
    </div>
<?php endif; ?>
