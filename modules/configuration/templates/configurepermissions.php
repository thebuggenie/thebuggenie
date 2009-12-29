<?php

	$bugs_response->setTitle(__('Configure permissions'));
	//$bugs_response->addJavascript('config/issuetypes.js');

?>
<table style="table-layout: fixed; width: 100%" cellpadding=0 cellspacing=0>
	<tr>
		<?php include_component('configleftmenu', array('selected_section' => 5)); ?>
		<td valign="top">
			<div style="width: 750px;" id="config_permissions">
				<div class="configheader"><?php echo __('Configure permissions'); ?></div>
				<div class="content"><?php echo __('Edit all global, group and team permissions from this page. User-specific permissions are handled from the user configuration page.'); ?></div>
				<div class="header_div" style="margin-top: 15px;"><?php echo __('General permissions'); ?></div>
				<ul style="width: 750px;">
					<?php foreach (BUGScontext::getAvailablePermissions('general') as $permission_key => $permission): ?>
						<li>
							<a href="javascript:void(0);" onclick="$('general_permission_<?php echo $permission_key; ?>_details').toggle();"><?php echo image_tag('cfg_icon_permissions.png', array('style' => 'float: right;')); ?><?php echo $permission['description']; ?></a>
							<div class="rounded_box white" style="margin: 5px 0 10px 0; display: none;" id="general_permission_<?php echo $permission_key; ?>_details" style="display: none;">
								<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
								<div class="xboxcontent" style="padding: 3px; font-size: 12px;">
									<div class="content">
										<?php include_component('configuration/permissionsinfo', array('key' => $permission_key, 'mode' => 'general', 'target_id' => 0, 'module' => 'core', 'access_level' => $access_level)); ?>
									</div>
								</div>
								<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
							</div>
						</li>
					<?php endforeach; ?>
				</ul>
				<div class="header_div" style="margin-top: 15px;"><?php echo __('Project-specific permissions'); ?></div>
				<?php if (count(BUGSproject::getAll()) > 0): ?>
					<ul>
						<?php foreach (BUGSproject::getAll() as $project): ?>
							<li>
								<a href="javascript:void(0);" onclick="$('project_permission_details_<?php echo $project->getID(); ?>').toggle();"><?php echo image_tag('icon_project_permissions.png', array('style' => 'float: right;')); ?><?php echo $project->getName(); ?> <span class="faded_medium smaller"><?php echo $project->getKey(); ?></span></a>
								<ul style="display: none;" id="project_permission_details_<?php echo $project->getID(); ?>">
									<?php foreach (BUGScontext::getAvailablePermissions('project') as $permission_key => $permission): ?>
										<li>
											<a href="javascript:void(0);" onclick="<?php if(array_key_exists('details', $permission)): ?>$('project_<?php echo $project->getID(); ?>_permission_<?php echo $permission_key; ?>_details').hide();<?php endif; ?>$('project_<?php echo $project->getID(); ?>_permission_<?php echo $permission_key; ?>_settings').toggle();" style="float: right;"><?php echo image_tag('cfg_icon_permissions.png'); ?></a>
											<?php if(array_key_exists('details', $permission)): ?>
												<a href="javascript:void(0);" onclick="$('project_<?php echo $project->getID(); ?>_permission_<?php echo $permission_key; ?>_settings').hide();$('project_<?php echo $project->getID(); ?>_permission_<?php echo $permission_key; ?>_details').toggle();" style="float: right; margin-right: 5px;" title="<?php echo __('More fine-tuned permissions are available. Click to see them.'); ?>"><?php echo image_tag('icon_project_permissions.png'); ?></a>
											<?php endif; ?>
											<a href="javascript:void(0);" onclick="$('project_<?php echo $project->getID(); ?>_permission_<?php echo $permission_key; ?>_settings').toggle();" style="display: block; width: 680px;"><?php echo $permission['description']; ?></a>
											<?php if(array_key_exists('details', $permission)): ?>
												<ul style="display: none;" id="project_<?php echo $project->getID(); ?>_permission_<?php echo $permission_key; ?>_details">
													<?php foreach ($permission['details'] as $detail_permission_key => $detail_permission): ?>
														<li>
															<a href="javascript:void(0);" onclick="$('project_<?php echo $project->getID(); ?>_permission_<?php echo $permission_key; ?>_<?php echo $detail_permission_key; ?>_settings').toggle();"><?php echo image_tag('cfg_icon_permissions.png', array('style' => 'float: right;')); ?><?php echo $detail_permission['description']; ?></a>
															<div class="rounded_box white" style="margin: 5px 5px 10px 0; display: none;" id="project_<?php echo $project->getID(); ?>_permission_<?php echo $permission_key; ?>_<?php echo $detail_permission_key; ?>_settings" style="display: none;">
																<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
																<div class="xboxcontent" style="padding: 3px; font-size: 12px;">
																	<div class="content">
																		<?php include_component('configuration/permissionsinfo', array('key' => $detail_permission_key, 'mode' => 'general', 'target_id' => $project->getID(), 'module' => 'core', 'access_level' => $access_level)); ?>
																	</div>
																</div>
																<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
															</div>
														</li>
													<?php endforeach; ?>
												</ul>
											<?php endif; ?>
											<div class="rounded_box white" style="margin: 5px 5px 10px 0; display: none;" id="project_<?php echo $project->getID(); ?>_permission_<?php echo $permission_key; ?>_settings" style="display: none;">
												<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
												<div class="xboxcontent" style="padding: 3px; font-size: 12px;">
													<div class="content">
														<?php include_component('configuration/permissionsinfo', array('key' => $permission_key, 'mode' => 'general', 'target_id' => $project->getID(), 'module' => 'core', 'access_level' => $access_level)); ?>
													</div>
												</div>
												<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
											</div>
										</li>
									<?php endforeach; ?>
								</ul>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php else: ?>
					<div class="faded_medium" style="padding: 2px;"><?php echo __('There are no projects'); ?></div>
				<?php endif; ?>
			</div>
		</td>
	</tr>
</table>