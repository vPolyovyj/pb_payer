<?php

class httpRequester
{
	static public function parseResponse($response)
	{
		$headers = array();
		$content = '';
		$str = strtok($response, "\n");
		$h = null;

		while ($str !== false)
		{
			if ($h and trim($str) === '')
			{
				$h = false;
				continue;
			}

			if ($h !== false and false !== strpos($str, ':'))
			{
				$h = true;
				list($headername, $headervalue) = explode(':', trim($str), 2);
				
				$headername = strtolower($headername);
				$headervalue = ltrim($headervalue);

				if (isset($headers[$headername])) 
				{
					$headers[$headername] .= ',' . $headervalue;
				}
				else 
				{
					$headers[$headername] = $headervalue;
				}
			} 

			if ($h === false)
			{
				$content .= $str . "\n";
			}

			$str = strtok("\n");
		}

		return array('headers' => $headers, 'content' => trim($content));
	}

	static public function load($url, $xml, $ref = '')
	{
		$parse = parse_url($url);

		$host = $parse['host'];

		$ch = curl_init();

		if ($parse['scheme'] == 'https')
		{
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		}
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		//curl_setopt($ch, CURLOPT_AUTOREFERER,		1);

		$header[0] = 'Host: ' . $host;
		$header[1] = 'User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.11) Gecko/20071127 Firefox/2.0.0.11';
		$header[2] = 'Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5';
		$header[3] = 'Accept-Language: en-us,en;q=0.5';
		$header[4] = 'Accept-Encoding: none';//gzip';//,deflate';
		$header[5] = 'Accept-Charset: ISO-8859-1;q=0.7,*;q=0.7';
		$header[6] = 'Keep-Alive: 300';
		$header[7] = 'Connection: keep-alive';
		$header[8] = 'Cache-Control: max-age=0';

		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
   		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_NOPROGRESS, 1);
		if ($ref != '')
		{
			curl_setopt($ch, CURLOPT_REFERER, $ref);
		}

		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);

		$content = curl_exec($ch);

		return $content;
	}
}

?>
