<textarea name="<?php echo $area_name; ?>" id="<?php echo (isset($area_id)) ? $area_id : $area_name; ?>" style="height: <?php echo $height; ?>; width: <?php echo $width; ?>;"><?php echo $value; ?></textarea>
<?php if (!isset($hide_hint) || $hide_hint == false): ?>
	<div class="textarea_hint">
		<div class="header"><?php echo __('Formatting tips'); ?></div>
		<p>
			<?php
			
				switch (rand(1, 9))
				{
					case 1:
						echo __('URLs will be automatically transformed to links. If you want to add a title, use the link formatter: [[http://awesome.com/something An awesome link]].');
						break;
					case 2:
						echo __('To auto-link to an existing issue, write "issue", "bug", "ticket" or the issue type (like "bug report" or "enhancement") followed by the issue number (ex: "%ticket_example_1%" or "%ticket_example_2%"). Enclose sourcecode samples in <source></source>; tags.', array('%ticket_example_1%' => '<span class="example">ticket #3</span>', '%ticket_example_2%' => '<span class="example">bug report MYPROJ-1</span>'));
						break;
					case 3:
						echo __('To create a bulleted list, start one or more lines with a star ("* bulleted list item"). You can create a numbered list by using the hash/pund sign instead ("# numbered list item").');
						break;
					case 4:
						echo __("If you have certain text you don't want to be formatted, enclose it in <nowiki></nowiki> tags: \"'''formatted''' text <nowiki>'''non-formatted''' text</nowiki>.");
						break;
					case 5:
						echo __('Code samples are best presented in <source></source> tags. The Bug Genie uses %geshi% for syntax highlighting with support for over 100 languages! (<source lang="php"><?php echo "fu"; ?></source>).', array('%geshi%' => link_tag('http://qbnz.com/highlighter/', 'GeSHi')));
						break;
					case 6:
						echo __('You can use simple formatting tags for underlined text (<b></b>) or strikethrough (<strike></strike>).');
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
				}
			
			?><br>
			<?php echo __('See more formatting tips in %wiki_formatting%.', array('%wiki_formatting%' => link_tag(make_url('publish_article', array('article_name' => 'WikiFormatting')), 'WikiFormatting', array('target' => '_new')))); ?>
		</p>
	</div>
<?php endif; ?>
