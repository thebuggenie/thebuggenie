<tr class="step">
    <td>
        <?php echo link_tag(make_url('configure_workflow_step', array('workflow_id' => $step->getWorkflow()->getID(), 'step_id' => $step->getID())), $step->getName(), array('class' => 'step_name'.((!$step->hasIncomingTransitions()) ? ' faded_out' : ''))); ?>
        <?php if (!$step->isCore()): ?>
            <div class="rounded_box shadowed white" id="step_<?php echo $step->getID(); ?>_transitions_delete" style="width: 720px; position: absolute; padding: 5px; margin: 5px; display: none;">
                <div class="header"><?php echo __('Delete all outgoing transition from step "%step_name"', array('%step_name' => $step->getName())); ?></div>
                <div class="content">
                    <?php echo __('Are you sure you want to delete ALL outgoing transitions from this step? This action cannot be reverted.'); ?>
                    <form accept-charset="<?php echo \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>" method="post" action="<?php echo make_url('configure_workflow_delete_step_transitions', array('workflow_id' => $step->getWorkflow()->getID(), 'step_id' => $step->getID())); ?>">
                        <div style="text-align: right;">
                            <input type="submit" value="<?php echo __('Yes'); ?>" onclick="$('step_<?php echo $step->getID(); ?>_transition_delete_indicator').show();$(this).hide();"> ::
                            <b><?php echo javascript_link_tag(__('No'), array('onclick' => "\$('step_{$step->getID()}_transitions_delete').toggle();")); ?></b>
                            <div style="padding: 10px 0 10px 0; display: none;" id="step_<?php echo $step->getID(); ?>_transition_delete_indicator"><span style="float: right;"><?php echo image_tag('spinning_16.gif'); ?></span>&nbsp;<?php echo __('Please wait'); ?></div>
                        </div>
                    </form>
                </div>
            </div>
            <?php include_component('configuration/workflowaddtransition', array('step' => $step)); ?>
            <div class="rounded_box shadowed white" id="step_<?php echo $step->getID(); ?>_delete" style="width: 720px; position: absolute; padding: 5px; margin: 5px; display: none;">
                <div class="header"><?php echo __('Completely delete step "%step_name"', array('%step_name' => $step->getName())); ?></div>
                <div class="content">
                    <?php echo __('Are you sure you want to completely delete this step? This action cannot be reverted.'); ?>
                    <form accept-charset="<?php echo \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>" method="post" action="<?php echo make_url('configure_workflow_delete_step', array('workflow_id' => $step->getWorkflow()->getID(), 'step_id' => $step->getID())); ?>">
                        <div style="text-align: right;">
                            <input type="submit" value="<?php echo __('Yes'); ?>" onclick="$('step_<?php echo $step->getID(); ?>_delete_indicator').show();$(this).hide();"> ::
                            <b><?php echo javascript_link_tag(__('No'), array('onclick' => "\$('step_{$step->getID()}_delete').toggle();")); ?></b>
                            <div style="padding: 10px 0 10px 0; display: none;" id="step_<?php echo $step->getID(); ?>_delete_indicator"><span style="float: right;"><?php echo image_tag('spinning_16.gif'); ?></span>&nbsp;<?php echo __('Please wait'); ?></div>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </td>
    <td>
        <?php if ($step->hasLinkedStatus()): ?>
            <table style="table-layout: auto; width: auto;" cellpadding=0 cellspacing=0>
                <tr class="status">
                    <td style="width: 16px; height: 16px;"><div style="border: 1px solid #AAA; background-color: <?php echo ($step->getLinkedStatus() instanceof \thebuggenie\core\entities\Datatype) ? $step->getLinkedStatus()->getColor() : '#FFF'; ?>; font-size: 1px; width: 15px; height: 15px; margin-right: 2px;">&nbsp;</div></td>
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
        <?php if ($step->isCore()): ?>
            <?php echo link_tag(make_url('configure_workflow_step', array('workflow_id' => $step->getWorkflow()->getID(), 'step_id' => $step->getID())), __('Show step info')); ?>
        <?php else: ?>
            <?php echo link_tag(make_url('configure_workflow_step', array('workflow_id' => $step->getWorkflow()->getID(), 'step_id' => $step->getID())), __('Edit step')); ?> |
            <?php if ($step->hasIncomingTransitions()): ?>
                <span class="faded_out"><a href="javascript:void(0);" class="disabled" onclick="TBG.Main.Helpers.Message.error('<?php echo __('You cannot delete a step with incoming transitions'); ?>', '<?php echo __('To delete a step that has incoming transitions, first remove all incoming transitions'); ?>');"><?php echo __('Delete step'); ?></a></span><br>
            <?php elseif ($step->getWorkflow()->getNumberOfSteps() == 1): ?>
                <span class="faded_out"><a href="javascript:void(0);" class="disabled" onclick="TBG.Main.Helpers.Message.error('<?php echo __('You cannot delete the last step'); ?>', '<?php echo __('To delete this step, make sure there are other steps available'); ?>');"><?php echo __('Delete step'); ?></a></span><br>
            <?php else: ?>
                <?php echo javascript_link_tag(__('Delete step'), array('onclick' => "\$('step_{$step->getID()}_delete').toggle();")); ?><br>
            <?php endif; ?>
            <?php echo javascript_link_tag(__('Add transition'), array('onclick' => "$('step_{$step->getID()}_transition_add').toggle()")); ?> |
            <?php echo javascript_link_tag(__('Delete outgoing transitions'), array('onclick' => "\$('step_{$step->getID()}_transitions_delete').toggle();")); ?>
        <?php endif; ?>
    </td>
</tr>
