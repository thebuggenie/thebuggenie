<tr>
	<td><?php echo $step->getName(); ?></td>
	<td>
		<?php if ($step->hasLinkedStatus()): ?>
			<table style="table-layout: auto; width: auto;" cellpadding=0 cellspacing=0>
				<tr>
					<td style="width: 16px; height: 16px;"><div style="border: 1px solid #AAA; background-color: <?php echo ($step->getLinkedStatus() instanceof TBGDatatype) ? $step->getLinkedStatus()->getColor() : '#FFF'; ?>; font-size: 1px; width: 15px; height: 15px; margin-right: 2px;">&nbsp;</div></td>
					<td style="padding-left: 0px;"><?php echo $step->getLinkedStatus()->getName(); ?></td>
				</tr>
			</table>
		<?php else: ?>
			<span class="faded_out"> - </span>
		<?php endif; ?>
	</td>
	<td>-</td>
	<td>-</td>
</tr>