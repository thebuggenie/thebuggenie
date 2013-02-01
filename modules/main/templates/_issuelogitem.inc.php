<?php if ($item instanceof TBGLogItem): ?>
	<li>
		<span class="date"><?php echo tbg_formatTime($item->getTime(), 6); ?></span>&nbsp;
		<?php

			$previous_value = null;
			$new_value = null;
			try
			{
				switch($item->getChangeType())
				{
					case TBGLogTable::LOG_ISSUE_CREATED:
						echo __('The issue was created');
						break;
					case TBGLogTable::LOG_ISSUE_CLOSE:
						echo __('The issue was closed');
						break;
					case TBGLogTable::LOG_ISSUE_REOPEN:
						echo __('The issue was reopened');
						break;
					case TBGLogTable::LOG_ISSUE_DEPENDS:
						echo __('The issue was reopened');
						break;
					case TBGLogTable::LOG_ISSUE_STATUS:
						echo image_tag('icon_status.png');
						if ($item->hasChangeDetails())
						{
							$previous_value = ($item->getPreviousValue()) ? (($old_item = TBGContext::factory()->TBGStatus($item->getPreviousValue())) ? __($old_item->getName()) : __('Unknown')) : __('Not determined');
							$new_value = ($item->getCurrentValue()) ? (($new_item = TBGContext::factory()->TBGStatus($item->getCurrentValue())) ? __($new_item->getName()) : __('Unknown')) : __('Not determined');
							echo __("Status changed: %previous_value% => %new_value%", array('%previous_value%' => '<strong>'.$previous_value.'</strong>', '%new_value%' => '<strong>'.$new_value.'</strong>'));
						}
						break;
					case TBGLogTable::LOG_ISSUE_RESOLUTION:
						echo image_tag('icon_resolution.png');
						if ($item->hasChangeDetails())
						{
							$previous_value = ($item->getPreviousValue()) ? (($old_item = TBGContext::factory()->TBGResolution($item->getPreviousValue())) ? __($old_item->getName()) : __('Unknown')) : __('Not determined');
							$new_value = ($item->getCurrentValue()) ? (($new_item = TBGContext::factory()->TBGResolution($item->getCurrentValue())) ? __($new_item->getName()) : __('Unknown')) : __('Not determined');
							echo __("Resolution changed: %previous_value% => %new_value%", array('%previous_value%' => '<strong>'.$previous_value.'</strong>', '%new_value%' => '<strong>'.$new_value.'</strong>'));
						}
						break;
					case TBGLogTable::LOG_ISSUE_PRIORITY:
						echo image_tag('icon_priority.png');
						if ($item->hasChangeDetails())
						{
							$previous_value = ($item->getPreviousValue()) ? (($old_item = TBGContext::factory()->TBGPriority($item->getPreviousValue())) ? __($old_item->getName()) : __('Unknown')) : __('Not determined');
							$new_value = ($item->getCurrentValue()) ? (($new_item = TBGContext::factory()->TBGPriority($item->getCurrentValue())) ? __($new_item->getName()) : __('Unknown')) : __('Not determined');
							echo __("Priority changed: %previous_value% => %new_value%", array('%previous_value%' => '<strong>'.$previous_value.'</strong>', '%new_value%' => '<strong>'.$new_value.'</strong>'));
						}
						break;
					case TBGLogTable::LOG_ISSUE_SEVERITY:
						echo image_tag('icon_severity.png');
						if ($item->hasChangeDetails())
						{
							$previous_value = ($item->getPreviousValue()) ? (($old_item = TBGContext::factory()->TBGSeverity($item->getPreviousValue())) ? __($old_item->getName()) : __('Unknown')) : __('Not determined');
							$new_value = ($item->getCurrentValue()) ? (($new_item = TBGContext::factory()->TBGSeverity($item->getCurrentValue())) ? __($new_item->getName()) : __('Unknown')) : __('Not determined');
							echo __("Severity changed: %previous_value% => %new_value%", array('%previous_value%' => '<strong>'.$previous_value.'</strong>', '%new_value%' => '<strong>'.$new_value.'</strong>'));
						}
						break;
					case TBGLogTable::LOG_ISSUE_REPRODUCABILITY:
						echo image_tag('icon_reproducability.png');
						if ($item->hasChangeDetails())
						{
							$previous_value = ($item->getPreviousValue()) ? (($old_item = TBGContext::factory()->TBGReproducability($item->getPreviousValue())) ? __($old_item->getName()) : __('Unknown')) : __('Not determined');
							$new_value = ($item->getCurrentValue()) ? (($new_item = TBGContext::factory()->TBGReproducability($item->getCurrentValue())) ? __($new_item->getName()) : __('Unknown')) : __('Not determined');
							echo __("Reproducability changed: %previous_value% => %new_value%", array('%previous_value%' => '<strong>'.$previous_value.'</strong>', '%new_value%' => '<strong>'.$new_value.'</strong>'));
						}
						break;
					case TBGLogTable::LOG_ISSUE_ISSUETYPE:
						echo image_tag('icon_issuetype.png');
						if ($item->hasChangeDetails())
						{
							$previous_value = ($item->getPreviousValue()) ? (($old_item = TBGContext::factory()->TBGIssuetype($item->getPreviousValue())) ? __($old_item->getName()) : __('Unknown')) : __('Not determined');
							$new_value = ($item->getCurrentValue()) ? (($new_item = TBGContext::factory()->TBGIssuetype($item->getCurrentValue())) ? __($new_item->getName()) : __('Unknown')) : __('Not determined');
							echo __("Issuetype changed: %previous_value% => %new_value%", array('%previous_value%' => '<strong>'.$previous_value.'</strong>', '%new_value%' => '<strong>'.$new_value.'</strong>'));
						}
						break;
					case TBGLogTable::LOG_ISSUE_CATEGORY:
						echo image_tag('icon_category.png');
						if ($item->hasChangeDetails())
						{
							$previous_value = ($item->getPreviousValue()) ? (($old_item = TBGContext::factory()->TBGCategory($item->getPreviousValue())) ? __($old_item->getName()) : __('Unknown')) : __('Not determined');
							$new_value = ($item->getCurrentValue()) ? (($new_item = TBGContext::factory()->TBGCategory($item->getCurrentValue())) ? __($new_item->getName()) : __('Unknown')) : __('Not determined');
							echo __("Category changed: %previous_value% => %new_value%", array('%previous_value%' => '<strong>'.$previous_value.'</strong>', '%new_value%' => '<strong>'.$new_value.'</strong>'));
						}
						break;
					case TBGLogTable::LOG_ISSUE_MILESTONE:
						echo image_tag('icon_milestone.png');
						if ($item->hasChangeDetails())
						{
							$previous_value = ($item->getPreviousValue()) ? (($old_item = TBGContext::factory()->TBGMilestone($item->getPreviousValue())) ? __($old_item->getName()) : __('Unknown')) : __('Not determined');
							$new_value = ($item->getCurrentValue()) ? (($new_item = TBGContext::factory()->TBGMilestone($item->getCurrentValue())) ? __($new_item->getName()) : __('Unknown')) : __('Not determined');
							echo __("Milestone changed: %previous_value% => %new_value%", array('%previous_value%' => '<strong>'.$previous_value.'</strong>', '%new_value%' => '<strong>'.$new_value.'</strong>'));
						}
						break;
					case TBGLogTable::LOG_ISSUE_CUSTOMFIELD_CHANGED:
						echo image_tag('icon_customdatatype.png');
						if ($item->hasChangeDetails())
						{
							$key_data = explode(':', $item->getText());
							$key = $key_data[0];
							$customdatatype = TBGCustomDatatype::getByKey($key);

							if ($customdatatype instanceof TBGCustomDatatype)
							{
								$old_value = $item->getPreviousValue();
								$new_value = $item->getCurrentValue();
								switch ($customdatatype->getType())
								{
									case TBGCustomDatatype::INPUT_TEXT:
									case TBGCustomDatatype::INPUT_TEXTAREA_SMALL:
									case TBGCustomDatatype::INPUT_TEXTAREA_MAIN:
										break;
									case TBGCustomDatatype::EDITIONS_CHOICE:
									case TBGCustomDatatype::COMPONENTS_CHOICE:
									case TBGCustomDatatype::RELEASES_CHOICE:
									case TBGCustomDatatype::STATUS_CHOICE:
										$old_object = null;
										$new_object = null;
										try
										{
											switch ($customdatatype->getType())
											{
												case TBGCustomDatatype::EDITIONS_CHOICE:
													$old_object = TBGContext::factory()->TBGEdition($old_value);
													break;
												case TBGCustomDatatype::COMPONENTS_CHOICE:
													$old_object = TBGContext::factory()->TBGComponent($old_value);
													break;
												case TBGCustomDatatype::RELEASES_CHOICE:
													$old_object = TBGContext::factory()->TBGBuild($old_value);
													break;
												case TBGCustomDatatype::STATUS_CHOICE:
													$old_object = TBGContext::factory()->TBGStatus($old_value);
													break;
											}
										}
										catch (Exception $e) {}
										try
										{
											switch ($customdatatype->getType())
											{
												case TBGCustomDatatype::EDITIONS_CHOICE:
													$new_object = TBGContext::factory()->TBGEdition($new_value);
													break;
												case TBGCustomDatatype::COMPONENTS_CHOICE:
													$new_object = TBGContext::factory()->TBGComponent($new_value);
													break;
												case TBGCustomDatatype::RELEASES_CHOICE:
													$new_object = TBGContext::factory()->TBGBuild($new_value);
													break;
												case TBGCustomDatatype::STATUS_CHOICE:
													$new_object = TBGContext::factory()->TBGStatus($new_value);
													break;
											}
										}
										catch (Exception $e) {}
										$old_value = (is_object($old_object)) ? $old_object->getName() : TBGContext::getI18n()->__('Unknown');
										$new_value = (is_object($new_object)) ? $new_object->getName() : TBGContext::getI18n()->__('Unknown');
										break;
									default:
										$old_item = null;
										$new_item = null;
										try
										{
											$old_item = ($old_value) ? new TBGCustomDatatypeOption($old_value) : null;
										}
										catch (Exception $e) {}
										try
										{
											$new_item = ($new_value) ? new TBGCustomDatatypeOption($new_value) : null;
										}
										catch (Exception $e) {}
										$old_value = ($old_item instanceof TBGCustomDatatypeOption) ? $old_item->getName() : TBGContext::getI18n()->__('Unknown');
										$new_value = ($new_item instanceof TBGCustomDatatypeOption) ? $new_item->getName() : TBGContext::getI18n()->__('Unknown');
										break;
								}
								echo __("%field_name% changed: %previous_value% => %new_value%", array('%field_name%' => $customdatatype->getName(), '%previous_value%' => '<strong>'.$old_value.'</strong>', '%new_value%' => '<strong>'.$new_value.'</strong>'));
							}
							echo __('Custom field changed');
						}
						break;
					case TBGLogTable::LOG_ISSUE_USERS:
						echo image_tag('icon_user.png');
						if ($item->hasChangeDetails())
						{
							$previous_value = ($item->getPreviousValue()) ? (($old_item = TBGContext::factory()->TBGUser($item->getPreviousValue())) ? __($old_item->getName()) : __('Unknown')) : __('Not determined');
							$new_value = ($item->getCurrentValue()) ? (($new_item = TBGContext::factory()->TBGUser($item->getCurrentValue())) ? __($new_item->getName()) : __('Unknown')) : __('Not determined');
							echo __("User working on issue changed: %previous_value% => %new_value%", array('%previous_value%' => '<strong>'.$previous_value.'</strong>', '%new_value%' => '<strong>'.$new_value.'</strong>'));
						}
						break;
					case TBGLogTable::LOG_ISSUE_ASSIGNED:
						echo image_tag('icon_user.png');
						echo __("Assignee changed to %new_value%", array('%new_value%' => '<strong>'.$item->getText().'</strong>'));
						break;
					case TBGLogTable::LOG_ISSUE_TIME_SPENT:
						echo image_tag('icon_time.png');
						echo __("Time spent changed: %value%", array('%value%' => '<strong>'.$item->getText().'</strong>'));
						break;
					case TBGLogTable::LOG_ISSUE_PERCENT:
						echo image_tag('icon_percent.png');
						if ($item->hasChangeDetails())
						{
							echo __("Milestone changed: %previous_value% => %new_value%", array('%previous_value%' => '<strong>'.(int) $item->getPreviousValue().'</strong>', '%new_value%' => '<strong>'.(int) $item->getCurrentValue().'</strong>'));
						}
						break;
					default:
						echo $item->getChangeType();
				}
				if (!$item->hasChangeDetails()) echo $item->getText();
			}
			catch (Exception $e)
			{
				echo __('Unknown change');
			}

		?>
	</li>
<?php endif; ?>
