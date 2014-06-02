<?php if ($filter instanceof TBGSearchFilter): ?>
	<?php

		switch ($filter->getFilterKey())
		{
			case 'project_id':
				?>
				<?php if (TBGContext::isProjectContext()): ?>
					<input type="hidden" name="fs[project_id][o]" value="=">
					<input type="hidden" name="fs[project_id][v]" value="<?php echo TBGContext::getCurrentProject()->getID(); ?>" id="filter_project_id_value_input">
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
				?>
				<div class="filter interactive_dropdown" id="interactive_filter_<?php echo $filter->getFilterKey(); ?>" data-filterkey="<?php echo $filter->getFilterKey(); ?>" data-value="<?php echo $filter->getValue(); ?>" data-all-value="<?php echo __('Anyone'); ?>">
					<input type="hidden" name="fs[<?php echo $filter->getFilterKey(); ?>][o]" value="<?php echo $filter->getOperator(); ?>">
					<input type="hidden" name="fs[<?php echo $filter->getFilterKey(); ?>][v]" value="" id="filter_<?php echo $filter->getFilterKey(); ?>_value_input">
					<label><?php 
					
						switch ($filter->getFilterKey())
						{
							case 'posted_by':
								echo __('Posted by');
								break;
							case 'owner_user':
								echo __('Owned by user');
								break;
							case 'assignee_user':
								echo __('Assigned user');
								break;
						}
						
					?></label>
					<span class="value"><?php if (!$filter->hasValue()) echo __('Anyone'); ?></span>
					<div class="interactive_menu">
						<h1><?php echo __('Select user(s)'); ?></h1>
						<input type="search" class="interactive_menu_filter" data-callback-url="<?php echo make_url('search_filter_findusers', array('filterkey' => $filter->getFilterKey())); ?>" placeholder="<?php echo __('Search for a user'); ?>"><?php echo image_tag('spinning_16.gif', array('class' => 'filter_indicator')); ?>
						<div class="interactive_values_container">
							<ul class="interactive_menu_values filter_callback_results">
							</ul>
							<ul class="interactive_menu_values filter_existing_values">
								<?php foreach ($filter->getAvailableValues() as $user): ?>
									<li data-value="<?php echo $user->getID(); ?>" class="filtervalue<?php if ($filter->hasValue($user->getID())) echo ' selected'; ?>">
										<?php echo image_tag('icon-mono-checked.png', array('class' => 'checked')); ?>
										<input type="checkbox" value="<?php echo $user->getID(); ?>" name="filters_<?php echo $filter->getFilterKey(); ?>_value_<?php echo $user->getID(); ?>" data-text="<?php echo ($user->getID() == $tbg_user->getID()) ? __('Yourself') : $user->getNameWithUsername(); ?>" id="filters_<?php echo $filter->getFilterKey(); ?>_value_<?php echo $user->getID(); ?>" <?php if ($filter->hasValue($user->getID())) echo 'checked'; ?>>
										<label for="filters_<?php echo $filter->getFilterKey(); ?>_value_<?php echo $user->getID(); ?>"><?php echo ($user->getID() == $tbg_user->getID()) ? __('Yourself').'<span class="hidden">'.$tbg_user->getNameWithUsername().'</span>' : $user->getNameWithUsername(); ?></label>
									</li>
								<?php endforeach; ?>
							</ul>
						</div>
					</div>
					<div class="filter_remove_button" onclick="TBG.Search.removeFilter($(this).up());"><?php echo image_tag('icon-mono-remove.png'); ?></div>
				</div>
				<?php
				break;
			case 'owner_team':
			case 'assignee_team':
				?>
				<div class="filter interactive_dropdown" id="interactive_filter_<?php echo $filter->getFilterKey(); ?>" data-filterkey="<?php echo $filter->getFilterKey(); ?>" data-value="<?php echo $filter->getValue(); ?>" data-all-value="<?php echo __('Anyone'); ?>">
					<input type="hidden" name="fs[<?php echo $filter->getFilterKey(); ?>][o]" value="<?php echo $filter->getOperator(); ?>">
					<input type="hidden" name="fs[<?php echo $filter->getFilterKey(); ?>][v]" value="" id="filter_<?php echo $filter->getFilterKey(); ?>_value_input">
					<label><?php 
					
						switch ($filter->getFilterKey())
						{
							case 'owner_team':
								echo __('Owned by team');
								break;
							case 'assignee_team':
								echo __('Assigned team');
								break;
						}
						
					?></label>
					<span class="value"><?php if (!$filter->hasValue()) echo __('Any team'); ?></span>
					<div class="interactive_menu">
						<h1><?php echo __('Select team(s)'); ?></h1>
						<input type="search" class="interactive_menu_filter" data-callback-url="<?php echo make_url('search_filter_findteams', array('filterkey' => $filter->getFilterKey())); ?>" placeholder="<?php echo __('Search for a team'); ?>"><?php echo image_tag('spinning_16.gif', array('class' => 'filter_indicator')); ?>
						<div class="interactive_values_container">
							<ul class="interactive_menu_values filter_callback_results">
							</ul>
							<ul class="interactive_menu_values filter_existing_values">
								<?php foreach ($filter->getAvailableValues() as $team): ?>
									<li data-value="<?php echo $team->getID(); ?>" class="filtervalue<?php if ($filter->hasValue($team->getID())) echo ' selected'; ?>">
										<?php echo image_tag('icon-mono-checked.png', array('class' => 'checked')); ?>
										<input type="checkbox" value="<?php echo $team->getID(); ?>" name="filters_<?php echo $filter->getFilterKey(); ?>_value_<?php echo $team->getID(); ?>" data-text="<?php echo $team->getName(); ?>" id="filters_<?php echo $filter->getFilterKey(); ?>_value_<?php echo $team->getID(); ?>" <?php if ($filter->hasValue($team->getID())) echo 'checked'; ?>>
										<label for="filters_<?php echo $filter->getFilterKey(); ?>_value_<?php echo $team->getID(); ?>"><?php echo $team->getName(); ?></label>
									</li>
								<?php endforeach; ?>
							</ul>
						</div>
					</div>
					<div class="filter_remove_button" onclick="TBG.Search.removeFilter($(this).up());"><?php echo image_tag('icon-mono-remove.png'); ?></div>
				</div>
				<?php
				break;
			case 'status':
				?>
				<div class="filter interactive_dropdown" data-filterkey="status" data-value="<?php echo $filter->getValue(); ?>" data-all-value="<?php echo __('All'); ?>">
					<input type="hidden" name="fs[status][o]" value="<?php echo $filter->getOperator(); ?>">
					<input type="hidden" name="fs[status][v]" value="" id="filter_status_value_input">
					<label><?php echo __('Status'); ?></label>
					<span class="value"><?php if (!$filter->hasValue()) echo __('All'); ?></span>
					<div class="interactive_menu">
						<h1><?php echo __('Filter on status'); ?></h1>
						<input type="search" class="interactive_menu_filter" placeholder="<?php echo __('Filter values'); ?>">
						<div class="interactive_values_container">
							<ul class="interactive_menu_values">
								<li data-value="open" class="filtervalue <?php if ($filter->hasValue('open')) echo ' selected'; ?>" data-exclusive data-selection-group="1">
									<?php echo image_tag('icon-mono-checked.png', array('class' => 'checked')); ?>
									<input type="checkbox" value="open" name="filters_status_value_open" data-text="<?php echo __('Only open issues'); ?>" id="filters_status_value_open" <?php if ($filter->hasValue('open')) echo 'checked'; ?>>
									<label for="filters_status_value_open"><?php echo __('Only open issues'); ?></label>
								</li>
								<li data-value="closed" class="filtervalue <?php if ($filter->hasValue('closed')) echo ' selected'; ?>" data-exclusive data-selection-group="1">
									<?php echo image_tag('icon-mono-checked.png', array('class' => 'checked')); ?>
									<input type="checkbox" value="closed" name="filters_status_value_closed" data-text="<?php echo __('Only closed issues'); ?>" id="filters_status_value_closed" <?php if ($filter->hasValue('closed')) echo 'checked'; ?>>
									<label for="filters_status_value_closed"><?php echo __('Only closed issues'); ?></label>
								</li>
								<li class="separator"></li>
								<?php foreach ($filter->getAvailableValues() as $status): ?>
									<li data-value="<?php echo $status->getID(); ?>" class="filtervalue<?php if ($filter->hasValue($status->getID())) echo ' selected'; ?>">
										<?php echo image_tag('icon-mono-checked.png', array('class' => 'checked')); ?>
										<input type="checkbox" value="<?php echo $status->getID(); ?>" name="filters_status_value_<?php echo $status->getID(); ?>" data-text="<?php echo __($status->getName()); ?>" id="filters_status_value_<?php echo $status->getID(); ?>" <?php if ($filter->hasValue($status->getID())) echo 'checked'; ?>>
										<label for="filters_status_value_<?php echo $status->getID(); ?>"><?php echo __($status->getName()); ?></label>
									</li>
								<?php endforeach; ?>
							</ul>
						</div>
					</div>
				</div>
				<?php
				break;
			case 'category':
				?>
				<div class="filter interactive_dropdown" data-filterkey="category" data-value="<?php echo $filter->getValue(); ?>" data-all-value="<?php echo __('All'); ?>">
					<input type="hidden" name="fs[category][o]" value="<?php echo $filter->getOperator(); ?>">
					<input type="hidden" name="fs[category][v]" value="" id="filter_category_value_input">
					<label><?php echo __('Category'); ?></label>
					<span class="value"><?php if (!$filter->hasValue()) echo __('All'); ?></span>
					<div class="interactive_menu">
						<h1><?php echo __('Filter on category'); ?></h1>
						<input type="search" class="interactive_menu_filter" placeholder="<?php echo __('Filter values'); ?>">
						<div class="interactive_values_container">
							<ul class="interactive_menu_values">
								<?php foreach ($filter->getAvailableValues() as $category): ?>
									<li data-value="<?php echo $category->getID(); ?>" class="filtervalue<?php if ($filter->hasValue($category->getID())) echo ' selected'; ?>">
										<?php echo image_tag('icon-mono-checked.png', array('class' => 'checked')); ?>
										<input type="checkbox" value="<?php echo $category->getID(); ?>" name="filters_category_value_<?php echo $category->getID(); ?>" data-text="<?php echo __($category->getName()); ?>" id="filters_category_value_<?php echo $category->getID(); ?>" <?php if ($filter->hasValue($category->getID())) echo 'checked'; ?>>
										<label for="filters_category_value_<?php echo $category->getID(); ?>"><?php echo __($category->getName()); ?></label>
									</li>
								<?php endforeach; ?>
							</ul>
						</div>
					</div>
				</div>
				<?php
				break;
			case 'build':
			case 'component':
			case 'edition':
			case 'milestone':
				?>
				<div class="filter interactive_dropdown" id="interactive_filter_<?php echo $filter->getFilterKey(); ?>" data-filterkey="<?php echo $filter->getFilterKey(); ?>" data-value="<?php echo $filter->getValue(); ?>" data-all-value="<?php echo __('Any'); ?>">
					<input type="hidden" name="fs[<?php echo $filter->getFilterKey(); ?>][o]" value="<?php echo $filter->getOperator(); ?>">
					<input type="hidden" name="fs[<?php echo $filter->getFilterKey(); ?>][v]" value="" id="filter_<?php echo $filter->getFilterKey(); ?>_value_input">
					<label><?php

							switch ($filter->getFilterKey())
							{
								case 'build':
									echo __('Affects release(s)');
									break;
								case 'component':
									echo __('Affects component(s)');
									break;
								case 'edition':
									echo __('Affects edition(s)');
									break;
								case 'milestone':
									echo __('Targetted milestone(s)');
									break;
							}

					?></label>
					<span class="value"><?php if (!$filter->hasValue()) echo __('Any'); ?></span>
					<div class="interactive_menu wider">
						<h1><?php

								switch ($filter->getFilterKey())
								{
									case 'build':
										echo __('Filter on affected release(s)');
										break;
									case 'component':
										echo __('Filter on affected component(s)');
										break;
									case 'edition':
										echo __('Filter on affected edition(s)');
										break;
									case 'milestone':
										echo __('Filter on targetted milestone(s)');
										break;
								}

						?></h1>
						<input type="search" class="interactive_menu_filter" placeholder="<?php echo __('Filter values'); ?>">
						<div class="interactive_values_container">
							<ul class="interactive_menu_values">
								<?php include_template('search/interactivefilterdynamicchoicelist', array('filter' => $filter, 'items' => $filter->getAvailableValues())); ?>
							</ul>
						</div>
					</div>
					<div class="filter_remove_button" onclick="TBG.Search.removeFilter($(this).up());"><?php echo image_tag('icon-mono-remove.png'); ?></div>
				</div>
				<?php
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
				include_template('search/interactivefilter_choice', compact('filter'));
				break;
			case 'posted':
			case 'last_updated':
				include_template('search/interactivefilter_date', compact('filter'));
				break;
			default:
				if (!in_array($filter->getFilterKey(), TBGSearchFilter::getValidSearchFilters()))
				{
					switch ($filter->getFilterType())
					{
						case TBGCustomDatatype::DATE_PICKER:
							include_template('search/interactivefilter_date', compact('filter'));
							break;
						case TBGCustomDatatype::CHECKBOX_CHOICES:
						case TBGCustomDatatype::RADIO_CHOICE:
							include_template('search/interactivefilter_choice', compact('filter'));
							break;
						case TBGCustomDatatype::INPUT_TEXT:
						case TBGCustomDatatype::INPUT_TEXTAREA_MAIN:
						case TBGCustomDatatype::INPUT_TEXTAREA_SMALL:
							include_template('search/interactivefilter_text', compact('filter'));
							break;
					}
				}
		}

	?>
<?php else: ?>
	<?php var_dump($filter); ?>
<?php endif; ?>
