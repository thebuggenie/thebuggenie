<?php foreach ($structure['dirs'] as $foldername => $directory): ?>
    <?php include_component('livelink/directory', ['basepath' => $foldername, 'foldername' => $foldername, 'directory' => $directory, 'structure' => $structure]); ?>
<?php endforeach; ?>
<script>
    require(['domReady', 'thebuggenie/tbg', 'jquery', 'jquery.nanoscroller'], function (domReady, tbgjs, jquery, nanoscroller) {
        domReady(function () {
            jquery('body').on('click', '.foldername', function (e) {
                jquery(this).toggleClass('collapsed');
            });
        });
    });
</script>