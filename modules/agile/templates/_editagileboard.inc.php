<?php

    use thebuggenie\modules\agile\entities\AgileBoard;

?>
<div class="backdrop_box large edit_agileboard sectioned">
    <div class="backdrop_detail_header">
        <?php echo ($board->getId()) ? __('Edit agile board') : __('Add agile board'); ?>
    </div>
    <div id="backdrop_detail_content" class="backdrop_detail_content">
        <form accept-charset="<?php echo \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>" action="<?php echo make_url('agile_board', array('project_key' => $board->getProject()->getKey(), 'board_id' => $board->getID())); ?>" method="post" id="edit_agileboard_form" onsubmit="TBG.Project.Planning.saveAgileBoard(this);return false;" data-board-id="<?php echo (int) $board->getId(); ?>">
            <input type="hidden" name="is_private" value="<?php echo (int) $board->isPrivate(); ?>">
            <input type="hidden" name="type" value="<?php echo $board->getType(); ?>" id="agileboard_type_input">
            <input type="hidden" name="swimlane" value="<?php echo $board->getSwimlaneType(); ?>" id="swimlane_input">
            <input type="hidden" name="use_swimlane" value="<?php echo (int) $board->usesSwimlanes(); ?>" id="use_swimlane_input">
            <label for="agileboard_name_<?php echo $board->getID(); ?>"><?php echo __('Name'); ?></label>
            <input type="text" class="primary" value="<?php echo $board->getName(); ?>" name="name" id="agileboard_name_<?php echo $board->getID(); ?>" placeholder="<?php echo __('Type a short, descriptive name such as "Project planning board"'); ?>">
            <table class="sectioned_table">
                <tr>
                    <td>
                        <label for="agileboard_type_<?php echo $board->getID(); ?>"><?php echo __('Board type'); ?></label>
                        <a href="javascript:void(0)" class="fancydropdown changeable" id="agileboard_type_<?php echo $board->getID(); ?>"><?php switch ($board->getType())
                                                {
                                                    case AgileBoard::TYPE_GENERIC:
                                                        echo __('Generic planning board');
                                                        break;
                                                    case AgileBoard::TYPE_SCRUM:
                                                        echo __('Scrum board');
                                                        break;
                                                    case AgileBoard::TYPE_KANBAN:
                                                        echo __('Kanban board');
                                                        break;
                                                }
                                                ?></a>
                        <ul data-input="agileboard_type_input" class="fancydropdown-list">
                            <li data-input-value="<?php echo AgileBoard::TYPE_GENERIC; ?>" data-display-name="<?php echo __('Generic planning board'); ?>" class="fancydropdown-item <?php if ($board->getType() == AgileBoard::TYPE_GENERIC) echo 'selected'; ?>">
                                <h1><?php echo __('Generic planning board'); ?></h1>
                                <?php echo image_tag('board_generic.png'); ?>
                                <p>
                                    <?php echo __('Just a generic planning board for planning upcoming milestones.'); ?>
                                </p>
                            </li>
                            <li data-input-value="<?php echo AgileBoard::TYPE_SCRUM; ?>" data-display-name="<?php echo __('Scrum board'); ?>" class="fancydropdown-item <?php if ($board->getType() == AgileBoard::TYPE_SCRUM) echo 'selected'; ?>">
                                <h1><?php echo __('Scrum board'); ?></h1>
                                <?php echo image_tag('board_scrum.png'); ?>
                                <p>
                                    <?php echo __('Board tailored for scrum-style workflows, card view as scrum stories with estimates.'); ?>
                                </p>
                            </li>
                            <li data-input-value="<?php echo AgileBoard::TYPE_KANBAN; ?>" data-display-name="<?php echo __('Kanban board'); ?>" class="fancydropdown-item <?php if ($board->getType() == AgileBoard::TYPE_KANBAN) echo 'selected'; ?>">
                                <h1><?php echo __('Kanban board'); ?></h1>
                                <?php echo image_tag('board_kanban.png'); ?>
                                <p>
                                    <?php echo __('Kanban board with workload limits and powerful plan mode.'); ?>
                                </p>
                            </li>
                        </ul>
                    </td>
                    <td>
                        <label for="agileboard_description_<?php echo $board->getID(); ?>"><?php echo __('Description'); ?></label>
                        <input type="text" class="secondary" value="<?php echo $board->getDescription(); ?>" name="description" id="agileboard_description_<?php echo $board->getID(); ?>" placeholder="<?php echo __('Type a short description to be shown in the board list'); ?>">
                    </td>
                </tr>
            </table>
            <h2><?php echo __('Planning mode settings'); ?></h2>
            <table class="sectioned_table">
                <tr>
                    <td>
                        <div class="fancyfilter filter interactive_dropdown" data-filterkey="epic_issuetype_id" data-value="<?php echo $board->getEpicIssuetypeID(); ?>" data-no-selection-value="<?php echo __('None selected'); ?>">
                            <input type="hidden" name="epic_issuetype_id" value="<?php echo $board->getEpicIssuetypeID(); ?>" id="filter_epic_issuetype_id_value_input">
                            <label><?php echo __('Epic issuetype'); ?></label>
                            <span class="value"><?php echo ($board->getEpicIssuetypeID()) ? $board->getEpicIssuetype()->getName() : __('None selected'); ?></span>
                            <div class="interactive_menu">
                                <h1><?php echo __('Select epic issuetype'); ?></h1>
                                <div class="interactive_values_container">
                                    <ul class="interactive_menu_values">
                                        <?php foreach ($issuetypes as $issuetype): ?>
                                            <li data-value="<?php echo $issuetype->getID(); ?>" class="filtervalue<?php if ($board->getEpicIssuetypeID() == $issuetype->getID()) echo ' selected'; ?>" data-exclusive>
                                                <?php echo image_tag('icon-mono-checked.png', array('class' => 'checked')); ?>
                                                <input type="checkbox" value="<?php echo $issuetype->getID(); ?>" name="epic_issuetype_id_<?php echo $issuetype->getID(); ?>" id="epic_issuetype_id_<?php echo $issuetype->getID(); ?>" data-text="<?php echo __($issuetype->getName()); ?>" id="filters_issuetype_value_<?php echo $issuetype->getID(); ?>" <?php if ($board->getEpicIssuetypeID() == $issuetype->getID()) echo 'checked'; ?>>
                                                <label for="epic_issuetype_id_<?php echo $issuetype->getID(); ?>"><?php echo __($issuetype->getName()); ?></label>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="fancyfilter filter interactive_dropdown" data-filterkey="task_issuetype_id" data-value="<?php echo $board->getTaskIssuetypeID(); ?>" data-no-selection-value="<?php echo __('None selected'); ?>">
                            <input type="hidden" name="task_issuetype_id" value="<?php echo $board->getTaskIssuetypeID(); ?>" id="filter_task_issuetype_id_value_input">
                            <label><?php echo __('Task issuetype'); ?></label>
                            <span class="value"><?php echo $board->getTaskIssuetype()->getName(); ?></span>
                            <div class="interactive_menu">
                                <h1><?php echo __('Select task issuetype'); ?></h1>
                                <div class="interactive_values_container">
                                    <ul class="interactive_menu_values">
                                        <?php foreach ($issuetypes as $issuetype): ?>
                                            <li data-value="<?php echo $issuetype->getID(); ?>" class="filtervalue<?php if ($board->getTaskIssuetypeID() == $issuetype->getID()) echo ' selected'; ?>" data-exclusive>
                                                <?php echo image_tag('icon-mono-checked.png', array('class' => 'checked')); ?>
                                                <input type="checkbox" value="<?php echo $issuetype->getID(); ?>" name="task_issuetype_id_<?php echo $issuetype->getID(); ?>" id="task_issuetype_id_<?php echo $issuetype->getID(); ?>" data-text="<?php echo __($issuetype->getName()); ?>" id="filters_issuetype_value_<?php echo $issuetype->getID(); ?>" <?php if ($board->getTaskIssuetypeID() == $issuetype->getID()) echo 'checked'; ?>>
                                                <label for="task_issuetype_id_<?php echo $issuetype->getID(); ?>"><?php echo __($issuetype->getName()); ?></label>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="fancyfilter filter interactive_dropdown" data-filterkey="backlog_search" data-value="<?php echo $board->getTaskIssuetypeID(); ?>" data-no-selection-value="<?php echo __('None selected'); ?>">
                            <input type="hidden" name="backlog_search" value="<?php echo $board->getBacklogSearchIdentifier(); ?>" id="filter_backlog_search_value_input">
                            <label><?php echo __('Backlog search'); ?></label>
                            <span class="value"><?php echo ($board->getBacklogSearch() instanceof \thebuggenie\core\entities\SavedSearch) ? $board->getBacklogSearch()->getName() : $autosearches[\thebuggenie\core\entities\SavedSearch::PREDEFINED_SEARCH_PROJECT_OPEN_ISSUES]; ?></span>
                            <div class="interactive_menu">
                                <h1><?php echo __('Select search to use for backlog'); ?></h1>
                                <div class="interactive_values_container">
                                    <ul class="interactive_menu_values">
                                        <?php foreach ($autosearches as $value => $description): ?>
                                            <?php $is_selected = ($board->usesAutogeneratedSearchBacklog() && $board->getAutogeneratedSearch() == $value); ?>
                                            <li data-value="predefined_<?php echo $value; ?>" class="filtervalue<?php if ($is_selected) echo ' selected'; ?>" data-exclusive data-selection-group="1">
                                                <?php echo image_tag('icon-mono-checked.png', array('class' => 'checked')); ?>
                                                <input type="checkbox" value="predefined_<?php echo $value; ?>" name="backlog_search_predefined_<?php echo $value; ?>" id="backlog_search_predefined_<?php echo $value; ?>" data-text="<?php echo $description; ?>" <?php if ($is_selected) echo 'checked'; ?>>
                                                <label for="backlog_search_predefined_<?php echo $value; ?>"><?php echo $description; ?></label>
                                            </li>
                                        <?php endforeach; ?>
                                        <li class="separator"></li>
                                        <?php if (count($savedsearches['public']) > 0): ?>
                                            <?php foreach ($savedsearches['public'] as $savedsearch): ?>
                                                <?php $is_selected = ($board->usesSavedSearchBacklog() && $board->getBacklogSearch()->getID() == $savedsearch->getID()); ?>
                                                <li data-value="saved_<?php echo $savedsearch->getID(); ?>" class="filtervalue<?php if ($is_selected) echo ' selected'; ?>" data-exclusive data-selection-group="1">
                                                    <?php echo image_tag('icon-mono-checked.png', array('class' => 'checked')); ?>
                                                    <input type="checkbox" value="saved_<?php echo $savedsearch->getID(); ?>" name="backlog_search_saved_<?php echo $savedsearch->getID(); ?>" id="backlog_search_saved_<?php echo $savedsearch->getID(); ?>" data-text="<?php echo $savedsearch->getName(); ?>" <?php if ($is_selected) echo 'checked'; ?>>
                                                    <label for="backlog_search_saved_<?php echo $savedsearch->getID(); ?>"><?php echo $savedsearch->getName(); ?></label>
                                                </li>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <li class="disabled"><?php echo __('There are no public saved searches for this project'); ?></li>
                                        <?php endif; ?>
                                        <li class="separator"></li>
                                        <?php if (count($savedsearches['user']) > 0): ?>
                                            <?php foreach ($savedsearches['user'] as $savedsearch): ?>
                                                <?php $is_selected = ($board->usesSavedSearchBacklog() && $board->getBacklogSearch()->getID() == $savedsearch->getID()); ?>
                                                <li data-value="saved_<?php echo $savedsearch->getID(); ?>" class="filtervalue<?php if ($is_selected) echo ' selected'; ?>" data-exclusive data-selection-group="1">
                                                    <?php echo image_tag('icon-mono-checked.png', array('class' => 'checked')); ?>
                                                    <input type="checkbox" value="saved_<?php echo $savedsearch->getID(); ?>" name="backlog_search_saved_<?php echo $savedsearch->getID(); ?>" id="backlog_search_saved_<?php echo $savedsearch->getID(); ?>" data-text="<?php echo $savedsearch->getName(); ?>" <?php if ($is_selected) echo 'checked'; ?>>
                                                    <label for="backlog_search_saved_<?php echo $savedsearch->getID(); ?>"><?php echo $savedsearch->getName(); ?></label>
                                                </li>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <li class="disabled"><?php echo __('You have no saved searches for this project'); ?></li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="description">
                            <?php echo __('The backlog search is used to display the backlog - a list of unassigned issues for this board.'); ?>
                        </div>
                    </td>
                </tr>
            </table>
            <h2><?php echo __('Whiteboard mode settings'); ?></h2>
            <table class="sectioned_table">
                <tr>
                    <td>
                        <label for="agileboard_swimlane_<?php echo $board->getID(); ?>"><?php echo __('Whiteboard swimlanes'); ?></label>
                        <a href="javascript:void(0)" class="fancydropdown changeable" id="swimlane_<?php echo $board->getID(); ?>"><?php

                            if (!$board->usesSwimlanes())
                            {
                                echo __('Not used');
                            }
                            else
                            {
                                switch ($board->getSwimlaneType())
                                {
                                    case AgileBoard::SWIMLANES_ISSUES:
                                        echo __('Issue swimlanes');
                                        break;
                                    case AgileBoard::SWIMLANES_GROUPING:
                                        echo __('Issues detail swimlanes');
                                        break;
                                    case AgileBoard::SWIMLANES_EXPEDITE:
                                        echo __('Level of service swimlane');
                                        break;
                                }
                            }

                        ?></a>
                        <ul data-input="use_swimlane_input" class="fancydropdown-list" data-callback="TBG.Project.Planning.toggleSwimlaneDetails">
                            <li data-input-value="0" data-swimlane-type="none" data-display-name="<?php echo __('Not used'); ?>" class="fancydropdown-item novalue <?php if (!$board->usesSwimlanes()) echo ' selected'; ?>" onclick="TBG.Project.Planning.toggleSwimlaneDetails(this);"><p><?php echo __("Don't use swimlanes"); ?></p></li>
                            <li data-input-value="1" data-swimlane-type="<?php echo AgileBoard::SWIMLANES_ISSUES; ?>" data-display-name="<?php echo __('Issue swimlanes'); ?>" class="fancydropdown-item <?php if ($board->usesSwimlanes() && $board->getSwimlaneType() == AgileBoard::SWIMLANES_ISSUES) echo 'selected'; ?>" onclick="TBG.Project.Planning.toggleSwimlaneDetails(this);">
                                <h1><?php echo __('Issue swimlanes'); ?></h1>
                                <?php echo image_tag('swimlanes_issues.png'); ?>
                                <p>
                                    <?php echo __('The board has a swimlane for each issue of one or more issue type(s).'); ?>
                                </p>
                            </li>
                            <li data-input-value="1" data-swimlane-type="<?php echo AgileBoard::SWIMLANES_GROUPING; ?>" data-display-name="<?php echo __('Issue detail swimlanes'); ?>" class="fancydropdown-item <?php if ($board->usesSwimlanes() && $board->getSwimlaneType() == AgileBoard::SWIMLANES_GROUPING) echo 'selected'; ?>" onclick="TBG.Project.Planning.toggleSwimlaneDetails(this);">
                                <h1><?php echo __('Issue detail swimlanes'); ?></h1>
                                <?php echo image_tag('swimlanes_grouping.png'); ?>
                                <p>
                                    <?php echo __('The board is grouped in swimlanes where issues that share the same characteristics are grouped together.'); ?>
                                </p>
                            </li>
                            <li data-input-value="1" data-swimlane-type="<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>" data-display-name="<?php echo __('Level of service swimlane'); ?>" class="fancydropdown-item <?php if ($board->usesSwimlanes() && $board->getSwimlaneType() == AgileBoard::SWIMLANES_EXPEDITE) echo 'selected'; ?>" onclick="TBG.Project.Planning.toggleSwimlaneDetails(this);">
                                <h1><?php echo __('Level of service swimlane'); ?></h1>
                                <?php echo image_tag('swimlanes_expedite.png'); ?>
                                <p>
                                    <?php echo __('No general grouping, but an increased level of service swimlane at the top for expediting issues'); ?>
                                </p>
                            </li>
                        </ul>
                    </td>
                    <td id="swimlane_details_container">
                        <div id="swimlane_none_container" style="<?php if ($board->usesSwimlanes()) echo 'display: none;'; ?>">
                            <div class="description"><?php echo __('There will be no swimlanes on the board'); ?></div>
                        </div>
                        <div id="swimlane_<?php echo AgileBoard::SWIMLANES_ISSUES; ?>_container" style="<?php if (!$board->usesSwimlanes() || $board->getSwimlaneType() != AgileBoard::SWIMLANES_ISSUES) echo 'display: none;'; ?>">
                            <input type="hidden" name="swimlane_<?php echo AgileBoard::SWIMLANES_ISSUES; ?>_identifier" value="issuetype" id="filter_swimlane_<?php echo AgileBoard::SWIMLANES_ISSUES; ?>_identifier_input">
                            <div class="fancyfilter filter interactive_dropdown" data-filterkey="swimlane_<?php echo AgileBoard::SWIMLANES_ISSUES; ?>_issuetype" data-value="<?php echo join(',', $board->getSwimlaneFieldValues()); ?>" data-no-selection-value="<?php echo __('None selected'); ?>">
                                <input type="hidden" name="swimlane_<?php echo AgileBoard::SWIMLANES_ISSUES; ?>_details[issuetype]" value="<?php echo join(',', $board->getSwimlaneFieldValues()); ?>" id="filter_swimlane_<?php echo AgileBoard::SWIMLANES_ISSUES; ?>_issuetype_value_input">
                                <label><?php echo __('Issuetype(s)'); ?></label>
                                <span class="value"><?php if (!$board->hasSwimlaneFieldValues()) echo __('None selected'); ?></span>
                                <div class="interactive_menu">
                                    <h1><?php echo __('Select issuetype(s)'); ?></h1>
                                    <div class="interactive_values_container">
                                        <ul class="interactive_menu_values">
                                            <?php foreach ($issuetypes as $issuetype): ?>
                                                <li data-value="<?php echo $issuetype->getID(); ?>" class="filtervalue<?php if ($board->hasSwimlaneFieldValue($issuetype->getID())) echo ' selected'; ?>">
                                                    <?php echo image_tag('icon-mono-checked.png', array('class' => 'checked')); ?>
                                                    <input type="checkbox" value="<?php echo $issuetype->getID(); ?>" name="swimlane_<?php echo AgileBoard::SWIMLANES_ISSUES; ?>_issuetype_<?php echo $issuetype->getID(); ?>" id="swimlane_<?php echo AgileBoard::SWIMLANES_ISSUES; ?>_issuetype_<?php echo $issuetype->getID(); ?>" data-text="<?php echo __($issuetype->getName()); ?>" id="filters_issuetype_value_<?php echo $issuetype->getID(); ?>" <?php if ($board->hasSwimlaneFieldValue($issuetype->getID())) echo 'checked'; ?>>
                                                    <label name="swimlane_<?php echo AgileBoard::SWIMLANES_ISSUES; ?>_issuetype_<?php echo $issuetype->getID(); ?>"><?php echo __($issuetype->getName()); ?></label>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <p class="description"><?php echo __('The whiteboard will have separate swimlanes for all issues that is of a certain type. Specify which issuetype qualifies as a swimlane.'); ?></p>
                        </div>
                        <div id="swimlane_<?php echo AgileBoard::SWIMLANES_GROUPING; ?>_container" style="<?php if (!$board->usesSwimlanes() || $board->getSwimlaneType() != AgileBoard::SWIMLANES_GROUPING) echo 'display: none;'; ?>">
                            <div class="fancyfilter filter interactive_dropdown" data-filterkey="swimlane_<?php echo AgileBoard::SWIMLANES_GROUPING; ?>_identifier" data-value="<?php echo $board->getSwimlaneIdentifier(); ?>" data-no-selection-value="<?php echo __('None selected'); ?>">
                                <input type="hidden" name="swimlane_<?php echo AgileBoard::SWIMLANES_GROUPING; ?>_identifier" value="<?php echo $board->getSwimlaneIdentifier(); ?>" id="filter_swimlane_<?php echo AgileBoard::SWIMLANES_GROUPING; ?>_identifier_value_input">
                                <label><?php echo __('Group by'); ?></label>
                                <span class="value"><?php if (!$board->getSwimlaneIdentifier()) echo __('None selected'); ?></span>
                                <div class="interactive_menu">
                                    <h1><?php echo __('Select detail to group by'); ?></h1>
                                    <div class="interactive_values_container">
                                        <ul class="interactive_menu_values">
                                            <?php foreach ($swimlane_groups as $value => $description): ?>
                                                <?php $is_selected = ($board->usesSwimlanes() && $board->getSwimlaneType() == AgileBoard::SWIMLANES_GROUPING && $board->getSwimlaneIdentifier() == $value); ?>
                                                <li data-value="<?php echo $value; ?>" class="filtervalue<?php if ($is_selected) echo ' selected'; ?>" data-exclusive>
                                                    <?php echo image_tag('icon-mono-checked.png', array('class' => 'checked')); ?>
                                                    <input type="checkbox" value="<?php echo $value; ?>" name="swimlane_<?php echo AgileBoard::SWIMLANES_GROUPING; ?>_identifier_<?php echo $value; ?>" id="swimlane_<?php echo AgileBoard::SWIMLANES_GROUPING; ?>_identifier_<?php echo $value; ?>" data-text="<?php echo $description; ?>" <?php if ($is_selected) echo 'checked'; ?>>
                                                    <label for="swimlane_<?php echo AgileBoard::SWIMLANES_GROUPING; ?>_identifier_<?php echo $value; ?>"><?php echo $description; ?></label>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <p class="description"><?php echo __('The whiteboard will have separate swimlanes / groups for issues that share the same characteristics. Specify which issue detail to group issues by.'); ?></p>
                        </div>
                        <div id="swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_container" style="<?php if (!$board->usesSwimlanes() || $board->getSwimlaneType() != AgileBoard::SWIMLANES_EXPEDITE) echo 'display: none;'; ?>">
                            <div class="fancyfilter filter interactive_dropdown" data-filterkey="swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_identifier" data-value="<?php echo $board->getSwimlaneIdentifier(); ?>" data-no-selection-value="<?php echo __('None selected'); ?>">
                                <input type="hidden" name="swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_identifier" value="<?php echo $board->getSwimlaneIdentifier(); ?>" id="filter_swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_identifier_value_input">
                                <label><?php echo __('Issue detail'); ?></label>
                                <span class="value"><?php if (!$board->getSwimlaneIdentifier()) echo __('None selected'); ?></span>
                                <div class="interactive_menu">
                                    <h1><?php echo __('Select issue field for expedite swimlane'); ?></h1>
                                    <div class="interactive_values_container">
                                        <ul class="interactive_menu_values">
                                            <?php foreach ($swimlane_groups as $value => $description): ?>
                                                <?php $is_selected = ($board->usesSwimlanes() && $board->getSwimlaneType() == AgileBoard::SWIMLANES_EXPEDITE && $board->getSwimlaneIdentifier() == $value); ?>
                                                <li data-value="<?php echo $value; ?>" class="filtervalue<?php if ($is_selected) echo ' selected'; ?>" data-exclusive onclick="TBG.Project.Planning.toggleSwimlaneExpediteDetails(this);">
                                                    <?php echo image_tag('icon-mono-checked.png', array('class' => 'checked')); ?>
                                                    <input type="checkbox" value="<?php echo $value; ?>" name="swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_identifier_<?php echo $value; ?>" id="swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_identifier_<?php echo $value; ?>" data-text="<?php echo $description; ?>" <?php if ($is_selected) echo 'checked'; ?>>
                                                    <label for="swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_identifier_<?php echo $value; ?>"><?php echo $description; ?></label>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div id="swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_container_details">
                                <div class="fancyfilter filter interactive_dropdown" data-filterkey="swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_identifier_priority" data-value="<?php echo join(',', $board->getSwimlaneFieldValues()); ?>" data-no-selection-value="<?php echo __('None selected'); ?>" id="swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_identifier_priority_values" style="<?php if (!($board->usesSwimlanes() && $board->getSwimlaneType() == AgileBoard::SWIMLANES_EXPEDITE && $board->getSwimlaneIdentifier() == 'priority')) echo 'display: none;'; ?>">
                                    <input type="hidden" name="swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_details[priority]" value="<?php echo join(',', $board->getSwimlaneFieldValues()); ?>" id="filter_swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_identifier_priority_value_input">
                                    <label><?php echo __('Field value(s)'); ?></label>
                                    <span class="value"><?php if (!$board->hasSwimlaneFieldValues()) echo __('None selected'); ?></span>
                                    <div class="interactive_menu">
                                        <h1><?php echo __('Select values for expedite issues'); ?></h1>
                                        <div class="interactive_values_container">
                                            <ul class="interactive_menu_values">
                                                <?php foreach ($priorities as $priority): ?>
                                                    <?php $is_selected = ($board->usesSwimlanes() && $board->getSwimlaneType() == AgileBoard::SWIMLANES_EXPEDITE && $board->getSwimlaneIdentifier() == 'priority' && $board->hasSwimlaneFieldValue($priority->getID())); ?>
                                                    <li data-value="<?php echo $priority->getID(); ?>" class="filtervalue<?php if ($is_selected) echo ' selected'; ?>">
                                                        <?php echo image_tag('icon-mono-checked.png', array('class' => 'checked')); ?>
                                                        <input type="checkbox" value="<?php echo $priority->getID(); ?>" name="swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_identifier_<?php echo $priority->getID(); ?>" id="swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_identifier_<?php echo $priority->getID(); ?>" data-text="<?php echo $priority->getName(); ?>" <?php if ($is_selected) echo 'checked'; ?>>
                                                        <label for="swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_identifier_<?php echo $priority->getID(); ?>"><?php echo $priority->getName(); ?></label>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="fancyfilter filter interactive_dropdown" data-filterkey="swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_identifier_severity" data-value="<?php echo join(',', $board->getSwimlaneFieldValues()); ?>" data-no-selection-value="<?php echo __('None selected'); ?>" id="swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_identifier_severity_values" style="<?php if (!($board->usesSwimlanes() && $board->getSwimlaneType() == AgileBoard::SWIMLANES_EXPEDITE && $board->getSwimlaneIdentifier() == 'severity')) echo 'display: none;'; ?>">
                                    <input type="hidden" name="swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_details[severity]" value="<?php echo join(',', $board->getSwimlaneFieldValues()); ?>" id="filter_swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_identifier_severity_value_input">
                                    <label><?php echo __('Field value(s)'); ?></label>
                                    <span class="value"><?php if (!$board->hasSwimlaneFieldValues()) echo __('None selected'); ?></span>
                                    <div class="interactive_menu">
                                        <h1><?php echo __('Select values for expedite issues'); ?></h1>
                                        <div class="interactive_values_container">
                                            <ul class="interactive_menu_values">
                                                <?php foreach ($severities as $severity): ?>
                                                    <?php $is_selected = ($board->usesSwimlanes() && $board->getSwimlaneType() == AgileBoard::SWIMLANES_EXPEDITE && $board->getSwimlaneIdentifier() == 'severity' && $board->hasSwimlaneFieldValue($severity->getID())); ?>
                                                    <li data-value="<?php echo $severity->getID(); ?>" class="filtervalue<?php if ($is_selected) echo ' selected'; ?>">
                                                        <?php echo image_tag('icon-mono-checked.png', array('class' => 'checked')); ?>
                                                        <input type="checkbox" value="<?php echo $severity->getID(); ?>" name="swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_identifier_<?php echo $severity->getID(); ?>" id="swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_identifier_<?php echo $severity->getID(); ?>" data-text="<?php echo $severity->getName(); ?>" <?php if ($is_selected) echo 'checked'; ?>>
                                                        <label for="swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_identifier_<?php echo $severity->getID(); ?>"><?php echo $severity->getName(); ?></label>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="fancyfilter filter interactive_dropdown" data-filterkey="swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_identifier_category" data-value="<?php echo join(',', $board->getSwimlaneFieldValues()); ?>" data-no-selection-value="<?php echo __('None selected'); ?>" id="swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_identifier_category_values" style="<?php if (!($board->usesSwimlanes() && $board->getSwimlaneType() == AgileBoard::SWIMLANES_EXPEDITE && $board->getSwimlaneIdentifier() == 'category')) echo 'display: none;'; ?>">
                                    <input type="hidden" name="swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_details[category]" value="<?php echo join(',', $board->getSwimlaneFieldValues()); ?>" id="filter_swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_identifier_category_value_input">
                                    <label><?php echo __('Field value(s)'); ?></label>
                                    <span class="value"><?php if (!$board->hasSwimlaneFieldValues()) echo __('None selected'); ?></span>
                                    <div class="interactive_menu">
                                        <h1><?php echo __('Select values for expedite issues'); ?></h1>
                                        <div class="interactive_values_container">
                                            <ul class="interactive_menu_values">
                                                <?php foreach ($categories as $category): ?>
                                                    <?php $is_selected = ($board->usesSwimlanes() && $board->getSwimlaneType() == AgileBoard::SWIMLANES_EXPEDITE && $board->getSwimlaneIdentifier() == 'category' && $board->hasSwimlaneFieldValue($category->getID())); ?>
                                                    <li data-value="<?php echo $category->getID(); ?>" class="filtervalue<?php if ($is_selected) echo ' selected'; ?>">
                                                        <?php echo image_tag('icon-mono-checked.png', array('class' => 'checked')); ?>
                                                        <input type="checkbox" value="<?php echo $category->getID(); ?>" name="swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_identifier_<?php echo $category->getID(); ?>" id="swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_identifier_<?php echo $category->getID(); ?>" data-text="<?php echo $category->getName(); ?>" <?php if ($is_selected) echo 'checked'; ?>>
                                                        <label for="swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_identifier_<?php echo $category->getID(); ?>"><?php echo $category->getName(); ?></label>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <p class="description"><?php echo __('The whiteboard will have a separate swimlane at the top for prioritized issues, like a expedite line / fastlane. Select which issue details puts issues in this swimlane.'); ?></p>
                        </div>
                    </td>
                </tr>
            </table>
            <div class="backdrop_details_submit">
                <?php if ($board->getID()): ?>
                    <input type="hidden" name="board_id" value="<?php echo $board->getID(); ?>">
                <?php endif; ?>
                <?php echo __('%cancel or %save_board', array('%cancel' => javascript_link_tag(__('Cancel'), array('onclick' => 'TBG.Main.Helpers.Backdrop.reset();')), '%save_board' => '')); ?>
                <span id="agileboard_edit_indicator" style="display: none;"><?php echo image_tag('spinning_20.gif'); ?></span>
                <input class="button button-silver" style="" type="submit" value="<?php echo __('Save board'); ?>" id="agileboard_save_button">
            </div>
        </form>
    </div>
</div>
