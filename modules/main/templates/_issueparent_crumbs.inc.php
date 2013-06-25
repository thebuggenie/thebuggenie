<?php if ($p_issues = $issue->getParentIssues()): ?>
	<?php include_template('issueparent_crumbs', array('issue' => array_shift($p_issues), 'parent' => true)); ?>
<?php endif; ?>
<span class="issue_state <?php echo $issue->isClosed() ? 'closed' : 'open'; ?>"><?php echo $issue->isClosed() ? mb_strtoupper(__('Closed')) : mb_strtoupper(__('Open')); ?></span>
<?php $it_string = __('%issuetype% %issue_no%', array('%issuetype%' => (($issue->hasIssueType()) ? $issue->getIssueType()->getName() : __('Unknown issuetype')), '%issue_no%' => $issue->getFormattedIssueNo(true))); ?>
<?php echo link_tag(make_url('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())), $it_string, array('title' => (isset($parent) && $parent) ? $it_string . ': ' . tbg_decodeUTF8($issue->getTitle()) : '')); ?>
<?php if (isset($parent) && $parent) echo '&nbsp;&raquo;&nbsp;'; ?>