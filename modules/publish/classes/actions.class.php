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
			if ($request->hasParameter('article_name') && count(explode(':', $request->getParameter('article_name'))) > 1)
			{
				$article_name = explode(':', $request->getParameter('article_name'));
				if (($project = BUGSproject::getByKey($article_name[0])) instanceof BUGSproject)
				{
					BUGScontext::setCurrentProject($project);
					$this->getResponse()->setProjectMenuStripHidden(false);
				}
			}
		}

		/**
		 * Show an article
		 *
		 * @param BUGSrequest $request
		 */
		public function runShowArticle($request)
		{
			$this->article = null;
			$this->is_project_article = false;
			$this->article_name = $request->getParameter('article_name');
			if ($row = B2DB::getTable('B2tArticles')->getArticleByName($this->article_name))
			{
				$this->article = PublishFactory::articleLab($row->get(B2tArticles::ID), $row);
			}
		}

	}