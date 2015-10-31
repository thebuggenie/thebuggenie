<?php
    $markuppable = (!isset($markuppable)) ? true : $markuppable;

    if ($markuppable)
    {
        $syntax = (isset($syntax)) ? $syntax : \thebuggenie\core\framework\Settings::SYNTAX_MW;
        if (is_numeric($syntax)) $syntax = \thebuggenie\core\framework\Settings::getSyntaxClass($syntax);
    }
    else
    {
        $syntax = \thebuggenie\core\framework\Settings::getSyntaxClass(\thebuggenie\core\framework\Settings::SYNTAX_MD);
    }

    switch ($syntax)
    {
        case 'mw':
            $syntaxname = __('Mediawiki');
            break;
        case 'md':
            $syntaxname = __('Markdown');
            break;
        case 'pt':
            $syntaxname = __('Plaintext');
            break;
    }
    $base_id = (isset($area_id)) ? $area_id : $area_name;
    $mentionable = isset($target_type) && isset($target_id);

?>
<div class="textarea_container syntax_<?php echo $syntax; ?>">
    <div class="syntax_picker_container">
        <input type="hidden" id="<?php echo $base_id; ?>_syntax" name="<?php echo $area_name; ?>_syntax" value="<?php echo \thebuggenie\core\framework\Settings::getSyntaxValue($syntax); ?>">
        <a id="<?php echo $base_id; ?>_toggle_syntax_button" class="button button-silver syntax_picker dropper"><?php echo __('Selected syntax: %selected_syntax', array('%selected_syntax' => '<span id="'.$base_id.'_selected_syntax">'.$syntaxname.'</span>')); ?>&nbsp;&nbsp;<span style="font-size: 0.8em; float: right;">&#x25BC;</span></a>
        <ul id="<?php echo $base_id; ?>_syntax_picker" class="simple_list rounded_box white shadowed more_actions_dropdown popup_box" onclick="$(this).previous().toggleClassName('button-pressed');TBG.Main.Profile.clearPopupsAndButtons();" style="display: none;">
            <li class="mw <?php if ($syntax == 'mw') echo 'selected'; ?>" data-syntax-name="<?php echo __('Mediawiki'); ?>"><a href="javascript:void(0);" onclick="TBG.Main.Helpers.setSyntax('<?php echo $base_id; ?>', 'mw');"><?php echo __('Mediawiki syntax'); ?></a></li>
            <li class="md <?php if ($syntax == 'md') echo 'selected'; ?>" data-syntax-name="<?php echo __('Markdown'); ?>"><a href="javascript:void(0);" onclick="TBG.Main.Helpers.setSyntax('<?php echo $base_id; ?>', 'md');"><?php echo __('Markdown syntax'); ?></a></li>
            <li class="pt <?php if ($syntax == 'pt') echo 'selected'; ?>" data-syntax-name="<?php echo __('Plaintext'); ?>"><a href="javascript:void(0);" onclick="TBG.Main.Helpers.setSyntax('<?php echo $base_id; ?>', 'pt');"><?php echo __('Plain text'); ?></a></li>
        </ul>
    </div>
    <textarea name="<?php echo $area_name; ?>" id="<?php echo $base_id; ?>" <?php if ($mentionable): ?> data-target-type="<?php echo $target_type; ?>" data-target-id="<?php echo $target_id; ?>" <?php endif; ?> class="syntax_<?php echo $syntax; ?> <?php if ($markuppable) echo ' markuppable'; ?> <?php if ($mentionable) echo ' mentionable'; ?>" style="<?php if (isset($height)) echo 'height: '.$height; ?>; width: <?php echo $width; ?>;"><?php echo $value; ?></textarea>
    <?php if (!isset($hide_hint) || $hide_hint == false): ?>
        <div class="textarea_hint">
            <p class="syntax_pt_hint hint_container">
                <?php

                    switch (rand(1, 2))
                    {
                        case 1:
                            echo __("Don't worry about URLs - they will automatically be transformed to links.");
                            break;
                        case 2:
                            echo str_replace(array('%ticket_example_1', '%ticket_example_2'), array('<span class="example">ticket #3</span>', '<span class="example">bug report MYPROJ-1</span>'), __('To auto-link to an existing issue, write "issue", "bug", "ticket" or the issue type (like "bug report" or "enhancement") followed by the issue number (ex: "%ticket_example_1" or "%ticket_example_2"). Enclose sourcecode samples between two "~~~~" lines.'));
                            break;
                    }

                ?><br>
                <?php echo __('See more formatting tips in %markdown_formatting.', array('%markdown_formatting' => '<a href="issues.thebuggenie.com/wiki/MarkdownFormatting" target="_new" tabindex="-1">MarkdownFormatting</a>')); ?>
            </p>
            <p class="syntax_md_hint hint_container">
                <?php

                    switch (rand(1, 6))
                    {
                        case 1:
                            echo __("Don't worry about URLs - they will automatically be transformed to links.");
                            break;
                        case 2:
                            echo str_replace(array('%ticket_example_1', '%ticket_example_2'), array('<span class="example">ticket #3</span>', '<span class="example">bug report MYPROJ-1</span>'), __('To auto-link to an existing issue, write "issue", "bug", "ticket" or the issue type (like "bug report" or "enhancement") followed by the issue number (ex: "%ticket_example_1" or "%ticket_example_2"). Enclose sourcecode samples between two "~~~~" lines.'));
                            break;
                        case 3:
                            echo __('To create a bulleted list, start one or more lines with a star ("* bulleted list item"). You can create a numbered list by using the hash/pund sign instead ("# numbered list item").');
                            break;
                        case 4:
                            echo __('Code samples are best presented between two "~~~~"-lines.') . ' ' . __('The Bug Genie uses %geshi for syntax highlighting with support for over 100 languages!', array('%geshi' => link_tag('http://qbnz.com/highlighter/', 'GeSHi', array('tabindex' => '-1'))));
                            echo htmlspecialchars(" (<source lang=\"php\"><?php echo \"fu\"; ?></source>)");
                            break;
                        case 5:
                            echo __('To specify bold text put text between two stars, and for underlines put text between two underscores.');
                            break;
                        case 6:
                            echo __('The Bug Genie uses an enhanced flavor of the markdown syntax, which makes each newline count. If you want to add a linebreak, add a new line and it will be presented just like you type.');
                            break;
                    }

                ?><br>
                <?php echo __('See more formatting tips in %markdown_formatting.', array('%markdown_formatting' => '<a href="issues.thebuggenie.com/wiki/MarkdownFormatting" target="_new" tabindex="-1">MarkdownFormatting</a>')); ?>
            </p>
            <p class="syntax_mw_hint hint_container">
                <?php

                    switch (rand(1, 10))
                    {
                        case 1:
                            echo __('URLs will be automatically transformed to links. If you want to add a title, use the link formatter: [[http://awesome.com/something An awesome link]].');
                            break;
                        case 2:
                            echo str_replace(array('%ticket_example_1', '%ticket_example_2'), array('<span class="example">ticket #3</span>', '<span class="example">bug report MYPROJ-1</span>'), __('To auto-link to an existing issue, write "issue", "bug", "ticket" or the issue type (like "bug report" or "enhancement") followed by the issue number (ex: "%ticket_example_1" or "%ticket_example_2"). Enclose sourcecode samples in <source></source> tags.'));
                            break;
                        case 3:
                            echo __('To create a bulleted list, start one or more lines with a star ("* bulleted list item"). You can create a numbered list by using the hash/pund sign instead ("# numbered list item").');
                            break;
                        case 4:
                            echo __("If you have certain text you don't want to be formatted, enclose it in <nowiki></nowiki> tags: \"'''formatted''' text <nowiki>'''non-formatted''' text</nowiki>.");
                            break;
                        case 5:
                            echo __('Code samples are best presented in <source></source> tags.') . ' ' . __('The Bug Genie uses %geshi for syntax highlighting with support for over 100 languages!', array('%geshi' => link_tag('http://qbnz.com/highlighter/', 'GeSHi', array('tabindex' => '-1'))));
                            echo htmlspecialchars(" (<source lang=\"php\"><?php echo \"fu\"; ?></source>)");
                            break;
                        case 6:
                            echo __("You can use simple formatting tags for underlined text (<b></b>) or strikethrough (<strike></strike>).");
                            break;
                        case 7:
                            echo __('You can create a link directly to a wikipedia page by using the "WIKIPEDIA:" link namespace: "Here is a link to the wikipedia article about [[WIKIPEDIA:Norway|Norway]].');
                            break;
                        case 8:
                            echo __('If you want to put a horizontal line in the document, use four dashes: "----".');
                            break;
                        case 9:
                            echo __('The Bug Genie will automatically add new lines when you add two new lines in your text, whereas one new line is used for formatting as you type. To force a new line, use the <br> tag: "There is a new<br>line here".');
                            break;
                        case 10:
                            echo __('If you want to add text that is not supposed to be parsed, put it inside <nowiki> tags, or start the line with a space.');
                            break;
                    }

                ?><br>
                <?php echo __('See more formatting tips in %wiki_formatting.', array('%wiki_formatting' => link_tag(make_url('publish_article', array('article_name' => 'WikiFormatting')), 'WikiFormatting', array('target' => '_new', 'tabindex' => '-1')))); ?>
            </p>
        </div>
    <?php endif; ?>
</div>
<script type="text/javascript">
    require(['thebuggenie/tbg', 'domReady', 'mention'], function (TBG, domReady, mention) {
        domReady(function () {
            $("<?php echo $base_id; ?>").on('focus', function (e) {
                TBG.Main.initializeMentionable(e.target);
                var ec = this.up('.editor_container');
                if (ec != undefined)
                    ec.addClassName('focussed');
            });
            $("<?php echo $base_id; ?>").on('blur', function (e) {
                var ec = this.up('.editor_container');
                if (ec != undefined)
                    ec.removeClassName('focussed');
            });
            <?php if ($markuppable): ?>
            TBG.Main.Helpers.MarkitUp([$("<?php echo $base_id; ?>")]);
            <?php endif; ?>
        });
    });
</script>
