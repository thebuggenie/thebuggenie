<li id="log_messages">
    <h1>
        Log messages
        <div class="log-selectors">
            <?php foreach(array("core", "main", "B2DB", "routing", "i18n", "cache", "search", "publish") as $category): ?>
            <span class="cat-<?php echo $category; ?>"><span class="badge catname selected" onclick="$(this).toggleClassName('selected');$('log_entries').select('.cat-<?php echo $category; ?>').each(Element.toggle);"><?php echo $category; ?></span></span>
            <?php endforeach; ?>
        </div>
    </h1>
    <ul id="log_entries">
    <?php foreach ($log as $entry): ?>
        <li class="cat-<?php echo $entry['category']; ?>">
            <span class="badge loglevel"><?php echo mb_strtoupper(\thebuggenie\core\framework\Logging::getLevelName($entry['level'])); ?></span>
            <span class="badge catname"><?php echo $entry['category']; ?></span>
            <span class="badge timing"><?php echo $entry['time']; ?></span>
            <span class="logmessage"><?php echo $entry['message']; ?></span>
        </li>
    <?php endforeach; ?>
    </ul>
</li>
