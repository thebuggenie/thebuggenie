<tr class="step">
	<td>
		<?php echo link_tag(make_url('configure_workflow_step', array('workflow_id' => $step->getWorkflow()->getID(), 'step_id' => $step->getID())), $step->getName(), array('class' => 'step_name'.((!$step->hasIncomingTransitions()) ? ' faded_out' : ''))); ?>
		<div class="rounded_box shadowed white" id="step_<?php echo $step->getID(); ?>_transitions_delete" style="width: 720px; position: absolute; padding: 5px; margin: 5px; display: none;">
			<div class="header"><?php echo __('Delete all outgoing transition from step "%step_name%"', array('%step_name%' => $step->getName())); ?></div>
			<div class="content">
				<?php echo __('Are you sure you want to delete ALL outgoing transitions from this step? This action cannot be reverted.'); ?>
				<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" method="post" action="<?php echo make_url('configure_workflow_delete_step_transitions', array('workflow_id' => $step->getWorkflow()->getID(), 'step_id' => $step->getID())); ?>">
					<div style="text-align: right;">
						<input type="submit" value="<?php echo __('Yes'); ?>" onclick="$('step_<?php echo $step->getID(); ?>_transition_delete_indicator').show();"> :: 
						<b><?php echo javascript_link_tag(__('No'), array('onclick' => "\$('step_{$step->getID()}_transitions_delete').toggle();")); ?></b>
						<div style="padding: 10px 0 10px 0; display: none;" id="step_<?php echo $step->getID(); ?>_transition_delete_indicator"><span style="float: right;"><?php echo image_tag('spinning_16.gif'); ?></span>&nbsp;<?php echo __('Please wait'); ?></div>
					</div>
				</form>
			</div>
		</div>
		<div class="rounded_box shadowed white" id="step_<?php echo $step->getID(); ?>_transition_add" style="width: 720px; position: absolute; padding: 5px; margin: 5px; display: none;">
			<div class="header"><?php echo __('Add outgoing transition from step "%step_name%"', array('%step_name%' => $step->getName())); ?></div>
			<div class="content">
				<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" method="post" action="<?php echo make_url('configure_workflow_add_transition', array('workflow_id' => $step->getWorkflow()->getID(), 'step_id' => $step->getID())); ?>">
					<ul class="simple_list">
						<li>
							<input type="radio" name="add_transition_type" value="existing" id="step_<?php echo $step->getID(); ?>_add_existing_transition">
							<label for="step_<?php echo $step->getID(); ?>_add_existing_transition"><?php echo __('Existing transition'); ?></label>
							<select name="existing_transition_id">
								<?php foreach ($step->getWorkflow()->getTransitions() as $transition): ?>
									<?php if ($step->hasOutgoingTransition($transition)) continue; ?>
									<option value="<?php echo $transition->getID(); ?>"><?php echo $transition->getName(); ?> &rarr; <?php echo $transition->getOutgoingStep()->getName(); ?></option>
								<?php endforeach; ?>
							</select>
							<div class="add_transition_separation"> </div>
						</li>
						<li>
							<input type="radio" name="add_transition_type" value="new" id="step_<?php echo $step->getID(); ?>_add_new_transition" checked>
							<label for="step_<?php echo $step->getID(); ?>_add_new_transition"><?php echo __('Create new transition'); ?></label>
							<dl style="margin: 10px 25px;">
								<dt><label for="add_transition_step_<?php echo $step->getID(); ?>_name"><?php echo __('Transition name'); ?></label></dt>
								<dd>
									<input type="text" id="add_transition_step_<?php echo $step->getID(); ?>_name" name="transition_name" style="width: 300px;"><br>
									<div class="faded_out"><?php echo __('This name will be presented to the user as a link'); ?></div>
								</dd>
								<dt><label for="add_transition_step_<?php echo $step->getID(); ?>_description" class="optional"><?php echo __('Description'); ?> <span>(<?php echo __('Optional'); ?>)</span></label></dt>
								<dd><input type="text" id="add_transition_step_<?php echo $step->getID(); ?>_description" name="transition_description" style="width: 500px;"></dd>
								<dt><label for="add_transition_step_<?php echo $step->getID(); ?>_outgoing_step_id"><?php echo __('Outgoing step'); ?></label></dt>
								<dd>
									<select id="add_transition_step_<?php echo $step->getID(); ?>_outgoing_step_id" name="outgoing_step_id">
										<?php foreach ($step->getWorkflow()->getSteps() as $workflow_step): ?>
											<?php if ($workflow_step->getID() == $step->getID()) continue; ?>
											<option value="<?php echo $workflow_step->getID(); ?>"><?php echo $workflow_step->getName(); ?></option>
										<?php endforeach; ?>
									</select>
								</dd>
								<dt><label for="add_transition_step_<?php echo $step->getID(); ?>_template"><?php echo __('Popup template'); ?></label></dt>
								<dd>
									<select id="add_transition_step_<?php echo $step->getID(); ?>_template" name="template">
										<?php foreach (TBGWorkflowTransition::getTemplates() as $template_key => $template_name): ?>
											<option value="<?php echo $template_key; ?>"><?php echo $template_name; ?></option>
										<?php endforeach; ?>
									</select>
								</dd>
							</dl>
						</li>
					</ul>
					<br style="clear: both;">
					<div style="text-align: center; padding: 10px;">
						<input type="submit" value="<?php echo __('Add transition'); ?>">
						<?php echo __('%add_transition% or %cancel%', array('%add_transition%' => '', '%cancel%' => '')); ?>
						<?php echo javascript_link_tag(__('cancel'), array('onclick' => "$('step_{$step->getID()}_transition_add').toggle()")); ?>
					</div>
				</form>
			</div>
		</div>
	</td>
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
					<?php echo link_tag(make_url('configure_workflow_transition', array('workflow_id' => $transition->getWorkflow()->getID(), 'transition_id' => $transition->getID())), $transition->getName()); ?>
					<span class="workflow_step_transition_outgoing_step">&rarr; <?php echo $transition->getOutgoingStep()->getName(); ?></span>
				</div>
			<?php endforeach; ?>
		<?php else: ?>
			<div class="faded_out"> - </div>
		<?php endif; ?>
	</td>
	<td class="workflow_step_actions">
		<?php if (false && ($step->isCore() || $step->getWorkflow()->isCore())): ?>
			<?php echo link_tag(make_url('configure_workflow_step', array('workflow_id' => $step->getWorkflow()->getID(), 'step_id' => $step->getID())), __('Show step info')); ?>
		<?php else: ?>
			<?php echo link_tag(make_url('configure_workflow_step', array('workflow_id' => $step->getWorkflow()->getID(), 'step_id' => $step->getID())), __('Edit step')); ?> |
			<?php if ($step->hasIncomingTransitions()): ?>
				<span class="faded_out"><a href="javascript:void(0);" class="disabled" onclick="failedMessage('<?php echo __('You cannot delete a step with incoming transitions'); ?>', '<?php echo __('To delete a step that has incoming transitions, first remove all incoming transitions'); ?>');"><?php echo __('Delete step'); ?></a></span><br>
			<?php else: ?>
				<a href="#"><?php echo __('Delete step'); ?></a><br>
			<?php endif; ?>
			<?php echo javascript_link_tag(__('Add transition'), array('onclick' => "$('step_{$step->getID()}_transition_add').toggle()")); ?> |
			<?php echo javascript_link_tag(__('Delete outgoing transitions'), array('onclick' => "\$('step_{$step->getID()}_transitions_delete').toggle();")); ?>
		<?php endif; ?>
	</td>
</tr>