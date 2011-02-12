				<?php TBGEvent::createNew('core', 'footer_begin')->trigger(); ?>
				</td>
			</tr>
			<tr>
				<td class="footer_bar">
					<div class="footer_container">
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
								<?php /*<td style="width: 100px; text-align: center;"><a href="http://validator.w3.org/check?uri=referer" class="image"><img src="<?php echo TBGContext::getTBGPath(); ?>valid-html401.png" alt="Valid HTML 4.01 Transitional" height="31" width="88"></a></td> */ ?>
							</tr>
						</table>
						<?php if (TBGLogging::isEnabled() && TBGContext::isDebugMode()): ?>
							<div style="border-top: 1px dotted #CCC;">
								<table style="width: 100%; border: 0;" cellpadding="0" cellspacing="0">
									<tr>
										<td style="width: 400px; padding: 3px; font-size: 11px; font-family: Ubuntu;">
											<?php echo image_tag('debug_route.png', array('style' => 'float: left; margin-right: 5px;')); ?>
											<b>Current route: </b>[<i><?php echo TBGContext::getRouting()->getCurrentRouteName(); ?></i>] <?php echo TBGContext::getRouting()->getCurrentRouteModule(); ?> / <?php echo TBGContext::getRouting()->getCurrentRouteAction() ?>
										</td>
										<td style="width: 100px; cursor: pointer; padding: 3px; font-size: 11px; font-family: Ubuntu;" onclick="$('log_timing').toggle();" title="Click to toggle timing overview">
											<?php echo image_tag('debug_time.png', array('style' => 'float: left; margin-right: 5px;')); ?>
											<?php echo $tbg_summary['load_time']; ?>
										</td>
										<td style="width: 80px; padding: 3px; font-size: 11px; font-family: Ubuntu;">
											<?php echo image_tag('debug_scope.png', array('style' => 'float: left; margin-right: 5px;')); ?>
											<b>Scope: </b><?php echo $tbg_summary['scope_id']; ?>
										</td>
										<td onclick="$('log_sql').toggle();" style="width: 200px; cursor: pointer; padding: 3px; font-size: 11px; font-family: Ubuntu;">
											<?php echo image_tag('debug_database.png', array('style' => 'float: left; margin-right: 5px;')); ?>
											<?php if (array_key_exists('db_queries', $tbg_summary)): ?>
												<b><?php echo count($tbg_summary['db_queries']); ?></b> database queries (<?php echo ($tbg_summary['db_timing'] > 1) ? round($tbg_summary['db_timing'], 2) . 's' : round($tbg_summary['db_timing'] * 1000, 1) . 'ms'; ?>)
											<?php else: ?>
												<span class="faded_out">No database queries</span>
											<?php endif; ?>
										</td>
										<td style="padding: 3px; font-size: 11px; font-family: Ubuntu; text-align: right;">
											<span onclick="$('log_messages').toggle();" style="cursor: pointer;">
												Toggle log messages
												<?php echo image_tag('debug_log.png', array('style' => 'float: right; margin-left: 5px; cursor: pointer;')); ?>
											</span>
										</td>
									</tr>
								</table>
							</div>
						<?php endif; ?>
					</div>
					<?php TBGEvent::createNew('core', 'footer_end')->trigger(); ?>
				</td>
			</tr>
		</table>
		<?php if (TBGLogging::isEnabled() && TBGContext::isDebugMode()): ?>
			<div id="log_timing" style="display: none;">
				<div style="font-size: 16px; font-weight: bold; border-bottom: 1px solid #DDD; padding: 4px;">Timing</div>
				<div class="log">
					<ul class="simple_list">
					<?php foreach (TBGContext::getVisitedPartials() as $partial_visited => $details): ?>
						<li>
							<b><?php echo $partial_visited; ?>: </b>
							<span class="faded_out dark">Visited <?php echo $details['count']; ?>time(s), totalling <?php echo ($details['time'] >= 1) ? round($details['time'], 2) . ' seconds' : round($details['time'] * 1000, 1) . 'ms'; ?></span>
						</li>
					<?php endforeach; ?>
					</ul>
				</div>
			</div>
			<div id="log_sql" style="display: none;">
				<div style="font-size: 16px; font-weight: bold; border-bottom: 1px solid #DDD; padding: 4px;">Database calls</div>
				<div class="log">
					<ol class="simple_list">
					<?php $cc = 1; ?>
					<?php foreach (B2DB::getSQLHits() as $details): ?>
						<li>
							<b><?php echo $cc++; ?>
							<span class="faded_out dark small">[<?php echo ($details['time'] >= 1) ? round($details['time'], 2) . ' seconds' : round($details['time'] * 1000, 1) . 'ms'; ?>]</span> </b>
							<?php echo $details['sql']; ?>
						</li>
					<?php endforeach; ?>
					</ol>
				</div>
			</div>
			<div id="log_messages" style="display: none;">
				<div style="font-size: 16px; font-weight: bold; border-bottom: 1px solid #DDD; padding: 4px;">Log messages</div>
				<div class="log">
				<?php foreach (TBGContext::getI18n()->getMissingStrings() as $text => $t): ?>
					<?php TBGLogging::log('The text "' . $text . '" does not exist in list of translated strings, and was added automatically', 'i18n', TBGLogging::LEVEL_NOTICE); ?>
				<?php endforeach; ?>
				<?php foreach (TBGLogging::getEntries() as $entry): ?>
					<?php $color = TBGLogging::getCategoryColor($entry['category']); ?>
					<div class="log_<?php echo $entry['category']; ?>"><strong><?php echo strtoupper(TBGLogging::getLevelName($entry['level'])); ?></strong> <strong style="color: #<?php echo $color; ?>">[<?php echo $entry['category']; ?>]</strong> <span style="color: #555; font-size: 10px; font-style: italic;"><?php echo $entry['time']; ?></span>&nbsp;&nbsp;<?php echo $entry['message']; ?></div>
				<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>
		<?php /*foreach (TBGContext::getI18n()->getMissingStrings() as $text => $t): ?>
			<?php echo '$strings[\'' . str_replace("'", "\'", $text) . '\'] = \'' . str_replace("'", "\'", $text) . "';\n"; ?>
		<?php endforeach; */?>
		<script type="text/javascript">
			var containerResize = function ()
			{
				if ($('fullpage_backdrop').visible())
				{
					var docheight = document.viewport.getHeight();
					var backdropheight = $('backdrop_detail_content').getHeight();
					if (backdropheight > (docheight - 100))
					{
						$('backdrop_detail_content').setStyle({height: docheight - 100 + 'px', overflow: 'scroll'});
					}
					else
					{
						$('backdrop_detail_content').setStyle({height: 'auto', overflow: ''});
					}
				}
			}

			Event.observe(window, 'resize', containerResize);
		</script>		
	</body>
</html>