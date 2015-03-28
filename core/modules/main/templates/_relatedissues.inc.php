<ul class="simple_list related_issues_list" id="related_child_issues_inline">
    <?php foreach ($child_issues as $child_issue): ?>
        <?php include_component('main/relatedissue', array('issue' => $child_issue, 'related_issue' => $issue)); ?>
    <?php endforeach; ?>
</ul>
<div class="no_items" id="no_related_issues"<?php if (count($child_issues) > 0): ?> style="display: none;"<?php endif; ?>><?php echo __('This issue does not have any child issues'); ?></div>
