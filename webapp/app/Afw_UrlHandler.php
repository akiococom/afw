<?php
/**
 *  Afw_UrlHandler.php
 *
 *  @author     {$author}
 *  @package    Afw
 *  @version    $Id: app.url_handler.php 470 2007-07-08 17:48:26Z ichii386 $
 */

/**
 *  URLハンドラクラス
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Afw
 */
class Afw_UrlHandler extends Ethna_UrlHandler
{
    /** @var    array   アクションマッピング */
    var $action_map = array(
        'Afw' => array(
/** sample
        	'hoge_hoge' => array(
                'path' => 'hoge/hoge',
                'path_regexp' => '|^hoge/hoge/([\d]+)/([^/]+)$|',
                'path_ext' => array(
                    'hoge_id' => array(),
                    'char' => array(),
                ),
            ),
            'fuga_fuga' => array(
                'path' => 'fuga/fuga',
                'path_ext' => false,
            ),
 */
			// 固定ページ
            'about_view' => array(
                'path' => 'about',
                'path_ext' => false,
            ),
            'about_privacy_view' => array(
                'path' => 'privacy',
                'path_ext' => false,
            ),
            'about_terms_view' => array(
                'path' => 'terms',
                'path_ext' => false,
            ),
            
			// サインイン系
            'sign_in_form' => array(
                'path' => 'signin',
                'path_ext' => false,
            ),
            'sign_up_form' => array(
                'path' => 'signup',
                'path_ext' => false,
            ),
            'sign_out_accept' => array(
                'path' => 'signout',
                'path_ext' => false,
            ),
            'sign_up_mail_accept' => array(
                'path_regexp' => '|^mr/([\d]+)/([^/]+)$|',
                'path_ext' => array(
                    'user_id' => array(),
                    'password_md5' => array(),
                ),
            ),
            'sign_remind_password_form' => array(
                'path' => 'remind',
                'path_ext' => false,
            ),
            'sign_remind_mail_form' => array(
                'path_regexp' => '|^rm/([\d]+)/([^/]+)$|',
                'path_ext' => array(
                    'user_id' => array(),
                    'code' => array(),
                ),
            ),
            'user_leave_form' => array(
                'path' => 'leave',
                'path_ext' => false,
            ),
            
            // ユーザー系
            'user_view' => array(
                'path_regexp' => '|^user/([^/]+)$|',
                'path_ext' => array(
                    'user_key' => array(),
                ),
            ),
            'user_form' => array(
                'path' => 'app/user/edit',
                'path_ext' => false,
            ),
            'user_point_view' => array(
                'path' => 'app/user/point',
                'path_ext' => false,
            ),
            'user_point_list' => array(
                'path' => 'app/user/points',
                'path_ext' => false,
            ),
            
            // Stripe
            'user_payment_form' => array(
                'path' => 'payment',
                'path_ext' => false,
            ),
            'user_payment_cancel_form' => array(
                'path' => 'payment/cancel',
                'path_ext' => false,
            ),

            // 外部WPからの決済
            'payment_wp_form' => array(
                'path' => 'payment/wp',
                'path_ext' => false,
            ),
            'payment_wp_finish_view' => array(
                'path' => 'payment/wp/finish',
                'path_ext' => false,
            ),
            'payment_wp_cancel_view' => array(
                'path' => 'payment/wp/cancel',
                'path_ext' => false,
            ),
            
            // Stripe Webhook
            'stripe_subscription_update_accept' => array(
                'path' => 'stripe/subscription/update',
                'path_ext' => false,
            ),
            
            // コンテンツ系
            'page_form' => array(
                'path' => 'page/edit',
                'path_regexp' => '|^page/edit/([^/]+)$|',
                'path_ext' => array(
                    'page_key' => array(),
                ),
            ),
            'page_view' => array(
                'path_regexp' => '|^page/([^/]+)$|',
                'path_ext' => array(
                    'page_key' => array(),
                ),
            ),
            'page_list' => array(
                'path' => 'pages',
                'path_regexp' => array(
                	'month' => '|^pages/([^/]+)/([\d]+)$|',
                	'page' => '|^pages/([\d]+)$|',
                ),
                'path_ext' => array(
                	'date' => array(
                		'month' => array(),
                		'p' => array(),
                	),
                    'page' => array(
                    	'p' => array(),
                    ),
                ),
            ),
            
            // イベント系
            'event_view' => array(
                'path_regexp' => '|^event/([^/]+)$|',
                'path_ext' => array(
                    'event_key' => array(),
                ),
            ),
            'event_form' => array(
                'path' => 'manage/event/edit',
                'path_regexp' => '|^manage/event/edit/([^/]+)$|',
                'path_ext' => array(
                    'event_key' => array(),
                ),
            ),
            'event_studio_view' => array(
                'path' => 'manage/event/studio',
                'path_regexp' => '|^manage/event/studio/([^/]+)$|',
                'path_ext' => array(
                    'event_key' => array(),
                ),
            ),
            'event_list' => array(
                'path' => 'events',
                'path_ext' => false,
            ),
            'vod_list' => array(
                'path' => 'vods',
                'path_ext' => false,
            ),
            'feedback_event_view' => array(
                'path_regexp' => '|^feedbacker/([^/]+)$|',
                'path_ext' => array(
                    'event_key' => array(),
                ),
            ),

            // アーティスト
            'artist_list' => array(
                'path' => 'artists',
                'path_ext' => false,
            ),
            'artist_view' => array(
                'path_regexp' => '|^artist/([^/]+)$|',
                'path_ext' => array(
                    'artist_key' => array(),
                ),
            ),
            
            // お知らせ系
            'message_view' => array(
                'path_regexp' => '|^message/([\d]+)$|',
                'path_ext' => array(
                    'message_id' => array(),
                ),
            ),
            'message_form' => array(
                'path' => 'manage/message/edit',
                'path_regexp' => '|^manage/message/edit/([\d]+)$|',
                'path_ext' => array(
                    'message_id' => array(),
                ),
            ),
            'message_remove_accept' => array(
                'path_regexp' => '|^manage/message/remove/([\d]+)$|',
                'path_ext' => array(
                    'message_id' => array(),
                ),
            ),
            'message_list' => array(
                'path' => 'messages',
                'path_ext' => false,
            ),
            
            // ライブ系
            'live_view' => array(
                'path_regexp' => '|^live/([^/]+)$|',
                'path_ext' => array(
                    'live_key' => array(),
                ),
            ),
            'vod_view' => array(
                'path_regexp' => '|^vod/([^/]+)$|',
                'path_ext' => array(
                    'live_key' => array(),
                ),
            ),
            
            // QR系
            'qr_view' => array(
                'path' => 'qr',
                'path_ext' => false
            ),
            
            // システム系
            'upload_accept' => array(
                'path' => 'upload',
                'path_ext' => false
            ),
            'css_common_view' => array(
                'path' => 'plantcss',
                'path_ext' => false
            ),
            'twitter_view' => array(
                'path' => 'twitter',
                'path_ext' => false
            ),
            'twitter_api_accept' => array(
                'path' => 'twitter/callback',
                'path_ext' => false
            ),
            'manage_index' => array(
                'path' => 'manage',
                'path_ext' => false
            ),
            'manage_item_list' => array(
                'path' => 'manage/items',
                'path_ext' => false
            ),
            'manage_user_list' => array(
                'path' => 'manage/users',
                'path_ext' => false
            ),
            'manage_item_form' => array(
            	'path' => 'manage/item',
                'path_regexp' => '|^manage/item/([\d]+)$|',
                'path_ext' => array(
                    'item_id' => array(),
                ),
            ),
            'manage_bulk_user_form' => array(
                'path' => 'manage/bulk/user',
                'path_ext' => false
            ),
            'manage_genre_list' => array(
                'path' => 'manage/genres',
                'path_ext' => false
            ),
            
            // システムAPI
            
            // RTMP
            'api_rtmp_publish' => array(
                'path' => 'rtmp/publish',
                'path_ext' => false
            ),
            'api_rtmp_done' => array(
                'path' => 'rtmp/done',
                'path_ext' => false
            ),

            // IFRAME
            'embed_view' => array(
                'path_regexp' => '|^embed/([^/]+)$|',
                'path_ext' => array(
                    'event_key' => array(),
                ),
            ),
        ),
    );

    /**
     *  Afw_UrlHandlerクラスのインスタンスを取得する
     *
     *  @access public
     */
    function &getInstance($class_name = null)
    {
        $instance =& parent::getInstance(__CLASS__);
        return $instance;
    }

    // {{{ ゲートウェイリクエスト正規化
    /**
     *  リクエスト正規化(userゲートウェイ)
     *
     *  @access private
     */
    /*
    function _normalizeRequest_User($http_vars)
    {
        return $http_vars;
    }
     */
    // }}}

    // {{{ ゲートウェイパス生成
    /**
     *  パス生成(userゲートウェイ)
     *
     *  @access private
     */
    /*
    function _getPath_User($action, $param)
    {
        return array("/user", array());
    }
     */
    // }}}

    // {{{ フィルタ
    // }}}
}

// vim: foldmethod=marker tabstop=4 shiftwidth=4 autoindent
?>
