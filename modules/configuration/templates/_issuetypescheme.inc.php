<li id="workflow_<?php echo $scheme->getID(); ?>" class="rounded_box lightgrey">
	<table>
		<tr>
			<td class="workflow_info scheme">
				<div class="workflow_name"><?php echo $scheme->getName(); ?></div>
				<div class="workflow_description"><?php echo $scheme->getDescription(); ?></div>
			</td>
			<td class="workflow_actions">
				<?php echo __('Actions: %list%', array('%list%' => '')); ?><br>
				<a href="#" class="rounded_box"><?php echo image_tag('icon_delete.png', array('title' => __('Delete this issue type scheme'))); ?></a>
				<a href="#" class="rounded_box"><?php echo image_tag('icon_copy.png', array('title' => __('Create a copy of this issue type scheme'))); ?></a>
				<?php echo link_tag(make_url('configure_issuetypes_scheme', array('scheme_id' => $scheme->getID())), image_tag('icon_workflow_scheme_edit.png', array('title' => __('Show / edit issue type associations'))), array('class' => 'rounded_box')); ?></a>
			</td>
		</tr>
	</table>
</li>