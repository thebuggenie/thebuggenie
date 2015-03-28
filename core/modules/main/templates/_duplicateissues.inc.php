<ul class="simple_list related_issues_list" id="related_duplicate_issues_inline">
    <?php foreach ($duplicate_issues as $duplicate_issue): ?>
        <?php include_component('main/relatedissue', array('issue' => $duplicate_issue, 'related_issue' => $issue)); ?>
    <?php endforeach; ?>
</ul>
<div class="no_items" id="no_related_issues"<?php if (count($duplicate_issues) > 0): ?> style="display: none;"<?php endif; ?>><?php echo __('This issue does not have any duplicates'); ?></div>
