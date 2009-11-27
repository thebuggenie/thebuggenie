<?php

	$bugs_response->setTitle(__('Find issues'));

?>
<table style="width: 100%;" cellpadding="0" cellspacing="0">
	<tr>
		<td style="width: 300px; padding: 5px 5px 0 5px; vertical-align: top;">
			<div class="left_menu_header"><?php echo __('Saved searches'); ?></div>
			or something else
		</td>
		<td style="width: auto; padding: 5px; vertical-align: top;" id="find_issues">
			<div class="rounded_box iceblue_borderless" style="margin: 5px 0 5px 0;">
				<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
				<div class="xboxcontent" style="padding: 3px 10px 3px 10px; font-size: 14px;">
					<form accept-charset="<?php echo BUGScontext::getI18n()->getCharset(); ?>" action="<?php echo (BUGScontext::isProjectContext()) ? make_url('project_issues', array('project_key' => BUGScontext::getCurrentProject()->getKey())) : make_url('search'); ?>" method="get" id="find_issues_form">
						<a href="#" onclick="$('search_filters').toggle();" style="float: right; margin-top: 3px;"><?php echo __('More'); ?></a>
						<label for="issues_searchfor"><?php echo __('Search for'); ?></label>
						<input type="text" name="searchfor" value="<?php echo $searchterm; ?>" id="issues_searchfor" style="width: 300px;">
						<select name="issues_per_page">
							<?php foreach (array(15, 30, 50, 100) as $cc): ?>
								<option value="<?php echo $cc; ?>"<?php if ($ipp == $cc): ?> selected<?php endif; ?>><?php echo __('%number_of_issues% issues per page', array('%number_of_issues%' => $cc)); ?></option>
							<?php endforeach; ?>
							<option value="0"<?php if ($ipp == 0): ?> selected<?php endif; ?>><?php echo __('All results on one page'); ?></option>
						</select>
						<input type="submit" value="<?php echo __('Search'); ?>">
						<div style="display: none; padding: 5px;" id="search_filters">
							you will be able to choose from filters here
							<?php foreach ($filters as $filter => $value): ?>
								<input type="hidden" name="filter[<?php echo $filter; ?>]" value="<?php echo $value; ?>">
							<?php endforeach; ?>
						</div>
					</form>
				</div>
				<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
			</div>
			<?php if ($show_results): ?>
				<div class="main_header">
					<?php echo __('Search results for %search_term%', array('%search_term%' => '<span class="searchterm">"' . $searchterm . '"</span>')); ?>
					&nbsp;&nbsp;<span class="faded_medium"><?php echo __('%number_of% issue(s)', array('%number_of%' => $resultcount)); ?></span>
				</div>
				<?php if (count($issues) > 0): ?>
					<div id="search_results">
						<?php include_template("search/{$templatename}", array('issues' => $issues)); ?>
						<?php include_component('search/pagination', array('searchterm' => $searchterm, 'filters' => $filters, 'groupby' => $groupby, 'resultcount' => $resultcount, 'ipp' => $ipp, 'offset' => $offset)); ?>
					</div>
				<?php else: ?>
					<div class="faded_medium" id="no_issues"><?php echo __('No issues were found'); ?></div>
				<?php endif; ?>
			<?php endif; ?>
		</td>
	</tr>
</table>
