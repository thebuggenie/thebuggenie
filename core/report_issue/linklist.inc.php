<?php

	if ($step4_links !== null)
	{
		?>
		<table style="width: 100%;" cellpadding=0 cellspacing=0>
		<?php
		
		$ccLinks = 0;
		foreach ($step4_links as $aLink)
		{
			?>
			<tr>
			<td style="width: 20px; padding: 2px;" valign="top"><?php echo image_tag('icon_link.png'); ?></td>
			<td style="width: auto; padding: 2px;"><a href="<?php print $aLink['url']; ?>" target="_blank"><?php print $aLink['desc']; ?></a></td>
			<?php

			if ($step4_set == false)
			{
				?>
				<td style="width: 50px; padding: 2px; text-align: right; font-size: 10px;"><a href="javascript:void(0);" onclick="removeLink(<?php print $ccLinks; ?>)"><?php echo __('Remove'); ?></a></td>
				<?php
			}

			?>
			</tr>
			<?php
			$ccLinks++;
		}
			
		?>
		</table>
		<?php
	}
	else
	{
		?>
		<div style="padding: 3px; color: #AAA;"><?php echo __('You have not added any links yet'); ?></div>
		<?php
	}

?>