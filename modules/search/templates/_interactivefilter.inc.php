<?php if ($filter instanceof TBGSearchFilter): ?>
	<?php

		switch ($filter->getFilterKey())
		{
			case 'issuetype':
				?>
				<div class="filter interactive_dropdown" data-filterkey="issuetype" data-value="<?php echo $filter->getValue(); ?>" data-all-value="<?php echo __('All'); ?>">
					<input type="hidden" name="filters[issuetype][operator]" value="=">
					<input type="hidden" name="filters[issuetype][value]" value="" id="filter_issuetype_value_input">
					<label><?php echo __('Issuetype'); ?></label>
					<span class="value"><?php if (!$filter->hasValue()) echo __('All'); ?></span>
					<div class="interactive_menu">
						<h1><?php echo __('Filter on issuetype'); ?></h1>
						<input type="search" class="interactive_menu_filter" placeholder="<?php echo __('Filter values'); ?>">
						<ul class="interactive_menu_values">
							<?php foreach ($filter->getAvailableValues() as $issuetype): ?>
								<li data-value="<?php echo $issuetype->getID(); ?>" class="filtervalue<?php if ($filter->hasValue($issuetype->getID())) echo ' selected'; ?>">
									<input type="checkbox" value="<?php echo $issuetype->getID(); ?>" name="filters_issuetype_value_<?php echo $issuetype->getID(); ?>" data-text="<?php echo __($issuetype->getName()); ?>" id="filters_issuetype_value_<?php echo $issuetype->getID(); ?>" <?php if ($filter->hasValue($issuetype->getID())) echo 'checked'; ?>>
									<label for="filters_issuetype_value_<?php echo $issuetype->getID(); ?>"><?php echo __($issuetype->getName()); ?></label>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				</div>
				<?php
				break;
			case 'status':
				?>
				<div class="filter interactive_dropdown" data-filterkey="status" data-value="<?php echo $filter->getValue(); ?>" data-all-value="<?php echo __('All'); ?>">
					<input type="hidden" name="filters[status][operator]" value="=">
					<input type="hidden" name="filters[status][value]" value="" id="filter_status_value_input">
					<label><?php echo __('Status'); ?></label>
					<span class="value"><?php if (!$filter->hasValue()) echo __('All'); ?></span>
					<div class="interactive_menu">
						<h1><?php echo __('Filter on status'); ?></h1>
						<input type="search" class="interactive_menu_filter" placeholder="<?php echo __('Filter values'); ?>">
						<ul class="interactive_menu_values">
							<li data-value="open" class="filtervalue <?php if ($filter->hasValue('open')) echo ' selected'; ?>">
								<input type="checkbox" value="open" name="filters_status_value_open" value="open" data-text="<?php echo __('Open'); ?>" id="filters_status_value_open" <?php if ($filter->hasValue('open')) echo 'checked'; ?>>
								<label for="filters_status_value_open"><?php echo __('Open'); ?></label>
							</li>
							<li data-value="closed" class="filtervalue <?php if ($filter->hasValue('closed')) echo ' selected'; ?>">
								<input type="checkbox" value="closed" name="filters_status_value_closed" value="closed" data-text="<?php echo __('Closed'); ?>" id="filters_status_value_closed" <?php if ($filter->hasValue('closed')) echo 'checked'; ?>>
								<label for="filters_status_value_closed"><?php echo __('Closed'); ?></label>
							</li>
							<li class="separator"></li>
							<?php foreach ($filter->getAvailableValues() as $status): ?>
								<li data-value="<?php echo $status->getID(); ?>" class="filtervalue<?php if ($filter->hasValue($status->getID())) echo ' selected'; ?>">
									<input type="checkbox" value="<?php echo $status->getID(); ?>" name="filters_status_value_<?php echo $status->getID(); ?>" data-text="<?php echo __($status->getName()); ?>" id="filters_status_value_<?php echo $status->getID(); ?>" <?php if ($filter->hasValue($status->getID())) echo 'checked'; ?>>
									<label for="filters_status_value_<?php echo $status->getID(); ?>"><?php echo __($status->getName()); ?></label>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				</div>
				<?php
				break;
			case 'category':
				?>
				<div class="filter interactive_dropdown" data-filterkey="category" data-value="<?php echo $filter->getValue(); ?>" data-all-value="<?php echo __('All'); ?>">
					<input type="hidden" name="filters[category][operator]" value="=">
					<input type="hidden" name="filters[category][value]" value="" id="filter_category_value_input">
					<label><?php echo __('Category'); ?></label>
					<span class="value"><?php if (!$filter->hasValue()) echo __('All'); ?></span>
					<div class="interactive_menu">
						<h1><?php echo __('Filter on category'); ?></h1>
						<input type="search" class="interactive_menu_filter" placeholder="<?php echo __('Filter values'); ?>">
						<ul class="interactive_menu_values">
							<?php foreach ($filter->getAvailableValues() as $category): ?>
								<li data-value="<?php echo $category->getID(); ?>" class="filtervalue<?php if ($filter->hasValue($category->getID())) echo ' selected'; ?>">
									<input type="checkbox" value="<?php echo $category->getID(); ?>" name="filters_category_value_<?php echo $category->getID(); ?>" data-text="<?php echo __($category->getName()); ?>" id="filters_category_value_<?php echo $category->getID(); ?>" <?php if ($filter->hasValue($category->getID())) echo 'checked'; ?>>
									<label for="filters_category_value_<?php echo $category->getID(); ?>"><?php echo __($category->getName()); ?></label>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				</div>
				<?php
				break;
			case 'priority':
			case 'resolution':
			case 'reproducability':
			case 'severity':
				$key = $filter->getFilterKey();
				switch ($key)
				{
					case 'priority':
						$title = __('Priority');
						$description = __('Filter on priority');
						break;
					case 'resolution':
						$title = __('Resolution');
						$description = __('Filter on resolution');
						break;
					case 'severity':
						$title = __('Severity');
						$description = __('Filter on severity');
						break;
					case 'reproducability':
						$title = __('Reproducability');
						$description= __('Filter on reproducability');
						break;
				}
				?>
				<div class="filter interactive_dropdown" id="interactive_filter_<?php echo $key; ?>" data-filterkey="<?php echo $key; ?>" data-value="<?php echo $filter->getValue(); ?>" data-all-value="<?php echo __('All'); ?>">
					<input type="hidden" name="filters[<?php echo $key; ?>][operator]" value="=">
					<input type="hidden" name="filters[<?php echo $key; ?>][value]" value="" id="filter_<?php echo $key; ?>_value_input">
					<label><?php echo $title; ?></label>
					<span class="value"><?php if (!$filter->hasValue()) echo __('All'); ?></span>
					<div class="interactive_menu">
						<h1><?php echo $description; ?></h1>
						<input type="search" class="interactive_menu_filter" placeholder="<?php echo __('Filter values'); ?>">
						<ul class="interactive_menu_values">
							<?php foreach ($filter->getAvailableValues() as $value): ?>
								<li data-value="<?php echo $value->getID(); ?>" class="filtervalue<?php if ($filter->hasValue($value->getID())) echo ' selected'; ?>">
									<input type="checkbox" value="<?php echo $value->getID(); ?>" name="filters_<?php echo $key; ?>_value_<?php echo $value->getID(); ?>" data-text="<?php echo __($value->getName()); ?>" id="filters_<?php echo $key; ?>_value_<?php echo $value->getID(); ?>" <?php if ($filter->hasValue($value->getID())) echo 'checked'; ?>>
									<label for="filters_<?php echo $key; ?>_value_<?php echo $value->getID(); ?>"><?php echo __($value->getName()); ?></label>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
					<div class="filter_remove_button" onclick="TBG.Search.removeFilter($(this).up());"><?php echo image_tag('action_delete.png'); ?></div>
				</div>
				<?php
				break;
		}

	?>
<?php else: ?>
	<?php var_dump($filter); ?>
<?php endif; ?>
