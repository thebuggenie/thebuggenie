<?php

	class publishActions extends BUGSaction
	{

		/**
		 * Pre-execute function
		 *
		 * @param BUGSrequest $request
		 */
		public function preExecute($request, $action)
		{
			$this->getResponse()->setProjectMenuStripHidden();
			$this->getResponse()->setPage('wiki');

			$this->article = null;
			$this->article_name = $request->getParameter('article_name');

			if ($request->hasParameter('article_name') && strpos($request->getParameter('article_name'), ':') !== false)
			{
				$namespace = substr($this->article_name, 0, strpos($this->article_name, ':'));
				$article_name = substr($this->article_name, strpos($this->article_name, ':') + 1);

				if ($namespace == 'Category')
				{
					$namespace = substr($article_name, 0, strpos($article_name, ':'));
					$article_name = substr($article_name, strpos($article_name, ':') + 1);
				}
				
				if (($project = BUGSproject::getByKey($namespace)) instanceof BUGSproject)
				{
					BUGScontext::setCurrentProject($project);
					$this->getResponse()->setProjectMenuStripHidden(false);
				}
			}
			
			if ($row = B2DB::getTable('B2tArticles')->getArticleByName($this->article_name))
			{
				$this->article = PublishFactory::articleLab($row->get(B2tArticles::ID), $row);
			}
		}

		/**
		 * Show an article
		 *
		 * @param BUGSrequest $request
		 */
		public function runShowArticle($request)
		{
			$this->message = BUGScontext::getMessageAndClear('publish_article_message');
			$this->error = BUGScontext::getMessageAndClear('publish_article_error');
		}

		/**
		 * Delete an article
		 *
		 * @param BUGSrequest $request
		 */
		public function runDeleteArticle($request)
		{
			if ($article_name = $request->getParameter('article_name'))
			{
				PublishArticle::deleteByName($article_name);
				BUGScontext::setMessage('publish_article_error', BUGScontext::getI18n()->__('The article was deleted'));
				$this->forward(BUGScontext::getRouting()->generate('publish_article', array('article_name' => $article_name)));
			}
		}

		/**
		 * Show an article
		 *
		 * @param BUGSrequest $request
		 */
		public function runEditArticle($request)
		{
			if ($request->isMethod(BUGSrequest::POST))
			{
				if ($request->hasParameter('new_article_name') && $request->getParameter('new_article_name') != '')
				{
					try
					{
						if ($request->getParameter('article_id') != 0 && ($article = PublishFactory::articleLab($request->getParameter('article_id'))) && $article instanceof PublishArticle)
						{
							if ($article->getLastUpdatedDate() != $request->getParameter('last_modified'))
							{
								$this->error = BUGScontext::getI18n()->__('The file has been modified since you last opened it');
							}
							else
							{
								try
								{
									$article->setName($request->getParameter('new_article_name'));
									$article->setContent($request->getRawParameter('new_article_content'));
									$article->save();
									BUGScontext::setMessage('publish_article_message', BUGScontext::getI18n()->__('The article was saved'));
									$this->forward(BUGScontext::getRouting()->generate('publish_article', array('article_name' => $article->getName())));
								}
								catch (Exception $e)
								{
									$this->error = $e->getMessage();
								}
							}
						}
					}
					catch (Exception $e) {}
					
					if (($article = PublishArticle::getByName($request->getParameter('new_article_name'))) && $article instanceof PublishArticle)
					{
						$this->error = BUGScontext::getI18n()->__('An article with that name already exists. Please choose a different article name');
					}
					else
					{
						$article_id = PublishArticle::createNew($request->getParameter('new_article_name'), $request->getRawParameter('new_article_content', ''), true);

						// Trigger this once so it saves categories, links, etc.
						PublishFactory::articleLab($article_id)->save();
						
						$this->forward(BUGScontext::getRouting()->generate('publish_article', array('article_name' => $request->getParameter('new_article_name'))));
					}
				}
				else
				{
					$this->error = BUGScontext::getI18n()->__('You need to specify the article name');
				}
			}
			$this->article_title = null;
			$this->article_content = null;
			$this->article_intro = null;

			if ($this->article instanceof PublishArticle)
			{
				$this->article_title = $this->article->getTitle();
				$this->article_content = $this->article->getContent();
				$this->article_intro = $this->article->getIntro();
			}
			else
			{
				BUGScontext::loadLibrary('publish');
				$this->article_title = str_replace(array(':', '_'), array(' ', ' '), get_spaced_name($this->article_name));
			}
		}

	}