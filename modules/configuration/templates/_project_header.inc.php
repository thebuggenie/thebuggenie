<script type="text/javascript" src="<?php echo TBGContext::getTBGPath(); ?>js/config/projects_ajax.js"></script>
<table style="width: 100%" cellpadding=0 cellspacing=0>
	<tr>
		<td style="padding-right: 10px;">
			<div class="configheader" style="width: 750px;"><?php echo __('Configure projects'); ?></div>
			<p style="padding-top: 5px;">
				<?php if (TBGContext::getRequest()->getParameter('edit_settings')): ?>
					<?php $help_topic = 'setup_project'; ?>
				<?php else: ?>
					<?php $help_topic = 'config_projects'; ?>
				<?php endif; ?>
				<?php echo __('More information about projects, editions, builds and components is available from the %tbg_online_help%.', array('%tbg_online_help%' => tbg_helpBrowserHelper($help_topic, __('The Bug Genie online help')))); ?>
			</p>
		</td>
	</tr>
</table>
<?php if ($mode > 1): ?>
	<div class="rounded_box mediumgrey" style="margin: 15px 0px 15px 0px; width: 700px; vertical-align: middle; padding: 5px 10px 5px 10px; font-size: 12px;">
		<?php if (isset($theEdition) && $theEdition instanceof TBGEdition): ?>
			<?php echo __('You are now looking at %project_name% &gt;&gt; %edition_name%', array('%project_name%' => '<b>'.$theProject->getName().'</b>', '%edition_name%' => '<span id="edition_name_span" style="font-weight: bold;">'.$theEdition->getName().'</span>')); ?><br>
			<b><?php echo link_tag(make_url('configure_project_editions_components', array('project_id' => $theProject->getID())), '&lt;&lt;&nbsp;'.__('Go back to project editions and components overview')); ?></b><?php echo __('%something% or %something_else%', array('%something%' => '', '%something_else%' => '')); ?><br>
		<?php else: ?>
			<?php echo __('You are now looking at %project_name%', array('%project_name%' => '<span id="project_name_span" style="font-weight: bold;">' . $theProject->getName() . '</span>')); ?><br>
		<?php endif; ?>
		<b><?php echo link_tag(make_url('configure_projects'), '&lt;&lt;&nbsp;'.__('Go back to list of projects')); ?></b>
	</div>
	<?php if (!isset($hide_tabbar) || $hide_tabbar == false): ?>
		<div style="width: 750px; clear: both; height: 30px;" class="tab_menu">
			<ul>
				<li<?php if ($mode == 2): ?> class="selected"<?php endif; ?>><?php echo link_tag(make_url('configure_project_settings', array('project_id' => $theProject->getID())), image_tag('cfg_icon_projectsettings.png', array('style' => 'float: left;')).__('Information &amp; settings')); ?></li>
				<li<?php if ($mode == 3): ?> class="selected"<?php endif; ?>><?php echo link_tag(make_url('configure_project_editions_components', array('project_id' => $theProject->getID())), image_tag('cfg_icon_projecteditionsbuilds.png', array('style' => 'float: left;')).__('Editions, components &amp; releases')); ?></li>
				<li<?php if ($mode == 4): ?> class="selected"<?php endif; ?>><?php echo link_tag(make_url('configure_project_milestones', array('project_id' => $theProject->getID())), image_tag('icon_milestones.png', array('style' => 'float: left;')).__('Milestones')); ?></li>
				<li<?php if ($mode == 5): ?> class="selected"<?php endif; ?>><?php echo link_tag(make_url('configure_project_developers', array('project_id' => $theProject->getID())), image_tag('cfg_icon_project_devs.png', array('style' => 'float: left;')).__('Related users')); ?></li>
				<li<?php if ($mode == 6): ?> class="selected"<?php endif; ?>><?php echo link_tag(make_url('configure_project_other', array('project_id' => $theProject->getID())), image_tag('cfg_icon_datatypes.png', array('style' => 'float: left;')).__('Other')); ?></li>
			</ul>
		</div>
	<?php endif; ?>	
<?php endif; ?>