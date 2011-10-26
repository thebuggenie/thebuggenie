<div class="rounded_box<?php if ($scope->isDefault()): ?> lightgrey<?php else: ?> lightyellow<?php endif; ?> borderless" style="margin-bottom: 5px; min-height: 25px;">
	<?php if (!$scope->isDefault()): ?>
		<a href="javascript:void(0);" onclick="$('delete_scope_<?php echo $scope->getID(); ?>').toggle();" class="button button-icon button-silver" style="float: right;"><?php echo image_tag('icon_delete.png', array('title' => __('Delete this scope'))); ?></a>
		<?php echo link_tag(make_url('configure_scope', array('id' => $scope->getID())), image_tag('icon_edit.png', array('title' => __('Edit scope settings'))), array('class' => 'button button-silver button-icon', 'style' => 'float: right; margin-right: 3px;')); ?></a>
	<?php endif; ?>
	<div class="header">
		<?php echo $scope->getName(); ?>
		<span style="font-weight: normal;" class="faded_out">
			<?php if (!$scope->isDefault()): ?>
				(<?php echo join(', ', $scope->getHostnames()); ?>)
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
	<?php if (!$scope->isDefault()): ?>
		<div class="rounded_box white shadowed" id="delete_scope_<?php echo $scope->getID(); ?>" style="display: none;">
			<div class="header"><?php echo __('Do you really want to delete this scope?'); ?></div>
			<div class="content">
				<?php echo __('Deleting this scope will destroy all data that exists inside this scope.'); ?> <i><?php echo __('This action cannot be undone.'); ?></i>
				<div style="text-align: right; margin: 10px 0;">
					<form action="<?php echo make_url('configure_scope', array('id' => $scope->getID())); ?>" method="post" style="display: inline;">
						<input type="submit" value="<?php echo __('Yes, delete it'); ?>">
						<input type="hidden" name="scope_action" value="delete">
					</form>
					<?php echo __('%yes_delete_it% or %no_dont_delete_it%', array('%yes_delete_it%' => '', '%no_dont_delete_it%' => '<b>'.javascript_link_tag(__("no, don't delete it"), array('onclick' => "$('delete_scope_{$scope->getID()}').toggle();")).'</b>')); ?>
				</div>
			</div>
		</div>
	<?php endif; ?>
</div>