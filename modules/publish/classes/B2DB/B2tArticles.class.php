<?php

	class B2tArticles extends B2DBTable 
	{
		const B2DBNAME = 'articles';
		const ID = 'articles.id';
		const TITLE = 'articles.title';
		const ARTICLE_NAME = 'articles.article_name';
		const CONTENT = 'articles.content';
		const LINK = 'articles.link';
		const DELETED = 'articles.deleted';
		const IS_PUBLISHED = 'articles.is_published';
		const DATE = 'articles.date';
		const INTRO_TEXT = 'articles.intro_text';
		const AUTHOR = 'articles.author';
		const ORDER = 'articles.order';
		const ICON = 'articles.icon';
		const SCOPE = 'articles.scope';
		
		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addVarchar(self::TITLE, 255);
			parent::_addVarchar(self::ARTICLE_NAME, 255);
			parent::_addText(self::INTRO_TEXT, false);
			parent::_addText(self::CONTENT, false);
			parent::_addText(self::LINK, false);
			parent::_addBoolean(self::IS_PUBLISHED);
			parent::_addBoolean(self::DELETED);
			parent::_addInteger(self::DATE, 10);
			parent::_addInteger(self::ORDER, 5);
			parent::_addVarchar(self::ICON, 50, '');
			parent::_addForeignKeyColumn(self::AUTHOR, B2DB::getTable('B2tUsers'), B2tUsers::ID);
			parent::_addForeignKeyColumn(self::SCOPE, B2DB::getTable('B2tScopes'), B2tScopes::ID);
		}

		public function loadFixtures($scope)
		{
			$crit = $this->getCriteria();
			$crit->addInsert(self::ARTICLE_NAME, 'IndexMessage');
			$crit->addInsert(self::TITLE, 'Welcome to The Bug Genie');
			$crit->addInsert(self::AUTHOR, 0);
			$crit->addInsert(self::DATE, $_SERVER["REQUEST_TIME"]);
			$crit->addInsert(self::INTRO_TEXT, '');
			$crit->addInsert(self::CONTENT, "Thank you for installing The Bug Genie!

By using The Bug Genie, we want to make your development environment a whole lot less cumbersome.<br>
Project management, issue tracking, source code control, fully editable wiki for all your documenation needs, and more.
 
Please take a few moments setting up your new issue tracker, by clicking the [[TBG:configure|Configure]] menu option in the top menu.<br>
From this page you can configure The Bug Genie the way you want.

For more information on getting started, have a look at GettingStarted, ConfiguringTheBugGenie and CreatingIssues.

To learn more about the wiki formatting used in The Bug Genie, check out WikiFormatting.

<br>
'''Enjoy The Bug Genie!'''

''-The Bug Genie development team''<br>
[http://www.thebuggenie.com]

");
			$crit->addInsert(self::IS_PUBLISHED, true);
			$crit->addInsert(self::SCOPE, $scope);
			$res = $this->doInsert($crit);

			$crit = $this->getCriteria();
			$crit->addInsert(self::ARTICLE_NAME, 'MainPage');
			$crit->addInsert(self::TITLE, 'Wiki home');
			$crit->addInsert(self::AUTHOR, 0);
			$crit->addInsert(self::DATE, $_SERVER["REQUEST_TIME"]);
			$crit->addInsert(self::INTRO_TEXT, '');
			$crit->addInsert(self::CONTENT, "This is the main wiki homepage!

");

			$crit = $this->getCriteria();
			$crit->addInsert(self::ARTICLE_NAME, 'WikiFormatting');
			$crit->addInsert(self::TITLE, 'Writing wiki articles with wiki formatting');
			$crit->addInsert(self::AUTHOR, 0);
			$crit->addInsert(self::DATE, $_SERVER["REQUEST_TIME"]);
			$crit->addInsert(self::INTRO_TEXT, '');
			$crit->addInsert(self::CONTENT, "{{TOC}}
This article will try to explain how to write pages in The Bug Genie wiki.

= Wiki formatting =
The wiki module uses a formatting technique called \"Wiki formatting\", most commonly known from Wikipedia (!MediaWiki).
The Bug Genie wiki tries to stay as close to the !MediaWiki syntax as possible, so if you're familiar with that syntax you should feel right at home.

Wiki formatting is well explained in the [http://en.wikipedia.org/wiki/Help:Wikitext_examples Wikipedia help article], but can be easily summarized as a simple method of formatting your text by placing certain characters.
We will show you the most common syntax below.

== Creating links between documents ==
Traditionally, wikis have used something called [http://en.wikipedia.org/wiki/CamelCase Camel Casing] to create links between documents. CamelCasing means that you put any word or combination of words as a \"'''camel cased'''\" word, and then the wiki will create a link to the document with that name for you automatically. If the page you are trying to link to isn't yet created, the link will still be displayed, and you can click it to start editing the new article.

If you want to write a word with more than one capital letter, use an exclamation mark infront of it - that will stop it from being turned into a link automatically. The support for \"camel casing\" can be turned off in the wiki settings.

You can also use the double square bracket link format to link to internal pages, if you don't want to use the CamelCasing style:
  [[InternalPage]]
[[InternalPage]]
  [[InternalPage Internal|Page]]
[[InternalPage Internal|Page]]

With this method you can also link to internal pages in The Bug Genie, by either using the LOCAL namespace and specifying the relative url (like \"/configure\" points to the configuration page and \"/wiki\" points to the wiki), or use the internal route namespace \"TBG:\" (this has the added benefit of being safe if the link ever changes in a future release).
Unfortunately, the list of routes used in The Bug Genie is quite long, but a few examples are listed below:

  [[LOCAL:/about|About The Bug Genie]]
[[LOCAL:/about|About The Bug Genie]]
  [[LOCAL:/logout|Log out]]
[[LOCAL:/logout|Log out]]
  [[TBG:configure_projects|Configure projects]]
[[TBG:configure_projects|Configure projects]]
  [[TBG:configure_modules|Modules configuration]]
[[TBG:configure_modules|Modules configuration]]

'''Remember - if all this sound complicated, you can always just use CamelCasing (provided it's not disabled)'''

== Links ==
In addition to linking between internal pages with double square brackets, you can link to external pages with single square brackets. Links inside your text will not automatically be turned into clickable links, but simply putting a pair of square brackets around the link makes it clickable. In addition, you can add a title if you want to:

  http://www.thebuggenie.com
http://www.thebuggenie.com
  [http://www.thebuggenie.com]
[http://www.thebuggenie.com]
  [http://www.thebuggenie.com The Bug Genie website]
[http://www.thebuggenie.com The Bug Genie website]

== Line breaks and text formatting ==
You can use line breaks to space out the text and make it more readable in the editor. One line break will not be transformed into a line break when the page is
displayed. A blank line makes a new paragraph. You can put &lt;br&gt; to make a hard line break, but be careful with this as it might break layout.
  This text is easy to &amp;nbsp;
  read because it is &amp;nbsp;
  split into several lines
  (put &amp;nbsp; at the end of each line if you don't want the text to run together)
This text is easy to &nbsp;
read because it is &nbsp;
split into several lines

Text can be formatted by putting '-characters around the text you want to format. Here are some examples:

  ''this is some italic text''
''this is some italic text''
  '''this is some bold text'''
'''this is some bold text'''
  '''''this is some bold and italic text'''''
'''''this is some bold and italic text'''''

You can also use simple html formatting for things like underlined and strikethrough:

  &lt;strike&gt;strikethrough&lt;/strike&gt;
<strike>strikethrough</strike>
  &lt;u&gt;underlined&lt;/u&gt;
<u>underlined</u>

== Table of contents ==
You can get a table of content on your page (like the one in the top right on this page) by using the TOC variable the same way as the \"normal\" variables above. It doesn't matter where you put this variable in your document, it will always be displayed in the top right corner.

== Headings ==
To specify headings, use equals-character around the line you want to be a heading. The number of equals-characters you put around the line decides how big the heading is (1 is biggest, 6 is lowest).
  = I'm a big header =
  == I'm a fairly big header ==
  ===== I'm a very small header =====
Headings will automatically appear in the table of contents (if you have one).

== Horizontal line ==
If you want to put a horizontal line in the document, use four dashes:

  ----
----

= Advanced formatting =

== Images ==
You can show uploaded images by using the same notation as for links:
  [[Image:bao1.jpg]]

== Completely unparsed text ==
If you have some text that you don't want to be automatically parsed at all, put them inside &lt;nowiki&gt;&lt;/nowiki&gt; tags:
  &lt;nowiki&gt;some text that shouldn't be parsed [[a|link]] and '''bold text'''&lt;/nowiki&gt;
<nowiki>some text that shouldn't be parsed [[a|link]] and '''bold text'''</nowiki>

== Lists ==
To create a list, use the star character for a bulleted list, hash character for a numbered (ordered) list, or a combination:

  * One bullet
  * Another '''bullet'''
  *# a list item
  *# another list item
  *#* unordered, ordered, unordered
  *#* again
  *# back down one
* One bullet
* Another '''bullet'''
*# a list item
*# another list item
*#* unordered, ordered, unordered
*#* again
*# back down one


== Variables ==
There are some shortcuts available for outputting special data such as the current date, hour, day, etc. By putting special keywords enclosed with two { and }-characters on each side, The Bug Genie will automatically translate this for you.

'''Here are some examples''':
  {{CURRENTMONTH}}
{{CURRENTMONTH}}
  {{CURRENTDAY}}
{{CURRENTDAY}}
  {{SITENAME}}
{{SITENAME}}

The following keywords are available for automatic substitution: CURRENTMONTH, CURRENTMONTHNAMEGEN, CURRENTMONTHNAME, CURRENTDAY, CURRENTDAYNAME, CURRENTYEAR, CURRENTTIME, NUMBEROFARTICLES, PAGENAME, NAMESPACE, SITENAME, SITETAGLINE.

== Linking to issues ==
If you want to link to an issue, use one of the following keywords: '''bug''', '''issue''', '''ticket''', '''story''', followed by the issue number:
  ticket #123
ticket [http://sample.com/product/issue/123 #123 - title from ticket here]
  bug #200
bug [http://sample.com/product/issue/200 #200 - serious bug]
  issue TBG-24
issue <strike>[http://sample.com/product/issue/TBG-24 TBG-24 - issue title]</strike>

The links will automatically include things such as the title, and a strikethrough if the issue is closed.

== Examples / comments ==
If you want to put some text on the page that shouldn't be interpreted (like the examples above), put two spaces in front of each line.
    I'm an example because I have two spaces in front of me (actually I have four, but that's just so you can see the two spaces)
    This is a second line
");
			$crit->addInsert(self::IS_PUBLISHED, true);
			$crit->addInsert(self::SCOPE, $scope);
			$res = $this->doInsert($crit);

			$crit = $this->getCriteria();
			$crit->addInsert(self::ARTICLE_NAME, 'CamelCasing');
			$crit->addInsert(self::TITLE, 'What is Camel Casing?');
			$crit->addInsert(self::AUTHOR, 0);
			$crit->addInsert(self::DATE, $_SERVER["REQUEST_TIME"]);
			$crit->addInsert(self::INTRO_TEXT, '');
			$crit->addInsert(self::CONTENT, "'''!CamelCase''' (also spelled \"camel case\") or ''medial capitals'' is the practice of writing compound words or phrases in which the elements are joined without spaces, with each element's initial letter capitalized within the compound, and the first letter can be upper or lower case — as in !LaBelle, !BackColor, !MacGyver, or iPod.

The name comes from the uppercase \"bumps\" in the middle of the compound word, suggestive of the humps of a camel.

The practice is also known by many other names, such as '''!BumpCaps''', '''!BeefCaps''', '''!CapWords''' and '''!WikiWords'''.

'''This is a short introduction to the subject, based on the [[Wikipedia:CamelCase|Wikipedia article]] about camel case.");
			$crit->addInsert(self::IS_PUBLISHED, true);
			$crit->addInsert(self::SCOPE, $scope);
			$res = $this->doInsert($crit);


		}

		public function getArticleByName($name)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::ARTICLE_NAME, $name);
			$crit->addWhere(self::SCOPE, BUGScontext::getScope()->getID());
			$row = $this->doSelectOne($crit);

			return $row;
		}

		public function getUnpublishedArticlesByUser($user_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::IS_PUBLISHED, false);
			$crit->addWhere(self::SCOPE, BUGScontext::getScope()->getID());

			$res = $this->doSelect($crit);

			return $res;
		}

	}

?>