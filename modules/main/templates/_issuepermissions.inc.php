<div class="backdrop_box medium" id="viewissue_add_item_div">
	<div class="backdrop_detail_header"><?php echo __('Issue access policy'); ?></div>
	<div id="backdrop_detail_content">
		<input type="radio" name="issue_access" id="issue_access_public" onchange="TBG.Issues.ACL.toggle_checkboxes(this, <?php echo $issue->getID(); ?>);" value="public" checked><label for="issue_access_public"><?php echo __('Available to anyone with access'); ?></label><br>
		<input type="radio" name="issue_access" id="issue_access_restricted" onchange="TBG.Issues.ACL.toggle_checkboxes(this, <?php echo $issue->getID(); ?>);" value="restricted"><label for="issue_access_restricted"><?php echo __('Available only to those listed below'); ?></label><br>
		<?php image_tag('spinning_16.gif', array('id' => 'acl_indicator_'.$issue->getID(), 'style' => '')); ?>
		<form action="<?php echo make_url('unlock_issue', array('issue_id' => $issue->getID())); ?>" onsubmit="TBG.Issues.ACL.set('<?php echo make_url('unlock_issue', array('issue_id' => $issue->getID())); ?>');return false;" method="post" id="acl_<?php echo $issue->getID(); ?>_public">
			<div style="text-align: right;">
				<input type="submit" value="<?php echo __('Save changes'); ?>" class="button button-green">
			</div>
		</form>
		<div id="acl_<?php echo $issue->getID(); ?>_restricted" style="display: none;">
			<h4 style="margin-top: 10px;">
				<?php echo javascript_link_tag(__('Add a user or team'), array('onclick' => "$('popup_find_acl_{$issue->getID()}').toggle();", 'style' => 'float: right;', 'class' => 'button button-silver')); ?>
				<?php echo __('Users or teams who can see this issue'); ?>
			</h4>
			<?php include_component('identifiableselector', array(	'html_id' 			=> "popup_find_acl_{$issue->getID()}",
																	'header' 			=> __('Give someone access to this issue'),
																	'callback'		 	=> "TBG.Issues.ACL.addTarget('" . make_url('getacl_formentry', array('identifiable_type' => 'user', 'identifiable_value' => '%identifiable_value%')) . "', {$issue->getID()});",
																	'team_callback' 	=> "TBG.Issues.ACL.addTarget('" . make_url('getacl_formentry', array('identifiable_type' => 'team', 'identifiable_value' => '%identifiable_value%')) . "', {$issue->getID()});",
																	'base_id'			=> "popup_find_acl_{$issue->getID()}",
																	'include_teams'		=> true,
																	'allow_clear'		=> false,
																	'absolute'			=> true)); ?>
			<form action="<?php echo make_url('move_issue', array('issue_id' => $issue->getID())); ?>" method="post" onsubmit="TBG.Issues.ACL.set('<?php echo make_url('unlock_issue', array('issue_id' => $issue->getID())); ?>');return false;" id="acl_<?php echo $issue->getID(); ?>_form">
				<div id="issue_<?php echo $issue->getID(); ?>_access_list_none" class="faded_out" style="padding: 5px;"><?php echo __('Noone else can see this issue'); ?></div>
				<ul class="simple_list" id="issue_<?php echo $issue->getID(); ?>_access_list" style="padding: 5px;">
					<?php foreach ($issue->getAccessList() as $item): ?>
						<?php include_template('main/issueaclformentry', array('target' => $item['target'])); ?>
					<?php endforeach; ?>
				</ul>
				<div style="text-align: right;">
					<input type="submit" value="<?php echo __('Save changes'); ?>" class="button button-green">
				</div>
			</form>
		</div>
	</div>
	<div class="backdrop_detail_footer">
		<a href="javascript:void(0);" onclick="TBG.Main.Helpers.Backdrop.reset();"><?php echo __('Cancel'); ?></a>
	</div>
</div>