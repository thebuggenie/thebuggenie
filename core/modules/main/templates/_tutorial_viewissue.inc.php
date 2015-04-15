<script>
    require(['domReady', 'thebuggenie/tbg'], function (domReady, TBG) {
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
                highlight: {element: 'issue_details', blocked: true, delay: 500},
                message: "<h3><?php echo __('Issue details'); ?></h3><?php echo __("This area contains all the data about an issue, such as the status, who's assigned, how much time is spent, etc."); ?><br><br><?php echo __("The sidebar is nicely grouped in sections such as 'People involved' and 'Time tracking' to make it easer to quickly find out what you need to know."); ?>",
                messageSize: 'large',
                messagePosition: 'right',
                button: '<?php echo __('I can dig that'); ?>'
            },
            7: {
                highlight: {element: 'issue_main', blocked: true},
                message: "<h3><?php echo __('Issue main area'); ?></h3><?php echo __('This area contains all the main details, such as the description and reproductions steps.'); ?>",
                messageSize: 'medium',
                messagePosition: 'center',
                button: '<?php echo __('That makes sense'); ?>'
            },
            8: {
                highlight: {element: 'issue_main', blocked: true},
                message: "<h3><?php echo __('A few notes about editing issues'); ?></h3><?php echo __("Almost everything about an issue can be edited, and - depending on your workflow setup - some items may be locked from changes after a certain step (for example not being allowed to change the description on 'Confirmed' issues)."); ?>",
                messageSize: 'large',
                messagePosition: 'center',
                button: '<?php echo __('That also makes sense'); ?>'
            },
            9: {
                highlight: {element: 'issue_main', blocked: true},
                message: "<h3><?php echo __('A few notes about editing issues'); ?></h3><?php echo __('To edit anything about an issue, move your mouse over the detail you want to change and press the edit icon that appears (usually to the left).'); ?><br><br><?php echo __("Any changes you make are temporary until you press the 'Save changes' button below the title (this button appears only when you have unsaved changes)."); ?>",
                messageSize: 'large',
                messagePosition: 'center',
                button: '<?php echo __('I see'); ?>'
            },
            10: {
                highlight: {element: 'issue_main', blocked: true},
                message: "<h3><?php echo __('A few notes about editing issues'); ?></h3><?php echo __('This does not apply to workflow transitions - which happens instantly when you press a workflow button. However, most workflows allows you to post comments or change details about the issue during the workflow.'); ?><br><br><?php echo __("Keep in mind that most of this can be configured through the workflow configuration."); ?>",
                messageSize: 'large',
                messagePosition: 'center',
                button: "<?php echo __("I think I'm good to go"); ?>"
            },
            11: {
                message: "<h3><?php echo __('Good to go!'); ?></h3><?php echo __("That's all for this tutorial. Don't forget that you can always get help for any page by using the 'Help' menu entry in your user menu at the top right corner."); ?><br><br><?php echo __('Have fun using The Bug Genie!'); ?>",
                messageSize: 'large',
                messagePosition: 'center',
                button: '<?php echo __("I most certainly will!"); ?>'
            }
        };
        TBG.Tutorial.start('viewissue');
    });
</script>
