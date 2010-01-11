<div class="rounded_box iceblue_borderless" id="viewissue_left_box_top">
	<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
	<div class="xboxcontent" style="padding: 5px;">
		<div id="viewissue_left_box_issuetype">
			<?php if ($theIssue->getIssueType() instanceof TBGDatatype): ?>
				<table cellpadding=0 cellspacing=0>
					<tr>
						<td style="width: 24px; padding: 2px 0 0 0;"><?php echo image_tag($theIssue->getIssueType()->getIcon() . '_small.png'); ?></td>
						<td style="padding: 0 0 0 5px;"><?php echo $theIssue->getIssueType()->getName(); ?></td>
					</tr>
				</table>
			<?php else: ?>
				<div class="faded_medium"><?php echo __('Unknown issue type'); ?></div>
			<?php endif; ?>
		</div>
		<div id="viewissue_left_box_status" <?php if (!$theIssue->getStatus() instanceof TBGDatatype): ?>class="faded_medium"<?php endif; ?>>
			<?php if ($theIssue->getStatus() instanceof TBGDatatype): ?>
				<table style="table-layout: auto; width: auto;" cellpadding=0 cellspacing=0>
					<tr>
						<td style="width: 24px;"><div style="border: 1px solid #AAA; background-color: <?php echo ($theIssue->getStatus() instanceof TBGDatatype) ? $theIssue->getStatus()->getColor() : '#FFF'; ?>; font-size: 1px; width: 20px; height: 15px; margin-right: 2px;">&nbsp;</div></td>
						<td style="padding-left: 5px;"><?php echo $theIssue->getStatus()->getName(); ?></td>
					</tr>
				</table>
			<?php else: ?>
				<?php echo __('Status not determined'); ?>
			<?php endif; ?>
		</div>
		<dl class="viewissue_list">
			<dt style="vertical-align: middle;"><?php echo __('Assigned to'); ?></dt>
			<dd>
				<?php echo image_tag('action_dropdown_small.png', array('class' => 'dropdown')); ?>
				<?php if ($theIssue->isAssigned()): ?>
					<table style="width: 200px; display: inline;" cellpadding=0 cellspacing=0>
						<?php if ($theIssue->getAssigneeType() == TBGIdentifiableClass::TYPE_USER): ?>
							<?php echo include_component('main/userdropdown', array('user' => $theIssue->getAssignee())); ?>
						<?php else: ?>
							<?php echo include_component('main/teamdropdown', array('user' => $theIssue->getAssignee())); ?>
						<?php endif; ?>
					</table>
				<?php else: ?>
					<span class="faded_medium"><?php echo __('Not assigned to anyone'); ?></span>
				<?php endif; ?>
			</dd>
			<?php if ($theIssue->isPercentCompleteVisible()): ?>
				<dt id="percent_header"<?php if ($theIssue->isPercentCompleteChanged()): ?> class="issue_detail_changed"<?php endif; ?>><?php echo __('Progress'); ?></dt>
				<dd id="percent_content"<?php if ($theIssue->isPercentCompleteChanged()): ?> class="issue_detail_changed"<?php endif; ?>>
					<div style="width: 210px;">
						<?php echo image_tag('spinning_16.gif', array('class' => 'spinning', 'id' => 'percent_spinning', 'style' => 'display: none;')); ?>
						<?php if (!$theIssue->canEditPercentage()): ?>
							<table style="table-layout: fixed; width: 180px;" cellpadding=0 cellspacing=0>
								<tr id="percentage_tds">
									<?php for ($cc = 0; $cc <= 100; $cc++): ?>
										<td class="<?php if ($theIssue->getPercentCompleted() <= $cc): ?>percent_unfilled<?php else: ?>percent_filled<?php endif; ?>" style="font-size: 1px; width: 1%; height: 14px;">
											<a href="javascript:void(0);" onclick="setPercentage('<?php echo make_url('issue_setpercent', array('issue_id' => $theIssue->getID(), 'percent' => $cc)); ?>');" title="<?php echo __('Set to %pct% percent', array('%pct%' => $cc)); ?>">&nbsp;</a>
										</td>
									<?php endfor; ?>
								</tr>
							</table>
						<?php else: ?>
							<?php include_template('main/percentbar', array('percent' => $theIssue->getPercentCompleted(), 'height' => 14)); ?>
						<?php endif; ?>
					</div>
				</dd>
			<?php endif; ?>
		</dl>
		<?php if ($theIssue->getProject()->isVotesEnabled()): ?>
			<?php //TODO: Add a vote counter, and a "plus" button? ?>
			<?php if ($bugs_user->canVoteOnIssuesForProduct($theIssue->getProject()->getID()) && $bugs_user->canVoteForIssue($theIssue->getID())): /* ?>
				<div style="border-bottom: 1px solid #DDD; padding: 3px; font-size: 12px; margin-top: 5px;">
					<b>VOTE!</b>
				</div>
			<?php */ endif; ?>
		<?php endif; ?>
		
		<div style="clear: both;"> </div>
	</div>
</div>
<div class="rounded_box mediumgrey_borderless" style="margin: 0;" id="viewissue_left_box_bottom">
	<div class="xboxcontent" style="vertical-align: middle; padding: 0 10px 0 5px;">
		<dl class="viewissue_list">
			<?php if ($theIssue->getProject()->isEditionsEnabled() && $theIssue->isEditionsVisible()): ?>
				<dt><?php echo __('Edition(s)'); ?></dt>
				<dd>
					<?php echo image_tag('action_dropdown_small.png', array('class' => 'dropdown')); ?>
					<?php if (count($theIssue->getEditions()) > 0): ?>
						<ul>
							<?php foreach ($theIssue->getEditions() as $edition): ?>
								<li id="issue_affected_edition_<?php $edition->getID(); ?>_inline"><?php $edition->getName(); ?></li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</dd>
			<?php endif; ?>
			<dt style="vertical-align: middle;"><?php echo __('Posted by'); ?></dt>
			<dd>
				<table style="width: 200px;" cellpadding=0 cellspacing=0>
					<?php echo include_component('main/userdropdown', array('user' => $theIssue->getPostedBy())); ?>
				</table>
			</dd>
			<dt style="vertical-align: middle;"><?php echo __('Owned by'); ?></dt>
			<dd>
				<?php echo image_tag('action_dropdown_small.png', array('class' => 'dropdown')); ?>
				<?php if ($theIssue->isOwned()): ?>
					<table style="width: 200px; display: inline;" cellpadding=0 cellspacing=0>
						<?php if ($theIssue->getOwnerType() == TBGIdentifiableClass::TYPE_USER): ?>
							<?php echo include_component('main/userdropdown', array('user' => $theIssue->getOwner())); ?>
						<?php else: ?>
							<?php echo include_component('main/teamdropdown', array('user' => $theIssue->getOwner())); ?>
						<?php endif; ?>
					</table>
				<?php else: ?>
					<span class="faded_medium"><?php echo __('Not owned by anyone'); ?></span>
				<?php endif; ?>
			</dd>
			<?php if ($theIssue->isCategoryVisible()): ?>
					<dt id="category_header"<?php if ($theIssue->isCategoryChanged()): ?> class="issue_detail_changed"<?php endif; ?>>
						<?php echo __('Category'); ?>
					</dt>
					<dd id="category_content"<?php if ($theIssue->isCategoryChanged()): ?> class="issue_detail_changed"<?php endif; ?>>
						<a href="javascript:void(0);" onclick="$('category_change').toggle();" title="<?php echo __('Click to change category'); ?>"><?php echo image_tag('action_dropdown_small.png', array('class' => 'dropdown')); ?></a>
						<span id="category_name"<?php if (!$theIssue->getCategory() instanceof TBGDatatype): ?> style="display: none;"<?php endif; ?>><?php if ($theIssue->getCategory() instanceof TBGDatatype) echo $theIssue->getCategory()->getName(); ?></span>
						<span class="faded_medium" id="no_category"<?php if ($theIssue->getCategory() instanceof TBGDatatype): ?> style="display: none;"<?php endif; ?>><?php echo __('Not determined'); ?></span>
					</dd>
				</dl>
				<div style="clear: both;"> </div>
				<div class="rounded_box white" id="category_change" style="display: none; clear: both; width: 315px; margin: 5px 0 5px 0;">
					<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
					<div class="xboxcontent" style="padding: 5px;">
						<div class="dropdown_header"><?php echo __('Change category'); ?></div>
						<div class="dropdown_content">
							<a href="javascript:void(0);" onclick="setCategory('<?php echo make_url('issue_setcategory', array('issue_id' => $theIssue->getID(), 'category_id' => 0)); ?>');"><?php echo __('Clear the category'); ?></a><br>
						</div>
						<div class="dropdown_content">
							<?php echo __('%clear_the_category% or click to select a new category', array('%clear_the_category%' => '')); ?>:<br>
							<ul>
								<?php foreach (TBGDatatype::getCategories() as $category): ?>
									<li>
										<a href="javascript:void(0);" onclick="setCategory('<?php echo make_url('issue_setcategory', array('issue_id' => $theIssue->getID(), 'category_id' => $category->getID())); ?>');"><?php echo $category->getName(); ?></a>
									</li>
								<?php endforeach; ?>
							</ul>
							<div id="category_spinning" style="margin-top: 3px; display: none;"><?php echo image_tag('spinning_20.gif', array('style' => 'float: left; margin-right: 5px;')) . '&nbsp;' . __('Please wait'); ?>...</div>
						</div>
						<div id="category_change_error" class="error_message" style="display: none;"></div>
					</div>
					<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
				</div>
				<dl class="viewissue_list">
			<?php endif; ?>
			<?php if ($theIssue->isResolutionVisible()): ?>
				<dt><?php echo __('Resolution'); ?></dt>
				<dd>
					<?php echo image_tag('action_dropdown_small.png', array('class' => 'dropdown')); ?>
					<?php if ($theIssue->getResolution() instanceof TBGDatatype): ?>
						<?php echo $theIssue->getResolution()->getName(); ?>
					<?php else: ?>
						<span class="faded_medium"><?php echo __('Not determined'); ?></span>
					<?php endif; ?>
				</dd>
			<?php endif; ?>
			<?php if ($theIssue->isReproducabilityVisible()): ?>
				<dt><?php echo __('Reproducability'); ?></dt>
				<dd>
					<?php echo image_tag('action_dropdown_small.png', array('class' => 'dropdown')); ?>
					<?php if ($theIssue->getReproducability() instanceof TBGDatatype): ?>
						<?php echo $theIssue->getReproducability()->getName(); ?>
					<?php else: ?>
						<span class="faded_medium"><?php echo __('Not determined'); ?></span>
					<?php endif; ?>
				</dd>
			<?php endif; ?>
			<?php if ($theIssue->isSeverityVisible()): ?>
				<dt><?php echo __('Severity'); ?></dt>
				<dd>
					<?php echo image_tag('action_dropdown_small.png', array('class' => 'dropdown')); ?>
					<?php if ($theIssue->getSeverity() instanceof TBGDatatype): ?>
						<?php echo $theIssue->getSeverity()->getName(); ?>
					<?php else: ?>
						<span class="faded_medium"><?php echo __('Not determined'); ?></span>
					<?php endif; ?>
				</dd>
			<?php endif; ?>
			<?php if ($theIssue->isPriorityVisible()): ?>
				<dt><?php echo __('Priority'); ?></dt>
				<dd>
					<?php echo image_tag('action_dropdown_small.png', array('class' => 'dropdown')); ?>
					<?php if ($theIssue->getPriority() instanceof TBGDatatype): ?>
						<?php echo $theIssue->getPriority()->getName(); ?>
					<?php else: ?>
						<span class="faded_medium"><?php echo __('Not determined'); ?></span>
					<?php endif; ?>
				</dd>
			<?php endif; ?>
			<?php if ($theIssue->isEstimatedTimeVisible()): ?>
				<dt><?php echo __('Estimated'); ?></dt>
				<dd id="issue_estimated">
					<?php echo image_tag('action_dropdown_small.png', array('class' => 'dropdown')); ?>
					<?php if ($theIssue->hasEstimatedTime()): ?>
						<?php echo $theIssue->getFormattedTime($theIssue->getEstimatedTime()); ?>
					<?php else: ?>
						<span class="faded_medium"><?php echo __('Not determined'); ?></span>
					<?php endif; ?>
				</dd>
			<?php endif; ?>
			<?php if ($theIssue->isSpentTimeVisible()): ?>
				<dt><?php echo __('Spent'); ?></dt>
				<dd id="issue_spent">
					<?php echo image_tag('action_dropdown_small.png', array('class' => 'dropdown')); ?>
					<?php if ($theIssue->hasSpentTime()): ?>
						<?php echo $theIssue->getFormattedTime($theIssue->getSpentTime()); ?>
					<?php else: ?>
						<span class="faded_medium"><?php echo __('Not determined'); ?></span>
					<?php endif; ?>
				</dd>
			<?php endif; ?>
			<?php if ($theIssue->isMilestoneVisible()): ?>
				<dt><?php echo __('Targetted for'); ?></dt>
				<dd id="issue_assigned_milestones">
					<?php echo image_tag('action_dropdown_small.png', array('class' => 'dropdown')); ?>
					<?php if($theIssue->getMilestone() instanceof TBGMilestone): ?>
						<?php echo image_tag('icon_milestones.png', array('style' => 'float: left; margin-right: 5px;')); ?>
						<?php echo $theIssue->getMilestone()->getName(); ?>
					<?php else: ?>
						<span class="faded_medium"><?php echo __('Not determined'); ?></span>
					<?php endif; ?>
				</dd>
			<?php endif; ?>
		</dl>
		<div style="clear: both;"> </div>
		<div style="text-align: center; font-size: 14px; width: 70px; margin: 5px auto 0 auto; padding: 5px 0 5px 0; height: 20px;">
			<a href="javascript:void(0);" class="faded_medium"><?php echo image_tag('action_add_small_faded.png', array('style' => 'float: left; margin-right: 5px;')); ?><span style="float: left;"><?php echo __('Add'); ?></span></a>
		</div>
	</div>
	<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
</div>