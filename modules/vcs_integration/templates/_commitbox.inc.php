<?php
	if ($author == 0)
	{
		$user = __('No such user');
	}
	else
	{
		$theUser = TBGContext::factory()->TBGUser($author);
	}

	$web_path = TBGContext::getModule('vcs_integration')->getSetting('web_path_' . $projectId);
	$web_repo = TBGContext::getModule('vcs_integration')->getSetting('web_repo_' . $projectId);
	
	switch (TBGContext::getModule('vcs_integration')->getSetting('web_type_' . $projectId))
	{
		case 'viewvc':
			$link_rev = $web_path . '/' . '?root=' . $web_repo . '&amp;view=rev&amp;revision=' . $revision; 
			break;
		case 'viewvc_repo':
			$link_rev = $web_path . '/' . '?view=rev&amp;revision=' . $revision; 
			break;
		case 'websvn':
			$link_rev = $web_path . '/revision.php?repname=' . $web_repo . '&amp;isdir=1&amp;rev=' . $revision; 
			break;
		case 'websvn_mv':
			$link_rev = $web_path . '/' . '?repname=' . $web_repo . '&amp;op=log&isdir=1&amp;rev=' . $revision; 
			break;
		case 'loggerhead':
			$link_rev = $web_path . '/' . $web_repo . '/revision/' . $revision; 
			break;
		case 'gitweb':
			$link_rev = $web_path . '/' . '?p=' . $web_repo . ';a=commitdiff;h=' . $revision; 
			break;
		case 'cgit':
			$link_rev = $web_path . '/' . $web_repo . '/commit/?id=' . $revision; 
			break;
		case 'hgweb':
			$revision = explode(':', $revision);
			$link_rev = $web_path . '/' . $web_repo . '/rev/' . $revision[1]; 
			break;
		case 'github':
			$link_rev = 'http://github.com/' . $web_repo . '/commit/' . $revision; 
			break;
	}
?>

<div class="rounded_box mediumgrey borderless cut_top cut_bottom">
	<a href="javascript:void(0);" style="float: left; padding-right: 5px;" id="checkin_expand_<?php echo $id; ?>" onclick="$('checkin_details_<?php echo $id; ?>').show(); $('checkin_expand_<?php echo $id; ?>').hide(); $('checkin_collapse_<?php echo $id; ?>').show();"><?php echo image_tag('expand.png'); ?></a>
	<a href="javascript:void(0);" style="display: none; float: left; padding-right: 5px;" id="checkin_collapse_<?php echo $id; ?>" onclick="$('checkin_details_<?php echo $id; ?>').hide(); $('checkin_expand_<?php echo $id; ?>').show(); $('checkin_collapse_<?php echo $id; ?>').hide();"><?php echo image_tag('collapse.png'); ?></a>
<?php 
	switch (TBGContext::getModule('vcs_integration')->getSetting('web_type_' . $projectId))
	{
		case 'hgweb':
			if (isset($theUser))
			{
				$user = '<div style="display: inline;">'.get_component_html('main/userdropdown', array('user' => $theUser, 'size' => 'small')).'</div>';
			}
			?>
			<span class="commenttitle" style="float: left; padding-right: 5px;"><a href="<?php echo $link_rev; ?>" target="_new"><?php echo __('Revision %revno%', array('%revno%' => $revision[0].':'.$revision[1])) . '</a> - ' . __('committed on %date% by', array('%date%' => tbg_formatTime($date, 10))) . '</span> ' . $user; ?>
			<?php
			break;
		default:
			if (isset($theUser))
			{
				$user = '<div style="display: inline;">'.get_component_html('main/userdropdown', array('user' => $theUser, 'size' => 'small')).'</div>';
			}
			?>
			<span class="commenttitle" style="float: left; padding-right: 5px;"><a href="<?php echo $link_rev; ?>" target="_new"><?php echo __('Revision %revno%', array('%revno%' => $revision)) . '</a> - ' . __('committed on %date% by', array('%date%' => tbg_formatTime($date, 10))) . '</span> ' . $user; ?>
			<?php
			break;
	}
?>
</div>
<div id="checkin_details_<?php echo $id; ?>" style="display: none;" class="rounded_box borderless cut_bottom cut_top iceblue">
	<h4><?php echo __('Log entry:'); ?></h4>
	<pre>
<?php echo $log; ?>
	</pre>
	<h4><?php echo __('Changed files:'); ?></h4>
	<table border=0 cellpadding=0 cellspacing=0 style="width: 100%;">
	<?php

	foreach ($files as $file)
	{
		echo '<tr>';
		$action = $file[1];
		if ($action == 'M'): $action = 'U'; endif;
		echo '<td class="imgtd">' . image_tag('icon_action_' . $action . '.png', null, false, 'vcs_integration') . '</td>';
		switch (TBGContext::getModule('vcs_integration')->getSetting('web_type_' . $projectId))
		{
			case 'viewvc':
				$link_file = $web_path . '/' . $file[0] . '?root=' . $web_repo . '&amp;view=log';
				$link_diff = $web_path . '/' . $file[0] . '?root=' . $web_repo . '&amp;r1=' . $file[2] . '&amp;r2=' . $file[3];
				$link_view = $web_path . '/' . $file[0] . '?root=' . $web_repo . '&amp;revision=' . $file[2] . '&amp;view=markup';
				break;
			case 'viewvc_repo':
				$link_file = $web_path . '/' . $file[0] . '?view=log';
				$link_diff = $web_path . '/' . $file[0] . '?r1=' . $file[2] . '&amp;r2=' . $file[3];
				$link_view = $web_path . '/' . $file[0] . '?revision=' . $file[2] . '&amp;view=markup';
				break;
			case 'websvn':
				$link_file = $web_path . '/log.php?repname=' . $web_repo . '&amp;path=/' . $file[0];
				$link_diff = $web_path . '/comp.php?repname=' . $web_repo . '&amp;compare[]=/' . $file[0] . '@' . $file[2] . '&amp;compare[]=/' . $file[0] . '@' . $file[3];
				$link_view = $web_path . '/filedetails.php?repname=' . $web_repo . '&path=/' . $file[0] . '&amp;rev=' . $file[2];
				break;
			case 'websvn_mv':
				$link_file = $web_path . '/' . $file[0] . '?repname=' . $web_repo;
				$link_diff = $web_path . '/' . $file[0] . '?repname=' . $web_repo . '&amp;compare[]=/' . $file[0] . '@' . $file[2] . '&amp;compare[]=/' . $file[0] . '@' . $file[3];
				$link_view = $web_path . '/' . $file[0] . '?repname=' . $web_repo . '&amp;rev=' . $file[2];
				break;
			case 'loggerhead':
				$link_file = $web_path . '/' . $web_repo . '/changes';
				$link_diff = $web_path . '/' . $web_repo . '/revision/' . $file[2] . '?compare_revid=' . $file[3];
				$link_view = $web_path . '/' . $web_repo . '/annotate/head:/' . $file[0];
				break;
			case 'gitweb':
				$link_file = $web_path . '/' . '?p=' . $web_repo . ';a=history;f=' . $file[0] . ';hb=HEAD';
				$link_diff = $web_path . '/' . '?p=' . $web_repo . ';a=blobdiff;f=' . $file[0] . ';hb=' . $file[2] . ';hpb=' . $file[3];
				$link_view = $web_path . '/' . '?p=' . $web_repo . ';a=blob;f=' . $file[0] . ';hb=' . $file[2];
				break;
			case 'cgit':
				$link_file = $web_path . '/' . $web_repo . '/log';
				$link_diff = $web_path . '/' . $web_repo . '/diff/' . $file[0] . '?id=' . $file[2] . '?id2=' . $file[3];
				$link_view = $web_path . '/' . $web_repo . '/tree/' . $file[0] . '?id=' . $file[2];
				break;
			case 'hgweb':
				$rev = explode(':', $file[2]);
				$link_file = $web_path . '/' . $web_repo . '/log/tip/' . $file[0];
				$link_diff = $web_path . '/' . $web_repo . '/diff/' . $rev[1] . '/' . $file[0];
				$link_view = $web_path . '/' . $web_repo . '/file/' .$rev[1] . '/' . $file[0];
				break;
			case 'github':
				$link_file = 'http://github.com/' . $web_repo . '/commits/master/' . $file[0];
				$link_diff = 'http://github.com/' . $web_repo . '/commit/' . $file[2];
				$link_view = 'http://github.com/' . $web_repo . '/blob/' .$file[2] . '/' . $file[0];
				break;
		}

		echo '<td><a href="' . $link_file . '" target="_new"><b>' . $file[0] . '</b></a></td>';
		if ($action == "U" || $action == "M")
		{
			if (substr($file[0], -1) == '/' || substr($file[0], -1) == '\\')
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
	?>
	</table>
</div>
