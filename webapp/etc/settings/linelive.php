<?php
$config = array(
    // SERVER settings
    'url' => 'https://liveline.jp/',	// /で終わる
    'base' => '/',			// /ではじまり/で終わる
	'dsn' => 'mysql://plant:onsakura@localhost/liveline.jp',
	'stream_servers' => array(
		'local' => 'https://stream-spice.onsakura.club/',
		'global' => 'https://stream-spice.onsakura.club/',
		'vod' => 'https://spice.onsakura.club/',
	),
	'stream_name' => 'spicestream',
	'stripe_public_key' => 'pk_live_51IyWzHL31rKCFPprZBf8XpscooWJI7pN3le50J6gQGC8NTxnaqzXRakUi9SBMFulnZFypOfLj4bZ2NEZglSnG0um00XUGxb9Fx',
	'stripe_secret_key' => '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',
	'stripe_monthly_plan' => false,

	// SITE settings
	'app_name' => 'SPiCE STREAM',
	'md5_salt' => 'spice5',	
	'site_description' => 'SPiCEがお送りするストリーミング配信サイト',
	'footer_url' => 'https://spice-sapporo.jp/',
	'footer_name' => 'SPiCE',
	'config_insta' => false,
	'config_twitter' => 'SPiCE_SAPPORO',
	'google_analytics' => false,
	'jasrac' => false,
	'event_name' => 'ライブ',
	'menu' => array(
		// メニュー名 => array(0:全員 1:非ログイン 2:ログイン,  URL)
		'お知らせ' => array(0, 'messages/'),
		'ライブ' => array(0, 'events/'),
		// 'はじめての方へ' => array(1, 'about/'),
		// 'サインアップ' => array(1, 'signup/'),
	),
	'place_user' => 'スパイス太郎',
	'place_url' => 'spice',
	'views' => array(
		// 以下はaction_view
		'about' => 'onsakura_about',
		'css' => 'onsakura_css',
		// 以下はinclude
		'news' => 'onsakura/news.tpl',
		'signup' => 'onsakura/signup.tpl',
	),
	'asset_url' => 'assets/clubs/spice/', 
	'live_cover' => 'assets/clubs/spice/live_cover.png', 
 	'nouser_image' => '../assets/clubs/spice/h_fc_box.png',
	'logo_filename' => 'h_fc_white.png',

	// MAIL settings
	'mail_from'	=> 'noreply@onsakura.club',
	'mail_domain' => '@onsakura.club',
	'mail_debug' => 'akio@spa.att.ne.jp',
	'mail_debug_mobile' => '',
	'mail_support' => 'support@live-air.tech',
	'mail_bcc' => 'onsakura@live-air.tech',
);