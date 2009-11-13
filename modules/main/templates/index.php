<?php 

	$bugs_response->setTitle('Frontpage');

?>
<?php if ($bugs_user->getID() == 1 && count(BUGSproject::getAll()) == 1): ?>
	<?php include_component('main/hideableInfoBox', array('key' => 'index_single_project_mode', 'title' => __('Only using The Bug Genie to track issues for one project?'), 'content' => __("It looks likes you're only using The Bug Genie to track issues for one project. If you don't want to use this homepage, you can set The Bug Genie to <i>single project tracker mode</i>, which will automatically forward the frontpage to the project overview page.<br><br><i>Single project tracker mode</i> can be enabled from %configure_settings%.", array('%configure_settings%' => link_tag(make_url('configure_settings'), '<b>' . __('Configure &ndash;&gt; Settings') . '</b>'))))); ?>
<?php endif; ?>
<table style="margin-top: 0px; table-layout: fixed; width: 100%" cellpadding=0 cellspacing=0>
	<tr>
		<?php if ($showleftbar): ?>
			<td class="left_bar">
				<div class="left_menu">
					<div class="left_menu_header"><?php echo __('Menu'); ?></div>
					<?php if (count($links) > 0): ?>
						<ul>
							<?php foreach ($links as $link): ?>
								<?php if ($link['url'] == ''): ?>
									<li>&nbsp;</li>
								<?php else: ?>
									<li><a href="<?php echo $link['url']; ?>" title="<?php echo $link['url']; ?>"><?php echo $link['description']; ?></a></li>
								<?php endif; ?>
							<?php endforeach; ?>
						</ul>
					<?php else: ?>
						<div style="padding-left: 5px;" class="faded_medium"><?php echo __('There are no links in this menu'); ?></div>
					<?php endif; ?>
				</div>
				<?php
				
					BUGScontext::trigger('core', 'index_left_top');
					BUGScontext::trigger('core', 'index_left_middle');
					BUGScontext::trigger('core', 'index_left_bottom');
				
				?>
			</td>
		<?php endif; ?>
		<td class="main_area">
			<?php
			
				BUGScontext::trigger('core', 'index_right_top');
			
				BUGScontext::trigger('core', 'index_right_middle');
				BUGScontext::trigger('core', 'index_right_middle_top');
				
			?>
			<?php if (BUGSsettings::isProjectOverviewEnabled()): ?>
				<div class="project_overview">
					<div class="header"><?php echo __('Projects'); ?></div>
						<?php if (count(BUGSproject::getAll()) > 0): ?>
							<ul class="project_list">
							<?php foreach (BUGSproject::getAll() as $aProject): ?>
								<li><?php include_component('project/overview', array('project' => $aProject)); ?></li>
							<?php endforeach; ?>
							</ul>
						<?php else: ?>
							<p class="content"><?php echo __('There are no projects'); ?>.
								<?php if ($bugs_user->hasPermission("b2saveconfig", 10, "core")): ?>
									<br>
									<?php echo image_tag('cfg_icon_projects.png', array('style' => 'float: left; margin-right: 5px;')); ?>
									<b><?php echo link_tag(make_url('configure_projects'), __('Click here to go to project management'), array('target' => '_blank')); ?></b>
								<?php else: ?>
									<?php echo __('Projects can only be created by an administrator'); ?>.
								<?php endif; ?>
							</p>
						<?php endif; ?>
				</div>
			<?php endif; ?>
			<?php 
			
				BUGScontext::trigger('core', 'index_right_middle_bottom');
				BUGScontext::trigger('core', 'index_right_bottom');
			
			?>
		</td>
	</tr>
</table>