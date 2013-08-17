<?php

	use \Michelf\MarkdownExtra;

	/**
	 * Text parser class, markdown syntax
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage main
	 */

	/**
	 * Text parser class, markdown syntax
	 *
	 * @package thebuggenie
	 * @subpackage main
	 */
	class TBGTextParserMarkdown extends \Michelf\MarkdownExtra
	{

		public $code_attr_on_pre = true;

		public function transform($text)
		{
			$text = parent::transform($text);

			$text = preg_replace_callback(TBGTextParser::getIssueRegex(), array($this, '_parse_issuelink'), $text);

			return $text;
		}

		protected function _parse_issuelink($matches)
		{
			return TBGTextParser::parseIssuelink($matches);
		}

		protected function doHardBreaks($text)
		{
			return preg_replace_callback('/ {2,}\n|\n{1}/', array(&$this, '_doHardBreaks_callback'), $text);
		}

	}
