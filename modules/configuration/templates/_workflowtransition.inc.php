<div class="workflow_browser_step_transition transition <?php echo $direction; ?>" id="transition_<?php echo $transition->getID(); ?>">
	<?php if (!$transition->isCore() && !$transition->isInitialTransition()): ?>
		<?php echo javascript_link_tag(image_tag('icon_delete.png'), array('class' => 'image', 'onclick' => "TBG.Main.Helpers.Dialog.show('".__('Do you really want to delete this transition?')."', '".__('If this transition is used by more than one step, it will be removed from these steps as well!')."', {yes: {click: function() {TBG.Config.Workflows.Transition.remove('".make_url('configure_workflow_delete_transition', array('workflow_id' => $transition->getWorkflow()->getID(), 'transition_id' => $transition->getID()))."', {$transition->getID()}, '{$direction}'); }}, no: { click: TBG.Main.Helpers.Dialog.dismiss }});")); ?>
	<?php endif; ?>
	<?php echo link_tag(make_url('configure_workflow_transition', array('workflow_id' => $transition->getWorkflow()->getID(), 'transition_id' => $transition->getID())), $transition->getName()); ?>
	<?php if ($direction == 'outgoing'): ?>
		<br>&rarr;
		<span><?php echo link_tag(make_url('configure_workflow_step', array('workflow_id' => $transition->getWorkflow()->getID(), 'step_id' => $transition->getOutgoingStep()->getID())), $transition->getOutgoingStep()->getName()); ?></span>
	<?php endif; ?>
</div>
