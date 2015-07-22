<?php

	$pay  = array(
		'sum' => '',
		'num' => '',
		'service_code' => ''
	);

	$pay = updateByRequest($pay);

	$errorMessage = '';
	$payMessage   = '';
	$payStatus = 0;

	$reference = isset($_GET['reference']) ? $_GET['reference'] : '';

	$xml    = pbXml::search($pay['num']);
	$answer = httpRequester::load($serverUrl, $xml);

	$response = httpRequester::parseResponse($answer);
	$data 	  = pbXml::xml2array($response['content']);

	$payer = array();
	$payer['num']  	  = $data['Transfer']['Data']['PayerInfo']
									['attr']['billIdentifier'];
	$payer['name'] 	  = $data['Transfer']['Data']['PayerInfo']
											  ['Fio']['value'];
	$payer['address'] = $data['Transfer']['Data']['PayerInfo']
										  ['Address']['value'];

	if (isset($data['Transfer']['Data']['PayerInfo']
							      ['Phone']['value']))
	{
		$payer['phone'] = $data['Transfer']['Data']['PayerInfo']
										      ['Phone']['value'];
	}
	else
	{
		$payer['phone'] = '';
	}

	$debtServices = $data['Transfer']['Data']['ServiceGroup']['DebtService'];
	$debtService  = array();

	$rows = array();
	if (array_key_exists('0', $debtServices))
	{
		foreach ($debtServices as $debtData)
		{
			if ($debtData['attr']['serviceCode'] == $_GET['service_code'])
			{
				$debtService = $debtData;
				break;
			}
		}

		$companyInfo = $debtService['CompanyInfo'];
	}
	else
	{
		$debtService = $debtServices;
		$companyInfo = $debtServices['CompanyInfo'];
	}

	$company = array();
	$company['name'] 	= $companyInfo['CompanyName']['value'];
	$company['id'] 		= $companyInfo['CompanyCode']['value'];
	$company['mfo']  	= isset($companyInfo['attr']['mfo']) ? 
								$companyInfo['attr']['mfo'] : '';
	$company['okpo']  	= isset($companyInfo['attr']['okpo']) ?
								$companyInfo['attr']['okpo'] : '';
	$company['account'] = isset($companyInfo['attr']['account']) ?
								$companyInfo['attr']['account'] : '';

	$debtInfo = $debtService['DebtInfo'];

	$debt = array();
	$debt['amount_to_pay'] = money($debtInfo['attr']['amountToPay']);
	$debt['debt'] 		   = money($debtInfo['attr']['debt']);
	$debt['service_name']  = $debtService['ServiceName']['value'];
	$debt['service_price'] = isset($debtService['attr']['metersGlobalTarif']) ? 
		money($debtService['attr']['metersGlobalTarif']) : 0.0;
	$debt['service_code']  = isset($debtService['attr']['serviceCode']) ?
		$debtService['attr']['serviceCode'] : '';

	if (isset($_GET['sum']) && !isset($_GET['cancel']) && !isset($_GET['back']))
	{
		$xml    = pbXml::check($_GET, $payer, $company, $debt);
		$answer = httpRequester::load($serverUrl, $xml);

		$_SESSION['xml_query'][0] = $xml;

		$response = httpRequester::parseResponse($answer);
		$data 	  = pbXml::xml2array($response['content']);

		$_SESSION['xml_answer'][0] = $response['content'];

		$reference = '';
		if (isset($data['Transfer']['Data']['attr']['reference']))
		{			
			$reference = $data['Transfer']['Data']['attr']['reference'];
		}

		$_GET['reference'] = $reference;

		if (pbXml::isError($data))
		{
			$errorMessage = $data['Transfer']['Data']['Message']['value'];
		}
		else if (isset($data['Transfer']))
		{
			$xml    = pbXml::pay($_GET, $payer, $company, $debt);
			$answer = httpRequester::load($serverUrl, $xml);

			$_SESSION['xml_query'][1] = $xml;
		
			$response = httpRequester::parseResponse($answer);
			$data 	  = pbXml::xml2array($response['content']);

			$_SESSION['xml_answer'][1] = $response['content'];

			if (pbXml::isError($data))
			{
				$errorMessage = $data['Transfer']['Data']['Message']['value'];
			}
			else
			{
				$payMessage = 'Платіж прийнято';
				$payStatus = 1;
			}
		}
	}
	else if (isset($_GET['cancel']))
	{
		$xml    = pbXml::cancel($_GET, $payer, $company, $debt);
		$answer = httpRequester::load($serverUrl, $xml);

		$_SESSION['xml_query'][0] = $xml;

		$response = httpRequester::parseResponse($answer);
		$data 	  = pbXml::xml2array($response['content']);

		$_SESSION['xml_answer'][0] = $response['content'];

		$payMessage = 'Платіж сакасовано';
		$payStatus = 2;

		unset($_SESSION['xml_query'][1]);
		unset($_SESSION['xml_answer'][1]);
	}
	else if (isset($_GET['back']))
	{
		session_unset();

		redirect(url('main'));
	}

	$smarty->assign('pay', $pay);
	$smarty->assign('error_msg', $errorMessage);
	$smarty->assign('reference', $reference);
	$smarty->assign('pay_msg', $payMessage);
	$smarty->assign('pay_status', $payStatus);

?>