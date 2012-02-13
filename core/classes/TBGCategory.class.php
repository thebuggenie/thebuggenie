<?php

	/**
	 * @Table(name="TBGListTypesTable")
	 */
	class TBGCategory extends TBGDatatype 
	{

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
		
		/**
		 * Returns all categories available
		 * 
		 * @return array 
		 */		
		public static function getAll()
		{
			return TBGListTypesTable::getTable()->getAllByItemType(self::CATEGORY);
		}

		public function hasAccess()
		{
			return $this->canUserSet(TBGContext::getUser());
		}

	}
