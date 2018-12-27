<?php use thebuggenie\core\entities\SearchFilter;

if ($filter instanceof SearchFilter): ?>
    <?php

        switch ($filter->getFilterKey())
        {
            case 'project_id':
                ?>
                <?php if (\thebuggenie\core\framework\Context::isProjectContext()): ?>
                    <input type="hidden" name="fs[project_id][o]" value="=">
                    <input type="hidden" name="fs[project_id][v]" value="<?= \thebuggenie\core\framework\Context::getCurrentProject()->getID(); ?>" id="filter_project_id_value_input">
                <?php else: ?>
                    <div class="filter interactive_dropdown" data-filterkey="project_id" data-value="<?= $filter->getValue(); ?>" data-all-value="<?= __('All'); ?>">
                        <input type="hidden" name="fs[project_id][o]" value="<?= $filter->getOperator(); ?>">
                        <input type="hidden" name="fs[project_id][v]" value="" id="filter_project_id_value_input">
                        <label><?= __('Project(s)'); ?></label>
                        <span class="value"><?php if (!$filter->hasValue()) echo __('All'); ?></span>
                        <div class="interactive_menu">
                            <h1><?= __('Choose issues from project(s)'); ?></h1>
                            <input type="search" class="interactive_menu_filter" placeholder="<?= __('Filter values'); ?>">
                            <div class="interactive_values_container">
                                <ul class="interactive_menu_values">
                                    <?php foreach ($filter->getAvailableValues() as $project): ?>
                                        <li data-value="<?= $project->getID(); ?>" class="filtervalue<?php if ($filter->hasValue($project->getID())) echo ' selected'; ?>">
                                            <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
                                            <?= image_tag($project->getSmallIconName(), array('class' => 'icon'), $project->hasSmallIcon()); ?>
                                            <input type="checkbox" value="<?= $project->getID(); ?>" name="filters_project_id_value_<?= $project->getID(); ?>" data-text="<?= $project->getName(); ?>" id="filters_project_id_value_<?= $project->getID(); ?>" <?php if ($filter->hasValue($project->getID())) echo 'checked'; ?>>
                                            <label for="filters_project_id_value_<?= $project->getID(); ?>"><?= $project->getName(); ?></label>
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
                <div class="filter interactive_dropdown" data-filterkey="issuetype" data-value="<?= $filter->getValue(); ?>" data-all-value="<?= __('All'); ?>">
                    <input type="hidden" name="fs[issuetype][o]" value="<?= $filter->getOperator(); ?>">
                    <input type="hidden" name="fs[issuetype][v]" value="" id="filter_issuetype_value_input">
                    <label><?= __('Issuetype'); ?></label>
                    <span class="value"><?php if (!$filter->hasValue()) echo __('All'); ?></span>
                    <div class="interactive_menu">
                        <h1><?= __('Filter on issuetype'); ?></h1>
                        <input type="search" class="interactive_menu_filter" placeholder="<?= __('Filter values'); ?>">
                        <div class="interactive_values_container">
                            <ul class="interactive_menu_values">
                                <?php foreach ($filter->getAvailableValues() as $issuetype): ?>
                                    <?php /** @var \thebuggenie\core\entities\Issuetype $issuetype */ ?>
                                    <li data-value="<?= $issuetype->getID(); ?>" class="filtervalue<?php if ($filter->hasValue($issuetype->getID())) echo ' selected'; ?>">
                                        <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
                                        <input type="checkbox" value="<?= $issuetype->getID(); ?>" name="filters_issuetype_value_<?= $issuetype->getID(); ?>" data-text="<?= __($issuetype->getName()); ?>" id="filters_issuetype_value_<?= $issuetype->getID(); ?>" <?php if ($filter->hasValue($issuetype->getID())) echo 'checked'; ?>>
                                        <label for="filters_issuetype_value_<?= $issuetype->getID(); ?>"><?= fa_image_tag($issuetype->getFontAwesomeIcon(), ['class' => 'issuetype-icon issuetype-' . $issuetype->getIcon()]) . __($issuetype->getName()); ?></label>
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
                <div class="filter interactive_dropdown" id="interactive_filter_subprojects" data-filterkey="subprojects" data-value="<?= $filter->getValue(); ?>" data-all-value="<?= __('All'); ?>">
                    <input type="hidden" name="fs[subprojects][o]" value="<?= $filter->getOperator(); ?>">
                    <input type="hidden" name="fs[subprojects][v]" value="" id="filter_subprojects_value_input">
                    <label><?= __('Subproject(s)'); ?></label>
                    <span class="value"><?php if (!$filter->hasValue()) echo __('All'); ?></span>
                    <div class="interactive_menu">
                        <h1><?= __('Include issues from subproject(s)'); ?></h1>
                        <input type="search" class="interactive_menu_filter" placeholder="<?= __('Filter values'); ?>">
                        <div class="interactive_values_container">
                            <ul class="interactive_menu_values">
                                <li data-value="all" class="filtervalue <?php if ($filter->hasValue('all')) echo ' selected'; ?>" data-exclusive data-selection-group="1" data-exclude-group="2">
                                    <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
                                    <input type="checkbox" value="all" name="filters_subprojects_value_exclusive_all" data-text="<?= __('All'); ?>" id="filters_subprojects_value_all" <?php if ($filter->hasValue('all')) echo 'checked'; ?>>
                                    <label for="filters_subprojects_value_all"><?= __('All'); ?></label>
                                </li>
                                <li data-value="none" class="filtervalue <?php if ($filter->hasValue('none')) echo ' selected'; ?>" data-exclusive data-selection-group="1" data-exclude-group="2">
                                    <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
                                    <input type="checkbox" value="none" name="filters_subprojects_value_exclusive_none" data-text="<?= __('None'); ?>" id="filters_subprojects_value_none" <?php if ($filter->hasValue('none')) echo 'checked'; ?>>
                                    <label for="filters_subprojects_value_none"><?= __('None'); ?></label>
                                </li>
                                <li class="separator"></li>
                                <?php foreach ($filter->getAvailableValues() as $subproject): ?>
                                    <li data-value="<?= $subproject->getID(); ?>" class="filtervalue<?php if ($filter->hasValue($subproject->getID())) echo ' selected'; ?>" data-selection-group="2" data-exclude-group="1">
                                        <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
                                        <input type="checkbox" value="<?= $subproject->getID(); ?>" name="filters_subprojects_value_<?= $subproject->getID(); ?>" data-text="<?= __($subproject->getName()); ?>" id="filters_subprojects_value_<?= $subproject->getID(); ?>" <?php if ($filter->hasValue($subproject->getID())) echo 'checked'; ?>>
                                        <label for="filters_subprojects_value_<?= $subproject->getID(); ?>"><?= $subproject->getName(); ?>&nbsp;&nbsp;<span class="faded_out"><?= $subproject->getKey(); ?></span></label>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                    <div class="filter_remove_button" onclick="TBG.Search.removeFilter($(this).up());"><?= fa_image_tag('times'); ?></div>
                </div>
                <?php
                break;
            case 'blocking':
                ?>
                <div class="filter interactive_dropdown" id="interactive_filter_blocking" data-filterkey="blocking" data-value="<?= $filter->getValue(); ?>" data-all-value="<?= __('Any'); ?>">
                    <input type="hidden" name="fs[blocking][o]" value="<?= $filter->getOperator(); ?>">
                    <input type="hidden" name="fs[blocking][v]" value="" id="filter_blocking_value_input">
                    <label><?= __('Blocker status'); ?></label>
                    <span class="value"><?php if (!$filter->hasValue()) echo __('Any'); ?></span>
                    <div class="interactive_menu">
                        <h1><?= __('Filter on blocker status'); ?></h1>
                        <div class="interactive_values_container">
                            <ul class="interactive_menu_values">
                                <li data-value="1" class="filtervalue <?php if ($filter->hasValue('1')) echo ' selected'; ?>" data-exclusive data-selection-group="1">
                                    <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
                                    <input type="checkbox" value="1" name="filters_blocking_value" data-text="<?= __('Only blocker issues'); ?>" id="filters_blocking_value_yes" <?php if ($filter->hasValue('1')) echo 'checked'; ?>>
                                    <label for="filters_blocking_value_yes"><?= __('Only blocker issues'); ?></label>
                                </li>
                                <li data-value="0" class="filtervalue <?php if ($filter->hasValue('0')) echo ' selected'; ?>" data-exclusive data-selection-group="1">
                                    <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
                                    <input type="checkbox" value="0" name="filters_blocking_value" data-text="<?= __('Not blocker issues'); ?>" id="filters_blocking_value_none" <?php if ($filter->hasValue('0')) echo 'checked'; ?>>
                                    <label for="filters_blocking_value_no"><?= __('Not blocker issues'); ?></label>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="filter_remove_button" onclick="TBG.Search.removeFilter($(this).up());"><?= fa_image_tag('times'); ?></div>
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
            case 'time_spent':
                include_component('search/interactivefilter_date', compact('filter'));
                break;
            case 'relation':
                ?>
                <div class="filter interactive_dropdown" id="interactive_filter_relation" data-filterkey="relation" data-value="<?= $filter->getValue(); ?>" data-all-value="<?= __('Any'); ?>">
                    <input type="hidden" name="fs[relation][o]" value="<?= $filter->getOperator(); ?>">
                    <input type="hidden" name="fs[relation][v]" value="" id="filter_relation_value_input">
                    <label><?= __('Relation'); ?></label>
                    <span class="value"><?php if (!$filter->hasValue()) echo __('Any'); ?></span>
                    <div class="interactive_menu">
                        <h1><?= __('Filter on relation'); ?></h1>
                        <div class="interactive_values_container">
                            <ul class="interactive_menu_values">
                                <li data-value="<?= SearchFilter::FILTER_RELATION_ONLY_CHILD; ?>" class="filtervalue <?php if ($filter->hasValue(SearchFilter::FILTER_RELATION_ONLY_CHILD)) echo ' selected'; ?>" data-exclusive data-selection-group="1">
                                    <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
                                    <input type="checkbox" value="<?= SearchFilter::FILTER_RELATION_ONLY_CHILD; ?>" name="filters_relation_value" data-text="<?= __('Only child issues'); ?>" id="filters_relation_value_yes" <?php if ($filter->hasValue(SearchFilter::FILTER_RELATION_ONLY_CHILD)) echo 'checked'; ?>>
                                    <label for="filters_relation_value_yes"><?= __('Only child issues'); ?></label>
                                </li>
                                <li data-value="<?= SearchFilter::FILTER_RELATION_WITHOUT_CHILD; ?>" class="filtervalue <?php if ($filter->hasValue(SearchFilter::FILTER_RELATION_WITHOUT_CHILD)) echo ' selected'; ?>" data-exclusive data-selection-group="1">
                                    <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
                                    <input type="checkbox" value="<?= SearchFilter::FILTER_RELATION_WITHOUT_CHILD; ?>" name="filters_relation_value" data-text="<?= __('Without child issues'); ?>" id="filters_relation_value_yes" <?php if ($filter->hasValue(SearchFilter::FILTER_RELATION_WITHOUT_CHILD)) echo 'checked'; ?>>
                                    <label for="filters_relation_value_yes"><?= __('Without child issues'); ?></label>
                                </li>
                                <li data-value="<?= SearchFilter::FILTER_RELATION_ONLY_PARENT; ?>" class="filtervalue <?php if ($filter->hasValue(SearchFilter::FILTER_RELATION_ONLY_PARENT)) echo ' selected'; ?>" data-exclusive data-selection-group="1">
                                    <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
                                    <input type="checkbox" value="<?= SearchFilter::FILTER_RELATION_ONLY_PARENT; ?>" name="filters_relation_value" data-text="<?= __('Only parent issues'); ?>" id="filters_relation_value_yes" <?php if ($filter->hasValue(SearchFilter::FILTER_RELATION_ONLY_PARENT)) echo 'checked'; ?>>
                                    <label for="filters_relation_value_yes"><?= __('Only parent issues'); ?></label>
                                </li>
                                <li data-value="<?= SearchFilter::FILTER_RELATION_WITHOUT_PARENT; ?>" class="filtervalue <?php if ($filter->hasValue(SearchFilter::FILTER_RELATION_WITHOUT_PARENT)) echo ' selected'; ?>" data-exclusive data-selection-group="1">
                                    <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
                                    <input type="checkbox" value="<?= SearchFilter::FILTER_RELATION_WITHOUT_PARENT; ?>" name="filters_relation_value" data-text="<?= __('Without parent issues'); ?>" id="filters_relation_value_none" <?php if ($filter->hasValue(SearchFilter::FILTER_RELATION_WITHOUT_PARENT)) echo 'checked'; ?>>
                                    <label for="filters_relation_value_no"><?= __('Without parent issues'); ?></label>
                                </li>
                                <li data-value="<?= SearchFilter::FILTER_RELATION_NEITHER_CHILD_NOR_PARENT; ?>" class="filtervalue <?php if ($filter->hasValue(SearchFilter::FILTER_RELATION_NEITHER_CHILD_NOR_PARENT)) echo ' selected'; ?>" data-exclusive data-selection-group="1">
                                    <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
                                    <input type="checkbox" value="<?= SearchFilter::FILTER_RELATION_NEITHER_CHILD_NOR_PARENT; ?>" name="filters_relation_value" data-text="<?= __('Neither child nor parent issues'); ?>" id="filters_relation_value_yes" <?php if ($filter->hasValue(SearchFilter::FILTER_RELATION_NEITHER_CHILD_NOR_PARENT)) echo 'checked'; ?>>
                                    <label for="filters_relation_value_yes"><?= __('Neither child nor parent issues'); ?></label>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="filter_remove_button" onclick="TBG.Search.removeFilter($(this).up());"><?= image_tag('icon-mono-remove.png'); ?></div>
                </div>
                <?php
                break;
            default:
                if (!in_array($filter->getFilterKey(), SearchFilter::getValidSearchFilters()))
                {
                    switch ($filter->getFilterType())
                    {
                        case \thebuggenie\core\entities\CustomDatatype::DATE_PICKER:
                        case \thebuggenie\core\entities\CustomDatatype::DATETIME_PICKER:
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
