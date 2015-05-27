<div class="almost_not_transparent shadowed popup_message failure" onclick="TBG.Main.Helpers.Message.clear();" style="display: none;" id="thebuggenie_failuremessage">
    <div style="padding: 10px 0 10px 0;">
        <div class="dismiss_me"><?php echo __('Click this message to dismiss it'); ?></div>
        <span class="messagetitle" id="thebuggenie_failuremessage_title"></span>
        <span id="thebuggenie_failuremessage_content"></span>
    </div>
</div>
<div class="tutorial-message" id="tutorial-message" style="display: none;" data-disable-url="<?php echo make_url('disable_tutorial'); ?>">
    <div id="tutorial-message-container"></div>
    <br>
    <div class="tutorial-buttons">
        <button class="button button-standard button-next" id="tutorial-next-button"></button>
        <a class="button-disable" id="disable-tutorial-button" href="javascript:void(0);"><?php echo __('Skip this tutorial'); ?></a>
    </div>
    <br style="clear: both;">
    <div class="tutorial-status"><span id="tutorial-current-step"></span> of <span id="tutorial-total-steps"></span></div>
</div>
<div class="almost_not_transparent shadowed popup_message success" onclick="TBG.Main.Helpers.Message.clear();" style="display: none;" id="thebuggenie_successmessage">
    <div style="padding: 10px 0 10px 0;">
        <div class="dismiss_me"><?php echo __('Click this message to dismiss it'); ?></div>
        <span class="messagetitle" id="thebuggenie_successmessage_title"></span>
        <span id="thebuggenie_successmessage_content"></span>
    </div>
</div>
<div id="fullpage_backdrop" class="fullpage_backdrop" style="display: none;">
    <div id="fullpage_backdrop_indicator">
        <?php echo image_tag('spinning_32.gif'); ?><br>
        <?php echo __('Please wait ...'); ?>
    </div>
    <div id="fullpage_backdrop_content" class="fullpage_backdrop_content"> </div>
</div>
<?php if (\thebuggenie\core\framework\Context::getRouting()->getCurrentRouteName() != 'login_page' && $tbg_user->isGuest()): ?>
    <div id="login_backdrop" class="fullpage_backdrop" style="display: none;">
        <div id="login_content" class="fullpage_backdrop_content">
            <?php include_component('main/loginpopup', array('content' => get_component_html('main/login'), 'mandatory' => false)); ?>
        </div>
    </div>
<?php endif; ?>
<?php if (\thebuggenie\core\framework\Settings::isPersonaAvailable() && ($tbg_user->isGuest() || $tbg_request->hasCookie('tbg3_persona_session'))): ?>
    <script src="https://login.persona.org/include.js"></script>
    <script type="text/javascript">
        require(['domReady', 'thebuggenie/tbg', 'jquery'], function (domReady, tbgjs, jquery) {
            domReady(function () {
                TBG = tbgjs;
                var currentUser = <?php echo (!$tbg_user->isGuest()) ? "'{$tbg_user->getEmail()}'" : 'null'; ?>;

                navigator.id.watch({
                  loggedInUser: currentUser,
                  onlogin: function(assertion) {
                    // A user has logged in! Here you need to:
                    // 1. Send the assertion to your backend for verification and to create a session.
                    // 2. Update your UI.
                    TBG.Main.Helpers.ajax('<?php echo make_url('login'); ?>', {
                        url_method: 'post',
                        additional_params: '&persona=true&assertion='+assertion+'&referrer_route=<?php echo \thebuggenie\core\framework\Context::getRouting()->getCurrentRouteName(); ?>',
                        loading: {
                            indicator: 'fullpage_backdrop',
                            clear: 'fullpage_backdrop_content',
                            hide: 'login_backdrop',
                            show: 'fullpage_backdrop_indicator'
                        },
                        success: {
                            callback: function(json) {
                                window.location.reload();
                            }
                        },
                        failure: {
                            callback: function(json) {
                                navigator.id.logout();
                            }
                        }
                    });
                  },
                  onlogout: function() {
                    // A user has logged out! Here you need to:
                    // Tear down the user's session by redirecting the user or making a call to your backend.
                    // Also, make sure loggedInUser will get set to null on the next page load.
                    // (That's a literal JavaScript null. Not false, 0, or undefined. null.)
                    TBG.Main.Helpers.ajax('<?php echo make_url('logout'); ?>', {
                        url_method: 'post',
                        loading: {
                            indicator: 'fullpage_backdrop',
                            clear: 'fullpage_backdrop_content',
                            show: 'fullpage_backdrop_indicator'
                        },
                        success: {
                            callback: function(json) {
                                window.location = json.url;
                            }
                        }
                    });
                  }
                });
                if ($('persona-signin-button')) $('persona-signin-button').observe('click', function() { navigator.id.request(); } );
            });
        });
    </script>
<?php endif; ?>
<div class="fullpage_backdrop" id="dialog_backdrop" style="display: none;">
    <div id="dialog_backdrop_content" class="backdrop_box backdrop_detail_content">
        <h3 id="dialog_title"></h3>
        <p id="dialog_content"></p>
        <div style="text-align: right; padding: 20px;">
            <?php echo image_tag('spinning_20.gif', array('style' => 'display: none;', 'id' => 'dialog_indicator')); ?>
            <a href="javascript:void(0)" id="dialog_yes" class="button button-silver"><?php echo __('Yes'); ?></a>
            <a href="javascript:void(0)" id="dialog_no" class="button button-silver"><?php echo __('No'); ?></a>
        </div>
    </div>
</div>
<div class="fullpage_backdrop" id="dialog_backdrop_modal" style="display: none;">
    <div id="dialog_backdrop_modal_content" class="backdrop_box backdrop_detail_content">
        <h3 id="dialog_modal_title"></h3>
        <p id="dialog_modal_content"></p>
        <div style="text-align: right; padding: 20px;">
            <a href="javascript:void(0)" id="dialog_okay" onclick="TBG.Main.Helpers.Dialog.dismissModal();" class="button button-silver"><?php echo __('Okay'); ?></a>
        </div>
    </div>
</div>
<input type="file" id="file_upload_dummy" style="display: none;" multiple onchange="TBG.Main.selectFiles(this);" data-upload-url="<?php echo make_url('upload_file'); ?>">