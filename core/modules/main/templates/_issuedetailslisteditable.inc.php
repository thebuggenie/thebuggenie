<?php \thebuggenie\core\framework\Event::createNew('core', 'viewissue_left_top', $issue)->trigger(); ?>
<fieldset>
    <legend onclick="$('issue_details_fieldslist_basics').toggle();"><?php echo __('Issue basics'); ?></legend>
    <ul class="issue_details simple_list" id="issue_details_fieldslist_basics">
        <li id="issuetype_field" class="issue_detail_field primary <?php if ($issue->isIssuetypeChanged()): ?>issue_detail_changed<?php endif; ?><?php if (!$issue->isIssuetypeMerged()): ?> issue_detail_unmerged<?php endif; ?>">
            <dl class="viewissue_list">
                <dt id="issuetype_header">
                    <?php echo __('Type of issue'); ?>
                </dt>
                <dd class="hoverable">
                    <?php if ($issue->isEditable() && $issue->canEditIssuetype()): ?>
                        <a href="javascript:void(0);" onclick="TBG.Issues.Field.revert('<?php echo make_url('issue_revertfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'issuetype')); ?>', 'issuetype');" title="<?php echo __('Undo this change'); ?>"><?php echo image_tag('undo.png', array('class' => 'undo')); ?></a>
                        <?php echo image_tag('spinning_16.gif', array('style' => 'display: none; float: left; margin: 5px 5px 0 0;', 'id' => 'issuetype_undo_spinning')); ?>
                        <a href="javascript:void(0);" class="dropper dropdown_link" title="<?php echo __('Click to change issue type'); ?>"><?php echo image_tag('tabmenu_dropdown.png', array('class' => 'dropdown')); ?></a>
                        <ul id="issuetype_change" class="popup_box more_actions_dropdown">
                            <li class="header"><?php echo __('Set issue type'); ?></li>
                            <?php foreach ($issuetypes as $issuetype): ?>
                                <li>
                                    <a href="javascript:void(0);" onclick="TBG.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'issuetype', 'issuetype_id' => $issuetype->getID())); ?>', 'issuetype');">
                                        <?php echo image_tag($issuetype->getIcon() . '_tiny.png'); ?>
                                        <?php echo __($issuetype->getName()); ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                            <li id="issuetype_spinning" style="margin-top: 3px; display: none;"><?php echo image_tag('spinning_20.gif', array('style' => 'float: left; margin-right: 5px;')) . '&nbsp;' . __('Please wait'); ?>...</li>
                            <li id="issuetype_change_error" class="error_message" style="display: none;"></li>
                        </ul>
                    <?php endif; ?>
                    <span id="issuetype_content"><?php if ($issue->hasIssueType()) echo __($issue->getIssueType()->getName()); ?></span>
                    <span class="faded_out" id="no_issuetype"<?php if ($issue->getIssueType() instanceof \thebuggenie\core\entities\Issuetype): ?> style="display: none;"<?php endif; ?>><?php echo __('Unknown issue type'); ?></span>
                </dd>
            </dl>
        </li>
        <li id="shortname_field"<?php if (!$issue->isShortnameVisible()): ?> style="display: none;"<?php endif; ?> class="viewissue_shortname<?php if ($issue->isShortnameChanged()): ?> issue_detail_changed<?php endif; ?><?php if (!$issue->isShortnameMerged()): ?> issue_detail_unmerged<?php endif; ?> issue_detail_field hoverable">
            <dl class="viewissue_list">
                <dt id="shortname_header">
                    <?php echo __('Issue label'); ?>
                </dt>
                <dd class="hoverable">
                    <?php if ($issue->isEditable() && $issue->canEditShortname()): ?>
                        <a href="javascript:void(0);" onclick="TBG.Issues.Field.revert('<?php echo make_url('issue_revertfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'shortname')); ?>', 'shortname');" title="<?php echo __('Undo this change'); ?>"><?php echo image_tag('undo.png', array('class' => 'undo')); ?></a>
                        <?php echo image_tag('spinning_16.gif', array('style' => 'display: none; float: left; margin: 5px 5px 0 0;', 'id' => 'shortname_undo_spinning')); ?>
                        <a href="javascript:void(0);" class="dropper dropdown_link" title="<?php echo __('Click to edit issue label'); ?>"><?php echo image_tag('tabmenu_dropdown.png', array('class' => 'dropdown')); ?></a>
                        <ul id="shortname_change" class="popup_box more_actions_dropdown">
                            <li class="header"><?php echo __('Set issue label'); ?></li>
                            <li class="nohover">
                                <form id="shortname_form" action="<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'shortname')); ?>" method="post" onSubmit="TBG.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'shortname')) ?>', 'shortname', 'shortname'); return false;">
                                    <input type="text" name="shortname_value" value="<?php echo $issue->getShortname(); ?>" /><?php echo __('%save or %cancel', array('%save' => '<input type="submit" value="'.__('Save').'">', '%cancel' => '<a href="#" onclick="$(\'shortname_change\').hide(); return false;">'.__('cancel').'</a>')); ?>
                                </form>
                            </li>
                            <li id="shortname_spinning" style="margin-top: 3px; display: none;"><?php echo image_tag('spinning_20.gif', array('style' => 'float: left; margin-right: 5px;')) . '&nbsp;' . __('Please wait'); ?>...</li>
                            <li id="shortname_change_error" class="error_message" style="display: none;"></li>
                        </ul>
                    <?php endif; ?>
                    <span id="shortname_name"><?php if ($issue->hasShortname()) echo $issue->getShortname(); ?></span>
                    <span class="faded_out" id="no_shortname"<?php if ($issue->hasShortname()): ?> style="display: none;"<?php endif; ?>><?php echo __('No label set'); ?></span>
                </dd>
            </dl>
        </li>
        <?php $field = $fields_list['category']; unset($fields_list['category']); ?>
        <?php include_component('main/issuedetailslistfield', array('field' => 'category', 'info' => $field, 'issue' => $issue)); ?>
        <?php $field = $fields_list['milestone']; unset($fields_list['milestone']); ?>
        <?php include_component('main/issuedetailslistfield', array('field' => 'milestone', 'info' => $field, 'issue' => $issue)); ?>
        <li id="status_field" class="issue_detail_field primary <?php if ($issue->isStatusChanged()): ?>issue_detail_changed<?php endif; ?><?php if (!$issue->isStatusMerged()): ?> issue_detail_unmerged<?php endif; ?>">
            <dl class="viewissue_list">
                <dt id="status_header">
                    <?php echo __('Status'); ?>
                </dt>
                <dd class="hoverable">
                    <?php if ($issue->canEditStatus()): ?>
                        <a href="javascript:void(0);" onclick="TBG.Issues.Field.revert('<?php echo make_url('issue_revertfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'status')); ?>', 'status');" title="<?php echo __('Undo this change'); ?>"><?php echo image_tag('undo.png', array('class' => 'undo')); ?></a>
                        <?php echo image_tag('spinning_16.gif', array('style' => 'display: none; float: left; margin-right: 5px;', 'id' => 'status_undo_spinning')); ?>
                        <a href="javascript:void(0);" class="dropper dropdown_link" title="<?php echo __('Click to change status'); ?>"><?php echo image_tag('tabmenu_dropdown.png', array('class' => 'dropdown')); ?></a>
                        <ul class="popup_box more_actions_dropdown" id="status_change">
                            <li class="header"><?php echo __('Set status'); ?></li>
                            <?php foreach ($statuses as $status): ?>
                                <?php if (!$status->canUserSet($tbg_user)) continue; ?>
                                <li>
                                    <a href="javascript:void(0);" onclick="TBG.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'status', 'status_id' => $status->getID())); ?>', 'status');">
                                        <div class="status_badge" style="background-color: <?php echo ($status instanceof \thebuggenie\core\entities\Status) ? $status->getColor() : '#FFF'; ?>;color: <?php echo ($status instanceof \thebuggenie\core\entities\Status) ? $status->getTextColor() : '#333'; ?>;">
                                            <span>&nbsp;&nbsp;</span>
                                        </div>
                                        <?php echo __($status->getName()); ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                            <li id="status_spinning" style="margin-top: 3px; display: none;"><?php echo image_tag('spinning_20.gif', array('style' => 'float: left; margin-right: 5px;')) . '&nbsp;' . __('Please wait'); ?>...</li>
                            <li id="status_change_error" class="error_message" style="display: none;"></li>
                        </ul>
                    <?php endif; ?>
                    <div style="display: inline-block; float: left; width: auto;<?php if (!$issue->getStatus() instanceof \thebuggenie\core\entities\Datatype): ?> display: none;<?php endif; ?>" id="status_table">
                        <div class="status_badge" style="background-color: <?php echo ($issue->getStatus() instanceof \thebuggenie\core\entities\Datatype) ? $issue->getStatus()->getColor() : '#FFF'; ?>;color: <?php echo ($issue->getStatus() instanceof \thebuggenie\core\entities\Datatype) ? $issue->getStatus()->getTextColor() : '#333'; ?>;" id="status_<?php echo $issue->getID(); ?>_color">
                            <span id="status_content"><?php if ($issue->getStatus() instanceof \thebuggenie\core\entities\Datatype) echo __($issue->getStatus()->getName()); ?></span>
                        </div>
                    </div>
                    <span class="faded_out" id="no_status"<?php if ($issue->getStatus() instanceof \thebuggenie\core\entities\Datatype): ?> style="display: none;"<?php endif; ?>><?php echo __('Status not determined'); ?></span>
                </dd>
            </dl>
        </li>
        <li class="viewissue_list issue_detail_field<?php if ($issue->isPercentCompletedChanged()): ?> issue_detail_changed<?php endif; ?><?php if (!$issue->isPercentCompletedMerged()): ?> issue_detail_unmerged<?php endif; ?>" id="percent_complete_field"<?php if (!$issue->isPercentCompletedVisible()): ?> style="display: none;"<?php endif; ?>>
            <dl class="viewissue_list">
                <dt id="percent_complete_header"><?php echo __('Progress'); ?></dt>
                <dd id="percent_complete_content" class="hoverable">
                    <?php if ($issue->canEditPercentage()): ?>
                        <a href="javascript:void(0);" onclick="TBG.Issues.Field.revert('<?php echo make_url('issue_revertfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'percent_complete')); ?>', 'percent_complete');" title="<?php echo __('Undo this change'); ?>"><?php echo image_tag('undo.png', array('class' => 'undo')); ?></a>
                        <?php echo image_tag('spinning_16.gif', array('style' => 'display: none; float: left; margin-right: 5px;', 'id' => 'percent_complete_undo_spinning')); ?>
                        <a href="javascript:void(0);" class="dropper dropdown_link" title="<?php echo __('Click to set percent completed'); ?>"><?php echo image_tag('tabmenu_dropdown.png', array('class' => 'dropdown')); ?></a>
                        <ul class="popup_box more_actions_dropdown" id="percent_complete_change">
                            <li class="header"><?php echo __('Set percent completed'); ?></li>
                            <li><a href="javascript:void(0);" onclick="TBG.Issues.Field.setPercent('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'percent_complete', 'percent' => 0)); ?>', 'set');"><?php echo __('Clear percent completed'); ?></a></li>
                            <li class="separator"></li>
                            <li class="nohover">
                                <form id="percent_complete_form" method="post" accept-charset="<?php echo \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>" action="" onsubmit="TBG.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'percent_complete')); ?>', 'percent_complete', 'percent_complete');return false;">
                                    <label for="set_percent"><?php echo __('Percent complete'); ?></label>&nbsp;<input type="text" style="width: 40px;" name="percent" id="set_percent">&percnt;
                                    <input type="submit" value="<?php echo __('Set'); ?>">
                                </form>
                            </li>
                            <li id="percent_complete_spinning" style="margin-top: 3px; display: none;"><?php echo image_tag('spinning_20.gif', array('style' => 'float: left; margin-right: 5px;')) . '&nbsp;' . __('Please wait'); ?>...</li>
                        </ul>
                    <?php endif; ?>
                    <div id="issue_percent_complete">
                        <?php include_component('main/percentbar', array('percent' => $issue->getPercentCompleted(), 'height' => 16)); ?>
                    </div>
                </dd>
            </dl>
        </li>
        <?php $field = $fields_list['priority']; unset($fields_list['priority']); ?>
        <?php include_component('main/issuedetailslistfield', array('field' => 'priority', 'info' => $field, 'issue' => $issue)); ?>
    </ul>
</fieldset>
<fieldset id="issue_details_fieldslist_pain_container" style="<?php if (!$issue->isUserPainVisible()): ?> display: none;<?php endif; ?>">
    <legend onclick="$('issue_details_fieldslist_pain').toggle();"><?php echo __('User pain'); ?></legend>
    <ul class="issue_details simple_list" id="issue_details_fieldslist_pain">
        <li id="pain_bug_type_field" class="issue_detail_field<?php if ($issue->isPainBugTypeChanged()): ?> issue_detail_changed<?php endif; ?><?php if (!$issue->isPainBugTypeMerged()): ?> issue_detail_unmerged<?php endif; ?>" style="<?php if (!$issue->isUserPainVisible()): ?> display: none;<?php endif; ?>">
            <dl class="viewissue_list">
                <dt id="pain_bug_type_header"><?php echo __('Type of bug'); ?></dt>
                <dd id="pain_bug_type_content">
                    <?php if ($issue->isUpdateable() && $issue->canEditUserPain()): ?>
                        <a href="javascript:void(0);" onclick="TBG.Issues.Field.revert('<?php echo make_url('issue_revertfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'pain_bug_type')); ?>', 'pain_bug_type');" title="<?php echo __('Undo this change'); ?>"><?php echo image_tag('undo.png', array('class' => 'undo')); ?></a>
                        <?php echo image_tag('spinning_16.gif', array('style' => 'display: none; float: left; margin-right: 5px;', 'id' => 'pain_bug_type_undo_spinning')); ?>
                        <a href="javascript:void(0);" class="dropper dropdown_link" title="<?php echo __('Click to triage type of bug'); ?>"><?php echo image_tag('tabmenu_dropdown.png', array('class' => 'dropdown')); ?></a>
                        <ul class="popup_box more_actions_dropdown" id="pain_bug_type_change">
                            <li class="header"><?php echo __('Triage bug type'); ?></li>
                            <li>
                                <a href="javascript:void(0);" onclick="TBG.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'pain_bug_type', 'pain_bug_type_id' => 0)); ?>', 'pain_bug_type');"><?php echo __('Clear bug type'); ?></a>
                            </li>
                            <li class="separator"></li>
                            <?php foreach (\thebuggenie\core\entities\Issue::getPainTypesOrLabel('pain_bug_type') as $choice_id => $choice): ?>
                                <li>
                                    <a href="javascript:void(0);" onclick="TBG.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'pain_bug_type', 'pain_bug_type_id' => $choice_id)); ?>', 'pain_bug_type');"><?php echo $choice; ?></a>
                                </li>
                            <?php endforeach; ?>
                            <li id="pain_bug_type_spinning" style="margin-top: 3px; display: none;"><?php echo image_tag('spinning_20.gif', array('style' => 'float: left; margin-right: 5px;')) . '&nbsp;' . __('Please wait'); ?>...</li>
                            <li id="pain_bug_type_change_error" class="error_message" style="display: none;"></li>
                        </ul>
                    <?php endif; ?>
                    <span id="pain_bug_type_name"<?php if (!$issue->hasPainBugType()): ?> style="display: none;"<?php endif; ?>>
                        <?php echo ($issue->hasPainBugType()) ? $issue->getPainBugTypeLabel() : ''; ?>
                    </span>
                    <span class="faded_out" id="no_pain_bug_type"<?php if ($issue->hasPainBugType()): ?> style="display: none;"<?php endif; ?>><?php echo __('Not triaged'); ?></span>
                </dd>
            </dl>
        </li>
        <li id="pain_likelihood_field" class="issue_detail_field<?php if ($issue->isPainLikelihoodChanged()): ?> issue_detail_changed<?php endif; ?><?php if (!$issue->isPainLikelihoodMerged()): ?> issue_detail_unmerged<?php endif; ?>" style="<?php if (!$issue->isUserPainVisible()): ?> display: none;<?php endif; ?>">
            <dl class="viewissue_list">
                <dt id="pain_likelihood_header"><?php echo __('Likelihood'); ?></dt>
                <dd id="pain_likelihood_content">
                    <?php if ($issue->isUpdateable() && $issue->canEditUserPain()): ?>
                        <a href="javascript:void(0);" onclick="TBG.Issues.Field.revert('<?php echo make_url('issue_revertfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'pain_likelihood')); ?>', 'pain_likelihood');" title="<?php echo __('Undo this change'); ?>"><?php echo image_tag('undo.png', array('class' => 'undo')); ?></a>
                        <?php echo image_tag('spinning_16.gif', array('style' => 'display: none; float: left; margin-right: 5px;', 'id' => 'pain_likelihood_undo_spinning')); ?>
                        <a href="javascript:void(0);" class="dropper dropdown_link" title="<?php echo __('Click to triage likelihood'); ?>"><?php echo image_tag('tabmenu_dropdown.png', array('class' => 'dropdown')); ?></a>
                        <ul class="popup_box more_actions_dropdown" id="pain_likelihood_change">
                            <li class="header"><?php echo __('Triage likelihood'); ?></li>
                            <li>
                                <a href="javascript:void(0);" onclick="TBG.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'pain_likelihood', 'pain_likelihood_id' => 0)); ?>', 'pain_likelihood');"><?php echo __('Clear likelihood'); ?></a>
                            </li>
                            <li class="separator"></li>
                            <?php foreach (\thebuggenie\core\entities\Issue::getPainTypesOrLabel('pain_likelihood') as $choice_id => $choice): ?>
                                <li>
                                    <a href="javascript:void(0);" onclick="TBG.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'pain_likelihood', 'pain_likelihood_id' => $choice_id)); ?>', 'pain_likelihood');"><?php echo $choice; ?></a>
                                </li>
                            <?php endforeach; ?>
                            <li id="pain_likelihood_spinning" style="margin-top: 3px; display: none;"><?php echo image_tag('spinning_20.gif', array('style' => 'float: left; margin-right: 5px;')) . '&nbsp;' . __('Please wait'); ?>...</li>
                            <li id="pain_likelihood_change_error" class="error_message" style="display: none;"></li>
                        </ul>
                    <?php endif; ?>
                    <span id="pain_likelihood_name"<?php if (!$issue->hasPainLikelihood()): ?> style="display: none;"<?php endif; ?>>
                        <?php echo ($issue->hasPainLikelihood()) ? $issue->getPainLikelihoodLabel() : ''; ?>
                    </span>
                    <span class="faded_out" id="no_pain_likelihood"<?php if ($issue->hasPainLikelihood()): ?> style="display: none;"<?php endif; ?>><?php echo __('Not triaged'); ?></span>
                </dd>
            </dl>
        </li>
        <li id="pain_effect_field" class="issue_detail_field<?php if ($issue->isPainEffectChanged()): ?> issue_detail_changed<?php endif; ?><?php if (!$issue->isPainEffectMerged()): ?> issue_detail_unmerged<?php endif; ?>" style="<?php if (!$issue->isUserPainVisible()): ?> display: none;<?php endif; ?>">
            <dl class="viewissue_list">
                <dt id="pain_effect_header"><?php echo __('Effect'); ?></dt>
                <dd id="pain_effect_content">
                    <?php if ($issue->isUpdateable() && $issue->canEditUserPain()): ?>
                        <a href="javascript:void(0);" onclick="TBG.Issues.Field.revert('<?php echo make_url('issue_revertfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'pain_effect')); ?>', 'pain_effect');" title="<?php echo __('Undo this change'); ?>"><?php echo image_tag('undo.png', array('class' => 'undo')); ?></a>
                        <?php echo image_tag('spinning_16.gif', array('style' => 'display: none; float: left; margin-right: 5px;', 'id' => 'pain_effect_undo_spinning')); ?>
                        <a href="javascript:void(0);" class="dropper dropdown_link" title="<?php echo __('Click to triage effect'); ?>"><?php echo image_tag('tabmenu_dropdown.png', array('class' => 'dropdown')); ?></a>
                        <ul class="popup_box more_actions_dropdown" id="pain_effect_change">
                            <li class="header"><?php echo __('Triage effect'); ?></li>
                            <li>
                                <a href="javascript:void(0);" onclick="TBG.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'pain_effect', 'pain_effect_id' => 0)); ?>', 'pain_effect');"><?php echo __('Clear effect'); ?></a>
                            </li>
                            <li class="separator"></li>
                            <?php foreach (\thebuggenie\core\entities\Issue::getPainTypesOrLabel('pain_effect') as $choice_id => $choice): ?>
                                <li>
                                    <a href="javascript:void(0);" onclick="TBG.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'pain_effect', 'pain_effect_id' => $choice_id)); ?>', 'pain_effect');"><?php echo $choice; ?></a>
                                </li>
                            <?php endforeach; ?>
                            <li id="pain_effect_spinning" style="margin-top: 3px; display: none;"><?php echo image_tag('spinning_20.gif', array('style' => 'float: left; margin-right: 5px;')) . '&nbsp;' . __('Please wait'); ?>...</li>
                            <li id="pain_effect_change_error" class="error_message" style="display: none;"></li>
                        </ul>
                    <?php endif; ?>
                    <span id="pain_effect_name"<?php if (!$issue->hasPainEffect()): ?> style="display: none;"<?php endif; ?>>
                        <?php echo ($issue->hasPainEffect()) ? $issue->getPainEffectLabel() : ''; ?>
                    </span>
                    <span class="faded_out" id="no_pain_effect"<?php if ($issue->hasPainEffect()): ?> style="display: none;"<?php endif; ?>><?php echo __('Not triaged'); ?></span>
                </dd>
            </dl>
        </li>
    </ul>
</fieldset>
<fieldset id="viewissue_affected_container">
    <legend>
        <?php echo image_tag('spinning_16.gif', array('style' => 'display: none;', 'id' => 'issue_affected_indicator')) . __('Affected by this issue (%count)', array('%count' => '<span id="viewissue_affects_count">'.$affected_count.'</span>')); ?>
    </legend>
    <div id="viewissue_affected">
        <?php include_component('main/issueaffected', array('issue' => $issue)); ?>
    </div>
</fieldset>
<fieldset>
    <legend onclick="$('issue_details_fieldslist_people').toggle();"><?php echo __('People involved'); ?></legend>
    <ul class="issue_details simple_list" id="issue_details_fieldslist_people">
        <li id="posted_by_field" class="issue_detail_field<?php if ($issue->isPostedByChanged()): ?> issue_detail_changed<?php endif; ?><?php if (!$issue->isPostedByMerged()): ?> issue_detail_unmerged<?php endif; ?>">
            <dl class="viewissue_list">
                <dt id="posted_by_header"><?php echo __('Posted by'); ?></dt>
                <dd id="posted_by_content">
                    <?php if ($issue->isEditable() && $issue->canEditPostedBy()): ?>
                        <a href="javascript:void(0);" onclick="TBG.Issues.Field.revert('<?php echo make_url('issue_revertfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'posted_by')); ?>', 'posted_by');" title="<?php echo __('Undo this change'); ?>"><?php echo image_tag('undo.png', array('class' => 'undo')); ?></a>
                        <?php echo image_tag('spinning_16.gif', array('style' => 'display: none; float: left; margin-right: 5px;', 'id' => 'posted_by_undo_spinning')); ?>
                        <a href="javascript:void(0);" class="dropper dropdown_link" title="<?php echo __('Click to change owner'); ?>"><?php echo image_tag('tabmenu_dropdown.png', array('class' => 'dropdown')); ?></a>
                        <?php include_component('identifiableselector', array(    'html_id'             => 'posted_by_change',
                                                                                'header'             => __('Change poster'),
                                                                                'allow_clear'        => false,
                                                                                'clear_link_text'    => '',
                                                                                'callback'             => "TBG.Issues.Field.set('" . make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'posted_by', 'value' => '%identifiable_value')) . "', 'posted_by');",
                                                                                'base_id'            => 'posted_by',
                                                                                'absolute'            => true,
                                                                                'classes'            => 'leftie popup_box more_actions_dropdown')); ?>
                    <?php endif; ?>
                    <div style="width: 170px; display: inline;" id="posted_by_name">
                        <?php echo include_component('main/userdropdown', array('user' => $issue->getPostedBy())); ?>
                    </div>
                    <span id="no_posted_by" style="display: none;"> </span>
                </dd>
            </dl>
        </li>
        <li id="owned_by_field" style="<?php if (!$issue->isOwnedByVisible()): ?> display: none;<?php endif; ?>" class="issue_detail_field<?php if ($issue->isOwnerChanged()): ?> issue_detail_changed<?php endif; ?><?php if (!$issue->isOwnerMerged()): ?> issue_detail_unmerged<?php endif; ?>">
            <dl class="viewissue_list">
                <dt id="owned_by_header"><?php echo __('Owned by'); ?></dt>
                <dd id="owned_by_content">
                    <?php if ($issue->isUpdateable() && $issue->canEditOwner()): ?>
                        <a href="javascript:void(0);" onclick="TBG.Issues.Field.revert('<?php echo make_url('issue_revertfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'owned_by')); ?>', 'owned_by');" title="<?php echo __('Undo this change'); ?>"><?php echo image_tag('undo.png', array('class' => 'undo')); ?></a>
                        <?php echo image_tag('spinning_16.gif', array('style' => 'display: none; float: left; margin-right: 5px;', 'id' => 'owned_by_undo_spinning')); ?>
                        <a href="javascript:void(0);" class="dropper dropdown_link" title="<?php echo __('Click to change owner'); ?>"><?php echo image_tag('tabmenu_dropdown.png', array('class' => 'dropdown')); ?></a>
                        <?php include_component('identifiableselector', array(    'html_id'             => 'owned_by_change',
                                                                                'header'             => __('Change issue owner'),
                                                                                'callback'             => "TBG.Issues.Field.set('" . make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'owned_by', 'identifiable_type' => 'user', 'value' => '%identifiable_value')) . "', 'owned_by');",
                                                                                'team_callback'         => "TBG.Issues.Field.set('" . make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'owned_by', 'identifiable_type' => 'team', 'value' => '%identifiable_value')) . "', 'owned_by');",
                                                                                'teamup_callback'     => "TBG.Issues.Field.set('" . make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'owned_by', 'identifiable_type' => 'team', 'value' => '%identifiable_value', 'teamup' => true)) . "', 'owned_by');",
                                                                                'clear_link_text'    => __('Clear current owner'),
                                                                                'base_id'            => 'owned_by',
                                                                                'include_teams'        => true,
                                                                                'absolute'            => true,
                                                                                'classes'            => 'leftie popup_box more_actions_dropdown')); ?>
                    <?php endif; ?>
                    <div style="width: 170px; display: <?php if ($issue->isOwned()): ?>inline<?php else: ?>none<?php endif; ?>;" id="owned_by_name">
                        <?php if ($issue->getOwner() instanceof \thebuggenie\core\entities\User): ?>
                            <?php echo include_component('main/userdropdown', array('user' => $issue->getOwner())); ?>
                        <?php elseif ($issue->getOwner() instanceof \thebuggenie\core\entities\Team): ?>
                            <?php echo include_component('main/teamdropdown', array('team' => $issue->getOwner())); ?>
                        <?php endif; ?>
                    </div>
                    <span class="faded_out" id="no_owned_by"<?php if ($issue->isOwned()): ?> style="display: none;"<?php endif; ?>><?php echo __('Not owned by anyone'); ?></span>
                </dd>
            </dl>
        </li>
        <li id="assigned_to_field" class="issue_detail_field primary<?php if ($issue->isAssigneeChanged()): ?> issue_detail_changed<?php endif; ?><?php if (!$issue->isAssigneeMerged()): ?> issue_detail_unmerged<?php endif; ?>">
            <dl class="viewissue_list">
                <dt id="assigned_to_header"><?php echo __('Assigned to'); ?></dt>
                <dd id="assigned_to_content">
                    <?php if ($issue->canEditAssignee() && $issue->isUpdateable()): ?>
                        <a href="javascript:void(0);" onclick="TBG.Issues.Field.revert('<?php echo make_url('issue_revertfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'assigned_to')); ?>', 'assigned_to');" title="<?php echo __('Undo this change'); ?>"><?php echo image_tag('undo.png', array('class' => 'undo')); ?></a>
                        <?php echo image_tag('spinning_16.gif', array('style' => 'display: none; float: left; margin-right: 5px;', 'id' => 'assigned_to_undo_spinning')); ?>
                        <a href="javascript:void(0);" class="dropper dropdown_link" title="<?php echo __('Click to change assignee'); ?>"><?php echo image_tag('tabmenu_dropdown.png', array('class' => 'dropdown')); ?></a>
                        <?php include_component('identifiableselector', array(    'html_id'             => 'assigned_to_change',
                                                                                'header'             => __('Assign this issue'),
                                                                                'callback'             => "TBG.Issues.Field.set('" . make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'assigned_to', 'identifiable_type' => 'user', 'value' => '%identifiable_value')) . "', 'assigned_to');",
                                                                                'team_callback'         => "TBG.Issues.Field.set('" . make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'assigned_to', 'identifiable_type' => 'team', 'value' => '%identifiable_value')) . "', 'assigned_to');",
                                                                                'teamup_callback'     => "TBG.Issues.Field.set('" . make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'assigned_to', 'identifiable_type' => 'team', 'value' => '%identifiable_value', 'teamup' => true)) . "', 'assigned_to');",
                                                                                'clear_link_text'    => __('Clear current assignee'),
                                                                                'base_id'            => 'assigned_to',
                                                                                'include_teams'        => true,
                                                                                'absolute'            => true,
                                                                                'classes'            => 'leftie popup_box more_actions_dropdown')); ?>
                    <?php endif; ?>
                    <div style="width: 170px; display: <?php if ($issue->isAssigned()): ?>inline<?php else: ?>none<?php endif; ?>;" id="assigned_to_name">
                        <?php if ($issue->getAssignee() instanceof \thebuggenie\core\entities\User): ?>
                            <?php echo include_component('main/userdropdown', array('user' => $issue->getAssignee())); ?>
                        <?php elseif ($issue->getAssignee() instanceof \thebuggenie\core\entities\Team): ?>
                            <?php echo include_component('main/teamdropdown', array('team' => $issue->getAssignee())); ?>
                        <?php endif; ?>
                    </div>
                    <span class="faded_out" id="no_assigned_to"<?php if ($issue->isAssigned()): ?> style="display: none;"<?php endif; ?>><?php echo __('Not assigned to anyone'); ?></span>
                </dd>
            </dl>
            <?php if ($issue->canEditAssignee() && $issue->isEditable()): ?>
            <?php endif; ?>
        </li>
        <li id="subscribers_field" class="issue_detail_field" style="position: relative;">
            <dl class="viewissue_list">
                <dt id="subscribers_header">
                    <?php echo __('Subscribers'); ?>
                </dt>
                <dd class="hoverable">
                    <a href="javascript:void(0)" onclick="TBG.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'issue_subscribers', 'issue_id' => $issue->getID())); ?>');"><?php echo __('%number_of subscriber(s)', array('%number_of' => '<span id="subscribers_field_count">'.count($issue->getSubscribers()).'</span>')); ?></a>
                </dd>
            </dl>
            <div class="tooltip from-above" style="width: 300px; font-size: 0.9em; margin-top: 10px; margin-left: 17px;"><?php echo __('Click here to show the list of subscribers'); ?></div>
        </li>
    </ul>
</fieldset>
<fieldset id="issue_timetracking_container">
    <legend onclick="$('issue_details_fieldslist_time').toggle();"><?php echo __('Times and dates'); ?></legend>
    <ul class="issue_details simple_list" id="issue_details_fieldslist_time">
        <li id="posted_at_field" class="issue_detail_field primary">
            <dl class="viewissue_list">
                <dt id="posted_at_header">
                    <?php echo __('Posted at'); ?>
                </dt>
                <dd class="hoverable">
                    <time datetime="<?php echo tbg_formatTime($issue->getPosted(), 24); ?>" title="<?php echo tbg_formatTime($issue->getPosted(), 21); ?>" pubdate><?php echo tbg_formatTime($issue->getPosted(), 20); ?></time>
                </dd>
            </dl>
        </li>
        <li id="updated_at_field" class="issue_detail_field">
            <dl class="viewissue_list">
                <dt id="updated_at_header">
                    <?php echo __('Last updated'); ?>
                </dt>
                <dd class="hoverable">
                    <time datetime="<?php echo tbg_formatTime($issue->getLastUpdatedTime(), 24); ?>" title="<?php echo tbg_formatTime($issue->getLastUpdatedTime(), 21); ?>"><?php echo tbg_formatTime($issue->getLastUpdatedTime(), 20); ?></time>
                </dd>
            </dl>
        </li>
        <li id="estimated_time_field"<?php if (!$issue->isEstimatedTimeVisible()): ?> style="display: none;"<?php endif; ?> class="issue_detail_field<?php if ($issue->isEstimatedTimeChanged()): ?> issue_detail_changed<?php endif; ?><?php if (!$issue->isEstimatedTimeMerged()): ?> issue_detail_unmerged<?php endif; ?>">
            <dl class="viewissue_list">
                <dt id="estimated_time_header"><?php echo __('Estimated time'); ?></dt>
                <dd id="estimated_time_content">
                    <?php if ($issue->isUpdateable() && $issue->canEditEstimatedTime()): ?>
                        <a href="javascript:void(0);" onclick="TBG.Issues.Field.revert('<?php echo make_url('issue_revertfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'estimated_time')); ?>', 'estimated_time');" title="<?php echo __('Undo this change'); ?>"><?php echo image_tag('undo.png', array('class' => 'undo')); ?></a>
                        <?php echo image_tag('spinning_16.gif', array('style' => 'display: none; float: left; margin-right: 5px;', 'id' => 'estimated_time_undo_spinning')); ?>
                        <a href="javascript:void(0);" class="dropper dropdown_link" title="<?php echo __('Click to estimate this issue'); ?>"><?php echo image_tag('tabmenu_dropdown.png', array('class' => 'dropdown')); ?></a>
                        <?php include_component('main/issueestimator', array('issue' => $issue, 'field' => 'estimated_time', 'mode' => 'left')); ?>
                    <?php endif; ?>
                    <span id="estimated_time_<?php echo $issue->getID(); ?>_name"<?php if (!$issue->hasEstimatedTime()): ?> style="display: none;"<?php endif; ?>>
                        <?php echo \thebuggenie\core\entities\Issue::getFormattedTime($issue->getEstimatedTime()); ?>
                    </span>
                    <span class="faded_out" id="no_estimated_time_<?php echo $issue->getID(); ?>"<?php if ($issue->hasEstimatedTime()): ?> style="display: none;"<?php endif; ?>><?php echo __('Not estimated'); ?></span>
                </dd>
            </dl>
        </li>
        <li id="spent_time_field" style="position: relative;<?php if (!$issue->isSpentTimeVisible()): ?> display: none;<?php endif; ?>" class="issue_detail_field<?php if ($issue->isSpentTimeChanged()): ?> issue_detail_changed<?php endif; ?><?php if (!$issue->isSpentTimeMerged()): ?> issue_detail_unmerged<?php endif; ?>">
            <dl class="viewissue_list">
                <dt id="spent_time_header"><?php echo __('Time spent'); ?></dt>
                <dd id="spent_time_content">
                    <span id="spent_time_<?php echo $issue->getID(); ?>_name"<?php if (!$issue->hasSpentTime()): ?> style="display: none;"<?php endif; ?>>
                        <a href="javascript:void(0)" onclick="TBG.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'issue_spenttimes', 'issue_id' => $issue->getID())); ?>');" id="spent_time_<?php echo $issue->getID(); ?>_value"><?php echo \thebuggenie\core\entities\Issue::getFormattedTime($issue->getSpentTime()); ?></a>
                    </span>
                    <span class="faded_out" id="no_spent_time_<?php echo $issue->getID(); ?>"<?php if ($issue->hasSpentTime()): ?> style="display: none;"<?php endif; ?>><a href="javascript:void(0)" onclick="TBG.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'issue_spenttimes', 'issue_id' => $issue->getID())); ?>');"><?php echo __('No time spent'); ?></a></span>
                    <div id="estimated_percentbar"<?php if (!($issue->hasEstimatedTime())): ?> style="display: none;"<?php endif; ?>><?php include_component('main/percentbar', array('percent' => $issue->getEstimatedPercentCompleted(), 'height' => 3)); ?></div>
                </dd>
            </dl>
            <div class="tooltip from-above" style="width: 300px; font-size: 0.9em; margin-top: 10px; margin-left: 17px;"><?php echo __('Click here to see time logged against this issue'); ?></div>
        </li>
    </ul>
</fieldset>
<fieldset>
    <legend onclick="$('issue_details_fieldslist').toggle();"><?php echo __('Issue details'); ?></legend>
    <ul class="issue_details simple_list" id="issue_details_fieldslist">
        <?php foreach ($fields_list as $field => $info): ?>
            <?php include_component('main/issuedetailslistfield', compact('field', 'info', 'issue')); ?>
        <?php endforeach; ?>
        <?php foreach ($customfields_list as $field => $info): ?>
            <?php if ($info['type'] == \thebuggenie\core\entities\CustomDatatype::INPUT_TEXTAREA_MAIN): continue; endif; ?>
            <li id="<?php echo $field; ?>_field" class="issue_detail_field<?php if (!$info['merged']): ?> issue_detail_unmerged<?php elseif ($info['changed']): ?> issue_detail_changed<?php endif; ?>"<?php if (!$info['visible']): ?> style="display: none;"<?php endif; ?>>
                <dl class="viewissue_list">
                    <dt id="<?php echo $field; ?>_header">
                        <?php echo $info['title']; ?>
                    </dt>
                    <dd id="<?php echo $field; ?>_content">
                        <?php if ($issue->isUpdateable() && $issue->canEditCustomFields() && $info['editable']): ?>
                            <a href="javascript:void(0);" onclick="TBG.Issues.Field.revert('<?php echo make_url('issue_revertfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field)); ?>', '<?php echo $field; ?>');" title="<?php echo __('Undo this change'); ?>"><?php echo image_tag('undo.png', array('class' => 'undo')); ?></a>
                            <?php echo image_tag('spinning_16.gif', array('style' => 'display: none; float: left; margin-right: 5px;', 'id' => $field . '_undo_spinning')); ?>
                            <a href="javascript:void(0);" class="dropper dropdown_link" title="<?php echo $info['change_tip']; ?>"><?php echo image_tag('tabmenu_dropdown.png', array('class' => 'dropdown')); ?></a>
                            <?php if ($info['type'] == \thebuggenie\core\entities\CustomDatatype::USER_CHOICE): ?>
                                <?php include_component('identifiableselector', array(    'html_id'             => $field.'_change',
                                                                                        'header'             => __('Select a user'),
                                                                                        'callback'             => "TBG.Issues.Field.set('" . make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field, 'identifiable_type' => 'user', $field.'_value' => '%identifiable_value')) . "', '".$field."');",
                                                                                        'clear_link_text'    => __('Clear currently selected user'),
                                                                                        'base_id'            => $field,
                                                                                        'include_teams'        => false,
                                                                                        'absolute'            => true,
                                                                                        'classes'            => 'leftie popup_box more_actions_dropdown')); ?>
                            <?php elseif ($info['type'] == \thebuggenie\core\entities\CustomDatatype::TEAM_CHOICE): ?>
                                <?php include_component('identifiableselector', array(    'html_id'             => $field.'_change',
                                                                                        'header'             => __('Select a team'),
                                                                                        'callback'             => "TBG.Issues.Field.set('" . make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field, 'identifiable_type' => 'team', $field.'_value' => '%identifiable_value')) . "', '".$field."');",
                                                                                        'clear_link_text'    => __('Clear currently selected team'),
                                                                                        'base_id'            => $field,
                                                                                        'include_teams'        => true,
                                                                                        'include_users'        => false,
                                                                                        'absolute'            => true,
                                                                                        'classes'            => 'leftie popup_box more_actions_dropdown')); ?>
                            <?php elseif ($info['type'] == \thebuggenie\core\entities\CustomDatatype::CLIENT_CHOICE): ?>
                                <?php include_component('identifiableselector', array(    'html_id'             => $field.'_change',
                                                                                        'header'             => __('Select a client'),
                                                                                        'callback'             => "TBG.Issues.Field.set('" . make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field, 'identifiable_type' => 'client', $field.'_value' => '%identifiable_value')) . "', '".$field."');",
                                                                                        'clear_link_text'    => __('Clear currently selected client'),
                                                                                        'base_id'            => $field,
                                                                                        'include_clients'    => true,
                                                                                        'include_teams'        => false,
                                                                                        'include_users'        => false,
                                                                                        'absolute'            => true,
                                                                                        'classes'            => 'leftie popup_box more_actions_dropdown')); ?>
                            <?php else: ?>
                                <ul class="popup_box more_actions_dropdown" id="<?php echo $field; ?>_change">
                                    <li class="header"><?php echo $info['change_header']; ?></li>
                                    <li id="<?php echo $field; ?>_spinning" style="margin-top: 3px; display: none;"><?php echo image_tag('spinning_20.gif', array('style' => 'float: left; margin-right: 5px;')) . '&nbsp;' . __('Please wait'); ?>...</li>
                                    <li id="<?php echo $field; ?>_change_error" class="error_message" style="display: none;"></li>
                                    <?php if (array_key_exists('choices', $info) && is_array($info['choices'])): ?>
                                        <li>
                                            <a href="javascript:void(0);" onclick="TBG.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field, $field . '_value' => "")); ?>', '<?php echo $field; ?>');"><?php echo $info['clear']; ?></a>
                                        </li>
                                        <li class="separator"></li>
                                        <?php foreach ($info['choices'] ?: array() as $choice): ?>
                                            <li>
                                                <a href="javascript:void(0);" onclick="TBG.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field, $field . '_value' => $choice->getID())); ?>', '<?php echo $field; ?>');"><?php echo image_tag('icon_customdatatype.png').__($choice->getName()); ?></a>
                                            </li>
                                        <?php endforeach; ?>
                                    <?php elseif ($info['type'] == \thebuggenie\core\entities\CustomDatatype::DATE_PICKER): ?>
                                        <li>
                                            <a href="javascript:void(0);" onclick="TBG.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field, $field . '_value' => "")); ?>', '<?php echo $field; ?>');"><?php echo $info['clear']; ?></a>
                                        </li>
                                        <li class="separator"></li>
                                        <li id="customfield_<?php echo $field; ?>_calendar_container"></li>
                                        <script type="text/javascript">
                                            require(['domReady', 'thebuggenie/tbg', 'calendarview'], function (domReady, tbgjs, Calendar) {
                                                domReady(function () {
                                                    Calendar.setup({
                                                        dateField: '<?php echo $field; ?>_name',
                                                        parentElement: 'customfield_<?php echo $field; ?>_calendar_container',
                                                        valueCallback: function(element, date) {
                                                            var value = Math.floor(date.getTime() / 1000);
                                                            TBG.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field)); ?>?<?php echo $field; ?>_value='+value, '<?php echo $field; ?>');
                                                        }
                                                    });
                                                });
                                            });
                                        </script>
                                    <?php else: ?>
                                        <li>
                                            <a href="javascript:void(0);" onclick="TBG.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field, $field . '_value' => "")); ?>', '<?php echo $field; ?>');"><?php echo $info['clear']; ?></a>
                                        </li>
                                        <li class="separator"></li>
                                        <?php

                                        switch ($info['type'])
                                        {
                                            case \thebuggenie\core\entities\CustomDatatype::EDITIONS_CHOICE:
                                                ?>
                                                <?php foreach ($issue->getProject()->getEditions() as $choice): ?>
                                                    <li>
                                                        <a href="javascript:void(0);" onclick="TBG.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field, $field . '_value' => $choice->getID())); ?>', '<?php echo $field; ?>');"><?php echo image_tag('icon_edition.png').__($choice->getName()); ?></a>
                                                    </li>
                                                <?php endforeach; ?>
                                                <?php
                                                break;
                                            case \thebuggenie\core\entities\CustomDatatype::MILESTONE_CHOICE:
                                                ?>
                                                <?php foreach ($issue->getProject()->getMilestonesForIssues() as $choice): ?>
                                                    <li>
                                                        <a href="javascript:void(0);" onclick="TBG.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field, $field . '_value' => $choice->getID())); ?>', '<?php echo $field; ?>');"><?php echo image_tag('icon_milestone.png').__($choice->getName()); ?></a>
                                                    </li>
                                                <?php endforeach; ?>
                                                <?php
                                                break;
                                            case \thebuggenie\core\entities\CustomDatatype::STATUS_CHOICE:
                                                ?>
                                                <?php foreach (\thebuggenie\core\entities\Status::getAll() as $choice): ?>
                                                    <li>
                                                        <a href="javascript:void(0);" onclick="TBG.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field, $field . '_value' => $choice->getID())); ?>', '<?php echo $field; ?>');">
                                                            <div class="status_badge" style="background-color: <?php echo ($choice instanceof \thebuggenie\core\entities\Status) ? $choice->getColor() : '#FFF'; ?>;">
                                                                <span id="status_content">&nbsp;&nbsp;</span>
                                                            </div>
                                                            <?php echo __($choice->getName()); ?>
                                                        </a>
                                                    </li>
                                                <?php endforeach; ?>
                                                <?php
                                                break;
                                            case \thebuggenie\core\entities\CustomDatatype::COMPONENTS_CHOICE:
                                                ?>
                                                <?php foreach ($issue->getProject()->getComponents() as $choice): ?>
                                                    <li>
                                                        <a href="javascript:void(0);" onclick="TBG.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field, $field . '_value' => $choice->getID())); ?>', '<?php echo $field; ?>');"><?php echo image_tag('icon_components.png').$choice->getName(); ?></a>
                                                    </li>
                                                <?php endforeach; ?>
                                                <?php
                                                break;
                                            case \thebuggenie\core\entities\CustomDatatype::RELEASES_CHOICE:
                                                ?>
                                                <?php foreach ($issue->getProject()->getBuilds() as $choice): ?>
                                                    <li>
                                                        <a href="javascript:void(0);" onclick="TBG.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field, $field . '_value' => $choice->getID())); ?>', '<?php echo $field; ?>');"><?php echo image_tag('icon_build.png').$choice->getName(); ?></a>
                                                    </li>
                                                <?php endforeach; ?>
                                                <?php
                                                break;
                                            case \thebuggenie\core\entities\CustomDatatype::INPUT_TEXT:
                                                ?>
                                                <li class="nohover">
                                                    <form id="<?php echo $field; ?>_form" action="<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field)); ?>" method="post" onSubmit="TBG.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field)) ?>', '<?php echo $field; ?>', '<?php echo $field; ?>'); return false;">
                                                        <input type="text" name="<?php echo $field; ?>_value" value="<?php echo $info['name'] ?>" /><?php echo __('%save or %cancel', array('%save' => '<input type="submit" value="'.__('Save').'">', '%cancel' => '<a href="#" onclick="$(\''.$field.'_change\').hide(); return false;">'.__('cancel').'</a>')); ?>
                                                    </form>
                                                </li>
                                                <?php
                                                break;
                                            case \thebuggenie\core\entities\CustomDatatype::INPUT_TEXTAREA_SMALL:
                                                ?>
                                                <li class="nohover">
                                                    <form id="<?php echo $field; ?>_form" action="<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field)); ?>" method="post" onSubmit="TBG.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field)) ?>', '<?php echo $field; ?>', '<?php echo $field; ?>'); return false;">
                                                        <?php include_component('main/textarea', array('area_name' => $field.'_value', 'target_type' => 'issue', 'target_id' => $issue->getID(), 'area_id' => $field.'_value', 'height' => '100px', 'width' => '100%', 'value' => $info['name'])); ?>
                                                        <br><?php echo __('%save or %cancel', array('%save' => '<input type="submit" value="'.__('Save').'">', '%cancel' => '<a href="#" onclick="$(\''.$field.'_change\').hide(); return false;">'.__('cancel').'</a>')); ?>
                                                    </form>
                                                </li>
                                                <?php
                                                break;
                                        }

                                    endif; ?>
                                </ul>
                            <?php endif; ?>
                        <?php endif; ?>
                        <?php
                            switch ($info['type'])
                            {
                                case \thebuggenie\core\entities\CustomDatatype::INPUT_TEXTAREA_SMALL:
                                    ?>
                                    <span id="<?php echo $field; ?>_name"<?php if (!$info['name_visible']): ?> style="display: none;"<?php endif; ?>>
                                        <?php echo tbg_parse_text($info['name'], false, null, array('headers' => false)); ?>
                                    </span>
                                    <span class="faded_out" id="no_<?php echo $field; ?>"<?php if (!$info['noname_visible']): ?> style="display: none;"<?php endif; ?>>
                                        <?php echo __('Not determined'); ?>
                                    </span>
                                    <?php
                                    break;
                                case \thebuggenie\core\entities\CustomDatatype::USER_CHOICE:
                                    ?>
                                    <span id="<?php echo $field; ?>_name"<?php if (!$info['name_visible']): ?> style="display: none;"<?php endif; ?>>
                                        <?php echo include_component('main/userdropdown', array('user' => $info['name'])); ?>
                                    </span>
                                    <span class="faded_out" id="no_<?php echo $field; ?>"<?php if (!$info['noname_visible']): ?> style="display: none;"<?php endif; ?>>
                                        <?php echo __('Not determined'); ?>
                                    </span>
                                    <?php
                                    break;
                                case \thebuggenie\core\entities\CustomDatatype::TEAM_CHOICE:
                                    ?>
                                    <span id="<?php echo $field; ?>_name"<?php if (!$info['name_visible']): ?> style="display: none;"<?php endif; ?>>
                                        <?php echo include_component('main/teamdropdown', array('team' => $info['name'])); ?>
                                    </span>
                                    <span class="faded_out" id="no_<?php echo $field; ?>"<?php if (!$info['noname_visible']): ?> style="display: none;"<?php endif; ?>>
                                        <?php echo __('Not determined'); ?>
                                    </span>
                                    <?php
                                    break;
                                case \thebuggenie\core\entities\CustomDatatype::EDITIONS_CHOICE:
                                case \thebuggenie\core\entities\CustomDatatype::COMPONENTS_CHOICE:
                                case \thebuggenie\core\entities\CustomDatatype::RELEASES_CHOICE:
                                case \thebuggenie\core\entities\CustomDatatype::MILESTONE_CHOICE:
                                case \thebuggenie\core\entities\CustomDatatype::CLIENT_CHOICE:
                                     ?>
                                    <span id="<?php echo $field; ?>_name"<?php if (!$info['name_visible']): ?> style="display: none;"<?php endif; ?>>
                                        <?php echo (isset($info['name'])) ? $info['name'] : __('Unknown'); ?>
                                    </span>
                                    <span class="faded_out" id="no_<?php echo $field; ?>"<?php if (!$info['noname_visible']): ?> style="display: none;"<?php endif; ?>>
                                        <?php echo __('Not determined'); ?>
                                    </span><?php
                                    break;
                                case \thebuggenie\core\entities\CustomDatatype::STATUS_CHOICE:
                                    $status = null;
                                    $value = null;
                                    $color = '#FFF';
                                    try
                                    {
                                        $status = new \thebuggenie\core\entities\Status($info['name']);
                                        $value = $status->getName();
                                        $color = $status->getColor();
                                    }
                                    catch (\Exception $e) { }
                                    ?><span id="<?php echo $field; ?>_name"<?php if (!$info['name_visible']): ?> style="display: none;"<?php endif; ?>><div class="status_badge" style="background-color: <?php echo $color; ?>;"><span><?php echo __($value); ?></span></div></span><span class="faded_out" id="no_<?php echo $field; ?>"<?php if (!$info['noname_visible']): ?> style="display: none;"<?php endif; ?>><?php echo __('Not determined'); ?></span><?php
                                    break;
                                case \thebuggenie\core\entities\CustomDatatype::DATE_PICKER:
                                    $tbg_response->addJavascript('calendarview');
                                    $value = ($info['name']) ? date('Y-m-d', $info['name']) : __('Not set');
                                    ?><span id="<?php echo $field; ?>_name"<?php if (!$info['name_visible']): ?> style="display: none;"<?php endif; ?>><?php echo $value; ?></span><span class="faded_out" id="no_<?php echo $field; ?>"<?php if (!$info['noname_visible']): ?> style="display: none;"<?php endif; ?>><?php echo __('Not set'); ?></span><?php
                                    break;
                                default:
                                    ?><span id="<?php echo $field; ?>_name"<?php if (!$info['name_visible']): ?> style="display: none;"<?php endif; ?>><?php echo (filter_var($info['name'], FILTER_VALIDATE_URL) !== false) ? link_tag($info['name'], $info['name']) : $info['name']; ?></span><span class="faded_out" id="no_<?php echo $field; ?>"<?php if (!$info['noname_visible']): ?> style="display: none;"<?php endif; ?>><?php echo __('Not determined'); ?></span><?php
                                    break;
                            }
                        ?>
                    </dd>
                </dl>
                <div style="clear: both;"> </div>
                <?php if ($issue->isUpdateable() && $issue->canEditCustomFields()): ?>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
</fieldset>
<fieldset id="viewissue_attached_information_container">
    <legend>
        <?php echo __('Attachments (%count)', array('%count' => '<span id="viewissue_uploaded_attachments_count">'.(count($issue->getLinks()) + count($issue->getFiles())).'</span>')); ?>
    </legend>
    <div id="viewissue_attached_information">
        <div class="no_items" id="viewissue_no_uploaded_files"<?php if (count($issue->getFiles()) + count($issue->getLinks()) > 0): ?> style="display: none;"<?php endif; ?>><?php echo __('There is nothing attached to this issue'); ?></div>
        <ul class="attached_items" id="viewissue_uploaded_links">
            <?php foreach ($issue->getLinks() as $link_id => $link): ?>
                <?php include_component('attachedlink', array('issue' => $issue, 'link' => $link, 'link_id' => $link['id'])); ?>
            <?php endforeach; ?>
        </ul>
        <ul class="attached_items" id="viewissue_uploaded_files">
            <?php foreach ($issue->getFiles() as $file_id => $file): ?>
                <?php if (!$file->isImage()): ?>
                    <?php include_component('main/attachedfile', array('base_id' => 'viewissue_files', 'mode' => 'issue', 'issue' => $issue, 'file' => $file)); ?>
                <?php endif; ?>
            <?php endforeach; ?>
            <?php foreach ($issue->getFiles() as $file_id => $file): ?>
                <?php if ($file->isImage()): ?>
                    <?php include_component('main/attachedfile', array('base_id' => 'viewissue_files', 'mode' => 'issue', 'issue' => $issue, 'file' => $file)); ?>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    </div>
</fieldset>
<?php \thebuggenie\core\framework\Event::createNew('core', 'viewissue_left_after_attachments', $issue)->trigger(); ?>
<fieldset id="viewissue_related_information_container">
    <legend>
        <?php echo image_tag('spinning_16.gif', array('style' => 'display: none;', 'id' => 'related_issues_indicator')) . __('Child issues (%count)', array('%count' => '<span id="viewissue_related_issues_count">'.count($issue->getChildIssues()).'</span>')); ?>
    </legend>
    <div id="viewissue_related_information">
        <?php include_component('main/relatedissues', array('issue' => $issue)); ?>
    </div>
</fieldset>
<fieldset id="viewissue_duplicate_issues_container">
    <legend>
        <?php echo image_tag('spinning_16.gif', array('style' => 'display: none;', 'id' => 'duplicate_issues_indicator')) . __('Duplicate issues (%count)', array('%count' => '<span id="viewissue_duplicate_issues_count">'.$issue->getNumberOfDuplicateIssues().'</span>')); ?>
    </legend>
    <div id="viewissue_duplicate_issues">
        <?php include_component('main/duplicateissues', array('issue' => $issue)); ?>
    </div>
</fieldset>
<?php \thebuggenie\core\framework\Event::createNew('core', 'viewissue_left_bottom', $issue)->trigger(); ?>
<div style="clear: both; margin-bottom: 5px;"> </div>
