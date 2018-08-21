<?php

    namespace thebuggenie\core\helpers;

    use \Michelf\MarkdownExtra;
    use thebuggenie\core\entities\traits\TextParserTodo;
    use thebuggenie\core\framework;

    /**
     * Text parser class, markdown syntax
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage main
     */

    /**
     * Text parser class, markdown syntax
     *
     * @package thebuggenie
     * @subpackage main
     */
    class TextParserMarkdown extends MarkdownExtra implements ContentParser
    {
        use TextParserTodo;

        /**
         * An array of mentioned users
         * 
         * @var array|\thebuggenie\core\entities\User
         */
        protected $mentions = array();

        protected $options = [];
        
        public $code_attr_on_pre = true;

        protected function _parse_line($text, $options = [])
        {
            return $text;
        }

        public function transform($text)
        {
            $this->no_markup = true;
            $this->no_entities = true;

            $text = preg_replace_callback(\thebuggenie\core\helpers\TextParser::getIssueRegex(), array($this, '_parse_issuelink'), $text);
            $text = parent::transform($text);
            $text = preg_replace_callback('/^(?:\<(.*?)\>)?(\[(?P<closed>x)?\] )(?P<text>.*?)(?:\<(.*?)\>)?$/mi', [$this, '_parse_todo'], $text);
            $text = preg_replace_callback(\thebuggenie\core\helpers\TextParser::getMentionsRegex(), array($this, '_parse_mention'), $text);
            $text = preg_replace_callback(self::getStrikethroughRegex(), array($this, '_parse_strikethrough'), $text);

            $parameters = array();
            if (isset($this->options['target'])) $parameters['target'] = $this->options['target'];
            $event = framework\Event::createNew('core', 'thebuggenie\core\framework\helpers\TextParserMarkdown::transform', $this, $parameters);
            $event->trigger();

            foreach ($event->getReturnList() as $regex) {
                $text = preg_replace_callback($regex[0], $regex[1], $text);
            }

            return $text;
        }

        protected function _parse_issuelink($matches)
        {
            return \thebuggenie\core\helpers\TextParser::parseIssuelink($matches, true);
        }

        protected function doHardBreaks($text)
        {
            return preg_replace_callback('/ {2,}\n|\n{1}/', array(&$this, '_doHardBreaks_callback'), $text);
        }

        protected function _parse_mention($matches)
        {
            $user = \thebuggenie\core\entities\tables\Users::getTable()->getByUsername($matches[1]);
            if ($user instanceof \thebuggenie\core\entities\User)
            {
                $output = framework\Action::returnComponentHTML('main/userdropdown_inline', array('user' => $matches[1], 'in_email' => isset($this->options['in_email']) ? $this->options['in_email'] : false));
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
            $user_id = ($user instanceof \thebuggenie\core\entities\User) ? $user->getID() : $user;
            
            return array_key_exists($user_id, $this->mentions);
        }

        public static function getStrikethroughRegex()
        {
            return array('/~~(.+?)~~/');
        }

        protected function _parse_strikethrough($matches)
        {
            if (! isset($matches[1])) return $matches[0];

            return '<strike>'.$matches[1].'</strike>';
        }

    }
