<?php if ($filter instanceof \thebuggenie\core\entities\SearchFilter): ?>
    <?php

        switch ($filter->getFilterKey())
        {
            case 'project_id':
                ?>
                <?php if (\thebuggenie\core\framework\Context::isProjectContext()): ?>
                    <input type="hidden" name="fs[project_id][o]" value="=">
                    <input type="hidden" name="fs[project_id][v]" value="<?php echo \thebuggenie\core\framework\Context::getCurrentProject()->getID(); ?>" id="filter_project_id_value_input">
                <?php else: ?>
                    <div class="filter interactive_dropdown" data-filterkey="project_id" data-value="<?php echo $filter->getValue(); ?>" data-all-value="<?php echo __('All'); ?>">
                        <input type="hidden" name="fs[project_id][o]" value="<?php echo $filter->getOperator(); ?>">
                        <input type="hidden" name="fs[project_id][v]" value="" id="filter_project_id_value_input">
                        <label><?php echo __('Project(s)'); ?></label>
                        <span class="value"><?php if (!$filter->hasValue()) echo __('All'); ?></span>
                        <div class="interactive_menu">
                            <h1><?php echo __('Choose issues from project(s)'); ?></h1>
                            <input type="search" class="interactive_menu_filter" placeholder="<?php echo __('Filter values'); ?>">
                            <div class="interactive_values_container">
                                <ul class="interactive_menu_values">
                                    <?php foreach ($filter->getAvailableValues() as $project): ?>
                                        <li data-value="<?php echo $project->getID(); ?>" class="filtervalue<?php if ($filter->hasValue($project->getID())) echo ' selected'; ?>">
                                            <?php echo image_tag('icon-mono-checked.png', array('class' => 'checked')); ?>
                                            <?php echo image_tag($project->getSmallIconName(), array('class' => 'icon'), $project->hasSmallIcon()); ?>
                                            <input type="checkbox" value="<?php echo $project->getID(); ?>" name="filters_project_id_value_<?php echo $project->getID(); ?>" data-text="<?php echo $project->getName(); ?>" id="filters_project_id_value_<?php echo $project->getID(); ?>" <?php if ($filter->hasValue($project->getID())) echo 'checked'; ?>>
                                            <label for="filters_project_id_value_<?php echo $project->getID(); ?>"><?php echo $project->getName(); ?></label>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                <?php
                break;
            case 'issuetype':
                ?>
                <div class="filter interactive_dropdown" data-filterkey="issuetype" data-value="<?php echo $filter->getValue(); ?>" data-all-value="<?php echo __('All'); ?>">
                    <input type="hidden" name="fs[issuetype][o]" value="<?php echo $filter->getOperator(); ?>">
                    <input type="hidden" name="fs[issuetype][v]" value="" id="filter_issuetype_value_input">
                    <label><?php echo __('Issuetype'); ?></label>
                    <span class="value"><?php if (!$filter->hasValue()) echo __('All'); ?></span>
                    <div class="interactive_menu">
                        <h1><?php echo __('Filter on issuetype'); ?></h1>
                        <input type="search" class="interactive_menu_filter" placeholder="<?php echo __('Filter values'); ?>">
                        <div class="interactive_values_container">
                            <ul class="interactive_menu_values">
                                <?php foreach ($filter->getAvailableValues() as $issuetype): ?>
                                    <li data-value="<?php echo $issuetype->getID(); ?>" class="filtervalue<?php if ($filter->hasValue($issuetype->getID())) echo ' selected'; ?>">
                                        <?php echo image_tag('icon-mono-checked.png', array('class' => 'checked')); ?>
                                        <input type="checkbox" value="<?php echo $issuetype->getID(); ?>" name="filters_issuetype_value_<?php echo $issuetype->getID(); ?>" data-text="<?php echo __($issuetype->getName()); ?>" id="filters_issuetype_value_<?php echo $issuetype->getID(); ?>" <?php if ($filter->hasValue($issuetype->getID())) echo 'checked'; ?>>
                                        <label for="filters_issuetype_value_<?php echo $issuetype->getID(); ?>"><?php echo __($issuetype->getName()); ?></label>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <?php
                break;
            case 'posted_by':
            case 'owner_user':
            case 'assignee_user':
                include_component('search/interactivefilter_user', compact('filter'));
                break;
            case 'owner_team':
            case 'assignee_team':
                include_component('search/interactivefilter_team', compact('filter'));
                break;
            case 'status':
                include_component('search/interactivefilter_status', compact('filter'));
                break;
            case 'category':
                include_component('search/interactivefilter_category', compact('filter'));
                break;
            case 'build':
            case 'component':
            case 'edition':
            case 'milestone':
                include_component('search/interactivefilter_affected', compact('filter'));
                break;
            case 'subprojects':
                ?>
                <div class="filter interactive_dropdown" id="interactive_filter_subprojects" data-filterkey="subprojects" data-value="<?php echo $filter->getValue(); ?>" data-all-value="<?php echo __('All'); ?>">
                    <input type="hidden" name="fs[subprojects][o]" value="<?php echo $filter->getOperator(); ?>">
                    <input type="hidden" name="fs[subprojects][v]" value="" id="filter_subprojects_value_input">
                    <label><?php echo __('Subproject(s)'); ?></label>
                    <span class="value"><?php if (!$filter->hasValue()) echo __('All'); ?></span>
                    <div class="interactive_menu">
                        <h1><?php echo __('Include issues from subproject(s)'); ?></h1>
                        <input type="search" class="interactive_menu_filter" placeholder="<?php echo __('Filter values'); ?>">
                        <div class="interactive_values_container">
                            <ul class="interactive_menu_values">
                                <li data-value="all" class="filtervalue <?php if ($filter->hasValue('all')) echo ' selected'; ?>" data-exclusive data-selection-group="1" data-exclude-group="2">
                                    <?php echo image_tag('icon-mono-checked.png', array('class' => 'checked')); ?>
                                    <input type="checkbox" value="all" name="filters_subprojects_value_exclusive_all" data-text="<?php echo __('All'); ?>" id="filters_subprojects_value_all" <?php if ($filter->hasValue('all')) echo 'checked'; ?>>
                                    <label for="filters_subprojects_value_all"><?php echo __('All'); ?></label>
                                </li>
                                <li data-value="none" class="filtervalue <?php if ($filter->hasValue('none')) echo ' selected'; ?>" data-exclusive data-selection-group="1" data-exclude-group="2">
                                    <?php echo image_tag('icon-mono-checked.png', array('class' => 'checked')); ?>
                                    <input type="checkbox" value="none" name="filters_subprojects_value_exclusive_none" data-text="<?php echo __('None'); ?>" id="filters_subprojects_value_none" <?php if ($filter->hasValue('none')) echo 'checked'; ?>>
                                    <label for="filters_subprojects_value_none"><?php echo __('None'); ?></label>
                                </li>
                                <li class="separator"></li>
                                <?php foreach ($filter->getAvailableValues() as $subproject): ?>
                                    <li data-value="<?php echo $subproject->getID(); ?>" class="filtervalue<?php if ($filter->hasValue($subproject->getID())) echo ' selected'; ?>" data-selection-group="2" data-exclude-group="1">
                                        <?php echo image_tag('icon-mono-checked.png', array('class' => 'checked')); ?>
                                        <input type="checkbox" value="<?php echo $subproject->getID(); ?>" name="filters_subprojects_value_<?php echo $subproject->getID(); ?>" data-text="<?php echo __($subproject->getName()); ?>" id="filters_subprojects_value_<?php echo $subproject->getID(); ?>" <?php if ($filter->hasValue($subproject->getID())) echo 'checked'; ?>>
                                        <label for="filters_subprojects_value_<?php echo $subproject->getID(); ?>"><?php echo $subproject->getName(); ?>&nbsp;&nbsp;<span class="faded_out"><?php echo $subproject->getKey(); ?></span></label>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                    <div class="filter_remove_button" onclick="TBG.Search.removeFilter($(this).up());"><?php echo image_tag('icon-mono-remove.png'); ?></div>
                </div>
                <?php
                break;
            case 'blocking':
                ?>
                <div class="filter interactive_dropdown" id="interactive_filter_blocking" data-filterkey="blocking" data-value="<?php echo $filter->getValue(); ?>" data-all-value="<?php echo __('Any'); ?>">
                    <input type="hidden" name="fs[blocking][o]" value="<?php echo $filter->getOperator(); ?>">
                    <input type="hidden" name="fs[blocking][v]" value="" id="filter_blocking_value_input">
                    <label><?php echo __('Blocker status'); ?></label>
                    <span class="value"><?php if (!$filter->hasValue()) echo __('Any'); ?></span>
                    <div class="interactive_menu">
                        <h1><?php echo __('Filter on blocker status'); ?></h1>
                        <div class="interactive_values_container">
                            <ul class="interactive_menu_values">
                                <li data-value="1" class="filtervalue <?php if ($filter->hasValue('1')) echo ' selected'; ?>" data-exclusive data-selection-group="1">
                                    <?php echo image_tag('icon-mono-checked.png', array('class' => 'checked')); ?>
                                    <input type="checkbox" value="1" name="filters_blocking_value" data-text="<?php echo __('Only blocker issues'); ?>" id="filters_blocking_value_yes" <?php if ($filter->hasValue('1')) echo 'checked'; ?>>
                                    <label for="filters_blocking_value_yes"><?php echo __('Only blocker issues'); ?></label>
                                </li>
                                <li data-value="0" class="filtervalue <?php if ($filter->hasValue('0')) echo ' selected'; ?>" data-exclusive data-selection-group="1">
                                    <?php echo image_tag('icon-mono-checked.png', array('class' => 'checked')); ?>
                                    <input type="checkbox" value="0" name="filters_blocking_value" data-text="<?php echo __('Not blocker issues'); ?>" id="filters_blocking_value_none" <?php if ($filter->hasValue('0')) echo 'checked'; ?>>
                                    <label for="filters_blocking_value_no"><?php echo __('Not blocker issues'); ?></label>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="filter_remove_button" onclick="TBG.Search.removeFilter($(this).up());"><?php echo image_tag('icon-mono-remove.png'); ?></div>
                </div>
                <?php
                break;
            case 'priority':
            case 'resolution':
            case 'reproducability':
            case 'severity':
                include_component('search/interactivefilter_choice', compact('filter'));
                break;
            case 'posted':
            case 'last_updated':
                include_component('search/interactivefilter_date', compact('filter'));
                break;
            default:
                if (!in_array($filter->getFilterKey(), \thebuggenie\core\entities\SearchFilter::getValidSearchFilters()))
                {
                    switch ($filter->getFilterType())
                    {
                        case \thebuggenie\core\entities\CustomDatatype::DATE_PICKER:
                            include_component('search/interactivefilter_date', compact('filter'));
                            break;
                        case \thebuggenie\core\entities\CustomDatatype::RADIO_CHOICE:
                        case \thebuggenie\core\entities\CustomDatatype::DROPDOWN_CHOICE_TEXT:
                            include_component('search/interactivefilter_choice', compact('filter'));
                            break;
                        case \thebuggenie\core\entities\CustomDatatype::COMPONENTS_CHOICE:
                        case \thebuggenie\core\entities\CustomDatatype::EDITIONS_CHOICE:
                        case \thebuggenie\core\entities\CustomDatatype::RELEASES_CHOICE:
                        case \thebuggenie\core\entities\CustomDatatype::MILESTONE_CHOICE:
                            include_component('search/interactivefilter_affected', compact('filter'));
                            break;
                        case \thebuggenie\core\entities\CustomDatatype::USER_CHOICE:
                            include_component('search/interactivefilter_user', compact('filter'));
                            break;
                        case \thebuggenie\core\entities\CustomDatatype::TEAM_CHOICE:
                            include_component('search/interactivefilter_team', compact('filter'));
                            break;
                        case \thebuggenie\core\entities\CustomDatatype::CLIENT_CHOICE:
                            include_component('search/interactivefilter_client', compact('filter'));
                            break;
                        case \thebuggenie\core\entities\CustomDatatype::INPUT_TEXT:
                        case \thebuggenie\core\entities\CustomDatatype::INPUT_TEXTAREA_MAIN:
                        case \thebuggenie\core\entities\CustomDatatype::INPUT_TEXTAREA_SMALL:
                            include_component('search/interactivefilter_text', compact('filter'));
                            break;
                    }
                }
        }

    ?>
<?php endif; ?>
