<?php

	$bugs_response->setTitle((BUGScontext::isProjectContext()) ? __('Find issues for %project_name%', array('%project_name%' => BUGScontext::getCurrentProject()->getName())) : __('Find issues'));
	if (BUGScontext::isProjectContext())
	{
		$bugs_response->addFeed(make_url('project_issues', array('project_key' => BUGScontext::getCurrentProject()->getKey(), 'predefined_search' => BUGScontext::PREDEFINED_SEARCH_PROJECT_OPEN_ISSUES, 'search' => true, 'format' => 'rss')), __('Open issues for %project_name%', array('%project_name%' => BUGScontext::getCurrentProject()->getName())));
		$bugs_response->addFeed(make_url('project_issues', array('project_key' => BUGScontext::getCurrentProject()->getKey(), 'predefined_search' => BUGScontext::PREDEFINED_SEARCH_PROJECT_CLOSED_ISSUES, 'search' => true, 'format' => 'rss')), __('Closed issues for %project_name%', array('%project_name%' => BUGScontext::getCurrentProject()->getName())));
		$bugs_response->addFeed(make_url('project_issues', array('project_key' => BUGScontext::getCurrentProject()->getKey(), 'predefined_search' => BUGScontext::PREDEFINED_SEARCH_PROJECT_MILESTONE_TODO, 'search' => true, 'format' => 'rss')), __('Milestone todo-list for %project_name%', array('%project_name%' => BUGScontext::getCurrentProject()->getName())));
		$bugs_response->addFeed(make_url('project_issues', array('project_key' => BUGScontext::getCurrentProject()->getKey(), 'predefined_search' => BUGScontext::PREDEFINED_SEARCH_MY_REPORTED_ISSUES, 'search' => true, 'format' => 'rss')), __('Issues reported by me') . ' ('.BUGScontext::getCurrentProject()->getName().')');
		$bugs_response->addFeed(make_url('project_issues', array('project_key' => BUGScontext::getCurrentProject()->getKey(), 'predefined_search' => BUGScontext::PREDEFINED_SEARCH_MY_ASSIGNED_OPEN_ISSUES, 'search' => true, 'format' => 'rss')), __('Open issues assigned to me') . ' ('.BUGScontext::getCurrentProject()->getName().')');
		$bugs_response->addFeed(make_url('project_issues', array('project_key' => BUGScontext::getCurrentProject()->getKey(), 'predefined_search' => BUGScontext::PREDEFINED_SEARCH_TEAM_ASSIGNED_OPEN_ISSUES, 'search' => true, 'format' => 'rss')), __('Open issues assigned to my teams') . ' ('.BUGScontext::getCurrentProject()->getName().')');
	}
	else
	{
		$bugs_response->addFeed(make_url('search', array('predefined_search' => BUGScontext::PREDEFINED_SEARCH_MY_REPORTED_ISSUES, 'search' => true, 'format' => 'rss')), __('Issues reported by me'));
		$bugs_response->addFeed(make_url('search', array('predefined_search' => BUGScontext::PREDEFINED_SEARCH_MY_ASSIGNED_OPEN_ISSUES, 'search' => true, 'format' => 'rss')), __('Open issues assigned to you'));
		$bugs_response->addFeed(make_url('search', array('predefined_search' => BUGScontext::PREDEFINED_SEARCH_TEAM_ASSIGNED_OPEN_ISSUES, 'search' => true, 'format' => 'rss')), __('Open issues assigned to your teams'));
	}

?>
<table style="width: 100%;" cellpadding="0" cellspacing="0">
	<tr>
		<td class="saved_searches">
			<div class="left_menu_header" style="margin-top: 5px;"><?php echo __('Predefined searches'); ?></div>
			<?php if (BUGScontext::isProjectContext()): ?>
				<div style="clear: both;">
					<?php echo link_tag(make_url('project_issues', array('project_key' => BUGScontext::getCurrentProject()->getKey(), 'predefined_search' => BUGScontext::PREDEFINED_SEARCH_PROJECT_OPEN_ISSUES, 'search' => true, 'format' => 'rss')), image_tag('icon_rss.png'), array('title' => __('Download feed'), 'style' => 'float: left; margin-right: 5px;', 'class' => 'image')); ?>
					<?php echo link_tag(make_url('project_issues', array('project_key' => BUGScontext::getCurrentProject()->getKey(), 'predefined_search' => BUGScontext::PREDEFINED_SEARCH_PROJECT_OPEN_ISSUES, 'search' => true)), __('Open issues for this project')); ?><br>
				</div>
				<div style="clear: both; margin-bottom: 20px;">
					<?php echo link_tag(make_url('project_issues', array('project_key' => BUGScontext::getCurrentProject()->getKey(), 'predefined_search' => BUGScontext::PREDEFINED_SEARCH_PROJECT_CLOSED_ISSUES, 'search' => true, 'format' => 'rss')), image_tag('icon_rss.png'), array('title' => __('Download feed'), 'style' => 'float: left; margin-right: 5px;', 'class' => 'image')); ?>
					<?php echo link_tag(make_url('project_issues', array('project_key' => BUGScontext::getCurrentProject()->getKey(), 'predefined_search' => BUGScontext::PREDEFINED_SEARCH_PROJECT_CLOSED_ISSUES, 'search' => true)), __('Closed issues for this project')); ?>
				</div>
				<div style="clear: both; margin-bottom: 20px;">
					<?php echo link_tag(make_url('project_issues', array('project_key' => BUGScontext::getCurrentProject()->getKey(), 'predefined_search' => BUGScontext::PREDEFINED_SEARCH_PROJECT_MILESTONE_TODO, 'search' => true, 'format' => 'rss')), image_tag('icon_rss.png'), array('title' => __('Download feed'), 'style' => 'float: left; margin-right: 5px;', 'class' => 'image')); ?>
					<?php echo link_tag(make_url('project_issues', array('project_key' => BUGScontext::getCurrentProject()->getKey(), 'predefined_search' => BUGScontext::PREDEFINED_SEARCH_PROJECT_MILESTONE_TODO, 'search' => true)), __('Milestone todo-list for this project')); ?>
				</div>
				<div style="clear: both;">
					<?php echo link_tag(make_url('project_issues', array('project_key' => BUGScontext::getCurrentProject()->getKey(), 'predefined_search' => BUGScontext::PREDEFINED_SEARCH_MY_REPORTED_ISSUES, 'search' => true, 'format' => 'rss')), image_tag('icon_rss.png'), array('title' => __('Download feed'), 'style' => 'float: left; margin-right: 5px;', 'class' => 'image')); ?>
					<?php echo link_tag(make_url('project_issues', array('project_key' => BUGScontext::getCurrentProject()->getKey(), 'predefined_search' => BUGScontext::PREDEFINED_SEARCH_MY_REPORTED_ISSUES, 'search' => true)), __('Issues reported by me')); ?><br>
				</div>
				<div style="clear: both;">
					<?php echo link_tag(make_url('project_issues', array('project_key' => BUGScontext::getCurrentProject()->getKey(), 'predefined_search' => BUGScontext::PREDEFINED_SEARCH_MY_ASSIGNED_OPEN_ISSUES, 'search' => true, 'format' => 'rss')), image_tag('icon_rss.png'), array('title' => __('Download feed'), 'style' => 'float: left; margin-right: 5px;', 'class' => 'image')); ?>
					<?php echo link_tag(make_url('project_issues', array('project_key' => BUGScontext::getCurrentProject()->getKey(), 'predefined_search' => BUGScontext::PREDEFINED_SEARCH_MY_ASSIGNED_OPEN_ISSUES, 'search' => true)), __('Open issues assigned to me')); ?><br>
				</div>
				<div style="clear: both;">
					<?php echo link_tag(make_url('project_issues', array('project_key' => BUGScontext::getCurrentProject()->getKey(), 'predefined_search' => BUGScontext::PREDEFINED_SEARCH_TEAM_ASSIGNED_OPEN_ISSUES, 'search' => true, 'format' => 'rss')), image_tag('icon_rss.png'), array('title' => __('Download feed'), 'style' => 'float: left; margin-right: 5px;', 'class' => 'image')); ?>
					<?php echo link_tag(make_url('project_issues', array('project_key' => BUGScontext::getCurrentProject()->getKey(), 'predefined_search' => BUGScontext::PREDEFINED_SEARCH_TEAM_ASSIGNED_OPEN_ISSUES, 'search' => true)), __('Open issues assigned to my teams')); ?><br>
				</div>
			<?php else: ?>
				<div style="clear: both;">
					<?php echo link_tag(make_url('search', array('predefined_search' => BUGScontext::PREDEFINED_SEARCH_MY_REPORTED_ISSUES, 'search' => true, 'format' => 'rss')), image_tag('icon_rss.png'), array('title' => __('Download feed'), 'style' => 'float: left; margin-right: 5px;', 'class' => 'image')); ?>
					<?php echo link_tag(make_url('search', array('predefined_search' => BUGScontext::PREDEFINED_SEARCH_MY_REPORTED_ISSUES, 'search' => true)), __('Issues reported by me')); ?><br>
				</div>
				<div style="clear: both;">
					<?php echo link_tag(make_url('search', array('predefined_search' => BUGScontext::PREDEFINED_SEARCH_MY_ASSIGNED_OPEN_ISSUES, 'search' => true, 'format' => 'rss')), image_tag('icon_rss.png'), array('title' => __('Download feed'), 'style' => 'float: left; margin-right: 5px;', 'class' => 'image')); ?>
					<?php echo link_tag(make_url('search', array('predefined_search' => BUGScontext::PREDEFINED_SEARCH_MY_ASSIGNED_OPEN_ISSUES, 'search' => true)), __('Open issues assigned to me')); ?><br>
				</div>
				<div style="clear: both;">
					<?php echo link_tag(make_url('search', array('predefined_search' => BUGScontext::PREDEFINED_SEARCH_TEAM_ASSIGNED_OPEN_ISSUES, 'search' => true, 'format' => 'rss')), image_tag('icon_rss.png'), array('title' => __('Download feed'), 'style' => 'float: left; margin-right: 5px;', 'class' => 'image')); ?>
					<?php echo link_tag(make_url('search', array('predefined_search' => BUGScontext::PREDEFINED_SEARCH_TEAM_ASSIGNED_OPEN_ISSUES, 'search' => true)), __('Open issues assigned to my teams')); ?><br>
				</div>
			<?php endif; ?>
			<div class="left_menu_header" style="margin-top: 15px;"><?php echo __('Saved searches'); ?></div>
		</td>
		<td style="width: auto; padding: 5px; vertical-align: top;" id="find_issues">
			<div class="rounded_box iceblue_borderless" style="margin: 5px 0 5px 0;">
				<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
				<div class="xboxcontent" style="padding: 3px 10px 3px 10px; font-size: 14px;">
					<form accept-charset="<?php echo BUGScontext::getI18n()->getCharset(); ?>" action="<?php echo (BUGScontext::isProjectContext()) ? make_url('project_issues', array('project_key' => BUGScontext::getCurrentProject()->getKey())) : make_url('search'); ?>" method="get" id="find_issues_form">
						<a href="#" onclick="$('search_filters').toggle();$('add_filter_form').toggle();" style="float: right; margin-top: 3px;"><b><?php echo __('More'); ?></b></a>
						<label for="issues_searchfor"><?php echo __('Search for'); ?></label>
						<input type="text" name="searchfor" value="<?php echo $searchterm; ?>" id="issues_searchfor" style="width: 450px;">
						<input type="submit" value="<?php echo __('Search'); ?>" id="search_button_top">
						<div style="<?php if (count($appliedfilters) <= (int) BUGScontext::isProjectContext()): ?>display: none; <?php endif; ?>padding: 5px;" id="search_filters">
							<label for="result_template"><?php echo __('Display results as'); ?></label>
							<select name="template" id="result_template">
								<?php foreach ($templates as $template_name => $template_description): ?>
									<option value="<?php echo $template_name; ?>"<?php if ($template_name == $templatename): ?> selected<?php endif; ?>><?php echo $template_description; ?></option>
								<?php endforeach; ?>
							</select><br>
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
								<?php if (!BUGScontext::isProjectContext()): ?>
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
								<option disabled value="edition"<?php if ($groupby == 'edition'): ?> selected<?php endif; ?>><?php echo __('Edition'); ?></option>
								<option disabled value="build"<?php if ($groupby == 'build'): ?> selected<?php endif; ?>><?php echo __('Version'); ?></option>
								<option disabled value="component"<?php if ($groupby == 'component'): ?> selected<?php endif; ?>><?php echo __('Component'); ?></option>
							</select>
							<select name="grouporder" id="grouporder">
								<option value="asc"<?php if ($grouporder == 'asc'): ?> selected<?php endif; ?>><?php echo __('Ascending'); ?></option>
								<option value="desc"<?php if ($grouporder == 'desc'): ?> selected<?php endif; ?>><?php echo __('Descending'); ?></option>
							</select><br>
							<ul id="search_filters_list">
								<?php foreach ($appliedfilters as $filter => $filter_info): ?>
									<?php if (array_key_exists('value', $filter_info)): ?>
										<?php include_component('search/filter', array('filter' => $filter, 'selected_operator' => $filter_info['operator'], 'selected_value' => $filter_info['value'], 'key' => 0)); ?>
									<?php else: ?>
										<?php foreach ($filter_info as $k => $single_filter): ?>
											<?php include_component('search/filter', array('filter' => $filter, 'selected_operator' => $single_filter['operator'], 'selected_value' => $single_filter['value'], 'key' => $k)); ?>
										<?php endforeach; ?>
									<?php endif; ?>
								<?php endforeach; ?>
							</ul>
							<div style="text-align: right;">
								<input type="submit" value="<?php echo __('Search'); ?>" id="search_button_top">
							</div>
						</div>
					</form>
					<input type="hidden" id="max_filters" name="max_filters" value="<?php echo count($appliedfilters); ?>">
					<form accept-charset="<?php echo BUGScontext::getI18n()->getCharset(); ?>" action="<?php echo (BUGScontext::isProjectContext()) ? make_url('project_search_add_filter', array('project_key' => BUGScontext::getCurrentProject()->getKey())) : make_url('search_add_filter'); ?>" method="post" id="add_filter_form"<?php if (count($appliedfilters) <= (int) BUGScontext::isProjectContext()): ?> style="display: none;"<?php endif; ?> onsubmit="addSearchFilter('<?php echo (BUGScontext::isProjectContext()) ? make_url('project_search_add_filter', array('project_key' => BUGScontext::getCurrentProject()->getKey())) : make_url('search_add_filter'); ?>');return false;">
						<label for="add_filter"><?php echo __('Add filter'); ?></label>
						<select name="filter_name">
							<?php if (!BUGScontext::isProjectContext()): ?>
								<option value="project_id"><?php echo __('Project'); ?></option>
							<?php endif; ?>
							<option value="state"><?php echo __('Issue state - whether an issue is open or closed'); ?></option>
							<option value="status"><?php echo __('Status - what status an issue has'); ?></option>
							<option value="resolution"><?php echo __("Resolution - the issue's resolution"); ?></option>
							<option value="category"><?php echo __("Category - which category an issue is in"); ?></option>
							<option value="priority"><?php echo __("Priority - how high the issue is prioritised"); ?></option>
							<option value="severity"><?php echo __("Severity - how serious the issue is"); ?></option>
							<option value="reproducability"><?php echo __("Reproducability - how often you can reproduce the issue"); ?></option>
							<option value="issue_type"><?php echo __("Issue type - what kind of issue it is"); ?></option>
							<?php foreach (BUGScustomdatatype::getAll() as $customdatatype): ?>
								<option value="<?php echo $customdatatype->getKey(); ?>"><?php echo __($customdatatype->getDescription()); ?></option>
							<?php endforeach; ?>
						</select>
						<?php echo image_submit_tag('action_add_small.png'); ?>
						<?php echo image_tag('spinning_16.gif', array('style' => 'margin-left: 5px; display: none;', 'id' => 'add_filter_indicator')); ?>
					</form>
				</div>
				<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
			</div>
			<?php if ($show_results): ?>
				<div class="main_header">
					<?php echo $searchtitle; ?>
					&nbsp;&nbsp;<span class="faded_medium"><?php echo __('%number_of% issue(s)', array('%number_of%' => $resultcount)); ?></span>
				</div>
				<?php if (count($issues) > 0): ?>
					<div id="search_results">
						<?php include_template('search/issues_paginated', array('issues' => $issues, 'templatename' => $templatename, 'searchterm' => $searchterm, 'filters' => $appliedfilters, 'groupby' => $groupby, 'resultcount' => $resultcount, 'ipp' => $ipp, 'offset' => $offset)); ?>
					</div>
				<?php else: ?>
					<div class="faded_medium" id="no_issues"><?php echo __('No issues were found'); ?></div>
				<?php endif; ?>
			<?php endif; ?>
		</td>
	</tr>
</table>
