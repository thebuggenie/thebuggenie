<?php include_component("search/".$search_object->getTemplateName(), array('search_object' => $search_object, 'cc' => 1, 'prevgroup_id' => null, 'show_project' => true)); ?>
<?php if ($search_object->hasPagination() && $search_object->needsPagination()): ?>
    <?php include_component('search/pagination', compact('search_object')); ?>
<?php endif; ?>
