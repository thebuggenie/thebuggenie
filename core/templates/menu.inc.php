<div class="medium_transparent" style="z-index: 200001; margin: 0; position: fixed; top: 0; left: 0; width: 100%; padding: 0; background-color: #E84545; font-size: 14px; color: #000; border-bottom: 1px solid #555; display: none;" id="thebuggenie_failuremessage">
	<div style="padding: 10px 0 10px 0;">
		<span style="color: #000; font-weight: bold;" id="thebuggenie_failuremessage_title"></span><br>
		<span id="thebuggenie_failuremessage_content"></span>
	</div>
</div>
<div class="medium_transparent" style="z-index: 200000; margin: 0; position: fixed; top: 0; left: 0; width: 100%; padding: 0; background-color: #45E845; font-size: 14px; color: #000; border-bottom: 1px solid #555; display: none;" id="thebuggenie_successmessage">
	<div style="padding: 10px 0 10px 0;">
		<span style="color: #000; font-weight: bold;" id="thebuggenie_successmessage_title"></span><br>
		<span id="thebuggenie_successmessage_content"></span>
	</div>
</div>
<div id="fullpage_backdrop" style="display: none; background-color: transparent; width: 100%; height: 100%; position: absolute; top: 0; left: 0; margin: 0; padding: 0; text-align: center;">
	<div style="position: absolute; top: 45%; left: 40%; z-index: 100001; color: #FFF; font-size: 15px; font-weight: bold;" id="fullpage_backdrop_indicator">
		<?php echo image_tag('spinning_32.gif'); ?><br>
		<?php echo __('Please wait, loading content'); ?>...
	</div>
	<div id="fullpage_backdrop_content"> </div>
	<div style="background-color: #000; width: 100%; height: 100%; position: absolute; top: 0; left: 0; margin: 0; padding: 0; z-index: 100000;" class="semi_transparent" onclick="resetFadedBackdrop();"> </div>
</div>
<div class="tab_menu header_menu">
	<ul>
		<?php if (!TBGSettings::isSingleProjectTracker()): ?>
			<li<?php if ($tbg_response->getPage() == 'home'): ?> class="selected"<?php endif; ?>><?php echo link_tag(make_url('home'), image_tag('tab_index.png', array('style' => 'float: left;')).__('Frontpage')); ?></li>
		<?php endif; ?>
		<?php if (!$tbg_user->isThisGuest()): ?>
			<li<?php if ($tbg_response->getPage() == 'dashboard'): ?> class="selected"<?php endif; ?>><?php echo link_tag(make_url('dashboard'), image_tag('icon_dashboard_small.png', array('style' => 'float: left;')).__('My dashboard')); ?></li>
		<?php endif; ?>
		<?php if ($tbg_user->canReportIssues() || (TBGContext::isProjectContext() && $tbg_user->canReportIssues(TBGContext::getCurrentProject()->getID()))): ?>
			<?php if (TBGContext::isProjectContext() && $tbg_user->canReportIssues(TBGContext::getCurrentProject()->getID())): ?>
				<li<?php if ($tbg_response->getPage() == 'reportissue'): ?> class="selected"<?php endif; ?>><?php echo link_tag(make_url('project_reportissue', array('project_key' => TBGContext::getCurrentProject()->getKey())), image_tag('tab_reportissue.png', array('style' => 'float: left;')).((isset($_SESSION['rni_step1_set'])) ? __('Continue reporting') : __('Report an issue'))); ?></li>
			<?php elseif ($tbg_user->canReportIssues()): ?>
				<li<?php if ($tbg_response->getPage() == 'reportissue'): ?> class="selected"<?php endif; ?>><?php echo link_tag(make_url('reportissue'), image_tag('tab_reportissue.png', array('style' => 'float: left;')).((isset($_SESSION['rni_step1_set'])) ? __('Continue reporting') : __('Report an issue'))); ?></li>
			<?php endif; ?>
		<?php endif; ?>
		<?php if ($tbg_user->canSearchForIssues()): ?>
			<li<?php if ($tbg_response->getPage() == 'search'): ?> class="selected"<?php endif; ?>><?php echo link_tag(make_url('search'), image_tag('tab_search.png', array('style' => 'float: left;')).__('Find issues')); ?></li>
		<?php endif; ?>
		<?php foreach (TBGContext::getModules() as $module): ?>
			<?php if ($module->hasAccess() && $module->isVisibleInMenu() && $module->isEnabled()): ?>
				<li<?php if ($tbg_response->getPage() == $module->getTabKey()): ?> class="selected"<?php endif; ?>><?php echo link_tag($module->getRoute(), image_tag('tab_' . $module->getName() . '.png', array('style' => 'float: left;'), false, $module->getName()).$module->getMenuTitle()); ?></li>
			<?php endif; ?>
		<?php endforeach; ?>
		<?php if ($tbg_user->canAccessConfigurationPage()): ?>
			<li<?php if ($tbg_response->getPage() == 'config'): ?> class="selected"<?php endif; ?>><?php echo link_tag(make_url('configure'), image_tag('tab_config.png', array('style' => 'float: left;')).__('Configure')); ?></li>
		<?php endif; ?>
		<?php /*?><li<?php if ($tbg_response->getPage() == 'about'): ?> class="selected"<?php endif; ?>><?php echo link_tag(make_url('about'), image_tag('tab_about.png', array('style' => 'float: left;')).__('About')); ?></li> */ ?>
	</ul>
	<?php if ($tbg_user->canSearchForIssues()): ?>
		<ul class="right">
			<li style="height: 24px;" class="nohover">
				<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="<?php echo (TBGContext::isProjectContext()) ? make_url('project_issues', array('project_key' => TBGContext::getCurrentProject()->getKey(), 'quicksearch' => 'true')) : make_url('search', array('quicksearch' => 'true')); ?>" method="get" name="quicksearchform">
					<div style="width: auto; padding: 0; text-align: right; position: relative;">
					<label for="searchfor"><?php echo __('Quick search'); ?></label>
					<?php $quicksearch_title = __('Search for anything here'); ?>
					<input type="text" name="searchfor" id="searchfor" value="<?php echo $quicksearch_title; ?>" style="width: 220px; padding: 1px 1px 1px;" onblur="if ($('searchfor').getValue() == '') { $('searchfor').value = '<?php echo $quicksearch_title; ?>'; $('searchfor').addClassName('faded_out'); }" onfocus="if ($('searchfor').getValue() == '<?php echo $quicksearch_title; ?>') { $('searchfor').clear(); } $('searchfor').removeClassName('faded_out');" class="faded_out"><div id="searchfor_autocomplete_choices" class="autocomplete"></div>
					<script type="text/javascript">

					new Ajax.Autocompleter("searchfor", "searchfor_autocomplete_choices", '<?php echo (TBGContext::isProjectContext()) ? make_url('project_quicksearch', array('project_key' => TBGContext::getCurrentProject()->getKey())) : make_url('quicksearch'); ?>', {paramName: "searchfor", minChars: 2});

					</script>
					<input type="submit" value="<?php echo TBGContext::getI18n()->__('Find'); ?>" style="padding: 0 2px 0 2px;">
					</div>
				</form>
			</li>
		</ul>
	<?php endif; ?>
</div>
<?php if ($tbg_response->isProjectMenuStripVisible()): ?>
	<div id="project_menustrip"><?php include_component('project/menustrip', array('project' => TBGContext::getCurrentProject())); ?></div>
<?php endif; ?>