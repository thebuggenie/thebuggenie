<div <?php if (!isset($fake) || !$fake): ?> id="whiteboard_issue_<?php echo $issue->getID(); ?>"<?php endif; ?> class="whiteboard-issue <?php if ($issue->isClosed()) echo 'issue_closed'; ?> <?php if ($issue->isBlocking()) echo 'blocking'; ?>" data-issue-id="<?php echo $issue->getID(); ?>" data-status-id="<?php echo $issue->getStatus()->getID(); ?>" data-last-updated="<?php echo $issue->getLastUpdatedTime(); ?>" data-valid-status-ids="<?php echo join(',', array_keys($issue->getAvailableStatuses())); ?>" data-column-id="<?php echo $column->getID(); ?>">
    <div class="planning_indicator" id="issue_<?php echo $issue->getID(); ?>_indicator" style="display: none;"><?php echo image_tag('spinning_16.gif'); ?></div>
    <?php include_component('agile/colorpicker', array('issue' => $issue)); ?>
    <div>
        <div class="issue_estimates">
            <div class="issue_estimate points" style="<?php if (!$issue->getEstimatedPoints() && !$issue->getSpentPoints()) echo 'display: none;'; ?>"><?php if ($issue->getSpentPoints()): ?><span title="<?php echo __('Spent points'); ?>"><?php echo $issue->getSpentPoints(); ?></span>/<?php endif; ?><span title="<?php echo __('Estimated points'); ?>"><?php echo $issue->getEstimatedPoints(); ?></span></div>
            <div class="issue_estimate hours" style="<?php if (!$issue->getEstimatedHoursAndMinutes(true, true) && !$issue->getSpentHoursAndMinutes(true, true)) echo 'display: none;'; ?>"><?php if ($issue->getSpentHoursAndMinutes(true, true)): ?><span title="<?php echo __('Spent hours'); ?>"><?php echo $issue->getSpentHoursAndMinutes(true, true); ?></span>/<?php endif; ?><span title="<?php echo __('Estimated hours'); ?>"><?php echo $issue->getEstimatedHoursAndMinutes(true, true); ?></span></div>
        </div>
        <?php if ($issue->getPriority() instanceof \thebuggenie\core\entities\Priority): ?>
            <div class="priority priority_<?php echo ($issue->getPriority() instanceof \thebuggenie\core\entities\Priority) ? $issue->getPriority()->getValue() : 0; ?>" title="<?php echo ($issue->getPriority() instanceof \thebuggenie\core\entities\Priority) ? __($issue->getPriority()->getName()) : __('Priority not set'); ?>"><?php echo ($issue->getPriority() instanceof \thebuggenie\core\entities\Priority) ? $issue->getPriority()->getAbbreviation() : '-'; ?></div>
        <?php endif; ?>
    <?php echo link_tag(make_url('viewissue', array('issue_no' => $issue->getFormattedIssueNo(), 'project_key' => $issue->getProject()->getKey())), $issue->getFormattedTitle(true, false), array('title' => $issue->getFormattedTitle(), 'target' => '_blank', 'class' => 'issue_header')); ?>
    </div>
    <?php if (isset($swimlane)): ?>
        <div class="issue_more_actions_link_container">
            <a title="<?php echo __('Show more actions'); ?>" class="dropper dynamic_menu_link" data-id="<?php echo $issue->getID(); ?>" id="more_actions_<?php echo $issue->getID(); ?>_button" href="javascript:void(0);"><?php echo fa_image_tag('ellipsis-v'); ?></a>
            <?php include_component('main/issuemoreactions', array('issue' => $issue, 'multi' => true, 'dynamic' => true, 'estimator_mode' => 'left')); ?>
        </div>
    <?php endif; ?>
    <div class="extra">
        <div class="description"><?php echo __e($issue->getDescription()); ?></div>
    </div>
    <?php if ($issue->hasChildIssues()): ?>
        <ol class="child-issues">
            <?php foreach ($issue->getChildIssues() as $child_issue): ?>
                <li title="<?php echo __e($child_issue->getFormattedTitle()); ?>" class="<?php if ($child_issue->isClosed()) echo 'closed'; ?>"><?php echo link_tag(make_url('viewissue', array('issue_no' => $child_issue->getFormattedIssueNo(), 'project_key' => $child_issue->getProject()->getKey())), $child_issue->getFormattedTitle(true, false), array('title' => $child_issue->getFormattedTitle(), 'target' => '_blank')); ?></li>
            <?php endforeach; ?>
        </ol>
    <?php endif; ?>
    <?php $issue_custom_fields_of_type = array_filter($issue->getCustomFieldsOfTypes(array(\thebuggenie\core\entities\CustomDatatype::DATE_PICKER, \thebuggenie\core\entities\CustomDatatype::DATETIME_PICKER))); ?>
    <?php if (count($issue->getBuilds()) || count($issue->getComponents()) || (isset($swimlane) && $swimlane->getBoard()->getEpicIssuetypeID() && $issue->hasParentIssuetype($swimlane->getBoard()->getEpicIssuetypeID()) && count(array_filter($issue->getParentIssues(), function($parent) use($swimlane) { return $parent->getIssueType()->getID() == $swimlane->getBoard()->getEpicIssuetypeID(); })))): ?>
        <div class="issue_info<?php if (isset($swimlane) && $swimlane->getBoard()->hasIssueFieldValues() && count(array_filter(array_keys($issue_custom_fields_of_type), function($custom_field_key) use($swimlane) { return $swimlane->getBoard()->hasIssueFieldValue($custom_field_key); }))) echo ' issue_info_top'; ?>">
            <?php foreach ($issue->getBuilds() as $details): ?>
                <div class="issue_release"><?php echo $details['build']->getVersion(); ?></div>
            <?php endforeach; ?>
            <?php foreach ($issue->getComponents() as $details): ?>
                <div class="issue_component"><?php echo $details['component']->getName(); ?></div>
            <?php endforeach; ?>
            <?php if (isset($swimlane)): ?>
                <?php if ($swimlane->getBoard()->getEpicIssuetypeID() && $issue->hasParentIssuetype($swimlane->getBoard()->getEpicIssuetypeID())): ?>
                    <?php foreach ($issue->getParentIssues() as $parent): ?>
                        <?php if ($parent->getIssueType()->getID() == $swimlane->getBoard()->getEpicIssuetypeID()): ?>
                            <?php echo link_tag(make_url('viewissue', array('issue_no' => $parent->getFormattedIssueNo(), 'project_key' => $parent->getProject()->getKey())), $parent->getShortname(), array('title' => $parent->getFormattedTitle(), 'target' => '_blank', 'class' => 'epic_badge', 'style' => 'background-color: ' . $parent->getAgileColor().'; color: ' . $parent->getAgileTextColor(), 'data-parent-epic-id' => $parent->getID())); ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    <?php if (isset($swimlane) && $swimlane->getBoard()->hasIssueFieldValues() && count(array_filter(array_keys($issue_custom_fields_of_type), function($custom_field_key) use($swimlane) { return $swimlane->getBoard()->hasIssueFieldValue($custom_field_key); }))): ?>
        <div class="issue_info
        <?php if (count($issue->getBuilds()) || count($issue->getComponents()) || (isset($swimlane) && $swimlane->getBoard()->getEpicIssuetypeID() && $issue->hasParentIssuetype($swimlane->getBoard()->getEpicIssuetypeID()) && count(array_filter($issue->getParentIssues(), function($parent) use($swimlane) { return $parent->getIssueType()->getID() == $swimlane->getBoard()->getEpicIssuetypeID(); })))) echo ' issue_info_middle'; ?>">
            <?php if ($swimlane->getBoard()->hasIssueFieldValues()): ?>
                <?php foreach ($issue_custom_fields_of_type as $key => $value): ?>
                    <?php if (!$swimlane->getBoard()->hasIssueFieldValue($key)) continue; ?>
                    <div class="issue_component issue_date" title="<?php echo \thebuggenie\core\entities\CustomDatatype::getByKey($key)->getDescription(); ?>"><?php echo tbg_formattime($value, 14); ?></div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    <div class="issue_info">
        <?php if ($issue->countUserComments()): ?>
            <div class="comments-badge">
                <?php echo fa_image_tag('comment-o') .'<span>'. $issue->countUserComments() .'</span>'; ?>
            </div>
        <?php endif; ?>
        <?php if ($issue->countFiles()): ?>
            <div class="attachments-badge">
                <?php echo fa_image_tag('paperclip') .'<span>'. $issue->countFiles() .'</span>'; ?>
            </div>
        <?php endif; ?>
        <?php echo image_tag('icon_block.png', array('class' => 'blocking', 'title' => __('This issue is marked as a blocker'))); ?>
        <?php if ($issue->getStatus() instanceof \thebuggenie\core\entities\Datatype): ?>
            <div class="status_badge" style="background-color: <?php echo ($issue->getStatus() instanceof \thebuggenie\core\entities\Datatype) ? $issue->getStatus()->getColor() : '#FFF'; ?>;" title="<?php echo ($issue->getStatus() instanceof \thebuggenie\core\entities\Datatype) ? $issue->getStatus()->getName() : __('Unknown'); ?>">&nbsp;&nbsp;&nbsp;</div>
        <?php endif; ?>
        <?php if ($issue->isAssigned()): ?>
            <?php if ($issue->getAssignee() instanceof \thebuggenie\core\entities\User): ?>
                <a href="javascript:void(0);" onclick="TBG.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'usercard', 'user_id' => $issue->getAssignee()->getID())); ?>');"><?php echo image_tag($issue->getAssignee()->getAvatarURL(), array('alt' => ' ', 'class' => 'avatar'), true); ?></a>
            <?php else: ?>
                <?php echo include_component('main/teamdropdown', array('team' => $issue->getAssignee(), 'size' => 'large', 'displayname' => '')); ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    <div class="issue_percentage" title="<?php echo __('%percentage % completed', array('%percentage' => $issue->getPercentCompleted())); ?>">
        <div class="filler" id="issue_<?php echo $issue->getID(); ?>_percentage_filler" style="width: <?php echo $issue->getPercentCompleted(); ?>%;" title="<?php echo __('%percentage completed', array('%percentage' => $issue->getPercentCompleted().'%')); ?>"></div>
    </div>
</div>
