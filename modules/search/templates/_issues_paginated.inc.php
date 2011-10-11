<?php include_component("search/{$templatename}", array('issues' => $issues, 'template_parameter' => $template_parameter, 'groupby' => $groupby, 'cc' => 1, 'prevgroup_id' => null, 'show_project' => true)); ?>
<?php if ($ipp > 0 && $resultcount > $ipp): ?>
	<?php include_component('search/pagination', array('searchterm' => $searchterm, 'filters' => $filters, 'templatename' => $templatename, 'grouporder' => $grouporder, 'groupby' => $groupby, 'resultcount' => $resultcount, 'ipp' => $ipp, 'offset' => $offset)); ?>
<?php endif; ?>