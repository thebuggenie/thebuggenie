<?php

	/**
	 * actions for the publish module
	 */
	class publishActions extends TBGAction
	{

		/**
		 * Pre-execute function
		 *
		 * @param TBGRequest $request
		 */
		public function preExecute(TBGRequest $request, $action)
		{
			$this->getResponse()->setPage('wiki');
			$i18n = TBGContext::getI18n();

			$this->article = null;
			$this->article_name = $request['article_name'];

			if ($request->hasParameter('article_name') && mb_strpos($request['article_name'], ':') !== false)
			{
				$namespace = mb_substr($this->article_name, 0, mb_strpos($this->article_name, ':'));
				$article_name = mb_substr($this->article_name, mb_strpos($this->article_name, ':') + 1);

				if ($namespace == 'Category')
				{
					$namespace = mb_substr($article_name, 0, mb_strpos($article_name, ':'));
					$article_name = mb_substr($article_name, mb_strpos($article_name, ':') + 1);
				}
				
				if ($namespace != '')
				{
					$key = mb_strtolower($namespace);
					$row = TBGProjectsTable::getTable()->getByKey($key);
					
					if ($row instanceof \b2db\Row)
					{
						$project = TBGContext::factory()->TBGProject($row->get(TBGProjectsTable::ID), $row);
						
						if ($project instanceof TBGProject)
							$this->forward403unless($project->hasAccess());

						TBGContext::setCurrentProject($project);
					}
				}
			}
			else
			{
				try
				{
					if ($project_key = $request['project_key'])
					{
						$row = TBGProjectsTable::getTable()->getByKey($project_key);
						
						$this->selected_project = TBGContext::factory()->TBGProject($row->get(TBGProjectsTable::ID), $row);
					}
					elseif ($project_id = (int) $request['project_id'])
						$this->selected_project = TBGContext::factory()->TBGProject($project_id);

					if ($this->selected_project instanceof TBGProject)
						$this->forward403unless($this->selected_project->hasAccess());

					TBGContext::setCurrentProject($this->selected_project);
				}
				catch (Exception $e) {}
				
			}
			
			if ($row = TBGArticlesTable::getTable()->getArticleByName($this->article_name))
			{
				$this->article = PublishFactory::article($row->get(TBGArticlesTable::ID), $row);
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
			$this->redirected_from = TBGContext::getMessageAndClear('publish_redirected_article');

			if ($this->article instanceof TBGWikiArticle)
			{
				if (!$this->article->hasAccess())
				{
					$this->error = TBGContext::getI18n()->__("You don't have access to read this article");
					$this->article = null;
				}
				else
				{
					if (!$request->hasParameter('no_redirect') && mb_substr($this->article->getContent(), 0, 10) == "#REDIRECT ")
					{
						$content = explode("\n", $this->article->getContent());
						preg_match('/(\[\[([^\]]*?)\]\])$/im', mb_substr(array_shift($content), 10), $matches);
						if (count($matches) == 3)
						{
							$redirect_article = $matches[2];
							TBGContext::setMessage('publish_redirected_article', $this->article->getName());
							$this->forward(TBGContext::getRouting()->generate('publish_article', array('article_name' => $redirect_article)));
						}
					}
					try
					{
						if ($request->hasParameter('revision'))
						{
							$this->revision = $request['revision'];
							$this->article->setRevision($this->revision);
						}
					}
					catch (Exception $e)
					{
						$this->error = TBGContext::getI18n()->__('There was an error trying to show this revision');
					}
				}
			}
		}

		public function runArticleAttachments(TBGRequest $request)
		{
			
		}
		
		public function runArticlePermissions(TBGRequest $request)
		{
			if ($this->article instanceof TBGWikiArticle)
			{
				$this->forward403unless($this->article->canEdit());
				$namespaces = $this->article->getCombinedNamespaces();
				$namespaces[] = $this->article->getName();
				array_unshift($namespaces, 0);
				$this->namespaces = array_reverse($namespaces);
			}
		}
		
		public function runArticleHistory(TBGRequest $request)
		{
			$this->history_action = $request['history_action'];
			if ($this->article instanceof TBGWikiArticle)
			{	
				$this->history = $this->article->getHistory();
				$this->revision_count = count($this->history);

				switch ($this->history_action)
				{
					case 'list':
						break;
					case 'diff':
						$from_revision = $request['from_revision'];
						$to_revision = $request['to_revision'];

						if (!$from_revision || !$to_revision)
						{
							$this->error = TBGContext::getI18n()->__('Please specify a from- and to-revision to compare');
						}
						else
						{
							list ($content, $diff) = $this->article->compareRevisions($from_revision, $to_revision);

							$this->from_revision = $from_revision;
							$this->from_revision_author = $content[$from_revision]['author'];
							$this->from_revision_date = $content[$from_revision]['date'];
							$this->to_revision = $to_revision;
							$this->to_revision_author = $content[$to_revision]['author'];
							$this->to_revision_date = $content[$to_revision]['date'];

							$this->diff = explode("\n", $diff);
						}
						break;
					case 'revert':
						if (!TBGContext::getModule('publish')->canUserEditArticle($article_name))
						{
							TBGContext::setMessage('publish_article_error', TBGContext::getI18n()->__('You do not have permission to edit this article'));
							$this->forward(TBGContext::getRouting()->generate('publish_article_history', array('article_name' => $article_name)));
						}
						$revision = $request['revision'];
						if ($revision)
						{
							$this->article->restoreRevision($revision);
							$this->forward(TBGContext::getRouting()->generate('publish_article_history', array('article_name' => $this->article->getName())));
						}
						else
						{
							$this->forward(TBGContext::getRouting()->generate('publish_article_history', array('article_name' => $this->article->getName())));
						}
				}
			}
		}

		/**
		 * Delete an article
		 *
		 * @param TBGRequest $request
		 */
		public function runDeleteArticle(TBGRequest $request)
		{
			if (!TBGContext::getModule('publish')->canUserDeleteArticle($this->article->getName()))
			{
				TBGContext::setMessage('publish_article_error', TBGContext::getI18n()->__('You do not have permission to delete this article'));
				$this->forward(TBGContext::getRouting()->generate('publish_article', array('article_name' => $this->article->getName())));
			}
			if ($article_name = $request['article_name'])
			{
				TBGWikiArticle::deleteByName($article_name);
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
			$article_name = ($this->article instanceof TBGWikiArticle) ? $this->article->getName() : $request['article_name'];
			if (!TBGContext::getModule('publish')->canUserEditArticle($article_name))
			{
				TBGContext::setMessage('publish_article_error', TBGContext::getI18n()->__('You do not have permission to edit this article'));
				$this->forward(TBGContext::getRouting()->generate('publish_article', array('article_name' => $article_name)));
			}
			if ($request->isPost())
			{
				if ($request->hasParameter('new_article_name') && $request['new_article_name'] != '')
				{
					if (($request->hasParameter('change_reason') && trim($request['change_reason']) != '') || TBGPublish::getModule()->getSetting('require_change_reason') == 0)
					{
						try
						{
							if ($request['article_id'])
							{
								if (($article = PublishFactory::article($request['article_id'])) && $article instanceof TBGWikiArticle)
								{
									if ($article->getLastUpdatedDate() != $request['last_modified'])
									{
										$this->error = TBGContext::getI18n()->__('The file has been modified since you last opened it');
									}
									else
									{
										try
										{
											$article->setName($request['new_article_name']);
											$article->setContent($request->getRawParameter('new_article_content'));
											if ($request['preview'])
											{
												$this->article = $article;
											}
											else
											{
												$article->doSave(array(), $request['change_reason']);
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

						if (($article = TBGWikiArticle::getByName($request['new_article_name'])) && $article instanceof TBGWikiArticle && $article->getID() != $request['article_id'])
						{
							$this->error = TBGContext::getI18n()->__('An article with that name already exists. Please choose a different article name');
						}
						elseif (!$article instanceof TBGWikiArticle)
						{
							if ($request['preview'])
							{
								$article = new TBGWikiArticle();
								$article->setContent($request->getRawParameter('new_article_content'));
								$article->setName($request['new_article_name']);
								$this->article = $article;							}
							else
							{
								$article_id = TBGWikiArticle::createNew($request['new_article_name'], $request->getRawParameter('new_article_content', ''), true);

								$this->forward(TBGContext::getRouting()->generate('publish_article', array('article_name' => $request['new_article_name'])));
							}

						}
					}
					else
					{
						$this->error = TBGContext::getI18n()->__('You have to provide a reason for the changes');
					}
				}
				else
				{
					$this->error = TBGContext::getI18n()->__('You need to specify the article name');
				}
			}
			$this->preview = (bool) $request['preview'];
			$this->article_title = null;
			$this->article_content = null;
			$this->article_intro = null;
			$this->change_reason = null;
			
			if ($this->article instanceof TBGWikiArticle)
			{
				$this->forward403unless($this->article->canEdit());
				$this->article_title = $this->article->getTitle();
				$this->article_content = $this->article->getContent();

				if ($request->isPost())
				{
					if ($request->hasParameter('new_article_name'))
					{
						$this->article_title = $request['new_article_name'];
						$this->article->setName($request['new_article_name']);
					}
					if ($request->hasParameter('new_article_content'))
					{
						$this->article_content = $request->getRawParameter('new_article_content');
					}
					if ($request->hasParameter('change_reason'))
					{
						$this->change_reason = $request['change_reason'];
					}
				}
			}
			else
			{
				$this->forward403if(TBGContext::isProjectContext() && TBGContext::getCurrentProject()->isArchived());
				if ($request->hasParameter('new_article_content'))
				{
					$this->article_content = $request->getRawParameter('new_article_content');
				}
					
				TBGContext::loadLibrary('publish');
				$this->article_title = str_replace(array(':', '_'), array(' ', ' '), get_spaced_name($this->article_name));
			}
		}
		
		public function runFindArticles(TBGRequest $request)
		{
			$this->articlename = $request['articlename'];
			
			if ($this->articlename)
			{
				list ($this->resultcount, $this->articles) = TBGWikiArticle::findArticlesByContentAndProject($this->articlename, TBGContext::getCurrentProject(), 10);
			}
		}

	}