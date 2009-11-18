<?php

	class publishActions extends BUGSaction
	{

		/**
		 * Articles frontpage
		 *
		 * @param BUGSrequest $request
		 */
		public function runIndex($request)
		{
			$this->getResponse()->setProjectMenuStripHidden();
		}

	}