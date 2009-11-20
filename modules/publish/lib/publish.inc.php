<?php

	function get_spaced_name($camelcased)
	{
		return preg_replace('/(?<=[a-z])(?=[A-Z])/',' ', $camelcased);
	}

	function _return_article_link_tag($article_name)
	{
		return link_tag(make_url('publish_article', array('article_name' => $article_name)), get_spaced_name($article_name));
	}

	function publish_parse($text, $toc = false, $article_id = null)
	{
		$text = common_text_replacements($text);
		
		// Perform wiki parsing
		$wiki_parser = new WikiParser($toc, 'article_' . $article_id);
		//return $wiki_parser->test();
		$text = $wiki_parser->parse($text);

		// Do camelcasing parsing
		if (BUGScontext::getModule('publish')->getSetting('allow_camelcase_links'))
		{
			$text = preg_replace('/(?<![\!|\"|\[|\>|\/\:])\b[A-Z]+[a-z]+[A-Z][A-Za-z]*\b/e', '_return_article_link_tag("\\0")', $text);
		}
		//var_dump(substr($text, strpos($text, 'InternalPage') - 20, 200));
		$text = preg_replace('/(?<!")\![A-Z]+[a-z]+[A-Z][A-Za-z]*\b/e', 'substr("\\0", 1)', $text);
		//var_dump(substr($text, strpos($text, 'InternalPage') - 20, 200));

		return $text;
	}