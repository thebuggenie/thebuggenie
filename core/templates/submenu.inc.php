<div class="submenu_strip<?php if (TBGContext::isProjectContext()): ?> project_context<?php endif; ?>">
	<div class="project_stuff">
		<ul>
			<?php $breadcrumbs = $tbg_response->getBreadcrumbs(); ?>
			<?php foreach ($breadcrumbs as $index => $breadcrumb): ?>
				<?php $next_has_menu = (array_key_exists($index + 1, $breadcrumbs) && array_key_exists('subitems', $breadcrumbs[$index + 1]) && is_array($breadcrumbs[$index + 1]['subitems'])); ?>
				<li class="breadcrumb">
					<?php if (array_key_exists('subitems', $breadcrumb) && is_array($breadcrumb['subitems']) && count($breadcrumb['subitems'])): ?>
						<div class="popoutmenu">
							<?php $first = true; ?>
							<?php foreach ($breadcrumb['subitems'] as $subindex => $subitem): ?>
								<?php if (array_key_exists('url', $subitem) || $subitem['title'] == $breadcrumb['title']): ?>
									<a href="<?php echo (array_key_exists('url', $subitem)) ? $subitem['url'] : '#'; ?>"<?php if ($subitem['title'] == $breadcrumb['title']): ?> class="selected<?php if ($first): ?> rounded_list_first_item<?php endif; ?>"<?php endif; ?>><?php echo $subitem['title']; ?></a>
									<?php $first = false; ?>
								<?php endif; ?>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
					<div>
						<?php $class = (array_key_exists('class', $breadcrumb) && $breadcrumb['class']) ? $breadcrumb['class'] : ''; ?>
						<?php if ($breadcrumb['url']): ?>
							<?php echo link_tag($breadcrumb['url'], $breadcrumb['title'], array('style' => 'float: left;', 'class' => $class)); ?>
						<?php else: ?>
							<span <?php if ($class): ?> class="<?php echo $class; ?>"<?php endif; ?> style="float: left;"><?php echo $breadcrumb['title']; ?></span>
						<?php endif; ?>
						<?php if ($next_has_menu): ?>
							<?php echo javascript_link_tag(image_tag('tabmenu_dropdown_popout.png', array('class' => 'dropdown_activator clickable')), array('onclick' => "$(this).up('li').next().toggleClassName('popped_out');$(this).toggleClassName('activated');", 'title' => __('Click to expand'))); ?>
						<?php elseif ($index < count($breadcrumbs) - 1): ?>
							<?php echo image_tag('tabmenu_dropdown_popout.png', array('class' => 'dropdown_activator')); ?>
						<?php endif; ?>
					</div>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
	<?php if ($tbg_user->canSearchForIssues()): ?>
		<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="<?php echo (TBGContext::isProjectContext()) ? make_url('project_quicksearch', array('project_key' => TBGContext::getCurrentProject()->getKey())) : make_url('quicksearch'); ?>" method="get" name="quicksearchform" style="float: right;">
			<div style="width: auto; padding: 0; text-align: right; position: relative;">
				<?php $quicksearch_title = __('Search for anything here'); ?>
				<input type="hidden" name="filters[text][operator]" value="=">
				<?php echo image_tag('spinning_16.gif', array('id' => 'quicksearch_indicator', 'style' => 'position: absolute; left: 305px; top: 2px; display: none; z-index: 10;')); ?>
				<input type="text" name="filters[text][value]" id="searchfor" value="<?php echo $quicksearch_title; ?>" style="width: 320px; padding: 1px 1px 1px;" onblur="if ($('searchfor').getValue() == '') { $('searchfor').value = '<?php echo $quicksearch_title; ?>'; $('searchfor').addClassName('faded_out'); }" onfocus="if ($('searchfor').getValue() == '<?php echo $quicksearch_title; ?>') { $('searchfor').clear(); } $('searchfor').removeClassName('faded_out');" class="faded_out"><div id="searchfor_autocomplete_choices" class="autocomplete rounded_box"></div>
				<script type="text/javascript">

				Ajax.Autocompleter.extract_value = function (value, className)
				{
					var result;
					var elements = value.getElementsByClassName(className);
					if (elements && elements.length == 1)
					{
						result = elements[0].innerHTML.unescapeHTML();
					}

					return result;
				};
  
				new Ajax.Autocompleter(
					"searchfor",
					"searchfor_autocomplete_choices",
					'<?php echo (TBGContext::isProjectContext()) ? make_url('project_quicksearch', array('project_key' => TBGContext::getCurrentProject()->getKey())) : make_url('quicksearch'); ?>',
					{
						paramName: "filters[text][value]",
						minChars: 2,
						indicator: 'quicksearch_indicator',
						afterUpdateElement: function (elem, value)
						{
							val = Ajax.Autocompleter.extract_value(value, 'url');
							if (val != '' && val !== undefined)
							{
								window.location = val;
								$('quicksearch_indicator').show();
								$('searchfor').blur();
							}
						}
					}
				);

				</script>
				<input type="submit" value="<?php echo TBGContext::getI18n()->__('Find'); ?>" style="padding: 0 2px 0 2px;">
			</div>
		</form>
	<?php endif; ?>
</div>