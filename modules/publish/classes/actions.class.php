<?php

	/**
	 * actions for the publish module
	 *
	 * @property TBGWikiArticle $article
	 *
	 */
	class publishActions extends TBGAction
	{

		protected function _getArticleNameDetails($original_article_name)
		{
			$namespace = mb_substr($original_article_name, 0, mb_strpos($original_article_name, ':'));
			$article_name = mb_substr($original_article_name, mb_strpos($original_article_name, ':') + 1);

			if (strtolower($namespace) == 'special')
			{
				$this->special = true;
				$namespace = mb_substr($article_name, 0, mb_strpos($article_name, ':'));
				if ($namespace) 
				{
					$this->selected_project = TBGProject::getByKey($namespace);
					$article_name = mb_substr($article_name, mb_strpos($article_name, $namespace) + strlen($namespace) + 1);
				}
				$article_name = mb_strtolower(mb_substr($article_name, mb_strpos($article_name, ':')));
			}
			elseif ($namespace == 'Category')
			{
				$namespace = mb_substr($article_name, 0, mb_strpos($article_name, ':'));
				$article_name = mb_substr($article_name, mb_strpos($article_name, ':') + 1);
			}

			if ($namespace != '')
			{
				$key = mb_strtolower($namespace);
				$this->selected_project = TBGProject::getByKey($key);
			}
			
			return ($namespace == 'Category' || $this->special) ? $article_name : $original_article_name;
		}
		
		/**
		 * Pre-execute function
		 *
		 * @param TBGRequest $request
		 */
		public function preExecute(TBGRequest $request, $action)
		{
			$this->article = null;
			$this->article_name = $request['article_name'];
			$this->article_id = (int) $request['article_id'];
			$this->special = false;

			if ($request->hasParameter('article_name') && mb_strpos($request['article_name'], ':') !== false)
			{
				$this->article_name = $this->_getArticleNameDetails($request['article_name']);
			}
			else
			{
				try
				{
					if ($project_key = $request['project_key'])
					{
						$this->selected_project = TBGProject::getByKey($project_key);
					}
					elseif ($project_id = (int) $request['project_id'])
					{
						$this->selected_project = TBGProjectsTable::getTable()->selectById($project_id);
					}
				}
				catch (Exception $e) {}
			}

			if ($this->selected_project instanceof TBGProject)
			{
				if (!$this->selected_project->hasAccess())
				{
					$this->forward403();
				}
				else
				{
					TBGContext::setCurrentProject($this->selected_project);
				}
			}

			if (!$this->special)
			{
				if ($this->article_id)
				{
					$this->article = TBGArticlesTable::getTable()->selectById($this->article_id);
				}
				elseif ($this->article_name)
				{
					$this->article = TBGArticlesTable::getTable()->getArticleByName($this->article_name);
				}

				if (!$this->article instanceof TBGWikiArticle)
				{
					$this->article = new TBGWikiArticle();
					if ($this->article_name) 
					{
						$this->article->setName($this->article_name);
					}
					elseif ($request->hasParameter('parent_article_name')) 
					{
						$this->article->setParentArticle(TBGArticlesTable::getTable()->getArticleByName($request['parent_article_name']));
						if ($this->article->getParentArticle() instanceof TBGWikiArticle)
						{
							if ($this->article->getParentArticle()->getArticleType() == TBGWikiArticle::TYPE_WIKI)
							{
								$this->article->setName($this->article->getParentArticle()->getName() . ':');
							}
							$this->_getArticleNameDetails($this->article->getParentArticle()->getName());
							$this->article->setArticleType($this->article->getParentArticle()->getArticleType());
						}
					}
					$this->article->setContentSyntax($this->getUser()->getPreferredWikiSyntax(true));
				}
			}
		}
		
		public function runSpecialArticle(TBGRequest $request)
		{
			$this->component = null;
			if (TBGActionComponent::doesComponentExist("publish/special{$this->article_name}", false))
			{
				$this->component = $this->article_name;
				$this->projectnamespace = ($this->selected_project instanceof TBGProject) ? ucfirst($this->selected_project->getKey()).':' : '';
			}
		}

		/**
		 * Show an article
		 *
		 * @param TBGRequest $request
		 */
		public function runShowArticle(TBGRequest $request)
		{
			if ($this->special) $this->redirect('specialArticle');

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
					if (!$request->hasParameter('no_redirect') && $this->article->isRedirect())
					{
						if ($redirect_article = $this->article->getRedirectArticleName())
						{
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
						$article_name = $this->article->getName();
						if (!TBGContext::getModule('publish')->canUserEditArticle($article_name))
						{
							TBGContext::setMessage('publish_article_error', TBGContext::getI18n()->__('You do not have permission to edit this article'));
							$this->forward(TBGContext::getRouting()->generate('publish_article_history', array('article_name' => $article_name)));
						}
						$revision = $request['revision'];
						if ($revision)
						{
							$this->article->restoreRevision($revision);
							$this->forward(TBGContext::getRouting()->generate('publish_article_history', array('article_name' => $article_name)));
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
			try
			{
				if (!$this->article instanceof TBGWikiArticle)
				{
					throw new Exception($this->getI18n()->__('This article does not exist'));
				}
				if (!TBGContext::getModule('publish')->canUserDeleteArticle($this->article->getName()))
				{
					throw new Exception($this->getI18n()->__('You do not have permission to delete this article'));
				}
				if (!$request['article_name'])
				{
					throw new Exception($this->getI18n()->__('Please specify an article name'));
				}
				else
				{
					TBGWikiArticle::deleteByName($request['article_name']);
				}
			}
			catch (Exception $e)
			{
				$this->getResponse()->setHttpStatus(400);
				return $this->renderJSON(array('title' => $this->getI18n()->__('An error occured'), 'error' => $e->getMessage()));
			}
			return $this->renderJSON(array('message' => $this->getI18n()->__('The article was deleted')));
		}

		/**
		 * Get avilable parent articles for an article
		 *
		 * @param TBGRequest $request
		 */
		public function runGetAvailableParents(TBGRequest $request)
		{
			$articles = TBGArticlesTable::getTable()->getManualSidebarArticles(TBGContext::getCurrentProject());
			
			$parent_articles = array();
			foreach ($articles as $article)
			{
				if ($article->getID() == $this->article->getID()) continue;
				$parent_articles[$article->getName()] = $article->getManualName();
			}
			
			return $this->renderJSON(array('list' => $this->getTemplateHTML('publish/getavailableparents', compact('parent_articles'))));
		}

		/**
		 * Show an article
		 *
		 * @param TBGRequest $request
		 */
		public function runEditArticle(TBGRequest $request)
		{
			if (!$this->article->canEdit())
			{
				TBGContext::setMessage('publish_article_error', TBGContext::getI18n()->__('You do not have permission to edit this article'));
				$this->forward(TBGContext::getRouting()->generate('publish_article', array('article_name' => $this->article_name)));
			}

			$this->article_route = ($this->article->getID()) ? 'publish_article_edit' : 'publish_article_new';
			$this->article_route_params = ($this->article->getID()) ? array('article_name' => $this->article_name) : array();
			
			if ($request->isPost())
			{
				$this->preview = (bool) $request['preview'];
				$this->change_reason = $request['change_reason'];
				try
				{
					$this->article->setArticleType($request['article_type']);
					$this->article->setName($request['new_article_name']);
					$this->article->setParentArticle(TBGArticlesTable::getTable()->getArticleByName($request['parent_article_name']));
					$this->article->setManualName($request['manual_name']);
					if ($this->article->getArticleType() == TBGWikiArticle::TYPE_MANUAL && !$this->article->getName())
					{
						$article_name_prefix = ($this->article->getParentArticle() instanceof TBGWikiArticle) ? $this->article->getParentArticle()->getName() . ':' : '';
						$this->article->setName(str_replace(' ', '', $article_name_prefix . $this->article->getManualName()));
					}
					$this->article->setContentSyntax($request['article_content_syntax']);
					$this->article->setContent($request->getRawParameter('article_content'));

					if (!$this->article->getName() || trim($this->article->getName()) == '' || !preg_match('/[\w:]+/i', $this->article->getName()))
						throw new Exception(TBGContext::getI18n()->__('You need to specify a valid article name'));

					if ($request['article_type'] == TBGWikiArticle::TYPE_MANUAL && (!$this->article->getManualName() || trim($this->article->getManualName()) == '' || !preg_match('/[\w:]+/i', $this->article->getManualName())))
						throw new Exception(TBGContext::getI18n()->__('You need to specify a valid article name'));

					if (TBGPublish::getModule()->getSetting('require_change_reason') == 1 && (!$this->change_reason || trim($this->change_reason) == ''))
						throw new Exception(TBGContext::getI18n()->__('You have to provide a reason for the changes'));

					if ($this->article->getLastUpdatedDate() != $request['last_modified'])
						throw new Exception(TBGContext::getI18n()->__('The file has been modified since you last opened it'));

					if (($article = TBGWikiArticle::getByName($request['new_new_article_name'])) && $article instanceof TBGWikiArticle && $article->getID() != $request['article_id'])
						throw new Exception(TBGContext::getI18n()->__('An article with that name already exists. Please choose a different article name'));

					if (!$this->preview)
					{
						$this->article->doSave(array(), $request['change_reason']);
						TBGContext::setMessage('publish_article_message', TBGContext::getI18n()->__('The article was saved'));
						$this->forward(TBGContext::getRouting()->generate('publish_article', array('article_name' => $this->article->getName())));
					}
				}
				catch (Exception $e)
				{
					$this->error = $e->getMessage();
				}
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

		/**
		 * Toggle favourite article (starring)
		 *  
		 * @param TBGRequest $request
		 */
		public function runToggleFavouriteArticle(TBGRequest $request)
		{
			if ($article_id = $request['article_id'])
			{
				try
				{
					$article = TBGArticlesTable::getTable()->selectById($article_id);
					$user = TBGUsersTable::getTable()->selectById($request['user_id']);
				}
				catch (Exception $e)
				{
					return $this->renderText('fail');
				}
			}
			else
			{
				return $this->renderText('no article');
			}
			
			if ($user->isArticleStarred($article_id))
			{
				$retval = !$user->removeStarredArticle($article_id);
			}
			else
			{
				$retval = $user->addStarredArticle($article_id);
				if ($user->getID() != $this->getUser()->getID())
				{
					TBGEvent::createNew('core', 'article_subscribe_user', $article, compact('user'))->trigger();
				}
			}

			return $this->renderText(json_encode(array('starred' => $retval, 'subscriber' => $this->getTemplateHTML('publish/articlesubscriber', array('user' => $user, 'article' => $article)))));
		}
		
	}
