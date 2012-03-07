<?php

	/**
	 * @Table(name="TBGListTypesTable")
	 */
	class TBGCategory extends TBGDatatype 
	{

		const ITEMTYPE = TBGDatatype::CATEGORY;

		protected $_itemtype = TBGDatatype::CATEGORY;

		public static function loadFixtures(TBGScope $scope)
		{
			$categories = array('General', 'Security', 'User interface');
			
			foreach ($categories as $name)
			{
				$category = new TBGCategory();
				$category->setName($name);
				$category->setScope($scope);
				$category->save();
			}
		}

		public function hasAccess()
		{
			return $this->canUserSet(TBGContext::getUser());
		}

	}
