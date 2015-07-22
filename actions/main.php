<?php

	$errorMessage = '';

	if (filter_var($serverUrl, FILTER_VALIDATE_URL) &&
		checkUrl($serverUrl))
	{
		$presearchFlag = 0;
		if (isset($_GET['presearch_by_addr']))
		{
			$presearchFlag = 1;
		}
		else if (isset($_GET['presearch_by_pn']))
		{
			$presearchFlag = 2;
		}

		if (isset($_GET['search']))
		{		
			redirect(url('search'));
		}
		else if ($presearchFlag)
		{
			$params = array();
			$params['presearch_flag'] = $presearchFlag;
	
			redirect(url('presearch', $params));
		}
	}
	else
	{
		$errorMessage = 'Адреса сервера не існує!';
	}

	$smarty->assign('error_msg', $errorMessage);

?>