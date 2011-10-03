<div class="rounded_box iceblue borderless searchbox_container"<?php if ($show_results): ?> style="display: none;"<?php endif; ?> id="search_builder">
	<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="<?php echo (TBGContext::isProjectContext()) ? make_url('project_issues', array('project_key' => TBGContext::getCurrentProject()->getKey())) : make_url('search'); ?>" method="get" id="find_issues_form">
		<label for="issues_searchfor"><?php echo __('Search for'); ?></label>
		<select name="filters[text][operator]">
			<option value="="<?php if (array_key_exists('text', $appliedfilters) && ((array_key_exists('operator', $appliedfilters['text']) && $appliedfilters['text']['operator'] == '=') || (!array_key_exists('operator', $appliedfilters['text']) && $appliedfilters['text'][0]['operator'] == '='))): ?> selected<?php endif; ?>><?php echo __('Issues containing'); ?></option>
			<option value="!="<?php if (array_key_exists('text', $appliedfilters) && ((array_key_exists('operator', $appliedfilters['text']) && $appliedfilters['text']['operator'] == '!=') || (!array_key_exists('operator', $appliedfilters['text']) && $appliedfilters['text'][0]['operator'] == '!='))): ?> selected<?php endif; ?>><?php echo __('Issues not containing'); ?></option>
		</select>
		<input type="text" name="filters[text][value]" value="<?php echo $searchterm; ?>" id="issues_searchfor" style="width: 450px;">
		<input type="submit" value="<?php echo __('Search'); ?>" id="search_button_top" onclick="$('save_search').disable();" style="margin-top: -3px;">
		<div class="faded_out" style="padding: 2px 0 5px 2px; font-size: 12px;"><p><?php echo __('Leave this input field blank to list all issues based on filters below'); ?></p></div>
		<div style="padding: 5px;" id="search_filters">
			<label for="result_template"><?php echo __('Display results as'); ?></label>
			<select name="template" id="result_template" onchange="if (this.getValue() == 'results_userpain_totalpainthreshold' || this.getValue() == 'results_userpain_singlepainthreshold') { $('template_parameter_div').show();$('template_parameter_label').update(__('User pain threshold')); } else { $('template_parameter_div').hide();$('template_parameter_label').update(__('Template parameter')); }">
				<?php foreach ($templates as $template_name => $template_description): ?>
					<option value="<?php echo $template_name; ?>"<?php if ($template_name == $templatename): ?> selected<?php endif; ?>><?php echo $template_description; ?></option>
				<?php endforeach; ?>
			</select><br>
			<div id="template_parameter_div" style="margin-bottom: 10px;<?php if (!in_array($templatename, array('results_userpain_singlepainthreshold', 'results_userpain_totalpainthreshold'))): ?> display: none;<?php endif; ?>">
				<label for="template_parameter" id="template_parameter_label"><?php echo (!in_array($templatename, array('results_userpain_singlepainthreshold', 'results_userpain_totalpainthreshold'))) ? __('Template parameter') : __('User pain threshold'); ?></label>
				<input name="template_parameter" id="template_parameter" type="text" value="<?php echo $template_parameter; ?>" style="width: 100px;">
				<div class="faded_out"><?php echo __('If the template has a custom parameter, use this field to specify it'); ?></div>
			</div>
			<label for="issues_per_page"><?php echo __('Issues per page'); ?></label>
			<select name="issues_per_page" id="issues_per_page">
				<?php foreach (array(15, 30, 50, 100) as $cc): ?>
					<option value="<?php echo $cc; ?>"<?php if ($ipp == $cc): ?> selected<?php endif; ?>><?php echo __('%number_of_issues% issues per page', array('%number_of_issues%' => $cc)); ?></option>
				<?php endforeach; ?>
				<option value="0"<?php if ($ipp == 0): ?> selected<?php endif; ?>><?php echo __('All results on one page'); ?></option>
			</select><br>
			<label for="groupby"><?php echo __('Group results by'); ?></label>
			<select name="groupby" id="groupby">
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
			<select name="grouporder" id="grouporder">
				<option value="asc"<?php if ($grouporder == 'asc'): ?> selected<?php endif; ?>><?php echo __('Ascending'); ?></option>
				<option value="desc"<?php if ($grouporder == 'desc'): ?> selected<?php endif; ?>><?php echo __('Descending'); ?></option>
			</select><br>
			<ul id="search_filters_list">
				<?php foreach ($appliedfilters as $filter => $filter_info): ?>
					<?php if (array_key_exists('value', $filter_info) && $filter != 'text'): ?>
						<?php include_component('search/filter', array('filter' => $filter, 'selected_operator' => $filter_info['operator'], 'selected_value' => $filter_info['value'], 'key' => 0)); ?>
					<?php elseif ($filter != 'text'): ?>
						<?php foreach ($filter_info as $k => $single_filter): ?>
							<?php include_component('search/filter', array('filter' => $filter, 'selected_operator' => $single_filter['operator'], 'selected_value' => $single_filter['value'], 'key' => $k)); ?>
						<?php endforeach; ?>
					<?php endif; ?>
				<?php endforeach; ?>
			</ul>
			<div style="text-align: right;">
				<?php if ($issavedsearch && ((TBGContext::isProjectContext() && !TBGContext::getCurrentProject()->isArchived()) || !TBGContext::isProjectContext())): ?>
					<button onclick="$('find_issues_form').method = 'post';$('saved_search_details').show();$('saved_search_name').enable();$('saved_search_description').enable();<?php if ($tbg_user->canCreatePublicSearches()): ?>$('saved_search_public').enable();<?php endif; ?>$('save_search').enable();$('search_button_bottom').disable();$('search_button_bottom').hide();$('search_button_top').disable();$('search_button_top').hide();$('saved_search_id').enable();$('search_button_save_new').hide();$('search_button_save').show();return false;"><?php echo __('Edit saved search details'); ?></button>
					<button onclick="$('find_issues_form').method = 'post';$('save_search').enable();$('saved_search_name').enable();$('saved_search_description').enable();<?php if ($tbg_user->canCreatePublicSearches()): ?>$('saved_search_public').enable();<?php endif; ?>$('search_button_bottom').disable();$('search_button_bottom').hide();$('search_button_top').disable();$('search_button_top').hide();$('saved_search_id').disable();$('search_button_save_new').show();$('search_button_save').hide();if ($('saved_search_details').visible()) { return true; } else { $('saved_search_details').show(); return false; };"><?php echo __('Save as new saved search'); ?></button>
				<?php elseif (((TBGContext::isProjectContext() && !TBGContext::getCurrentProject()->isArchived()) || !TBGContext::isProjectContext())): ?>
					<button onclick="$('find_issues_form').method = 'post';$('saved_search_details').show();$('saved_search_name').enable();$('saved_search_description').enable();<?php if ($tbg_user->canCreatePublicSearches()): ?>$('saved_search_public').enable();<?php endif; ?>$('save_search').enable();$('search_button_bottom').disable();$('search_button_bottom').hide();$('search_button_top').disable();$('search_button_save').hide();$('search_button_top').hide();return false;"><?php echo __('Save this search'); ?></button>
				<?php endif; ?>
				<input type="submit" value="<?php echo __('Search'); ?>" id="search_button_bottom" onclick="$('save_search').disable();$('saved_search_name').disable();$('saved_search_description').disable();<?php if ($tbg_user->canCreatePublicSearches()): ?>$('saved_search_public').disable();<?php endif; ?>$('find_issues_form').method = 'get';" style="margin-top: -1px;">
			</div>
			<div class="rounded_box white borderless" style="display: none; margin: 5px 0 5px 0; padding: 3px 10px 3px 10px; font-size: 14px;" id="saved_search_details">
				<?php if (TBGContext::isProjectContext()): ?>
					<p style="padding-bottom: 15px;" class="faded_out"><?php echo __('This saved search will be available under this project only. To make a non-project-specific search, use the main "%find_issues%" page instead', array('%find_issues%' => link_tag(make_url('search'), __('Find issues')))); ?></p>
				<?php endif; ?>
				<?php if ($issavedsearch): ?>
					<input type="hidden" name="saved_search_id" id="saved_search_id" value="<?php echo $savedsearch->get(TBGSavedSearchesTable::ID); ?>">
				<?php endif; ?>
				<input type="hidden" name="save" value="1" id="save_search" disabled>
				<label for="saved_search_name"><?php echo __('Saved search name'); ?></label>
				<input type="text" name="saved_search_name" id="saved_search_name"<?php if ($issavedsearch): ?> value="<?php echo $savedsearch->get(TBGSavedSearchesTable::NAME); ?>"<?php endif; ?> style="width: 350px;" disabled><br>
				<label for="saved_search_description"><?php echo __('Description'); ?> <span style="font-weight: normal;">(<?php echo __('Optional'); ?>)</span></label>
				<input type="text" name="saved_search_description" id="saved_search_description"<?php if ($issavedsearch): ?> value="<?php echo $savedsearch->get(TBGSavedSearchesTable::DESCRIPTION); ?>"<?php endif; ?> style="width: 350px;" disabled><br>
				<label for="saved_search_public"><?php echo __('Available to'); ?></label>
				<select name="saved_search_public" id="saved_search_public" disabled<?php if (!$tbg_user->canCreatePublicSearches()): ?> style="display: none;"<?php endif; ?>>
					<option value="0"<?php if ($issavedsearch && $savedsearch->get(TBGSavedSearchesTable::IS_PUBLIC) == 0): ?> selected<?php endif; ?>><?php echo __('Only to me'); ?></option>
					<option value="1"<?php if ($issavedsearch && $savedsearch->get(TBGSavedSearchesTable::IS_PUBLIC) == 1): ?> selected<?php endif; ?>><?php echo __('To everyone'); ?></option>
				</select>
				<div style="text-align: right;">
					<input type="submit" value="<?php echo __('Update this saved search'); ?>" id="search_button_save" onclick="$('find_issues_form').method = 'post';$('save_search').enable();return true;">
					<input type="submit" value="<?php echo __('Save this search'); ?>" id="search_button_save_new" onclick="$('find_issues_form').method = 'post';$('save_search').enable();return true;">
					<?php echo __('%update_or_save_search% or %cancel%', array('%update_or_save_search%' => '', '%cancel%' => "<a href=\"javascript:void('0');\" onclick=\"$('saved_search_details').hide();$('saved_search_name').disable();$('saved_search_description').disable();".(($tbg_user->canCreatePublicSearches()) ? "$('saved_search_public').disable();" : '')."$('search_button_bottom').enable();$('search_button_bottom').show();$('search_button_top').enable();$('search_button_top').show();\"><b>".__('cancel').'</b></a>')); ?>
				</div>
			</div>
		</div>
	</form>
	<input type="hidden" id="max_filters" name="max_filters" value="<?php echo count($appliedfilters); ?>">
	<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="<?php echo (TBGContext::isProjectContext()) ? make_url('project_search_add_filter', array('project_key' => TBGContext::getCurrentProject()->getKey())) : make_url('search_add_filter'); ?>" method="post" id="add_filter_form" onsubmit="TBG.Search.Filter.add('<?php echo (TBGContext::isProjectContext()) ? make_url('project_search_add_filter', array('project_key' => TBGContext::getCurrentProject()->getKey())) : make_url('search_add_filter'); ?>');return false;">
		<label for="add_filter"><?php echo __('Add filter'); ?></label>
		<select name="filter_name">
			<?php if (!TBGContext::isProjectContext()): ?>
				<option value="project_id"><?php echo __('Project'); ?></option>
			<?php endif; ?>
			<option value="state"><?php echo __('Issue state - whether an issue is open or closed'); ?></option>
			<option value="status"><?php echo __('Status - what status an issue has'); ?></option>
			<option value="resolution"><?php echo __("Resolution - the issue's resolution"); ?></option>
			<option value="category"><?php echo __("Category - which category an issue is in"); ?></option>
			<option value="priority"><?php echo __("Priority - how high the issue is prioritised"); ?></option>
			<option value="severity"><?php echo __("Severity - how serious the issue is"); ?></option>
			<option value="reproducability"><?php echo __("Reproducability - how often you can reproduce the issue"); ?></option>
			<option value="issuetype"><?php echo __("Issue type - what kind of issue it is"); ?></option>
			<option value="component"><?php echo __("Component - which components have been affected"); ?></option>
			<option value="build"><?php echo __("Build - which builds have been affected"); ?></option>
			<option value="edition"><?php echo __("Edition - which editions have been affected"); ?></option>
			<?php foreach (TBGCustomDatatype::getAll() as $customdatatype): ?>
				<option value="<?php echo $customdatatype->getKey(); ?>"><?php echo __($customdatatype->getDescription()); ?></option>
			<?php endforeach; ?>
		</select>
		<input type="submit" class="button button-silver" value="<?php echo __('Add filter'); ?>">
		<?php echo image_tag('spinning_16.gif', array('style' => 'margin-left: 5px; display: none;', 'id' => 'add_filter_indicator')); ?>
		<div class="faded_out" style="padding: 10px 0 5px 0;"><?php echo __('Adding the same filter more than once means that any of the given values for that filter will return a match if you are matching with "is", and neither of the given values if you are matching with "is not"'); ?></div>
	</form>
</div>
