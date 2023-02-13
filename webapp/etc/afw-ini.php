<?php
$customer = array();
if (file_exists(dirname(__FILE__) . '/afw-customer-ini.php')) require_once dirname(__FILE__) . '/afw-customer-ini.php';

/**
 * - システム自体の設定値 -
 * 環境に依存しない設定値はここで設定しバージョン管理の対象とします。
 */
$default = array(
	// サーバー設定関連
    'url' => 'http://' . @$_SERVER['HTTP_HOST'] . '/afw/',	// /で終わる
    'base' => '/afw/',			// /ではじまり/で終わる
    'dsn' => 'mysql://glexa:glexa@localhost/dbname',

	// SITE Settings
	'app_name' => 'AFW',
	'google_analytics' => '',

	// MAIL settings
	'mail_from'	=> 'NORPLAY <noreply@plant-fc.com>',
	'mail_debug' => 'akio@spa.att.ne.jp',
	'mail_debug_mobile' => '',
	'mail_bcc' => 'akio@spa.att.ne.jp',
	
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

$config = array_merge($default, $customer);
?>
