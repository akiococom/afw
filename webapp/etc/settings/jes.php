<?php
$config = array(
	'stripe_public_key' => 'pk_live_51IyWzHL31rKCFPprZBf8XpscooWJI7pN3le50J6gQGC8NTxnaqzXRakUi9SBMFulnZFypOfLj4bZ2NEZglSnG0um00XUGxb9Fx',
	'stripe_secret_key' => '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',

	// SITE settings
	'app_name' => 'JES 関東・埼玉 大会当日ページ',
	'md5_salt' => 'spice5',	
	'site_description' => '第21回 小学校英語教育学会 関東・埼玉大会の大会当日ページです。',
	'footer_url' => 'https://jes-zenkoku.sakura.ne.jp/2021/',
	'footer_name' => '第21回 小学校英語教育学会 関東・埼玉大会',
	'config_insta' => false,
	'config_twitter' => false,
	'google_analytics' => 'G-6K86WHXN61',
	'jasrac' => false,
	'event_name' => '発表',
	'cheer_message' => '',
	'menu' => array(
		// メニュー名 => array(0:全員 1:非ログイン 2:ログイン,  URL)
		'領収書' => array(0, 'receipt/'),
		'お問い合わせ' => array(0, 'support/'),
		// 'お知らせ' => array(0, 'messages/'),
		// '特集' => array(0, 'pages/'),
		// 'ライブ' => array(0, 'events/'),
		// 'PLANT FCとは' => array(2, 'about/'),
		// 'はじめての方へ' => array(1, 'about/'),
		// '会員登録' => array(1, 'signup/'),
	),
	'place_user' => '小学校太郎',
	'place_url' => '',
	'index_action' => 'custom_jes_index',
	'sigined_action' => '/',
	'views' => array(
		// 以下はaction_view
		// 'about' => 'plant_about',
		'css' => 'onsakura_jes_css',
		'receipt' => 'custom_jes_receipt',
		// 以下はinclude
		'news' => 'onsakura/jes/news.tpl',
		//'signup' => 'plant/signup.tpl',
		'receipt' => 'custom/jes/receipt.tpl',
	),
	'logo_filename' => false,
	'asset_url' => 'assets/confs/jes2021/', 
	'live_cover' => false, 
 	'nouser_image' => 'assets/confs/nouser.png',

	// MAIL settings
	'mail_from'	=> '第21回 小学校英語教育学会 <noreply@jes-zenkoku.sakura.ne.jp>',
	'mail_domain' => '@jes-zenkoku.sakura.ne.jp',
	'mail_debug' => 'akio@spa.att.ne.jp',
	'mail_debug_mobile' => '',
	'mail_support' => 'support@live-air.tech',
	'mail_bcc' => 'jes@live-air.tech',
	'payment_support' => 'JES2021関東埼玉大会事務局(jeskantosaitama2021@gmail.com)',
	
	// SYSTEM settings
	'is_test' => false,			// テストサイト（BASIC認証）
	'is_js' => true,			// JS圧縮
	'is_expire' => false,		// サブスク会員
	'is_signup' => false,		// サインアップ
	'is_point' => false,			// ポイント利用
	'is_mypage' => false,		// マイページ
	'is_favorite' => false,		// イベントのいいね
	'is_plain_password' => true,	
	'chat_user_id' => 1,		// チャットシステムユーザーID
	'chat_sec' => 10,
	 	
 	'basic_authors' => array(
 		'plant' => 'fc',
 	),

);