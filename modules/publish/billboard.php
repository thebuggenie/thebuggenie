<?php

	$page = 'publish';
	define ('THEBUGGENIE_PATH', '../../');
	
	require THEBUGGENIE_PATH . 'include/checkcookie.inc.php';
	require THEBUGGENIE_PATH . "include/b2_engine.inc.php";
	
	require TBGContext::getIncludePath() . "include/ui_functions.inc.php";

	TBGContext::getModule('publish')->activate();
	
	require TBGContext::getIncludePath() . "modules/publish/billboard_logic.inc.php";
	require TBGContext::getIncludePath() . "include/header.inc.php";
	require TBGContext::getIncludePath() . "include/menu.inc.php";

?>
<div style="text-align: left; width: 100%;">
	<div style="float: left; width: 230px; padding: 10px;">
		<div style="border: 1px solid #DDD; width: 228px;">
			<div style="background-color: #F1F1F1; border-bottom: 1px solid #DDD; padding: 3px 0px 3px 7px; font-weight: bold;"><p><?php echo __('Billboard'); ?></p></div>
			<div style="padding: 7px 0px 7px 7px;">
				<table style="width: 100%; margin-bottom: 10px;" cellpadding=0 cellspacing=0>
					<tr>
						<td style="width: 20px; padding: 2px;"><?php echo image_tag('news_item.png'); ?></td>
						<td style="width: auto; padding: 2px;"><b><a href="<?php echo TBGContext::getTBGPath(); ?>modules/publish/publish.php"><?php echo __('Visit News &amp; Articles center'); ?></a></b></td>
					</tr>
				</table>
				<?php echo __('The billboard is a place where users and developers can share ideas, links or interesting articles.'); ?>
				<table style="width: 100%;" cellpadding=0 cellspacing=0>
					<?php 
					
					if (TBGContext::getRequest()->getParameter('billboard'))
					{
						?>
						<tr>
							<td style="width: 20px; padding: 2px;"><?php echo image_tag('publish/icon_manage.png'); ?></td>
							<td style="width: auto; padding: 2px;"><b><a href="<?php echo TBGContext::getTBGPath(); ?>modules/publish/billboard.php"><?php echo __('Show all billboards'); ?></a></b></td>
						</tr>
						<?php 
					}

					if (TBGContext::getUser()->hasPermission('publish_postonglobalbillboard', 0, "publish") || TBGContext::getUser()->hasPermission('publish_postonteambillboard', 0, "publish"))
					{
						?>
						<tr><td colspan=2>&nbsp;</td></tr>
						<tr>
							<td style="width: 20px; padding: 2px;"><?php echo image_tag('publish/icon_new_link.png'); ?></td>
							<td style="width: auto; padding: 2px;"><a href="javascript:void(0);" onclick="$('post_new_link').toggle();"><?php echo __('Post new link on a billboard'); ?></a></td>
						</tr>
						<tr>
							<td style="width: 20px; padding: 2px;"><?php echo image_tag('publish/icon_new_link.png'); ?></td>
							<td style="width: auto; padding: 2px;"><a href="javascript:void(0);" onclick="$('post_new_text').toggle();"><?php echo __('Post new text on a billboard'); ?></a></td>
						</tr>
						<?php 
					}

					?>
				</table>
				<?php 
				
				if (TBGContext::getUser()->hasPermission('publish_postonglobalbillboard', 0, "publish") || TBGContext::getUser()->hasPermission('publish_postonteambillboard', 0, "publish"))
				{
					?>
					<div style="padding: 3px;"><i><?php echo __('To post an article on a billboard, visit the article and select "post on a billboard" from the left menu'); ?></i></div>
					<?php 
				}

				?>
			</div>
		</div>
		<div style="position: relative; margin-top: 5px;">
			<div style="padding: 3px; position: absolute; width: 222px; background-color: #F9F9F9; border: 1px solid #DDD; display: none;" id="post_new_link">
			<div style="border-bottom: 1px solid #DDD;"><b><?php echo __('Post a new link'); ?></b></div>
			<div style="padding-top: 5px;"><?php echo __('Enter the URL and a description here:'); ?><br>
			<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="billboard.php" method="post" name="add_new_link_form" id="add_new_link_form" onsubmit="return false">
			<input type="hidden" name="billboard_post_new_link" value="true">
			<b><?php echo __('URL:'); ?></b><br>
			<input type="text" name="post_link_url" value="" style="width: 100%;">
			<b><?php echo __('Description:'); ?></b><br>
			<input type="text" name="post_link_description" value="" style="width: 100%;">
			<b><?php echo __('Select billboard:'); ?></b><br>
			<select name="post_link_billboard" id="post_link_billboard" style="width: 100%;">
				<?php 
				
				if (TBGContext::getUser()->hasPermission('publish_postonglobalbillboard', 0, "publish"))
				{
					?>
					<option value="0"><?php echo __('Global billboard'); ?></option>
					<?php 
				}

				if (TBGContext::getUser()->hasPermission('publish_postonteambillboard', 0, "publish"))
				{
					foreach (TBGContext::getUser()->getTeams() as $aTeamID)
					{
						$theTeam = TBGFactory::teamLab($aTeamID);
						?><option value="<?php echo $aTeamID; ?>"><?php echo $theTeam->getName(); ?></option><?php
					}
				}
					
				?>
			</select>
			<div style="padding-top: 3px; text-align: right;"><input type="submit" value="<?php echo __('Post link'); ?>" onclick="addBillboardLink();"></div>
			</form></div>
			</div>
			<div style="padding: 3px; position: absolute; width: 262px; background-color: #F9F9F9; border: 1px solid #DDD; display: none;" id="post_new_text">
			<div style="border-bottom: 1px solid #DDD;"><b><?php echo __('Post a new text'); ?></b></div>
			<div style="padding-top: 5px;"><?php echo __('Enter a title and the content here:'); ?><br>
			<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="billboard.php" method="post" name="add_new_post_form" id="add_new_post_form" onsubmit="return false">
			<input type="hidden" name="billboard_post_new_text" value="true">
			<b><?php echo __('Title:'); ?></b><br>
			<input type="text" name="post_text_title" value="" style="width: 100%;">
			<b><?php echo __('Content:'); ?></b><br>
			<?php echo tbg_newTextArea('post_text_content', '100px', '250px'); ?>
			<b><?php echo __('Select billboard:'); ?></b><br>
			<select name="post_text_billboard" id="post_text_billboard" style="width: 100%;">
				<?php 
				
				if (TBGContext::getUser()->hasPermission('publish_postonglobalbillboard', 0, "publish"))
				{
					?>
					<option value="0"><?php echo __('Global billboard'); ?></option>
					<?php 
				}

				if (TBGContext::getUser()->hasPermission('publish_postonteambillboard', 0, "publish"))
				{
					foreach (TBGContext::getUser()->getTeams() as $aTeamID)
					{
						$theTeam = TBGFactory::teamLab($aTeamID);
						?><option value="<?php echo $aTeamID; ?>"><?php echo $theTeam->getName(); ?></option><?php
					}
				}
					
				?>
			</select>
			<div style="padding-top: 3px; text-align: right;"><input type="submit" value="<?php echo __('Post this text'); ?>" onclick="addBillboardPost();"></div>
			</form></div>
			</div>
		</div> 
	</div>
	<div style="margin-left: 255px; margin-right: 10px; text-align: left; padding-top: 10px;">
	<?php 
		
	if (!TBGContext::getRequest()->getParameter('billboard'))
	{
		?>
		<div style="border-bottom: 1px solid #DDD; padding: 3px; font-size: 13px;"><b><a href="<?php echo TBGContext::getTBGPath(); ?>modules/publish/billboard.php?billboard=0"><?php echo __('Global billboard'); ?></a></b> <span style="display: inline; font-weight: normal; font-size: 11px;"></span></div>
		<?php
		
			$billboardposts = TBGContext::getModule('publish')->getBillboardPosts(0, 9);

			for ($cc = 1; $cc <= 3; $cc++) 
			{
				?>
				<div style="width: 30%; float: left;">
					<ul style="list-style-type: none; padding: 0px; margin: 0px;" id="billboard_0">
						<?php 
						
							for ($cc2 = 1; $cc2 <= 3; $cc2++) 
							{
								if (!empty($billboardposts))
								{
									$billboardpost = array_shift($billboardposts);
									TBGContext::getModule('publish')->printBillboardPostOnBillboard($billboardpost); 
								}
							}
							
						?>
					</ul>
				</div>
				<?php
			}
			
		?><div style="clear: both;">&nbsp;</div>
		<?php

		if (TBGContext::getModule('publish')->getSetting('enableteambillboards') == 1)
		{
			foreach (TBGContext::getUser()->getTeams() as $aTeamID) 
			{
				$billboardposts = TBGContext::getModule('publish')->getBillboardPosts($aTeamID, 9);
				$aTeam = TBGFactory::teamLab($aTeamID);
				?><div style="border-bottom: 1px solid #DDD; padding: 3px; font-size: 13px;"><b><a href="<?php echo TBGContext::getTBGPath(); ?>modules/publish/billboard.php?billboard=<?php echo $aTeam->getID(); ?>"><?php echo __('%teamname% billboard', array('%teamname%' => $aTeam->getName())); ?></a></b> <span style="display: inline; font-weight: normal; font-size: 11px;">(<?php echo __('available only to team members'); ?>)</span></div>
				<?php
				for ($cc = 1; $cc <= 3; $cc++) 
				{
					?>
					<div style="width: 30%; float: left;">
						<ul style="list-style-type: none; padding: 0px; margin: 0px;" id="billboard_<?php echo $aTeamID; ?>">
							<?php 
							
								for ($cc2 = 1; $cc2 <= 3; $cc2++) 
								{
									if (!empty($billboardposts))
									{
										$billboardpost = array_shift($billboardposts);
										TBGContext::getModule('publish')->printBillboardPostOnBillboard($billboardpost);
									}
								}
								
							?>
						</ul>
					</div>
					<?php
				}
				?><div style="clear: both;">&nbsp;</div>
				<?php
			}
		}
		
	}
	else
	{
		$billboardtitle = null;
		
		if ((int) TBGContext::getRequest()->getParameter('billboard') == 0)
		{
			$billboardposts = TBGContext::getModule('publish')->getBillboardPosts(0, 30);
			$billboardtitle = 'Global billboard';
		}
		elseif (in_array((int) TBGContext::getRequest()->getParameter('billboard'), TBGContext::getUser()->getTeams()))
		{
			$billboardposts = TBGContext::getModule('publish')->getBillboardPosts(TBGContext::getRequest()->getParameter('billboard'), 30);
			$billboardtitle = TBGFactory::teamLab(TBGContext::getRequest()->getParameter('billboard'))->getName() . ' billboard';
		}
			
		if ($billboardtitle !== null)
		{
			?><div style="border-bottom: 1px solid #DDD; padding: 3px; font-size: 13px;"><b><?php echo $billboardtitle; ?></b></div>
			<?php
			for ($cc = 1; $cc <= 3; $cc++) 
			{
				?>
				<div style="width: 30%; float: left;">
					<ul style="list-style-type: none; padding: 0px; margin: 0px;" id="billboard_<?php echo (int) TBGContext::getRequest()->getParameter('billboard'); ?>">
						<?php 
						
							for ($cc2 = 1; $cc2 <= 10; $cc2++) 
							{
								if (!empty($billboardposts))
								{
									$billboardpost = array_shift($billboardposts);
									TBGContext::getModule('publish')->printBillboardPostOnBillboard($billboardpost);
								}
							}
							
						?>
					</ul>
				</div>
				<?php
			}
			?><div style="clear: both;">&nbsp;</div>
			<?php
		}
	}
		
	 ?>
	 </div>
</div>
<?php

	require_once TBGContext::getIncludePath() . "include/footer.inc.php";

?>