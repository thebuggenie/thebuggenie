<?php

    $tbg_response->setTitle(__('Configure authentication'));
    
?>
<center>
<h1><?php echo __('Settings saved'); ?></h1>
<?php echo __('To apply changes to the authentication system, you have been automatically logged out. The new authentication system is now in use.'); ?>
<p><?php echo link_tag(make_url('home'), __('Continue')); ?></p>
</center>

<?php \thebuggenie\core\framework\Context::logout(); ?>
