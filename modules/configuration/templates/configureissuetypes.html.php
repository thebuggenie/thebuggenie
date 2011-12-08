<?php

	$tbg_response->setTitle(__('Configure issue types'));

?>
<table style="table-layout: fixed; width: 100%" cellpadding=0 cellspacing=0>
	<tr>
		<?php include_component('leftmenu', array('selected_section' => 6)); ?>
		<td valign="top" style="padding-left: 15px;">
			<div style="width: 788px;" id="config_issuetypes">
				<div style="width: 788px; clear: both; height: 30px; margin-top: 15px;" class="tab_menu">
					<ul id="issuetypes_menu">
						<li id="tab_types"<?php if ($mode == 'issuetypes'): ?> class="selected"<?php endif; ?>><?php echo link_tag(make_url('configure_issuetypes'), __('Available issue types')); ?></li>
						<li id="tab_schemes"<?php if ($mode == 'schemes'): ?> class="selected"<?php endif; ?>><?php echo link_tag(make_url('configure_issuetypes_schemes'), __('Issue type schemes')); ?></li>
						<?php if (isset($scheme)): ?>
							<li id="tab_scheme" class="selected"><?php echo link_tag(make_url('configure_issuetypes_scheme', array('scheme_id' => $scheme->getID())), $scheme->getName()); ?></li>
						<?php endif; ?>
					</ul>
				</div>
				<div id="issuetypes_menu_panes">
					<?php if ($mode == 'issuetypes'): ?>
						<div id="tab_types_pane" style="padding-top: 0; width: 788px;">
							<div class="content">
								<?php echo __('In this tab you can add/remove/edit what issue types are available to issue type schemes. If you add a new issue type on this page, remember to associate it to an issue type scheme on the %issue_type_schemes% tab to get it to show up for users.', array('%issue_type_schemes%' => javascript_link_tag(__('Issue type schemes'), array('onclick' => "TBG.Main.Helpers.tabSwitcher('tab_schemes', 'issuetypes_menu');")))); ?>
							</div>
							<div id="issuetypes_list">
								<?php foreach ($issue_types as $type): ?>
									<?php include_component('issuetype', array('type' => $type)); ?>
								<?php endforeach; ?>
							</div>
							<div class="header_div" style="margin-top: 20px;"><?php echo __('Add a new issue type'); ?></div>
							<div class="rounded_box yellow borderless" style="margin: 5px 0 0 0; padding: 3px; font-size: 12px;">
								<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_issuetypes_add'); ?>" onsubmit="TBG.Config.Issuetype.add('<?php echo make_url('configure_issuetypes_add'); ?>');return false;" id="add_issuetype_form">
									<label for="new_issuetype_name"><?php echo __('Issue type name'); ?></label>
									<input type="text" name="name" id="new_issuetype_name" style="width: 200px;">
									<label for="new_issuetype_icon"><?php echo __('Type'); ?></label>
									<select name="icon" id="new_issuetype_icon">
										<?php foreach ($icons as $icon => $description): ?>
											<option value="<?php echo $icon; ?>"<?php if ($icon == 'bug_report'): ?> selected<?php endif; ?>><?php echo $description; ?></option>
										<?php endforeach; ?>
									</select>
									<input type="submit" value="<?php echo __('Add'); ?>" style="font-weight: bold;" id="add_issuetype_button">
									<?php echo image_tag('spinning_16.gif', array('style' => 'margin-right: 5px; display: none;', 'id' => 'add_issuetype_indicator')); ?>
								</form>
							</div>
						</div>
					<?php elseif ($mode == 'schemes'): ?>
						<div id="tab_schemes_pane" style="padding-top: 0; width: 750px;">
							<div class="content">
								<?php echo __('In this tab you can add/remove/edit issue type schemes. If you add a new issue type on the previous tab, you must associate it with an issue type scheme in this tab to get it to show up for users.'); ?><br>
								<br>
								<i class="faded_out dark"><?php echo __('Keep in mind that the original issue type scheme is uneditable, so to make any changes, make a copy of the first issue type scheme, or edit one that is not the one shipped with The Bug Genie'); ?></i>
							</div>
							<ul class="scheme_list issuetype_scheme_list simple_list" id="issuetype_schemes_list">
								<?php foreach ($issue_type_schemes as $scheme): ?>
									<?php include_component('issuetypescheme', array('scheme' => $scheme)); ?>
								<?php endforeach; ?>
							</ul>
						</div>
					<?php elseif ($mode == 'scheme'): ?>
						<div id="tab_scheme_pane" style="padding-top: 0; width: 750px;">
							<div class="content">
								<?php echo __('In this tab you can edit issue type associations for this issue type scheme. Enable/disable available issue types, and set options such as reportable issue types and reportable/visible/required issue details.'); ?>
							</div>
							<ul class="scheme_list issuetype_scheme_list simple_list">
								<?php foreach ($issue_types as $type): ?>
									<?php include_component('issuetype', array('type' => $type, 'scheme' => $scheme)); ?>
								<?php endforeach; ?>
							</ul>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</td>
	</tr>
</table>