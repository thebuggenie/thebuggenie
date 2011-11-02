<table style="width: 100%;" cellpadding="0" cellspacing="0" class="issue_affects" id="affected_list">
	<tr>
		<th style="width: 16px; text-align: right; padding-top: 0px; padding-right: 0px; padding-bottom: 0px; padding-left: 3px;"></th><th><?php echo __('Name'); ?></th><th><?php echo __('Status'); ?></th><th style="width: 90px; text-align: right; padding-top: 0px; padding-right: 3px; padding-bottom: 0px; padding-left: 0px;"><?php echo __('Confirmed'); ?></th>
	</tr>
	<tr id="no_affected" <?php if ($count != 0): ?>style="display: none;"<?php endif; ?>><td colspan="4"><span class="faded_out"><?php echo __('There are no items'); ?></span></td></tr>
	<?php
		if ($issue->getProject()->isEditionsEnabled()):
			foreach ($editions as $edition):
				$item = $edition;
				$itemtype = 'edition';
				$itemtypename = __('Edition');
				
				include_template('main/affecteditem', array('item' => $item, 'itemtype' => $itemtype, 'itemtypename' => $itemtypename, 'issue' => $issue, 'statuses' => $statuses));
			endforeach;
		endif;
	?>
	<?php
		if ($issue->getProject()->isComponentsEnabled()):
			foreach ($components as $component):
				$item = $component;
				$itemtype = 'component';
				$itemtypename = __('Component');
				
				include_template('main/affecteditem', array('item' => $item, 'itemtype' => $itemtype, 'itemtypename' => $itemtypename, 'issue' => $issue, 'statuses' => $statuses));
			endforeach;
		endif;
	?>
	<?php
		if ($issue->getProject()->isBuildsEnabled()):
			foreach ($builds as $build):
				$item = $build;
				$itemtype = 'build';
				$itemtypename = __('Release');
				
				include_template('main/affecteditem', array('item' => $item, 'itemtype' => $itemtype, 'itemtypename' => $itemtypename, 'issue' => $issue, 'statuses' => $statuses));
			endforeach;
		endif;
	?>
</table>
