<?php if (is_array($tbg_summary)): ?>
    <style type="text/css">
        /* logging colors for categories */
        <?php foreach(array("main", "B2DB", "core", "routing", "i18n", "cache", "search", "publish") as $category): ?>
            .cat-<?php echo $category; ?> .badge.catname {
                background-color:#<?php echo \thebuggenie\core\framework\Logging::getCategoryColor($category); ?>;
                color: #FFF;
            }
        <?php endforeach; ?>
        .catname { text-shadow: none; text-transform: uppercase; }
        h1 .log-selectors { float: right; font-size: 0.7em; }
        h1 .log-selectors .badge { opacity: 0.2; cursor: pointer; }
        h1 .log-selectors .badge.selected { opacity: 1; }
        #log_timing ul, #log_ajax ul, #log_messages ul, #debug_routes ul, #log_sql ol { list-style: none; padding: 0; margin: 0; }
        #log_timing ul li, #log_ajax ul li, #log_messages ul li { font-size: 1.1em; list-style: none; padding: 2px; margin: 2px 0; clear: both; display: block; }
        #log_timing ul li:hover, #log_ajax ul li:hover, #log_messages ul li:hover, #debug_routes ul li:hover { background-color: rgba(230, 230, 230, 0.1); }
        #debug_routes ul li.selected { background-color: rgba(160, 230, 160, 0.2); }
        #debug_routes ul li.selected:hover { background-color: rgba(160, 230, 160, 0.4); }
        #log_sql li .sql { font-family: monospace; font-size: 1em; display: block; margin: 5px 0; padding: 5px; border: 1px dotted rgba(100, 100, 100, 0.1); background-color: rgba(200, 200, 200, 0.2); color: #888; text-shadow: none; }
        #debug-frames-container .partial, #debug-frames-container .logmessage, #debug-frames-container .badge.url, #debug-frames-container badge.method { display: inline-block; font-weight: normal; font-size: 1.1em; }
        #debug-frames-container .badge.url { text-align: left; }
        #debug-frames-container .partial.hidden { display: none; }
        #debug-frames-container li:hover > .partial.hidden { display: initial; }
        #debug-frames-container .badge { display: inline-block; font-weight: normal; border-radius: 3px; padding: 3px 5px; text-align: center; min-width: 30px; margin-right: 5px; text-shadow: none; }
        #debug-frames-container .badge.timing { background-color: rgba(200, 225, 200, 0.5); min-width: 55px; }
        #debug-frames-container .badge.csrf { background-color: rgba(200, 225, 200, 0.5); opacity: 0.2; }
        #debug-frames-container .badge.csrf.enabled { opacity: 1; }
        #debug-frames-container .badge.timestamp { background-color: rgba(255, 255, 255, 1); min-width: 90px; }
        #debug-frames-container .badge.count, #debug-frames-container .badge.loglevel { background-color: rgba(225, 225, 225, 0.5); }
        #debug-frames-container .badge.routename { background-color: rgba(225, 225, 225, 0.5); min-width: 200px; }
        #debug-frames-container .badge.classname { background-color: rgba(235, 235, 205, 0.5); min-width: 200px; }
        #debug-frames-container .badge.classcount { background-color: rgba(205, 205, 235, 0.5); min-width: 30px; }
        #debug-frames-container .badge.modulename { background-color: rgba(225, 225, 225, 0.5); margin: 0; }
        #debug-bar { cursor: pointer; text-align: left; border-top: 1px solid rgba(100, 100, 100, 0.2); width: 100%; padding: 0; background-color: #FAFAFA; z-index: 10000; box-shadow: 0 -3px 2px rgba(100, 100, 100, 0.2); font-size: 1.1em; list-style: none; margin: 0; height: 40px; }
        #debug-bar.enabled { position: fixed; top: 0; left: 0; border: 0; }
        #debug-bar > li { display: block; float: left; padding: 11px 20px; border-right: 1px solid rgba(100, 100, 100, 0.2); border-left: 1px solid rgba(255, 255, 255, 0.8); vertical-align: middle; }
        #debug-bar > li:first-child { border-left: none; }
        #debug-bar.enabled > li.selected { background-color: #FFF; box-shadow: 0 -4px 4px rgba(100, 100, 100, 0.3); }
        #debug-bar > li img { display: inline; margin-right: 5px; float: left; vertical-align: middle; }
        #debug-bar.enabled + #debug-frames-container { display: block; }
        #debug-bar .minimizer { display: none; }
        #debug-bar.enabled .minimizer { display: inline-block; cursor: pointer; float:right; }
        #debug-frames-container { display: none; width: 100%; height: calc(100% - 40px); box-sizing: border-box; padding: 0; margin: 0; position: fixed; left: 0; top: 40px; background: #FFF; }
        #debug-frames-container > li { display: none }
        #debug-frames-container > li.selected { display: block; text-align: left; position: absolute; height: 100%; width: 100%; left: 0; top: 0; right: 0; bottom: 0; box-sizing: border-box; padding: 5px; background: #FFF; margin: 0; overflow: auto; }
        #debug-frames-container > li h1 { font-size: 17px; font-weight: normal; color: #999; border: 1px solid rgba(100, 100, 100, 0.2); background-color: rgba(200, 200, 200, 0.1); box-shadow: inset 0 0 3px rgba(100, 100, 100, 0.1); padding: 5px; text-transform: uppercase; }
    </style>
    <ul class="" id="debug-bar" onclick="$(this).addClassName('enabled');">
        <li onclick="tbg_debug_show_menu_tab('debug_routes', $(this));">
            <?php echo image_tag('debug_route.png'); ?>
            [<i><?php echo $tbg_summary['routing']['name']; ?></i>] <?php echo $tbg_summary['routing']['module']; ?> / <?php echo $tbg_summary['routing']['action']; ?>
        </li>
        <li onclick="tbg_debug_show_menu_tab('log_timing', $(this));" title="Click to toggle timing overview">
            <?php echo image_tag('debug_time.png'); ?>
            <?php echo $tbg_summary['load_time']; ?> /
            <?php echo round($tbg_summary['memory'] / 1000000, 2); ?>MiB
        </li>
        <li onclick="tbg_debug_show_menu_tab('log_ajax', $(this));" title="Click to toggle ajax calls list">
            <?php echo image_tag('debug_ajax.png'); ?>
            <span id="debug_ajax_count">1</span>
        </li>
        <li onclick="tbg_debug_show_menu_tab('scope_settings', $(this));"  title="Generated hostname: <?php echo $tbg_summary['scope']['hostnames']; ?>">
            <?php echo image_tag('debug_scope.png'); ?>
            <b>Scope: </b><?php echo $tbg_summary['scope']['id']; ?>
        </li>
        <?php if (array_key_exists('db', $tbg_summary)): ?>
            <li onclick="tbg_debug_show_menu_tab('log_sql', $(this));" title="Database queries">
                <?php echo image_tag('debug_database.png'); ?>
                <b><?php echo count($tbg_summary['db']['queries']); ?></b> (<?php echo ($tbg_summary['db']['timing'] > 1) ? round($tbg_summary['db']['timing'], 2) . 's' : round($tbg_summary['db']['timing'] * 1000, 1) . 'ms'; ?>)
            </li>
            <li onclick="tbg_debug_show_menu_tab('log_objectpopulation', $(this));" title="Database object population">
                <?php echo image_tag('debug_population.png'); ?>
                <b><?php echo $tbg_summary['db']['objectcount']; ?></b> (<?php echo ($tbg_summary['db']['objecttiming'] > 1) ? round($tbg_summary['db']['objecttiming'], 2) . 's' : round($tbg_summary['db']['objecttiming'] * 1000, 1) . 'ms'; ?>)
            </li>
        <?php else: ?>
            <li title="Database queries">
                <span class="faded_out">No database queries</span>
            </li>
        <?php endif; ?>
        <li onclick="tbg_debug_show_menu_tab('log_messages', $(this));" style="cursor: pointer;">
            <?php echo image_tag('debug_log.png'); ?>
            Log
        </li>
        <li onclick="setTimeout(function() { $('debug-bar').removeClassName('enabled'); }, 150);" title="Minimize" class="minimizer">
            <?php echo image_tag('tabmenu_dropdown.png'); ?>
        </li>
    </ul>
    <ul id="debug-frames-container">
        <?php include_component('debugger/scope', array('scope_settings' => $tbg_summary['settings'])); ?>
        <?php include_component('debugger/ajaxlogger'); ?>
        <?php include_component('debugger/timings', array('partials' => $tbg_summary['partials'])); ?>
        <?php include_component('debugger/routing', array('routing' => $tbg_summary['routing'])); ?>
        <?php include_component('debugger/db', array('db_summary' => $tbg_summary['db'])); ?>
        <?php include_component('debugger/log', array('log' => $tbg_summary['log'])); ?>
    </ul>
<?php else: ?>
    No debug data
<?php endif; ?>
