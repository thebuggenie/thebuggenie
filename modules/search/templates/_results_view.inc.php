<div class="header">
	<?php if ($rss == true): ?>
		<?php echo link_tag(make_url('search', array_merge($parameters, array('format' => 'rss'))), image_tag('icon_rss.png'), array('title' => __('Download feed'), 'style' => 'float: right; margin-left: 5px;', 'class' => 'image')); ?>
	<?php endif; ?>
		<?php echo image_tag('collapse_small.png', array('id' => 'dashboard_'.$id.'_collapse', 'style' => 'float: left; margin: 3px 5px 0 2px;', 'onclick' => "\$('dashboard_{$id}').toggle(); this.src = (this.src == '" . image_url('collapse_small.png', false, 'core', false) . "') ? '" . image_url('expand_small.png', false, 'core', false) . "' : '" . image_url('collapse_small.png', false, 'core', false) . "'")); ?>
		<?php //echo image_tag('collapse.png', array('id' => 'dashboard_'.$id.'_collapse', 'onclick' => "$('dashboard_{$id}').toggle(); this.src = (this.src == '" . image_url('collapse.png', false, 'core', false) . "') ? '" . image_url('expand.png', false, 'core', false) . "' : '" . image_url('collapse.png', false, 'core', false) . "'")); ?>
		<?php echo __($title); ?>
</div>
<div id="dashboard_<?php echo $id; ?>">
	<?php if (count($issues) > 0): ?>
		<table cellpadding=0 cellspacing=0 style="margin: 5px;">
		<?php foreach ($issues as $theIssue): ?>
			<tr class="<?php if ($theIssue->getState() == TBGIssue::STATE_CLOSED): ?>issue_closed<?php else: ?>issue_open<?php endif; ?> <?php if ($theIssue->isBlocking()): ?>issue_blocking<?php endif; ?>">
				<td class="imgtd"><?php echo image_tag($theIssue->getIssueType()->getIcon() . '_tiny.png'); ?></td>
				<td>
					<span class="faded_out smaller"><?php echo link_tag(make_url('project_dashboard', array('project_key' => $theIssue->getProject()->getKey())), '['.$theIssue->getProject()->getKey().']'); ?></span>
					<?php echo link_tag(make_url('viewissue', array('project_key' => $theIssue->getProject()->getKey(), 'issue_no' => $theIssue->getFormattedIssueNo())), $theIssue->getFormattedTitle(true)); ?>
				</td>
			</tr>
			<tr>
				<td colspan="2" class="faded_out" style="padding-bottom: 15px;">
					<?php echo __('<strong>%status%</strong>, updated %updated_at%', array('%status%' => (($theIssue->getStatus() instanceof TBGDatatype) ? $theIssue->getStatus()->getName() : __('Status not determined')), '%updated_at%' => tbg_formatTime($theIssue->getLastUpdatedTime(), 12))); ?>
				</td>
			</tr>
		<?php endforeach; ?>
		</table>
	<?php else: ?>
		<div class="faded_out" style="padding: 5px 5px 10px 5px;"><?php echo __($default_message); ?></div>
	<?php endif; ?>
</div>