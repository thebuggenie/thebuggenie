<div class="redbox" id="http400">
        <div class="viewissue_info_header"><?php echo __("400 - Bad Request"); ?></div>
        <div class="viewissue_info_content">
            <?php if (isset($message) && $message): ?>
                <?php echo $message; ?>
            <?php else: ?>
                <?php echo __("You have sent an invalid request."); ?>
            <?php endif; ?>
        </div>
    </div>
</div>
