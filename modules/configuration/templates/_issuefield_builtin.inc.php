<table style="width: 100%;" cellpadding="0" cellspacing="0">
	<?php if ($type == 'status'): ?>
		<?php foreach ($items as $item): ?>
			<tr class="canhover">
				<td style="width: 24px;"><div style="border: 1px solid #AAA; background-color: <?php echo $item->getColor(); ?>; font-size: 1px; width: 20px; height: 15px; margin-right: 2px;">&nbsp;</div></td>
				<td style="padding: 5px; font-size: 13px;"><?php echo $item->getName(); ?></td>
			</tr>
		<?php endforeach; ?>
	<?php endif; ?>
</table>