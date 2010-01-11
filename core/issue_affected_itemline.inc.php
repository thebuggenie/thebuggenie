<tr id="issue_affected_<?php echo (isset($anAffected['build'])) ? 'build' : 'component'; ?>_<?php echo $anAffected['a_id']; ?>_inline">
<td style="width: auto; padding: 4px; border-bottom: 1px solid #EEE;">
<table style="table-layout: fixed; width: 100%;" cellpadding=0 cellspacing=0>
<tr>
<td style="width: 20px;"><?php echo (isset($anAffected['build'])) ? image_tag('icon_build.png') : image_tag('icon_components.png'); ?></td>
<td><?php echo (isset($anAffected['build'])) ? $anAffected['build'] : $anAffected['component']; ?></td>
</tr>
</table>
</td>
<?php require TBGContext::getIncludePath() . 'include/issue_affected_status.inc.php'; ?>
</tr>