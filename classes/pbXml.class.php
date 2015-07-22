<?php

class pbXml
{
	private static $schema = 'http://www.w3.org/2001/XMLSchema-instance';
	private static $apiUrl = 'http://debt.privatbank.ua/Transfer';

/**
 * xml2array() will convert the given XML text to an array in the XML structure.
 * Link: http://www.bin-co.com/php/scripts/xml2array/
 * Arguments : $contents - The XML text
 *             $get_attributes - 1 or 0. If this is 1 the function will get the attributes as well as the tag values - this results in a different array structure in the return value.
 *             $priority - Can be 'tag' or 'attribute'. This will change the way the resulting array sturcture. For 'tag', the tags are given more importance.
 * Return: The parsed XML in an array form. Use print_r() to see the resulting array structure.
 * Examples: $array =  xml2array(file_get_contents('feed.xml'));
 *           $array =  xml2array(file_get_contents('feed.xml', 1, 'attribute'));
*/

	static function xml2array($content, $getAttributes = 1, $priority = 'attribute'/*'tag'*/)
	{
		if (!function_exists('xml_parser_create')) return array();

	    //Get the XML parser of PHP - PHP must have this module for the parser to work
		$parser = xml_parser_create('');
		xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8");
		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
		xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
		xml_parse_into_struct($parser, trim($content), $xmlValues);
		xml_parser_free($parser);

		if(!$xmlValues) return;
 
	    //Initializations
		$array = array();
		$parents = array();
		$openedTags = array();
		$arr = array();
		$repeatedTagIndex = array(); //Multiple tags with same name will be turned into an array

		$current = &$array;
 
		foreach($xmlValues as $data)
		{
			unset($attributes, $value);//Remove existing values, or there will be trouble
 
			//This command will extract these variables into the foreach scope
			// tag(string), type(string), level(int), attributes(array).
			extract($data);//We could use the array by itself, but this cooler.
 
			$result = array();
			$attributesData = array();
 
			if (isset($value))
			{
				if ($priority == 'tag') $result = $value;
				else $result['value'] = $value;//Put the value in a assoc array if we are in the 'Attribute' mode
			}
 
        	//Set the attributes too.
			if (isset($attributes) and $getAttributes)
			{
				foreach ($attributes as $attr => $val)
				{
					if ($priority == 'tag') $attributesData[$attr] = $val;
					else $result['attr'][$attr] = $val;
				}
			}
 
        	//See tag status and do the needed.
			if ($type == 'open')
			{
				$parent[$level - 1] = &$current;

				if (!is_array($current) or !in_array($tag, array_keys($current)))
				{
					$current[$tag] = $result;
					if ($attributesData) $current[$tag . '_attr'] = $attributesData;
					$repeatedTagIndex[$tag . '_' . $level] = 1;
 
					$current = &$current[$tag]; 
				}
				else
				{ 
					if (isset($current[$tag][0]))
					{
						$current[$tag][$repeatedTagIndex[$tag . '_' . $level]] = $result;
						$repeatedTagIndex[$tag . '_' . $level]++;
					}
					else
					{//This section will make the value an array if multiple tags with the same name appear together
						$current[$tag] = array($current[$tag], $result);//This will combine the existing item and the new item together to make an array
						$repeatedTagIndex[$tag . '_' . $level] = 2;
 
						if (isset($current[$tag . '_attr']))
						{ //The attribute of the last(0th) tag must be moved as well
							$current[$tag]['0_attr'] = $current[$tag . '_attr'];
							unset($current[$tag . '_attr']);
						} 
					}

					$lastItemIndex = $repeatedTagIndex[$tag . '_' . $level] - 1;
					$current = &$current[$tag][$lastItemIndex];
				} 
			}
			else if ($type == 'complete')
			{ //Tags that ends in 1 line '&lt;tag />'
            //See if the key is already taken.
				if (!isset($current[$tag]))
				{ //New Key
					$current[$tag] = $result;
					$repeatedTagIndex[$tag . '_' . $level] = 1;
					if ($priority == 'tag' and $attributesData) $current[$tag . '_attr'] = $attributesData;
 				}
				else
				{ //If taken, put all things inside a list(array)
					if (isset($current[$tag][0]) and is_array($current[$tag]))
					{//If it is already an array... 
						// ...push the new element into that array.
						$current[$tag][$repeatedTagIndex[$tag . '_' . $level]] = $result;
 
						if ($priority == 'tag' and $getAttributes and $attributesData)
						{
							$current[$tag][$repeatedTagIndex[$tag . '_' . $level] . '_attr'] = $attributesData;
						}

						$repeatedTagIndex[$tag . '_' . $level]++; 
					}
					else
					{ //If it is not an array...
						$current[$tag] = array($current[$tag], $result); //...Make it an array using using the existing value and the new value
						$repeatedTagIndex[$tag . '_' . $level] = 1;

						if ($priority == 'tag' and $getAttributes)
						{
							if (isset($current[$tag . '_attr']))
							{ //The attribute of the last(0th) tag must be moved as well 
								$current[$tag]['0_attr'] = $current[$tag . '_attr'];
								unset($current[$tag . '_attr']);
							}
 
							if ($attributesData)
							{
								$current[$tag][$repeatedTagIndex[$tag . '_' . $level] . '_attr'] = $attributesData;
							}
						}

						$repeatedTagIndex[$tag . '_' . $level]++; //0 and 1 index is already taken
					}
				} 
			}
			else if($type == 'close')
			{ //End of tag '&lt;/tag>'
				$current = &$parent[$level-1];
			}
		}

		return self::htmlspecialcharsRecursive($array);
	}

	static function htmlspecialcharsRecursive($data)
	{
		if (is_array($data))
		{
	    	$result = array();
			foreach ($data as $key => $value)
			{
				$result[$key] = self::htmlspecialcharsRecursive($value);
			}

			return $result;
		}

		if (is_string($data))
		{
			return htmlspecialchars($data);
		}

  		if (is_scalar($data) or is_null($data))
		{
			return $data;
		}

		return $data;
	}

	static function xml2html($xml)
	{
		$xml = preg_replace('/(>)(<)(\/*)/', "$1\n$2$3", $xml);
		$token      = strtok($xml, "\n");
		$result     = '';
		$pad        = 0; 
		$matches    = array();

		while ($token !== false)
		{
			if (preg_match('/.+<\/\w[^>]*>$/', $token, $matches)) 
				$indent = 0;                                           
			elseif (preg_match('/^<\/\w/', $token, $matches))
			{
				$pad--;
				$indent = 0;
			}
			elseif (preg_match('/^<\w[^>]*[^\/]>.*$/', $token, $matches))
			{
				$indent = 1;
			}
			else
			{
				$indent = 0;
			}

			$line    = str_pad($token, strlen($token) + $pad, ' ', STR_PAD_LEFT);
			$result .= $line . "\n";
			$token   = strtok("\n");
			$pad    += $indent;
    	} 

		$result  = highlight_string($result, true);
		$tresult = str_replace(array('<code>', '</code>'), '', $result);
		$tresult = str_replace(array('<span ', '</span>'), '', $tresult);
		$tresult = preg_replace('#style="color:(.*?)">#', '', $tresult);

		if (trim($tresult))
		{
			$result = str_replace(array('<span ', '</span>'), '', $result);
			$result = preg_replace('#style="color:(.*?)">#', '', $result);
		}
		else
		{
			$result = '';
		}

		return $result;
	}

	private static function head()
	{
		return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
	}

	static function presearch($row)
	{
		$xml  = self::head();
		$xml .= '<Transfer xmlns="' . self::$apiUrl . '" interface="Debt" action="Presearch">';
		$xml .= '<Data xmlns:xsi="' . self::$schema . '" xsi:type="Payer">';

		if (isset($row['street']) && isset($row['house']))
		{
			$branchStr = '';
			if (isset($row['branch']) && $row['branch'])
			{  
				$branchStr = '/' . $row['branch'];
			}

			$xml .= '<Unit name="street" value="' . $row['street'] . '"/>';
			$xml .= '<Unit name="house" value="' . $row['house'] . $branchStr . '"/>';

			if (isset($row['flat']))
			{
				$xml .= '<Unit name="flat" value="' . $row['flat'] . '"/>';
			}
		}
		else if (isset($row['pn']))
		{
			$xml .= '<Unit name="ls" value="' . $row['pn'] . '"/>';
		}

		$xml .= '</Data>';
		$xml .= '</Transfer>';

		return $xml;
	}

	static function search($num, $afterPresearch = false)
	{
		$xml  = self::head();
		$xml .= '<Transfer xmlns="' . self::$apiUrl . '" interface="Debt" action="Search">';

		if (!$afterPresearch)
		{
			$xml .= '<Data xmlns:xsi="' . self::$schema . '" xsi:type="Payer">';
			$xml .= '<Unit name="bill_identifier" value="' . $num . '"/>';
			$xml .= '</Data>';
		}
		else
		{
			$xml .= '<Data xmlns:xsi="' . self::$schema . '" xsi:type="Payer" presearchId="' . $num . '"/>';
		}

		$xml .= '</Transfer>';

		return $xml;
	}

	static function check($get, $payer, $company, $service)
	{
		$dt = date('c');

		$xml  = self::head();
		$xml .= '<Transfer xmlns="' . self::$apiUrl . '" interface="Debt" action="Check">';
		$xml .= '<Data xmlns:xsi="' . self::$schema . '" xsi:type="Payment" number="0.0.345032104.1" id="740150719">';
		$xml .= '<CompanyInfo companyId="' . $company['id'] . '">';
		$xml .= '<CompanyCode>' . $company['id'] . '</CompanyCode>';
		$xml .= '<CompanyName>' . $company['name'] . '</CompanyName>';
//		$xml .= '<DopData>';
//		$xml .= '<Dop name="доп информация" value="значение"/>';
//		$xml .= '</DopData>';
		$xml .= '</CompanyInfo>';
		$xml .= '<PayerInfo billIdentifier="' . $payer['num'] . '" ls="' . $payer['num'] . '">';
		$xml .= '<Fio>' . $payer['name'] . '</Fio>';
		$xml .= '<Phone>'  . $payer['phone'] . '</Phone>';
		$xml .= '<Address>' . $payer['address'] . '</Address>';
		$xml .= '</PayerInfo>';
		$xml .= '<TotalSum>' . $get['sum'] . '</TotalSum>';
		$xml .= '<CreateTime>' . $dt . '</CreateTime>';
		$xml .= '<ServiceGroup>';
		$xml .= '<Service sum="' . $service['service_price'] . '" serviceCode="' . $service['service_code'] . '">';
		$xml .= '<CompanyInfo>';
		$xml .= '<CompanyCode>' . $company['id'] . '</CompanyCode>';
		$xml .= '<CompanyName>' . $company['name'] . '</CompanyName>';
		$xml .= '</CompanyInfo>';
		$xml .= '<ServiceName>' . $service['service_name'] . '</ServiceName>';
		$xml .= '<Destination>Оплата за послугу "' . $service['service_name'] . '"</Destination>';
//		$xml .= '<MeterData>';
//		$xml .= '<Meter previosValue="213" currentValue="214" tarif="0.01" delta="1" name="Холодная вода кухня"/>';
//		$xml .= '</MeterData>';
//		$xml .= '<DopData>';
//		$xml .= '<Dop name="city_code" value="3"/>';
//		$xml .= '</DopData>';
		$xml .= '</Service>';
		$xml .= '</ServiceGroup>';
		$xml .= '</Data>';
		$xml .= '</Transfer>';

		return $xml;
	}

	static function pay($get, $payer, $company, $service)
	{
		$dtPay 	   = date('c');
		$dtConfirm = date('c', strtotime('+10 hours'));

		$xml  = self::head();
		$xml .= '<Transfer xmlns="' . self::$apiUrl . '" interface="Debt" action="Pay">';
		$xml .= '<Data xmlns:xsi="' . self::$schema . '" xsi:type="Payment" id="314423214" number="6359.143.1">';
		$xml .= '<CompanyInfo inn="' . $company['name'] . '" companyId="' . $company['id'] . '">';
		$xml .= '<CompanyCode>' . $company['id'] . '</CompanyCode>';
		$xml .= '<UnitCode>2221</UnitCode>';
		$xml .= '<CompanyName>' . $company['name'] . '</CompanyName>';
//		$xml .= '<DopData>';
//		$xml .= '<Dop name="доп информация" value="значение"/>';
//		$xml .= '</DopData>';
		$xml .= '<CheckReference>' . $get['reference'] . '</CheckReference>';
		$xml .= '</CompanyInfo>';
		$xml .= '<PayerInfo billIdentifier="' . $payer['num'] . '" ls="' . $payer['num'] . '">';
		$xml .= '<Fio>' . $payer['name'] . '</Fio>';
		$xml .= '<Phone>'  . $payer['phone'] . '</Phone>';
		$xml .= '<Address>' . $payer['address'] . '</Address>';
		$xml .= '</PayerInfo>';
		$xml .= '<TotalSum>' . $get['sum'] . '</TotalSum>';
		$xml .= '<CreateTime>' . $dtPay . '</CreateTime>';
		$xml .= '<ConfirmTime>' . $dtConfirm . '</ConfirmTime>';
		$xml .= '<NumberPack>143</NumberPack>';
		$xml .= '<SubNumberPack>1</SubNumberPack>';
		$xml .= '<ServiceGroup>';
		$xml .= '<Service sum="' . $service['service_price'] . '" serviceCode="' . $service['service_code'] . '">';
		$xml .= '<PayerInfo billIdentifier="' . $payer['num'] . '" ls="' . $payer['num'] . '">';
		$xml .= '<Fio>' . $payer['name'] . '</Fio>';
		$xml .= '<Phone>'  . $payer['phone'] . '</Phone>';
		$xml .= '<Address>' . $payer['address'] . '</Address>';
		$xml .= '</PayerInfo>';
		$xml .= '<CompanyInfo>';
		$xml .= '<CheckReference>' . $get['reference'] . '</CheckReference>';
		$xml .= '<CompanyCode>' . $company['id'] . '</CompanyCode>';
		$xml .= '<UnitCode>2221</UnitCode>';
		$xml .= '<CompanyName>' . $company['name'] . '</CompanyName>';
//		$xml .= '<DopData>';
//		$xml .= '<Dop name="city_code" value="3"/>';
//		$xml .= '</DopData>';
		$xml .= '</CompanyInfo>';
		$xml .= '<idinvoice>12345678</idinvoice>';
		$xml .= '<ServiceName>' . $service['service_name'] . '</ServiceName>';
		$xml .= '<Destination>Оплата за послугу "' . $service['service_name'] . '"</Destination>';
//		$xml .= '<MeterData>';
//		$xml .= '<Meter previosValue="213" currentValue="214" tarif="0.01" delta="1" name="Холодная вода кухня"/>';
//		$xml .= '...';
//		$xml .= '</MeterData>';
//		$xml .= '<DopData>';
//		$xml .= '<Dop name="city_code" value="3"/>';
//		$xml .= '</DopData>';
		$xml .= '<Comissions>';
		$xml .= '<Commision type="3" summ="0.99"/>';
		$xml .= '<Commision type="1" summ="0.10"/>';
		$xml .= '</Comissions>';
		$xml .= '</Service>';
		$xml .= '</ServiceGroup>';
		$xml .= '</Data>';
		$xml .= '</Transfer>';

		return $xml;
	}

	static function cancel($get, $payer, $company, $service)
	{
		$dtPay 	   = date('c');
		$dtConfirm = date('c', strtotime('+10 hours'));

		$xml  = self::head();
		$xml .= '<Transfer xmlns="' . self::$apiUrl . '" interface="Debt" action="Cancel">';
		$xml .= '<Data xmlns:xsi="' . self::$schema . '" xsi:type="Payment" id="314423214">';
		$xml .= '<CompanyInfo>';
		$xml .= '<CompanyCode>' . $company['id'] . '</CompanyCode>';
		$xml .= '<CompanyName>' . $company['name'] . '</CompanyName>';
//		$xml .= '<DopData>';
//		$xml .= '<Dop name="доп информация" value="значение"/>';
//		$xml .= '</DopData>';
		$xml .= '</CompanyInfo>';
		$xml .= '<PayerInfo billIdentifier="' . $payer['num'] . '" ls="' . $payer['num'] . '">';
		$xml .= '<Fio>' . $payer['name'] . '</Fio>';
		$xml .= '<Phone>'  . $payer['phone'] . '</Phone>';
		$xml .= '<Address>' . $payer['address'] . '</Address>';
		$xml .= '</PayerInfo>';
		$xml .= '<TotalSum>' . $get['sum'] . '</TotalSum>';
		$xml .= '<CreateTime>' . $dtPay . '</CreateTime>';
		$xml .= '<ConfirmTime>' . $dtConfirm . '</ConfirmTime>';
		$xml .= '<ServiceGroup>';
		$xml .= '<Service sum="' . $service['service_price'] . '" id="324124213">';
		$xml .= '<PayerInfo billIdentifier="' . $payer['num'] . '" ls="' . $payer['num'] . '">';
		$xml .= '<Fio>' . $payer['name'] . '</Fio>';
		$xml .= '<Phone>'  . $payer['phone'] . '</Phone>';
		$xml .= '<Address>' . $payer['address'] . '</Address>';
		$xml .= '</PayerInfo>';
		$xml .= '<CompanyInfo>';
		$xml .= '<CheckReference>' . $get['reference'] . '</CheckReference>';
		$xml .= '<CompanyCode>' . $company['id'] . '</CompanyCode>';
		$xml .= '<CompanyName>' . $company['name'] . '</CompanyName>';
//		$xml .= '<DopData>';
//		$xml .= '<Dop name="city_code" value="3"/>';
//		$xml .= '</DopData>';
		$xml .= '</CompanyInfo>';
		$xml .= '<idinvoice>123456789</idinvoice>';
		$xml .= '<ServiceName>' . $service['service_name'] . '</ServiceName>';
//		$xml .= '<DopData>';
//		$xml .= '<Dop name="city_code" value="3"/>';
//		$xml .= '</DopData>';
		$xml .= '</Service>';
		$xml .= '</ServiceGroup>';
		$xml .= '</Data>';
		$xml .= '</Transfer>';

		return $xml;
	}

	static function isError($data)
	{
		if (isset($data['Transfer']['Data']['attr']) &&
			$data['Transfer']['Data']['attr']['xsi:type'] == 'ErrorInfo')
		{
			return true;
		}

		return false;
	}
}

?>