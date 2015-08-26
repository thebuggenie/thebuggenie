<?php

    namespace thebuggenie\core\helpers;

    use thebuggenie\core\framework,
        thebuggenie\modules\publish\entities\tables\Articles,
        thebuggenie\modules\publish\entities\Article;

    /**
     * Text parser class
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage main
     */

    /**
     * Text parser class
     *
     * @package thebuggenie
     * @subpackage main
     */
    class TextParser implements ContentParser
    {

        protected static $additional_regexes = null;

        protected $preformat = null;
        protected $quote = null;
        protected $tablemode = null;
        protected $opentablecol = false;
        protected $options = array();
        protected $use_toc = false;
        protected $toc_base_id = null;
        protected $openblocks = array();
        protected $nowikis = array();
        protected $elinks = array();
        protected $ilinks = array();
        protected $codeblocks = array();
        protected $linknumber = 0;
        protected $internallinks = array();
        protected $categories = array();
        protected $mentions = array();
        protected $ignore_newline = false;
        protected $parsed_text = null;
        protected $toc = array();
        protected $text = null;

        /**
         * Add a regex to be parsed, with a function callback
         *
         * @param string $regex
         * @param callback $callback
         */
        public static function addRegex($regex, $callback)
        {
            if (self::$additional_regexes === null) self::$additional_regexes = array();
            self::$additional_regexes[] = array($regex, $callback);
        }

        /**
         * Return an array of the registered regexes to be parsed
         *
         * @return array
         */
        protected static function getRegexes()
        {
            if (self::$additional_regexes === null) self::$additional_regexes = array();
            return self::$additional_regexes;
        }

        /**
         * Returns an array of regular expressions that should be used for matching
         * the issue numbers and workflow transitions in a VCS commit.
         *
         * Each element of an array is a single regular expression that will be
         * applied against the incoming commit message. Each regular expression
         * should have two named patterns - one denoting the issue number (should
         * include prefix if used in project), and one denoting workflow
         * transitions.
         *
         * Simple example would be:
         *
         * '#fixes issue #(?P<issues>([A-Z0-9]+\-)?\d+) (?P<transitions> \(.*?\))?#i'
         *
         * @return array
         */
        public static function getIssueRegex()
        {
            // Try getting the regexes from cache first.
            if (!$regex = framework\Context::getCache()->get(framework\Cache::KEY_TEXTPARSER_ISSUE_REGEX))
            {
                // List of keywords that are expected to prefix the issue number in a
                // commit message (these are _not_ project prefixes).
                $issue_strings = array('bug', 'issue', 'ticket', 'fix', 'fixes', 'fixed', 'fixing', 'applies to', 'closes', 'references', 'ref', 'addresses', 're', 'see', 'according to', 'also see', 'story');

                // Add the issue types as prefixes as well.
                foreach (\thebuggenie\core\entities\Issuetype::getAll() as $issuetype)
                {
                    $issue_strings[] = $issuetype->getName();
                }

                // Construct the OR'ed (|) regex out of issue prefixes.
                $issue_string = join('|', $issue_strings);
                $issue_string = html_entity_decode($issue_string, ENT_QUOTES);
                $issue_string = str_replace(array(' ', "'"), array('\s{1,1}', "\'"), $issue_string);

                // Store all regular expressions for mathces in an array.
                $regex = array();

                // This regex will match messages that contain template like "KEYWORD
                // (#)ISSUE_NUMBER (TRANSITIONS)" (parenthesis means optional). For
                // example:
                // "Resolves issue #2 (Resolve issue)"
                $regex[] = '#( |^)(?<!\!)(('.$issue_string.')\s\#?(?P<issues>([A-Z0-9]+\-)?\d+))( \((?P<transitions>.*?)\))?#i';
                // This regex will match messages that contain template at the beginning
                // of message in format "ISSUE_NUMBER: (TRANSITIONS)".
                $regex[] = '#^(?<!\!)((?P<issues>([A-Z0-9]+\-)?\d+)):( \((?P<transitions>.*?)\))?#i';

                // Add the constructed regexes to cache.
                framework\Context::getCache()->add(framework\Cache::KEY_TEXTPARSER_ISSUE_REGEX, $regex);
            }

            // Return the regular expressions.
            return $regex;
        }

        public static function getMentionsRegex()
        {
            return '/\B\@([\w\-.]+)/i';
        }

        /**
         * Setup the parser object
         *
         * @param string $text The text to be parsed
         * @param boolean $use_toc [optional] Whether to use a TOC if found
         * @param string $toc_base_id [optional] Base id to use for the TOC element
         */
        public function __construct($text, $use_toc = false, $toc_base_id = null)
        {
            $this->text = str_replace("\r\n", "\n", $text);
            $this->use_toc = $use_toc;
            $this->toc_base_id = $toc_base_id;

            if (framework\Context::isProjectContext())
            {
                $this->namespace = framework\Context::getCurrentProject()->getKey();
            }
            if (!framework\Context::isCLI())
            {
                framework\Context::loadLibrary('ui');
            }
        }

        public function addInternalLinkOccurrence($article_name)
        {
            (!array_key_exists($article_name, $this->internallinks)) ? $this->internallinks[$article_name] = 1 : $this->internallinks[$article_name]++;
        }

        public function addCategorizer($category)
        {
            $this->categories[$category] = true;
        }

        protected function _parse_headers($matches)
        {
            if (array_key_exists('headers', $this->options) && !$this->options['headers'])
            {
                return $matches[0] . "\n";
            }

            $level = mb_strlen($matches[1]);
            $content = $matches[2];
            $this->stop = true;

            // avoid accidental run-on openblocks
            $retval = $this->_emphasize_off() . "\n";

            $retval .= "<h{$level}";
            if ($this->use_toc)
            {
                $id = $this->toc_base_id . '_toc_' . (count($this->toc) + 1);
                $this->toc[] = array('level' => $level, 'content' => $content, 'id' => $id);
                $retval .= " id=\"{$id}\"";
            }
            $retval .= ">" . $content;
            if (!isset($this->options['embedded']) || $this->options['embedded'] == false)
            {
                $retval .= "&nbsp;<a href=\"#top\">&uArr;&nbsp;".framework\Context::getI18n()->__('top')."</a>";
            }
            $retval .= "</h{$level}>\n";

            return $retval;
        }

        protected function _parse_newline($matches)
        {
            if ($this->ignore_newline) return $this->_emphasize_off();

            $this->stop = true;
            // avoid accidental run-on openblocks
            return $this->_emphasize_off() . "<br><br>";
        }

        protected function _parse_list($matches, $close = false)
        {
            $listtypes = array('*' => 'ul', '#' => 'ol');
            $output = "";

            $newlevel = ($close) ? 0 : mb_strlen($matches[1]);

            while ($this->list_level != $newlevel)
            {
                $listchar = mb_substr($matches[1], -1);
                if ((is_string($listchar) || is_numeric($listchar)) && array_key_exists($listchar, $listtypes))
                {
                    $listtype = $listtypes[$listchar];
                }
                else
                {
                    $listtype = 'ul';
                }

                if ($this->list_level >= $newlevel)
                {
                    $listtype = '/'.array_pop($this->list_level_types);
                    if ($this->list_level > $newlevel) $this->list_level--;
                }
                else
                {
                    $this->list_level++;
                    array_push($this->list_level_types, $listtype);
                }
                $output .= "\n<{$listtype}>\n";
            }

            if ($close)
            {
                return $output;
            }
            else
            {
                $output .= "<li>{$matches[2]}</li>\n";
                return $output;
            }
        }

        protected function _parse_definitionlist($matches, $close = false)
        {
            if ($close)
            {
                $this->deflist = false;
                return "</dl>\n";
            }

            $output = "";
            if (!$this->deflist) $output .= "<dl>\n";
            $this->deflist = true;

            switch ($matches[1])
            {
                case ';':
                    $term = $matches[2];
                    $p = mb_strpos($term, ' :');
                    if ($p !== false)
                    {
                        list($term, $definition) = explode(':', $term);
                        $output .= "<dt>{$term}</dt><dd>{$definition}</dd>";
                    }
                    else
                    {
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

        protected function _parse_preformat($matches, $close = false)
        {
            if ($close)
            {
                $this->preformat = false;
                return "</pre>\n";
            }

            $this->stop_all = true;

            $output = "";
            if (!$this->preformat) $output .= "<pre>";
            $this->preformat = true;

            $output .= $matches[0]; //htmlentities($matches[0]);

            return $output."\n";
        }

        protected function _parse_quote($matches, $close = false)
        {
            if ($close)
            {
                $this->quote = false;
                return "</blockquote>\n";
            }

            $this->stop_all = true;

            $output = "";
            if (!$this->quote) $output .= "<blockquote>";
            $this->quote = true;

            if ($matches[2])
                $output .= $matches[2]."<br>";

            return $output;
        }

        protected function _parse_horizontalrule($matches)
        {
            return "<hr />";
        }

        protected function _wiki_link($topic)
        {
            return ucfirst(str_replace(' ', '_', $topic));
        }

        protected function _parse_image($href,$title,$options)
        {
            // if ($this->ignore_images) return "";
            // if (!$this->image_uri) return $title;

            // $href = $this->image_uri . $href;

            $imagetag = sprintf('<img src="%s" alt="%s" />', $href, $title);
            foreach ($options as $k=>$option)
            {
                switch($option)
                {
                    case 'frame':
                        $imagetag = sprintf('<div style="float: right; background-color: #F5F5F5; border: 1px solid #D0D0D0; padding: 2px">%s<div>s</div></div>', $imagetag, $title);
                        break;
                    case 'right':
                        $imagetag = sprintf('<div style="float: right">%s</div>', $imagetag);
                        break;
                }
            }

            return $imagetag;
        }

        protected function _parse_internallink($matches)
        {
            $href = html_entity_decode($matches[4], ENT_QUOTES, 'UTF-8');

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

            if (mb_strtolower($namespace) == 'category')
            {
                if (mb_substr($matches[2], 0, 1) != ':')
                {
                    $this->addCategorizer($href);
                    return '';
                }
            }

            if (mb_strtolower($namespace) == 'wikipedia')
            {
                if (framework\Context::isCLI()) return $href;

                $options = explode('|', $title);
                $title = (array_key_exists(5, $matches) && (mb_strpos($matches[5], '|') !== false) ? '' : $namespace.':') . array_pop($options);

                return link_tag('http://en.wikipedia.org/wiki/'.$href, $title);
            }

            if (preg_match("/embed(\s+url\=)?/", mb_strtolower($namespace)) ||
                preg_match("/embed((:)?|(\s+url\=)?)/", mb_strtolower($matches[0]))) //Ticket #2308
            {
                if (framework\Context::isCLI()) return $href;

                // if the name space is null more than likely the user is
                // using embed url= format without the http:// in front of the URL
                // and the href tag will contain "embed url=" and it must be removed
                if ($namespace == null) $href = preg_replace("/embed(\s+)url=/","", $href);

                // if the href is empty or set to 'embed' then stop processing
                // an empty embed tag was entered '[[embed]]'
                if ($href == 'embed' || $href == null) return;

                $options = explode('|', $title);

                // Default values
                $width = 500;
                $height = 400;
                $type = 'iframe';

                // if the link is a youtube link prepare it for embedding
                if (tbg_youtube_link($href))
                {
                    $href = tbg_youtube_prepare_link($href);
                }

                // check to see if any size options exist
                if (array_key_exists(0, $options))
                {
                    $settings = $options[0];

                    // if width exists override default setting
                    if (preg_match_all("/width=(\d+)/", $settings, $width_matches))
                    {
                        if (!empty($width_matches))
                        {
                            $width = $width_matches[1][0];
                        }
                    }
                    // if height exists override default setting
                    if (preg_match_all("/height=(\d+)/", $settings, $height_matches))
                    {
                        if (!empty($height_matches))
                        {
                            $height = $height_matches[1][0];
                        }
                    }
                    // if type exists override default setting
                    if (preg_match_all("/type=(iframe|object)/", $settings, $type_matches))
                    {
                        if (!empty($type_matches))
                        {
                            $type = $type_matches[1][0];
                        }
                    }
                }

                if ($type == 'object')
                    $code = object_tag($href, $width, $height);
                else
                    $code = iframe_tag($href, $width, $height);

                return $code;
            }

            if (in_array(mb_strtolower($namespace), array('image', 'file')))
            {
                $retval = $namespace . ':' . $href;
                if (!framework\Context::isCLI())
                {
                    $options = explode('|', $title);
                    $filename = $href;
                    $issuemode = (bool) (isset($this->options['issue']) && $this->options['issue'] instanceof \thebuggenie\core\entities\Issue);
                    $articlemode = (bool) (isset($this->options['article']) && $this->options['article'] instanceof Article);

                    $file = null;
                    $file_link = $filename;
                    $caption = $filename;

                    if ($issuemode)
                    {
                        $file = $this->options['issue']->getFileByFilename($filename);
                    }
                    elseif ($articlemode)
                    {
                        $file = $this->options['article']->getFileByFilename($filename);
                    }
                    if ($file instanceof \thebuggenie\core\entities\File)
                    {
                        $caption = (!empty($options)) ? array_pop($options) : htmlentities($file->getDescription(), ENT_COMPAT, framework\Context::getI18n()->getCharset());
                        $caption = ($caption != '') ? $caption : htmlentities($file->getOriginalFilename(), ENT_COMPAT, framework\Context::getI18n()->getCharset());
                        $file_link = make_url('showfile', array('id' => $file->getID()));
                    }
                    else
                    {
                        $caption = (!empty($options)) ? array_pop($options) : false;
                    }

                    if ((($file instanceof \thebuggenie\core\entities\File && $file->isImage()) || $articlemode) && (mb_strtolower($namespace) == 'image' || $issuemode) && \thebuggenie\core\framework\Settings::isCommentImagePreviewEnabled())
                    {
                        $divclasses = array('image_container');
                        $style_dimensions = '';
                        foreach ($options as $option)
                        {
                            $optionlen = mb_strlen($option);
                            if (mb_substr($option, $optionlen - 2) == 'px')
                            {
                                if (is_numeric($option[0]))
                                {
                                    $style_dimensions = ' width: '.$option.';';
                                    break;
                                }
                                else
                                {
                                    $style_dimensions = ' height: '.mb_substr($option, 1).';';
                                    break;
                                }
                            }
                        }
                        if (in_array('thumb', $options))
                        {
                            $divclasses[] = 'thumb';
                        }
                        if (in_array('left', $options))
                        {
                            $divclasses[] = 'icleft';
                        }
                        if (in_array('center', $options))
                        {
                            $divclasses[] = 'iccenter';
                        }
                        if (in_array('right', $options))
                        {
                            $divclasses[] = 'icright';
                        }
                        $retval = '<div class="'.join(' ', $divclasses).'"';
                        if ($issuemode)
                        {
                            $retval .= ' style="float: left; clear: left;"';
                        }
                        $retval .= '>';
                        $retval .= image_tag($file_link, array('alt' => $caption, 'title' => $caption, 'style' => $style_dimensions, 'class' => 'image'), true);
                        if ($caption != '')
                        {
                            $retval .= '<br>'.$caption;
                        }
                        $retval .= link_tag($file_link, image_tag('icon_open_new.png', array('style' => 'margin-left: 5px;')), array('target' => 'new_window_'.rand(0, 10000), 'title' => __('Open image in new window')));
                        $retval .= '</div>';
                    }
                    else
                    {
                        if (strpos($file_link, 'http') === 0) {
                            $retval = $this->_parse_image($file_link, $caption, $options);
                        }
                        else if ($file_link == $filename) {
                            $retval = $caption . image_tag('icon_open_new.png', array('style' => 'margin-left: 5px;', 'title' => __('File no longer exists.')));
                        }
                        else {
                            $retval = link_tag($file_link, $caption . image_tag('icon_open_new.png', array('style' => 'margin-left: 5px;')), array('target' => 'new_window_'.rand(0, 10000), 'title' => __('Open file in new window')));
                        }
                    }
                }
                return $retval;
                //$file_id = \thebuggenie\core\entities\tables\Files::get
            }

            if ($namespace == 'TBG')
            {
                if (framework\Context::isCLI()) return $href;
                if (!framework\Context::getRouting()->hasRoute($href)) return $href;

                $options = explode('|',$title);
                $title = array_pop($options);

                try
                {
                    return link_tag(make_url($href), $title); // $this->parse_image($href,$title,$options);
                }
                catch (\Exception $e)
                {
                    return $href;
                }
            }

            if (mb_substr($href, 0, 1) == '/')
            {
                if (framework\Context::isCLI()) return $href;

                $options = explode('|', $title);
                $title = array_pop($options);

                return link_tag($href, $title); // $this->parse_image($href,$title,$options);
            }

            $title = preg_replace('/\(.*?\)/', '', $title);
            $title = preg_replace('/^.*?\:/', '', $title);

            if (!$namespace || !array_key_exists($namespace, array('ftp', 'http', 'https', 'gopher', 'mailto', 'news', 'nntp', 'telnet', 'wais', 'file', 'prospero', 'aim', 'webcal')))
            {
                if ($namespace) $href = $namespace . ':' . $href;
                $href = $this->_wiki_link($href);
                $title = (isset($title)) ? $title : $href;
                $this->addInternalLinkOccurrence($href);

                if (framework\Context::isCLI()) return $href;

                $href = framework\Context::getRouting()->generate('publish_article', array('article_name' => $href));
            }
            else
            {
                $href = $namespace.':'.$this->_wiki_link($href);
            }

            if (framework\Context::isCLI()) return $href;

            return link_tag($href, $title);
        }

        protected function _parse_externallink($matches)
        {
            if (!is_array($matches))
            {
                if (is_null($matches)) return '';

                $this->linknumber++;
                $href = $title = html_entity_decode($matches, ENT_QUOTES, 'UTF-8');
            }
            else
            {
                $href = html_entity_decode($matches[2], ENT_QUOTES, 'UTF-8');
                $title = null;
                $title = (array_key_exists(3, $matches)) ? $matches[3] : $matches[2];
                if (!$title)
                {
                    $this->linknumber++;
                    $title = "[{$this->linknumber}]";
                }

                if (framework\Context::isCLI()) return $href;
            }
            return link_tag(str_replace(array('[',']'), array('&#91;', '&#93;'), $href), str_replace(array('[',']'), array('&#91;', '&#93;'), $title), array('target' => '_new'));
        }

        protected function _parse_autosensedlink($matches)
        {
            return $this->_parse_externallink(array('', '', $matches[0]));
        }

        protected function _emphasize($level)
        {
            $levels = array(2 => array('<i>','</i>'), 3 => array('<b>','</b>'), 4 => array('<b>','</b>'), 5 => array('<i><b>','</b></i>'));

            $output = "";

            // handle cases where bold/italic words ends with an apostrophe, eg: ''somethin'''
            // should read <em>somethin'</em> instead of <em>somethin<strong>
            if ((!isset($this->openblocks[$level]) || (isset($this->openblocks[$level]) && !$this->openblocks[$level])) && (isset($this->openblocks[$level - 1]) && $this->openblocks[$level - 1]))
            {
                $level--;
                $output = "'";
            }

            $offset = (isset($this->openblocks[$level])) ? (int) $this->openblocks[$level] : 0;
            $output .= $levels[$level][$offset];

            $this->openblocks[$level] = !$offset;

            return $output;
        }

        protected function _parse_emphasize($matches)
        {
            $amount = mb_strlen($matches[1]);
            return $this->_emphasize($amount);
        }

        protected function _emphasize_off()
        {
            $output = "";
            if (count($this->openblocks))
            {
                foreach ($this->openblocks as $amount =>$state) {
                    if ($state) $output .= $this->_emphasize($amount);
                }
            }

            return $output;
        }

        protected function _parse_eliminate($matches)
        {
            return "";
        }

        public static function parseIssuelink($matches, $markdown_format = false)
        {
            $theIssue = \thebuggenie\core\entities\Issue::getIssueFromLink($matches[0]);
            $output = '';
            $classname = '';
            if ($theIssue instanceof \thebuggenie\core\entities\Issue && ($theIssue->isClosed() || $theIssue->isDeleted()))
            {
                $classname = 'closed';
            }
            if ($theIssue instanceof \thebuggenie\core\entities\Issue)
            {
                $theIssueUrl = make_url('viewissue', array('issue_no' => $theIssue->getFormattedIssueNo(false), 'project_key' => $theIssue->getProject()->getKey()));

                if ($markdown_format) {
                    if ($classname != '') $classname = ' {.'.$classname.'}';

                    $output = "[{$matches[0]}]($theIssueUrl \"{$theIssue->getFormattedTitle()}\")$classname";
                }
                else {
                    $output = ' '.link_tag($theIssueUrl, $matches[0], array('class' => $classname, 'title' => $theIssue->getFormattedTitle()));
                }
            }
            else
            {
                $output = $matches[0];
            }
            return $output;
        }

        protected function _parse_mention($matches)
        {
            $user = \thebuggenie\core\entities\tables\Users::getTable()->getByUsername($matches[1]);
            if ($user instanceof \thebuggenie\core\entities\User)
            {
                $output = framework\Action::returnComponentHTML('main/userdropdown_inline', array('user' => $matches[1], 'displayname' => $matches[0]));
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

        protected function _parse_issuelink($matches)
        {
            return self::parseIssuelink($matches);
        }

        protected function _parse_insert_variables($matches)
        {
            $param_detail = explode('|', $matches[1]);
            $param_name = array_shift($param_detail);
            $param_default = (!empty($param_detail)) ? array_shift($param_detail) : null;

            if (isset($this->options['parameters']) && isset($this->options['parameters'][$param_name]))
            {
                $val = trim($this->options['parameters'][$param_name]);
            }
            else
            {
                $val = ($param_default !== null) ? trim($param_default) : trim($param_name);
            }

            return $val;
        }

        protected function _parse_insert_template($matches)
        {
            switch($matches[1])
            {
                case 'CURRENTMONTH':
                    return date('m');
                case 'CURRENTMONTHNAMEGEN':
                case 'CURRENTMONTHNAME':
                    return date('F');
                case 'CURRENTDAY':
                    return date('d');
                case 'CURRENTDAYNAME':
                    return date('l');
                case 'CURRENTYEAR':
                    return date('Y');
                case 'CURRENTTIME':
                    return date('H:i');
                case 'NUMBEROFARTICLES':
                    return 0;
                case 'PAGENAME':
                    return framework\Context::getResponse()->getPage();
                case 'NAMESPACE':
                    return 'None';
                case 'TOC':
                    return (isset($this->options['included'])) ? '' : '{{TOC}}';
                case 'SITENAME':
                case 'SITETAGLINE':
                    return \thebuggenie\core\framework\Settings::getSiteHeaderName();
                default:
                    $details = explode('|', $matches[1]);
                    $template_name = array_shift($details);
                    if (substr($template_name, 0, 1) == ':') $template_name = substr($template_name, 1);
                    $template_name = (Article::doesArticleExist($template_name)) ? $template_name : 'Template:'.$template_name;
                    $template_article = Articles::getTable()->getArticleByName($template_name);
                    $parameters = array();
                    if (count($details))
                    {
                        foreach ($details as $parameter)
                        {
                            $param = explode('=', $parameter);
                            if (count($param) == 2)
                                $parameters[$param[0]] = $param[1];
                            else
                                $parameters[] = $parameter;
                        }
                    }
                    if ($template_article instanceof Article)
                    {
                        return tbg_parse_text($template_article->getContent(), false, null, array('included' => true, 'parameters' => $parameters));
                    }
                    else
                    {
                        return $matches[0];
                    }
            }
        }

        protected function _parse_tableopener($matches)
        {
            $element = simplexml_load_string("<table ".trim($matches[1])."></table>");
            $output = $this->_parse_tablecloser(false);
            $output = "<table class=\"";
            $output .= ($element['class']) ? $element['class'] : 'sortable resizable';
            $output .= '"';
            if ($element['style']) $output .= ' style="'.$element['style'].'"';
            if ($element['align']) $output .= ' align="'.$element['align'].'"';
            $output .= ">";
            $this->tablemode = true;
            $this->opentablecol = false;
            $this->stop_all = true;

            return $output;
        }

        protected function _parse_tablecloser($matches)
        {
            if (!$this->tablemode) return "";
            $this->tablemode = false;
            $this->stop_all = true;
            $output = '';
            if ($this->opentablecol == true)
            {
                $output .= '</td></tr>';
                $this->opentablecol = false;
            }
            $output .= "</table>";

            return $output;
        }

        protected function _parse_tablerow($matches)
        {
            $this->stop_all = true;
            $output = '';
            if ($this->opentablecol == true)
            {
                $output .= '</td></tr>';
                $this->opentablecol = false;
            }
            $element = simplexml_load_string("<tr ".trim($matches[1])."></tr>");
            $output .= "<tr";
            if ($element['class']) $output .= ' class="'.$element['class'].'"';
            if ($element['style']) $output .= ' style="'.$element['style'].'"';
            if ($element['align']) $output .= ' align="'.$element['align'].'"';
            $output .= ">";

            return $output;
        }

        protected function _parse_tableheader($matches)
        {
            $this->opentablecol = false;
            $output = '<thead>';
            if (array_key_exists(1, $matches))
            {
                $cols = explode(' !! ', $matches[1]);
                foreach ($cols as $col)
                {
                    $output .= $this->_parse_tablecellcontent($col, 'h')."</th>";
                }
            }
            $output .= "</thead>";

            return $output;
        }

        protected function _parse_tablecellcontent($content, $mode)
        {
            $matches = explode('|', $content);
            $output = "<t{$mode}";
            if (count($matches) > 1)
            {
                libxml_use_internal_errors(true);
                $element = simplexml_load_string("<t{$mode} ".trim($matches[0])."></t{$mode}>");

                if ($element instanceof \SimpleXMLElement)
                {
                    if ($element['class']) $output .= ' class="'.$element['class'].'"';
                    if ($element['style']) $output .= ' style="'.$element['style'].'"';
                    if ($element['align']) $output .= ' align="'.$element['align'].'"';
                    if ($element['scope']) $output .= ' scope="'.$element['scope'].'"';
                    if ($element['colspan']) $output .= ' colspan="'.$element['colspan'].'"';
                    if ($mode == 'd')
                    {
                        if ($element['rowspan']) $output .= ' rowspan="'.$element['rowspan'].'"';
                    }
                    $output .= ">{$matches[1]}";
                }
                else
                {
                    $output .= ">{$matches[0]}";
                }
                libxml_use_internal_errors(false);
            }
            else
            {
                $output .= ">{$matches[0]}";
            }

            return $output;
        }

        protected function _parse_tablerowcontent($matches)
        {
            $this->opentablecol = true;
            $first = true;
            $output = '';
            if (array_key_exists(1, $matches))
            {
                $cols = explode(' || ', $matches[1]);
                foreach ($cols as $col)
                {
                    if (!$first) $output .= "</td>";
                    $output .= $this->_parse_tablecellcontent($col, 'd');
                    $first = false;
                }
            }

            return $output;
        }

        protected function _parse_allowed_tags($matches)
        {
            libxml_use_internal_errors(true);
            $element = simplexml_load_string("<{$matches[1]}{$matches[2]}>{$matches[3]}</{$matches[1]}>");

            if ($element instanceof \SimpleXMLElement)
            {
                $html = "<{$element->getName()}";
                if (isset($element['style'])) $html .= ' style="'.$element['style'].'"';
                if (isset($element['class'])) $html .= ' class="'.$element['class'].'"';
                $html .= ">".$element."</{$element->getName()}>";
            }
            else
            {
                $html = $matches[0];
            }
            libxml_use_internal_errors(false);

            return $html;
        }

        protected function _parse_specialchar($matches)
        {
            return '<span title="&amp;'.$matches[1].';">&'.$matches[1].';</span>';
        }

        protected function _getsmiley($smiley_code)
        {
            switch ($smiley_code[1])
            {
                case ":(":
                case ":-(":
                    return image_tag('smileys/4.png');
                case ":)":
                case ":-)":
                    return image_tag('smileys/2.png');
                case "8)":
                case "8-)":
                    return image_tag('smileys/3.png');
                case "B)":
                case "B-)":
                    return image_tag('smileys/3.png');
                case ":-/":
                    return image_tag('smileys/10.png');
                case ":D":
                case ":-D":
                    return image_tag('smileys/5.png');
                case ":P":
                case ":-P":
                    return image_tag('smileys/6.png');
                case "(!)":
                    return image_tag('smileys/8.png');
                case "(?)":
                    return image_tag('smileys/9.png');
            }
        }

        protected function _parse_line($line, $options = array())
        {
            $line_regexes = array();

            $line_regexes['preformat'] = '^\s{1}(.*?)$';
            $line_regexes['quote'] = '^(\&gt\;)(.*?)$';
            $line_regexes['definitionlist'] = '^([\;\:])(?!\-?[\(\)\D\/P])\s*(.*?)$';
            $line_regexes['newline'] = '^$';
            $line_regexes['list'] = '^([\*\#]+)(.*?)$';
            $line_regexes['tableopener'] = '^\{\|(.*?)$';
            $line_regexes['tablecloser'] = '^\|\}$';
            $line_regexes['tablerow'] = '^\|-(.*?)$';
            $line_regexes['tableheader'] = '^\!(.*?)$';
            $line_regexes['tablerowcontent'] = '^\|{1,2}\s?(.*?)$';
            $line_regexes['headers'] = '^(={1,6})(.*?)(={1,6})$';
            $line_regexes['horizontalrule'] = '^----$';

            $char_regexes = array();
            $char_regexes[] = array('/(\'{2,5})/i', array($this, '_parse_emphasize'));
            $char_regexes[] = array('/(__NOTOC__|__NOEDITSECTION__)/i', array($this, '_parse_eliminate'));
            $char_regexes[] = array('/(\[\[(\:?([^\]]*?)\:)?([^\]]*?)(\|([^\]]*?))?\]\]([a-z]+)?)/i', array($this, "_parse_save_ilink"));
            $char_regexes[] = array('/(^|[ \t\r\n])((ftp|http|https|gopher|mailto|news|nntp|telnet|wais|file|prospero|aim|webcal):(([A-Za-z0-9$_.+!*(),;\[\]\/?:@&~=-])|%[A-Fa-f0-9]{2}){2,}(#([a-zA-Z0-9][a-zA-Z0-9\[\]$_.+!*(),;\/?:@&~=-]*))?([A-Za-z0-9\[\]$_+!*();\/?:~-]))/', array($this, '_parse_autosensedlink'));
            $char_regexes[] = array('/(\[([^\]]*?)(?:\s+([^\]]*?))?\])/i', array($this, "_parse_save_elink"));
            $char_regexes[] = array(self::getIssueRegex(), array($this, '_parse_issuelink'));
            $char_regexes[] = array('/\B\@([\w\-.]+)/i', array($this, '_parse_mention'));
            $char_regexes[] = array('/(?<=\s|^)(\:\(|\:-\(|\:\)|\:-\)|8\)|8-\)|B\)|B-\)|\:-\/|\:-D|\:-P|\(\!\)|\(\?\))(?=\s|$)/', array($this, '_getsmiley'));
            $char_regexes[] = array('/&amp;([A-Za-z0-9]+|\#[0-9]+|\#[xX][0-9A-Fa-f]+);/', array($this, '_parse_specialchar'));

            $event = framework\Event::createNew('core', 'thebuggenie\core\framework\helpers\TextParser::_parse_line::char_regexes', $this, array(), $char_regexes);
            $event->trigger();

            $char_regexes = $event->getReturnList();

            $this->stop = false;
            $this->stop_all = false;

            $called = array();

            foreach ($line_regexes as $func => $regex)
            {
                if (preg_match("/$regex/i", $line, $matches))
                {
                    $called[$func] = true;
                    $func = "_parse_".$func;
                    $line = $this->$func($matches);
                    if ($this->stop || $this->stop_all) break;
                }
            }

            if (!$this->stop_all)
            {
                $this->stop = false;
                foreach ($char_regexes as $regex)
                {
                    $line = preg_replace_callback($regex[0], $regex[1], $line);
                    if ($this->stop) break;
                }
                foreach (self::getRegexes() as $regex)
                {
                    $parser = $this;
                    $line = preg_replace_callback($regex[0], function($matches) use ($regex, $parser) { return call_user_func($regex[1], $matches, $parser); }, $line);
                    if ($this->stop) break;
                }
            }

            $isline = (bool) (mb_strlen(trim($line)) > 0);

            // if this wasn't a list item, and we are in a list, close the list tag(s)
            if (($this->list_level > 0) && !array_key_exists('list', $called)) $line = $this->_parse_list(false, true) . $line;
            if ($this->deflist && !array_key_exists('definitionlist', $called)) $line = $this->_parse_definitionlist(false, true) . $line;

            if ($this->preformat && !array_key_exists('preformat', $called)) $line = $this->_parse_preformat(false, true) . $line;
            if ($this->quote && !array_key_exists('quote', $called)) $line = $this->_parse_quote(false, true) . $line;

            // suppress linebreaks for the next line if we just displayed one; otherwise re-enable them
            if ($isline) $this->ignore_newline = (array_key_exists('newline', $called) || array_key_exists('headers', $called));

            if (mb_substr($line, -1) != "\n")
            {
                $line .= (isset($this->options['included'])) ? "\n" : " \n";
            }

            return $line;
        }

        protected function _parseText($options = array())
        {
            $options = array_merge($options, $this->options);
            framework\Context::loadLibrary('common');

            $output = "";
            $text = $this->text;

            if (!isset($this->options['plain']))
            {
                $this->list_level_types = array();
                $this->list_level = 0;
                $this->deflist = false;
                $this->ignore_newline = false;

                $text = preg_replace_callback('/<(nowiki|pre)>(.*)<\/(\\1)>(?!<\/(\\1)>)/ismU', array($this, "_parse_save_nowiki"), $text);
                $text = preg_replace_callback('/[\{]{3,3}([\d|\w|\|]*)[\}]{3,3}/ismU', array($this, "_parse_insert_variables"), $text);
                $text = preg_replace_callback('/(?<!\{)[\{]{2,2}([^{^}.]*)[\}]{2,2}(?!\})/ismU', array($this, "_parse_insert_template"), $text);
                if (isset($this->options['included']))
                {
                    $text = preg_replace_callback('/<noinclude>(.+?)<\/noinclude>(?!<\/noinclude>)/ism', array($this, "_parse_remove_noinclude"), $text);
                    $text = preg_replace_callback('/<includeonly>(.+?)<\/includeonly>(?!<\/includeonly>)/ism', array($this, "_parse_preserve_includeonly"), $text);
                    return $text;
                }

                if (!isset($this->options['included']))
                {
                    $text = preg_replace_callback('/<includeonly>(.+?)<\/includeonly>(?!<\/includeonly>)/ism', array($this, "_parse_remove_includeonly"), $text);
                    $text = preg_replace_callback('/<noinclude>(.+?)<\/noinclude>(?!<\/noinclude>)/ism', array($this, "_parse_preserve_noinclude"), $text);
                }
                $text = preg_replace_callback('/<source((?:\s+[^\s]+=".*")*)>\s*?(.+)\s*?<\/source>/ismU', array($this, "_parse_save_code"), $text);
                // Thanks to Mike Smith (scgtrp) for the above regexp

                $text = tbg_decodeUTF8($text, true);

                $text = preg_replace_callback('/&lt;(strike|u|pre|tt|s|del|ins|u|blockquote|div|span|font|sub|sup)(\s.*)?&gt;(.*)&lt;\/(\\1)&gt;/ismU', array($this, '_parse_allowed_tags') ,$text);
                $text = str_replace('&lt;br&gt;', '<br>' ,$text);

                $lines = explode("\n", $text);
                foreach ($lines as $line)
                {
                    if (mb_substr($line, -1) == "\r")
                    {
                        $line = mb_substr($line, 0, -1);
                    }
                    $output .= $this->_parse_line($line, $options);
                }

                // Check if we need to close any tags in case the list items, etc were the last line
                if ($this->list_level > 0) $output .= $this->_parse_list(false, true);
                if ($this->deflist) $output .= $this->_parse_definitionlist(false, true);
                if ($this->preformat) $output .= $this->_parse_preformat(false, true);
                if ($this->quote) $output .= $this->_parse_quote(false, true);
                $output .= $this->_parse_tablecloser(false);

                $this->nowikis = array_reverse($this->nowikis);
                $this->codeblocks = array_reverse($this->codeblocks);
                $this->elinks = array_reverse($this->elinks);

                if (!array_key_exists('ignore_toc', $options))
                {
                    $output = preg_replace_callback('/\{\{TOC\}\}/', array($this, "_parse_add_toc"), $output);
                }
                else
                {
                    $output = str_replace('{{TOC}}', '', $output);
                }
                $output = preg_replace_callback('/~~~NOWIKI~~~/i', array($this, "_parse_restore_nowiki"), $output);
                if (!isset($options['no_code_highlighting']))
                {
                    $output = preg_replace_callback('/~~~CODE~~~/Ui', array($this, "_parse_restore_code"), $output);
                }

                $output = preg_replace_callback('/~~~ILINK~~~/i', array($this, "_parse_restore_ilink"), $output);
                $output = preg_replace_callback('/~~~ELINK~~~/i', array($this, "_parse_restore_elink"), $output);
            }
            else
            {
                $text = nl2br(tbg_decodeUTF8($text, true));
                $text = preg_replace_callback(self::getIssueRegex(), array($this, '_parse_issuelink'), $text);
                $text = preg_replace_callback(self::getMentionsRegex(), array($this, '_parse_mention'), $text);

                $output = $text;
            }

            return $output;
        }

        public function getParsedText()
        {
            if ($this->parsed_text === null)
            {
                $this->parsed_text = $this->_parseText();
            }
            return $this->parsed_text;
        }

        public function doParse($options = array())
        {
            if ($this->parsed_text === null)
            {
                $this->parsed_text = $this->_parseText($options);
            }
        }

        protected function _parse_add_toc($matches)
        {
            if (framework\Context::isCLI()) return '';

            return framework\Action::returnComponentHTML('publish/toc', array('toc' => $this->toc));
        }

        protected function _parse_save_nowiki($matches)
        {
            array_push($this->nowikis, $matches[2]);
            return "~~~NOWIKI~~~";
        }

        protected function _parse_remove_noinclude($matches)
        {
            return "";
        }

        protected function _parse_preserve_noinclude($matches)
        {
            return $matches[1];
        }

        protected function _parse_remove_includeonly($matches)
        {
            return "";
        }

        protected function _parse_preserve_includeonly($matches)
        {
            return $matches[1];
        }

        protected function _parse_save_ilink($matches)
        {
            array_push($this->ilinks, $matches);
            return "~~~ILINK~~~";
        }

        protected function _parse_save_elink($matches)
        {
            array_push($this->elinks, $matches);
            return "~~~ELINK~~~";
        }

        protected function _parse_restore_ilink($matches)
        {
            return $this->_parse_internallink(array_shift($this->ilinks));
        }

        protected function _parse_restore_elink($matches)
        {
            return $this->_parse_externallink(array_pop($this->elinks));
        }

        protected function _parse_restore_nowiki($matches)
        {
            return nl2br(htmlspecialchars(array_pop($this->nowikis)));
        }

        protected function _parse_save_code($matches)
        {
            array_push($this->codeblocks, $matches);
            return "~~~CODE~~~";
        }

        protected function _geshify($matches)
        {
            if (!(is_array($matches) && count($matches) > 1))
            {
                return '';
            }
            $codeblock = $matches[2];
            if (strlen(trim($codeblock)))
            {
                $params = $matches[1];

                $language = preg_match('/(?<=lang=")(.+?)(?=")/', $params, $matches);

                if ($language !== 0)
                {
                    $language = $matches[0];
                }
                else
                {
                    $language = \thebuggenie\core\framework\Settings::get('highlight_default_lang');
                }

                $numbering_startfrom = preg_match('/(?<=line start=")(.+?)(?=")/', $params, $matches);
                if ($numbering_startfrom !== 0)
                {
                    $numbering_startfrom = (int) $matches[0];
                }
                else
                {
                    $numbering_startfrom = 1;
                }

                $geshi = new \GeSHi($codeblock, $language);

                $highlighting = preg_match('/(?<=line=")(.+?)(?=")/', $params, $matches);
                if ($highlighting !== 0)
                {
                    $highlighting = $matches[0];
                }
                else
                {
                    $highlighting = false;
                }

                $interval = preg_match('/(?<=highlight=")(.+?)(?=")/', $params, $matches);
                if ($interval !== 0)
                {
                    $interval = $matches[0];
                }
                else
                {
                    $interval = \thebuggenie\core\framework\Settings::get('highlight_default_interval');
                }

                if ($highlighting === false)
                {
                    switch (\thebuggenie\core\framework\Settings::get('highlight_default_numbering'))
                    {
                        case 1:
                            // Line numbering with a highloght every n rows
                            $geshi->enable_line_numbers(GESHI_FANCY_LINE_NUMBERS, $interval);
                            $geshi->start_line_numbers_at($numbering_startfrom);
                            break;
                        case 2:
                            // Normal line numbering
                            $geshi->enable_line_numbers(GESHI_NORMAL_LINE_NUMBERS, 10);
                            $geshi->start_line_numbers_at($numbering_startfrom);
                            break;
                        case 3:
                            break; // No numbering
                    }
                }
                else
                {
                    switch($highlighting)
                    {
                        case 'highlighted':
                        case 'GESHI_FANCY_LINE_NUMBERS':
                            // Line numbering with a highloght every n rows
                            $geshi->enable_line_numbers(GESHI_FANCY_LINE_NUMBERS, $interval);
                            $geshi->start_line_numbers_at($numbering_startfrom);
                            break;
                        case 'normal':
                        case 'GESHI_NORMAL_LINE_NUMBERS':
                            // Normal line numbering
                            $geshi->enable_line_numbers(GESHI_NORMAL_LINE_NUMBERS, 10);
                            $geshi->start_line_numbers_at($numbering_startfrom);
                            break;
                        case 3:
                            break; // No numbering
                    }
                }

                $codeblock = $geshi->parse_code();
                unset($geshi);
            }
            return '<code>' . $codeblock . '</code>';
        }

        protected function _parse_restore_code($matches)
        {
            return $this->_geshify(array_pop($this->codeblocks));
        }

        public function getInternalLinks()
        {
            return $this->internallinks;
        }

        public function getCategories()
        {
            return $this->categories;
        }

        public function setOption($option, $value)
        {
            $this->options[$option] = $value;
        }

    }
