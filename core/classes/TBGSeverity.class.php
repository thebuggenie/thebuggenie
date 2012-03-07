<?php

	/**
	 * @Table(name="TBGListTypesTable")
	 */
	class TBGSeverity extends TBGDatatype 
	{

		const ITEMTYPE = TBGDatatype::SEVERITY;

		protected static $_items = null;
		
		protected $_itemtype = TBGDatatype::SEVERITY;

		public static function loadFixtures(TBGScope $scope)
		{
			$severities = array();
			$severities['Low'] = '';
			$severities['Normal'] = '';
			$severities['Critical'] = '';

			foreach ($severities as $name => $itemdata)
			{
				$severity = new TBGSeverity();
				$severity->setName($name);
				$severity->setItemdata($itemdata);
				$severity->setScope($scope);
				$severity->save();
			}
		}
		
	}
