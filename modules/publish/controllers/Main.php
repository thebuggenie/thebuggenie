<?php

    namespace thebuggenie\modules\publish\controllers;

    use thebuggenie\core\framework,
        thebuggenie\modules\publish\entities,
        thebuggenie\modules\publish\entities\Article,
        thebuggenie\modules\publish\entities\tables\Articles;

    /**
     * actions for the publish module
     *
     * @property Article $article
     *
     */
    class Main extends framework\Action
    {

        protected function _getArticleNameDetails($article_name)
        {
            $namespaces = explode(':', $article_name);
            $namespace = array_shift($namespaces);

            if (strtolower($namespace) == 'special')
            {
                $this->special = true;
                $namespace = null;
                if (count($namespaces) > 1)
                {
                    $namespace = array_shift($namespaces);
                }
                $article_name = mb_strtolower(array_shift($namespaces));
            }
            elseif ($namespace == 'Category')
            {
                $namespace = array_shift($namespaces);
            }

            if (!is_null($namespace))
            {
                $key = mb_strtolower($namespace);
                $this->selected_project = \thebuggenie\core\entities\Project::getByKey($key);
            }

            return $article_name;
        }

        /**
         * Pre-execute function
         *
         * @param \thebuggenie\core\framework\Request $request
         */
        public function preExecute(framework\Request $request, $action)
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
                        $this->selected_project = \thebuggenie\core\entities\Project::getByKey($project_key);
                    }
                    elseif ($project_id = (int) $request['project_id'])
                    {
                        $this->selected_project = \thebuggenie\core\entities\tables\Projects::getTable()->selectById($project_id);
                    }
                }
                catch (\Exception $e)
                {

                }
            }

            if (!$this->special)
            {
                if ($this->article_id)
                {
                    $this->article = Articles::getTable()->selectById($this->article_id);
                }
                elseif ($this->article_name)
                {
                    $this->article = Articles::getTable()->getArticleByName($this->article_name);
                }

                if (!$this->article instanceof Article)
                {
                    $this->article = new Article();
                    if ($this->article_name)
                    {
                        $this->article->setName($this->article_name);
                    }
                    elseif ($request->hasParameter('parent_article_name'))
                    {
                        $this->article->setParentArticle(Articles::getTable()->getArticleByName($request['parent_article_name']));
                        $this->_getArticleNameDetails($request['parent_article_name']);
                        if ($this->article->getParentArticle() instanceof Article)
                        {
                            if ($this->article->getParentArticle()->getArticleType() == Article::TYPE_WIKI)
                            {
                                $this->article->setName($this->article->getParentArticle()->getName() . ':');
                            }
                            $this->article->setArticleType($this->article->getParentArticle()->getArticleType());
                        }
                    }
                    $this->article->setContentSyntax($this->getUser()->getPreferredWikiSyntax(true));
                }
            }

            if ($this->selected_project instanceof \thebuggenie\core\entities\Project)
            {
                if (!$this->selected_project->hasAccess())
                {
                    $this->forward403();
                }
                else
                {
                    framework\Context::setCurrentProject($this->selected_project);
                }
            }
        }

        public function runSpecialArticle(framework\Request $request)
        {
            $this->component = null;
            if (framework\ActionComponent::doesComponentExist("publish/special{$this->article_name}", false))
            {
                $this->component = $this->article_name;
                $this->projectnamespace = ($this->selected_project instanceof \thebuggenie\core\entities\Project) ? ucfirst($this->selected_project->getKey()) . ':' : '';
            }
        }

        /**
         * Show an article
         *
         * @param \thebuggenie\core\framework\Request $request
         */
        public function runShowArticle(framework\Request $request)
        {
            if ($this->special)
                $this->redirect('specialArticle');

            $this->message = framework\Context::getMessageAndClear('publish_article_message');
            $this->error = framework\Context::getMessageAndClear('publish_article_error');
            $this->redirected_from = framework\Context::getMessageAndClear('publish_redirected_article');

            if ($this->article instanceof Article)
            {
                if (!$this->article->hasAccess())
                {
                    $this->error = framework\Context::getI18n()->__("You don't have access to read this article");
                    $this->article = null;
                }
                else
                {
                    $this->getUser()->markNotificationsRead('article', $this->article->getID());

                    if (!$request->hasParameter('no_redirect') && $this->article->isRedirect())
                    {
                        if ($redirect_article = $this->article->getRedirectArticleName())
                        {
                            framework\Context::setMessage('publish_redirected_article', $this->article->getName());
                            $this->forward(framework\Context::getRouting()->generate('publish_article', array('article_name' => $redirect_article)));
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
                    catch (\Exception $e)
                    {
                        $this->error = framework\Context::getI18n()->__('There was an error trying to show this revision');
                    }
                }
            }
        }

        public function runArticleAttachments(framework\Request $request)
        {

        }

        public function runArticlePermissions(framework\Request $request)
        {
            if ($this->article instanceof Article)
            {
                $this->forward403unless($this->article->canEdit());
                $namespaces = $this->article->getCombinedNamespaces();
                $namespaces[] = $this->article->getName();
                array_unshift($namespaces, 0);
                $this->namespaces = array_reverse($namespaces);
            }
        }

        public function runArticleHistory(framework\Request $request)
        {
            $this->history_action = $request['history_action'];
            if ($this->article instanceof Article)
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
                            $this->error = framework\Context::getI18n()->__('Please specify a from- and to-revision to compare');
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
                        if (!framework\Context::getModule('publish')->canUserEditArticle($article_name))
                        {
                            framework\Context::setMessage('publish_article_error', framework\Context::getI18n()->__('You do not have permission to edit this article'));
                            $this->forward(framework\Context::getRouting()->generate('publish_article_history', array('article_name' => $article_name)));
                        }
                        $revision = $request['revision'];
                        if ($revision)
                        {
                            $this->article->restoreRevision($revision);
                            $this->forward(framework\Context::getRouting()->generate('publish_article_history', array('article_name' => $article_name)));
                        }
                        else
                        {
                            $this->forward(framework\Context::getRouting()->generate('publish_article_history', array('article_name' => $this->article->getName())));
                        }
                }
            }
        }

        /**
         * Delete an article
         *
         * @param \thebuggenie\core\framework\Request $request
         */
        public function runDeleteArticle(framework\Request $request)
        {
            try
            {
                if (!$this->article instanceof Article)
                {
                    throw new \Exception($this->getI18n()->__('This article does not exist'));
                }
                if (!framework\Context::getModule('publish')->canUserDeleteArticle($this->article->getName()))
                {
                    throw new \Exception($this->getI18n()->__('You do not have permission to delete this article'));
                }
                if (!$request['article_name'])
                {
                    throw new \Exception($this->getI18n()->__('Please specify an article name'));
                }
                else
                {
                    Article::deleteByName($request['article_name']);
                }
            }
            catch (\Exception $e)
            {
                $this->getResponse()->setHttpStatus(400);
                return $this->renderJSON(array('title' => $this->getI18n()->__('An error occured'), 'error' => $e->getMessage()));
            }
            return $this->renderJSON(array('message' => $this->getI18n()->__('The article was deleted')));
        }

        /**
         * Get avilable parent articles for an article
         *
         * @param \thebuggenie\core\framework\Request $request
         */
        public function runGetAvailableParents(framework\Request $request)
        {
            $articles = Articles::getTable()->getManualSidebarArticles(framework\Context::getCurrentProject(), $request['find_article']);

            $parent_articles = array();
            foreach ($articles as $article)
            {
                if ($article->getID() == $this->article->getID())
                    continue;
                $parent_articles[$article->getName()] = $article->getManualName();
            }

            return $this->renderJSON(array('list' => $this->getComponentHTML('publish/getavailableparents', compact('parent_articles'))));
        }

        /**
         * Show an article
         *
         * @param \thebuggenie\core\framework\Request $request
         */
        public function runEditArticle(framework\Request $request)
        {
            if (!$this->article->canEdit())
            {
                framework\Context::setMessage('publish_article_error', framework\Context::getI18n()->__('You do not have permission to edit this article'));
                $this->forward(framework\Context::getRouting()->generate('publish_article', array('article_name' => $this->article_name)));
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
                    $this->article->setParentArticle(Articles::getTable()->getArticleByName($request['parent_article_name']));
                    $this->article->setManualName($request['manual_name']);
                    if ($this->article->getArticleType() == Article::TYPE_MANUAL && !$this->article->getName())
                    {
                        $article_name_prefix = ($this->article->getParentArticle() instanceof Article) ? $this->article->getParentArticle()->getName() . ':' : $request['parent_article_name'];
                        $this->article->setName(str_replace(' ', '', $article_name_prefix . $this->article->getManualName()));
                    }
                    $this->article->setContentSyntax($request['article_content_syntax']);
                    $this->article->setContent($request->getRawParameter('article_content'));

                    if (!$this->article->getName() || trim($this->article->getName()) == '' || !preg_match('/[\w:]+/i', $this->article->getName()))
                        throw new \Exception(framework\Context::getI18n()->__('You need to specify a valid article name'));

                    if ($request['article_type'] == Article::TYPE_MANUAL && (!$this->article->getManualName() || trim($this->article->getManualName()) == '' || !preg_match('/[\w:]+/i', $this->article->getManualName())))
                        throw new \Exception(framework\Context::getI18n()->__('You need to specify a valid article name'));

                    if (!$this->preview && framework\Context::getModule('publish')->getSetting('require_change_reason') == 1 && (!$this->change_reason || trim($this->change_reason) == ''))
                        throw new \Exception(framework\Context::getI18n()->__('You have to provide a reason for the changes'));

                    if ($this->article->getLastUpdatedDate() != $request['last_modified'])
                        throw new \Exception(framework\Context::getI18n()->__('The file has been modified since you last opened it'));

                    if (($article = Article::getByName($request['new_new_article_name'])) && $article instanceof Article && $article->getID() != $request['article_id'])
                        throw new \Exception(framework\Context::getI18n()->__('An article with that name already exists. Please choose a different article name'));

                    if (!$this->preview)
                    {
                        $this->article->doSave(array(), $request['change_reason']);
                        framework\Context::setMessage('publish_article_message', framework\Context::getI18n()->__('The article was saved'));
                        $this->forward(framework\Context::getRouting()->generate('publish_article', array('article_name' => $this->article->getName())));
                    }
                }
                catch (\Exception $e)
                {
                    $this->error = $e->getMessage();
                }
            }
        }

        public function runFindArticles(framework\Request $request)
        {
            $this->articlename = $request['articlename'];

            if ($this->articlename)
            {
                list ($this->resultcount, $this->articles) = Article::findArticlesByContentAndProject($this->articlename, framework\Context::getCurrentProject(), 10);
            }
        }

        /**
         * Toggle favourite article (starring)
         *
         * @param \thebuggenie\core\framework\Request $request
         */
        public function runToggleFavouriteArticle(framework\Request $request)
        {
            if ($article_id = $request['article_id'])
            {
                try
                {
                    $article = Articles::getTable()->selectById($article_id);
                    $user = \thebuggenie\core\entities\User::getB2DBTable()->selectById($request['user_id']);
                }
                catch (\Exception $e)
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
                    framework\Event::createNew('core', 'article_subscribe_user', $article, compact('user'))->trigger();
                }
            }

            return $this->renderText(json_encode(array('starred' => $retval, 'subscriber' => $this->getComponentHTML('publish/articlesubscriber', array('user' => $user, 'article' => $article)))));
        }

    }
