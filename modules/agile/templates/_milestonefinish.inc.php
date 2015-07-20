<?php

    use thebuggenie\modules\agile\entities\AgileBoard;

    switch ($board->getType())
    {
        case AgileBoard::TYPE_GENERIC:
            $savelabel = __('Mark milestone finished');
            break;
        case AgileBoard::TYPE_SCRUM:
        case AgileBoard::TYPE_KANBAN:
            $savelabel = __('Mark sprint finished');
            break;
    }
?>
<form accept-charset="<?php echo \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>" action="<?php echo make_url('agile_markmilestonefinished', array('project_key' => $milestone->getProject()->getKey(), 'board_id' => $board->getID(), 'milestone_id' => $milestone->getID())); ?>" method="post" id="mark_milestone_finished_form" onsubmit="TBG.Project.Milestone.markFinished(this);return false;" data-milestone-id="<?php echo $milestone->getID(); ?>">
    <div class="backdrop_box large sectioned" id="milestone_finish_container">
        <div class="backdrop_detail_header">
            <?php
                switch ($board->getType())
                {
                    case AgileBoard::TYPE_GENERIC:
                        echo __('Mark milestone as finished');
                        break;
                    case AgileBoard::TYPE_SCRUM:
                    case AgileBoard::TYPE_KANBAN:
                        echo __('Mark sprint as finished');
                        break;
                }
            ?>
        </div>
        <div id="backdrop_detail_content" class="backdrop_detail_content edit_milestone">
            <?php
                switch ($board->getType())
                {
                    case AgileBoard::TYPE_GENERIC:
                        echo __('Milestone %milestone_name will be marked as finished.', array('%milestone_name' => $milestone->getName()));
                        break;
                    case AgileBoard::TYPE_SCRUM:
                    case AgileBoard::TYPE_KANBAN:
                        echo __('Sprint %milestone_name will be marked as finished.', array('%milestone_name' => $milestone->getName()));
                        break;
                }
            ?>
            <table class="sectioned_table">
                <tr>
                    <td><label for="reached_date_<?php echo $milestone->getID(); ?>"><?php echo __('Milestone reached'); ?></label></td>
                    <td style="width: auto;">
                        <select style="width: 90px;" name="milestone_finish_reached_month" id="reached_month_<?php echo $milestone->getID(); ?>">
                        <?php for ($cc = 1;$cc <= 12;$cc++): ?>
                            <option value="<?php echo $cc; ?>" <?php if ($milestone->getReachedMonth() == $cc || (!$milestone->hasReachedDate() && $cc == date('m'))) echo " selected"; ?>><?php echo strftime('%B', mktime(0, 0, 0, $cc, 1)); ?></option>
                        <?php endfor; ?>
                        </select>
                        <select style="width: 45px;" name="milestone_finish_reached_day" id="reached_day_<?php echo $milestone->getID(); ?>">
                        <?php for ($cc = 1;$cc <= 31;$cc++): ?>
                            <option value="<?php echo $cc; ?>" <?php if ($milestone->getReachedDay() == $cc || (!$milestone->hasReachedDate() && $cc == date('d'))) echo " selected"; ?>><?php echo $cc; ?></option>
                        <?php endfor; ?>
                        </select>
                        <select style="width: 60px;" name="milestone_finish_reached_year" id="reached_year_<?php echo $milestone->getID(); ?>">
                        <?php for ($cc = 1990;$cc <= (date("Y") + 10);$cc++): ?>
                            <option value="<?php echo $cc; ?>" <?php if ($milestone->getReachedYear() == $cc || (!$milestone->hasReachedDate() && $cc == date('Y'))) echo " selected"; ?>><?php echo $cc; ?></option>
                        <?php endfor; ?>
                        </select>
                    </td>
                </tr>
            </table>
            <?php if ($milestone->countOpenIssues()): ?>
                <div id="milestone_include_issues">
                    <div class="milestone_include_issues">
                        <?php echo __('There are %number issue(s) which are not currently resolved. Please select what to do with these issues, below.', array('%number' => $milestone->countOpenIssues())); ?>
                    </div>
                    <label for="select_unresolved_issues_action"><?php echo __('Unresolved issues action'); ?></label>
                    <select name="unresolved_issues_action" id="select_unresolved_issues_action" onchange="switch($(this).getValue()) { case 'keep': $('reassign_select').hide(); $('mark_milestone_finished_submit').show(); $('mark_milestone_finished_next').hide(); break; case 'reassign': $('reassign_select').show(); $('mark_milestone_finished_submit').show(); $('mark_milestone_finished_next').hide(); break; case 'addnew': $('reassign_select').hide(); $('mark_milestone_finished_submit').hide(); $('mark_milestone_finished_next').show(); }">
                        <option value="keep"><?php echo __("Don't do anything"); ?></option>
                        <option value="backlog" selected><?php echo __("Move to the backlog"); ?></option>
                        <option value="reassign"><?php
                            switch ($board->getType())
                            {
                                case AgileBoard::TYPE_GENERIC:
                                    echo __('Assign to an existing, unfinished milestone');
                                    break;
                                case AgileBoard::TYPE_SCRUM:
                                case AgileBoard::TYPE_KANBAN:
                                    echo __('Assign to an existing, unfinished sprint');
                                    break;
                            }
                        ?></option>
                        <option value="addnew"><?php
                            switch ($board->getType())
                            {
                                case AgileBoard::TYPE_GENERIC:
                                    echo __('Assign to a new milestone');
                                    break;
                                case AgileBoard::TYPE_SCRUM:
                                case AgileBoard::TYPE_KANBAN:
                                    echo __('Assign to a new sprint');
                                    break;
                            }
                        ?></option>
                    </select>
                    <select id="reassign_select" name="assign_issues_milestone_id" style="display: none;">
                        <?php foreach ($board->getMilestones() as $upcoming_milestone): ?>
                            <?php if ($upcoming_milestone->getID() == $milestone->getID()) continue; ?>
                            <option value="<?php echo $upcoming_milestone->getID(); ?>"><?php echo $upcoming_milestone->getName(); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>
            <div class="backdrop_details_submit">
                <?php echo __('%cancel or %mark_milestone_finished', array('%cancel' => javascript_link_tag(__('Cancel'), array('onclick' => 'TBG.Main.Helpers.Backdrop.reset();')), '%mark_milestone_finished' => '')); ?>
                <span id="milestone_edit_indicator" style="display: none;"><?php echo image_tag('spinning_20.gif'); ?></span>
                <input class="button button-silver" id="mark_milestone_finished_submit" type="submit" value="<?php echo $savelabel; ?>">
                <input class="button button-silver" id="mark_milestone_finished_next" type="button" value="<?php echo __('Next'); ?>" style="display: none;" onclick="['milestone_finish_container', 'edit_milestone_container'].each(Element.toggle);">
            </div>
        </div>
    </div>
    <?php if ($milestone->countOpenIssues()): ?>
        <?php include_component('agile/milestone', array('milestone' => new \thebuggenie\core\entities\Milestone($milestone->getID()), 'board' => $board, 'includeform' => false, 'starthidden' => true, 'savebuttonlabel' => $savelabel)); ?>
    <?php endif; ?>
</form>
