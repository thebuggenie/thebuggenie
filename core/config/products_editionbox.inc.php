<?php

if (!$anEdition instanceof BUGSedition)
{
	exit();
}

?>
<tr id="edition_box_<?php echo $anEdition->getID(); ?>">
<td style="width: 20px; padding: 2px;"><?php echo image_tag('icon_edition.png'); ?></td>
<td style="width: auto; padding: 2px;"><a href="config.php?module=core&amp;section=10&amp;p_id=<?php print $theProject->getID(); ?>&amp;edit_editions=true&amp;e_id=<?php print $anEdition->getID(); ?>"><b><?php print $anEdition; ?></b></a><?php print ($anEdition->isDefault()) ? __('%edition_name% (default)', array('%edition_name%' => '')) : ''; ?></td>
<td style="width: 20px; padding: 2px;"><a class="image" href="javascript:void(0);" onclick="Effect.Appear('del_edition_<?php echo $anEdition->getID(); ?>', { duration: 0.5 });"><?php echo image_tag('action_cancel_small.png'); ?></a><br>
<div id="del_edition_<?php echo $anEdition->getID(); ?>" style="display: none; position: absolute; width: 200px; padding: 10px; border: 1px solid #DDD; background-color: #FFF;"><b><?php echo __('Please confirm'); ?></b><br><?php echo __('Do you really want to delete this edition?'); ?><br>
<div style="text-align: right; padding-top: 5px;"><a href="javascript:void(0);" onclick="deleteEdition(<?php print $anEdition->getID(); ?>);"><?php echo __('Yes'); ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="javascript:void(0);" onclick="Effect.Fade('del_edition_<?php echo $anEdition->getID(); ?>', { duration: 0.5 });"><b><?php echo __('No'); ?></b></a></div>
</div>
</td>
</tr>
<tr>
<td style="padding: 2px;" colspan=3><?php print $anEdition->getDescription(); ?></td>
</tr>