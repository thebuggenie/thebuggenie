<?php

	if ($step4_files !== null)
	{
		?>
		<table style="width: 100%;" cellpadding=0 cellspacing=0>
		<?php
		
		$ccFiles = 0;
		foreach ($step4_files as $aFile)
		{
			?>
			<tr>
			<td style="width: 20px; padding: 2px;" valign="top"><?php echo image_tag('icon_file.png'); ?></td>
			<td style="width: auto; padding: 2px;"><?php echo $aFile['description']; ?></td>
			<?php

			if ($step4_set == false)
			{
				?>
				<td style="width: 50px; padding: 2px; text-align: right; font-size: 10px;"><a href="javascript:void(0);" onclick="removeFile(<?php print $ccFiles; ?>)"><?php echo __('Remove'); ?></a></td>
				<?php
			}

			?>
			</tr>
			<?php
			$ccFiles++;
		}
			
		?>
		</table>
		<?php
	}
	else
	{
		?>
		<div style="padding: 3px; color: #AAA;"><?php echo __('You have not added any files yet'); ?></div>
		<?php
	}

?>