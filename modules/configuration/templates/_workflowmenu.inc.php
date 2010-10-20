<div style="width: 750px;" id="config_workflows">
	<div class="config_header"><?php echo __('Configure workflows'); ?></div>
	<div class="content">
		<?php echo __('Workflow lets you define the lifecycle of an issue. You can define steps, transitions and more, that makes an issue move through its defined lifecycle.'); ?>
		<?php echo __('You can read more about how the workflow in The Bug Genie works and is set up in %link_to_wiki_workflow%.', array('%link_to_wiki_workflow%' => link_tag(make_url('publish_article', array('article_name' => 'TheBugGenie:Workflow')), 'TheBugGenie:Workflow'))); ?>
	</div>
	<br style="clear: both;">
	<div class="tab_menu" style="margin-top: 20px;">
		<ul id="workflow_menu">
			<li<?php if ($selected_tab == 'workflows'): ?> class="selected"<?php endif; ?>><?php echo link_tag(make_url('configure_workflow_workflows'), __('Workflows')); ?></li>
			<li<?php if ($selected_tab == 'schemes'): ?> class="selected"<?php endif; ?>><?php echo link_tag(make_url('configure_workflow_schemes'), __('Workflow schemes')); ?></li>
			<?php if (isset($workflow)): ?>
				<li<?php if ($selected_tab == 'workflow'): ?> class="selected"<?php endif; ?>><?php echo link_tag(make_url('configure_workflow_steps', array('workflow_id' => $workflow->getID())), $workflow->getName()); ?></li>
				<?php if (isset($step)): ?>
					<li<?php if ($selected_tab == 'step'): ?> class="selected"<?php endif; ?>><?php echo link_tag(make_url('configure_workflow_step', array('workflow_id' => $workflow->getID(), 'step_id' => $step->getID())), __('Step: %step_name%', array('%step_name%' => $step->getName()))); ?></li>
				<?php endif; ?>
			<?php endif; ?>
		</ul>
	</div>
</div>