<div id="manual_sidebar" class="<?php if (\thebuggenie\core\framework\Context::isProjectContext()) echo ' single_parent'; ?>">
    <ul>
        <?php $level = 0; ?>
        <?php $first = true; ?>
        <?php include_component('publish/manualsidebarlink', compact('parents', 'article', 'main_article', 'level', 'first')); ?>
    </ul>
</div>
