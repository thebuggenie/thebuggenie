<footer>
	<table cellpadding=0 cellspacing=0 style="table-layout: auto; margin: 0 auto 0 auto;">
		<tr>
			<td style="width: auto;">
<?php // BEGIN ADD CONTENT AND LINKS TO FOOTER ?>
	<?php echo __('%Privacy%', array('%Privacy%' => '<a href="/YOURBugGenieFolder/thebuggenie/wiki/Privacy"  target="_blank">Privacy </a>')); ?>|
		<?php echo __('%Terms of Use%', array('%Terms of Use%' => '<a href="/YOURBugGenieFolder/thebuggenie/wiki/TermsOfUse"  target="_blank">Terms of Use </a>')); ?>|
				<?php echo __('Project manager and wiki by %thebuggenie%', array('%thebuggenie%' => '<a href="/YOURBugGenieFolder/thebuggenie/about"  target="_blank">The Bug Genie</a>')); ?>.
				<?php echo __('Forum by %link_to_MPL%', array('%link_to_MPL%' => '<a href="http://vanillaforums.org/"  target="_blank">Vanilla</a>')); ?>.
				
				<?php echo __('Content copyright (except where noted) =
 %link_to_MPL%', array('%link_to_MPL%' => '<a href="http://creativecommons.org/licenses/by/4.0/"  target="_blank">CC BY 4.0</a>')); ?>.
<?php // END ADD CONTENT AND LINKS TO FOOTER ?> 
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