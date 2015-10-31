<?php

    \thebuggenie\core\framework\Context::loadLibrary('ui');

?>
<div id="login_backdrop">
    <div class="backdrop_box login_page login_popup" id="login_popup">
        <div id="backdrop_detail_content" class="backdrop_detail_content rounded_top login_content">
            <?php include_component('main/login', compact('section', 'error')); ?>
        </div>
    </div>
</div>
<script type="text/javascript">
    require(['domReady', 'thebuggenie/tbg', 'jquery'], function (domReady, TBG, jquery) {
        domReady(function () {
        <?php if (\thebuggenie\core\framework\Context::hasMessage('login_message')): ?>
            TBG.Main.Helpers.Message.success('<?php echo \thebuggenie\core\framework\Context::getMessageAndClear('login_message'); ?>');
        <?php elseif (\thebuggenie\core\framework\Context::hasMessage('login_message_err')): ?>
            TBG.Main.Helpers.Message.error('<?php echo \thebuggenie\core\framework\Context::getMessageAndClear('login_message_err'); ?>');
        <?php endif; ?>
            jquery('#tbg3_username').focus();
        });
    });
</script>