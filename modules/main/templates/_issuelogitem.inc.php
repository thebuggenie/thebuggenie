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
