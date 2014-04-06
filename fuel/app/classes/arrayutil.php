<?php

class ArrayUtil {

	static public function combination($arr, &$rarr, $vtemp = array())
	{
		foreach ($arr as $key => $value)
		{
			$vtemp2 = $vtemp;
			$vtemp2[] = $value;

			$atemp = $arr;
			unset($atemp[$key]);
			
			if (count($atemp) > 0)
			{
				ArrayUtil::combination($atemp, $rarr, $vtemp2);
			}
			else 
			{
				$t = array();
				foreach ($vtemp2 as $val)
				{
					$t[] = $val;
				}
				$rarr[] = $t; 
			}
		}
	}

}