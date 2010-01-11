<?php

	$tbg_response->setTitle(__('Configuration center'));
	
?>
<table style="table-layout: fixed; width: 990px; margin: 10px 0 0 10px;" cellpadding=0 cellspacing=0>
<tr>
<td valign="top">
<div style="font-size: 15px; font-weight: bold; padding: 5px; border-bottom: 1px solid #DDD;"><?php echo __('The Bug Genie - Configuration center')?></div>
<p style="padding: 5px;"><?php echo __('Please select a configuration section from the list below. If you are stuck, you can always refer to the %tbg_online_help%.', array('%tbg_online_help%' => tbg_helpBrowserHelper('configure', __('The Bug Genie online help')))); ?></p>
<div style="margin-left: 5px;">
	<div style="margin-top: 15px; background-color: #F5F5F5; font-size: 13px; font-weight: bold; padding: 3px; border-bottom: 1px solid #EEE;"><?php echo __('General configuration'); ?></div>
	<ul class="config_badges">
	<?php foreach ($general_config_sections as $section => $config_info): ?>
  		<?php if (array_key_exists('icon', $config_info)) $config_info = array($config_info); ?>
  		<?php foreach ($config_info as $info): ?>
  			<li>
  			<?php if (is_array($info['route'])): ?>
  				<?php $url = make_url($info['route'][0], $info['route'][1]); ?>
  			<?php else: ?>
  				<?php $url = make_url($info['route']); ?>
  			<?php endif;?>
	  			<a href="<?php echo $url; ?>">
		  			<b>
	  					<?php echo image_tag('cfg_icon_'.$info['icon'].'.png', array('style' => 'float: left; margin-right: 5px;')); ?>
	  					<?php echo $info['description']; ?>
	  				</b>
	  				<span><?php echo $info['details']; ?></span>
  				</a>
  			</li>
  		<?php endforeach;?>
	<?php endforeach;?>
	</ul>
	<div style="margin-top: 15px; background-color: #F5F5F5; clear: both; font-size: 13px; font-weight: bold; padding: 3px; border-bottom: 1px solid #EEE;"><?php echo __('Data configuration'); ?></div>
	<ul class="config_badges">
	<?php foreach ($data_config_sections as $section => $config_info): ?>
  		<?php if (array_key_exists('icon', $config_info)) $config_info = array($config_info); ?>
  		<?php foreach ($config_info as $info): ?>
  			<li>
  			<?php if (is_array($info['route'])): ?>
  				<?php $url = make_url($info['route'][0], $info['route'][1]); ?>
  			<?php else: ?>
  				<?php $url = make_url($info['route']); ?>
  			<?php endif;?>
  				<a href="<?php echo $url; ?>">
		  			<b>
	  					<?php echo image_tag('cfg_icon_'.$info['icon'].'.png', array('style' => 'float: left; margin-right: 5px;')); ?>
	  					<?php echo $info['description']; ?>
	  				</b>
	  				<span><?php echo $info['details']; ?></span>
  				</a>
  			</li>
  		<?php endforeach;?>
	<?php endforeach;?>
	</ul>
	<div style="margin-top: 15px; background-color: #F5F5F5; clear: both; font-size: 13px; font-weight: bold; padding: 3px; border-bottom: 1px solid #EEE;"><?php echo __('Modules / addons'); ?></div>
	<ul class="config_badges">
	<?php foreach ($module_config_sections as $section => $config_info): ?>
  		<?php if (array_key_exists('icon', $config_info)) $config_info = array($config_info); ?>
  		<?php foreach ($config_info as $info): ?>
			<?php if ($info['module'] != 'core' && !TBGContext::getModule($info['module'])->hasConfigSettings()) continue; ?>
  			<li>
  			<?php if (is_array($info['route'])): ?>
  				<?php $url = make_url($info['route'][0], $info['route'][1]); ?>
  			<?php else: ?>
  				<?php $url = make_url($info['route']); ?>
  			<?php endif;?>
	  			<a href="<?php echo $url; ?>">
		  			<b>
		  				<?php if ($info['module'] != 'core'): ?>
	  						<?php echo image_tag('cfg_icon_'.$info['icon'].'.png', array('style' => 'float: left; margin-right: 5px;'), false, $info['module']); ?>
  						<?php else: ?>
  							<?php echo image_tag('cfg_icon_'.$info['icon'].'.png', array('style' => 'float: left; margin-right: 5px;')); ?>
  						<?php endif; ?>
	  					<?php echo $info['description']; ?>
	  				</b>
	  				<span><?php echo $info['details']; ?></span>
  				</a>
  			</li>
  		<?php endforeach;?>
	<?php endforeach;?>
	</ul>
	<br style="clear: both;">
</div>
</td>
</tr>
</table>

<?php 

/*	if (TBGContext::getRequest()->getParameter('config_module'))
	{
		?>
		<td valign="top">
		<?php
	
		if (TBGContext::getRequest()->getParameter('config_module') == "core")
		{
			if (is_numeric(TBGContext::getRequest()->getParameter('section')))
			{
				$access_section = false;
				if (TBGContext::getUser()->hasPermission("b2saveconfig", TBGContext::getRequest()->getParameter('section'), "core"))
				{
					$access_section = true;
					$access_level = configurationActions::ACCESS_FULL;
				}
				elseif (TBGContext::getUser()->hasPermission("b2viewconfig", TBGContext::getRequest()->getParameter('section'), "core"))
				{
					$access_section = true;
					$access_level = "read";
				}
				
				if ($access_section == true)
				{
					switch (TBGContext::getRequest()->getParameter('section'))
					{
						case 1:
							$section_name = "teamgroups";
							break;
						case 2:
							$section_name = "users";
							break;
						case 3:
							$section_name = "files";
							break;
						case 4:
							$section_name = "datatypes";
							break;
						case 9:
							$section_name = "milestones";
							break;
						case 10:
							$section_name = "projects";
							break;
						case 11:
							$section_name = "server";
							break;
						case 12:
							$section_name = "general";
							break;
						case 13:
							$section_name = "wizard";
							break;
						case 14:
							if (TBGContext::getUser()->getScope()->getID() == TBGSettings::getDefaultScope()->getID()) $section_name = "scopes";
							break;
						case 15:
							$section_name = "modules";
							break;
						case 16:
							if (TBGContext::getUser()->getScope()->getID() == TBGSettings::getDefaultScope()->getID()) $section_name = "import";
							break;
					}
					if ($section_name != "")
					{
						include_template($section_name, array('access_level' => $access_level));
					}
				}
				else
				{
					tbg_msgbox(false, "", __('You do not have access to this page'));
				}
			}
		}
		else
		{
			$access_section = false;
	
			if (TBGContext::getUser()->hasPermission("b2saveconfig", 15, "core"))
			{
				$access_section = true;
				$access_level = configurationActions::ACCESS_FULL;
			}
			elseif (TBGContext::getUser()->hasPermission("b2viewconfig", 15, "core"))
			{
				$access_section = true;
				$access_level = "read";
			}
	
			if (TBGContext::getModule(TBGContext::getRequest()->getParameter('config_module')) instanceof TBGModule && TBGContext::getModule(TBGContext::getRequest()->getParameter('config_module'))->hasAccess() && $access_section)
			{
				require_once "modules/" . strtolower(TBGContext::getRequest()->getParameter('config_module')) . "/config.inc.php";
			}
			else
			{
				tbg_msgbox(false, "", __('You do not have access to this module'));
			}
		}
	}
	else
	{
		?>
		<td style="padding-right: 10px;" valign="top">
		<?php
		
		include_template('main_help');
	}
*/
