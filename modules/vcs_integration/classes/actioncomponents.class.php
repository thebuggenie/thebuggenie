<?php

	class vcs_integrationActionComponents extends TBGActionComponent
	{
		public function componentSettings()
		{
			$this->allProjects = TBGProject::getAll();
		}
	}