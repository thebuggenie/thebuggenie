<li class="rounded_box invisible" style="line-height: 1.3;">
	<?php if ($build->isActive()): ?>
		<div class="build_buttons" style="float: right; margin: 6px 3px 0 0;">
			<?php if ($build->hasDownload()): ?>
				<?php echo ($build->hasFile()) ? link_tag(make_url('downloadfile', array('id' => $build->getFile()->getID())), image_tag('icon_download.png').__('Download'), array('class' => 'button button-orange')) : link_tag($build->getFileURL(), image_tag('icon_download.png').__('Download'), array('class' => 'button button-orange')); ?>
			<?php endif; ?>
			<?php echo javascript_link_tag(__('Report an issue'), array('onclick' => "TBG.Main.Helpers.Backdrop.show('".make_url('get_partial_for_backdrop', array('key' => 'reportissue', 'project_id' => $build->getProject()->getId(), 'build_id' => $build->getID()))."');", 'class' => 'button button-green')); ?>
		</div>
		<?php echo image_tag('icon_build.png', array('style' => 'float: left; margin: 3px 5px 0 0;')); ?> <?php echo '<b style="font-size: 15px;">' . $build->getName() . '</b>&nbsp;&nbsp;<span class="faded_out">(' . $build->getVersion() . ')</span>'; ?><br>
	<?php else: ?>
		<?php if ($build->hasDownload()): ?>
			<div class="build_buttons" style="float: right; margin: 4px 4px 0 0;">
				<?php if (!$build->isReleased()): ?>
					<div class="button button-silver disabled" title="<?php echo __('This release is no longer available for download'); ?>"><?php echo __('Download'); ?></div>
				<?php else: ?>
					<?php echo ($build->hasFile()) ? link_tag(make_url('downloadfile', array('id' => $build->getFile()->getID())), image_tag('icon_download.png').__('Download'), array('class' => 'button button-orange')) : link_tag($build->getFileURL(), image_tag('icon_download.png').__('Download'), array('class' => 'button button-orange')); ?>
				<?php endif; ?>
			</div>
		<?php endif; ?>
		<?php echo image_tag('icon_build.png', array('style' => 'float: left; margin: 2px 5px 0 0;')); ?> <?php echo $build->getName() . '&nbsp;&nbsp;<span class="faded_out">(' . $build->getVersion() . ')</span>'; ?><br>
	<?php endif; ?>
	<span class="faded_out" style="font-size: 11px;"><?php echo __('Released %timestamp%', array('%timestamp%' => tbg_formatTime($build->getReleaseDate(), 7))); ?></span>
</li>
