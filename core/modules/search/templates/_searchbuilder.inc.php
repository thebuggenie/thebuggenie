<?php

    $tbg_response->addJavascript('calendarview');

?>
<div class="interactive_searchbuilder" id="search_builder">
    <form accept-charset="<?php echo \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>" action="<?php echo (\thebuggenie\core\framework\Context::isProjectContext()) ? make_url('project_search_paginated', array('project_key' => \thebuggenie\core\framework\Context::getCurrentProject()->getKey())) : make_url('search_paginated'); ?>" method="get" id="find_issues_form" <?php if ($show_results): ?>data-results-loaded<?php endif; ?> <?php if ($search_object->getID()): ?>data-is-saved<?php endif; ?> data-history-url="<?php echo (\thebuggenie\core\framework\Context::isProjectContext()) ? make_url('project_issues', array('project_key' => \thebuggenie\core\framework\Context::getCurrentProject()->getKey())) : make_url('search'); ?>" data-dynamic-callback-url="<?php echo make_url('search_filter_getdynamicchoices'); ?>" onsubmit="TBG.Search.liveUpdate(true);return false;">
        <div class="searchbuilder_filterstrip" id="searchbuilder_filterstrip">
            <?php include_component('search/interactivefilter', array('filter' => $search_object->getFilter('project_id'))); ?>
            <?php include_component('search/interactivefilter', array('filter' => $search_object->getFilter('issuetype'))); ?>
            <?php include_component('search/interactivefilter', array('filter' => $search_object->getFilter('status'))); ?>
            <?php include_component('search/interactivefilter', array('filter' => $search_object->getFilter('category'))); ?>
            <input type="hidden" name="sortfields" value="<?php echo $search_object->getSortFieldsAsString(); ?>" id="search_sortfields_input">
            <input type="hidden" name="fs[text][o]" value="=">
            <input type="search" name="fs[text][v]" id="interactive_filter_text" value="<?php echo htmlentities($search_object->getSearchTerm(), ENT_QUOTES, \thebuggenie\core\framework\Context::getI18n()->getCharset()); ?>" class="filter_searchfield" placeholder="<?php echo __('Enter a search term here'); ?>">
            <div class="interactive_plus_container" id="interactive_filters_availablefilters_container">
                <div class="interactive_plus_button" id="interactive_plus_button"><?php echo image_tag('icon-mono-add.png'); ?></div>
                <div class="interactive_filters_list <?php echo (count($nondatecustomfields)) ? 'three_columns' : 'two_columns'; ?>">
                    <div class="column">
                        <h1><?php echo __('People filters'); ?></h1>
                        <ul>
                            <li data-filter="posted_by" id="additional_filter_posted_by_link"><?php echo __('Posted by user'); ?></li>
                            <li data-filter="assignee_user" id="additional_filter_assignee_user_link"><?php echo __('Assigned to user'); ?></li>
                            <li data-filter="assignee_team" id="additional_filter_assignee_team_link"><?php echo __('Assigned to team'); ?></li>
                            <li data-filter="owner_user" id="additional_filter_owner_user_link"><?php echo __('Owned by user'); ?></li>
                            <li data-filter="owner_team" id="additional_filter_owner_team_link"><?php echo __('Owned by team'); ?></li>
                        </ul>
                        <h1><?php echo __('Time filters'); ?></h1>
                        <ul>
                            <li data-filter="posted" id="additional_filter_posted_link"><?php echo __('Created before / after'); ?></li>
                            <li data-filter="last_updated" id="additional_filter_last_updated_link"><?php echo __('Last updated before / after'); ?></li>
                            <?php foreach ($datecustomfields as $field): ?>
                                <li data-filter="<?php echo $field->getKey(); ?>" id="additional_filter_<?php echo $field->getKey(); ?>_link"><?php echo __($field->getDescription()); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <div class="column">
                        <h1><?php echo __('Project detail filters'); ?></h1>
                        <ul>
                            <?php if (\thebuggenie\core\framework\Context::isProjectContext()): ?>
                                <li data-filter="subprojects" id="additional_filter_subprojects_link"><?php echo __('Including subproject(s)'); ?></li>
                            <?php else: ?>
                                <li class="disabled">
                                    <?php echo __('Including subproject(s)'); ?>
                                    <div class="tooltip from-above leftie"><?php echo __('This filter is only available in project context'); ?></div>
                                </li>
                            <?php endif; ?>
                            <li data-filter="build" id="additional_filter_build_link"><?php echo __('Reported against a specific release'); ?></li>
                            <li data-filter="component" id="additional_filter_component_link"><?php echo __('Affecting a specific component'); ?></li>
                            <li data-filter="edition" id="additional_filter_edition_link"><?php echo __('Affecting a specific edition'); ?></li>
                            <li data-filter="milestone" id="additional_filter_milestone_link"><?php echo __('Targetting a specific milestone'); ?></li>
                        </ul>
                        <h1><?php echo __('Issue detail filters'); ?></h1>
                        <ul>
                            <li data-filter="priority" id="additional_filter_priority_link"><?php echo __('Priority'); ?></li>
                            <li data-filter="severity" id="additional_filter_severity_link"><?php echo __('Severity'); ?></li>
                            <li data-filter="resolution" id="additional_filter_resolution_link"><?php echo __('Resolution'); ?></li>
                            <li data-filter="reproducability" id="additional_filter_reproducability_link"><?php echo __('Reproducability'); ?></li>
                            <li data-filter="blocking" id="additional_filter_blocking_link"><?php echo __('Blocker status'); ?></li>
                        </ul>
                    </div>
                    <?php if (count($nondatecustomfields)): ?>
                        <div class="column">
                            <h1><?php echo __('Other filters'); ?></h1>
                            <ul>
                                <?php foreach ($nondatecustomfields as $field): ?>
                                    <li data-filter="<?php echo $field->getKey(); ?>" id="additional_filter_<?php echo $field->getKey(); ?>_link"><?php echo __($field->getDescription()); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div style="display: inline-block; position: relative;">
                <input type="image" src="<?php echo image_url('icon-mono-search.png'); ?>">
                <div class="tooltip from-above rightie" style="margin: 10px -25px 0 0; left: auto; right: 0;transition-delay: 1s;">
                    <?php echo __("Press the search button to trigger a search if it doesn't happen automatically"); ?>
                </div>
            </div>
            <div class="interactive_plus_container" id="interactive_settings_container">
                <div class="interactive_plus_button" id="interactive_grouping_button"><?php echo image_tag('icon-mono-grouping.png'); ?></div>
                <div class="interactive_menu" id="search_columns_container" data-url="<?php echo make_url('search_save_column_settings'); ?>">
                    <div id="search_column_settings_container" class="column">
                        <h1><?php echo __('Select columns to show'); ?></h1>
                        <input type="hidden" name="scs_current_template" value="" id="scs_current_template">
                        <input type="search" class="interactive_menu_filter" placeholder="<?php echo __('Filter values'); ?>">
                        <div class="interactive_values_container">
                            <ul class="interactive_menu_values">
                                <?php foreach ($columns as $c_key => $c_name): ?>
                                    <li data-value="<?php echo $c_key; ?>" class="search_column filtervalue unfiltered scs_<?php echo $c_key; ?>" id="search_column_<?php echo $c_key; ?>_toggler">
                                        <?php echo image_tag('icon-mono-checked.png', array('class' => 'checked')); ?>
                                        <input id="search_column_<?php echo $c_key; ?>_toggler_checkbox" type="checkbox" value="<?php echo $c_key; ?>" name="columns[<?php echo $c_key; ?>]" data-text="<?php echo $c_name; ?>">
                                        <?php echo $c_name; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php if (!$tbg_user->isGuest()): ?>
                            <div style="padding: 5px;">
                                <div id="search_column_settings_indicator" style="display: none; float: right; margin: 7px 5px 0 10px;"><?php echo image_tag('spinning_20.gif'); ?></div>
                                <?php echo javascript_link_tag(__('Reset columns'), array('onclick' => 'TBG.Search.resetColumns();return false;')); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="interactive_plus_button" id="interactive_template_button"><?php echo image_tag('icon-mono-settings.png'); ?></div>
                <div class="interactive_filters_list interactive_menu two_columns wide">
                    <div class="column">
                        <h1><?php echo __('Select how to present search results'); ?></h1>
                        <input type="hidden" name="template" id="filter_selected_template" value="<?php echo $search_object->getTemplateName(); ?>">
                        <div class="search_template_list">
                            <ul>
                                <?php foreach ($templates as $template_name => $template_details): ?>
                                    <li data-template-name="<?php echo $template_name; ?>" data-parameter="<?php echo (int) $template_details['parameter']; ?>" data-parameter-text="<?php echo ($template_details['parameter']) ? __e($template_details['parameter_text']) : ''; ?>" data-grouping="<?php echo (int) $template_details['grouping']; ?>" class="template-picker <?php if ($template_name == $search_object->getTemplateName()) echo 'selected'; ?>">
                                        <?php echo image_tag('search_template_'.$template_name.'.png'); ?>
                                        <h1><?php echo $template_details['title']; ?></h1>
                                        <?php echo $template_details['description']; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                    <div class="column <?php if (!$templates[$search_object->getTemplateName()]['grouping']) echo 'nogrouping'; ?> <?php if ($templates[$search_object->getTemplateName()]['parameter']) echo 'parameter'; ?>" id="search_grouping_container">
                        <h1><?php echo __('Search result grouping'); ?></h1>
                        <div class="nogrouping">
                            <?php echo __('This search template does not support grouping'); ?>
                        </div>
                        <div class="parameterdetails">
                            <h1><?php echo __('Special search parameters'); ?></h1>
                            <label for="search_filter_parameter_input" id="search_filter_parameter_description"><?php echo ($templates[$search_object->getTemplateName()]['parameter']) ? $templates[$search_object->getTemplateName()]['parameter_text'] : ''; ?></label>
                            <input type="text" id="search_filter_parameter_input" class="interactive_menu_filter filter_searchfield" data-maxlength="0" placeholder="" value="<?php echo $search_object->getTemplateParameter(); ?>" name="template_parameter">
                        </div>
                        <div class="groupingdetails">
                            <input type="search" class="interactive_menu_filter" placeholder="<?php echo __('Filter values'); ?>">
                            <div class="interactive_values_container">
                                <ul class="interactive_menu_values" id="filter_grouping_options">
                                    <?php foreach (array('asc' => __('Ascending'), 'desc' => __('Descending')) as $dir => $dir_desc): ?>
                                        <li data-sort-order="<?php echo $dir; ?>" data-value="<?php echo $dir; ?>" class="grouporder filtervalue sticky unfiltered <?php if ($search_object->getGrouporder() == $dir) echo 'selected'; ?>" data-exclusive data-selection-group="1" style="<?php if (!$search_object->getGroupby()) echo 'display: none;'; ?>">
                                            <?php echo image_tag('icon-mono-checked.png', array('class' => 'checked')); ?>
                                            <input type="radio" value="<?php echo $dir; ?>" name="grouporder" data-text="<?php echo $dir_desc; ?>" id="search_grouping_grouporder_<?php echo $dir; ?>" <?php if ($search_object->getGrouporder() == $dir) echo 'checked'; ?>>
                                            <?php echo $dir_desc; ?>
                                        </li>
                                    <?php endforeach; ?>
                                    <li class="separator"></li>
                                    <li data-groupby="" data-value="" class="groupby filtervalue unfiltered <?php if (!$search_object->getGroupby()) echo 'selected'; ?>" data-exclusive data-selection-group="2">
                                        <?php echo image_tag('icon-mono-checked.png', array('class' => 'checked')); ?>
                                        <input type="radio" value="" name="groupby" data-text="<?php echo __('No grouping'); ?>" id="search_grouping_none" <?php if (!$search_object->getGroupby()) echo 'checked'; ?>>
                                        <?php echo __('No grouping'); ?>
                                    </li>
                                    <?php foreach ($groupoptions as $grouping => $group_desc): ?>
                                        <li data-groupby="<?php echo $grouping; ?>" data-value="<?php echo $grouping; ?>" class="groupby filtervalue unfiltered <?php if ($search_object->getGroupby() == $grouping) echo 'selected'; ?>" data-exclusive data-selection-group="2">
                                            <?php echo image_tag('icon-mono-checked.png', array('class' => 'checked')); ?>
                                            <input type="radio" value="<?php echo $grouping; ?>" name="groupby" data-text="<?php echo $group_desc; ?>" id="search_grouping_groupby_<?php echo $grouping; ?>" <?php if ($search_object->getGroupby() == $grouping) echo 'checked'; ?>>
                                            <?php echo $group_desc; ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <h1><?php echo __('Select how many issues to show per page'); ?></h1>
                    <input type="hidden" name="issues_per_page" id="filter_issues_per_page" value="<?php echo $search_object->getIssuesPerPage(); ?>">
                    <div class="slider_container">
                        <div class="slider" id="issues_per_page_slider">
                            <div class="handle" id="issues_per_page_handle"></div>
                        </div>
                        <div id="issues_per_page_slider_value" class="slider_value"><?php echo $search_object->getIssuesPerPage(); ?></div>
                    </div>
                </div>
                <?php if (!$tbg_user->isGuest()): ?>
                    <div class="interactive_plus_button" id="interactive_save_button" style="<?php if (!$show_results) echo 'display: none;'; ?>"><?php echo image_tag('icon-mono-bookmark.png'); ?></div>
                    <div class="interactive_filters_list interactive_menu">
                        <h1><?php echo __('Save or download search results'); ?></h1>
                        <div class="interactive_values_container">
                            <ul class="interactive_menu_values" id="filter_export_options">
                                <li onclick="$('saved_search_details').toggle();"><?php echo image_tag('icon_savedsearch.png', array('class' => 'icon')) . __('Save search filters'); ?></li>
                                <li onclick="TBG.Search.download('ods');">
                                    <?php echo image_tag('icon_ods.png', array('class' => 'icon')) . __('Download as OpenDocument spreadsheet (.ods)'); ?>
                                </li>
                                <li onclick="TBG.Search.download('xlsx');">
                                    <?php echo image_tag('icon_xlsx.png', array('class' => 'icon')) . __('Download as Microsoft Excel spreadsheet (.xlsx)'); ?>
                                </li>
                                <li onclick="TBG.Search.download('rss');">
                                    <?php echo image_tag('icon_rss.png', array('class' => 'icon')) . __('Download as RSS feed'); ?>
                                </li>
                            </ul>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="interactive_plus_button disabled" id="interactive_save_button" style="<?php if (!$show_results) echo 'display: none;'; ?>">
                        <div class="tooltip from-above rightie" style="right: -5px; left: auto; margin-top: 10px;"><?php echo __('You have to be signed in to save this search'); ?></div>
                        <?php echo image_tag('icon-mono-bookmark.png', array('style' => 'opacity: 0.4;')); ?>
                    </div>
                <?php endif; ?>
            </div>
            <div id="searchbuilder_filterstrip_filtercontainer">
                <?php foreach ($search_object->getFilters() as $filter): ?>
                    <?php if (in_array($filter->getFilterKey(), array('project_id', 'status', 'issuetype', 'category', 'text'))) continue; ?>
                    <?php include_component('search/interactivefilter', compact('filter')); ?>
                <?php endforeach; ?>
            </div>
        </div>
    </form>
    <div id="searchbuilder_filter_hiddencontainer" style="display: none;">
        <?php if (\thebuggenie\core\framework\Context::isProjectContext()): ?>
            <?php if (!$search_object->hasFilter('subprojects')) include_component('search/interactivefilter', array('filter' => \thebuggenie\core\entities\SearchFilter::createFilter('subprojects'))); ?>
        <?php endif; ?>
        <?php foreach (array('priority', 'severity', 'reproducability', 'resolution', 'posted_by', 'assignee_user', 'assignee_team', 'owner_user', 'owner_team', 'milestone', 'edition', 'component', 'build', 'blocking') as $key): ?>
            <?php if (!$search_object->hasFilter($key)) include_component('search/interactivefilter', array('filter' => \thebuggenie\core\entities\SearchFilter::createFilter($key))); ?>
        <?php endforeach; ?>
        <?php foreach (array('posted', 'last_updated') as $key): ?>
            <?php if (!$search_object->hasFilter($key)) include_component('search/interactivefilter', array('filter' => \thebuggenie\core\entities\SearchFilter::createFilter($key, array('operator' => '<=', 'value' => time())))); ?>
        <?php endforeach; ?>
        <?php foreach ($nondatecustomfields as $customtype): ?>
            <?php if ($customtype->getType() == \thebuggenie\core\entities\CustomDatatype::DATE_PICKER) continue; ?>
            <?php if (!$search_object->hasFilter($customtype->getKey())) include_component('search/interactivefilter', array('filter' => \thebuggenie\core\entities\SearchFilter::createFilter($customtype->getKey()))); ?>
        <?php endforeach; ?>
        <?php foreach ($datecustomfields as $customtype): ?>
            <?php if (!$search_object->hasFilter($customtype->getKey())) include_component('search/interactivefilter', array('filter' => \thebuggenie\core\entities\SearchFilter::createFilter($customtype->getKey(), array('operator' => '<=', 'value' => time())))); ?>
        <?php endforeach; ?>
    </div>
    <?php if (!$tbg_user->isGuest()): ?>
        <div class="fullpage_backdrop" style="display: none;" id="saved_search_details">
            <div class="backdrop_box large">
                <div class="backdrop_detail_header"><?php echo __('Save this search'); ?></div>
                <div class="backdrop_detail_content">
                    <form id="save_search_form" action="<?php echo make_url('search_save'); ?>" method="post" onsubmit="TBG.Search.saveSearch();return false;">
                        <?php if (\thebuggenie\core\framework\Context::isProjectContext()): ?>
                            <input type="hidden" name="project_id" value="<?php echo \thebuggenie\core\framework\Context::getCurrentProject()->getID(); ?>">
                            <p style="padding-bottom: 15px;" class="faded_out"><?php echo __('This saved search will be available under this project only. To make a non-project-specific search, use the main "%find_issues" page instead', array('%find_issues' => link_tag(make_url('search'), __('Find issues')))); ?></p>
                        <?php endif; ?>
                        <?php if ($search_object->getID()): ?>
                            <input type="hidden" name="saved_search_id" id="saved_search_id" value="<?php echo $search_object->getID(); ?>">
                        <?php endif; ?>
                        <table class="padded_table" style="width: 780px;">
                            <tr>
                                <td style="vertical-align: top; width: 200px; font-size: 1.15em;"><label for="saved_search_name"><?php echo __('Saved search name'); ?></label></td>
                                <td style="vertical-align: top;">
                                    <input type="text" name="name" id="saved_search_name"<?php if ($search_object->getID()): ?> value="<?php echo $search_object->getName(); ?>"<?php endif; ?> style="width: 576px; font-size: 1.2em; padding: 4px;">
                                    <?php if ($search_object->getID()): ?>
                                        <br>
                                        <input type="checkbox" id="update_saved_search" name="update_saved_search" checked><label style="font-size: 1em; font-weight: normal;" for="update_saved_search"><?php echo __('Update this saved search'); ?></label>
                                    <?php endif; ?>

                                </td>
                            </tr>
                            <tr>
                                <td><label for="saved_search_description" class="optional"><?php echo __('Description'); ?></label></td>
                                <td><input type="text" name="description" id="saved_search_description"<?php if ($search_object->getID()): ?> value="<?php echo $search_object->getDescription(); ?>"<?php endif; ?> style="width: 350px;"><br></td>
                            </tr>
                            <?php if ($tbg_user->canCreatePublicSearches()): ?>
                                <tr>
                                    <td><label for="saved_search_public" class="optional"><?php echo __('Visibility'); ?></label></td>
                                    <td>
                                        <select name="is_public" id="saved_search_public">
                                            <option value="0"<?php if ($search_object->getID() && !$search_object->isPublic()): ?> selected<?php endif; ?>><?php echo __('Only visible for me'); ?></option>
                                            <option value="1"<?php if ($search_object->getID() && $search_object->isPublic()): ?> selected<?php endif; ?>><?php echo __('Shared with others'); ?></option>
                                        </select>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </table>
                        <div style="text-align: right;">
                            <?php echo image_tag('spinning_16.gif', array('style' => 'margin-left: 5px; display: none;', 'id' => 'save_search_indicator')); ?>
                            <?php echo __('%cancel or %save_search', array('%save_search' => '<input type="submit" value="'.__('Save search').'">', '%cancel' => '<a href="javascript:void(0);" onclick="$(\'saved_search_details\').hide();">'.__('cancel').'</b></a>')); ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
