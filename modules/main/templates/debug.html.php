<?php if (is_array($tbg_summary)): ?>
	<style type="text/css">
		#log_messages, #scope_settings, #log_timing, #log_sql, #log_ajax { filter:alpha(opacity=90); -moz-opacity:0.9; -khtml-opacity: 0.9; opacity: 0.9; position: fixed; bottom: 24px; border: 1px solid #DDD; border-bottom: 0; width: 98%; margin: 5px auto; padding: 5px; background-color: #F5F5F5; height: 540px; left: 10px; color: #000; font-size: 12px; box-shadow: 0 -3px 3px -3px rgba(0, 0, 0, 0.2) inset; }
		#log_messages div.log, #scope_settings div.log, #log_timing div.log, #log_sql div.log { height: 500px; overflow: auto; font-size: 12px; text-align: left; }
		#scope_settings div.log table tr:hover td { background-color: #DDD; }
	</style>
	<div style="border-top: 1px dotted #CCC; width: 100%; padding: 3px; background-color: #F1F1F1; box-shadow: 0 -3px 3px rgba(0, 0, 0, 0.3);">
		<table style="width: 100%; border: 0;" cellpadding="0" cellspacing="0">
			<tr>
				<td style="width: 20px; padding: 3px; cursor: pointer;" onclick="$('tbg___DEBUGINFO___').hide();" title="Hide debug bar">
					<?php echo image_tag('action_delete.png'); ?>
				</td>
				<td style="width: 400px; padding: 3px; font-size: 11px; font-family: Ubuntu;">
					<?php echo image_tag('debug_route.png', array('style' => 'float: left; margin-right: 5px;')); ?>
					<b>Current route: </b>[<i><?php echo $tbg_summary['routing']['name']; ?></i>] <?php echo $tbg_summary['routing']['module']; ?> / <?php echo $tbg_summary['routing']['action']; ?>
				</td>
				<td style="width: 100px; cursor: pointer; padding: 3px; font-size: 11px; font-family: Ubuntu;" onclick="$('log_timing').toggle();" title="Click to toggle timing overview">
					<?php echo image_tag('debug_time.png', array('style' => 'float: left; margin-right: 5px;')); ?>
					<?php echo $tbg_summary['load_time']; ?>
				</td>
				<td style="width: 100px; cursor: pointer; padding: 3px; font-size: 11px; font-family: Ubuntu;" onclick="$('log_ajax').toggle();" title="Click to toggle ajax calls list">
					<?php echo image_tag('debug_ajax.png', array('style' => 'float: left; margin-right: 5px;')); ?>
					<span id="debug_ajax_count">1</span>
				</td>
				<td onclick="$('scope_settings').toggle();" style="width: 80px; padding: 3px; cursor: pointer; font-size: 11px; font-family: Ubuntu;" title="Generated hostname: <?php echo $tbg_summary['scope']['hostnames']; ?>">
					<?php echo image_tag('debug_scope.png', array('style' => 'float: left; margin-right: 5px;')); ?>
					<b>Scope: </b><?php echo $tbg_summary['scope']['id']; ?>
				</td>
				<td onclick="$('log_sql').toggle();" style="width: 200px; cursor: pointer; padding: 3px; font-size: 11px; font-family: Ubuntu;">
					<?php echo image_tag('debug_database.png', array('style' => 'float: left; margin-right: 5px;')); ?>
					<?php if (array_key_exists('db', $tbg_summary)): ?>
						<b><?php echo count($tbg_summary['db']['queries']); ?></b> database queries (<?php echo ($tbg_summary['db']['timing'] > 1) ? round($tbg_summary['db']['timing'], 2) . 's' : round($tbg_summary['db']['timing'] * 1000, 1) . 'ms'; ?>)
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
	<div id="scope_settings" style="display: none;">
		<div style="font-size: 16px; font-weight: bold; border-bottom: 1px solid #DDD; padding: 4px;">Scope settings</div>
		<div class="log">
			<?php foreach ($tbg_summary['settings'] as $module => $settings): ?>
				<h3><?php echo $module; ?></h3>
				<table style="border: 0;" cellpadding="0" cellspacing="0">
					<?php foreach ($settings as $setting => $setting_details): ?>
						<tr>
							<td style="font-size: 12px; padding: 1px 5px 1px 1px;"><b><?php echo $setting; ?>: </b></td>
							<td style="font-size: 12px;">
								<?php foreach ($setting_details as $uid => $setting): ?>
									<?php echo htmlspecialchars($setting); ?>&nbsp;<i style="color: #AAA;">(<?php echo (!$uid) ? 'default' : "uid {$uid}"; ?>)</i><br>
								<?php endforeach; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</table>
			<?php endforeach; ?>
		</div>
	</div>
	<div id="log_ajax" style="display: none;">
		<div style="font-size: 16px; font-weight: bold; border-bottom: 1px solid #DDD; padding: 4px;">Ajax calls</div>
		<ul class="simple_list hover_highlight" id="log_ajax_items" style="text-align: left;">
		</div>
	</div>
	<div id="log_timing" style="display: none;">
		<div style="font-size: 16px; font-weight: bold; border-bottom: 1px solid #DDD; padding: 4px;">Timing</div>
		<div class="log">
			<ul class="simple_list">
			<?php foreach ($tbg_summary['partials'] as $partial_visited => $details): ?>
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
			<?php foreach ($tbg_summary['db']['queries'] as $cc => $details): ?>
				<li>
					<b><?php echo $cc+1; ?>
					<span class="faded_out dark small">[<?php echo ($details['time'] >= 1) ? round($details['time'], 2) . ' seconds' : round($details['time'] * 1000, 1) . 'ms'; ?>]</span> </b> from <b><?php echo $details['filename']; ?>, line <?php echo $details['line']; ?></b>:<br>
					<span style="font-size: 12px;"><?php geshi_highlight($details['sql'], 'sql'); ?></span>
				</li>
			<?php endforeach; ?>
			</ol>
		</div>
	</div>
	<div id="log_messages" style="display: none;">
		<div style="font-size: 16px; font-weight: bold; border-bottom: 1px solid #DDD; padding: 4px;">Log messages</div>
		<div class="log">
		<?php foreach ($tbg_summary['log'] as $entry): ?>
			<?php $color = TBGLogging::getCategoryColor($entry['category']); ?>
			<div class="log_<?php echo $entry['category']; ?>"><strong><?php echo mb_strtoupper(TBGLogging::getLevelName($entry['level'])); ?></strong> <strong style="color: #<?php echo $color; ?>">[<?php echo $entry['category']; ?>]</strong> <span style="color: #555; font-size: 10px; font-style: italic;"><?php echo $entry['time']; ?></span>&nbsp;&nbsp;<?php echo $entry['message']; ?></div>
		<?php endforeach; ?>
		</div>
	</div>
<?php else: ?>
	No debug data
<?php endif; ?>
