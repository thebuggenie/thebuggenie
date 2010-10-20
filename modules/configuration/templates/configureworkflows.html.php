<?php

	$tbg_response->setTitle(__('Configure workflows'));
	
?>
<table style="table-layout: fixed; width: 100%" cellpadding=0 cellspacing=0>
	<tr>
		<?php include_component('leftmenu', array('selected_section' => TBGSettings::CONFIGURATION_SECTION_WORKFLOW)); ?>
		<td valign="top">
			<?php include_template('configuration/workflowmenu', array('selected_tab' => 'workflows')); ?>
			<div class="content" style="width: 750px;">
				<ul class="workflow_list simple_list">
					<?php foreach ($workflows as $workflow): ?>
						<li id="workflow_<?php echo $workflow->getID(); ?>" class="rounded_box lightgrey">
							<div class="workflow_name"><?php echo $workflow->getName(); ?><?php if ($workflow->isCore()): ?>&nbsp;<span class="builtin"><?php echo __('This workflow is builtin'); ?></span><?php endif; ?></div>
							<div class="workflow_description"><?php echo $workflow->getDescription(); ?></div>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</td>
	</tr>
</table>