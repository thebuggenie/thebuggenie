<?php
	/*
	 * Generate link for browser
	 */
	 
	$web_path = $module->getSetting('web_path_' . $project->getID());
	$web_repo = $module->getSetting('web_repo_' . $project->getID());
	switch ($module->getSetting('web_type_' . $project->getID()))
	{
		case 'viewvc':
			$link_repo = $web_path . '/' . '?root=' . $web_repo;
			break;
		case 'viewvc_repo':
			$link_repo = $web_path . '/'; 
			break;
		case 'websvn':
			$link_repo = $web_path . '/listing.php?repname=' . $web_repo; 
			break;
		case 'websvn_mv':
			$link_repo = $web_path . '/?repname=' . $web_repo; 
			break;
		case 'loggerhead':
			$link_repo = $web_path . '/' . $web_repo . '/changes'; 
			break;
		case 'gitweb':
			$link_repo = $web_path . '/' . '?p=' . $web_repo; 
			break;
		case 'cgit':
			$link_repo = $web_path . '/' . $web_repo; 
			break;
		case 'hgweb':
			$link_repo = $web_path . '/' . $web_repo; 
			break;
		case 'github':
			$link_repo = 'http://www.github.com/' . $web_repo; 
			break;
	}
	if ($web_path != '')
	{
		echo link_tag(make_url('project_commits', array('project_key' => TBGContext::getCurrentProject()->getKey())), __('VCS Commits'), (($tbg_response->getPage() == 'project_commits') ? array('class' => 'selected') : array()));
		if (!isset($submenu) && $tbg_response->getPage() == 'project_commits'): ?>
			<ul class="simple_list">
				<li><?php echo '<a href="'.$link_repo.'" target="_blank">'.__('View source code').'</a>'; ?></li>
			</ul>
		<?php
		endif;
	}
?>