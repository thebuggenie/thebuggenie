<?php
    /*
     * Generate link for browser
     */
     
    $link_repo = \thebuggenie\core\framework\Context::getModule('livelink')->getSetting('browser_url_' . \thebuggenie\core\framework\Context::getCurrentProject()->getID());
    
    if (\thebuggenie\core\framework\Context::getModule('livelink')->getSetting('vcs_mode_' . \thebuggenie\core\framework\Context::getCurrentProject()->getID()) != \thebuggenie\modules\livelink\Vcs_integration::MODE_DISABLED)
    {
        echo '<a href="'.$link_repo.'" target="_blank" class="button button-blue">'.image_tag('cfg_icon_livelink.png', array(), false, 'livelink').__('Source code').'</a>';
    }

?>
