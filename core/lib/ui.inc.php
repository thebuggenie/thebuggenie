<?php

	/**
	 * UI functions
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 */
	/**
	 * @deprecated Use include_template('main/identifiableselector', $options) instead
	 * @param $user_title
	 * @param $team_title
	 * @param $setuser_url
	 * @param $setteam_url
	 * @param $setuser_update_div
	 * @param $setuser_update_url
	 * @param $setteam_update_div
	 * @param $setteam_update_url
	 * @param $container_span
	 * @param $group_title
	 * @param $setgroup_url
	 * @param $setgroup_update_div
	 * @param $setgroup_update_url
	 * @param $noteams
	 * @return unknown_type
	 */
	function bugs_AJAXuserteamselector($user_title, $team_title, $setuser_url, $setteam_url, $setuser_update_div, $setuser_update_url, $setteam_update_div, $setteam_update_url, $container_span, $group_title = '', $setgroup_url = '', $setgroup_update_div = '', $setgroup_update_url = '', $noteams = false)
	{
	}
	
	/**
	 * Returns an <option> element for a given color
	 
	 * @param string $color HTML color code
	 * @param string $data the name to display
	 * 
	 * @return string
	 */
	function bugs_printColorOptions($color, $data)
	{
		$retval = '<option value="' . $color . '" style="background-color: ' . $color . '; text-align: center;"';
		$retval .= ($data == $color) ? ' selected' : '';
		$retval .= '>' . $color . '</option>';
		
		return $retval;
	}
	
	/**
	 * Used in the automatic text parsing function, returns an <a> tag linking to a specified issue
	 * 
	 * @param string $issue issue number
	 * @param string $text text to display
	 * 
	 * @return string
	 */
	function bugs_issueLinkHelper($issue, $text)
	{
		$theIssue = BUGSissue::getIssueFromLink($issue);
		$retval = '';
		$classname = '';
		if ($theIssue instanceof BUGSissue && ($theIssue->isClosed() || $theIssue->isDeleted()))
		{
			$classname = 'closed';
		}
		if ($theIssue instanceof BUGSissue)
		{
			$retval .= '<a href="' . BUGScontext::getTBGPath(). 'viewissue.php?issue_no=' . $issue . '" class="inline_issue_link ' . $classname . '" title="';
			$retval .= $theIssue->getFormattedIssueNo() . ' - ' . BUGScontext::getRequest()->sanitize_input($theIssue->getTitle()) . '">' . $text . ': ' . BUGScontext::getRequest()->sanitize_input($theIssue->getTitle()) . '</a>';
		}
		else
		{
			$retval = $text;
		}
		return $retval;
	}
	
	/**
	 * Returns an <img> tag with a specified image
	 * 
	 * @param string $image image source
	 * @param array $params[optional] html parameters
	 * @param bool $notheme[optional] whether this is a themed image or a top level path
	 * 
	 * @return string
	 */
	function image_tag($image, $params = array(), $notheme = false, $module = 'core')
	{
		if ($notheme)
		{
			$params['src'] = $image;
		}
		else
		{
			if ($module != 'core' && !file_exists(BUGScontext::getIncludePath() . 'themes/' . BUGSsettings::getThemeName() . "/{$module}/" . $image))
			{
				$params['src'] = BUGScontext::getTBGPath() . "themes/modules/{$module}/" . BUGSsettings::getThemeName() . '/' . $image;
			}
			elseif ($module != 'core')
			{
				$params['src'] = BUGScontext::getTBGPath() . 'themes/' . BUGSsettings::getThemeName() . "/{$module}/" . $image;
			}
			else
			{
				$params['src'] = BUGScontext::getTBGPath() . 'themes/' . BUGSsettings::getThemeName() . '/' . $image;
			}
		}
		if (!isset($params['alt']))
		{
			$params['alt'] = $image;
		}
		return "<img " . parseHTMLoptions($params) . '>';
	}
	
	/**
	 * Returns an <a> tag linking to a specified url
	 * 
	 * @param string $url link target
	 * @param string $link_text the text displayed in the tag
	 * @param array $params[optional] html parameters
	 * 
	 * @return string
	 */
	function link_tag($url, $link_text, $params = array())
	{
		$params['href'] = $url;
		return "<a " . parseHTMLoptions($params) . ">{$link_text}</a>";
	}
	
	/**
	 * Returns an <input type="image"> tag
	 * 
	 * @param string $image image source
	 * @param array $params[optional] html parameters
	 * @param bool $notheme[optional] whether this is a themed image or a top level path
	 * 
	 * @return string
	 */
	function image_submit_tag($image, $params = array(), $notheme = false)
	{
		$params['src'] = (!$notheme) ? BUGScontext::getTBGPath() . 'themes/' . BUGSsettings::getThemeName() . '/' . $image : $image;
		return '<input type="image" ' . parseHTMLoptions($params) . ' />';
	}
	
	/**
	 * Returns an <a> tag linking to a specified topic in the online help
	 * 
	 * @param string $topic the topic
	 * @param string $linktext the text to display in the tag
	 * 
	 * @return string
	 */
	function bugs_helpBrowserHelper($topic, $linktext)
	{
		return '<a href="javascript:void(0);" onclick="window.open(\'help.php?topic=' . $topic . '\',\'mywindow\',\'menubar=0,toolbar=0,location=0,status=0,scrollbars=1,width=600,height=700\');"><b>' . $linktext . '</b></a>';
	}
	
	/**
	 * Prints a customer dropdown menu inside a table (must be there)
	 *
	 * @param BUGScustomer $cid
	 * @param bool $dontclose
	 * 
	 * @see bugs_teamDropdown
	 * @see bugs_userDropdown
	 * 
	 * @return string
	 */
	function bugs_customerDropdown($cid, $dontclose = 0)
	{
		return bugs_teamDropdown($cid, $dontclose, true);
	}
	
	/**
	 * Prints a team dropdown menu inside a table (must be there)
	 *
	 * @param BUGSteam $tid
	 * @param bool $dontclose
	 * 
	 * @see bugs_customerDropdown
	 * @see bugs_userDropdown
	 * 
	 * @return string
	 */
	function bugs_teamDropdown($tid, $dontclose = 0, $is_customer = false)
	{

		$the_tr = "";
		if ($dontclose != 0)
		{
			$ret_tr = array();
		}
		if (!$is_customer)
		{
			if ($tid instanceof BUGSteam)
			{
				$aTeam = $tid;
			}
			else
			{
				$aTeam = BUGSfactory::teamLab($tid);
			}
			if ($aTeam->getID() == '')
			{
				return '<tr><td style="padding: 2px; color: #BBB;">' . __('Unknown team') . '</td></tr>';
			}
		}
		else
		{
			if ($tid instanceof BUGScustomer)
			{
				$aTeam = $tid;
			}
			else
			{
				$aTeam = BUGSfactory::customerLab($tid);
			}
			if ($aTeam->getID() == '')
			{
				return '<tr><td style="padding: 2px; color: #BBB;">' . __('Unknown customer') . '</td></tr>';
			}
		}
		$rnd_no = rand();

		$closemenu_string = 'hideBud(\'team_' . $aTeam->getID() . '_' . $rnd_no . '\');';

		$the_tr = '<tr>';
		$the_tr .= '<td class="imgtd_bud" id="icon_team_';
		$the_tr .= $aTeam->getID() . '_' . $rnd_no;
		$the_tr .= '"><a href="javascript:void(0);" onclick="showBud(\'team_';
		$the_tr .= $aTeam->getID() . '_' . $rnd_no;
		$the_tr .= '\');" class="image">' . image_tag('icon_team.png') . '</a></td>';
		$the_tr .= '<td style="padding-left: 2px; padding-bottom: 0px;" valign="middle"><a href="javascript:void(0);" onclick="showBud(\'team_';
		$the_tr .= $aTeam->getID() . '_' . $rnd_no;
		$the_tr .= '\');">';
		$the_tr .= $aTeam->getName();
		$the_tr .= '</a></td>';
		if ($dontclose == 0)
		{
			$the_tr .= '</tr>';
		}
		else
		{
			$ret_tr[] = $the_tr;
			$the_tr = "";
		}
		$the_tr .= '<tr><td colspan="2"><div id="bud_team_';
		$the_tr .= $aTeam->getID() . '_' . $rnd_no;
		$the_tr .= '" style="display: none; position: absolute;" class="bud_actions">';
		$the_tr .= '<div style="padding: 3px; margin-bottom: 2px;"><b>' . $aTeam->getName() . '</b></div>';

		$trigger_name = (!$is_customer) ? 'teamactions' : 'customeractions';  
		$the_tr .= BUGScontext::trigger('core', $trigger_name.'_top', array("tid" => $tid, "closemenustring" => $closemenu_string, 'retval' => &$the_tr));

		$the_tr .= BUGScontext::trigger('core', $trigger_name.'_bottom', array("tid" => $tid, "closemenustring" => $closemenu_string));

		$the_tr .= '<div style="text-align: right; padding: 3px; font-size: 9px;"><a href="javascript:void(0);" onclick="' . $closemenu_string . '">' . __('Close this menu') . '</a></div>';
		$the_tr .= '</td></tr>';

		if ($dontclose == 0)
		{
			return $the_tr;
		}
		else
		{
			$ret_tr[] = $the_tr;
			return $ret_tr;
		}
	}
	

	/**
	 * Prints a list of milestones for a given project
	 *
	 * @param integer $project_id the id for the project you want to show milestones for
	 * @param boolean $onlyVisible if we want to show hidden milestones or not, default we dont
	 * 
	 * @return null
	 */
	function bugs_printMilestones($project_id, $onlyVisible = true)
	{

		$milestones = BUGSfactory::projectLab($project_id)->getMilestones($onlyVisible);

		?>
		<table style="margin-top: 5px; width: 100%;" cellpadding=0 cellspacing=0>
		<?php
		$cc = 0;
		foreach($milestones as $aMilestone)
		{
			$aMilestone = BUGSfactory::milestoneLab($aMilestone['id']);
			$cc++;
			?><tr>
			<td style="width: 50%; padding: 2px; padding-bottom: 10px;" valign="top">
			<div style="width: 100%; padding: 2px; padding-bottom: 0px;"><b><?php print $aMilestone->getName(); ?></b></div>
			<div style="padding: 2px; padding-top: 0px; font-size: 10px;">
			<?php
			if($aMilestone->isScheduled()) {
				echo __('Scheduled: %date%', array('%date%' => bugs_formatTime($aMilestone->getScheduledDate(), 5)));
			} else {
				echo __('Not scheduled yet');
			}
			?><br>
			<?php $milestoneStatus = $aMilestone->getScheduledStatus(); ?>
			<div style="color: #<?php echo $milestoneStatus['color']; ?>"><?php echo $milestoneStatus['status']; ?></div>			
			</div>
			<div style="padding: 2px;"><?php bugs_printPercentBar($aMilestone->getPercentComplete(), 10); ?></div>
			<table style="table-layout: fixed; width: 100%;" cellpadding=0 cellspacing=0>
			<tr>
			<td style="font-size: 10px; padding-left: 2px; width: 50%;"><i><?php echo __('Closed issues: %number_of_closed% of %number_of_issues% assigned to this milestone', array('%number_of_closed%' => $aMilestone->getClosedIssues(), '%number_of_issues%' => count($aMilestone->getIssues()))); ?></i></td>
			<td style="font-size: 11px; padding-right: 2px; width: 50%; text-align: right;"><a href="viewproject.php?project_id=<?php echo $project_id; ?>&amp;milestone_id=<?php print $aMilestone->getID(); ?>"><?php echo __('View details about this milestone'); ?></a></td>
			</tr>
			</table>
			</td>
			</tr>
			<?php
		}
		if (count($milestones) == 0 || $cc == 0)
		{
			?>
			<tr><td style="width: 100%; padding: 5px; color: #BBB;"><?php echo __('There are no milestones for this project.'); ?></td></tr>
			<?php
		}
		?>
		</table>
		<?php
	}
	
	/**
	 * Returns a percentage bar
	 *
	 * @param integer $percent
	 * @param integer $size[optional] the height of the percent bar
	 * 
	 * @return string
	 */
	function bugs_printPercentBar($percent, $size = 16)
	{
		echo '<table style="table-layout: fixed; width: 100%;" cellpadding=0 cellspacing=0>';
		echo '<tr>';
		if ($percent > 0)
		{
			echo '<td style="font-size: 3px; width: ' . $percent . '%; height: ' . $size . 'px; background-color: #8C8;"><b style="text-decoration: none;">&nbsp;</b></td>';
		}
		if ($percent < 100)
		{
			echo '<td style="font-size: 3px; width: ' . (100 - $percent) . '%; height: ' . $size . 'px; background-color: #AFA;"><b style="text-decoration: none;">&nbsp;</b></td>';
		}
		echo '</tr>';
		echo '</table>';
	}
	
	/**
	 * Returns a green box with a "succesful" look
	 *
	 * @param string $title the title inside the box
	 * @param string $content[optional] the content of the box
	 * @param string $id[optional] the html id for the box
	 * @param bool $start_hidden[optional] whether or not to start hidden
	 * @param bool $multiline[optional] whether or not the box is multiline
	 * 
	 * @see bugs_failureStrip
	 * 
	 * @return string
	 */
	function bugs_successStrip($title, $content = '', $id = '', $start_hidden = false, $multiline = true)
	{
		$retval = '';
		$retval .= '<div class="medium_transparent" style="margin: 0; position: fixed; top: 0; left: 0; width: 100%; padding: 10px 0 10px 0; background-color: #45E845; font-size: 14px; color: #000; border-bottom: 1px solid #555;';
		if ($start_hidden) $retval .= ' display: none;';
		$retval .= '"';
		if ($id != '') $retval .= " id=\"{$id}\"";
		$retval .= '>';
		if ($multiline)
		{
			$retval .= '<div style="color: #000; font-weight: bold;" id="'.$id.'_title">' . $title . '</div><div id="'.$id.'_content">' . $content . '</div>';
		}
		else
		{
			$retval .= '<span style="color: #000; font-weight: bold; margin-right: 10px;" id="'.$id.'_title">' . $title . '</span><span id="'.$id.'_content">' . $content . '</span>';
		}
		$retval .= '</div>';
		/*$retval .= '</td>';
		$retval .= '</tr>';
		$retval .= '</table>';*/
		
		return $retval;
	}

	/**
	 * Returns a red box with a "failure" look
	 *
	 * @param string $title the title inside the box
	 * @param string $content[optional] the content of the box
	 * @param string $id[optional] the html id for the box
	 * @param bool $start_hidden[optional] whether or not to start hidden
	 * @param bool $multiline[optional] whether or not the box is multiline
	 * 
	 * @see bugs_successStrip
	 * 
	 * @return string
	 */
	function bugs_failureStrip($title, $content = '', $id = '', $start_hidden = false)
	{
		$retval = '';
		$retval .= '<div class="medium_transparent" style="margin: 0; position: fixed; top: 0; left: 0; width: 100%; padding: 10px 0 10px 0; background-color: #E84545; font-size: 14px; color: #000; border-bottom: 1px solid #555;';
		if ($start_hidden) $retval .= ' display: none;';
		$retval .= '"';
		if ($id != '') $retval .= " id=\"{$id}\"";
		$retval .= '>';
		$retval .= '<div style="color: #333; font-weight: bold;" id="'.$id.'_title">' . $title . '</div><div id="'.$id.'_content">' . $content . '</div>';
		$retval .= '</div>';
				
		return $retval;
	}
	
	
	/**
	 * Prints out the print mode strip displayed at the top when in print mode
	 *
	 * @param string $title the link back to normal mode
	 * 
	 * @return null
	 */
	function bugs_printmodeStrip($normalmode_link)
	{
		?>
		<div class="print_header_strip">
		<table style="width: 100%;">
		<tr>
		<td style="width: 50%; text-align: left;"><b><?php echo __('You are now in "print friendly" mode'); ?>&nbsp;</b>(<a href="<?php echo $normalmode_link; ?>"><?php echo __('Switch back to normal mode'); ?></a>)</td>
		<td style="width: 50%; text-align: right;"><?php echo __('Logged in as %username%', array('%username%' => '<b>' . BUGScontext::getUser() . '</b>')); ?></td>
		</tr>
		</table>
		</div>
		<?php
	}

	/**
	 * Includes a template with specified parameters
	 * 
	 * @param string	$template	name of template to load, or module/template to load 
	 * @param array 	$params  	key => value pairs of parameters for the template
	 */
	function include_template($template, $params = array())
	{
		return BUGSactioncomponent::includeTemplate($template, $params);
	}
	
	/**
	 * Includes a component with specified parameters
	 * 
	 * @param string	$component	name of component to load, or module/component to load 
	 * @param array 	$params  	key => value pairs of parameters for the template
	 */
	function include_component($component, $params = array())
	{
		return BUGSactioncomponent::includeComponent($component, $params);
	}
	
	/**
	 * Generate a url based on a route
	 * 
	 * @param string	$name 	The route key
	 * @param array 	$params	key => value pairs of route parameters
	 * 
	 * @return string
	 */
	function make_url($name, $params = array())
	{
		return BUGScontext::getRouting()->generate($name, $params);
	}
	
	/**
	 * Returns a string with html options based on an array
	 * 
	 * @param array	$options an array of options
	 * 
	 * @return string
	 */
	function parseHTMLoptions($options)
	{
		$option_strings = array();
		if (!is_array($options))
		{
			throw new Exception('Invalid HTML options. Must be an array with key => value pairs corresponding to html attributes');
		}
		foreach ($options as $key => $val)
		{
			$option_strings[$key] = "{$key}=\"{$val}\"";
		}
		return implode(' ', array_values($option_strings));
	}
