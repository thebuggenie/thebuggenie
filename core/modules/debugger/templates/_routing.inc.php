<?php use thebuggenie\core\framework\Context; ?>
<li id="debug_routes">
    <h1>Routes (<?php echo count($tbg_routing->getRoutes()); ?>)</h1>
    <ul>
        <?php foreach ($tbg_routing->getRoutes() as $route_name => $route): ?>
            <?php list($route, $regexp, $names, $names_hash, $module, $action, $params, $csrf_enabled, $methods, $overridden) = $route; ?>
            <li <?php if ($routing['name'] == $route_name) echo 'class="selected"'; ?>>
                <span class="badge csrf <?php echo ($csrf_enabled) ? 'enabled' : ''; ?>">CSRF</span>
                <span class="badge routename"><?php echo $route_name; ?></span>
                <span class="badge url"><?php echo $route; ?></span>
                <span class="badge method">\thebuggenie\<?php echo (Context::isInternalModule($module)) ? "core\\" : ''; ?><span class="badge modulename"><?php echo $module; ?></span>\Actions::<?php echo $action; ?>()</span>
                <?php if ($overridden): ?>
                    <span class="badge csrf <?php echo ($csrf_enabled) ? 'enabled' : ''; ?>">Overridden</span>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
</li>
