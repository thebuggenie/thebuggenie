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

			if ($request->hasParameter('article_name') && count(explode(':', $request->getParameter('article_name'))) > 1)
			{
				$article_name = explode(':', $request->getParameter('article_name'));
				if (($project = BUGSproject::getByKey($article_name[0])) instanceof BUGSproject)
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
		}

		/**
		 * Show an article
		 *
		 * @param BUGSrequest $request
		 */
		public function runEditArticle($request)
		{
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