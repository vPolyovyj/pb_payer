<?php

	$localhostPath  = 'd:/www/web';//'d:/test/www';
	$smartyLibsPath = $localhostPath  . '/Smarty/libs';
	$smartyPath     = $smartyLibsPath . '/Smarty.Class.php';

	ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . $smartyPath);

	require_once $smartyPath;

	$smarty = new Smarty();

	$smartyCompileDirPath = $localhostPath . '/pb_payer';

	$smarty->template_dir = '.';
	$smarty->compile_dir  = $smartyCompileDirPath . '/templates_c';
	$smarty->config_dir   = '.';
	$smarty->cache_dir    = $smartyCompileDirPath . '/cache';

	require_once 'functions.php';
	require_once 'classes/httpRequester.class.php';
	require_once 'classes/pbXml.class.php';

	$basePath   = $localhostPath . '/pb_payer';
	$baseUrl    = 'http://localhost/pb_payer';
	$demoUrl    = 'http://localhost/PHP-PrivatBank/pb.php';

	$smarty->assign('base_url', $baseUrl);
	$smarty->assign('demo_url', $demoUrl);

	$testPayers = array(
		1 => array('id' => 1, 'name' => 'Алан Квотермейн', 'num' => '111111', 'phone' => '69315143', 
			'address' => 'м. Львів, вул. Дудаєва, буд. 1, кв. 3'),
		2 => array('id' => 2, 'name' => 'Сайрес Сміт', 'num' => '111222', 'phone' => '25364453', 
			'address' => 'м. Івано-Франківськ, вул. Галицька, буд. 5, кв. 21'),
		3 => array('id' => 3, 'name' => 'Залізний Дроворуб', 'num' => '333333', 'phone' => '3453524', 
			'address' => 'м. Київ, вул. Андріївський Узвіз, буд. 14А, кв. 9'),
		4 => array('id' => 4, 'name' => 'Нік Адамс', 'num' => '222444', 'phone' => '25434254', 
			'address' => 'м. Одеса, вул. Рішельє, буд. 31, кв. 1'),
		5 => array('id' => 5, 'name' => 'Урфін Джус', 'num' => '333555', 'phone' => '63736465', 
			'address' => 'м. Одеса, вул. Рішельє, буд. 31, кв. 33'),
		6 => array('id' => 6, 'name' => 'Аліса', 'num' => '888888', 'phone' => '53532424', 
			'address' => 'м. Лондон, вул. Пел Мел, буд. 1, кв. 1')
	);

	$smarty->assign('test_payers', $testPayers);

?>