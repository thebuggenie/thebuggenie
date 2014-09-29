<div class="redbox" id="notfound_error">
    <div class="viewissue_info_header"><?php echo __("403 - Forbidden"); ?></div>
    <div class="viewissue_info_content">
        <?php if (isset($message) && $message): ?>
            <?php echo $message; ?>
        <?php else: ?>
            <?php echo __("You are not allowed to access this page"); ?>
        <?php endif; ?>
    </div>
</div>
