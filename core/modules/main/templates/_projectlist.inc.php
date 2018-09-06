<?php

use thebuggenie\core\framework;

?>
<div class="project_overview">
    <div class="tab_menu inset">
        <ul id="projects_list_tabs">
            <li id="tab_active" class="selected"><a onclick="TBG.Project.loadList('active', '<?= $active_url; ?>');" href="javascript:void(0);"><?= fa_image_tag('diamond') . __('Active projects') . image_tag('spinning_16.gif', ['style' => 'display: none;', 'id' => 'project_list_tab_active_indicator']); ?></a></li>
            <li id="tab_archived" class=""><a onclick="TBG.Project.loadList('archived', '<?= $archived_url; ?>');" href="javascript:void(0);"><?= fa_image_tag('archive') . __('Archived projects') . image_tag('spinning_16.gif', ['style' => 'display: none;', 'id' => 'project_list_tab_archived_indicator']); ?></a></li>
            <?php if ($tbg_user->isAuthenticated()): ?>
                <li class="right">
                    <?= link_tag(make_url('configure_projects'), fa_image_tag('cog'), ['class' => 'button-icon']); ?>
                    <?php if ($tbg_user->canAccessConfigurationPage(framework\Settings::CONFIGURATION_SECTION_PROJECTS) && framework\Context::getScope()->hasProjectsAvailable()): ?>
                        <button class="button button-silver project-quick-edit" onclick="TBG.Main.Helpers.Backdrop.show('<?= make_url('get_partial_for_backdrop', ['key' => 'project_config']); ?>');"><?= __('Create project'); ?></button>
                    <?php endif; ?>
                </li>
            <?php endif; ?>
        </ul>
    </div>
    <div id="projects_list_tabs_panes">
        <div id="tab_active_pane" style=""></div>
        <div id="tab_archived_pane" style="display: none;"></div>
    </div>
</div>
<script type="text/javascript">
 require(['domReady', 'thebuggenie/tbg', 'prototype'], function (domReady, TBG, prototype) {
     domReady(function () {
         // Default to active tab, unless archived tab was specified
         // in URL.
         if (window.location.hash === '#tab_archived') {
             TBG.Project.loadList('archived', '<?= $archived_url; ?>');
         }
         else {
             TBG.Project.loadList('active', '<?= $active_url; ?>');
         }
     });
 });
</script>
