<?php $show_button = false; ?>
<li id="filter_<?php echo $key; ?>">
	<?php if (in_array($filter, TBGIssuesTable::getValidSearchFilters())): ?>
		<?php if ($filter == 'project_id' && !TBGContext::isProjectContext()): ?>
			<label<?php if (!TBGContext::isProjectContext()): ?> for="filter_project_id_<?php echo $key; ?>"<?php endif; ?>><?php echo __('Project'); ?></label>
			<?php if (TBGContext::isProjectContext()): ?>
				<?php //echo __('%project% is %project_name%', array('%project%' => '', '%project_name%' => '<i>"' . TBGContext::getCurrentProject()->getName() . '"</i>')); ?>
			<?php else: ?>
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
		<?php elseif (in_array($filter, array('assigned_to', 'posted_by', 'lead_by'))): ?>
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
