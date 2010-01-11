<?php 

	if ($include_table)
	{
		echo '<span id="show_team_' . $aTeam->getID() . '">';
	}
	
?>
<table class="grouplist" style="width: 100%; table-layout: auto; margin-top: 0px;" cellpadding=0 cellspacing=0>
<tr<?php ($theTeam instanceof TBGTeam && $theTeam->getID() == $aTeam->getID() && !TBGContext::getRequest()->getParameter('remove')) ? print " class=\"g_marked\"" : (($theTeam instanceof TBGTeam && $theTeam->getID() == $aTeam->getID() && TBGContext::getRequest()->getParameter('remove')) ? print " class=\"g_marked_red\"" : print "" ); ?>>
	<td style="width: auto;" valign="middle"><a href="config.php?module=core&amp;section=1&amp;team=<?php print $aTeam->getID(); ?>"><?php print $aTeam->getName() ; ?></a></td>
	<?php
		if ($access_level == "full")
		{
			?>
			<td valign="middle" style="width: 15px;"><a href="javascript:void(0);" onclick="getEditTeam(<?php echo $aTeam->getID(); ?>);" class="image"><?php echo image_tag('icon_edit.png'); ?></a></td>
			<td style="width: 18px; padding: 0px; text-align: center;"><a class="image" href="config.php?module=core&amp;section=1&amp;team=<?php print $aTeam->getID(); ?>&amp;remove=true"><?php echo image_tag('action_cancel_small.png', '', __('Remove team'), __('Remove team'), 0, 11, 11); ?></a></td>
			<?php
		}
	?>
</tr>
</table>
<?php if ($include_table) echo '</span>'; ?>