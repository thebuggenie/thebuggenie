<div class="workflow_browser_step_transition">
	<?php echo javascript_link_tag(image_tag('icon_delete.png'), array('class' => 'image', 'style' => 'float: right;')); ?>
	<?php echo link_tag(make_url('configure_workflow_transition', array('workflow_id' => $transition->getWorkflow()->getID(), 'transition_id' => $transition->getID())), $transition->getName()); ?>
</div>