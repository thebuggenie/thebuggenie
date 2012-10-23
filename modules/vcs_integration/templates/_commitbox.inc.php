<?php
	$base_url = TBGContext::getModule('vcs_integration')->getSetting('browser_url_' . $projectId);
	
	if (mb_strstr($commit->getRevision(), ':'))
	{
		$revision = explode(':', $commit->getRevision());
		$revision = $revision[1];
	}
	else
	{
		$revision = $commit->getRevision();
	}
	
	if (mb_strstr($commit->getPreviousRevision(), ':'))
	{
		$oldrevision = explode(':', $commit->getPreviousRevision());
		$oldrevision = $oldrevision[1];
	}
	else
	{
		$oldrevision = $commit->getPreviousRevision();
	}
	
	$misc_data = explode('|', $commit->getMiscData());
	
	$branchname = null;
	
	foreach ($misc_data as $data)
	{
		if (mb_strstr($data, 'branch'))
		{
			$branch = explode(':', $data);
			if (count($branch) == 2)
			{
				$branchname = $branch[1];
			}
		}
	}
	
	$link_base = TBGContext::getModule('vcs_integration')->getSetting('commit_url_' . $projectId);
	
	if ($branchname !== null)
	{
		$link_base = str_replace('%branch%', $branchname, $link_base);
	}
	
	$link_rev = $base_url.str_replace('%revno%', $revision, $link_base);
	$link_old = $base_url.str_replace('%revno%', $oldrevision, $link_base);
	
	
?>
<div class="comment" id="commit_<?php echo $commit->getID(); ?>">
	<div style="position: relative; overflow: visible; padding: 5px;" id="commit_view_<?php echo $commit->getID(); ?>" class="comment_main">
		<div id="commit_<?php echo $commit->getID(); ?>_header" class="commentheader">
			<a href="<?php echo $link_rev; ?>" class="comment_hash"><?php if (!is_numeric($commit->getRevision())): echo mb_substr($commit->getRevision(), 0, 7); else: echo $commit->getRevision(); endif; ?></a>
			<div class="commenttitle">
				<?php if ($branchname !== null): ?><span class="commitbranch"><?php echo $branchname; ?></span> <?php endif; ?><?php echo __('Revision %rev% by %user%', array('%rev%' => "<a href=".$link_rev.">".$commit->getRevision()."</a>", '%user%' => '<div style="display: inline;">'.get_component_html('main/userdropdown', array('user' => $commit->getAuthor(), 'size' => 'small')).'</div>')); ?>
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
									
							$link_file = str_replace('%revno%', $revision, TBGContext::getModule('vcs_integration')->getSetting('log_url_' . $projectId));
							$link_file = str_replace('%oldrev%', $oldrevision, $link_file);
							
							if ($branchname !== null)
							{
								$link_file = str_replace('%branch%', $branchname, $link_file);
							}
							
							$link_file = $base_url.str_replace('%file%', $file->getFile(), $link_file);
							
							$link_diff = str_replace('%revno%', $revision, TBGContext::getModule('vcs_integration')->getSetting('diff_url_' . $projectId));
							$link_diff = str_replace('%oldrev%', $oldrevision, $link_diff);
							
							if ($branchname !== null)
							{
								$link_diff = str_replace('%branch%', $branchname, $link_diff);
							}
							
							$link_diff = $base_url.str_replace('%file%', $file->getFile(), $link_diff);
							
							$link_view = str_replace('%revno%', $revision, TBGContext::getModule('vcs_integration')->getSetting('blob_url_' . $projectId));
							$link_view = str_replace('%oldrev%', $oldrevision, $link_view);
							
							if ($branchname !== null)
							{
								$link_view = str_replace('%branch%', $branchname, $link_view);
							}
							
							$link_view = $base_url.str_replace('%file%', $file->getFile(), $link_view);
					
							echo '<td><a href="' . $link_file . '" target="_new"><b>' . $file->getFile() . '</b></a></td>';
							if ($action == "U" || $action == "M")
							{
								if (mb_substr($file->getFile(), -1) == '/' || mb_substr($file->getFile(), -1) == '\\')
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
							elseif ($action == "A")
							{
								echo '<td class="faded_out" style="width: 75px;">'.__('new file').'</td>';
							}
							elseif ($action != "D")
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
							$c = 0;
							echo '<ul>';
							foreach ($valid_issues as $issue)
							{
								if ($issue->hasAccess())
								{
									$c++;
									echo '<li>'.link_tag(make_url('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())), $issue->getFormattedIssueNo(true, true)).'</li>';
								}
								
								if ($c == 0)
								{
									echo '<span class="faded_out">'.__('This commit only affects issues you do not hae access to').'</span>';
								}
							}
							echo '</ul>';
						}
					?>
				</div>
			</div>
		</div>
	</div>
</div>
