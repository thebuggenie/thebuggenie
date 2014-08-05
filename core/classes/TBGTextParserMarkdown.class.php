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
	class TBGTextParserMarkdown extends \Michelf\MarkdownExtra implements TBGContentParser
	{

		/**
		 * An array of mentioned users
		 * 
		 * @var array|TBGUser
		 */
		protected $mentions = array();
		
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

		protected function _parse_mention($matches)
		{
			$user = TBGUsersTable::getTable()->getByUsername($matches[1]);
			if ($user instanceof TBGUser)
			{
				$output = TBGAction::returnComponentHTML('main/userdropdown', array('user' => $matches[1], 'displayname' => $matches[0]));
				$this->mentions[$user->getID()] = $user;
			}
			else
			{
				$output = $matches[0];
			}
			
			return $output;
		}
		
		public function getMentions()
		{
			return $this->mentions;
		}
		
		public function hasMentions()
		{
			return (bool) count($this->mentions);
		}
		
		public function isMentioned($user)
		{
			$user_id = ($user instanceof TBGUser) ? $user->getID() : $user;
			
			return array_key_exists($user_id, $this->mentions);
		}

	}
