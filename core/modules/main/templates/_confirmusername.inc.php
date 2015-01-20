<div class="backdrop_box large" id="username_confirmation_popup">
    <div class="backdrop_detail_header"><?php echo __('Confirm username'); ?></div>
    <div id="backdrop_detail_content" class="backdrop_detail_content">
        <h5><?php echo __('The selected username is available'); ?></h5>
        <?php echo __('You cannot change the username after you have picked it. Please confirm that you want to use the following username: %username', array('%username' => '<h3>'.$username.'</h3>')); ?>
        <div style="text-align: center;">
            <form accept-charset="<?php echo \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>" action="<?php echo make_url('account_pick_username', array('selected_username' => $username)); ?>" method="post" id="pick_username_form">
                <input type="submit" value="<?php echo __('Yes'); ?>" class="button button-green">&nbsp;<?php echo __('%yes or %no', array('%yes' => '', '%no' => '')); ?>&nbsp;<button onclick="TBG.Main.Helpers.Backdrop.reset(); return false;" class="button button-silver"><?php echo __('No'); ?></button>
            </form>
        </div>
    </div>
    <div class="backdrop_detail_footer">
        <a href="javascript:void(0);" onclick="TBG.Main.Helpers.Backdrop.reset();"><?php echo __('Close'); ?></a>
    </div>
</div>
