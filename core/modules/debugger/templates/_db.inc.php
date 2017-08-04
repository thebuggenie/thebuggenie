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
            <span class="badge timing"><?= fa_image_tag('clock-o'); ?><span><?php echo ($details['time'] >= 1) ? round($details['time'], 2) . 's' : round($details['time'] * 1000, 1) . 'ms'; ?></span></span>
            <span class="partial code"><?php echo $classname . $type . $function; ?>()</span>
            <span class="partial hidden"><?= fa_image_tag('file-text-o', ['class' => 'file-icon']); ?><span class="filename"><?php echo join(DS, $file_details).DS.'<b>'.$filename.'</b>' ?>:<?php echo $details['line']; ?></span></span>
            <span class="partial hidden expander" onclick="$(this).up().toggleClassName('expanded');"><?= fa_image_tag('plus-circle', ['class' => 'expand']); ?><?= fa_image_tag('minus-circle', ['class' => 'collapse']); ?></span>
            <ul class="backtrace">
                <li class="b2db-hidden-toggler" onclick="$(this).up().toggleClassName('b2db-hidden-visible');">...</li>
                <?php foreach ($details['backtrace'] as $trace): ?>
                    <?php
                        $file_details = explode(DS, $trace['file']);
                        $filename = array_pop($file_details);
                        $classname = (isset($trace['class'])) ? $trace['class'] : '';
                        $type = (isset($trace['type'])) ? $trace['type'] : '';
                        $function = (isset($trace['function'])) ? $trace['function'] : 'unknown';
                    ?>
                    <li class="<?php if (substr($classname, 0, 5) == 'b2db\\') echo 'b2db-hidden'; ?>">
                        <span class="partial code"><?php echo $classname . $type . $function; ?>()</span>
                        <span class="partial"><?= fa_image_tag('file-text-o', ['class' => 'file-icon']); ?><?php echo join(DS, $file_details).DS.'<b>'.$filename.'</b>' ?>:<?php echo $trace['line']; ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
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
            <span class="badge timing"><?= fa_image_tag('clock-o'); ?><span><?php echo ($details['time'] >= 1) ? round($details['time'], 2) . 's' : round($details['time'] * 1000, 1) . 'ms'; ?></span></span>
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
