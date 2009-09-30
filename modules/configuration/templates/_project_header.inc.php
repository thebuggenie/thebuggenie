<script type="text/javascript" src="<?php echo BUGScontext::getTBGPath(); ?>js/config/projects_ajax.js"></script>
<table style="width: 100%" cellpadding=0 cellspacing=0>
	<tr>
		<td style="padding-right: 10px;">
			<div class="configheader" style="width: 750px;"><?php echo __('Configure projects'); ?></div>
            <div style="height: 60px; position: absolute;">
            	<?php echo bugs_successStrip(__('The project has been added'), __('Access has been granted to your group. Remember to give other users/groups permission to access it via the admin section to the left, if necessary.'), 'message_project_added', true); ?>
            	<?php echo bugs_failureStrip('', '', 'message_failed', true); ?>
            	<?php echo bugs_successStrip(__('The edition has been added'), __('Access has been granted to your group. Remember to give other users/groups permission to access it via the admin section to the left, if necessary.'), 'message_edition_added', true); ?>
            	<?php echo bugs_successStrip(__('The component has been added'), '', 'message_component_added', true); ?>
            	<?php echo bugs_successStrip(__('The build has been added'), __('Remember to give other users/groups permission access to it if necessary.'), 'message_build_added', true, false); ?>
            	<?php echo bugs_successStrip(__('The build details has been updated'), '', 'message_build_details_updated', true); ?>
            	<?php echo bugs_successStrip(__('The selected build has been deleted'), '', 'message_build_deleted', true); ?>
            	<?php echo bugs_successStrip(__('The selected build has been added to open issues based on your selections'), '', 'message_build_added_to_open_issues', true); ?>
            	<?php echo bugs_successStrip(__('The selected build has been marked as &laquo;Released&raquo;'), '', 'message_build_release', true); ?>
            	<?php echo bugs_successStrip(__('The selected build has been marked as &laquo;Not released&raquo;'), '', 'message_build_retract', true); ?>
            	<?php echo bugs_successStrip(__('The selected build is now locked for new issue reports'), '', 'message_build_lock', true); ?>
            	<?php echo bugs_successStrip(__('The selected build is no longer locked for new issue reports'), '', 'message_build_unlock', true); ?>
            	<?php echo bugs_successStrip(__('The selected build is now the initial default when reporting new issues for this project'), '', 'message_build_markdefault', true); ?>
            	<?php echo bugs_successStrip(__('Your changes has been saved'), '', 'message_changes_saved', true); ?>
            </div>
			<p style="padding-top: 5px;">
				<?php if (BUGScontext::getRequest()->getParameter('edit_settings')): ?>
					<?php $help_topic = 'setup_project'; ?>
				<?php else: ?>
					<?php $help_topic = 'config_projects'; ?>
				<?php endif; ?>
				<?php echo __('More information about projects, editions, builds and components is available from the %bugs_online_help%.', array('%bugs_online_help%' => bugs_helpBrowserHelper($help_topic, __('The Bug Genie online help')))); ?>
			</p>
		</td>
	</tr>
</table>
<?php if ($mode > 1): ?>
	<div class="rounded_box" style="margin: 15px 0px 15px 0px; width: 700px;">
		<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
		<div class="xboxcontent" style="vertical-align: middle; padding: 5px 10px 5px 10px; font-size: 12px;">
		<?php echo __('You are now looking at %project_name%', array('%project_name%' => '<span id="project_name_span" style="font-weight: bold;">' . $theProject->getName() . '</span>')); ?><br>
		<b><?php echo link_tag(make_url('configure_projects'), '&lt;&lt;&nbsp;'.__('Go back to list of projects')); ?></b>
		</div>
		<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
	</div>
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