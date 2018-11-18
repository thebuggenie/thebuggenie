<li id="log_timing">
    <h1>Timing</h1>
    <ul>
    <?php foreach ($partials as $partial_visited => $details): ?>
        <li>
            <span class="badge count"><?php echo $details['count']; ?></span><span class="badge timing"><?= fa_image_tag('clock'); ?><span><?php echo ($details['time'] >= 1) ? round($details['time'], 2) . ' s' : round($details['time'] * 1000, 1) . 'ms'; ?></span></span>
            <span class="partial"><?php echo $partial_visited; ?></span>
        </li>
    <?php endforeach; ?>
    </ul>
</li>
