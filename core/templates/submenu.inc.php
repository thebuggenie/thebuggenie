<div class="submenu_strip<?php if (TBGContext::isProjectContext()): ?> project_context<?php endif; ?>">
	<div class="project_stuff">
		<ul>
			<?php $breadcrumbs = $tbg_response->getBreadcrumbs(); ?>
			<?php foreach ($breadcrumbs as $index => $breadcrumb): ?>
				<?php $next_has_menu = (array_key_exists($index + 1, $breadcrumbs) && array_key_exists('subitems', $breadcrumbs[$index + 1]) && is_array($breadcrumbs[$index + 1]['subitems'])); ?>
				<li class="<?php echo (array_key_exists('class', $breadcrumb)) ? $breadcrumb['class'] : 'breadcrumb'; ?>">
					<?php if (array_key_exists('subitems', $breadcrumb) && is_array($breadcrumb['subitems']) && count($breadcrumb['subitems'])): ?>
						<div class="popoutmenu">
							<?php foreach ($breadcrumb['subitems'] as $subitem): ?>
								<?php if (array_key_exists('url', $subitem) || $subitem['title'] == $breadcrumb['title']): ?>
									<a href="<?php echo (array_key_exists('url', $subitem)) ? $subitem['url'] : '#'; ?>"<?php if ($subitem['title'] == $breadcrumb['title']): ?> class="selected"<?php endif; ?>><?php echo $subitem['title']; ?></a>
								<?php endif; ?>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
					<div>
						<?php if ($breadcrumb['url']): ?>
							<?php echo link_tag($breadcrumb['url'], $breadcrumb['title'], array('style' => 'float: left;')); ?>
						<?php else: ?>
							<span style="float: left;"><?php echo $breadcrumb['title']; ?></span>
						<?php endif; ?>
						<?php if ($next_has_menu): ?>
							<?php echo javascript_link_tag(image_tag('tabmenu_dropdown_popout.png', array('class' => 'dropdown_activator')), array('onclick' => "$(this).up('li').next().toggleClassName('popped_out');$(this).toggleClassName('activated');")); ?>
						<?php elseif ($index < count($breadcrumbs) - 1): ?>
							<?php echo image_tag('tabmenu_dropdown_popout.png', array('class' => 'dropdown_activator')); ?>
						<?php endif; ?>
					</div>
				</li>
			<?php endforeach; ?>
			<?php /* $tbg_name_printed = false; ?>
				<?php if (strtolower(TBGSettings::getTBGname()) != strtolower(TBGContext::getCurrentProject()->getName()) || TBGContext::isClientContext()): ?>
					<?php $tbg_name_printed = true; ?>
					<li class="breadcrumb"><?php echo link_tag(make_url('home'), TBGSettings::getTBGName()); ?></li>
					<?php if (TBGContext::isClientContext()): ?>
						<li class="breadcrumb">&raquo; <?php echo link_tag(make_url('client_dashboard', array('client_id' => TBGContext::getCurrentClient()->getID())), TBGContext::getCurrentClient()->getName()); ?></li>
					<?php endif; ?>
				<?php endif; ?>
				<?php break; ?>
			<?php endforeach; ?>
			<li class="project_name">
				<?php if ($tbg_name_printed): ?><span>&raquo;</span> <?php endif; ?><?php echo link_tag(make_url('project_dashboard', array('project_key' => TBGContext::getCurrentProject()->getKey())), TBGContext::getCurrentProject()->getName()); ?>
			</li>
			<?php foreach ($tbg_response->getBreadcrumbs() as $breadcrumb): ?>
				<li class="breadcrumb">&raquo;
					<?php if ($breadcrumb['url']): ?>
						<?php echo link_tag($breadcrumb['url'], $breadcrumb['title']); ?>
					<?php else: ?>
						<?php echo $breadcrumb['title']; ?>
					<?php endif; ?>
				</li>
			<?php endforeach; */ ?>
		</ul>
	</div>
	<?php if ($tbg_user->canSearchForIssues()): ?>
		<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="<?php echo (TBGContext::isProjectContext()) ? make_url('project_issues', array('project_key' => TBGContext::getCurrentProject()->getKey(), 'quicksearch' => 'true')) : make_url('search', array('quicksearch' => 'true')); ?>" method="get" name="quicksearchform" style="float: right;">
			<div style="width: auto; padding: 0; text-align: right; position: relative;">
				<?php $quicksearch_title = __('Search for anything here'); ?>
				<input type="hidden" name="filters[text][operator]" value="=">
				<input type="text" name="filters[text][value]" id="searchfor" value="<?php echo $quicksearch_title; ?>" style="width: 320px; padding: 1px 1px 1px;" onblur="if ($('searchfor').getValue() == '') { $('searchfor').value = '<?php echo $quicksearch_title; ?>'; $('searchfor').addClassName('faded_out'); }" onfocus="if ($('searchfor').getValue() == '<?php echo $quicksearch_title; ?>') { $('searchfor').clear(); } $('searchfor').removeClassName('faded_out');" class="faded_out"><div id="searchfor_autocomplete_choices" class="autocomplete"></div>
				<script type="text/javascript">

				new Ajax.Autocompleter("searchfor", "searchfor_autocomplete_choices", '<?php echo (TBGContext::isProjectContext()) ? make_url('project_quicksearch', array('project_key' => TBGContext::getCurrentProject()->getKey())) : make_url('quicksearch'); ?>', {paramName: "filters[text][value]", minChars: 2});

				</script>
				<input type="submit" value="<?php echo TBGContext::getI18n()->__('Find'); ?>" style="padding: 0 2px 0 2px;">
			</div>
		</form>
	<?php endif; ?>
</div>