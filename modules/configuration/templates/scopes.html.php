<?php $tbg_response->setTitle(__('Configure scopes')); ?>
<table style="table-layout: fixed; width: 100%" cellpadding=0 cellspacing=0>
	<tr>
		<?php include_component('leftmenu', array('selected_section' => TBGSettings::CONFIGURATION_SECTION_SCOPES)); ?>
		<td valign="top">
			<div style="width: 750px;" id="config_scopes">
				<div class="config_header"><?php echo __('Configure scopes'); ?></div>
				<div class="content">
					<?php echo __('The Bug Genie scopes are self-contained environments within the same The Bug Genie installation, set up to respond to different hostnames.'); ?>
					<?php echo __('The default scope - created during installation - is used for all hostnames where there is no other scope defined. Read more about scopes in %ConfigureScopes%.', array('%ConfigureScopes%' => link_Tag(make_url('publish_article', array('article_name' => 'ConfigureScopes')), 'ConfigureScopes'))); ?>
				</div>
				<div class="header" style="margin: 15px 0 10px 0;">
					<div class="nice_button" style="float: right;"><input type="button" value="<?php echo __('Add a new scope'); ?>" onclick="failedMessage('not implemented yet')"></input></div>
					<?php echo __('Scopes available on this installation'); ?>
				</div>
				<div id="scopes_list" style="margin-top: 5px;">
					<?php foreach ($scopes as $scope): ?>
						<?php include_template('configuration/scopebox', array('scope' => $scope)); ?>
					<?php endforeach; ?>
				</div>
			</div>
		</td>
	</tr>
</table>