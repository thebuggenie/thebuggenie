	<?php if ($issue->canEditIssue()): ?>
		<table border="0" cellpadding="0" cellspacing="0" style="margin: 5px; float: left;" id="affected_add_button"><tr><td class="nice_button" style="font-size: 13px; margin-left: 0;"><input type="button" onclick="failedMessage('Unimplemented');" value="<?php echo __('Add an item'); ?>"></td></tr></table>
	<?php else: ?>
		<table border="0" cellpadding="0" cellspacing="0" style="margin: 5px; float: left;" id="affected_add_button"><tr><td class="nice_button disabled" style="font-size: 13px; margin-left: 0;"><input type="button" onclick="failedMessage('<?php echo __('You are not allowed to add an item to this list'); ?>');" value="<?php echo __('Add an item'); ?>"></td></tr></table>
	<?php endif; ?>
<br><br>
<?php
	$editions = array();
	$components = array();
	$builds = array();
	
	if($issue->getProject()->isEditionsEnabled())
	{
		$editions = $issue->getEditions();
	}
	
	if($issue->getProject()->isComponentsEnabled())
	{
		$components = $issue->getComponents();
	}

	if($issue->getProject()->isBuildsEnabled())
	{
		$builds = $issue->getBuilds();
	}
	
	$count = count($editions) + count($components) + count($builds);
?>	
<table style="width: 100%;" cellpadding="0" cellspacing="0" class="issue_affects" id="affected_list">
	<tr>
		<th style="width: 16px; text-align: right; padding-top: 0px; padding-right: 0px; padding-bottom: 0px; padding-left: 3px;"></th><th><?php echo __('Name'); ?></th><th><?php echo __('Status'); ?></th><th style="width: 90px; text-align: right; padding-top: 0px; padding-right: 3px; padding-bottom: 0px; padding-left: 0px;"><?php echo __('Confirmed'); ?></th>
	</tr>
	<?php
		
	?>
	<tr id="no_affected" <?php if ($count != 0): ?>style="display: none;"<?php endif; ?>><td colspan="4"><span class="faded_out"><?php echo __('There are no items'); ?></span></td></tr>
	<?php
		if ($issue->getProject()->isEditionsEnabled()):
			foreach ($issue->getEditions() as $edition):
				?>
	<tr id="affected_edition_<?php echo $edition['a_id']; ?>">
		<td><?php echo image_tag('icon_edition.png', array('alt' => __('Edition'))); ?></td><td style="padding-left: 3px;"><?php if ($issue->canEditIssue()): echo '<a href="javascript:void(0);" onClick="$(\'affected_edition_'.$edition['a_id'].'_delete\').toggle()">'.image_tag('icon_delete.png', array('alt' => __('Delete'))).'</a> '; endif; ?><?php echo $edition['edition']->getName(); ?></td><td style="width: 240px">
		<table style="table-layout: auto; width: 240px"; cellpadding=0 cellspacing=0 id="status_table">
			<tr>
				<td style="width: 24px;"><div style="border: 1px solid #AAA; background-color: <?php echo $edition['status']->getColor(); ?>; font-size: 1px; width: 20px; height: 15px; margin-right: 2px;" id="status_color">&nbsp;</div></td>
				<td style="padding-left: 5px;" id="status_content"><?php echo $edition['status']->getName(); ?></td>
			</tr>
		</table>
		</td><td style="width: 90px; text-align: right; padding-top: 0px; padding-right: 3px; padding-bottom: 0px; padding-left: 0px;"><?php
		if ($edition['confirmed']): $image = image_tag('action_ok_small.png', array('alt' => __('Yes'), 'id' => 'affected_edition_'.$edition['a_id'].'_confirmed_icon')); else: $image = image_tag('action_cancel_small.png', array('alt' => __('No'), 'id' => 'affected_edition_'.$edition['a_id'].'_confirmed_icon')); endif;
		if ($issue->canEditIssue()): $url = "<a href=\"javascript:void(0);\" onClick=\"toggleConfirmed('".make_url('confirm_affected', array('issue_id' => $issue->getID(), 'affected_type' => 'edition', 'affected_id' => $edition['a_id']))."', 'edition_".$edition['a_id']."');\">".$image."</a>"; else: $url = $image; endif; echo $url; ?><span id="affected_edition_<?php echo $edition['a_id']; ?>_confirmed_spinner" style="display: none;"> <?php echo image_tag('spinning_16.gif'); ?></span></td>
	</tr>
	<?php if ($issue->canEditIssue()): ?>
	<tr id="affected_edition_<?php echo $edition['a_id']; ?>_delete" style="display: none;">
		<td colspan="4">
			<div class="rounded_box lightgrey">
				<b><?php echo __('Are you sure you want to remove the edition \'%item%\'?', array('%item%' => $edition['edition']->getName())); ?></b><br>
				<?php echo __('This will remove it from the list of items affected by this issue. It can be readded at any time.'); ?><br>
				<a href="javascript:void(0);" onClick="deleteAffected('<?php echo make_url('remove_affected', array('issue_id' => $issue->getID(), 'affected_type' => 'edition', 'affected_id' => $edition['a_id'])).'\', '.'\'edition_'.$edition['a_id']; ?>')"><?php echo __('Yes'); ?></a> - <a href="javascript:void(0);" onClick="$('affected_edition_<?php echo $edition['a_id']; ?>_delete').hide()"><?php echo __('No'); ?></a><span id="affected_edition_<?php echo $edition['a_id']; ?>_delete_spinner" style="display: none;"> <?php echo image_tag('spinning_16.gif'); ?></span>
			</div>
		</td>
	</tr>
	<?php endif; ?>
				<?php
			endforeach;
		endif;
	?>
	<?php
		if ($issue->getProject()->isComponentsEnabled()):
			foreach ($issue->getComponents() as $component):
				?>
	<tr id="affected_component_<?php echo $component['a_id']; ?>">
		<td><?php echo image_tag('icon_components.png', array('alt' => __('Component'))); ?></td><td style="padding-left: 3px;"><?php if ($issue->canEditIssue()): echo '<a href="javascript:void(0);" onClick="$(\'affected_component_'.$component['a_id'].'_delete\').toggle()">'.image_tag('icon_delete.png', array('alt' => __('Delete'))).'</a> '; endif; ?><?php echo $component['component']->getName(); ?></td><td style="width: 240px">
		<table style="table-layout: auto; width: 240px"; cellpadding=0 cellspacing=0 id="status_table">
			<tr>
				<td style="width: 24px;"><div style="border: 1px solid #AAA; background-color: <?php echo $component['status']->getColor(); ?>; font-size: 1px; width: 20px; height: 15px; margin-right: 2px;" id="status_color">&nbsp;</div></td>
				<td style="padding-left: 5px;" id="status_content"><?php echo $component['status']->getName(); ?></td>
			</tr>
		</table>
		</td><td style="width: 90px; text-align: right; padding-top: 0px; padding-right: 3px; padding-bottom: 0px; padding-left: 0px;"><?php
		if ($component['confirmed']): $image = image_tag('action_ok_small.png', array('alt' => __('Yes'), 'id' => 'affected_component_'.$component['a_id'].'_confirmed_icon')); else: $image = image_tag('action_cancel_small.png', array('alt' => __('No'), 'id' => 'affected_component_'.$component['a_id'].'_confirmed_icon')); endif;
		if ($issue->canEditIssue()): $url = "<a href=\"javascript:void(0);\" onClick=\"toggleConfirmed('".make_url('confirm_affected', array('issue_id' => $issue->getID(), 'affected_type' => 'component', 'affected_id' => $component['a_id']))."', 'component_".$component['a_id']."');\">".$image."</a>"; else: $url = $image; endif; echo $url; ?><span id="affected_component_<?php echo $component['a_id']; ?>_confirmed_spinner" style="display: none;"> <?php echo image_tag('spinning_16.gif'); ?></span></td>
	</tr>
	<?php if ($issue->canEditIssue()): ?>
	<tr id="affected_component_<?php echo $component['a_id']; ?>_delete" style="display: none;">
		<td colspan="4">
			<div class="rounded_box lightgrey">
				<b><?php echo __('Are you sure you want to remove the component \'%item%\'?', array('%item%' => $component['component']->getName())); ?></b><br>
				<?php echo __('This will remove it from the list of items affected by this issue. It can be readded at any time.'); ?><br>
				<a href="javascript:void(0);" onClick="deleteAffected('<?php echo make_url('remove_affected', array('issue_id' => $issue->getID(), 'affected_type' => 'component', 'affected_id' => $component['a_id'])).'\', '.'\'component_'.$component['a_id']; ?>')"><?php echo __('Yes'); ?></a> - <a href="javascript:void(0);" onClick="$('affected_component_<?php echo $component['a_id']; ?>_delete').hide()"><?php echo __('No'); ?></a><span id="affected_component_<?php echo $component['a_id']; ?>_delete_spinner" style="display: none;"> <?php echo image_tag('spinning_16.gif'); ?></span>
			</div>
		</td>
	</tr>
	<?php endif; ?>
				<?php
			endforeach;
		endif;
	?>
	<?php
		if ($issue->getProject()->isBuildsEnabled()):
			foreach ($issue->getBuilds() as $build):
				?>
	<tr id="affected_build_<?php echo $build['a_id']; ?>">
		<td><?php echo image_tag('icon_build.png', array('alt' => __('Release'))); ?></td><td style="padding-left: 3px;"><?php if ($issue->canEditIssue()): echo '<a href="javascript:void(0);" onClick="$(\'affected_build_'.$build['a_id'].'_delete\').toggle()">'.image_tag('icon_delete.png', array('alt' => __('Delete'))).'</a> '; endif; ?><?php echo $build['build']->getName(); ?></td><td style="width: 240px">
		<table style="table-layout: auto; width: 240px"; cellpadding=0 cellspacing=0 id="status_table">
			<tr>
				<td style="width: 24px;"><div style="border: 1px solid #AAA; background-color: <?php echo $build['status']->getColor(); ?>; font-size: 1px; width: 20px; height: 15px; margin-right: 2px;" id="status_color">&nbsp;</div></td>
				<td style="padding-left: 5px;" id="status_content"><?php echo $build['status']->getName(); ?></td>
			</tr>
		</table>
		</td><td style="width: 90px; text-align: right; padding-top: 0px; padding-right: 3px; padding-bottom: 0px; padding-left: 0px;"><?php
		if ($build['confirmed']): $image = image_tag('action_ok_small.png', array('alt' => __('Yes'), 'id' => 'affected_build_'.$build['a_id'].'_confirmed_icon')); else: $image = image_tag('action_cancel_small.png', array('alt' => __('No'), 'id' => 'affected_build_'.$build['a_id'].'_confirmed_icon')); endif;
		if ($issue->canEditIssue()): $url = "<a href=\"javascript:void(0);\" onClick=\"toggleConfirmed('".make_url('confirm_affected', array('issue_id' => $issue->getID(), 'affected_type' => 'build', 'affected_id' => $build['a_id']))."', 'build_".$build['a_id']."');\">".$image."</a>"; else: $url = $image; endif; echo $url; ?><span id="affected_build_<?php echo $build['a_id']; ?>_confirmed_spinner" style="display: none;"> <?php echo image_tag('spinning_16.gif'); ?></span></td>
	</tr>
	<?php if ($issue->canEditIssue()): ?>
	<tr id="affected_build_<?php echo $build['a_id']; ?>_delete" style="display: none;">
		<td colspan="4">
			<div class="rounded_box lightgrey">
				<b><?php echo __('Are you sure you want to remove the release \'%item%\'?', array('%item%' => $build['build']->getName())); ?></b><br>
				<?php echo __('This will remove it from the list of items affected by this issue. It can be readded at any time.'); ?><br>
				<a href="javascript:void(0);" onClick="deleteAffected('<?php echo make_url('remove_affected', array('issue_id' => $issue->getID(), 'affected_type' => 'build', 'affected_id' => $build['a_id'])).'\', '.'\'build_'.$build['a_id']; ?>')"><?php echo __('Yes'); ?></a> - <a href="javascript:void(0);" onClick="$('affected_build_<?php echo $build['a_id']; ?>_delete').hide()"><?php echo __('No'); ?></a><span id="affected_build_<?php echo $build['a_id']; ?>_delete_spinner" style="display: none;"> <?php echo image_tag('spinning_16.gif'); ?></span>
			</div>
		</td>
	</tr>
	<?php endif; ?>
				<?php
			endforeach;
		endif;
	?>
</table>
