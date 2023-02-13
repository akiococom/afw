<?php
$config = array(
	// LIVEAIRテストKEY（本番サーバーのみ本番KEY）
	'stripe_public_key' => 'pk_test_51IyWzHL31rKCFPpr7AtxshL6T9DiMZTaXLU3XFJiiyQ9QL0vnlIUYIOQzopQ051jDAgzLfeDOtPyALp479NnDO1800oImbxyoV',
	'stripe_secret_key' => 'sk_test_51IyWzHL31rKCFPprCajX5akDS8cxWGi7gRtDHW8Koah7zpVEfaa78Kq0bKY80Izgx9b57ma0WDbOVU6CZl7oEt7P005zAgspwD',
	'stripe_monthly_plan' => 'price_1JCHsKL31rKCFPprpgzH8pkM',
	

	// SITE settings
	'app_name' => 'PLANT FC',
	'md5_salt' => 'plant5',	
	'site_description' => 'PLANTファンに送るファンクラブサイトPLANT FC。ライブ配信や会員限定コンテンツをお楽しみいただけます。',
	'footer_url' => 'http://www.plant-ent.com/',
	'footer_name' => 'PLANT ENTERTAINMAINT',
	'config_insta' => 'sapporo_plant',
	'config_twitter' => 'plant_fc',
	'google_analytics' => false,
	'jasrac' => '9026546002Y45037',
	'event_name' => 'ライブ',
	'menu' => array(
		// メニュー名 => array(0:全員 1:非ログイン 2:ログイン,  URL)
		'お知らせ' => array(0, 'messages/'),
		// '特集' => array(0, 'pages/'),
		'ライブ' => array(0, 'events/'),
		'PLANT FCとは' => array(2, 'about/'),
		'はじめての方へ' => array(1, 'about/'),
		'会員登録' => array(1, 'signup/'),
	),
	'place_user' => 'プラント太郎',
	'place_url' => 'plant_fc',
	'views' => array(
		// 以下はaction_view
		'about' => 'plant_about',
		'css' => 'plant_css',
		// 以下はinclude
		'news' => 'plant/news.tpl',
		'signup' => 'plant/signup.tpl',
	),
	'logo_filename' => 'h_fc_white.png',
	'asset_url' => 'assets/plant/', 
	'live_cover' => 'assets/plant/live_cover.png', 
 	'nouser_image' => '../assets/plant/h_fc_box.png',

	// MAIL settings
	'mail_from'	=> 'PLANT-FC <noreply@plant-fc.com>',
	'mail_domain' => '@plant-fc.com',
	'mail_debug' => 'akio@spa.att.ne.jp',
	'mail_debug_mobile' => '',
	'mail_support' => 'support@plant-fc.com',
	'mail_bcc' => 'plant@apero.love',
	
	// SYSTEM settings
	'is_test' => true,			// テストサイト（BASIC認証）
	'is_js' => true,			// JS圧縮
	'is_expire' => true,		// サブスク会員
	'chat_user_id' => 1,		// チャットシステムユーザーID
	'chat_sec' => 10,
	 	
 	'basic_authors' => array(
 		'plant' => 'fc',
 	),
);