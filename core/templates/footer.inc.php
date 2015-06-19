<footer>
    <?php echo image_tag('footer_logo.png'); ?>
    <?php echo __('%thebuggenie, <b>friendly</b> issue tracking since 2002', array('%thebuggenie' => link_tag(make_url('about'), 'The Bug Genie')), true); ?><br>
        <a href="http://opensource.org/licenses/MPL-2.0"><?php echo __('Read the license (MPL 2.0)'); ?></a>
    <?php if ($tbg_user->canAccessConfigurationPage()): ?>
        | <b><?php echo link_tag(make_url('configure'), __('Configure %thebuggenie_name', array('%thebuggenie_name' => \thebuggenie\core\framework\Settings::getSiteHeaderName()))); ?></b>
    <?php endif; ?>
    <?php if (\thebuggenie\core\framework\Context::isDebugMode() && \thebuggenie\core\framework\Logging::isEnabled()): ?>
        <script>
            function tbg_debug_show_menu_tab(tab, clicked) {
                $('debug-bar').childElements().each(function (unclicked) {
                    unclicked.removeClassName('selected');
                });
                clicked.addClassName('selected');
                $('debug-frames-container').childElements().each(function (container) {
                    (container.id == tab) ? container.addClassName('selected') : container.removeClassName('selected');
                });
            }
        </script>
        <div id="tbg___DEBUGINFO___" style="position: fixed; bottom: 0; left: 0; z-index: 100; display: none; width: 100%;">
        </div>
        <?php echo image_tag('spinning_16.gif', array('style' => 'position: fixed; bottom: 5px; right: 23px;', 'id' => 'tbg___DEBUGINFO___indicator')); ?>
    <?php endif; ?>
</footer>
