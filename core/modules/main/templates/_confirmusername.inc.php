<div class="backdrop_box large" id="username_confirmation_popup">
    <div class="backdrop_detail_header">
        <span><?php echo __('Confirm username'); ?></span>
        <a href="javascript:void(0);" class="closer" onclick="TBG.Main.Helpers.Backdrop.reset();"><?= fa_image_tag('times'); ?></a>
    </div>
    <div id="backdrop_detail_content" class="backdrop_detail_content">
        <h5><?php echo __('The selected username is available'); ?></h5>
        <?php echo __('You cannot change the username after you have picked it. Please confirm that you want to use the following username: %username', array('%username' => '<h3>'.$username.'</h3>')); ?>
    </div>
    <form accept-charset="<?php echo \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>" action="<?php echo make_url('account_pick_username', array('selected_username' => $username)); ?>" method="post" id="pick_username_form">
        <div class="backdrop_details_submit">
            <span class="explanation"></span>
            <div class="submit_container">
                <input type="submit" value="<?php echo __('Yes'); ?>" class="button button-silver">
            </div>
        </div>
    </form>
</div>
