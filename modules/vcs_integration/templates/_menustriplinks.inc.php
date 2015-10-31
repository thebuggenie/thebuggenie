<?php
    /*
     * Generate link for browser
     */
     
    $link_repo = \thebuggenie\core\framework\Context::getModule('vcs_integration')->getSetting('browser_url_' . \thebuggenie\core\framework\Context::getCurrentProject()->getID());
    
    if (\thebuggenie\core\framework\Context::getModule('vcs_integration')->getSetting('vcs_mode_' . \thebuggenie\core\framework\Context::getCurrentProject()->getID()) != \thebuggenie\modules\vcs_integration\Vcs_integration::MODE_DISABLED)
    {
            echo link_tag(make_url('vcs_commitspage', array('project_key' => \thebuggenie\core\framework\Context::getCurrentProject()->getKey())), __('Commits'), (($tbg_response->getPage() == 'vcs_commitspage') ? array('class' => 'selected') : array()));
            if (!($submenu) && $tbg_response->getPage() == 'vcs_commitspage'): ?>
            <ul class="simple_list">
                <li><a href="<?php echo $link_repo; ?>" target="_blank"><?php echo __('Browse source code'); ?></a></li>
            </ul>
        <?php endif;
    }

?>
