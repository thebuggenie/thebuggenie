<?php

	class publishActions extends TBGAction
	{

		/**
		 * Pre-execute function
		 *
		 * @param TBGRequest $request
		 */
		public function preExecute(TBGRequest $request, $action)
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
				
				if (($project = TBGProject::getByKey($namespace)) instanceof TBGProject)
				{
					TBGContext::setCurrentProject($project);
					$this->getResponse()->setProjectMenuStripHidden(false);
				}
			}
			
			if ($row = B2DB::getTable('TBGArticlesTable')->getArticleByName($this->article_name))
			{
				$this->article = PublishFactory::articleLab($row->get(TBGArticlesTable::ID), $row);
			}
		}

		/**
		 * Show an article
		 *
		 * @param TBGRequest $request
		 */
		public function runShowArticle(TBGRequest $request)
		{
			$this->message = TBGContext::getMessageAndClear('publish_article_message');
			$this->error = TBGContext::getMessageAndClear('publish_article_error');
		}

		/**
		 * Delete an article
		 *
		 * @param TBGRequest $request
		 */
		public function runDeleteArticle(TBGRequest $request)
		{
			if ($article_name = $request->getParameter('article_name'))
			{
				PublishArticle::deleteByName($article_name);
				TBGContext::setMessage('publish_article_error', TBGContext::getI18n()->__('The article was deleted'));
				$this->forward(TBGContext::getRouting()->generate('publish_article', array('article_name' => $article_name)));
			}
		}

		/**
		 * Show an article
		 *
		 * @param TBGRequest $request
		 */
		public function runEditArticle(TBGRequest $request)
		{
			if ($request->isMethod(TBGRequest::POST))
			{
				if ($request->hasParameter('new_article_name') && $request->getParameter('new_article_name') != '')
				{
					try
					{
						if ($request->getParameter('article_id'))
						{
							if (($article = PublishFactory::articleLab($request->getParameter('article_id'))) && $article instanceof PublishArticle)
							{
								if ($article->getLastUpdatedDate() != $request->getParameter('last_modified'))
								{
									$this->error = TBGContext::getI18n()->__('The file has been modified since you last opened it');
								}
								else
								{
									try
									{
										$article->setName($request->getParameter('new_article_name'));
										$article->setContent($request->getRawParameter('new_article_content'));
										if ($request->getParameter('preview'))
										{
											$this->article = $article;
										}
										else
										{
											$article->save();
											TBGContext::setMessage('publish_article_message', TBGContext::getI18n()->__('The article was saved'));
											$this->forward(TBGContext::getRouting()->generate('publish_article', array('article_name' => $article->getName())));
										}
									}
									catch (Exception $e)
									{
										$this->error = $e->getMessage();
									}
								}
							}
						}
					}
					catch (Exception $e) {}
					
					if (($article = PublishArticle::getByName($request->getParameter('new_article_name'))) && $article instanceof PublishArticle && $article->getID() != $request->getParameter('article_id'))
					{
						$this->error = TBGContext::getI18n()->__('An article with that name already exists. Please choose a different article name');
					}
					elseif (!$article instanceof PublishArticle)
					{
						if ($request->getParameter('preview'))
						{
							$article = new PublishArticle();
							$article->setContent($request->getRawParameter('new_article_content'));
							$article->setName($request->getParameter('new_article_name'));
							$this->article = $article;
						}
						else
						{
							$article_id = PublishArticle::createNew($request->getParameter('new_article_name'), $request->getRawParameter('new_article_content', ''), true);

							$this->forward(TBGContext::getRouting()->generate('publish_article', array('article_name' => $request->getParameter('new_article_name'))));
						}

					}
				}
				else
				{
					$this->error = TBGContext::getI18n()->__('You need to specify the article name');
				}
			}
			$this->preview = (bool) $request->getParameter('preview');
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
				TBGContext::loadLibrary('publish');
				$this->article_title = str_replace(array(':', '_'), array(' ', ' '), get_spaced_name($this->article_name));
			}
		}

	}