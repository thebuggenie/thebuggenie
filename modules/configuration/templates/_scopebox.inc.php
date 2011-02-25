<div class="rounded_box<?php if ($scope->isDefault()): ?> lightgrey<?php endif; ?> borderless">
	<div class="header"><?php echo $scope->getName(); ?></div>
	<div class="content">
		<?php if (!$scope->isDefault()): ?>
			<div class="nice_button" style="float: right;"><input type="button" value="<?php echo __('Delete'); ?>" onclick="failedMessage('not implemented yet')"></input></div>
		<?php endif; ?>
		<div class="nice_button" style="float: right;"><input type="button" value="<?php echo __('Copy'); ?>" onclick="failedMessage('not implemented yet')"></input></div>
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