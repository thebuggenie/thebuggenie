<?php

	$tbg_response->setTitle(__('Configure workflows'));
	
?>
<table style="table-layout: fixed; width: 100%" cellpadding=0 cellspacing=0>
	<tr>
		<?php include_component('leftmenu', array('selected_section' => TBGSettings::CONFIGURATION_SECTION_WORKFLOW)); ?>
		<td valign="top">
			<?php include_template('configuration/workflowmenu', array('selected_tab' => 'workflows')); ?>
			<div class="content" style="width: 750px;">
				<?php if (isset($error)): ?>
					<div class="rounded_box red borderless" style="margin-top: 5px;">
						<?php echo $error; ?>
					</div>
				<?php endif; ?>
				<div class="rounded_box lightyellow" style="margin-top: 5px;">
					<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_workflow'); ?>" id="add_workflow_form" method="post">
						<label for="add_workflow_name"><?php echo __('Add an empty workflow'); ?></label>
						<input type="text" name="workflow_name" id="add_workflow_name" value="<?php echo __('Blank workflow'); ?>" style="width: 300px;">
						<div style="text-align: right; float: right;">
							<?php echo image_tag('spinning_16.gif', array('style' => 'margin-right: 5px; display: none;', 'id' => 'add_workflow_indicator')); ?>
							<input type="submit" value="<?php echo __('Add workflow'); ?>">
						</div>
					</form>
				</div>
				<ul class="scheme_list workflow_list simple_list" id="workflows_list">
					<?php foreach ($workflows as $workflow): ?>
						<?php include_template('configuration/workflow', array('workflow' => $workflow)); ?>
					<?php endforeach; ?>
				</ul>
			</div>
		</td>
	</tr>
</table>