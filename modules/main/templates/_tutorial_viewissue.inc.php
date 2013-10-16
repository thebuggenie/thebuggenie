<script>
	TBG.Tutorial.Stories.viewissue = {
		1: {
			message: "<h2><?php echo __('Get started with the issue overview'); ?></h2><?php echo __("This is the issue overview page. From this page you can get a full detail overview of the issues you're looking at"); ?><br><br><?php echo __("We'll just quickly go over the most important elements on this page to help you get going."); ?>",
			messageSize: 'large',
			button: "<?php echo __('Next'); ?>"
		},
		2: {
			highlight: {element: 'global_help_link', blocked: true, delay: 500},
			message: "<h3><?php echo __('Getting help'); ?></h3><?php echo __("Remember that you can always get help on any page in The Bug Genie by clicking '%help_for_this_page' in the user menu.", array('%help_for_this_page' => __('Help for this page'))).'<br>'.__("This will take you to the online help page for the specific page you are on."); ?>",
			messageSize: 'large',
			messagePosition: 'left',
			button: "<?php echo __('Got it!'); ?>",
			cb: function() {
				$('header_usermenu_link').addClassName('force_dropdown');
			}
		},
		3: {
			message: "<h3><?php echo __('Issue overview page layout'); ?></h3><?php echo __('The issue view is split into four parts: the header bar, the issue details, the main area and the bottom panels.'); ?>",
			messageSize: 'medium',
			button: '<?php echo __('Next'); ?>',
			cb: function() {
				$('header_usermenu_link').removeClassName('force_dropdown');
			}
		},
		4: {
			highlight: {element: 'viewissue_header_container', blocked: true},
			message: '<h3><?php echo __('The header bar'); ?></h3><?php echo __('The header bar is always at the top. If you scroll down the page, the header will follow you down the page, so you always have it available.'); ?>',
			messageSize: 'large',
			messagePosition: 'below',
			button: '<?php echo __('Cool'); ?>'
		},
		5: {
			highlight: {element: 'workflow_actions', blocked: true},
			message: '<h3><?php echo __('Issue workflow actions'); ?></h3><?php echo __('The header area also contains the workflow buttons used to move the issue through its lifecycle.'); ?><br><br><?php echo __('These buttons will also follow you down the page as you scroll.'); ?>',
			messageSize: 'medium',
			messagePosition: 'below',
			button: '<?php echo __('Nice'); ?>'
		},
		6: {
			highlight: {element: 'issue_details', blocked: true},
			message: "<h3><?php echo __('Issue details'); ?></h3><?php echo __('This area contains all the basic details'); ?>",
			messageSize: 'small',
			messagePosition: 'right',
			cb: function(td) {
				$('available-quests-list').childElements().each(function(elm) {
					if (elm.visible()) {
						elm.observe('click', function() {
							$('available-quests-list').childElements().each(function(elm) {
								elm.stopObserving('click');
								elm.observe('click', Devo.Main.highlightTellable);
							});
							Devo.Tutorial.playNextStep();
						});
					}
				});
			}
		},
		7: {
			highlight: {element: 'adventure-map', blocked: false},
			message: "<h4>Selecting a quest</h4>As you can see, the map now moved to the position where the selected quest is located. You can click on the point on the map to bring up more details about the quest.<br><br>Some quests are faded out because they are not available. You can see more details about the quest - including requirements and rewards - by holding your mouse over that map point.<br><br><strong>Click on the point on the map</strong>",
			messageSize: 'small',
			messagePosition: 'left',
			cb: function(td) {
				$('adventure-map').stopObserving('mousedown', Devo.Main.startMapDrag);
				$('adventure-map').observe('mousedown', function(event) { event.stopPropagation(); });
				$$('.map-point').each(function(elm) {
					if (elm.visible()) {
						elm.observe('click', Devo.Tutorial.playNextStep);
					}
				});
			}
		},
		8: {
			highlight: {element: 'adventure-map', blocked: true},
			message: "<h4>Selecting a quest</h4>When you've selected a quest, you can see more details about the quest, such as all the main chapters (if you've selected a story).<br><br>Clicking a chapter lets you see more details about that chapter, as well as start it.",
			messageSize: 'small',
			messagePosition: 'left',
			button: 'Next',
			cb: function(td) {
				$('adventure-map').stopObserving('mousedown');
				$('adventure-map').observe('mousedown', Devo.Main.startMapDrag);
				$$('.map-point').each(function(elm) {
					if (elm.visible()) {
						elm.stopObserving('click', Devo.Tutorial.playNextStep);
						$('tutorial-next-button').stopObserving('click');
						$('tutorial-next-button').observe('click', function() {
							$('adventure-book').show();
							$('tutorial-next-button').stopObserving('click');
							window.setTimeout(function() {
								Devo.Tutorial.playNextStep();
							}, 500);
						});
						$('adventure-book').hide();
					}
				});
			}
		},
		9: {
			highlight: {element: 'adventure-book', blocked: true},
			message: "<h4>Selecting a quest</h4>When you've selected a quest on the map, you will see the adventure book pop up.<br><br>The adventure book has more information about the selected quest with the background story and quest description, as well as more information about its rewards.<br><br>It will also keep track of your attempts at that quest (available later).",
			messageSize: 'large',
			messagePosition: 'above',
			button: "Next",
			cb: function(td) {
				$('tutorial-next-button').stopObserving('click');
				$('tutorial-next-button').observe('click', Devo.Tutorial.playNextStep);
			}
		},
		10: {
			highlight: {element: 'adventure-book', blocked: true},
			message: '<h4>Selecting a quest</h4>The adventure book also has buttons to start the quest if it is available.<br><br>By the way, if the adventure book is in your way, you can always move it by dragging it around.',
			messageSize: 'medium',
			messagePosition: 'above',
			button: "Can I try moving it now?"
		},
		11: {
			highlight: {element: 'adventure-book', blocked: true},
			message: '<h4>Selecting a quest</h4>Not now.',
			messageSize: 'medium',
			messagePosition: 'above',
			button: "Awww ..."
		},
		12: {
			message: "<h1>Have fun!</h1>That was it.<br><br>Don't hesitate to ask someone if you're stuck. Remember, the lobby is always just a click away.",
			messageSize: 'medium',
			button: 'Done!',
			cb: function() {
				Devo.Main.clearMapSelections();
			}
		}
	};
	TBG.Tutorial.start('viewissue', 4);
</script>
