<tr class="step">
	<td><?php echo link_tag(make_url('configure_workflow_step', array('workflow_id' => $step->getWorkflow()->getID(), 'step_id' => $step->getID())), $step->getName()); ?></td>
	<td>
		<?php if ($step->hasLinkedStatus()): ?>
			<table style="table-layout: auto; width: auto;" cellpadding=0 cellspacing=0>
				<tr class="status">
					<td style="width: 16px; height: 16px;"><div style="border: 1px solid #AAA; background-color: <?php echo ($step->getLinkedStatus() instanceof TBGDatatype) ? $step->getLinkedStatus()->getColor() : '#FFF'; ?>; font-size: 1px; width: 15px; height: 15px; margin-right: 2px;">&nbsp;</div></td>
					<td style="padding-left: 0px;"><?php echo $step->getLinkedStatus()->getName(); ?></td>
				</tr>
			</table>
		<?php else: ?>
			<span class="faded_out"> - </span>
		<?php endif; ?>
	</td>
	<td>
		<?php if ($step->getNumberOfOutgoingTransitions() > 0): ?>
			<?php foreach ($step->getOutgoingTransitions() as $transition): ?>
				<div class="workflow_step_transition_name">
					<?php echo $transition->getName(); ?>
					<span class="workflow_step_transition_outgoing_step">&rarr; <?php echo $transition->getOutgoingStep()->getName(); ?></span>
				</div>
			<?php endforeach; ?>
		<?php else: ?>
			<div class="faded_out"> - </div>
		<?php endif; ?>
	</td>
	<td>-</td>
</tr>