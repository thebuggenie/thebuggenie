<?php
	$web_path = TBGContext::getModule('vcs_integration')->getSetting('web_path_' . $projectId);
	$web_repo = TBGContext::getModule('vcs_integration')->getSetting('web_repo_' . $projectId);
	
	switch (TBGContext::getModule('vcs_integration')->getSetting('web_type_' . $projectId))
	{
		case 'viewvc':
			$link_rev = $web_path . '/' . '?root=' . $web_repo . '&amp;view=rev&amp;revision=' . $commit->getRevision(); 
			$link_old = $web_path . '/' . '?root=' . $web_repo . '&amp;view=rev&amp;revision=' . $commit->getPreviousRevision(); 
			break;
		case 'viewvc_repo':
			$link_rev = $web_path . '/' . '?view=rev&amp;revision=' . $commit->getRevision();
			$link_old = $web_path . '/' . '?view=rev&amp;revision=' . $commit->getPreviousRevision(); 
			break;
		case 'websvn':
			$link_rev = $web_path . '/revision.php?repname=' . $web_repo . '&amp;isdir=1&amp;rev=' . $commit->getRevision();
			$link_old = $web_path . '/revision.php?repname=' . $web_repo . '&amp;isdir=1&amp;rev=' . $commit->getPreviousRevision(); 
			break;
		case 'websvn_mv':
			$link_rev = $web_path . '/' . '?repname=' . $web_repo . '&amp;op=log&isdir=1&amp;rev=' . $commit->getRevision();
			$link_old = $web_path . '/' . '?repname=' . $web_repo . '&amp;op=log&isdir=1&amp;rev=' . $commit->getPreviousRevision(); 
			break;
		case 'loggerhead':
			$link_rev = $web_path . '/' . $web_repo . '/revision/' . $commit->getRevision();
			$link_old = $web_path . '/' . $web_repo . '/revision/' . $commit->getPreviousRevision(); 
			break;
		case 'gitweb':
			$link_rev = $web_path . '/' . '?p=' . $web_repo . ';a=commitdiff;h=' . $commit->getRevision();
			$link_old = $web_path . '/' . '?p=' . $web_repo . ';a=commitdiff;h=' . $commit->getPreviousRevision(); 
			break;
		case 'cgit':
			$link_rev = $web_path . '/' . $web_repo . '/commit/?id=' . $commit->getRevision();
			$link_old = $web_path . '/' . $web_repo . '/commit/?id=' . $commit->getPreviousRevision(); 
			break;
		case 'hgweb':
			$revision = explode(':', $commit->getRevision());
			$previousRevision = explode(':', $commit->getPreviousRevision());
			$link_rev = $web_path . '/' . $web_repo . '/rev/' . $revision[1];
			$link_old = $web_path . '/' . $web_repo . '/rev/' . $previousRevision[1]; 
			break;
		case 'github':
			$link_rev = 'http://github.com/' . $web_repo . '/commit/' . $commit->getRevision();
			$link_old = 'http://github.com/' . $web_repo . '/commit/' . $commit->getPreviousRevision(); 
			break;
		case 'gitorious':
			$link_rev = $web_path . '/' . $web_repo . '/commit/' . $commit->getRevision();
			$link_old = $web_path . '/' . $web_repo . '/commit/' . $commit->getPreviousRevision();
			break;
	}

?>
<div class="comment" id="commit_<?php echo $commit->getID(); ?>">
	<div style="position: relative; overflow: visible; padding: 5px;" id="commit_view_<?php echo $commit->getID(); ?>" class="comment_main">
		<div id="commit_<?php echo $commit->getID(); ?>_header" class="commentheader">
			<a href="<?php echo $link_rev; ?>" class="comment_hash"><?php if (!is_numeric($commit->getRevision())): echo substr($commit->getRevision(), 0, 7); else: echo $commit->getRevision(); endif; ?></a>
			<div class="commenttitle">
				<?php echo __('Revision %rev% by %user%', array('%rev%' => "<a href=".$link_rev.">".$commit->getRevision()."</a>", '%user%' => '<div style="display: inline;">'.get_component_html('main/userdropdown', array('user' => $commit->getAuthor(), 'size' => 'small')).'</div>')); ?>
			</div>
			<div class="commentdate" id="commit_<?php echo $commit->getID(); ?>_date"><?php echo tbg_formattime($commit->getDate(), 12); ?> - <?php echo __('Preceeded by %prev%', array('%prev%' => "<a href=".$link_old.">".$commit->getPreviousRevision()."</a>"))?></div>
		</div>
		
		<div class="commentbody article commit_main" id="commit_<?php echo $commit->getID(); ?>_body">
			<div class="commit_expander">
				<a href="javascript:void(0);" style="padding-right: 5px;" id="checkin_expand_<?php echo $commit->getID(); ?>" onclick="$('checkin_details_<?php echo $commit->getID(); ?>').show(); $('checkin_expand_<?php echo $commit->getID(); ?>').hide(); $('checkin_collapse_<?php echo $commit->getID(); ?>').show();"><?php echo image_tag('expand.png'); ?> <?php echo __("Show more details"); ?></a>
				<a href="javascript:void(0);" style="display: none; padding-right: 5px;" id="checkin_collapse_<?php echo $commit->getID(); ?>" onclick="$('checkin_details_<?php echo $commit->getID(); ?>').hide(); $('checkin_expand_<?php echo $commit->getID(); ?>').show(); $('checkin_collapse_<?php echo $commit->getID(); ?>').hide();"><?php echo image_tag('collapse.png'); ?> <?php echo __("Hide details"); ?></a>
			</div>
			
			<div class="commit_header"><?php echo __('Log entry'); ?></div>
			<pre><?php echo $commit->getLog(); ?></pre>
			<div id="checkin_details_<?php echo $commit->getID(); ?>" style="display: none;" >
				<div class="commit_left">
					<div class="commit_header"><?php echo __('Changed files'); ?></div>
					<table border=0 cellpadding=0 cellspacing=0 style="width: 100%;">
					<?php
					
					if (count($commit->getFiles()) == 0)
					{
						echo '<span class="faded_out">'.__('No files have been affected by this commit').'</span>';
					}
					else
					{
						foreach ($commit->getFiles() as $file)
						{
							echo '<tr>';
							$action = $file->getAction();
							if ($action == 'M'): $action = 'U'; endif;
							echo '<td class="imgtd">' . image_tag('icon_action_' . $action . '.png', null, false, 'vcs_integration') . '</td>';
							switch (TBGContext::getModule('vcs_integration')->getSetting('web_type_' . $projectId))
							{
								case 'viewvc':
									$link_file = $web_path . '/' . $file->getFile() . '?root=' . $web_repo . '&amp;view=log';
									$link_diff = $web_path . '/' . $file->getFile() . '?root=' . $web_repo . '&amp;r1=' . $commit->getRevision() . '&amp;r2=' . $commit->getPreviousRevision();
									$link_view = $web_path . '/' . $file->getFile() . '?root=' . $web_repo . '&amp;revision=' . $commit->getRevision() . '&amp;view=markup';
									break;
								case 'viewvc_repo':
									$link_file = $web_path . '/' . $file->getFile() . '?view=log';
									$link_diff = $web_path . '/' . $file->getFile() . '?r1=' . $commit->getRevision() . '&amp;r2=' . $commit->getPreviousRevision();
									$link_view = $web_path . '/' . $file->getFile() . '?revision=' . $commit->getRevision() . '&amp;view=markup';
									break;
								case 'websvn':
									$link_file = $web_path . '/log.php?repname=' . $web_repo . '&amp;path=/' . $file->getFile();
									$link_diff = $web_path . '/comp.php?repname=' . $web_repo . '&amp;compare[]=/' . $file->getFile() . '@' . $commit->getRevision() . '&amp;compare[]=/' . $file->getFile() . '@' . $commit->getPreviousRevision();
									$link_view = $web_path . '/filedetails.php?repname=' . $web_repo . '&path=/' . $file->getFile() . '&amp;rev=' . $commit->getRevision();
									break;
								case 'websvn_mv':
									$link_file = $web_path . '/' . $file->getFile() . '?repname=' . $web_repo;
									$link_diff = $web_path . '/' . $file->getFile() . '?repname=' . $web_repo . '&amp;compare[]=/' . $file->getFile() . '@' . $commit->getRevision() . '&amp;compare[]=/' . $file->getFile() . '@' . $commit->getPreviousRevision();
									$link_view = $web_path . '/' . $file->getFile() . '?repname=' . $web_repo . '&amp;rev=' . $commit->getRevision();
									break;
								case 'loggerhead':
									$link_file = $web_path . '/' . $web_repo . '/changes';
									$link_diff = $web_path . '/' . $web_repo . '/revision/' . $commit->getRevision() . '?compare_revid=' . $commit->getPreviousRevision();
									$link_view = $web_path . '/' . $web_repo . '/annotate/head:/' . $file->getFile();
									break;
								case 'gitweb':
									$link_file = $web_path . '/' . '?p=' . $web_repo . ';a=history;f=' . $file->getFile() . ';hb=HEAD';
									$link_diff = $web_path . '/' . '?p=' . $web_repo . ';a=blobdiff;f=' . $file->getFile() . ';hb=' . $commit->getRevision() . ';hpb=' . $commit->getPreviousRevision();
									$link_view = $web_path . '/' . '?p=' . $web_repo . ';a=blob;f=' . $file->getFile() . ';hb=' . $commit->getRevision();
									break;
								case 'cgit':
									$link_file = $web_path . '/' . $web_repo . '/log';
									$link_diff = $web_path . '/' . $web_repo . '/diff/' . $file->getFile() . '?id=' . $commit->getRevision() . '?id2=' . $commit->getPreviousRevision();
									$link_view = $web_path . '/' . $web_repo . '/tree/' . $file->getFile() . '?id=' . $commit->getRevision();
									break;
								case 'hgweb':
									$rev = explode(':', $commit->getRevision());
									$link_file = $web_path . '/' . $web_repo . '/log/tip/' . $file->getFile();
									$link_diff = $web_path . '/' . $web_repo . '/diff/' . $revision[1] . '/' . $file->getFile();
									$link_view = $web_path . '/' . $web_repo . '/file/' .$revision[1] . '/' . $file->getFile();
									break;
								case 'github':
									$link_file = 'http://github.com/' . $web_repo . '/commits/master/' . $file->getFile();
									$link_diff = 'http://github.com/' . $web_repo . '/commit/' . $commit->getRevision();
									$link_view = 'http://github.com/' . $web_repo . '/blob/' .$commit->getRevision() . '/' . $file->getFile();
									break;
								case 'gitorious':
									$link_file = $web_path . '/' . $web_repo . '/blobs/history/master/' . $file->getFile();
									$link_diff = $web_path . '/' . $web_repo . '/commit/' . $commit->getRevision();
									$link_view = $web_path . '/' . $web_repo . '/blobs/' .$commit->getRevision() . '/' . $file->getFile();
									break;
							}
					
							echo '<td><a href="' . $link_file . '" target="_new"><b>' . $file->getFile() . '</b></a></td>';
							if ($action == "U" || $action == "M")
							{
								if (substr($file->getFile(), -1) == '/' || substr($file->getFile(), -1) == '\\')
								{
									echo '<td style="width: 75px;" class="faded_out">' . __('directory') . '</td>';
								}
								else
								{
									echo '<td style="width: 75px;"><a href="' . $link_diff . '" target="_new"><b>' . __('Diff') . '</b></a></td>';
								}
							}
							
							if ($action == "D")
							{
								echo '<td colspan="2" class="faded_out" style="width: 150px;">'.__('deleted').'</td>';
							}
							
							if ($action == "A")
							{
								echo '<td class="faded_out" style="width: 75px;">'.__('new file').'</td>';
							}
							
							if($action != "D")
							{
								echo '<td style="width: 75px;"><a href="' . $link_view . '" target="_new"><b>' . __('View') . '</b></a></td>';
							}
							echo '</tr>';
						}
					}
					?>
					</table>
				</div>
				<div class="commit_right">
					<div class="commit_header"><?php echo __('Affected issues'); ?></div>
					<?php
						$valid_issues = array();
						
						foreach ($commit->getIssues() as $issue)
						{
							if ($issue instanceof TBGIssue)
							{
								if (!TBGContext::getCurrentProject() instanceof TBGProject || $issue->getProjectID() != TBGContext::getCurrentProject()->getID())
								{
									$issue = null;
								}
							}
							
							if ($issue != null)
							{
								$valid_issues[] = $issue;
							}
						}
						if (count($valid_issues) == '')
						{
							echo '<span class="faded_out">'.__('This commit affects no issues').'</span>';
						}
						else
						{
							echo '<ul>';
							foreach ($valid_issues as $issue)
							{
								echo '<li>'.link_tag(make_url('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())), $issue->getFormattedIssueNo(true, true)).'</li>';
							}
							echo '</ul>';
						}
					?>
				</div>
			</div>
		</div>
	</div>
</div>