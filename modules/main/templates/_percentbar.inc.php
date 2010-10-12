<table style="table-layout: fixed; width: 100%;" cellpadding=0 cellspacing=0>
<tr<?php if (isset($id)): ?> id="<?php echo $id; ?>"<?php endif; ?>>
	<td class="percent_filled" style="font-size: 3px; width: <?php echo $percent; ?>%; height: <?php echo $height; ?>px;"><b style="text-decoration: none;">&nbsp;</b></td>
	<td class="percent_unfilled" style="font-size: 3px; width: <?php echo (100 - $percent); ?>%; height: <?php echo $height; ?>px;"><b style="text-decoration: none;">&nbsp;</b></td>
</tr>
</table>