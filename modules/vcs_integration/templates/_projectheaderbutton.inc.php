<?php
	/*
	 * Generate link for browser
	 */
	 
	$link_repo = TBGContext::getModule('vcs_integration')->getSetting('browser_url_' . TBGContext::getCurrentProject()->getID());
	
	if (TBGContext::getModule('vcs_integration')->getSetting('vcs_mode_' . TBGContext::getCurrentProject()->getID()) != TBGVCSIntegration::MODE_DISABLED)
	{
		echo '<div class="button button-blue"><span>'.image_tag('cfg_icon_vcs_integration.png', null, false, 'vcs_integration').'<a href="'.$link_repo.'" target="_blank">'.__('Source code').'</a></span></div>';
	}

?>