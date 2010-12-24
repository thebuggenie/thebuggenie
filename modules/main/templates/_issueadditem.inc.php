<div class="rounded_box white borderless shadowed backdrop_box medium" id="viewissue_add_item_div">
	<div class="backdrop_detail_header"><?php echo __('Add affected item'); ?></div>
	<div class="backdrop_detail_content">
		<form id="viewissue_add_item_form" action="<?php echo make_url('add_affected', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID())); ?>" method="post" accept-charset="<?php echo TBGSettings::getCharset(); ?>" onsubmit="addAffected('<?php echo make_url('add_affected', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID())); ?>');return false;">
		<?php echo __('Please select the type and item you wish to add as affected by this issue.'); ?>
		<br />
		<div class="header_div"><?php echo __('Item type'); ?></div>
		<?php if ($issue->getProject()->isEditionsEnabled()): ?><input type="radio" name="item_type" id="item_type_edition" value="edition" onClick="$('no_type').hide(); $('which_item_edition').show(); $('which_item_component').hide(); $('which_item_build').hide(); $('item_submit').show();" /><label for="item_type_edition"> <?php echo __('Edition'); ?></label><br /><?php endif; ?>
		<?php if ($issue->getProject()->isComponentsEnabled()): ?><input type="radio" name="item_type" id="item_type_component" value="component" onClick="$('no_type').hide(); $('which_item_edition').hide(); $('which_item_component').show(); $('which_item_build').hide(); $('item_submit').show();" /><label for="item_type_component"> <?php echo __('Component'); ?></label><br /><?php endif; ?>
		<?php if ($issue->getProject()->isBuildsEnabled()): ?><input type="radio" name="item_type" id="item_type_build" value="build" onClick="$('no_type').hide(); $('which_item_edition').hide(); $('which_item_component').hide(); $('which_item_build').show(); $('item_submit').show();" /><label for="item_type_build"> <?php echo __('Release'); ?></label><br /><?php endif; ?>
		<div class="header_div"><?php echo __('Affected item'); ?></div>
		<div class="faded_out" id="no_type" style="padding-top: 10px;"><?php echo('Please select an item type'); ?></div>
		<select name="which_item_edition" id="which_item_edition" style="width: 100%; margin-top: 10px; display: none;">
		<?php if ($issue->getProject()->isEditionsEnabled()): ?>
			<?php foreach ($editions as $edition): ?>
			<option value="<?php echo $edition->getID(); ?>"><?php echo $edition->getName(); ?></option>
			<?php endforeach; ?>
		<?php endif; ?>
		</select>
		<select name="which_item_component" id="which_item_component" style="width: 100%; margin-top: 10px; display: none;">
		<?php if ($issue->getProject()->isComponentsEnabled()): ?>
			<?php foreach ($components as $component): ?>
			<option value="<?php echo $component->getID(); ?>"><?php echo $component->getName(); ?></option>
			<?php endforeach; ?>
		<?php endif; ?>
		</select>
		<select name="which_item_build" id="which_item_build" style="width: 100%; margin-top: 10px; display: none;">
		<?php if ($issue->getProject()->isBuildsEnabled()): ?>
			<?php foreach ($builds as $build): ?>
			<option value="<?php echo $build->getID(); ?>"><?php echo $build->getName(); ?></option>
			<?php endforeach; ?>
		<?php endif; ?>
		</select>
		<input type="submit" style="display: none; margin-top: 10px;" id="item_submit" value="<?php echo __('Add this item'); ?>">
		<?php echo image_tag('spinning_20.gif', array('id' => 'add_affected_spinning', 'style' => 'display: none;')); ?>
		</form>
	</div>
	<div class="backdrop_detail_footer">
		<a href="javascript:void(0);" onclick="resetFadedBackdrop();"><?php echo __('Cancel'); ?></a>
	</div>
</div>