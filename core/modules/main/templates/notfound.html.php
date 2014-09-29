<div class="redbox" id="notfound_error">
        <div class="viewissue_info_header"><?php echo __("404 - Not Found"); ?></div>
        <div class="viewissue_info_content">
            <?php if (isset($message) && $message): ?>
                <?php echo $message; ?>
            <?php else: ?>
                <?php echo __("This location doesn't exist, has been deleted or you don't have permission to see it"); ?>
            <?php endif; ?>
        </div>
    </div>
</div>
