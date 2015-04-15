<?php echo __("Welcome to The Bug Genie. This seems like the first time you're using this instance, and it doesn't look like you have had the chance to add any projects yet."); ?><br>
<br>
<?php echo __("If you want to play around a bit with The Bug Genie before you start using it for your own projects, you can generate sample data before adding your own projects. To get started, create a project."); ?>
<div class="close_me">
    <a href="<?php echo make_url('import_home'); ?>" class="button button-silver"><?php echo __('Generate sample data'); ?></a>
    <?php echo __('%generate_sample_data% or %create_a_project%', array('%generate_sample_data%' => '', '%create_a_project%' => '')); ?>
    <a href="<?php echo make_url('configure_projects'); ?>" class="button button-silver"><?php echo __('Create a project'); ?></a>
</div>
