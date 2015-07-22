<?php

	$params = array();

	$presearch = array(
		'street' => '',
		'house'  => '',
		'branch' => '',
		'flat'   => '',
		'pn' 	 => ''
	);

	$presearch = updateByRequest($presearch);

	$presearchFlag = 0;
	if (isset($_GET['presearch_flag']))
	{
		$presearchFlag = $_GET['presearch_flag'];
	}

	$errorMessage = '';

	if (isset($_GET['presearch']))
	{
		$xml    = pbXml::presearch($_GET);
		$answer = httpRequester::load($serverUrl, $xml);

		$_SESSION['xml_query'][0] = $xml;

		$response = httpRequester::parseResponse($answer);
		$data 	  = pbXml::xml2array($response['content']);

		$_SESSION['xml_answer'][0] = $response['content'];

		if (pbXml::isError($data))
		{
			$errorMessage = $data['Transfer']['Data']['Message']['value'];
		}
		else if (isset($data['Transfer']))
		{
			$rows = $data['Transfer']['Data']['Columns']['Column'];

			$sz = sizeof($rows[0]['Element']);

			$i = 0;
			$payers = array();
			foreach ($rows[0]['Element'] as $name)
			{
				$payerName = '';
				if ($sz == 1)
				{
					$payerName = $name;
					$payerNum  = $rows[1]['Element']['value'];
				}
				else
				{
					$payerName = $name['value'];
					$payerNum  = $rows[1]['Element'][$i]['value'];
				}

				$payers[] = array('name' => $payerName, 'num'  => $payerNum);

				$i++;
			}

			$smarty->assign('payers', $payers);
		}
	}
	if (isset($_GET['back']))
	{
		session_unset();

		redirect(url('main'));
	}
	
	$smarty->assign('presearch_flag', $presearchFlag);
	$smarty->assign('presearch', $presearch);
	$smarty->assign('error_msg', $errorMessage);

?>