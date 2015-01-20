<?php

    if ($workflow instanceof \thebuggenie\core\entities\Workflow)
    {
        $tbg_response->setTitle(__('Configure workflow "%workflow_name"', array('%workflow_name' => $workflow->getName())));
    }
    else
    {
        $tbg_response->setTitle(__('Configure workflows'));
    }
    
?>
<table style="table-layout: fixed; width: 100%" cellpadding=0 cellspacing=0 class="configuration_page">
    <tr>
        <?php include_component('leftmenu', array('selected_section' => \thebuggenie\core\framework\Settings::CONFIGURATION_SECTION_WORKFLOW)); ?>
        <td valign="top" style="padding-left: 15px;">
            <?php include_component('configuration/workflowmenu', array('selected_tab' => 'workflow', 'workflow' => $workflow)); ?>
            <div class="content" style="width: 730px;" id="workflow_steps_container">
                <?php if ($workflow instanceof \thebuggenie\core\entities\Workflow): ?>
                    <div class="greybox workflow_steps_intro">
                        <div class="header"><?php echo __('Editing steps for %workflow_name', array('%workflow_name' => $workflow->getName())); ?></div>
                        <div class="content">
                            <?php echo __('This page shows all the available steps for the selected workflow, as well as transitions between these steps.'); ?>
                            <?php echo __('You can add and remove steps from this page, as well as manage the transitions between them.'); ?><br>
                            <br>
                            <?php echo __('Steps without any incoming transitions are shown as faded out.'); ?><br>
                            <?php if (!$workflow->isCore()): ?>
                                <br>
                                <b><?php echo javascript_link_tag(__('Add a step'), array('onclick' => "\$('add_step_div').toggle();")); ?></b>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php if (!$workflow->isCore()): ?>
                        <div class="rounded_box shadowed white" id="add_step_div" style="width: 720px; position: absolute; padding: 5px; margin: 5px; display: none; z-index: 100;">
                            <div class="header"><?php echo __('Create a new workflow step'); ?></div>
                            <form accept-charset="<?php echo \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>" method="post" action="<?php echo make_url('configure_workflow_add_step', array('workflow_id' => $workflow->getID())); ?>" onsubmit="$('step_update_indicator').show();$('update_step_buttons').hide();">
                                <dl>
                                    <dt><label for="step_name"><?php echo __('Name'); ?></label></dt>
                                    <dd><input type="text" name="name" id="step_name" style="width: 150px;"></dd>
                                    <dt><label for="step_description"><?php echo __('Description'); ?></label></dt>
                                    <dd><input type="text" name="description" id="step_description" style="width: 250px;"></dd>
                                    <dt><label for="step_state"><?php echo __('State'); ?></label></dt>
                                    <dd>
                                        <select name="state" id="step_state" style="width: 125px;">
                                            <option value="<?php echo \thebuggenie\core\entities\Issue::STATE_OPEN; ?>"><?php echo __('Open'); ?></option>
                                            <option value="<?php echo \thebuggenie\core\entities\Issue::STATE_CLOSED; ?>"><?php echo __('Closed'); ?></option>
                                        </select>
                                        <select name="is_editable" id="step_editable" style="width: 125px;">
                                            <option value="1"><?php echo __('Editable'); ?></option>
                                            <option value="0"><?php echo __('Not editable'); ?></option>
                                        </select>
                                    </dd>
                                    <dt><label for="step_status"><?php echo __('Connected status'); ?></label></dt>
                                    <dd>
                                        <select name="status_id" id="step_status">
                                            <option value="0" selected><?php echo __('Not connected to a status'); ?></option>
                                            <?php foreach (\thebuggenie\core\entities\Status::getAll() as $status): ?>
                                            <option value="<?php echo $status->getID(); ?>"><?php echo $status->getName(); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </dd>
                                </dl>
                                <br style="clear: both;">
                                <div style="text-align: right; clear: both; padding: 10px 0 0 0;" id="update_step_buttons">
                                    <input type="submit" value="<?php echo __('Update step details'); ?>" name="edit">
                                    <?php echo __('%update_step_details or %cancel', array('%update_step_details' => '', '%cancel' => '')); ?>
                                    <b><?php echo javascript_link_tag(__('cancel'), array('onclick' => "\$('add_step_div').toggle();")); ?></b>
                                </div>
                                <div style="text-align: right; padding: 10px 0 10px 0; display: none;" id="step_update_indicator"><span style="float: right;"><?php echo image_tag('spinning_16.gif'); ?></span>&nbsp;<?php echo __('Please wait'); ?></div>
                            </form>
                        </div>
                    <?php endif; ?>
                    <table id="workflow_steps_list" cellpadding="0" cellspacing="0">
                        <thead>
                            <tr>
                                <th><?php echo __('Step name'); ?></th>
                                <th><?php echo __('Connected status'); ?></th>
                                <th><?php echo __('Outgoing transitions'); ?></th>
                                <th><?php echo __('Actions'); ?></th>
                            </tr>
                        </thead>
                        <tbody class="padded_table hover_highlight" id="workflow_steps_list_tbody">
                            <?php include_component('configuration/workflowstep', array('step' => $workflow->getInitialTransition()->getOutgoingStep(), 'workflow' => $workflow)); ?>
                            <?php foreach ($workflow->getSteps() as $step): ?>
                                <?php if ($step->getID() == $workflow->getInitialTransition()->getOutgoingStep()->getID()) continue; ?>
                                <?php include_component('configuration/workflowstep', array('step' => $step, 'workflow' => $workflow)); ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="redbox" id="no_such_workflow_error">
                        <div class="header"><?php echo $error; ?></div>
                    </div>
                <?php endif; ?>
            </div>
        </td>
    </tr>
</table>
