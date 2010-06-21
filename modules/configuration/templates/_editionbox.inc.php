<?php TBGContext::loadLibrary('ui'); ?>
<tr id="edition_box_<?php echo $edition->getID(); ?>" class="canhover_light">
	<td style="width: auto; padding: 2px;"><?php echo link_tag(make_url('configure_project_edition', array('project_id' => $edition->getProject()->getID(), 'edition_id' => $edition->getID(), 'mode' => 'general')), image_tag('icon_edition.png', array('style' => 'float: left; margin-right: 5px;')).'<b>'.$edition->getName().'</b>'); ?><?php if ($edition->isDefault()): echo __('%edition_name% (default)', array('%edition_name%' => '')); endif; ?></td>
	<td style="width: 20px; padding: 2px;"><a class="image" href="javascript:void(0);" onclick="Effect.Appear('del_edition_<?php echo $edition->getID(); ?>', { duration: 0.5 });"><?php echo image_tag('action_cancel_small.png'); ?></a><br>
		<div id="del_edition_<?php echo $edition->getID(); ?>" style="display: none; position: absolute; width: 200px; padding: 10px; border: 1px solid #DDD; background-color: #FFF;"><b><?php echo __('Please confirm'); ?></b><br><?php echo __('Do you really want to delete this edition?'); ?><br>
			<div style="text-align: right; padding-top: 5px;"><a href="javascript:void(0);" onclick="deleteEdition(<?php print $edition->getID(); ?>);"><?php echo __('Yes'); ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="javascript:void(0);" onclick="Effect.Fade('del_edition_<?php echo $edition->getID(); ?>', { duration: 0.5 });"><b><?php echo __('No'); ?></b></a></div>
		</div>
	</td>
</tr>
<tr>
	<td style="padding: 2px;" colspan=3>
	<?php if ($edition->hasDescription()): ?>
		<div style="padding-bottom: 10px; color: #AAA;"><?php print $edition->getDescription(); ?></div>
	<?php endif; ?>
	<?php if (TBGContext::getRequest()->isAjaxCall()): ?>
		<script type="text/javascript">new Effect.Pulsate('edition_box_<?php echo $edition->getID(); ?>');</script>
	<?php endif; ?>	
	</td>
</tr>