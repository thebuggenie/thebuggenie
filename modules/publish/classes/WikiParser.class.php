<?php
/* WikiParser
 * Version 1.0
 * Copyright 2005, Steve Blinch
 * http://code.blitzaffe.com
 *
 * This class parses and returns the HTML representation of a document containing
 * basic MediaWiki-style wiki markup.
 *
 *
 * USAGE
 *
 * Refer to class_WikiRetriever.php (which uses this script to parse fetched
 * wiki documents) for an example.
 *
 *
 * LICENSE
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 *
 *
 */

class WikiParser {

	protected $preformat = null;
	protected $emphasis = array();
	protected $suppress_linebreaks = false;
	protected $use_toc = false;
	protected $toc = array();
	protected $base_id = null;

	function __construct($use_toc = false, $base_id = null) {
		$this->use_toc = $use_toc;
		$this->base_id = $base_id;
		if (BUGScontext::getCurrentProject() instanceof BUGSproject)
		{
			$this->namespace = BUGScontext::getCurrentProject()->getKey();
		}
		$this->reference_wiki = 'http://thebuggenie/';
		$this->image_uri = '';
		$this->ignore_images = true;
	}

	function handle_sections($matches) {
		$level = strlen($matches[1]);
		$content = $matches[2];

		$this->stop = true;
		
		// avoid accidental run-on emphasis
		$retval = $this->emphasize_off() . "\n";

		$retval .= "<h{$level}";
		if ($this->use_toc)
		{
			$id = $this->base_id . '_toc_' . (count($this->toc) + 1);
			$this->toc[] = array('level' => $level, 'content' => $content, 'id' => $id);
			$retval .= " id=\"{$id}\"";
		}
		$retval .= ">{$content}&nbsp;<a href=\"#top\">&uArr;&nbsp;".__('top')."</a></h{$level}>\n";

		return $retval;
	}

	function handle_newline($matches) {
		if ($this->suppress_linebreaks) return $this->emphasize_off();

		$this->stop = true;
		// avoid accidental run-on emphasis
		return $this->emphasize_off() . "<br><br>";
	}

	function handle_list($matches,$close=false) {

		$listtypes = array(
			'*'=>'ul',
			'#'=>'ol',
		);

		$output = "";

		$newlevel = ($close) ? 0 : strlen($matches[1]);

		while ($this->list_level!=$newlevel) {
			$listchar = substr($matches[1],-1);
			if (is_string($listchar) || is_numeric($listchar))
			{
				$listtype = $listtypes[$listchar];
			}
			else
			{
				$listtype = 'ul';
			}

			//$output .= "[".$this->list_level."->".$newlevel."]";

			if ($this->list_level>$newlevel) {
				$listtype = '/'.array_pop($this->list_level_types);
				$this->list_level--;
			} else {
				$this->list_level++;
				array_push($this->list_level_types,$listtype);
			}
			$output .= "\n<{$listtype}>\n";
		}

		if ($close) return $output;

		$output .= "<li>".$matches[2]."</li>\n";

		return $output;
	}

	function handle_definitionlist($matches,$close=false) {

		if ($close) {
			$this->deflist = false;
			return "</dl>\n";
		}


		$output = "";
		if (!$this->deflist) $output .= "<dl>\n";
		$this->deflist = true;

		switch($matches[1]) {
			case ';':
				$term = $matches[2];
				$p = strpos($term,' :');
				if ($p!==false) {
					list($term,$definition) = explode(':',$term);
					$output .= "<dt>{$term}</dt><dd>{$definition}</dd>";
				} else {
					$output .= "<dt>{$term}</dt>";
				}
				break;
			case ':':
				$definition = $matches[2];
				$output .= "<dd>{$definition}</dd>\n";
				break;
		}

		return $output;
	}

	function handle_preformat($matches,$close=false) {
		if ($close) {
			$this->preformat = false;
			return "</pre>\n";
		}

		$this->stop_all = true;

		$output = "";
		if (!$this->preformat) $output .= "<pre>";
		$this->preformat = true;

		$output .= $matches[1];

		return $output."\n";
	}

	function handle_horizontalrule($matches) {
		return "<hr />";
	}

	function handle_underline($matches) {
		return "<u>".$matches[1]."</u>";
	}

	function wiki_link($topic) {
		return ucfirst(str_replace(' ','_',$topic));
	}

	function handle_image($href,$title,$options) {
		if ($this->ignore_images) return "";
		if (!$this->image_uri) return $title;

		$href = $this->image_uri . $href;

		$imagetag = sprintf(
			'<img src="%s" alt="%s" />',
			$href,
			$title
		);
		foreach ($options as $k=>$option) {
			switch($option) {
				case 'frame':
					$imagetag = sprintf(
						'<div style="float: right; background-color: #F5F5F5; border: 1px solid #D0D0D0; padding: 2px">'.
						'%s'.
						'<div>%s</div>'.
						'</div>',
						$imagetag,
						$title
					);
					break;
				case 'right':
					$imagetag = sprintf(
						'<div style="float: right">%s</div>',
						$imagetag
					);
					break;
			}
		}

		return $imagetag;
	}

	function handle_internallink($matches) {
		//var_dump($matches);
		$nolink = false;
		$newwindow = false;

		$href = $matches[4];
		//var_dump($href);
		if (isset($matches[6]) && $matches[6])
		{
			$title = $matches[6];
		}
		else
		{
			$title = $href;
			if (isset($matches[7]) && $matches[7])
			{
				$title .= $matches[7];
			}
		}
		$namespace = $matches[3];

		if ($namespace=='Image') {
			$options = explode('|',$title);
			$title = array_pop($options);

			return image_tag($href, array('alt' => $title)); // $this->handle_image($href,$title,$options);
		}

		if ($namespace=='Wikipedia') {
			$options = explode('|',$title);
			$title = array_pop($options);

			return link_tag('http://wikipedia.org/wiki/'.$href, $title); // $this->handle_image($href,$title,$options);
		}

		if ($namespace=='TBG') {
			$options = explode('|',$title);
			$title = array_pop($options);

			return link_tag(make_url($href), $title); // $this->handle_image($href,$title,$options);
		}

		if ($namespace=='LOCAL') {
			$options = explode('|',$title);
			$title = array_pop($options);

			return link_tag($href, $title); // $this->handle_image($href,$title,$options);
		}

		$title = preg_replace('/\(.*?\)/','',$title);
		$title = preg_replace('/^.*?\:/','',$title);

		if (!$namespace)
		{
			//var_dump($href);
			$href = BUGScontext::getRouting()->generate('publish_article', array('article_name' => $this->wiki_link($href)));
		}
		else
		{
			$href = $namespace.':'.$this->wiki_link($href);
		}

		if ($nolink) return $title;

		$options = ($newwindow) ? array('target' => "_blank") : array();
		/*if (strlen($href) > 18)
		{
			var_dump($href);var_dump($title);var_dump($options);
			var_dump(link_tag($href, $title, $options));
			die();
		}*/
		return link_tag($href, $title, $options);

		/*return sprintf(
			'<a href="%s"%s>%s</a>',
			$href,
			($newwindow?' target="_blank"':''),
			$title
		);*/
	}

	function handle_externallink($matches) {
		$href = $matches[2];
		$title = null;
		$title = (array_key_exists(3, $matches)) ? $matches[3] : $matches[2];
		if (!$title) {
			$this->linknumber++;
			$title = "[{$this->linknumber}]";
		}
		$newwindow = true;

		return sprintf(
			'<a href="%s"%s>%s</a>',
			$href,
			($newwindow?' target="_blank"':''),
			$title
		);
	}

	function emphasize($amount) {
		$amounts = array(
			2=>array('<em>','</em>'),
			3=>array('<strong>','</strong>'),
			4=>array('<strong>','</strong>'),
			5=>array('<em><strong>','</strong></em>'),
		);

		$output = "";

		// handle cases where emphasized phrases end in an apostrophe, eg: ''somethin'''
		// should read <em>somethin'</em> rather than <em>somethin<strong>
		if ( (!isset($this->emphasis[$amount]) || (isset($this->emphasis[$amount]) && !$this->emphasis[$amount])) && (isset($this->emphaasis[$amount]) && $this->emphasis[$amount-1]) ) {
			$amount--;
			$output = "'";
		}

		$offset = (isset($this->emphasis[$amount])) ? (int) $this->emphasis[$amount] : 0;
		$output .= $amounts[$amount][$offset];

		$this->emphasis[$amount] = !$offset;

		return $output;
	}

	function handle_emphasize($matches) {
		$amount = strlen($matches[1]);
		return $this->emphasize($amount);

	}

	function emphasize_off() {
		$output = "";
		if (count($this->emphasis))
		{
			foreach ($this->emphasis as $amount=>$state) {
				if ($state) $output .= $this->emphasize($amount);
			}
		}

		return $output;
	}

	function handle_eliminate($matches) {
		return "";
	}

	function handle_variable($matches) {
		switch($matches[2]) {
			case 'CURRENTMONTH': return date('m');
			case 'CURRENTMONTHNAMEGEN':
			case 'CURRENTMONTHNAME': return date('F');
			case 'CURRENTDAY': return date('d');
			case 'CURRENTDAYNAME': return date('l');
			case 'CURRENTYEAR': return date('Y');
			case 'CURRENTTIME': return date('H:i');
			case 'NUMBEROFARTICLES': return 0;
			case 'PAGENAME': return BUGScontext::getResponse()->getPage();
			case 'NAMESPACE': return 'None';
			case 'TOC': return '{{TOC}}';
			case 'SITENAME': return BUGSsettings::getTBGname();
			case 'SITETAGLINE': return BUGSsettings::getTBGtagline();
			default: return '';
		}
	}

	function parse_line($line) {
		$line_regexes = array(
			'preformat'=>'^\s{2}(.*?)$',
			'definitionlist'=>'^([\;\:])\s*(.*?)$',
			'newline'=>'^$',
			'list'=>'^([\*\#]+)(.*?)$',
			'sections'=>'^(={1,6})(.*?)(={1,6})$',
			'horizontalrule'=>'^----$',
		);
		$char_regexes = array(
//			'link'=>'(\[\[((.*?)\:)?(.*?)(\|(.*?))?\]\]([a-z]+)?)',
			'internallink'=>'('.
				'\[\['. // opening brackets
					'(([^\]]*?)\:)?'. // namespace (if any)
					'([^\]]*?)'. // target
					'(\|([^\]]*?))?'. // title (if any)
				'\]\]'. // closing brackets
				'([a-z]+)?'. // any suffixes
				')',
			'externallink'=>'('.
				'\['.
					'([^\]]*?)'.
					'(\s+[^\]]*?)?'.
				'\]'.
				')',
			'emphasize'=>'(\'{2,5})',
			'eliminate'=>'(__NOTOC__|__NOEDITSECTION__)',
			'variable'=>'('. '\{\{' . '([^\}]*?)' . '\}\}' . ')',
		);

		$this->stop = false;
		$this->stop_all = false;

		$called = array();
		$line = rtrim($line);

		foreach ($line_regexes as $func=>$regex) {
			if (preg_match("/$regex/i",$line,$matches)) {
				$called[$func] = true;
				$func = "handle_".$func;
				$line = $this->$func($matches);
				if ($this->stop || $this->stop_all) break;
			}
		}
		if (!$this->stop_all) {
			$this->stop = false;
			foreach ($char_regexes as $func=>$regex) {
				$line = preg_replace_callback("/$regex/i",array(&$this,"handle_".$func),$line);
				if ($this->stop) break;
			}
		}

		$isline = strlen(trim($line))>0;

		// if this wasn't a list item, and we are in a list, close the list tag(s)
		if (($this->list_level>0) && !array_key_exists('list', $called)) $line = $this->handle_list(false,true) . $line;
		if ($this->deflist && !array_key_exists('definitionlist', $called)) $line = $this->handle_definitionlist(false,true) . $line;
		if ($this->preformat && !array_key_exists('preformat', $called)) $line = $this->handle_preformat(false,true) . $line;

		// suppress linebreaks for the next line if we just displayed one; otherwise re-enable them
		if ($isline) $this->suppress_linebreaks = (array_key_exists('newline', $called) || array_key_exists('sections', $called));
		//if ($isline) $line .= $this->handle_newline(array());

		return $line;
	}

	function test() {
		$text = "WikiParser stress tester. <br /> Testing...
{{TOC}}

== Nowiki test ==
<nowiki>[[wooticles|narf]] and '''test''' and stuff.</nowiki>

== Character formatting ==
This is ''emphasized'', this is '''really emphasized''', this is ''''grossly emphasized'''',
and this is just '''''freeking insane'''''.
Done.

== Variables ==
{{CURRENTDAY}}/{{CURRENTMONTH}}/{{CURRENTYEAR}}
Done.

== Image test ==
[[:Image:bao1.jpg]]
[[Image:bao1.jpg|frame|alternate text]]
[[Image:bao1.jpg|right|alternate text]]
Done.

== Horizontal Rule ==
Above the rule.
----
Done.

== Hyperlink test ==
This is a [[namespace:link target|bitchin hypalink]] to another document for [[click]]ing, with [[(some) hidden text]] and a [[namespace:hidden namespace]].

A link to an external site [http://www.google.ca] as well another [http://www.esitemedia.com], and a [http://www.blitzaffe.com titled link] -- woo!
Done.

== Preformat ==
Not preformatted.
 Totally preformatted 01234    o o
 Again, this is preformatted    b    <-- It's a face
 Again, this is preformatted   ---'
Done.

== Bullet test ==
* One bullet
* Another '''bullet'''
*# a list item
*# another list item
*#* unordered, ordered, unordered
*#* again
*# back down one
Done.

== Definition list ==
; yes : opposite of no
; no : opposite of yes
; maybe
: somewhere in between yes and no
Done.

== Indent ==
Normal
: indented woo
: more indentation
Done.

";
		return $this->parse($text);
	}

	function parse($text,$title="") {
		$this->redirect = false;

		$this->nowikis = array();
		$this->list_level_types = array();
		$this->list_level = 0;

		$this->deflist = false;
		$this->linknumber = 0;
		$this->suppress_linebreaks = false;

		$this->page_title = $title;

		$output = "";

		$text = preg_replace_callback('/<nowiki>([\s\S]*)<\/nowiki>/i',array($this,"handle_save_nowiki"),$text);

		$lines = explode("\n",$text);

		if (preg_match('/^\#REDIRECT\s+\[\[(.*?)\]\]$/',trim($lines[0]),$matches)) {
			$this->redirect = $matches[1];
		}

		foreach ($lines as $k=>$line) {
			$line = $this->parse_line($line);
			$output .= $line;
		}

		$output = preg_replace_callback('/{{TOC}}/',array($this,"handle_add_toc"), $output);

		$output = preg_replace_callback('/<nowiki><\/nowiki>/i',array($this,"handle_restore_nowiki"),$output);


		return $output;
	}

	function handle_add_toc($matches)
	{
		return BUGSaction::returnTemplateHTML('publish/toc', array('toc' => $this->toc));
	}

	function handle_save_nowiki($matches) {
		array_push($this->nowikis,$matches[1]);
		return "<nowiki></nowiki>";
	}

	function handle_restore_nowiki($matches) {
		return array_pop($this->nowikis);
	}
}
?>