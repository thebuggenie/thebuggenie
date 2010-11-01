				<?php
					
					TBGEvent::createNew('core', 'footer_begin')->trigger();
					TBGContext::ping();
				
				?>
				</td>
			</tr>
			<tr>
				<td class="footer_bar">
					<div class="footer_container">
						<table cellpadding=0 cellspacing=0 style="table-layout: auto; margin: 0 auto 0 auto;">
							<tr>
								<td style="width: auto;">
									<?php echo image_tag('footer_logo.png', array('alt' => $run_summary, 'title' => $run_summary, 'onclick' => (TBGLogging::isEnabled()) ? "\$('log_messages').toggle();" : '')); ?>
									<?php echo __('%thebuggenie%, <b>friendly</b> issue tracking since 2002', array('%thebuggenie%' => link_tag(make_url('about'), 'The Bug Genie'))); ?>.
									<?php echo __('Licensed under the MPL 1.1 only, read it at %link_to_MPL%', array('%link_to_MPL%' => '<a href="http://www.opensource.org/licenses/mozilla1.1.php">opensource.org</a>')); ?>
									<?php if (TBGContext::isProjectContext() && $tbg_user->canAccessConfigurationPage()): ?>
										| <b><?php echo link_tag(make_url('configure'), __('Configure The Bug Genie')); ?></b>
									<?php endif; ?>
								</td>
								<?php /*<td style="width: 100px; text-align: center;"><a href="http://validator.w3.org/check?uri=referer" class="image"><img src="<?php echo TBGContext::getTBGPath(); ?>valid-html401.png" alt="Valid HTML 4.01 Transitional" height="31" width="88"></a></td> */ ?>
							</tr>
						</table>
					</div>
					<?php
				
					TBGEvent::createNew('core', 'footer_end')->trigger();
				
					?>
				</td>
			</tr>
		</table>
		<?php if (TBGLogging::isEnabled()): ?>
			<div id="log_messages" style="display: none;">
				<div style="font-size: 16px; font-weight: bold; border-bottom: 1px solid #DDD; padding: 4px;">Log messages</div>
				<div style="height: 470px; overflow: auto;">
				<?php foreach (TBGContext::getI18n()->getMissingStrings() as $text => $t): ?>
					<?php TBGLogging::log('The text "' . $text . '" does not exist in list of translated strings, and was added automatically', 'i18n', TBGLogging::LEVEL_NOTICE); ?>
				<?php endforeach; ?>
				<?php foreach (TBGLogging::getEntries() as $entry): ?>
					<?php $color = TBGLogging::getCategoryColor($entry['category']); ?>
					<div class="log_<?php echo $entry['category']; ?>"><strong><?php echo strtoupper(TBGLogging::getLevelName($entry['level'])); ?></strong> <strong style="color: #<?php echo $color; ?>">[<?php echo $entry['category']; ?>]</strong> <span style="color: #555; font-size: 10px; font-style: italic;"><?php echo $entry['time']; ?></span>&nbsp;&nbsp;<?php echo $entry['message']; ?></div>
				<?php endforeach; ?>
				</div>
				<div style="border-top: 1px solid #BBB; padding-top: 5px; font-size: 13px; font-weight: bold;"><?php echo $run_summary; ?></div>
			</div>
		<?php endif; ?>
		<?php /*foreach (TBGContext::getI18n()->getMissingStrings() as $text => $t): ?>
			<?php echo '$strings[\'' . str_replace("'", "\'", $text) . '\'] = \'' . str_replace("'", "\'", $text) . "';\n"; ?>
		<?php endforeach; */?>
	</body>
</html>