<footer>
	<table cellpadding=0 cellspacing=0 style="table-layout: auto; margin: 0 auto 0 auto;">
		<tr>
			<td style="width: auto;">
				<?php echo image_tag('footer_logo.png'); ?>
				<?php echo __('%thebuggenie%, <b>friendly</b> issue tracking since 2002', array('%thebuggenie%' => link_tag(make_url('about'), 'The Bug Genie'))); ?>.
				<?php echo __('Licensed under the MPL 1.1 only, read it at %link_to_MPL%', array('%link_to_MPL%' => '<a href="http://www.opensource.org/licenses/mozilla1.1.php">opensource.org</a>')); ?>
				<?php if ($tbg_user->canAccessConfigurationPage()): ?>
					| <b><?php echo link_tag(make_url('configure'), __('Configure The Bug Genie')); ?></b>
				<?php endif; ?>
			</td>
		</tr>
	</table>
	<?php if (TBGContext::isDebugMode() && TBGLogging::isEnabled()): ?>
		<div id="tbg___DEBUGINFO___" style="position: fixed; bottom: 0; left: 0; z-index: 100; display: none; width: 100%;">
		</div>
		<?php echo image_tag('spinning_16.gif', array('style' => 'position: fixed; bottom: 5px; right: 23px;', 'id' => 'tbg___DEBUGINFO___indicator')); ?>
		<?php echo image_tag('debug_show.png', array('style' => 'position: fixed; bottom: 5px; right: 3px; cursor: pointer;', 'onclick' => "$('tbg___DEBUGINFO___').toggle();", 'title' => 'Show debug bar')); ?>
	<?php endif; ?>
</footer>