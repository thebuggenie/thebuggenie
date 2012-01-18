<li id="workflow_scheme_<?php echo $scheme->getID(); ?>" class="rounded_box lightgrey" style="margin-bottom: 5px;">
	<table>
		<tr>
			<td class="workflow_info scheme">
				<div class="workflow_name"><?php echo $scheme->getName(); ?></div>
				<?php if ($scheme->getDescription()): ?>
					<div class="workflow_description"><?php echo $scheme->getDescription(); ?></div>
				<?php endif; ?>
			</td>
			<td class="workflow_scheme_issuetypes"><?php echo __('Issue types with associated workflows: %number_of_associated_issuetypes%', array('%number_of_associated_issuetypes%' => '<span>'.$scheme->getNumberOfAssociatedWorkflows().'</span>')); ?></td>
			<td class="workflow_actions">
				<div class="button-group">
					<?php echo link_tag(make_url('configure_workflow_scheme', array('scheme_id' => $scheme->getID())), image_tag('icon_workflow_scheme_edit.png', array('title' => __('Show / edit issue type associations'))), array('class' => 'button button-icon button-silver')); ?></a>
					<?php if (TBGContext::getScope()->isCustomWorkflowsEnabled()): ?>
						<a href="javascript:void(0);" onclick="$('copy_scheme_<?php echo $scheme->getID(); ?>_popup').toggle();" class="button button-icon button-silver"><?php echo image_tag('icon_copy.png', array('title' => __('Create a copy of this workflow scheme'))); ?></a>
					<?php endif; ?>
					<?php if (!$scheme->isCore()): ?>
						<?php if ($scheme->isInUse()): ?>
							<a href="javascript:void(0);" onclick="TBG.Main.Helpers.Message.error('<?php echo __('Cannot delete workflow scheme'); ?>', '<?php echo __('This workflow scheme can not be deleted as it is being used by %number_of_projects% project(s)', array('%number_of_projects%' => $scheme->getNumberOfProjects())); ?>');" class="button button-icon button-silver"><?php echo image_tag('icon_delete.png', array('title' => __('Delete this issue type scheme'))); ?></a>
						<?php else: ?>
							<a href="javascript:void(0);" onclick="$('delete_scheme_<?php echo $scheme->getID(); ?>_popup').toggle();" class="button button-icon button-silver"><?php echo image_tag('icon_delete.png', array('title' => __('Delete this issue type scheme'))); ?></a>
						<?php endif; ?>
					<?php endif; ?>
				</div>
			</td>
		</tr>
	</table>
</li>
<?php if (TBGContext::getScope()->isCustomWorkflowsEnabled()): ?>
	<li class="rounded_box white shadowed" id="copy_scheme_<?php echo $scheme->getID(); ?>_popup" style="margin-bottom: 5px; padding: 10px; display: none;">
		<div class="header"><?php echo __('Copy worfklow scheme'); ?></div>
		<div class="content">
			<?php echo __('Please enter the name of the new worfklow scheme'); ?><br>
			<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_workflow_copy_scheme', array('scheme_id' => $scheme->getID())); ?>" onsubmit="TBG.Config.Workflows.Scheme.copy('<?php echo make_url('configure_workflow_copy_scheme', array('scheme_id' => $scheme->getID())); ?>', <?php echo $scheme->getID(); ?>);return false;" id="copy_workflow_scheme_<?php echo $scheme->getID(); ?>_form">
				<label for="copy_scheme_<?php echo $scheme->getID(); ?>_new_name"><?php echo __('New name'); ?></label>
				<input type="text" name="new_name" id="copy_scheme_<?php echo $scheme->getID(); ?>_new_name" value="<?php echo __('Copy of %old_name%', array('%old_name%' => addslashes($scheme->getName()))); ?>" style="width: 300px;">
				<div style="text-align: right;">
					<?php echo image_tag('spinning_16.gif', array('style' => 'margin-right: 5px; display: none;', 'id' => 'copy_workflow_scheme_'.$scheme->getID().'_indicator')); ?>
					<input type="submit" value="<?php echo __('Copy worfklow scheme'); ?>">
				</div>
			</form>
		</div>
	</li>
<?php endif; ?>
<?php if (!$scheme->isCore() && !$scheme->isInUse()): ?>
	<li class="rounded_box white shadowed" id="delete_scheme_<?php echo $scheme->getID(); ?>_popup" style="margin-bottom: 5px; padding: 10px; display: none;">
		<div class="header"><?php echo __('Are you sure?'); ?></div>
		<div class="content">
			<?php echo __('Please confirm that you want to delete this worfklow scheme.'); ?><br>
			<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_workflow_delete_scheme', array('scheme_id' => $scheme->getID())); ?>" onsubmit="TBG.Config.Workflows.Scheme.remove('<?php echo make_url('configure_workflow_delete_scheme', array('scheme_id' => $scheme->getID())); ?>', <?php echo $scheme->getID(); ?>);return false;" id="delete_workflow_scheme_<?php echo $scheme->getID(); ?>_form">
				<div style="text-align: right;">
					<?php echo image_tag('spinning_16.gif', array('style' => 'margin-right: 5px; display: none;', 'id' => 'delete_workflow_scheme_'.$scheme->getID().'_indicator')); ?>
					<input type="submit" value="<?php echo __('Yes, delete it'); ?>"><?php echo __('%delete% or %cancel%', array('%delete%' => '', '%cancel%' => '<b>'.javascript_link_tag(__('cancel'), array('onclick' => "$('delete_scheme_{$scheme->getID()}_popup').toggle();")).'</b>')); ?>
				</div>
			</form>
		</div>
	</li>
<?php endif; ?>