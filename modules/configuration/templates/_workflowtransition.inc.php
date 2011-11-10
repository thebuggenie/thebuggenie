<div class="workflow_browser_step_transition transition" id="transition_<?php echo $transition->getID(); ?>">
	<?php if (!$transition->isCore()): ?>
		<?php echo javascript_link_tag(image_tag('icon_delete.png'), array('class' => 'image', 'style' => 'float: right;', 'onclick' => "\$('delete_transition_{$transition->getID()}_confirm').toggle();")); ?>
	<?php endif; ?>
	<?php echo link_tag(make_url('configure_workflow_transition', array('workflow_id' => $transition->getWorkflow()->getID(), 'transition_id' => $transition->getID())), $transition->getName()); ?>
</div>
<?php if (!$transition->isCore()): ?>
	<div class="rounded_box white shadowed" style="position: absolute; width: 285px; display: none;" id="delete_transition_<?php echo $transition->getID(); ?>_confirm">
		<div class="header"><?php echo __('Confirm delete transition'); ?></div>
		<div class="content">
			<?php echo __('Do you really want to delete this transition? If this transition is used by more than one step, it will be removed from these steps as well!'); ?>
			<div style="text-align: right;">
				<?php echo javascript_link_tag(__('Yes'), array('onclick' => "TBG.Config.Workflows.Transition.remove('".make_url('configure_workflow_delete_transition', array('workflow_id' => $transition->getWorkflow()->getID(), 'transition_id' => $transition->getID()))."', {$transition->getID()}, '{$direction}');")); ?> ::
				<b><?php echo javascript_link_tag(__('No'), array('onclick' => "\$('delete_transition_{$transition->getID()}_confirm').toggle();")); ?></b>
			</div>
			<div style="padding: 10px 0 10px 0; display: none;" id="delete_transition_<?php echo $transition->getID(); ?>_indicator"><span style="float: left;"><?php echo image_tag('spinning_16.gif'); ?></span>&nbsp;<?php echo __('Please wait'); ?></div>
		</div>
	</div>
<?php endif; ?>