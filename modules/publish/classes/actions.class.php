<?php

	class publishActions extends BUGSaction
	{

		public function preExecute($request, $action)
		{
			$this->getResponse()->setProjectMenuStripHidden();
		}

		/**
		 * Articles frontpage
		 *
		 * @param BUGSrequest $request
		 */
		public function runIndex($request)
		{
		}

		/**
		 * Show an article
		 *
		 * @param BUGSrequest $request
		 */
		public function runShowArticle($request)
		{
			$this->article = null;
			$this->article_name = $request->getParameter('article_name');
			if ($row = B2DB::getTable('B2tArticles')->getArticleByName($this->article_name))
			{
				$this->article = PublishFactory::articleLab($row->get(B2tArticles::ID), $row);
			}
		}

	}