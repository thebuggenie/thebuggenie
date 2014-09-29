<div style="margin: 10px auto; width: 990px;">
    <h2><?php echo __('You are currently not registered in this scope'); ?></h2>
    <p>
        <?php echo __('Do you want to register your username in this scope? This will link your main user account to this scope and give you access to the scope just like a regular user.'); ?><br>
        <br>
        <b><?php echo __('NOTE: Registering in this scope will give administrators in this scope access to your user details such as username, email, name, etc.'); ?></b><br>
        <?php echo __('If this is not what you want, select "No" below. Keep in mind that your main account can never be disabled or unregistered by administrators in this scope.'); ?><br>
    </p>
    <div style="margin-top: 25px;">
        <form action="<?php echo make_url('add_scope'); ?>" method="post">
            <input type="submit" style="font-size: 1.1em !important; padding: 2px 10px !important;" class="button button-green" value="<?php echo __('Yes, link my account'); ?>">&nbsp;&nbsp;
            <a style="font-size: 1.1em !important; padding: 4px 10px !important;" class="button button-silver" href="<?php echo make_url('logout'); ?>"><?php echo __("No, that's not what I want"); ?></a>
        </form>
        <p class="faded_out"><?php echo __('You can cancel this membership at any time from your account page'); ?></p>
    </div>
</div>
