<?php echo __("It looks likes you're only using The Bug Genie to track issues for one project."); ?><br>
<?php echo __("If you don't want to use this homepage, you can set The Bug Genie to %single_project_tracker_mode, which will automatically forward the frontpage to the project overview page.", array('%single_project_tracker_mode' => '<i>'.__('Single project tracker mode').'</i>')); ?><br>
<br>
<?php echo __("%single_project_tracker_mode can be enabled from %configure_settings.", array('%single_project_tracker_mode' => '<i>'.__('Single project tracker mode').'</i>', '%configure_settings' => link_tag(make_url('configure_settings'), '<b>' . __('Configure &rarr; Settings', array(), true) . '</b>'))); ?>
