<div class="rounded_box shadowed white" id="step_<?php echo $step->getID(); ?>_transition_add" style="width: 720px; position: absolute; padding: 5px; margin: 5px; display: none; z-index: 100;">
    <div class="header"><?php echo __('Add outgoing transition from step "%step_name"', array('%step_name' => $step->getName())); ?></div>
    <div class="content">
        <form accept-charset="<?php echo \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>" method="post" action="<?php echo make_url('configure_workflow_add_transition', array('workflow_id' => $step->getWorkflow()->getID(), 'step_id' => $step->getID())); ?>">
            <ul class="simple_list">
                <li>
                    <input type="radio" name="add_transition_type" value="existing" id="step_<?php echo $step->getID(); ?>_add_existing_transition">
                    <label for="step_<?php echo $step->getID(); ?>_add_existing_transition"><?php echo __('Existing transition'); ?></label>
                    <select name="existing_transition_id" onclick="$('step_<?php echo $step->getID(); ?>_add_existing_transition').checked = true;">
                        <?php foreach ($step->getWorkflow()->getTransitions() as $transition): ?>
                            <?php if ($step->hasOutgoingTransition($transition) || $transition->getOutgoingStep()->getID() == $step->getID()) continue; ?>
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
                                <?php foreach (\thebuggenie\core\entities\WorkflowTransition::getTemplates() as $template_key => $template_name): ?>
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
                <?php echo __('%add_transition or %cancel', array('%add_transition' => '', '%cancel' => '')); ?>
                <?php echo javascript_link_tag(__('cancel'), array('onclick' => "$('step_{$step->getID()}_transition_add').toggle()")); ?>
            </div>
        </form>
    </div>
</div>
