<table class="configstrip" cellpadding=0 cellspacing=0>
	<tr>
		<td class="cleft"><b><?php echo __('Configuration center'); ?></b></td>
	</tr>
	<tr>
		<td class="cdesc">
		<?php echo __('This is the BUGS 2 Configuration center. From here you configure everything about BUGS 2. Below is a more detailed description of each section.'); ?><br><br>
		</td>
	</tr>
</table>
<table cellpadding=0 cellspacing=0 style="width: 720px;">
	<tr>
		<td style="padding: 5px; width: 50%;" valign="top"><b><?php echo __('General configuration'); ?></b><br>
		<?php echo __('This section contains all the main settings that will be set up once and then forgotten. Hopefully.'); ?></td>
		<td style="padding: 5px;" valign="top"><b><?php echo __('Reporting issues'); ?></b><br>
		<?php echo __('This section contains all the settings needed to be able to report issues. You need to set up at least the following:'); ?>
		<ul>
			<li><?php echo __('A project, with an edition (and one build)'); ?></li>
			<li><?php echo __('A component'); ?></li>
			<li><?php echo __('Data types such as issue types, priorities, etc.'); ?></li>
		</ul>
		<?php echo __('There are already a lot of common data types in BUGS 2, so in many cases you won\'t need to set up all these on your own. Just have a look at what is there.'); ?> 
		</td>
	</tr>
	<tr>
		<td style="padding: 5px; padding-top: 10px;" valign="top"><b><?php echo __('Users and data'); ?></b><br>
		<?php echo __('This section contains everything related to users, groups, teams and permissions. It also contains all user data (files).'); ?>
		<td style="padding: 5px; padding-top: 10px;" valign="top"><b><?php echo __('Configure modules'); ?></b><br>
		<?php echo __('This is where you set up BUGS 2 modules. A lot of modules are already installed and set up if you chose to do so during the installation.'); ?><br>
		<?php echo __('Additional modules can be added / installed from here.'); ?>
	</tr>
</table>