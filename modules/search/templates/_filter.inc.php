<?php $show_button = false; ?>
<li id="filter_<?php echo $key; ?>">
	<?php if (in_array($filter, TBGIssuesTable::getValidSearchFilters())): ?>
		<?php if ($filter == 'project_id' && !TBGContext::isProjectContext()): ?>
			<label<?php if (!TBGContext::isProjectContext()): ?> for="filter_project_id_<?php echo $key; ?>"<?php endif; ?>><?php echo __('Project'); ?></label>
			<?php if (!TBGContext::isProjectContext()): ?>
				<select name="filters[project_id][<?php echo $key; ?>][operator]">
					<option value="="<?php if ($selected_operator == '='): ?> selected<?php endif; ?>><?php echo __('%field% is %value%', array('%field%' => '', '%value%' => '')); ?></option>
					<option value="!="<?php if ($selected_operator == '!='): ?> selected<?php endif; ?>><?php echo __('%field% is not %value%', array('%field%' => '', '%value%' => '')); ?></option>
				</select>
				<select name="filters[project_id][<?php echo $key; ?>][value]" id="filter_project_id_<?php echo $key; ?>">
					<?php foreach (TBGProject::getAll() as $project): ?>
						<option value="<?php echo $project->getID(); ?>"<?php if ($selected_value == $project->getID()): ?> selected<?php endif; ?>><?php echo $project->getName(); ?></option>
					<?php endforeach; ?>
				</select>
				<?php $show_button = true; ?>
			<?php endif; ?>
		<?php elseif (in_array($filter, array('posted', 'last_updated'))): ?>
			<label for="filter_<?php echo $filter; ?>_<?php echo $key; ?>"><?php echo $filters[$filter]['description']; ?></label>
			<select name="filters[<?php echo $filter; ?>][<?php echo $key; ?>][operator]">
				<option value="<?php echo urlencode('<='); ?>"<?php if (urldecode($selected_operator) == '<='): ?> selected<?php endif; ?>><?php echo __('%posted_or_updated% before %value%', array('%posted_or_updated%' => '', '%value%' => '')); ?></option>
				<option value="<?php echo urlencode('>='); ?>"<?php if (urldecode($selected_operator) == '>='): ?> selected<?php endif; ?>><?php echo __('%posted_or_updated% on or after %value%', array('%posted_or_updated%' => '', '%value%' => '')); ?></option>
			</select>
			<select id="filter_<?php echo $filter; ?>_<?php echo $key; ?>_day" onchange="TBG.Search.Filter.setTimestamp('<?php echo $filter; ?>', '<?php echo $key; ?>');">
				<?php for($cc = 1; $cc <= 31; $cc++): ?>
				<option value="<?php echo $cc; ?>"<?php if ($cc == date('d', $selected_value)): ?> selected<?php endif; ?>><?php echo $cc; ?></option>
				<?php endfor; ?>
			</select>
			<select id="filter_<?php echo $filter; ?>_<?php echo $key; ?>_month" onchange="TBG.Search.Filter.setTimestamp('<?php echo $filter; ?>', '<?php echo $key; ?>');">
				<?php for($cc = 1; $cc <= 12; $cc++): ?>
				<option value="<?php echo $cc-1; ?>"<?php if ($cc == date('m', $selected_value)): ?> selected<?php endif; ?>><?php echo date('F', mktime(0, 0, 0, $cc)); ?></option>
				<?php endfor; ?>
			</select>
			<select id="filter_<?php echo $filter; ?>_<?php echo $key; ?>_year" onchange="TBG.Search.Filter.setTimestamp('<?php echo $filter; ?>', '<?php echo $key; ?>');">
				<?php for($cc = 1990; $cc <= date('Y') + 10; $cc++): ?>
				<option value="<?php echo $cc; ?>"<?php if ($cc == date('Y', $selected_value)): ?> selected<?php endif; ?>><?php echo $cc; ?></option>
				<?php endfor; ?>
			</select>
			<input type="hidden" name="filters[<?php echo $filter; ?>][<?php echo $key; ?>][value]" id="filter_<?php echo $filter; ?>_<?php echo $key; ?>" value="<?php echo $selected_value; ?>">
			<?php $show_button = true; ?>
		<?php elseif (in_array($filter, array('assignee_user', 'posted_by', 'owner_user'))): ?>
			<label for="filter_<?php echo $filter; ?>_<?php echo $key; ?>"><?php echo $filters[$filter]['description']; ?></label>
			<select name="filters[<?php echo $filter; ?>][<?php echo $key; ?>][operator]">
				<option value="="<?php if ($selected_operator == '='): ?> selected<?php endif; ?>><?php echo __('%field% is %value%', array('%field%' => '', '%value%' => '')); ?></option>
				<option value="!="<?php if ($selected_operator == '!='): ?> selected<?php endif; ?>><?php echo __('%field% is not %value%', array('%field%' => '', '%value%' => '')); ?></option>
			</select>
			<?php echo image_tag('spinning_16.gif', array('style' => 'float: left; margin: 0 5px; display: none;', 'id' => 'filter_'.$filter.'_'.$key.'_indicator')); ?>
			<div id="filter_<?php echo $filter; ?>_<?php echo $key; ?>_name" style="display: block; float: left; margin: 3px 10px;">
				<?php include_component('main/userdropdown', array('user' => $selected_value)); ?>
			</div>
			<input type="hidden" name="filters[<?php echo $filter; ?>][<?php echo $key; ?>][value]" id="filter_<?php echo $filter; ?>_<?php echo $key; ?>" value="<?php echo $selected_value; ?>">
			<?php include_component('main/identifiableselector', array(	'html_id' 			=> 'filter_'.$filter.'_'.$key.'_popup',
																		'header' 			=> __('Please select'),
																		'callback'		 	=> "TBG.Search.Filter.setIdentifiable('" . make_url('get_temp_identifiable') . "', '".$filter."', '".$key."', %identifiable_value%, 'user');",
																		'clear_link_text'	=> __('Clear selected user'),
																		'base_id'			=> 'filter_'.$filter.'_'.$key.'_popup',
																		'include_teams'		=> false,
																		'allow_clear'		=> false,
																		'absolute'			=> true)); ?>
			<button onclick="$('filter_<?php echo $filter; ?>_<?php echo $key; ?>_popup').toggle(); return false;" class="button button-silver"><?php echo __('Select'); ?></button>
			<?php $show_button = true; ?>
		<?php elseif (in_array($filter, array('assignee_team', 'owner_team'))): ?>
			<label for="filter_<?php echo $filter; ?>_<?php echo $key; ?>"><?php echo $filters[$filter]['description']; ?></label>
			<select name="filters[<?php echo $filter; ?>][<?php echo $key; ?>][operator]">
				<option value="="<?php if ($selected_operator == '='): ?> selected<?php endif; ?>><?php echo __('%field% is %value%', array('%field%' => '', '%value%' => '')); ?></option>
				<option value="!="<?php if ($selected_operator == '!='): ?> selected<?php endif; ?>><?php echo __('%field% is not %value%', array('%field%' => '', '%value%' => '')); ?></option>
			</select>
			<?php echo image_tag('spinning_16.gif', array('style' => 'float: left; margin: 0 5px; display: none;', 'id' => 'filter_'.$filter.'_'.$key.'_indicator')); ?>
			<div id="filter_<?php echo $filter; ?>_<?php echo $key; ?>_name" style="display: block; float: left; margin: 3px 10px;">
				<?php include_component('main/teamdropdown', array('team' => $selected_value)); ?>
			</div>
			<input type="hidden" name="filters[<?php echo $filter; ?>][<?php echo $key; ?>][value]" id="filter_<?php echo $filter; ?>_<?php echo $key; ?>" value="<?php echo $selected_value; ?>">
			<?php include_component('main/identifiableselector', array(	'html_id' 			=> 'filter_'.$filter.'_'.$key.'_popup',
																		'header' 			=> __('Please select'),
																		'team_callback'	 	=> "TBG.Search.Filter.setIdentifiable('" . make_url('get_temp_identifiable') . "', '".$filter."', '".$key."', %identifiable_value%, 'team');",
																		'clear_link_text'	=> __('Clear selected team'),
																		'base_id'			=> 'filter_'.$filter.'_'.$key.'_popup',
																		'include_teams'		=> true,
																		'include_users'		=> false,
																		'allow_clear'		=> false,
																		'absolute'			=> true)); ?>
			<button onclick="$('filter_<?php echo $filter; ?>_<?php echo $key; ?>_popup').toggle(); return false;" class="button button-silver"><?php echo __('Select'); ?></button>
			<?php $show_button = true; ?>
		<?php elseif (in_array($filter, array_keys($filters))): ?>
			<label for="filter_<?php echo $filter; ?>_<?php echo $key; ?>"><?php echo $filters[$filter]['description']; ?></label>
			<select name="filters[<?php echo $filter; ?>][<?php echo $key; ?>][operator]">
				<option value="="<?php if ($selected_operator == '='): ?> selected<?php endif; ?>><?php echo __('%field% is %value%', array('%field%' => '', '%value%' => '')); ?></option>
				<option value="!="<?php if ($selected_operator == '!='): ?> selected<?php endif; ?>><?php echo __('%field% is not %value%', array('%field%' => '', '%value%' => '')); ?></option>
			</select>
			<select name="filters[<?php echo $filter; ?>][<?php echo $key; ?>][value]" id="filter_<?php echo $filter; ?>_<?php echo $key; ?>">
				<option value="0"> - </option>
				<?php foreach ($filters[$filter]['options'] as $item): ?>
					<option value="<?php echo $item->getID(); ?>"<?php if ($selected_value == $item->getID()): ?> selected<?php endif; ?>><?php echo $item->getName(); ?></option>
				<?php endforeach; ?>
			</select>
			<?php $show_button = true; ?>
		<?php elseif ($filter == 'state'): ?>
			<label for="filter_state_<?php echo $key; ?>"><?php echo __('Issue state'); ?></label>
			<select name="filters[state][<?php echo $key; ?>][operator]">
				<option value="="<?php if ($selected_operator == '='): ?> selected<?php endif; ?>><?php echo __('%field% is %value%', array('%field%' => '', '%value%' => '')); ?></option>
				<option value="!="<?php if ($selected_operator == '!='): ?> selected<?php endif; ?>><?php echo __('%field% is not %value%', array('%field%' => '', '%value%' => '')); ?></option>
			</select>
			<select name="filters[state][<?php echo $key; ?>][value]" id="filter_state_<?php echo $key; ?>">
				<option value="<?php echo TBGIssue::STATE_OPEN; ?>"<?php if ($selected_value == TBGIssue::STATE_OPEN): ?> selected<?php endif; ?>><?php echo __('Open'); ?></option>
				<option value="<?php echo TBGIssue::STATE_CLOSED; ?>"<?php if ($selected_value == TBGIssue::STATE_CLOSED): ?> selected<?php endif; ?>><?php echo __('Closed'); ?></option>
			</select>
			<?php $show_button = true; ?>
		<?php endif; ?>
	<?php else: ?>
		<?php $customdatatype = TBGCustomDatatype::getByKey($filter); ?>
		<label for="filter_<?php echo $filter; ?>_<?php echo $key; ?>"><?php echo __($customdatatype->getDescription()); ?></label>
		<select name="filters[<?php echo $filter; ?>][<?php echo $key; ?>][operator]">
			<option value="="<?php if ($selected_operator == '='): ?> selected<?php endif; ?>><?php echo __('%field% is provided and is %value%', array('%field%' => '', '%value%' => '')); ?></option>
			<option value="!="<?php if ($selected_operator == '!='): ?> selected<?php endif; ?>><?php echo __('%field% is provided and is not %value%', array('%field%' => '', '%value%' => '')); ?></option>
		</select>
		<?php if ($customdatatype->hasCustomOptions()): ?>
			<select name="filters[<?php echo $filter; ?>][<?php echo $key; ?>][value]" id="filter_<?php echo $filter; ?>_<?php echo $key; ?>">
				<?php foreach ($customdatatype->getOptions() as $option): ?>
					<option value="<?php echo $option->getID(); ?>"<?php if ($selected_value == $option->getID()): ?> selected<?php endif; ?>><?php echo $option->getName(); ?></option>
				<?php endforeach; ?>
			</select>
		<?php else: ?>
			<input name="filters[<?php echo $filter; ?>][<?php echo $key; ?>][value]" id="filter_<?php echo $filter; ?>_<?php echo $key; ?>">
		<?php endif; ?>
		<?php $show_button = true; ?>
	<?php endif; ?>
	<?php if ($show_button): ?>
		<button onclick="TBG.Search.Filter.remove(<?php echo $key; ?>);"><?php echo __('Remove'); ?></button>
	<?php endif; ?>
</li>
