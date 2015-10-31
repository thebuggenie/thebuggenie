<div style="width: 730px;" id="config_workflows">
    <h3><?php echo __('Configure workflows'); ?></h3>
    <div class="content faded_out">
        <p>
            <?php echo __('Workflow lets you define the lifecycle of an issue. You can define steps, transitions and more, that makes an issue move through its defined lifecycle.'); ?>
            <?php echo __('You can read more about how the workflow in The Bug Genie works and is set up in %link_to_wiki_workflow.', array('%link_to_wiki_workflow' => link_tag(make_url('publish_article', array('article_name' => 'TheBugGenie:Workflow')), 'TheBugGenie:Workflow'))); ?>
        </p>
    </div>
    <?php if (\thebuggenie\core\framework\Context::getScope()->getMaxWorkflowsLimit()): ?>
        <div class="faded_out dark" style="margin: 12px 0;">
            <?php echo __('This instance is currently using %num of max %max custom workflows', array('%num' => '<b id="current_workflow_num_count">'.\thebuggenie\core\entities\Workflow::getCustomWorkflowsCount().'</b>', '%max' => '<b>'.\thebuggenie\core\framework\Context::getScope()->getMaxWorkflowsLimit().'</b>')); ?>
        </div>
    <?php endif; ?>
    <br style="clear: both;">
    <div class="tab_menu inset">
        <ul id="workflow_menu">
            <li<?php if ($selected_tab == 'workflows'): ?> class="selected"<?php endif; ?>><?php echo link_tag(make_url('configure_workflow_workflows'), __('Workflows')); ?></li>
            <?php if (isset($workflow)): ?>
                <li<?php if ($selected_tab == 'workflow'): ?> class="selected"<?php endif; ?>><?php echo link_tag(make_url('configure_workflow_steps', array('workflow_id' => $workflow->getID())), $workflow->getName()); ?></li>
                <?php if (isset($step)): ?>
                    <li<?php if ($selected_tab == 'step'): ?> class="selected"<?php endif; ?>><?php echo link_tag(make_url('configure_workflow_step', array('workflow_id' => $workflow->getID(), 'step_id' => $step->getID())), __('Step: %step_name', array('%step_name' => $step->getName()))); ?></li>
                <?php endif; ?>
                <?php if (isset($transition)): ?>
                    <li<?php if ($selected_tab == 'transition'): ?> class="selected"<?php endif; ?>><?php echo link_tag(make_url('configure_workflow_transition', array('workflow_id' => $workflow->getID(), 'transition_id' => $transition->getID())), __('Transition: %transition_name', array('%transition_name' => $transition->getName()))); ?></li>
                <?php endif; ?>
            <?php endif; ?>
            <li<?php if ($selected_tab == 'schemes'): ?> class="selected"<?php endif; ?>><?php echo link_tag(make_url('configure_workflow_schemes'), __('Workflow schemes')); ?></li>
            <?php if (isset($scheme)): ?>
                <li<?php if ($selected_tab == 'scheme'): ?> class="selected"<?php endif; ?>><?php echo link_tag(make_url('configure_workflow_scheme', array('scheme_id' => $scheme->getID())), $scheme->getName()); ?></li>
            <?php endif; ?>
        </ul>
    </div>
</div>
