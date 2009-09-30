<?php

	class publishActionComponents extends BUGSactioncomponent
	{
		
		public function componentLatestNewsBox()
		{
			$publish_module = BUGScontext::getModule('publish');
			$publish_module->log('retrieving news items');
			
			$this->news = $publish_module->getNews();
			$publish_module->log('done (retrieving news items)');
		}
		
	}