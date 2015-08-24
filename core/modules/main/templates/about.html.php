<?php

    $tbg_response->setTitle(__('About %sitename', array('%sitename' => \thebuggenie\core\framework\Settings::getSiteHeaderName())));
    $tbg_response->addBreadcrumb(__('About %sitename', array('%sitename' => \thebuggenie\core\framework\Settings::getSiteHeaderName())), make_url('about'));

?>
<div class="rounded_box borderless mediumgrey" style="margin: 10px auto 0 auto; width: 500px; padding: 5px 5px 15px 5px; font-size: 13px; text-align: center;">
    <div style="text-align: left; padding: 10px;">
        <h1 style="font-size: 25px; margin-bottom: 0px; padding-bottom: 3px;">
            The Bug Genie
            <span style="font-size: 14px; font-weight: normal; color: #888;">
                <?php echo __('Version %thebuggenie_version', array('%thebuggenie_version' => \thebuggenie\core\framework\Settings::getVersion(true))); ?>
            </span>
        </h1>
        <h3 style="margin-top: 0; padding-top: 0;">Beautiful issue tracking and project management</h3>
        <?php echo __('The Bug Genie is an issue tracking system with a strong focus on being friendly &ndash; both for regular users and power users'); ?>.<br>
        <br>
        <?php echo __('The Bug Genie follows an open development model, and is released under an open source software license called the MPL (Mozilla Public License). This license gives you the freedom to pick up the sourcecode for The Bug Genie and work with it any way you need.'); ?><br>
        <br>
        <?php echo __('Extend, develop and change The Bug Genie in any way you want, and do whatever you want with the new piece of software (The only thing you cannot do is call your software The Bug Genie). Please do send us your modifications for inclusion in The Bug Genie.'); ?><br>
        <br>
        <b><?php echo __('Enjoy using The Bug Genie!'); ?></b>
    </div>
    <br>
    <a href="http://www.thebuggenie.com" target="_blank">The Bug Genie</a>, Copyright &copy; 2002 - <?php echo date('Y'); ?> <b>The Bug Genie team</b><br>
    <?php echo __('Licensed under the MPL 2.0, read it at %link_to_MPL', array('%link_to_MPL' => '<a href="http://opensource.org/licenses/MPL-2.0">opensource.org</a>')); ?>.<br>
    <br>
    <span class="faded_out">
        <?php echo __('The Bug Genie uses icons from the %link_to_iconset', array('%link_to_iconset' => '<a href="https://sourceforge.net/projects/openiconlibrary">Oxygen icon set</a>')); ?>.<br>
        <?php echo __('These icons may be freely distributed under the %link_to_license', array('%link_to_license' => '<a href="http://creativecommons.org/licenses/by-sa/3.0/">CC BY-SA 3.0 License</a>')); ?>.
    </span>
</div>
