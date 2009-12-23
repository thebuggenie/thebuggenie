<tr class="canhover_light" id="item_<?php echo $key; ?>_<?php echo $issuetype->getID(); ?>">
	<td style="padding: 2px; font-size: 12px;" id="<?php echo $key; ?>_<?php echo $issuetype->getID(); ?>_name">
		<?php
		
			if (is_object($item))
			{
				echo $item->getDescription();
			}
			else
			{
				switch ($item)
				{
					case 'description':
						echo __('Issue description');
						break;
					case 'reproduction_steps':
						echo __('Steps to reproduce the issue');
						break;
					case 'percent_complete':
						echo __('Percent completed');
						break;
					case 'builds':
						echo __('Affected release(s)');
						break;
					case 'component':
						echo __('Affected component(s)');
						break;
					case 'edition':
						echo __('Affected edition(s)');
						break;
					case 'estimated_time':
						echo __('Estimated time to complete');
						break;
					case 'elapsed_time':
						echo __('Time spent working on the issue');
						break;
					case 'milestone':
						echo __('Targetted for milestone');
						break;
					default:
						echo __(ucfirst($item));
						break;
				}

			}

		?>
	</td>
	<td style="padding: 2px; text-align: center;"><input type="checkbox" name="field[<?php echo $key; ?>]"<?php if (array_key_exists($key, $visiblefields)): ?> checked<?php endif; ?>></td>
	<td style="padding: 2px; text-align: center;"><input type="checkbox" name="field[<?php echo $key; ?>][reportable]"<?php if (array_key_exists($key, $visiblefields) && $visiblefields[$key]['reportable']): ?> checked<?php endif; ?>></td>
	<td style="padding: 2px; text-align: center;"><input type="checkbox" name="field[<?php echo $key; ?>][additional]"<?php if (array_key_exists($key, $visiblefields) && $visiblefields[$key]['additional']): ?> checked<?php endif; ?>></td>
	<td style="padding: 2px; text-align: center;"><input type="checkbox" name="field[<?php echo $key; ?>][required]"<?php if (array_key_exists($key, $visiblefields) && $visiblefields[$key]['required']): ?> checked<?php endif; ?>></td>
</tr>