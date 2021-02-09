<?php
require_once dirname(__FILE__) . '/config.inc.php';

define('SYSTEM_LOG', true);

if (SYSTEM_LOG) {
	$post = substr(str_replace(array('Array' . PHP_EOL . '(', ')' . PHP_EOL), '', print_r($_POST, 1)), 0, 500);
	// $post = str_replace(array('Array' . PHP_EOL . '(', ')' . PHP_EOL), '', print_r($_POST, 1));
	$get = str_replace(array('Array' . PHP_EOL . '(', ')' . PHP_EOL), '', print_r($_GET, 1));
	$files = str_replace(array('Array' . PHP_EOL . '(', ')' . PHP_EOL), '', print_r($_FILES, 1));
	file_put_contents('./webapp/log/test.log', date('Y-m-d H:i:s') . "\t" . ' requested.' . PHP_EOL . $get . $post . ($files ? print_r($files, 1) : ''), FILE_APPEND);
}

$_SERVER['URL_HANDLER'] = 'Afw';

// 携帯使用時
// define('mobile', true);

header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
Afw_Controller::main('Afw_Controller', 'index', 'index');

/* アクセスできるアクションを限定する場合
Afw_Controller::main('Afw_Controller', array(
		'index',
		'hoge_*',
	)
);
*/