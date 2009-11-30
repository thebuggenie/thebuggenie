<?php if (in_array($filter, B2tIssues::getValidSearchFilters())): ?>
	<li id="filter_<?php echo $key; ?>">
		<?php if ($filter == 'project_id' && !BUGScontext::isProjectContext()): ?>
			<label<?php if (!BUGScontext::isProjectContext()): ?> for="filter_project_id_<?php echo $key; ?>"<?php endif; ?>><?php echo __('Project'); ?></label>
			<?php if (BUGScontext::isProjectContext()): ?>
				<?php //echo __('%project% is %project_name%', array('%project%' => '', '%project_name%' => '<i>"' . BUGScontext::getCurrentProject()->getName() . '"</i>')); ?>
			<?php else: ?>
				<select name="filters[project_id][<?php echo $key; ?>][operator]">
					<option value="="<?php if ($selected_operator == '='): ?> selected<?php endif; ?>><?php echo __('%field% is %value%', array('%field%' => '', '%value%' => '')); ?></option>
					<option value="!="<?php if ($selected_operator == '!='): ?> selected<?php endif; ?>><?php echo __('%field% is not %value%', array('%field%' => '', '%value%' => '')); ?></option>
				</select>
				<select name="filters[project_id][<?php echo $key; ?>][value]" id="filter_project_id_<?php echo $key; ?>">
					<?php foreach (BUGSproject::getAll() as $project): ?>
						<option value="<?php echo $project->getID(); ?>"<?php if ($selected_value == $project->getID()): ?> selected<?php endif; ?>><?php echo $project->getName(); ?></option>
					<?php endforeach; ?>
				</select>
				<a class="image" href="javascript:void(0);" onclick="removeSearchFilter(<?php echo $key; ?>);"><?php echo image_tag('action_remove_small.png', array('style' => 'margin-left: 5px;')); ?></a>
			<?php endif; ?>
		<?php elseif ($filter == 'status'): ?>
			<label for="filter_status_<?php echo $key; ?>"><?php echo __('Status'); ?></label>
			<select name="filters[status][<?php echo $key; ?>][operator]">
				<option value="="<?php if ($selected_operator == '='): ?> selected<?php endif; ?>><?php echo __('%field% is %value%', array('%field%' => '', '%value%' => '')); ?></option>
				<option value="!="<?php if ($selected_operator == '!='): ?> selected<?php endif; ?>><?php echo __('%field% is not %value%', array('%field%' => '', '%value%' => '')); ?></option>
			</select>
			<select name="filters[status][<?php echo $key; ?>][value]" id="filter_project_id_<?php echo $key; ?>">
				<option value="0"> - </option>
				<?php foreach (BUGSstatus::getAll() as $status): ?>
					<option value="<?php echo $status->getID(); ?>"<?php if ($selected_value == $status->getID()): ?> selected<?php endif; ?>><?php echo $status->getName(); ?></option>
				<?php endforeach; ?>
			</select>
			<a class="image" href="javascript:void(0);" onclick="removeSearchFilter(<?php echo $key; ?>);"><?php echo image_tag('action_remove_small.png', array('style' => 'margin-left: 5px;')); ?></a>
		<?php elseif ($filter == 'resolution'): ?>
			<label for="filter_resolution_<?php echo $key; ?>"><?php echo __('Resolution'); ?></label>
			<select name="filters[resolution][<?php echo $key; ?>][operator]">
				<option value="="<?php if ($selected_operator == '='): ?> selected<?php endif; ?>><?php echo __('%field% is %value%', array('%field%' => '', '%value%' => '')); ?></option>
				<option value="!="<?php if ($selected_operator == '!='): ?> selected<?php endif; ?>><?php echo __('%field% is not %value%', array('%field%' => '', '%value%' => '')); ?></option>
			</select>
			<select name="filters[resolution][<?php echo $key; ?>][value]" id="filter_project_id_<?php echo $key; ?>">
				<option value="0"> - </option>
				<?php foreach (BUGSresolution::getAll() as $resolution): ?>
					<option value="<?php echo $resolution->getID(); ?>"<?php if ($selected_value == $resolution->getID()): ?> selected<?php endif; ?>><?php echo $resolution->getName(); ?></option>
				<?php endforeach; ?>
			</select>
			<a class="image" href="javascript:void(0);" onclick="removeSearchFilter(<?php echo $key; ?>);"><?php echo image_tag('action_remove_small.png', array('style' => 'margin-left: 5px;')); ?></a>
		<?php elseif ($filter == 'state'): ?>
			<label for="filter_state_<?php echo $key; ?>"><?php echo __('Issue state'); ?></label>
			<select name="filters[state][<?php echo $key; ?>][operator]">
				<option value="="<?php if ($selected_operator == '='): ?> selected<?php endif; ?>><?php echo __('%field% is %value%', array('%field%' => '', '%value%' => '')); ?></option>
				<option value="!="<?php if ($selected_operator == '!='): ?> selected<?php endif; ?>><?php echo __('%field% is not %value%', array('%field%' => '', '%value%' => '')); ?></option>
			</select>
			<select name="filters[state][<?php echo $key; ?>][value]" id="filter_state_<?php echo $key; ?>">
				<option value="<?php echo BUGSissue::STATE_OPEN; ?>"<?php if ($selected_value == BUGSissue::STATE_OPEN): ?> selected<?php endif; ?>><?php echo __('Open'); ?></option>
				<option value="<?php echo BUGSissue::STATE_CLOSED; ?>"<?php if ($selected_value == BUGSissue::STATE_CLOSED): ?> selected<?php endif; ?>><?php echo __('Closed'); ?></option>
			</select>
			<a class="image" href="javascript:void(0);" onclick="removeSearchFilter(<?php echo $key; ?>);"><?php echo image_tag('action_remove_small.png', array('style' => 'margin-left: 5px;')); ?></a>
		<?php endif; ?>
	</li>
<?php endif; ?>