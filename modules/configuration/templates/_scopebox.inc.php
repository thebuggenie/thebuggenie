<div class="rounded_box<?php if ($scope->isDefault()): ?> lightgrey<?php endif; ?> borderless">
	<div class="header"><?php echo $scope->getName(); ?></div>
	<div class="content">
		<?php if (!$scope->isDefault()): ?>
			<a href="javascript:void(0);" onclick="failedMessage('not implemented yet')" class="rounded_box action_button"><?php echo image_tag('icon_delete.png', array('title' => __('Delete this scope'))); ?></a>
		<?php endif; ?>
		<a href="javascript:void(0);" onclick="failedMessage('not implemented yet')" class="rounded_box action_button"><?php echo image_tag('icon_copy.png', array('title' => __('Create a copy of this scope for a different hostname'))); ?></a>
		<div class="faded_out scope_description">
			<?php if (!$scope->isDefault()): ?>
				<?php echo $scope->getDescription(); ?>
			<?php else: ?>
				<?php echo __('This is the default scope, loaded and used whenever The Bug Genie is accessed on a hostname it does not have an explicit scope for.'); ?>
			<?php endif; ?>
		</div>
		<div class="header"><?php echo __('Hostnames active for this scope:'); ?></div>
		<ul class="simple_list">
			<?php foreach ($scope->getHostnames() as $hostname): ?>
				<li>
					<span><?php echo $hostname; ?></span>
					<?php if ($hostname == '*'): ?>
						(<?php echo __('This means "all hostnames not covered by other scopes"'); ?>)
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</div>