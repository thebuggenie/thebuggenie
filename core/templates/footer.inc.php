<footer>
	<table cellpadding=0 cellspacing=0 style="table-layout: auto; margin: 0 auto 0 auto;">
		<tr>
			<td style="width: auto;">
			
			
<?php // BEGIN MODIFY FOOTER ?>
	<a href="<?php echo TBGContext::getTBGPath(); ?>wiki/Privacy">Privacy</a> |
	<a href="<?php echo TBGContext::getTBGPath(); ?>wiki/TermsOfUse">Terms of Use</a> |
	Project Manager <a href="<?php echo TBGContext::getTBGPath(); ?>about">The Bug Genie</a> | 
	Forum <a href="http://vanillaforums.org/">Vanilla</a> | 
	Template <a href="http://helpgivethanks.org/apps/thebuggenie/wiki/WebsiteTemplate">HGT</a> | 
	Content Copyright (except where noted) <a href="http://creativecommons.org/licenses/by/4.0/">CC BY 4.0</a>
	<?php // if ($tbg_user->canAccessConfigurationPage()): ?>
	<?php // ADD THIS PHP TAG TO TURN OFF THIS LINE OF CODE | <b><?php echo link_tag(make_url('configure'), __('Configure The Bug Genie')); ?></b>
				<?php // endif; ?>
<?php // END MODIFY FOOTER ?>


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