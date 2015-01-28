<li id="log_sql">
    <h1>Database calls</h1>
    <?php if (!\b2db\Core::isDebugMode()): ?>
        <div>Database debugging disabled</div>
    <?php endif; ?>
    <ol>
    <?php foreach ($db_summary['queries'] as $cc => $details): ?>
        <li>
            <?php 
                $file_details = explode(DS, $details['filename']);
                $filename = array_pop($file_details);
                $classname = (isset($details['class'])) ? $details['class'] : 'unknown';
                $type = (isset($details['type'])) ? $details['type'] : 'unknown';
                $function = (isset($details['function'])) ? $details['function'] : 'unknown';
            ?>
            <span class="badge timing"><?php echo ($details['time'] >= 1) ? round($details['time'], 2) . 's' : round($details['time'] * 1000, 1) . 'ms'; ?></span>
            <span class="partial"><?php echo $classname . $type . $function; ?>()</span>
            <span class="partial hidden"> in <?php echo join(DS, $file_details).DS.'<b>'.$filename.'</b>' ?>:<?php echo $details['line']; ?></span>
            <div class="sql"><?php geshi_highlight($details['sql'], 'sql'); ?></div>
        </li>
    <?php endforeach; ?>
    </ol>
</li>
<li id="log_objectpopulation">
    <h1>ORM object population</h1>
    <?php if (!\b2db\Core::isDebugMode()): ?>
        <div>Database debugging disabled</div>
    <?php endif; ?>
    <ol>
    <?php foreach ($db_summary['objectpopulation'] as $cc => $details): ?>
        <li>
            <?php
                $file_details = explode(DS, $details['filename']);
                $filename = array_pop($file_details);
                $classname = (isset($details['class'])) ? $details['class'] : 'unknown';
                $type = (isset($details['type'])) ? $details['type'] : 'unknown';
                $function = (isset($details['function'])) ? $details['function'] : 'unknown';
            ?>
            <span class="badge timing"><?php echo ($details['time'] >= 1) ? round($details['time'], 2) . 's' : round($details['time'] * 1000, 1) . 'ms'; ?></span>
            <span class="badge classcount"><?php echo $details['num_classes']; ?></span>
            <?php foreach ($details['classnames'] as $classname): ?>
                <span class="badge classname"><?php echo $classname; ?></span>
            <?php endforeach; ?>
            <span class="partial"><?php echo $classname . $type . $function; ?>()</span>
            <span class="partial hidden"> in <?php echo join(DS, $file_details).DS.'<b>'.$filename.'</b>' ?>:<?php echo $details['line']; ?></span>
        </li>
    <?php endforeach; ?>
    </ol>
</li>
