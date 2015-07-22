<?php

	function url($page, $params = array())
	{
		global $baseUrl;

		$params['page'] = $page;
		$urlParams = $params ? http_build_query($params) : '';

		return $baseUrl . '?' . $urlParams;
	}
	             
	function redirect($url)
	{
		header('Location: ' . $url);
	}

	function updateByRequest($row)
	{
		foreach ($row as $key => $value)
		{
			if (isset($_REQUEST[$key]))
			{	
				$row[$key] = $_REQUEST[$key];
			}
		}

		return $row;
	}

	function money($s)
	{
		return number_format((double)$s, 2, '.', ' ');
	}

	function checkUrl($url)
	{
		$parse = parse_url($url);
		$host  = $parse['host'];

		$ch = curl_init();

		if ($parse['scheme'] == 'https')
		{
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		}

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		$data 	 = curl_exec($ch);
		$headers = curl_getinfo($ch);

		curl_close($ch);

		return $headers['http_code'] == '200' ? true : false;
	}

?>