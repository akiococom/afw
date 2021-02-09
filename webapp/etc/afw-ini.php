<?php
$db = array();
$customer = array();
if (file_exists(dirname(__FILE__) . '/afw-db-ini.php')) require_once dirname(__FILE__) . '/afw-db-ini.php';
if (file_exists(dirname(__FILE__) . '/afw-customer-ini.php')) require_once dirname(__FILE__) . '/afw-customer-ini.php';

/**
 * - システム自体の設定値 -
 * 環境に依存しない設定値はここで設定しバージョン管理の対象とします。
 */
$default = array(
    // site
	'app_name' => 'AFW',
    'url' => 'http://' . @$_SERVER['HTTP_HOST'] . '/afw/',	// /で終わる
    'base' => '/afw/',			// /ではじまり/で終わる
	
    // db
    'dsn' => 'mysql://akky:akky@localhost/afw',
	
	// mail
	'mail_from'	=> 'ADMIN',
	'mail_debug' => '',
	'mail_debug_mobile' => '',
	
	// session
 	// 'session_table' => 'sys_sessions', // nullでファイルに保存
 	
	// debug
    'debug' => true,		// デフォルトの影響範囲: メールをmail_debugに送信する

    // log
    'log_directory' 			=> dirname(__FILE__) . '/../log/',
    'log_facility'          => 'echo',
    'log_level'             => 'warning',
    'log_option'            => 'pid,function,pos',
    'log_filter_do'         => '',
    'log_filter_ignore'     => 'Undefined index.*%%.*tpl',
);

$config = array_merge($default, $db, $customer);
?>
