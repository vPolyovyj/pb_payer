<?php

	require_once 'config.php';

	session_start();

	$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 'main';

	$pageName = $page . '.php';

	$smarty->assign('page', $page);
	$smarty->assign('page_name', ucfirst($page));

	$path = 'actions/' . $pageName;

	if (isset($_GET['server_url']))
	{
		$_SESSION['server_url'] = $_GET['server_url'];
	}

	$serverUrl = isset($_SESSION['server_url']) ?
		$_SESSION['server_url'] :
		/*'https://test.tcworld.net/pay_pb.php?provider_id=4207';/*/ $demoUrl;

	$smarty->assign('server_url', $serverUrl);

	ob_start();

	include $path;

	if (!isset($_SESSION['xml_query']))
	{
		$_SESSION['xml_query'] = array();
	}

	if (!isset($_SESSION['xml_answer']))
	{
		$_SESSION['xml_answer'] = array();
	}

	$xmlQueries = array();
	foreach ($_SESSION['xml_query'] as $xmlQuery)
	{
		$xmlQueries[] = pbXml::xml2html($xmlQuery);
	}

	$xmlAnswers = array();
	foreach ($_SESSION['xml_answer'] as $xmlAnswer)
	{
		$xmlAnswers[] = pbXml::xml2html($xmlAnswer);
	}

	$smarty->assign('xml_query',  $xmlQueries);
	$smarty->assign('xml_answer', $xmlAnswers);

	$smarty->display('view/top.tpl');
	$smarty->display('view/' . $page . '.tpl');
	$smarty->display('view/bottom.tpl');

	$s = ob_get_contents();
	ob_end_clean();

	echo $s;

?>