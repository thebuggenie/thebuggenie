<footer>
    <?php echo image_tag('footer_logo.png'); ?>
    <?php echo __('%thebuggenie, <b>friendly</b> issue tracking since 2002', array('%thebuggenie' => link_tag(make_url('about'), 'The Bug Genie'))); ?><br>
        <a href="http://www.opensource.org/licenses/mozilla1.1.php"><?php echo __('Read the license (MPL 1.1 only)'); ?></a>
    <?php if ($tbg_user->canAccessConfigurationPage()): ?>
        | <b><?php echo link_tag(make_url('configure'), __('Configure %thebuggenie_name', array('%thebuggenie_name' => TBGSettings::getTBGname()))); ?></b>
    <?php endif; ?>
    <?php if (TBGContext::isDebugMode() && TBGLogging::isEnabled()): ?>
        <div id="tbg___DEBUGINFO___" style="position: fixed; bottom: 0; left: 0; z-index: 100; display: none; width: 100%;">
        </div>
        <?php echo image_tag('spinning_16.gif', array('style' => 'position: fixed; bottom: 5px; right: 23px;', 'id' => 'tbg___DEBUGINFO___indicator')); ?>
        <?php echo image_tag('debug_show.png', array('style' => 'position: fixed; bottom: 5px; right: 3px; cursor: pointer;', 'onclick' => "$('tbg___DEBUGINFO___').toggle();", 'title' => 'Show debug bar')); ?>
    <?php endif; ?>
</footer>
