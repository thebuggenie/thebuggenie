<?php

    if ($workflow instanceof \thebuggenie\core\entities\Workflow)
        $tbg_response->setTitle(__('Configure workflow "%workflow_name"', array('%workflow_name' => $workflow->getName())));
    else
        $tbg_response->setTitle(__('Configure workflows'));
    
?>
<table style="table-layout: fixed; width: 100%" cellpadding=0 cellspacing=0 class="configuration_page">
    <tr>
        <?php include_component('leftmenu', array('selected_section' => \thebuggenie\core\framework\Settings::CONFIGURATION_SECTION_WORKFLOW)); ?>
        <td valign="top" style="padding-left: 15px;">
            <?php include_component('configuration/workflowmenu', array('selected_tab' => 'transition', 'workflow' => $workflow, 'transition' => $transition)); ?>
            <div class="content" style="width: 730px;" id="workflow_step_container">
                <?php if ($transition instanceof \thebuggenie\core\entities\WorkflowTransition): ?>
                    <h3>
                        <?php if (!$transition->isCore()): ?>
                            <?php echo javascript_link_tag(__('Edit details'), array('onclick' => "\$('transition_details_form').toggle();\$('transition_details_info').toggle();", 'class' => 'button button-silver')); ?>
                        <?php endif; ?>
                        <?php echo __('Transition "%transition_name"', array('%transition_name' => $transition->getName())); ?>
                    </h3>
                    <div class="workflow_step_intro">
                        <div class="content">
                            <?php echo __('This page shows all the available details for this transition for the selected workflow, as well as incoming and outgoing steps from this transition.'); ?>
                            <?php echo __('You can edit all details about the selected transitions from this page.'); ?><br>
                        </div>
                    </div>
                    <div class="lightyellowbox" id="workflow_browser_step">
                        <div class="header"><?php echo __('Transition path'); ?></div>
                        <div class="content">
                            <?php if ($transition->getNumberOfIncomingSteps() == 0 && $transition->getID() !== $workflow->getInitialTransition()->getID()): ?>
                                <div class="faded_out"><?php echo __("This transaction doesn't have any originating step"); ?></div>
                            <?php elseif ($transition === $workflow->getInitialTransition()): ?>
                                <div class="faded_out"><?php echo __("Issue is created"); ?></div>
                            <?php else: ?>
                                <?php

                                $output = array();
                                foreach ($transition->getIncomingSteps() as $step)
                                {
                                    $output[] = '<div class="workflow_browser_step_transition">'.link_tag(make_url('configure_workflow_step', array('workflow_id' => $step->getWorkflow()->getID(), 'step_id' => $step->getID())), $step->getName())."</div>";
                                }
                                $glue = "<div class=\"faded_out\">".__('%a_workflow_step_transition or %a_workflow_step_transition', array('%a_workflow_step_transition' => ''))."</div>";
                                echo join($glue, $output);

                                ?>
                            <?php endif; ?>
                            <div class="workflow_browser_step_image"><?php echo image_tag('workflow_step_transitions_outgoing.png'); ?></div>
                            <div class="workflow_browser_step_name"><?php echo $transition->getName(); ?></div>
                            <div class="workflow_browser_step_image"><?php echo image_tag('workflow_step_transitions_outgoing.png'); ?></div>
                            <div class="workflow_browser_step_transition"><?php echo link_tag(make_url('configure_workflow_step', array('workflow_id' => $transition->getOutgoingStep()->getWorkflow()->getID(), 'step_id' => $transition->getOutgoingStep()->getID())), $transition->getOutgoingStep()->getName()); ?></div>
                        </div>
                    </div>
                    <div id="workflow_details_transition">
                        <dl id="transition_details_info">
                            <dt><?php echo __('Name'); ?></dt>
                            <dd><?php echo $transition->getName(); ?></dd>
                            <dt><?php echo __('Description'); ?></dt>
                            <dd class="description"><?php echo $transition->getDescription(); ?></dd>
                            <dt><?php echo __('Template'); ?></dt>
                            <dd><?php echo ($transition->hasTemplate()) ? $transition->getTemplateName() : __('No template used - transition happens instantly'); ?></dd>
                            <dt><?php echo __('Outgoing step'); ?></dt>
                            <dd><?php echo link_tag(make_url('configure_workflow_step', array('workflow_id' => $transition->getOutgoingStep()->getWorkflow()->getID(), 'step_id' => $transition->getOutgoingStep()->getID())), $transition->getOutgoingStep()->getName()); ?></dd>
                        </dl>
                        <?php if (!$transition->isCore()): ?>
                            <form accept-charset="<?php echo \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>" method="post" action="<?php echo make_url('configure_workflow_edit_transition', array('workflow_id' => $transition->getWorkflow()->getID(), 'transition_id' => $transition->getID())); ?>" id="transition_details_form" style="display: none;" onsubmit="$('transition_update_indicator').show();$('update_transition_buttons').hide();">
                                <dl>
                                    <?php if (!$transition->isInitialTransition()): ?>
                                        <dt><label for="edit_transition_<?php echo $transition->getID(); ?>_name"><?php echo __('Transition name'); ?></label></dt>
                                        <dd>
                                            <input type="text" id="edit_transition_<?php echo $transition->getID(); ?>_name" name="transition_name" style="width: 300px;" value="<?php echo $transition->getName(); ?>"><br>
                                            <div class="faded_out"><?php echo __('This name will be presented to the user as a link'); ?></div>
                                        </dd>
                                        <dt><label for="edit_transition_<?php echo $transition->getID(); ?>_description" class="optional"><?php echo __('Description'); ?></label></dt>
                                        <dd>
                                            <input type="text" id="edit_transition_<?php echo $transition->getID(); ?>_description" name="transition_description" style="width: 300px;" value="<?php echo $transition->getDescription(); ?>">
                                            <div class="faded_out"><?php echo __('This optional description will be presented to the user'); ?></div>
                                        </dd>
                                        <dt><label for="edit_transition_<?php echo $transition->getID(); ?>_template"><?php echo __('Popup template'); ?></label></dt>
                                        <dd>
                                            <select id="edit_transition_<?php echo $transition->getID(); ?>_template" name="template">
                                                <?php foreach (\thebuggenie\core\entities\WorkflowTransition::getTemplates() as $template_key => $template_name): ?>
                                                    <option value="<?php echo $template_key; ?>"<?php if ($transition->getTemplate() == $template_key): ?> selected<?php endif; ?>><?php echo $template_name; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </dd>
                                    <?php else: ?>
                                        <dt><?php echo __('Name'); ?></dt>
                                        <dd><?php echo $transition->getName(); ?></dd>
                                        <dt><?php echo __('Description'); ?></dt>
                                        <dd class="description"><?php echo $transition->getDescription(); ?></dd>
                                        <dt><?php echo __('Template'); ?></dt>
                                        <dd><?php echo __('No template used - transition happens instantly'); ?></dd>
                                    <?php endif; ?>
                                    <dt><label for="edit_transition_<?php echo $transition->getID(); ?>_outgoing_step_id"><?php echo __('Outgoing step'); ?></label></dt>
                                    <dd>
                                        <select id="edit_transition_<?php echo $transition->getID(); ?>_outgoing_step_id" name="outgoing_step_id">
                                            <?php foreach ($transition->getWorkflow()->getSteps() as $workflow_step): ?>
                                                <option value="<?php echo $workflow_step->getID(); ?>"<?php if ($workflow_step->getID() == $transition->getOutgoingStep()->getID()): ?> selected<?php endif; ?>><?php echo $workflow_step->getName(); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </dd>
                                </dl>
                                <br style="clear: both;">
                                <div style="text-align: right; clear: both; padding: 10px 0 0 0;" id="update_transition_buttons">
                                    <input type="submit" value="<?php echo __('Update transition details'); ?>" name="edit">
                                    <?php echo __('%update_transition_details or %cancel', array('%update_transition_details' => '', '%cancel' => '')); ?>
                                    <b><?php echo javascript_link_tag(__('cancel'), array('onclick' => "\$('transition_details_form').toggle();\$('transition_details_info').toggle();")); ?></b>
                                </div>
                                <div style="text-align: right; padding: 10px 0 10px 0; display: none;" id="transition_update_indicator"><span style="float: right;"><?php echo image_tag('spinning_16.gif'); ?></span>&nbsp;<?php echo __('Please wait'); ?></div>
                            </form>
                        <?php endif; ?>
                    </div>
                    <br style="clear: both;">
                    <div id="workflow_transition_actions_validations">
                        <div id="pre_validation_tab_pane">
                            <h3>
                                <?php if (!$transition->isCore()): ?>
                                    <a href="javascript:void(0);" class="button button-silver dropper">Add validation rule</a>
                                    <ul class="simple_list rounded_box white shadowed popup_box more_actions_dropdown" onclick="$(this).previous().toggleClassName('button-pressed');$(this).toggle();" id="add_pre_validation_rule">
                                        <?php foreach (\thebuggenie\core\entities\WorkflowTransitionValidationRule::getAvailablePreValidationRules() as $key => $description): ?>
                                            <li <?php if ($transition->hasPreValidationRule($key)) echo ' style="display: none;"'; ?> id="add_workflowtransitionprevalidationrule_<?php echo $key; ?>">
                                                <a href="javascript:void(0);" onclick="TBG.Config.Workflows.Transition.Validations.add('<?php echo make_url('configure_workflow_transition_add_validation_rule', array('workflow_id' => $transition->getWorkflow()->getID(), 'transition_id' => $transition->getID(), 'postorpre' => 'pre', 'rule' => $key)); ?>', 'pre', '<?php echo $key; ?>');"><?php echo $description; ?></a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                                <?php echo __('Pre-transition validation rules'); ?>
                                <?php echo image_tag('spinning_16.gif', array('id' => 'workflowtransitionprevalidationrule_add_indicator', 'style' => 'display: none; margin-left: 5px;')); ?>
                            </h3>
                            <?php if ($transition !== $workflow->getInitialTransition()): ?>
                                <div class="content" style="padding: 5px 0 10px 2px;">
                                    <?php echo __('The following validation rules has to be fullfilled for the transition to be available to the user'); ?>
                                </div>
                                <table cellpadding="0" cellspacing="0" style="width: 100%;">
                                    <tbody class="hover_highlight" id="workflowtransitionprevalidationrules_list">
                                    <?php foreach ($transition->getPreValidationRules() as $rule): ?>
                                        <?php include_component('configuration/workflowtransitionvalidationrule', array('rule' => $rule)); ?>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                                <span class="faded_out" id="no_workflowtransitionprevalidationrules"<?php if ($transition->hasPreValidationRules()): ?> style="display: none;"<?php endif; ?>><?php echo __('This transition has no pre-validation rules'); ?></span>
                            <?php else: ?>
                                <span class="faded_out"><?php echo __('This is the initial transition, so no pre-transition validation is performed'); ?></span>
                            <?php endif; ?>
                        </div>
                        <div id="post_validation_tab_pane">
                            <h3>
                                <?php if (!$transition->isCore() && $transition->hasTemplate()): ?>
                                    <a href="javascript:void(0);" class="button button-silver dropper">Add validation rule</a>
                                    <ul class="simple_list rounded_box white shadowed popup_box more_actions_dropdown" onclick="$(this).previous().toggleClassName('button-pressed');$(this).toggle();" id="add_post_validation_rule">
                                        <?php foreach (\thebuggenie\core\entities\WorkflowTransitionValidationRule::getAvailablePostValidationRules() as $key => $description): ?>
                                            <li <?php if ($transition->hasPostValidationRule($key)) echo ' style="display: none;"'; ?> id="add_workflowtransitionpostvalidationrule_<?php echo $key; ?>"><a href="javascript:void(0);" onclick="TBG.Config.Workflows.Transition.Validations.add('<?php echo make_url('configure_workflow_transition_add_validation_rule', array('workflow_id' => $transition->getWorkflow()->getID(), 'transition_id' => $transition->getID(), 'postorpre' => 'post', 'rule' => $key)); ?>', 'post', '<?php echo $key; ?>');"><?php echo $description; ?></a></li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                                <?php echo __('Post-transition validation rules'); ?>
                                <?php echo image_tag('spinning_16.gif', array('id' => 'workflowtransitionpostvalidationrule_add_indicator', 'style' => 'display: none; margin-left: 5px;')); ?>
                            </h3>
                            <?php if ($transition->hasTemplate()): ?>
                                <div class="content" style="padding: 5px 0 10px 2px;">
                                    <?php echo __('The following validation rules will be applied to the input given by the user in the transition view. If the validation fails, the transition will not take place.'); ?>
                                </div>
                                <table cellpadding="0" cellspacing="0" style="width: 100%;">
                                    <tbody class="hover_highlight" id="workflowtransitionpostvalidationrules_list">
                                    <?php foreach ($transition->getPostValidationRules() as $rule): ?>
                                        <?php include_component('configuration/workflowtransitionvalidationrule', array('rule' => $rule)); ?>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                                <span class="faded_out" id="no_workflowtransitionpostvalidationrules"<?php if ($transition->hasPostValidationRules()): ?> style="display: none;"<?php endif; ?>><?php echo __('This transition has no post validation rules'); ?></span>
                            <?php else: ?>
                                <span class="faded_out"><?php echo __('This transition does not use any template, so user input cannot be validated'); ?></span>
                            <?php endif; ?>
                        </div>
                        <div id="actions_tab_pane">
                            <h3>
                                <?php if (!$transition->isCore()): ?>
                                    <a href="javascript:void(0);" class="button button-silver dropper">Add transition action</a>
                                    <div class="rounded_box white shadowed popup_box more_actions_dropdown" onclick="$(this).previous().toggleClassName('button-pressed');$(this).toggle();" id="add_post_action">
                                        <div class="column">
                                            <h1><?php echo __('Set issue fields'); ?></h1>
                                            <ul class="simple_list">
                                                <?php foreach (\thebuggenie\core\entities\WorkflowTransitionAction::getAvailableTransitionActions('set') as $key => $description): ?>
                                                    <li <?php if ($transition->hasAction($key)) echo ' style="display: none;"'; ?> id="add_workflowtransitionaction_<?php echo $key; ?>"><a href="javascript:void(0);" onclick="TBG.Config.Workflows.Transition.Actions.add('<?php echo make_url('configure_workflow_transition_add_action', array('workflow_id' => $transition->getWorkflow()->getID(), 'transition_id' => $transition->getID(), 'action_type' => $key)); ?>', '<?php echo $key; ?>');" title="<?php echo $description; ?>"><?php echo $description; ?></a></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                        <div class="column">
                                            <h1><?php echo __('Clear issue fields'); ?></h1>
                                            <ul class="simple_list">
                                                <?php foreach (\thebuggenie\core\entities\WorkflowTransitionAction::getAvailableTransitionActions('clear') as $key => $description): ?>
                                                    <li <?php if ($transition->hasAction($key)) echo ' style="display: none;"'; ?> id="add_workflowtransitionaction_<?php echo $key; ?>"><a href="javascript:void(0);" onclick="TBG.Config.Workflows.Transition.Actions.add('<?php echo make_url('configure_workflow_transition_add_action', array('workflow_id' => $transition->getWorkflow()->getID(), 'transition_id' => $transition->getID(), 'action_type' => $key)); ?>', '<?php echo $key; ?>');" title="<?php echo $description; ?>"><?php echo $description; ?></a></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                        <div class="column">
                                            <h1><?php echo __('Special actions'); ?></h1>
                                            <ul class="simple_list">
                                                <?php foreach (\thebuggenie\core\entities\WorkflowTransitionAction::getAvailableTransitionActions('special') as $key => $description): ?>
                                                    <li <?php if ($transition->hasAction($key)) echo ' style="display: none;"'; ?> id="add_workflowtransitionaction_<?php echo $key; ?>"><a href="javascript:void(0);" onclick="TBG.Config.Workflows.Transition.Actions.add('<?php echo make_url('configure_workflow_transition_add_action', array('workflow_id' => $transition->getWorkflow()->getID(), 'transition_id' => $transition->getID(), 'action_type' => $key)); ?>', '<?php echo $key; ?>');" title="<?php echo $description; ?>"><?php echo $description; ?></a></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <?php echo __('Post-transition actions'); ?>
                                <?php echo image_tag('spinning_16.gif', array('id' => 'workflowtransitionaction_add_indicator', 'style' => 'display: none; margin-left: 5px;')); ?>
                            </h3>
                            <div class="content" style="padding: 5px 0 10px 2px;">
                                <?php echo __('The following actions will be applied to the issue during this transition.'); ?>
                            </div>
                            <table cellpadding="0" cellspacing="0" style="width: 100%;">
                                <tbody class="hover_highlight" id="workflowtransitionactions_list">
                                <?php if ($transition->hasTemplate()): ?>
                                    <tr>
                                        <td colspan="2"><?php echo __('Add a comment if one is specified'); ?></td>
                                    </tr>
                                <?php endif; ?>
                                <?php foreach ($transition->getActions() as $action): ?>
                                    <?php include_component('configuration/workflowtransitionaction', array('action' => $action)); ?>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                            <span class="faded_out" id="no_workflowtransitionactions"<?php if ($transition->hasActions() || $transition->hasTemplate()): ?> style="display: none;"<?php endif; ?>><?php echo __('This transition has no actions'); ?></span>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="redbox" id="no_such_workflow_error">
                        <div class="header"><?php echo $error; ?></div>
                    </div>
                <?php endif; ?>
            </div>
        </td>
    </tr>
</table>
