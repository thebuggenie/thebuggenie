<tr id="edition_box_<?php echo $edition->getID(); ?>" class="hover_highlight">
	<td style="width: 20px; padding: 2px;"><?php echo image_tag('icon_edition.png'); ?></td>
	<td style="width: auto; padding: 2px;"><?php echo $edition->getName(); ?><?php if ($edition->isDefault()): echo __('%edition_name% (default)', array('%edition_name%' => '')); endif; ?></td>
	<td style="width: 40px; padding: 2px;">
		<?php echo javascript_link_tag(image_tag('icon_edit.png'), array('class' => 'image', 'onclick' => "editEdition('".make_url('configure_project_edition', array('project_id' => $edition->getProject()->getID(), 'edition_id' => $edition->getID()))."', '".$edition->getID()."');", 'title' => __('Edit edition'))); ?>
		<?php echo javascript_link_tag(image_tag('action_cancel_small.png'), array('class' => 'image', 'onclick' => "\$('del_edition_{$edition->getID()}').toggle();")); ?>
		<div id="del_edition_<?php echo $edition->getID(); ?>" style="display: none; position: absolute; width: 200px; padding: 10px; border: 1px solid #DDD; background-color: #FFF;"><b><?php echo __('Please confirm'); ?></b><br><?php echo __('Do you really want to delete this edition?'); ?><br>
			<div style="text-align: right; padding-top: 5px;"><a href="javascript:void(0);" onclick="deleteEdition(<?php print $edition->getID(); ?>);"><?php echo __('Yes'); ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="javascript:void(0);" onclick="Effect.Fade('del_edition_<?php echo $edition->getID(); ?>', { duration: 0.5 });"><b><?php echo __('No'); ?></b></a></div>
		</div>
	</td>
</tr>
<?php if ($edition->hasDescription()): ?>
	<tr>
		<td style="padding: 2px;" colspan=3>
			<div style="padding-bottom: 10px; color: #AAA;"><?php print $edition->getDescription(); ?></div>
		</td>
	</tr>
<?php endif; ?>