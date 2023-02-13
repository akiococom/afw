<?php

$customer = array(
	$customer = array(
		'url' => 'https://form.onsakura.club/',	// /で終わる
		'base' => '/',			// /ではじまり/で終わる
		'app_name' => '愛と平和',
		'md5_salt' => 'form5',
		'debug' =>false,
		'site_description' => 'form 「愛と平和」ライブ配信サイト',
		'footer_url' => 'https://form.onsakura.club/',
		'footer_name' => 'Powered by 「愛と平和」',
		'config_insta' => '',
		'config_twitter' => '',
		'google_analytics' => 'G-J81GRL27T7',
		
		'event_name' => 'イベント',
		'menu' => array(
			// メニュー名 => array(0:全員1:非ログイン2:ログイン, URL)
			'お知らせ' => array(0, 'messages/'),
			// '特集' => array(0, 'pages/'),
			'イベント' => array(0, 'events/'),
			// 'PLANT FCとは' => array(2, 'about/'),
			//'はじめての方へ' => array(1, 'about/'),
			'サインアップ' => array(1, 'signup/'), 
		),
		'place_user' => 'フォーム太郎',
		'place_url' => 'form',
		'views' => array(
			'about' => 'plant_about',
			'css' => 'onsakura_css',
			'news' => 'onsakura/news.tpl',
		),
		
		'is_test' => false,			// テストサイト（BASIC認証）
		'is_stripe' => true,		// Stripe決済
		'is_js' => true,			// JS圧縮
		'is_expire' => false,
		'chat_user_id' => 1,		// チャットシステムユーザーID
		'jasrac' => '',
		
		'asset_url' => 'assets/clubs/form/', 
		'nouser_image' => '../assets/clubs/form/h_fc_box.png',
		
		'basic_authors' => array(
			'plant' => 'fc',
		),
		
		// db
		'dsn' => 'mysql://plant:onsakura@localhost/form.onsakura.club',
		
		// mail
		'mail_from'	=> 'noreply@onsakura.club',
		'mail_domain' => '@onsakura.club',
		'mail_debug' => 'akio@spa.att.ne.jp',
		'mail_debug_mobile' => '',
		'mail_support' => 'support@apero.love',
		'mail_bcc' => 'akky@apero.love',
		
		'stream_servers' => array(
			'local' => 'https://stream.onsakura.club/',
			//'local' => 'https://stream.onsakura.club/',
			'global' => 'https://stream.onsakura.club/',
			//'global' => 'https://stream.onsakura.club/',
	
			'vod' => 'https://vod.onsakura.club/',
		),
		'stream_name' => 'form',
	
		'stripe_public_key' => 'pk_live_51IyWzHL31rKCFPprZBf8XpscooWJI7pN3le50J6gQGC8NTxnaqzXRakUi9SBMFulnZFypOfLj4bZ2NEZglSnG0um00XUGxb9Fx',
		'stripe_secret_key' => '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',
		'stripe_monthly_plan' => 'price_1IXhP7F01aaxXHxpJAL0msqD',
		);
	
    