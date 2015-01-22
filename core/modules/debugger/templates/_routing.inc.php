<li id="debug_routes">
    <h1>Routes (<?php echo count($tbg_routing->getRoutes()); ?>)</h1>
    <div class="log">
        <table style="border: 0;" cellpadding="0" cellspacing="0">
        <?php foreach ($tbg_routing->getRoutes() as $route_name => $route): ?>
            <?php list($route, $regexp, $names, $names_hash, $module, $action, $params, $csrf_enabled) = $route; ?>
            <tr <?php if ($routing['name'] == $route_name) echo 'class="selected"'; ?>>
                <td><b><?php echo $route_name; ?>: </b></td>
                <td>
                    <b class="log_routing"><?php echo $route; ?></b>, <?php echo $module; ?>::<?php echo $action; ?>()
                    <div class="faded">
                        Auto-CSRF protection: <?php echo ($csrf_enabled) ? 'yes' : 'no'; ?>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        </table>
    </div>
</li>
