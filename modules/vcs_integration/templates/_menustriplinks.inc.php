<?php
	/*
	 * Generate link for browser
	 */
	 
	$link_repo = TBGContext::getModule('vcs_integration')->getSetting('browser_url_' . TBGContext::getCurrentProject()->getID());
	
	if (TBGContext::getModule('vcs_integration')->getSetting('vcs_mode_' . TBGContext::getCurrentProject()->getID()) != TBGVCSIntegration::MODE_DISABLED)
	{
			echo link_tag(make_url('vcs_commitspage', array('project_key' => TBGContext::getCurrentProject()->getKey())), __('Commits'), (($tbg_response->getPage() == 'vcs_commitspage') ? array('class' => 'selected') : array()));
			if (!($submenu) && $tbg_response->getPage() == 'vcs_commitspage'): ?>
			<ul class="simple_list">
				<li><a href="<?php echo $link_repo; ?>" target="_blank"><?php echo __('Browse source code'); ?></a></li>
			</ul>
		<?php endif;
	}

?>