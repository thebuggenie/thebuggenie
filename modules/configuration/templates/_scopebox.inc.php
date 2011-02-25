<div class="rounded_box<?php if ($scope->isDefault()): ?> lightgrey<?php else: ?> lightyellow<?php endif; ?> borderless" style="margin-bottom: 5px; min-height: 25px;">
	<?php if (!$scope->isDefault()): ?>
		<a href="javascript:void(0);" onclick="failedMessage('not implemented yet')" class="rounded_box action_button"><?php echo image_tag('icon_delete.png', array('title' => __('Delete this scope'))); ?></a>
		<?php echo link_tag(make_url('configure_scope', array('id' => $scope->getID())), image_tag('icon_edit.png', array('title' => __('Edit scope settings'))), array('class' => 'rounded_box action_button')); ?></a>
	<?php endif; ?>
	<a href="javascript:void(0);" onclick="failedMessage('not implemented yet')" class="rounded_box action_button"><?php echo image_tag('icon_copy.png', array('title' => __('Create a copy of this scope for a different hostname'))); ?></a>
	<div class="header">
		<?php echo $scope->getName(); ?>
		<span style="font-weight: normal;" class="faded_out">
			<?php if (!$scope->isDefault()): ?>
				(<?php echo join(' / ', $scope->getHostnames()); ?>)
			<?php else: ?>
				<span style="font-size: 11px;">(<?php echo __('All hostnames not covered by other scopes'); ?>)</span>
			<?php endif; ?>
		</span>
	</div>
	<div class="content">
		<div class="faded_out scope_description">
			<?php if (!$scope->isDefault()): ?>
				<?php echo $scope->getDescription(); ?>
			<?php else: ?>
				<?php echo __('This is the default scope. It will be loaded and used whenever The Bug Genie is accessed on a hostname for which there is not explicit scope defined.'); ?>
			<?php endif; ?>
		</div>
	</div>
</div>