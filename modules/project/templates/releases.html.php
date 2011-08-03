<?php

	$tbg_response->addBreadcrumb(__('Releases'), null, tbg_get_breadcrumblinks('project_summary', $selected_project));
	$tbg_response->setTitle(__('"%project_name%" releases', array('%project_name%' => $selected_project->getName())));

	if (!$selected_project instanceof TBGProject) exit();
	
?>
			<?php include_template('project/projectheader', array('selected_project' => $selected_project)); ?>
			<?php include_template('project/projectinfosidebar', array('selected_project' => $selected_project)); ?>
			<h3><?php echo __('Active releases'); ?></h3>
			<?php if (count($active_builds)): ?>
				<ul class="simple_list">
				<?php foreach ($active_builds as $build): ?>
					<li class="rounded_box invisible" style="line-height: 1.3;">
						<div class="build_buttons" style="float: right; margin: 3px 3px 0 0;">
							<?php if ($build->hasDownload()): ?>
								<div class="button button-orange"><span><?php echo image_tag('icon_download.png').__('Download'); ?></span></div>
							<?php endif; ?>
						</div>
						<?php echo image_tag('icon_build.png', array('style' => 'float: left; margin-right: 5px;')); ?> <?php echo '<b style="font-size: 15px;">' . $build->getName() . '</b>&nbsp;&nbsp;<span class="faded_out">(' . $build->getVersion() . ')</span>'; ?><br>
						<span class="faded_out" style="font-size: 11px;"><?php echo __('Released %timestamp%', array('%timestamp%' => tbg_formatTime($build->getReleaseDate(), 7))); ?></span>
					</li>
				<?php endforeach; ?>
				</ul>
			<?php else: ?>
				<div class="faded_out"><?php echo __('There are no active releases for this project'); ?></div>
			<?php endif; ?>
			<h3 style="margin-top: 30px;"><?php echo __('Archived releases'); ?></h3>
			<?php if (count($archived_builds)): ?>
				<ul class="simple_list">
				<?php foreach ($archived_builds as $build): ?>
					<li class="rounded_box invisible">
						<div class="build_buttons" style="float: right; margin: 4px 4px 0 0;">
							<?php if ($build->hasDownload()): ?>
								<div class="button button-silver disabled" title="<?php echo __('This release is no longer available for download'); ?>"><span><?php echo __('Download'); ?></span></div>
							<?php endif; ?>
						</div>
						<?php echo image_tag('icon_build.png', array('style' => 'float: left; margin-right: 5px;')); ?> <?php echo $build->getName() . '&nbsp;&nbsp;<span class="faded_out">(' . $build->getVersion() . ')</span>'; ?><br>
						<span class="faded_out" style="font-size: 11px;"><?php echo __('Released %timestamp%', array('%timestamp%' => tbg_formatTime($build->getReleaseDate(), 7))); ?></span>
					</li>
				<?php endforeach; ?>
				</ul>
			<?php else: ?>
				<div class="faded_out"><?php echo __('There are no archived releases for this project'); ?></div>
			<?php endif; ?>
		</td>
	</tr>
</table>