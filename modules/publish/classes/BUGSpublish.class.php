<?php

	class BUGSpublish extends BUGSmodule 
	{

		public function __construct($m_id, $res = null)
		{
			parent::__construct($m_id, $res);
			$this->_module_version = '1.0';
			$this->setLongName(BUGScontext::getI18n()->__('Wiki'));
			$this->setMenuTitle(BUGScontext::getI18n()->__('Wiki'));
			$this->setConfigTitle(BUGScontext::getI18n()->__('Wiki'));
			$this->setDescription(BUGScontext::getI18n()->__('Enables Wiki-functionality'));
			$this->setConfigDescription(BUGScontext::getI18n()->__('Set up the Wiki module from this section'));
			$this->setHasConfigSettings();
			$this->addAvailablePermission('article_management', 'Can create and manage articles');
			$this->addAvailablePermission('manage_billboard', 'Can delete billboard posts');
			$this->addAvailablePermission('publish_postonglobalbillboard', 'Can post articles on global billboard');
			$this->addAvailablePermission('publish_postonteambillboard', 'Can post articles on team billboard');
			$this->addAvailableListener('core', 'index_left_middle', 'listen_latestArticles', 'Frontpage "Last news items"');
			$this->addAvailableListener('core', 'index_right_middle', 'listen_frontpageArticle', 'Frontpage article');
			$this->addAvailableListener('core', 'project_overview_item_links', 'listen_projectLinks', 'Project overview links');
			$this->addAvailableListener('core', 'project_menustrip_item_links', 'listen_projectMenustripLinks', 'Project menustrip links');

			$this->_addRoutes();

			if ($this->getSetting('allow_camelcase_links'))
			{
				BUGSTextParser::addRegex('/(?<![\!|\"|\[|\>|\/\:])\b[A-Z]+[a-z]+[A-Z][A-Za-z]*\b/', array($this, 'getArticleLinkTag'));
				BUGSTextParser::addRegex('/(?<!")\![A-Z]+[a-z]+[A-Z][A-Za-z]*\b/', array($this, 'stripExclamationMark'));
			}
		}

		public function initialize()
		{
		}

		protected function _addRoutes()
		{
			$this->addRoute('publish', '/wiki', 'showArticle', array('article_name' => 'MainPage'));
			$this->addRoute('publish_article_new', '/wiki/new', 'editArticle', array('article_name' => 'NewArticle'));
			$this->addRoute('publish_article', '/wiki/:article_name', 'showArticle');
			$this->addRoute('publish_article_edit', '/wiki/:article_name/edit', 'editArticle');
			$this->addRoute('publish_article_delete', '/wiki/:article_name/delete', 'deleteArticle');
			$this->addRoute('publish_article_save', '/wiki/savearticle', 'saveArticle');
			$this->addRoute('publish_article_history', '/wiki/:article_name/history', 'showArticle');
		}

		public static function install($scope = null)
		{
  			$scope = ($scope === null) ? BUGScontext::getScope()->getID() : $scope;

			$module = parent::_install('publish', 'BUGSpublish','1.0', true, true, false, $scope);

			BUGScontext::setPermission('article_management', 0, 'publish', 0, 1, 0, true, $scope);
			BUGScontext::setPermission('publish_postonglobalbillboard', 0, 'publish', 0, 1, 0, true, $scope);
			BUGScontext::setPermission('publish_postonteambillboard', 0, 'publish', 0, 1, 0, true, $scope);
			BUGScontext::setPermission('manage_billboard', 0, 'publish', 0, 1, 0, true, $scope);
			$module->saveSetting('allow_camelcase_links', 1);

			$module->enableListenerSaved('core', 'index_left_middle');
			$module->enableListenerSaved('core', 'index_right_middle');
			$module->enableListenerSaved('core', 'project_overview_item_links');
			$module->enableListenerSaved('core', 'project_menustrip_item_links');
  									  
			if ($scope == BUGScontext::getScope()->getID())
			{
				B2DB::getTable('B2tArticles')->create();
				B2DB::getTable('B2tArticleViews')->create();
				B2DB::getTable('B2tArticleLinks')->create();
				B2DB::getTable('B2tArticleCategories')->create();
				B2DB::getTable('B2tBillboardPosts')->create();
			}

			try
			{
				BUGScontext::getRouting()->addRoute('publish_article', '/wiki/:article_name', 'publish', 'showArticle');
				self::loadFixtures($scope);
			}
			catch (Exception $e)
			{
				throw $e;
			}

			return true;
		}
		
		static function loadFixtures($scope)
		{
			try
			{
				$article_name = 'FrontpageArticle';
				$content = "== Thank you for installing this preview release of The Bug Genie! ==

We want to make your development environment a lot easier and manageable. You get:<br>
Project management, issue tracking, source code control, fully editable wiki for all your documenation needs, and more.

Please take a few moments setting up your new issue tracker, by clicking the [[TBG:configure|Configure]] menu option in the top menu.<br>
From this page you can configure The Bug Genie the way you want.

For more information on getting started, have a look at GettingStarted, ConfiguringTheBugGenie and CreatingIssues.

To learn more about the wiki formatting used in The Bug Genie, check out WikiFormatting.

<br>
'''Enjoy The Bug Genie!'''

''-The Bug Genie development team''<br>
[http://www.thebuggenie.com]

''ps: this page can be edited from [[FrontpageArticle]]''
";
				PublishArticle::createNew($article_name, $content, true, $scope);

				$article_name = 'GettingStarted';
				$content = "= Getting started with The Bug Genie =
{{TOC}}
== Name, slogan and other settings ==
You might want to set the name and slogan, or change some of the other default settings. You can do this from [[TBG:configure_settings|Configure -> Settings]], where you have all the main settings available.

== Your first project ==
The first thing you want to do is set up your project. Although The Bug Genie can be used without any projects, it's mainly an issue tracker, and that's where it excels.

To add a project, log in as an admin, and either click the little configuration icon next to the project list on the frontpage, or click [[TBG:configure_projects|Configure -> projects]]. This will take you to the projects list. To create a project from here, simply enter the project name in the text box and click \"Add\" or press Enter.

The Bug Genie is now ready to be used for issue reporting with your new project. However, you might want to set up more information about the project. Click the \"Edit project\" link on the project and change any settings in here. From that page you can also add a project description, release date and more information about your project. This is also where you set up editions, components, releases, milestones, and more.

=== Project team ===
Someone is usually working on your project. You should add developers working on The Bug Genie to your project. (This of course requires that your developers have user accounts). Click the \"Edit project\" icon on your project, and then open up the '''Related users''' tab. Add developers, project managers, testers and documentors from this page.

[[Category:Help]][[Category:HowTo]]
";
				PublishArticle::createNew($article_name, $content, true, $scope);

				$article_name = 'ConfigurePermissions';
				$content = "= Setting up and understanding permissions in The Bug Genie =
{{TOC}}
== How are permissions defined? ==
Permissions are defined on a 4-level basis, in the following order:
# '''Global permissions''' - applies to all users, groups and teams
# '''Group-specific permissions''' - applies only to members in that usergroup
# '''Team-specific permissions''' - applies to any member of that team, and you can be a member of multiple teams
# '''User-specific permissions''' - applies to the user it's specified for
Each new level in the above list overrides the previous one. That means if you've set \"''Report issues''\" to \"''Allowed''\" for \"'''''Everyone'''''\" and \"''Denied''\" for the \"'''''Guest group'''''\", then the group permission will override the global permission for all users in the guest group. In addition to this, The Bug Genie has a \"''Permissive''\" or \"''Restrictive''\" security setting which means if you trust all your users you can set this setting to \"''Permissive''\" and everyone will be able to do pretty much anything unless specifically denied. The only exception here is access to the configuration pages, where you must have explicit \"''Allowed''\" access. This gives you fine-grained control over what users, groups and teams can do.

== Permissions hierarchy ==
Many settings are grouped, and most specific settings (ex: \"''Can set issue priority''\") are only available as sub-permissions of more general groups. An example is the \"''Can edit basic information on your own issues''\" permission. This has three sub-settings, \"''Can edit title''\", \"''Can edit descrition''\" and \"''Can edit reproductions steps''\". Using \"'''''Everyone'''''\" as an example, if you set the top setting to \"''Allowed''\", everyone can edit basic information (title, description and reproduction steps) for issues they create. If you want to modify this, say you only want them to change the title, then you can do this two ways:
# Set \"''Can edit description''\" and \"''Can edit reproduction steps''\" to \"''Denied''\". They will still have the basic \"''Can edit basic information (...)''\" setting, which means the title is editable, but the \"''Denied''\" settings for editing description and reproduction steps has higher priority, which means they will not be editable.
# Unset the \"''Can edit basic information (...)''\" permission, and only set \"''Can edit title''\" to \"''Allowed''\".
This grouping is used several places, and is indicated by a little list icon next to the permission icon in the permissions overview. Clicking the list icon brings up all permissions in that group and lets you specify a more fine-grained permission in the group if desired.

== Project-specific permissions ==
Adding to all this, many permissions can be set on a project-specific basis. You might want to only let people report issues for a certain project, or restrict people from doing something on a specific project. The \"''Project-specific permissions''\"-tab in the permissions overview lets you define this. The same rules apply as above, but if a project-specific permission is set, then it will take presedence over the global permission.

== Page access permissions ==
You can limit access to certain pages if you want to restrict users from accessing them. Some of these settings are available as both global and project-specific settings - such as the project-specific pages. This setting is \"''Permissive''\" by default and cannot be changed.

== Datatype permissions ==
Every datatype (status, priority, category, etc) also have their own permissions available for each defined value. This setting is permissive by default (and cannot be changed), but lets you control who can set which field to a certain value. Even if you set a datatype field permission to \"''Denied''\" the user will still be able to ''see'' the field value, but they cannot set it to that value if the permission is \"''Denied''\". This can be useful in a workflow setting where you only want certain users to be able to mark issues as \"''Fixed''\", \"''Closed''\", etc.
'''These permissions are also available for all custom datatypes'''.

== Nice to know about the permissions handling ==
* There is one important exception in effect when you're reporting an issue: If a field is set to \"'''Required'''\" in the reporting process, then the user will have access to this field, even if it set in the permissions manager that they are not allowed to change it. This is to stop the reporting wizard from failing if you're missing access to a required field. They still need access to a field value to set it, so if all field values are set to \"''Denied''\" then they won't have any options to choose from. They will also not be able to change it after the issue has been reported if they don't have access to do so.
* If a user has read or write access to any of the configuration pages, they will automatically see the \"''Configure''\" link in the menu. If they don't have access to any configuration page, this link will be hidden.
* The permissions setting dialog will always show if a setting is permissive or restrictive by default, by showing a faded out \"''Denied''\" or faded out \"''Allowed''\" icon.

[[Category:Help]][[Category:HowTo]]
";
				PublishArticle::createNew($article_name, $content, true, $scope);

				$article_name = 'MainPage';
				$content = "This is the main wiki homepage!";
				PublishArticle::createNew($article_name, $content, true, $scope);

				$article_name = 'WikiFormatting';
				$content = "{{TOC}}
This article will try to explain how to write pages in The Bug Genie wiki.

= Wiki formatting =
The wiki module uses a formatting technique called \"Wiki formatting\", most commonly known from Wikipedia (!MediaWiki).
The Bug Genie wiki tries to stay as close to the !MediaWiki syntax as possible, so if you're familiar with that syntax you should feel right at home.

Wiki formatting is well explained in the [http://en.wikipedia.org/wiki/Help:Wikitext_examples Wikipedia help article], but can be easily summarized as a simple method of formatting your text by placing certain characters.
We will show you the most common syntax below.

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

== Headings ==
To specify headings, use equals-character around the line you want to be a heading. The number of equals-characters you put around the line decides how big the heading is (1 is biggest, 6 is lowest).
  = I'm a big header =
  == I'm a fairly big header ==
  ===== I'm a very small header =====
Headings will automatically appear in the table of contents (if you have one).

== Creating links between documents ==
Traditionally, wikis have used something called [[WIKIPEDIA:CamelCase|Camel Casing]] to create links between documents. CamelCasing means that you put any word or combination of words as a \"'''camel cased'''\" word, and then the wiki will create a link to the document with that name for you automatically. If the page you are trying to link to isn't yet created, the link will still be displayed, and you can click it to start editing the new article.

If you want to write a word with more than one capital letter, use an exclamation mark infront of it - that will stop it from being turned into a link automatically. The support for \"camel casing\" can be turned off in the wiki settings.

You can also use the double square bracket link format to link to internal pages, if you don't want to use the CamelCasing style:
  [[InternalPage]]
[[InternalPage]]
  [[Myproject:MainPage|Myproject wiki frontpage]]
[[Myproject:MainPage|Myproject wiki frontpage]]

With this method you can also link to internal pages in The Bug Genie, by either specifying the relative url (like \"/configure\" points to the configuration page and \"/wiki\" points to the wiki), or use the internal route namespace \"TBG:\" (this has the added benefit of being safe if the link ever changes in a future release).
Unfortunately, the list of routes used in The Bug Genie is quite long, but a few examples are listed below:

  [[/about|About The Bug Genie]]
[[/about|About The Bug Genie]]
  [[/logout|Log out]]
[[/logout|Log out]]
  [[TBG:configure_projects|Configure projects]]
[[TBG:configure_projects|Configure projects]]
  [[TBG:configure_modules|Modules configuration]]
[[TBG:configure_modules|Modules configuration]]

The Bug Genie wiki also lets you link directly to [http://www.wikipedia.org Wikipedia] articles by using the WIKIPEDIA namespace:

  [[WIKIPEDIA:CamelCase]]
[[WIKIPEDIA:CamelCase]]
  [[WIKIPEDIA:CamelCase|Wikipedia article]]
[[WIKIPEDIA:CamelCase|Wikipedia article]]

'''Remember - if all this sound complicated, you can always just use CamelCasing (provided it's not disabled)'''

== Links ==
In addition to linking between internal pages with double square brackets, you can link to external pages with single square brackets. Any URLs inside your text will also automatically be turned into clickable links, but you can also put a pair of square brackets around the link to make it clickable. In addition, you can add a title if you want to:

  http://www.thebuggenie.com
http://www.thebuggenie.com
  [http://www.thebuggenie.com]
[http://www.thebuggenie.com]
  [http://www.thebuggenie.com The Bug Genie website]
[http://www.thebuggenie.com The Bug Genie website]

== Horizontal line ==
If you want to put a horizontal line in the document, use four dashes:

  ----
----

= Advanced usage =

== Categories ==
Your article can be in none, one or more categories. You specify which category your article is in by using the Category namespace link:
  [[Category:Mycategory]]

This won't show up on the page, and you usually place this at the very end of your wiki page when you edit it. Categories will show up in the \"Categories\" area at the bottom of the article.

If you want to link to a Category, instead of put your article '''in''' a category, put a colon in front of the word \"Category\":
  [[:Category:Mycategory]]
[[:Category:Mycategory]]

A category can have one or more ''subcategories''. You put one category in another category by putting the Category link shown above ''inside the subcategory article''. 

'''Ex:''' after having created the page [[:Category:Actors]], create the page [[:Category:MaleActors]], and at the bottom of that article, put:
  [[Category:Actors]]
This will make [[:Category:MaleActors]] a subcategory of [[:Category:Actors]]. That means [[:Category:MaleActors]] will show up in the \"Subcategories\" list on the [[:Category:Actors]] page.

Before you can see the content of a category - its subcategories or pages in that category, the category must be created. To create a category, put an article in a category, click the category link at the bottom of the article, and create the page.
A category page is in essence just any other wiki article, but with the list of subcategories (if any) and pages in that category.

== Images ==
You can show uploaded images by using the same notation as for links:
  [[Image:image1.png]]

== Completely unparsed text ==
If you have some text that you don't want to be automatically parsed at all, put them inside &lt;nowiki&gt;&lt;/nowiki&gt; tags:
  &lt;nowiki&gt;<nowiki>some text that shouldn't be parsed [[a|link]] and '''bold text'''</nowiki>&lt;/nowiki&gt;
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

== Table of contents ==
You can get a table of content on your page (like the one in the top right on this page) by using the TOC variable the same way as the \"normal\" variables above. It doesn't matter where you put this variable in your document, it will always be displayed in the top right corner.

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

[[Category:Help]][[Category:HowTo]]
";
				PublishArticle::createNew($article_name, $content, true, $scope);

				$article_name = 'CamelCasing';
				$content = "'''!CamelCase''' (also spelled \"camel case\") or ''medial capitals'' is the practice of writing compound words or phrases in which the elements are joined without spaces, with each element's initial letter capitalized within the compound, and the first letter can be upper or lower case — as in !LaBelle, !BackColor, !MacGyver, or iPod.

The name comes from the uppercase \"bumps\" in the middle of the compound word, suggestive of the humps of a camel.

The practice is also known by many other names, such as '''!BumpCaps''', '''!BeefCaps''', '''!CapWords''' and '''!WikiWords'''.

'''This is a short introduction to the subject, based on the [[Wikipedia:CamelCase|Wikipedia article]] about camel case.
[[Category:Help]]";
				PublishArticle::createNew($article_name, $content, true, $scope);

				$article_name = 'Category:Help';
				$content = "This is a list of all the available help articles in The Bug Genie. If you are stuck, look here for help.";
				PublishArticle::createNew($article_name, $content, true, $scope);

				$article_name = 'Category:HowTo';
				$content = "[[Category:Help]]";
				PublishArticle::createNew($article_name, $content, true, $scope);

			}
			catch (Exception $e)
			{
				throw $e;
			}
		}
		
		public function uninstall()
		{
			if (BUGScontext::getScope()->getID() == 1)
			{
				B2DB::getTable('B2tArticles')->drop();
				B2DB::getTable('B2tBillboardPosts')->drop();
			}
			parent::_uninstall();
		}

		public function getRoute()
		{
			return BUGScontext::getRouting()->generate('publish');
		}

		public function hasProjectAwareRoute()
		{
			return true;
		}

		public function getProjectAwareRoute($project_key)
		{
			return BUGScontext::getRouting()->generate('publish_article', array('article_name' => ucfirst($project_key).":MainPage"));
		}

		public function postConfigSettings()
		{
			$settings = array('allow_camelcase_links', 'menu_title');
			foreach ($settings as $setting)
			{
				if (BUGScontext::getRequest()->hasParameter($setting))
				{
					$this->saveSetting($setting, BUGScontext::getRequest()->getParameter($setting));
				}
			}
		}

		public function getMenuTitle()
		{
			if (($menu_title = $this->getSetting('menu_title')) !== null)
			{
				$i18n = BUGScontext::getI18n();
				switch ($menu_title)
				{
					case 5: return $i18n->__('Archive');
					case 3: return $i18n->__('Documentation');
					case 4: return $i18n->__('Documents');
					case 2: return $i18n->__('Help');
					case 1: return $i18n->__('Wiki');
				}

			}
			return parent::getMenuTitle();
		}

		public function getSpacedName($camelcased)
		{
			return preg_replace('/(?<=[a-z])(?=[A-Z])/',' ', $camelcased);
		}

		public function stripExclamationMark($matches)
		{
			return substr($matches[0], 1);
		}

		public function getArticleLinkTag($matches)
		{
			BUGScontext::loadLibrary('ui');
			$article_name = $matches[0];
			BUGSTextParser::getCurrentParser()->addInternalLinkOccurrence($article_name);
			$article_name = $this->getSpacedName($matches[0]);
			return link_tag(make_url('publish_article', array('article_name' => $matches[0])), $article_name);
		}


		public function getBillboardPosts($target_board = 0, $posts = 5)
		{
			$crit = new B2DBCriteria();
			$crit->addWhere(B2tBillboardPosts::SCOPE, BUGScontext::getScope()->getID());
			$crit->addWhere(B2tBillboardPosts::IS_DELETED, 0);
			$crit->setLimit($posts);
			$crit->addOrderBy(B2tBillboardPosts::DATE, 'desc');
			if (is_array($target_board))
			{
				$crit->addWhere(B2tBillboardPosts::TARGET_BOARD, $target_board, B2DBCriteria::DB_IN);
			}
			else
			{
				$crit->addWhere(B2tBillboardPosts::TARGET_BOARD, $target_board);
			}
	
			$posts = array();
	
			$res = B2DB::getTable('B2tBillboardPosts')->doSelect($crit);
			while ($row = $res->getNextRow())
			{
				$posts[] = new PublishBillboardPost($row);
			}
	
			return $posts;
		}
		
		public function getLatestArticles($limit = 5)
		{
			return $this->getArticles($limit, true);
		}
	
		public function getAllArticles()
		{
			$crit = new B2DBCriteria();
			$crit->addOrderBy(B2tArticles::ORDER, 'asc');
			$crit->addOrderBy(B2tArticles::DATE, 'desc');
			$res = B2DB::getTable('B2tArticles')->doSelect($crit);
			$articles = array();
			while ($row = $res->getNextRow())
			{
				$articles[] = PublishFactory::articleLab($row->get(B2tArticles::ID), $row);
			}
			return $articles;
		}
		
		public function getArticles($num_articles = 5, $news = false, $published = true)
		{
			$crit = new B2DBCriteria();
			$crit->addWhere(B2tArticles::SCOPE, BUGScontext::getScope()->getID());
			$crit->addWhere(B2tArticles::ARTICLE_NAME, 'Category:%', B2DBCriteria::DB_NOT_LIKE);
			
			$crit->addOrderBy(B2tArticles::DATE, 'desc');
			
			if ($published) $crit->addWhere(B2tArticles::IS_PUBLISHED, 1);
	
			$articles = array();
			
			if ($res = B2DB::getTable('B2tArticles')->doSelect($crit))
			{
				while (($row = $res->getNextRow()) && (count($articles) < $num_articles))
				{
					try
					{
						$article = PublishFactory::articleLab($row->get(B2tArticles::ID), $row);
					}
					catch (Exception $e) 
					{
						continue;
					}
					
					if ($article->canRead())
					{
						$articles[] = $article;
					}
				}
			}
	
			return $articles;
		}

		public function getMenuItems()
		{
			return array();
		}

		public function getUserDrafts()
		{
			$articles = array();

			if ($res = B2DB::getTable('B2tArticles')->getUnpublishedArticlesByUser(BUGScontext::getUser()->getID()))
			{
				while ($row = $res->getNextRow())
				{
					try
					{
						$article = PublishFactory::articleLab($row->get(B2tArticles::ID), $row);
					}
					catch (Exception $e)
					{
						continue;
					}

					if ($article->canRead())
					{
						$articles[] = $article;
					}
				}
			}

			return $articles;
		}
		
		public function getFrontpageArticle()
		{
			if ($row = B2DB::getTable('B2tArticles')->getArticleByName('FrontpageArticle'))
			{
				return PublishFactory::articleLab($row->get(B2tArticles::ID), $row);
			}
			return null;
		}
		
		public function listen_frontpageArticle()
		{
			$index_article = $this->getFrontpageArticle();
			if ($index_article instanceof PublishArticle)
			{
				BUGSactioncomponent::includeComponent('publish/articledisplay', array('article' => $index_article, 'show_title' => false, 'show_details' => false, 'show_actions' => false, 'embedded' => true));
			}
		}

		public function listen_latestArticles()
		{
			BUGSactioncomponent::includeComponent('publish/latestArticles');
		}

		public function listen_projectLinks($params)
		{
			BUGSactioncomponent::includeTemplate('publish/projectlinks', $params);
		}

		public function listen_projectMenustripLinks($params)
		{
			BUGSactioncomponent::includeTemplate('publish/projectmenustriplinks', $params);
		}

		public function getTabKey()
		{
			return (BUGScontext::isProjectContext()) ? parent::getTabKey() : 'wiki';
		}

	}

?>
