<?php

	function _return_article_link_tag($article_name)
	{
		return link_tag(make_url('publish_article', array('article_name' => $article_name)), $article_name);
	}

	function publish_parse($text, $toc = false, $article_id = null)
	{
		// Perform wiki parsing
		$wiki_parser = new WikiParser($toc, 'article_' . $article_id);
		//return $wiki_parser->test();
		$text = $wiki_parser->parse($text);

		// Do camelcasing parsing
		$text = preg_replace('/(?<!\!)\b[A-Z]+[a-z]+[A-Z][A-Za-z]*\b/e', '_return_article_link_tag("\\0")', $text);

		return $text;
	}