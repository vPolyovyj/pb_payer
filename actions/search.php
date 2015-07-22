<?php

	$data = array();
	$errorMessage = '';

	$search = array('num' => '');

	if (isset($_GET['pn']))
	{
		$presearchId = $_GET['pn'];

		$xml    = pbXml::search($presearchId, true);
		$answer = httpRequester::load($serverUrl, $xml);

		$_SESSION['xml_query'][0] = $xml;

		$response = httpRequester::parseResponse($answer);

		$smarty->assign('presearch_id', $presearchId);
		$_SESSION['xml_answer'][0] = $response['content'];
	}
	else
	{	
		$search = updateByRequest($search);

		if (isset($_GET['search']))
		{
			$xml    = pbXml::search($search['num']);
			$answer = httpRequester::load($serverUrl, $xml);

			$_SESSION['xml_query'][0] = $xml;

			$response = httpRequester::parseResponse($answer);
			$_SESSION['xml_answer'][0] = $response['content'];
		}
		else if (isset($_GET['back']))
		{
			session_unset();

			redirect(url('main'));
		}
	}

	if (isset($_GET['pay']))
	{
		$params = array();
		$params['num'] 			= $_GET['num'];
		$params['service_code'] = $_GET['service_code'];

		if (!isset($_GET['sum']) ||
			!is_numeric($_GET['sum']) ||
			$_GET['sum'] < 0)
		{
			$errorMessage = 'Введіть значення грошової суми';
		}
		else
		{
			$params['sum'] = $_GET['sum'];
			redirect(url('pay', $params));
		}
	}

	if (isset($response['content']))
	{
		$data = pbXml::xml2array($response['content']);

		if (pbXml::isError($data))
		{
			$errorMessage = $data['Transfer']['Data']['Message']['value'];
		}
		else
		{
			$rows = array();
			if (array_key_exists('0', $data['Transfer']['Data']
								['ServiceGroup']['DebtService']))
			{
				$rows = $data['Transfer']['Data']['ServiceGroup']
												  ['DebtService'];
			}
			else
			{
				$rows[] = $data['Transfer']['Data']['ServiceGroup']
													['DebtService'];
			}

			$payerInfo = array();
			$payerInfo['num']  	  = $data['Transfer']['Data']['PayerInfo']
												['attr']['billIdentifier'];
			$payerInfo['name'] 	  = $data['Transfer']['Data']['PayerInfo']
														  ['Fio']['value'];
			$payerInfo['address'] = $data['Transfer']['Data']['PayerInfo']
													  ['Address']['value'];

			$payerInfo['phone'] = '';
			if (isset($data['Transfer']['Data']['PayerInfo']
										  ['Phone']['value']))
			{
				$payerInfo['phone'] = $data['Transfer']['Data']['PayerInfo']
														  ['Phone']['value'];
			}

			$i = 0;
			$services = array();
			$debts = array();
			foreach ($rows as $key => $row)
			{
				$debt = array();

				$debt['amount_to_pay'] = money($row['DebtInfo']['attr']['amountToPay']);
				$debt['debt'] 		   = money($row['DebtInfo']['attr']['debt']);
				$debt['service_name']  = isset($row['ServiceName']['value']) ?
					$row['ServiceName']['value'] : '';
				$debt['service_code']  = isset($row['attr']['serviceCode']) ?
					$row['attr']['serviceCode'] : '';
				$debt['service_price'] = isset($row['attr']['metersGlobalTarif']) ? 
					money($row['attr']['metersGlobalTarif']) : '';
				$debt['destination']   = isset($row['Destination']['value']) ?
					$row['Destination']['value'] : '';
				$debt['num'] 		   = $row['PayerInfo']['attr']['ls'];
				$debt['year'] 		   = $row['DebtInfo']['Year']['value'];
				$debt['month'] 		   = $row['DebtInfo']['Month']['value'];
				$debt['charge'] 	   = money($row['DebtInfo']['Charge']['value']);
				$debt['balance'] 	   = money($row['DebtInfo']['Balance']['value']);
				$debt['recalc'] 	   = money($row['DebtInfo']['Recalc']['value']);
				$debt['subsidies'] 	   = money($row['DebtInfo']['Subsidies']['value']);
				$debt['remission'] 	   = money($row['DebtInfo']['Remission']['value']);
				$debt['lastPaying']    = money($row['DebtInfo']['LastPaying']['value']);
				$debt['company_name']  = $row['CompanyInfo']['CompanyName']['value'];
				$debt['company_mfo'] = isset($row['CompanyInfo']['attr']['mfo']) ?
					$row['CompanyInfo']['attr']['mfo'] : '';
				$debt['company_okpo'] = isset($row['CompanyInfo']['attr']['okpo']) ?
					$row['CompanyInfo']['attr']['okpo'] : '';
				$debt['company_accnt'] =  isset($row['CompanyInfo']['attr']['account']) ?
					$row['CompanyInfo']['attr']['account'] : '';

				$debts[] = $debt;

				$services[$debt['service_code']] = $debt;
			}

			$_SESSION['services'] = $services;

			$smarty->assign('debts', $debts);
			$smarty->assign('payer_info', $payerInfo);
		}
	}

	$gs = isset($_GET['search']) ? $_GET['search'] : '';

	$smarty->assign('error_msg', $errorMessage);
	$smarty->assign('search', $search);
	$smarty->assign('get_search', $gs);

?>