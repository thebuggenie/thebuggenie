<div class="<?php if ($scope->isDefault()): ?> bluebox<?php else: ?> greybox<?php endif; ?>" style="margin-bottom: 5px; min-height: 25px;">
    <?php if (!$scope->isDefault()): ?>
        <div class="button-group" style="float: right;">
            <?php echo link_tag(make_url('configure_scope', array('id' => $scope->getID())), image_tag('icon_edit.png', array('title' => __('Edit scope settings'))), array('class' => 'button button-silver button-icon')); ?></a>
            <a href="javascript:void(0);" onclick="TBG.Main.Helpers.Dialog.show('<?php echo __('Do you really want to delete this scope?'); ?>', '<?php echo __('Deleting this scope will destroy all data that exists inside this scope.'); ?> <i><?php echo __('This action cannot be undone.'); ?></i>', {yes: {click: function() {$('delete_scope_<?php echo $scope->getID(); ?>_form').submit();}}, no: {click: TBG.Main.Helpers.Dialog.dismiss}});" class="button button-icon button-silver"><?php echo image_tag('icon_delete.png', array('title' => __('Delete this scope'))); ?></a>
        </div>
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
        <?php if (!$scope->isDefault()): ?>
            <?php echo __('%num_projects project(s), %num_issues issue(s)', array('%num_projects' => $scope->getNumberOfProjects(), '%num_issues' => $scope->getNumberOfIssues())); ?>
        <?php endif; ?>
        <?php if ($scope->isDefault() || strlen(trim($scope->getDescription()))): ?>
            <div class="faded_out scope_description">
                <?php if (!$scope->isDefault()): ?>
                    <?php echo $scope->getDescription(); ?>
                <?php else: ?>
                    <?php echo __('This is the default scope. It will be loaded and used whenever The Bug Genie is accessed on a hostname for which there is not explicit scope defined.'); ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
    <?php if (!$scope->isDefault()): ?>
        <form action="<?php echo make_url('configure_scope', array('id' => $scope->getID())); ?>" method="post" style="display: none;" id="delete_scope_<?php echo $scope->getID(); ?>_form">
            <input type="hidden" name="scope_action" value="delete">
        </form>
    <?php endif; ?>
</div>
