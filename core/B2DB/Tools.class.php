<?php

	namespace b2db;
	
	class Tools
	{

		public static function array_diff_recursive($aArray1, $aArray2)
		{
			$aReturn = array();

			foreach ($aArray1 as $mKey => $mValue)
			{
				if (array_key_exists($mKey, $aArray2)) 
				{
					if (is_array($mValue))
					{
						$aRecursiveDiff = self::array_diff_recursive($mValue, $aArray2[$mKey]);
						if (count($aRecursiveDiff)) { $aReturn[$mKey] = $aRecursiveDiff; }
					} 
					else
					{
						if ($mValue != $aArray2[$mKey])
						{
							$aReturn[$mKey] = $mValue;
						}
					}
				}
				else
				{
					$aReturn[$mKey] = $mValue;
				}
			}
			return $aReturn;
		}		

	}