<tr class="milestone_issue_row<?php if ($issue->isClosed()): ?> issue_closed<?php endif; ?>" id="issue_<?php echo $issue->getID(); ?>">
	<?php if ($issue->canEditIssue()): ?>
		<td style="padding: 2px;"><input type="checkbox" name="update_issue[<?php echo $issue->getID(); ?>]" onclick="TBG.Search.toggleCheckbox(this);" value="<?php echo $issue->getID(); ?>"></td>
	<?php endif; ?>
	<td>
		<?php if ($issue->canEditIssue()): ?>
			<input type="hidden" name="issue_id[<?php echo $issue->getID(); ?>]" value="<?php echo $issue->getID(); ?>">
		<?php endif; ?>
		<div id="issue_<?php echo $issue->getID(); ?>_draggable">
			<?php if ($issue->canEditIssue()): ?>
				<div style="display: none;" class="rounded_box shadowed white story_color_selector" id="color_selector_<?php echo $issue->getID(); ?>">
					<div>
						<div class="header" style="margin-left: 5px;"><?php echo __('Pick a planning color for this issue'); ?></div>
						<div style="margin-left: 5px;"><?php echo __('Selecting a color makes the issue easily recognizable in the planning view'); ?>.</div>
						<?php echo image_tag('spinning_20.gif', array('id' => 'color_selector_'.$issue->getID().'_indicator', 'style' => 'position: absolute; right: 2px; top: 2px; display: none;')); ?>
					</div>
					<div class="color_items">
						<?php foreach ($colors as $color): ?>
							<div <?php if ($issue->canEditIssue()): ?>onclick="TBG.Project.Scrum.Story.setColor('<?php echo make_url('project_scrum_story_setcolor', array('project_key' => $issue->getProject()->getKey(), 'story_id' => $issue->getID())); ?>', <?php echo $issue->getID(); ?>, '<?php echo $color; ?>');" <?php endif; ?>class="story_color_selector_item" style="background-color: <?php echo $color; ?>;">&nbsp;</div>
						<?php endforeach; ?>
					</div>
					<br style="clear: both;">
					<div style="margin: 5px;">
						<?php echo javascript_link_tag(__('%color_list% or keep the current color', array('%color_list%' => '')), array('onclick' => "$('color_selector_{$issue->getID()}').toggle()")); ?>
					</div>
				</div>
			<?php endif; ?>
			<div class="story_color" id="story_color_<?php echo $issue->getID(); ?>" <?php if ($issue->canEditIssue()): ?>onclick="$('color_selector_<?php echo $issue->getID(); ?>').toggle();"<?php endif; ?> style="<?php if ($issue->canEditIssue()): ?>cursor: pointer; <?php endif; ?>background-color: <?php echo $issue->getScrumColor(); ?>;">&nbsp;</div>
			<?php if ($issue->canEditIssue()): ?>
				<div class="draggable" id="issue_<?php echo $issue->getID(); ?>_handle">
					<span></span><span></span><span></span>
				</div>
				<input type="hidden" id="issue_<?php echo $issue->getID(); ?>_id" value="<?php echo $issue->getID(); ?>">
			<?php endif; ?>
			<?php echo link_tag(make_url('viewissue', array('issue_no' => $issue->getFormattedIssueNo(), 'project_key' => $issue->getProject()->getKey())), $issue->getFormattedTitle()); ?>
		</div>
	</td>
	<td>
		<?php if ($issue->isAssigned()): ?>
			<?php if ($issue->getAssignee() instanceof TBGUser): ?>
				<?php echo include_component('main/userdropdown', array('user' => $issue->getAssignee())); ?>
			<?php else: ?>
				<?php echo include_component('main/teamdropdown', array('team' => $issue->getAssignee())); ?>
			<?php endif; ?>
		<?php else: ?>
			-
		<?php endif; ?>
	</td>
	<td class="sc_status"><span class="sc_status_name"><?php echo ($issue->getStatus() instanceof TBGStatus) ? $issue->getStatus()->getName() : '-' ?></span></td>
	<?php if ($issue->canEditIssue()): ?>
		<?php foreach ($selected_columns as $key => $data):
			if (!isset($all_columns[$key])) { continue; }
			if ($key == 'estimated_time' || $key == 'spent_time') { continue; /* Handle these last */ }
			if (!$issue->isFieldVisible($key)) {
				echo "<td>-</td>";
				continue;
			}
			$fielddata = $all_columns[$key];

			switch ($key) {
				case 'priority': ?>
					<td class="milestoneupdateable">
						<select name="priority[<?php echo $issue->getID(); ?>]" id="priority_selector_<?php echo $issue->getID(); ?>">
							<option value="0"<?php if (!$issue->getPriority() instanceof TBGPriority): ?> selected<?php endif; ?>>--</option>
							<?php foreach (TBGPriority::getAll() as $p_id => $priority): ?>
								<option value="<?php echo $p_id; ?>"<?php if ($issue->getPriority() instanceof TBGPriority && $issue->getPriority()->getID() == $p_id): ?> selected<?php endif; ?>><?php echo $priority->getName(); ?></option>
							<?php endforeach; ?>
						</select>
					</td>
				<?php break;
				case 'severity': ?>
					<td class="milestoneupdateable">
						<select name="severity[<?php echo $issue->getID(); ?>]" id="severity_selector_<?php echo $issue->getID(); ?>">
							<option value="0"<?php if (!$issue->getSeverity() instanceof TBGSeverity): ?> selected<?php endif; ?>>--</option>
							<?php foreach (TBGSeverity::getAll() as $s_id => $severity): ?>
								<option value="<?php echo $s_id; ?>"<?php if ($issue->getSeverity() instanceof TBGSeverity && $issue->getSeverity()->getID() == $s_id): ?> selected<?php endif; ?>><?php echo $severity->getName(); ?></option>
							<?php endforeach; ?>
						</select>
					</td>
				<?php break;
				case 'reproducability': ?>
					<td class="milestoneupdateable">
						<select name="reproducability[<?php echo $issue->getID(); ?>]" id="reproducability_selector_<?php echo $issue->getID(); ?>">
							<option value="0"<?php if (!$issue->getReproducability() instanceof TBGReproducability): ?> selected<?php endif; ?>>--</option>
							<?php foreach (TBGReproducability::getAll() as $r_id => $reproducability): ?>
								<option value="<?php echo $r_id; ?>"<?php if ($issue->getReproducability() instanceof TBGReproducability && $issue->getReproducability()->getID() == $r_id): ?> selected<?php endif; ?>><?php echo $reproducability->getName(); ?></option>
							<?php endforeach; ?>
						</select>
					</td>
				<?php break;
				case 'category': ?>
					<td class="milestoneupdateable">
						<select name="category[<?php echo $issue->getID(); ?>]" id="category_selector_<?php echo $issue->getID(); ?>">
							<option value="0"<?php if (!$issue->getCategory() instanceof TBGCategory): ?> selected<?php endif; ?>>--</option>
							<?php foreach (TBGCategory::getAll() as $c_id => $category): ?>
								<option value="<?php echo $c_id; ?>"<?php if ($issue->getCategory() instanceof TBGCategory && $issue->getCategory()->getID() == $c_id): ?> selected<?php endif; ?>><?php echo $category->getName(); ?></option>
							<?php endforeach; ?>
						</select>
					</td>
				<?php break;
				case 'percent_complete':
					?> <td class="milestoneupdateable pointsandtime"><input type="text" value="<?php echo $issue->getFieldValue($key); ?>" name="percent_complete[<?php echo $issue->getID(); ?>]"></td> <?php
					break;
				default:

					switch($fielddata['type']) {
						case TBGCustomDatatype::INPUT_TEXT:
							?> <td class="milestoneupdateable"><input type="text" value="<?php echo $issue->getFieldValue($key); ?>" name="customfield[<?php echo $key ?>][<?php echo $issue->getID(); ?>]"></td> <?php
							break;
						case TBGCustomDatatype::COMPONENTS_CHOICE:
						case TBGCustomDatatype::RELEASES_CHOICE:
						case TBGCustomDatatype::EDITIONS_CHOICE:
						case TBGCustomDatatype::STATUS_CHOICE:
							$classnames = array(
								TBGCustomDatatype::COMPONENTS_CHOICE => 'TBGComponent',
								TBGCustomDatatype::RELEASES_CHOICE => 'TBGBuild',
								TBGCustomDatatype::EDITIONS_CHOICE => 'TBGEdition',
								TBGCustomDatatype::STATUS_CHOICE => 'TBGStatus'
							);

							try {
								$item = new $classnames[$fielddata['type']]($issue->getFieldValue($key));
								$name = $item->getName();
							} catch (Exception $e) {
								$name = '';
							}
							?> <td><?php echo $name; ?></td> <?php
							break;
						case TBGCustomDatatype::DROPDOWN_CHOICE_TEXT:
						case TBGCustomDatatype::RADIO_CHOICE:
							$customtype = TBGCustomDatatype::getByKey($key);
							$options = $customtype->getOptions();
							?><td class="milestoneupdateable">
								<select name="customfield[<?php echo $key ?>][<?php echo $issue->getID(); ?>]" id="customfield_<?php echo $key; ?>_selector_<?php echo $issue->getID(); ?>">
									<option value=""<?php if (!$issue->getFieldValue($key)): ?> selected<?php endif; ?>>--</option>
									<?php foreach ($options as $opt_id => $option): ?>
										<option value="<?php echo $option->getID(); ?>"<?php if ($option->getName() == $issue->getFieldValue($key)): ?> selected<?php endif; ?>><?php echo $option->getName(); ?></option>
									<?php endforeach; ?>
								</select>
							</td><?php
							break;
						default:
							?><td><?php echo $issue->getFieldValue($key); ?></td><?php
							break;
					}

					break;
			}
		endforeach; ?>

		<?php if (isset($selected_columns['estimated_time'])) : ?>
		<td class="milestoneupdateable pointsandtime"><input type="text" value="<?php echo $issue->getEstimatedHours(); ?>" name="estimated_hours[<?php echo $issue->getID(); ?>]"></td>
		<td class="milestoneupdateable pointsandtime"><input type="text" value="<?php echo $issue->getEstimatedPoints(); ?>" name="estimated_points[<?php echo $issue->getID(); ?>]"></td>
		<?php endif; ?>

		<?php if (isset($selected_columns['spent_time'])) : ?>
		<td class="milestoneupdateable pointsandtime"><input type="text" value="<?php echo $issue->getSpentHours(); ?>" name="spent_hours[<?php echo $issue->getID(); ?>]"></td>
		<td class="milestoneupdateable pointsandtime last"><input type="text" value="<?php echo $issue->getSpentPoints(); ?>" name="spent_points[<?php echo $issue->getID(); ?>]"></td>
		<?php endif; ?>
	<?php else: ?>
		<?php foreach ($selected_columns as $key => $data):
			if ($key == 'estimated_time' || $key == 'spent_time') { continue; }
		?>
			<td><?php echo $issue->getFieldValue($key); ?></td>
		<?php endforeach ?>

		<?php if (isset($selected_columns['estimated_time'])) : ?>
		<td class="pointsandtime"><?php echo $issue->getEstimatedHours(); ?></td>
		<td class="pointsandtime"><?php echo $issue->getEstimatedPoints(); ?></td>
		<?php endif; ?>

		<?php if (isset($selected_columns['spent_time'])) : ?>
		<td class="pointsandtime"><?php echo $issue->getSpentHours(); ?></td>
		<td class="pointsandtime"><?php echo $issue->getSpentPoints(); ?></td>
		<?php endif; ?>
	<?php endif; ?>
</tr>