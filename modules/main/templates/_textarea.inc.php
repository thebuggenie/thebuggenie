<textarea name="<?php echo $area_name; ?>" id="<?php echo (isset($area_id)) ? $area_id : $area_name; ?>" style="height: <?php echo $height; ?>; width: <?php echo $width; ?>;"><?php echo $value; ?></textarea>
<?php if (!isset($hide_hint) || $hide_hint == false): ?>
	<div class="textarea_hint">
		<div class="header"><?php echo __('Formatting tips'); ?></div>
		<p>
			<?php
			
				switch (rand(2, 2))
				{
					case 1:
						echo htmlspecialchars(__('URLs will be automatically transformed to links. If you want to add a title, use the link formatter: [[http://awesome.com/something An awesome link]].'));
						break;
					case 2:
						echo str_replace(array('%ticket_example_1%', '%ticket_example_2%'), array('<span class="example">ticket #3</span>', '<span class="example">bug report MYPROJ-1</span>'), htmlspecialchars(__('To auto-link to an existing issue, write "issue", "bug", "ticket" or the issue type (like "bug report" or "enhancement") followed by the issue number (ex: "%ticket_example_1%" or "%ticket_example_2%"). Enclose sourcecode samples in <source></source> tags.')));
						break;
					case 3:
						echo htmlspecialchars(__('To create a bulleted list, start one or more lines with a star ("* bulleted list item"). You can create a numbered list by using the hash/pund sign instead ("# numbered list item").'));
						break;
					case 4:
						echo htmlspecialchars(__("If you have certain text you don't want to be formatted, enclose it in <nowiki></nowiki> tags: \"'''formatted''' text <nowiki>'''non-formatted''' text</nowiki>."));
						break;
					case 5:
						echo htmlspecialchars(__('Code samples are best presented in <source></source> tags.')) . ' ' . __('The Bug Genie uses %geshi% for syntax highlighting with support for over 100 languages!', array('%geshi%' => link_tag('http://qbnz.com/highlighter/', 'GeSHi', array('tabindex' => '-1'))));
						echo htmlspecialchars(" (<source lang=\"php\"><?php echo \"fu\"; ?></source>)");
						break;
					case 6:
						echo htmlspecialchars(__('You can use simple formatting tags for underlined text (<b></b>) or strikethrough (<strike></strike>).'));
						break;
					case 7:
						echo __('You can create a link directly to a wikipedia page by using the "WIKIPEDIA:" link namespace: "Here is a link to the wikipedia article about [[WIKIPEDIA:Norway|Norway]].');
						break;
					case 8:
						echo htmlspecialchars(__('If you want to put a horizontal line in the document, use four dashes: "----".'));
						break;
					case 9:
						echo htmlspecialchars(__('The Bug Genie will automatically add new lines when you add two new lines in your text, whereas one new line is used for formatting as you type. To force a new line, use the <br> tag: "There is a new<br>line here".'));
						break;
					case 10:
						echo htmlspecialchars(__('If you want to add text that is not supposed to be parsed, put it inside <nowiki> tags, or start the line with a space.'));
						break;
				}
			
			?><br>
			<?php echo __('See more formatting tips in %wiki_formatting%.', array('%wiki_formatting%' => link_tag(make_url('publish_article', array('article_name' => 'WikiFormatting')), 'WikiFormatting', array('target' => '_new', 'tabindex' => '-1')))); ?>
		</p>
	</div>
<?php endif; ?>
