<div class="rounded_box white borderless shadowed backdrop_box large">
	<div class="backdrop_detail_header">
		<?php echo __('Configure my personal dashboard'); ?>
	</div>
	<div class="backdrop_detail_content" id="login_content">
		<ul id="views_list" style="float: left; margin: 0; padding: 0; list-style: none;" class="sortable">
		<?php foreach ($dashboardViews as $view): ?>
			<li id="view_<?php echo $view->get(TBGUserDashboardViewsTable::VIEW); ?>" class="rounded_box mediumgrey" style="margin: 5px 10px; float: left; width: 30em; cursor: move; text-align: left;">
				<span id="<?php echo $view->get(TBGUserDashboardViewsTable::VIEW); ?>_<?php echo $view->get(TBGUserDashboardViewsTable::TYPE); ?>"><?php echo $views[$view->get(TBGUserDashboardViewsTable::TYPE)][$view->get(TBGUserDashboardViewsTable::VIEW)]; ?></span>
				<?php echo javascript_link_tag(image_tag('tabmenu_dropdown.png', array('class' => 'menu_dropdown', 'style' => 'float: right; margin-left: 5px;')), array('onclick' => "this.ancestors()[0].childElements()[3].toggle(); this.ancestors()[0].className = this.ancestors()[0].childElements()[1].visible() ? 'rounded_box verylightyellow' : 'rounded_box mediumgrey';")); ?>
				<?php echo javascript_link_tag(image_tag('action_remove_small.png', array('class' => 'menu_dropdown', 'style' => 'float: right; margin-left: 5px;')), array('onclick' => "this.ancestors()[0].remove();  Sortable.create('views');")); ?>
				<table style="display: none; width: 100%; cursor: default;">
					<?php foreach ($views as $id_type => $view_type): ?>
						<?php foreach ($view_type as $id_view => $a_view): ?>
							<?php if ($id_view == $view->get(TBGUserDashboardViewsTable::VIEW)) continue; ?>
							<tr class="hover_highlight">
								<td style="padding-left: 12px;" onclick="swapDashboardView(this.childElements()[0], this.ancestors()[3].childElements()[0]);">
									<span id="<?php echo $id_view; ?>_<?php echo $id_type; ?>"><?php echo $a_view; ?></span>
								</td>
							</tr>
						<?php endforeach; ?>
					<?php endforeach; ?>
				</table>
			</li>
		<?php endforeach; ?>
		</ul>
		
		<ul id="view_template" style="display: none;">
			<li id="view_default" class="rounded_box mediumgrey" style="margin: 5px 10px; float: left; width: 30em; cursor: move; text-align: left;">
				<span class="template_view" id="0_0"><?php echo __('...Select a view...'); ?></span>
				<?php echo javascript_link_tag(image_tag('tabmenu_dropdown.png', array('class' => 'menu_dropdown', 'style' => 'float: right; margin-left: 5px;')), array('onclick' => "this.ancestors()[0].childElements()[3].toggle(); this.ancestors()[0].className = this.ancestors()[0].childElements()[1].visible() ? 'rounded_box verylightyellow' : 'rounded_box mediumgrey';")); ?>
				<?php echo javascript_link_tag(image_tag('action_remove_small.png', array('class' => 'menu_dropdown', 'style' => 'float: right; margin-left: 5px;')), array('onclick' => "this.ancestors()[0].remove(); Sortable.create('views');")); ?>
				<table id="view_other_default" style="display: none; width: 100%; cursor: default;">
					<?php foreach ($views as $id_type => $view_type): ?>
						<?php foreach ($view_type as $id_view => $a_view): ?>
							<tr class="hover_highlight">
								<td style="padding-left: 12px;" onclick="swapDashboardView(this.childElements()[0], this.ancestors()[3].childElements()[0]);">
									<span id="<?php echo $id_view; ?>_<?php echo $id_type; ?>"><?php echo $a_view; ?></span>
								</td>
							</tr>
						<?php endforeach; ?>
					<?php endforeach; ?>
				</table>
			</li>
		</ul>

		<ul style="margin: 0; padding: 0; list-style:none; clear: both;">
			<li class="rounded_box white" style="margin: 5px 10px; text-align: center;">
				<?php echo javascript_link_tag(__('Add a view to my dashboard'), array('onclick' => "addDashboardView();")); ?>
				<?php echo javascript_link_tag(image_tag('action_add_small.png', array('class' => 'menu_dropdown', 'style' => 'float: right; margin-left: 5px;')), array('onclick' => "addDashboardView();")); ?>
			</li>
		</ul>
		<div style="text-align: right; padding: 10px;">
			<?php echo __("When you're happy, save your changes"); ?>
			<button onclick="saveDashboard('<?php echo make_url('dashboard_save'); ?>');" style="float: right; margin-left: 10px;"><?php echo __('Save my dashboard'); ?></button>
		</div>
	</div>
	<div class="backdrop_detail_footer">
		<a href="javascript:void(0);" onclick="resetFadedBackdrop();"><?php echo __('Close'); ?></a>
	</div>
</div>
<script>Sortable.create('views_list', {constraint: ''});</script>