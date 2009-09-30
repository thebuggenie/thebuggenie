<?php 

	if ($include_table)
	{
		echo '<span id="show_group_' . $aGroup->getID() . '">';
	}
	
?>
<table class="grouplist" style="width: 100%; table-layout: auto; margin-top: 0px;" cellpadding=0 cellspacing=0>
<tr<?php ($theGroup instanceof BUGSgroup && $theGroup->getID() == $aGroup->getID() && !BUGScontext::getRequest()->getParameter('remove')) ? print " class=\"g_marked\"" : (($theGroup instanceof BUGSgroup && $theGroup->getID() == $aGroup->getID() && BUGScontext::getRequest()->getParameter('remove')) ? print " class=\"g_marked_red\"" : print "" ); ?>>
	<td style="width: auto;" valign="middle"><a href="config.php?module=core&amp;section=1&amp;group=<?php print $aGroup->getID(); ?>"><?php print $aGroup->getName() ; ?></a></td>
	<?php
		if ($access_level == "full")
		{
			?>
			<td valign="middle" style="width: 15px;"><a href="javascript:void(0);" onclick="getEditGroup(<?php echo $aGroup->getID(); ?>);" class="image"><?php echo image_tag('icon_edit.png'); ?></a></td>
			<td style="width: 18px; padding: 0px; text-align: center;"><a class="image" href="config.php?module=core&amp;section=1&amp;group=<?php print $aGroup->getID(); ?>&amp;remove=true"><?php echo image_tag('action_cancel_small.png', '', __('Remove group'), __('Remove group'), 0, 11, 11); ?></a></td>
			<?php
		}
	?>
</tr>
</table>
<?php if ($include_table) echo '</span>'; ?>