<div class="interactive_searchbuilder" id="search_builder">
	<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="<?php echo (TBGContext::isProjectContext()) ? make_url('project_search_paginated', array('project_key' => TBGContext::getCurrentProject()->getKey())) : make_url('search_paginated'); ?>" method="get" id="find_issues_form" <?php if ($show_results): ?>data-results-loaded<?php endif; ?> data-dynamic-callback-url="<?php echo make_url('search_filter_getdynamicchoices'); ?>" onsubmit="TBG.Search.liveUpdate(true);return false;">
		<div class="searchbuilder_filterstrip" id="searchbuilder_filterstrip">
			<?php include_component('search/interactivefilter', array('filter' => $search_object->getFilter('project_id'))); ?>
			<?php include_component('search/interactivefilter', array('filter' => $search_object->getFilter('issuetype'))); ?>
			<?php include_component('search/interactivefilter', array('filter' => $search_object->getFilter('status'))); ?>
			<?php include_component('search/interactivefilter', array('filter' => $search_object->getFilter('category'))); ?>
			<input type="hidden" name="filters[text][operator]" value="=">
			<input type="search" name="filters[text][value]" id="interactive_filter_text" value="<?php echo $search_object->getSearchTerm(); ?>" class="filter_searchfield" placeholder="<?php echo __('Enter a search term here'); ?>">
			<div class="interactive_plus_container" id="interactive_filters_availablefilters_container">
				<div class="interactive_plus_button" id="interactive_plus_button"><?php echo image_tag('icon-mono-add.png'); ?></div>
				<div class="interactive_filters_list two_columns">
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
							<li class="disabled"><?php echo __('Created before'); ?></li>
							<li class="disabled"><?php echo __('Created after'); ?></li>
							<li class="disabled"><?php echo __('Last updated before'); ?></li>
							<li class="disabled"><?php echo __('Last updated after'); ?></li>
						</ul>
					</div>
					<div class="column">
						<h1><?php echo __('Project detail filters'); ?></h1>
						<ul>
							<?php if (TBGContext::isProjectContext()): ?>
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
						</ul>
					</div>
					<input type="hidden" name="issues_per_page" value="50">
				</div>
			</div>
			<div class="interactive_plus_container" id="interactive_settings_container">
				<div class="interactive_plus_button" id="interactive_template_button"><?php echo image_tag('icon-mono-settings.png'); ?></div>
				<div class="interactive_filters_list two_columns wide">
					<h1><?php echo __('Select how to present search results'); ?></h1>
					<input type="hidden" name="template" id="filter_selected_template" value="<?php echo $search_object->getTemplateName(); ?>">
					<div class="search_template_list">
						<ul>
							<?php foreach ($templates as $template_name => $template_details): ?>
								<li data-template-name="<?php echo $template_name; ?>" class="template-picker <?php if ($template_name == $search_object->getTemplateName()) echo 'selected'; ?>">
									<?php echo image_tag('search_template_'.$template_name.'.png'); ?>
									<h1><?php echo $template_details['title']; ?></h1>
									<?php echo $template_details['description']; ?>
								</li>
							<?php endforeach; ?>
						</ul>
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
					<div class="interactive_plus_button" id="interactive_save_button" style="<?php if (!$show_results) echo 'display: none;'; ?>" onclick="$('saved_search_details').toggle();"><?php echo image_tag('icon-mono-bookmark.png'); ?></div>
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
		<?php /*<div style="padding-top: 5px;" id="search_filters">
			<ul id="search_filters_list">
				<li>
					<label for="issues_searchfor"><?php echo __('Search for (text)'); ?></label>
					<select name="filters[text][operator]">
						<option value="="<?php if (array_key_exists('text', $appliedfilters) && ((array_key_exists('operator', $appliedfilters['text']) && $appliedfilters['text']['operator'] == '=') || (!array_key_exists('operator', $appliedfilters['text']) && $appliedfilters['text'][0]['operator'] == '='))): ?> selected<?php endif; ?>><?php echo __('Issues containing'); ?></option>
						<option value="!="<?php if (array_key_exists('text', $appliedfilters) && ((array_key_exists('operator', $appliedfilters['text']) && $appliedfilters['text']['operator'] == '!=') || (!array_key_exists('operator', $appliedfilters['text']) && $appliedfilters['text'][0]['operator'] == '!='))): ?> selected<?php endif; ?>><?php echo __('Issues not containing'); ?></option>
					</select>
					<input type="text" name="filters[text][value]" value="<?php echo $searchterm; ?>" id="issues_searchfor" style="width: 450px;" placeholder="<?php echo __('Leave this input field blank to list all issues based on filters below'); ?>">
				</li>
				<?php foreach ($appliedfilters as $filter => $filter_info): ?>
					<?php if (array_key_exists('value', $filter_info) && $filter != 'text'): ?>
						<?php include_component('search/filter', array('filter' => $filter, 'selected_operator' => $filter_info['operator'], 'selected_value' => $filter_info['value'], 'key' => 0)); ?>
					<?php elseif ($filter != 'text'): ?>
						<?php foreach ($filter_info as $k => $single_filter): ?>
							<?php include_component('search/filter', array('filter' => $filter, 'selected_operator' => $single_filter['operator'], 'selected_value' => $single_filter['value'], 'filter_info' => $filter_info, 'key' => $k)); ?>
						<?php endforeach; ?>
					<?php endif; ?>
				<?php endforeach; ?>
			</ul>
			<ul class="simple_list" style="clear: both;">
				<li>
					<input type="hidden" id="max_filters" name="max_filters" value="<?php echo count($appliedfilters); ?>">
					<label for="add_filter"><?php echo __('Add filter'); ?></label>
					<select name="filter_name" id="add_search_filter_dropdown">
						<?php if (!TBGContext::isProjectContext()): ?>
							<option value="project_id"><?php echo __('Project - which project an issue is reported for'); ?></option>
						<?php else: ?>
							<option value="subprojects"><?php echo __('Subproject - whether to include subprojects or not'); ?></option>
						<?php endif; ?>
						<option value="state"><?php echo __('Issue state - whether an issue is open or closed'); ?></option>
						<option value="status"><?php echo __('Status - what status an issue has'); ?></option>
						<option value="resolution"><?php echo __("Resolution - the issue's resolution"); ?></option>
						<option value="category"><?php echo __("Category - which category an issue is in"); ?></option>
						<option value="priority"><?php echo __("Priority - how high the issue is prioritised"); ?></option>
						<option value="severity"><?php echo __("Severity - how serious the issue is"); ?></option>
						<option value="reproducability"><?php echo __("Reproducability - how often you can reproduce the issue"); ?></option>
						<option value="issuetype"><?php echo __("Issue type - what kind of issue it is"); ?></option>
						<option value="milestone"><?php echo __("Milestone - which milestone an issue is targetted for"); ?></option>
						<option value="component"><?php echo __("Component - which components have been affected"); ?></option>
						<option value="build"><?php echo __("Build - which builds have been affected"); ?></option>
						<option value="edition"><?php echo __("Edition - which editions have been affected"); ?></option>
						<option value="posted_by"><?php echo __("Posted by user - which user posted the issue"); ?></option>
						<option value="owner_user"><?php echo __("Owned by user - which user owns an issue"); ?></option>
						<option value="owner_team"><?php echo __("Owned by team - which team owns an issue"); ?></option>
						<option value="assignee_user"><?php echo __("Assigned to user - which user is an issue assigned to"); ?></option>
						<option value="assignee_team"><?php echo __("Assigned to team - which team is an issue assigned to"); ?></option>
						<option value="posted"><?php echo __("Date reported - when was the issue reported"); ?></option>
						<option value="last_updated"><?php echo __("Date last updated - when was the issue last updated"); ?></option>
						<?php foreach (TBGCustomDatatype::getAll() as $customdatatype): ?>
							<?php if (!$customdatatype->isSearchable()) { continue; } ?>
							<option value="<?php echo $customdatatype->getKey(); ?>"><?php echo __($customdatatype->getDescription()); ?></option>
						<?php endforeach; ?>
					</select>
					<button class="button button-silver" onclick="TBG.Search.Filter.add('<?php echo (TBGContext::isProjectContext()) ? make_url('project_search_add_filter', array('project_key' => TBGContext::getCurrentProject()->getKey())) : make_url('search_add_filter'); ?>'); return false;"><?php echo __('Add filter'); ?></button>
					<?php echo image_tag('spinning_16.gif', array('style' => 'margin-left: 5px; display: none;', 'id' => 'add_filter_indicator')); ?>
					<div class="faded_out" style="clear: both; padding: 0 0 5px 2px;"><?php echo __('Adding the same filter more than once means that any of the given values for that filter will return a match if you are matching with "is", and neither of the given values if you are matching with "is not"'); ?></div>
				</li>
			</ul>
			<div class="search_buttons button-group">
				<?php if (!$tbg_user->isGuest()): ?>
					<?php if ($issavedsearch && ((TBGContext::isProjectContext() && !TBGContext::getCurrentProject()->isArchived()) || !TBGContext::isProjectContext())): ?>
						<input type="button" class="button button-silver" onclick="$('find_issues_form').method = 'post';$('saved_search_details').show();$('saved_search_name').enable();$('saved_search_description').enable();<?php if ($tbg_user->canCreatePublicSearches()): ?>$('saved_search_public').enable();<?php endif; ?>$('save_search').enable();$('search_button_bottom').disable();$('search_button_bottom').hide();$('saved_search_id').enable();$('search_button_save_new').hide();$('search_button_save').show();return false;" value="<?php echo __('Edit saved search details'); ?>">
						<input type="button" class="button button-silver" onclick="$('find_issues_form').method = 'post';$('save_search').enable();$('saved_search_name').enable();$('saved_search_name').focus();$('saved_search_description').enable();<?php if ($tbg_user->canCreatePublicSearches()): ?>$('saved_search_public').enable();<?php endif; ?>$('search_button_bottom').disable();$('search_button_bottom').hide();$('saved_search_id').disable();$('search_button_save_new').show();$('search_button_save').hide();if ($('saved_search_details').visible()) { return true; } else { $('saved_search_details').show(); return false; };" value="<?php echo __('Save as new saved search'); ?>">
					<?php elseif (((TBGContext::isProjectContext() && !TBGContext::getCurrentProject()->isArchived()) || !TBGContext::isProjectContext())): ?>
						<input type="button" class="button button-silver" onclick="$('find_issues_form').method = 'post';$('saved_search_details').show();$('saved_search_name').enable();$('saved_search_name').focus();$('saved_search_description').enable();<?php if ($tbg_user->canCreatePublicSearches()): ?>$('saved_search_public').enable();<?php endif; ?>$('save_search').enable();$('search_button_bottom').disable();$('search_button_bottom').hide();$('search_button_save').hide();return false;" value="<?php echo __('Save this search'); ?>">
					<?php endif; ?>
					<input type="button" class="button button-silver" onclick="$('search_advanced_details').toggle();return false;" value="<?php echo __('More search details'); ?>">
				<?php endif; ?>
				<input type="submit" class="button button-silver" value="<?php echo __('Search'); ?>" id="search_button_bottom" onclick="$('save_search').disable();$('saved_search_name').disable();$('saved_search_description').disable();<?php if ($tbg_user->canCreatePublicSearches()): ?>$('saved_search_public').disable();<?php endif; ?>$('find_issues_form').method = 'get';">
			</div>
			<br style="clear: both;">
			<div class="rounded_box white borderless" style="display: none; margin: 5px 0 5px 0; padding: 3px 10px 3px 10px;" id="search_advanced_details">
				<ul class="simple_list">
					<li>
						<label for="result_template"><?php echo __('Display results as'); ?></label>
						<select name="template" id="result_template" onchange="if (this.getValue() == 'results_userpain_totalpainthreshold' || this.getValue() == 'results_userpain_singlepainthreshold') { $('template_parameter_div').show();$('template_parameter_label').update(__('User pain threshold')); } else { $('template_parameter_div').hide();$('template_parameter_label').update(__('Template parameter')); }">
							<?php foreach ($templates as $template_name => $template_description): ?>
								<option value="<?php echo $template_name; ?>"<?php if ($template_name == $templatename): ?> selected<?php endif; ?>><?php echo $template_description; ?></option>
							<?php endforeach; ?>
						</select>
					</li>
					<li id="template_parameter_div" style="<?php if (!in_array($templatename, array('results_userpain_singlepainthreshold', 'results_userpain_totalpainthreshold'))): ?> display: none;<?php endif; ?>">
						<label for="template_parameter" id="template_parameter_label"><?php echo (!in_array($templatename, array('results_userpain_singlepainthreshold', 'results_userpain_totalpainthreshold'))) ? __('Template parameter') : __('User pain threshold'); ?></label>
						<input name="template_parameter" id="template_parameter" type="text" value="<?php echo $template_parameter; ?>" style="width: 100px;">
						<div class="faded_out"><?php echo __('If the template has a custom parameter, use this field to specify it'); ?></div>
					</li>
					<li>
						<label for="issues_per_page"><?php echo __('Issues per page'); ?></label>
						<select name="issues_per_page" id="issues_per_page">
							<?php foreach (array(15, 30, 50, 100, 250, 500) as $cc): ?>
								<option value="<?php echo $cc; ?>"<?php if ($ipp == $cc): ?> selected<?php endif; ?>><?php echo __('%number_of_issues issues per page', array('%number_of_issues' => $cc)); ?></option>
							<?php endforeach; ?>
							<option value="0"<?php if ($ipp == 0): ?> selected<?php endif; ?>><?php echo __('All results on one page'); ?></option>
						</select>
					</li>
					<li>
						<label for="groupby"><?php echo __('Group results by'); ?></label>
						<select name="groupby" id="groupby" onchange="if ($(this).value != '') { $('grouporder').show(); } else { $('grouporder').hide(); }">
							<option value=""><?php echo __('No grouping'); ?></option>
							<?php if (!TBGContext::isProjectContext()): ?>
								<option disabled value="project_id"<?php if ($groupby == 'project_id'): ?> selected<?php endif; ?>><?php echo __('Project'); ?></option>
							<?php endif; ?>
							<option value="milestone"<?php if ($groupby == 'milestone'): ?> selected<?php endif; ?>><?php echo __('Milestone'); ?></option>
							<option value="assignee"<?php if ($groupby == 'assignee'): ?> selected<?php endif; ?>><?php echo __("Who's assigned"); ?></option>
							<option value="state"<?php if ($groupby == 'state'): ?> selected<?php endif; ?>><?php echo __('State (open or closed)'); ?></option>
							<option value="severity"<?php if ($groupby == 'severity'): ?> selected<?php endif; ?>><?php echo __('Severity'); ?></option>
							<option value="category"<?php if ($groupby == 'category'): ?> selected<?php endif; ?>><?php echo __('Category'); ?></option>
							<option value="status"<?php if ($groupby == 'status'): ?> selected<?php endif; ?>><?php echo __('Status'); ?></option>
							<option value="resolution"<?php if ($groupby == 'resolution'): ?> selected<?php endif; ?>><?php echo __('Resolution'); ?></option>
							<option value="issuetype"<?php if ($groupby == 'issuetype'): ?> selected<?php endif; ?>><?php echo __('Issue type'); ?></option>
							<option value="priority"<?php if ($groupby == 'priority'): ?> selected<?php endif; ?>><?php echo __('Priority'); ?></option>
							<option value="edition"<?php if ($groupby == 'edition'): ?> selected<?php endif; ?>><?php echo __('Edition'); ?></option>
							<option value="build"<?php if ($groupby == 'build'): ?> selected<?php endif; ?>><?php echo __('Release'); ?></option>
							<option value="component"<?php if ($groupby == 'component'): ?> selected<?php endif; ?>><?php echo __('Component'); ?></option>
						</select>
						<select name="grouporder" id="grouporder"<?php if (!$groupby): ?> style="display: none;"<?php endif; ?>>
							<option value="asc"<?php if ($grouporder == 'asc'): ?> selected<?php endif; ?>><?php echo __('Ascending'); ?></option>
							<option value="desc"<?php if ($grouporder == 'desc'): ?> selected<?php endif; ?>><?php echo __('Descending'); ?></option>
						</select>
					</li>
				</ul>
			</div>
			<?php if (!$tbg_user->isGuest()): ?>
				<div class="fullpage_backdrop" style="display: none;" id="saved_search_details">
					<div class="backdrop_box large">
						<div class="backdrop_detail_content" style="font-size: 14px; text-align: left;">
							<?php if (TBGContext::isProjectContext()): ?>
								<p style="padding-bottom: 15px;" class="faded_out"><?php echo __('This saved search will be available under this project only. To make a non-project-specific search, use the main "%find_issues" page instead', array('%find_issues' => link_tag(make_url('search'), __('Find issues')))); ?></p>
							<?php endif; ?>
							<?php if ($issavedsearch): ?>
								<input type="hidden" name="saved_search_id" id="saved_search_id" value="<?php echo $savedsearch->get(TBGSavedSearchesTable::ID); ?>">
							<?php endif; ?>
							<input type="hidden" name="save" value="1" id="save_search" disabled>
							<label for="saved_search_name"><?php echo __('Saved search name'); ?></label>
							<input type="text" name="saved_search_name" id="saved_search_name"<?php if ($issavedsearch): ?> value="<?php echo $savedsearch->get(TBGSavedSearchesTable::NAME); ?>"<?php endif; ?> style="width: 350px;" disabled><br>
							<label for="saved_search_description"><?php echo __('Description'); ?> <span style="font-weight: normal;">(<?php echo __('Optional'); ?>)</span></label>
							<input type="text" name="saved_search_description" id="saved_search_description"<?php if ($issavedsearch): ?> value="<?php echo $savedsearch->get(TBGSavedSearchesTable::DESCRIPTION); ?>"<?php endif; ?> style="width: 350px;" disabled><br>
							<?php if ($tbg_user->canCreatePublicSearches()): ?>
								<label for="saved_search_public"><?php echo __('Available to'); ?></label>
								<select name="saved_search_public" id="saved_search_public" disabled>
									<option value="0"<?php if ($issavedsearch && $savedsearch->get(TBGSavedSearchesTable::IS_PUBLIC) == 0): ?> selected<?php endif; ?>><?php echo __('Only to me'); ?></option>
									<option value="1"<?php if ($issavedsearch && $savedsearch->get(TBGSavedSearchesTable::IS_PUBLIC) == 1): ?> selected<?php endif; ?>><?php echo __('To everyone'); ?></option>
								</select>
							<?php endif; ?>
							<div style="text-align: right;">
								<input type="submit" value="<?php echo __('Update this saved search'); ?>" id="search_button_save" onclick="$('find_issues_form').method = 'post';$('save_search').enable();return true;">
								<input type="submit" value="<?php echo __('Save this search'); ?>" id="search_button_save_new" onclick="$('find_issues_form').method = 'post';$('save_search').enable();return true;">
								<?php echo __('%update_or_save_search or %cancel', array('%update_or_save_search' => '', '%cancel' => "<a href=\"javascript:void('0');\" onclick=\"$('saved_search_details').hide();$('saved_search_name').disable();$('saved_search_description').disable();".(($tbg_user->canCreatePublicSearches()) ? "$('saved_search_public').disable();" : '')."$('search_button_bottom').enable();$('search_button_bottom').show();\"><b>".__('cancel').'</b></a>')); ?>
							</div>
						</div>
					</div>
				</div>
			<?php endif; ?>
		</div> */ ?>
	</form>
	<div id="searchbuilder_filter_hiddencontainer" style="display: none;">
		<?php if (TBGContext::isProjectContext()): ?>
			<?php if (!$search_object->hasFilter('subprojects')) include_component('search/interactivefilter', array('filter' => TBGSearchFilter::createFilter('subprojects'))); ?>
		<?php endif; ?>
		<?php foreach (array('priority', 'severity', 'reproducability', 'resolution', 'posted_by', 'assignee_user', 'assignee_team', 'owner_user', 'owner_team', 'milestone', 'edition', 'component', 'build') as $key): ?>
		<?php if (!$search_object->hasFilter($key)) include_component('search/interactivefilter', array('filter' => TBGSearchFilter::createFilter($key))); ?>
		<?php endforeach; ?>
	</div>
	<?php if (!$tbg_user->isGuest()): ?>
		<div class="fullpage_backdrop" style="display: none;" id="saved_search_details">
			<div class="backdrop_box large">
				<div class="backdrop_detail_header"><?php echo __('Save this search'); ?></div>
				<div class="backdrop_detail_content">
					<form id="save_search_form" action="<?php echo make_url('search_save'); ?>" method="post" onsubmit="TBG.Search.saveSearch();return false;">
						<?php if (TBGContext::isProjectContext()): ?>
							<input type="hidden" name="project_id" value="<?php echo TBGContext::getCurrentProject()->getID(); ?>">
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
