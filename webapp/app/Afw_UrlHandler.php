<?php
/**
 *  Afw_UrlHandler.php
 *
 *  @author     akio.co.com
 *  @package    Afw
 *  @version    $Id: app.url_handler.php 470 2007-07-08 17:48:26Z ichii386 $
 */

/**
 *  URLハンドラクラス
 *
 *  @author     akio.co.com
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
			// サインイン系
            'sign_in_form' => array(
                'path' => 'app/signin',
                'path_ext' => false,
            ),
            'sign_up_form' => array(
                'path' => 'app/signup',
                'path_ext' => false,
            ),
            'sign_out_accept' => array(
                'path' => 'app/signout',
                'path_ext' => false,
            ),
            'sign_up_mail_accept' => array(
                'path_regexp' => '|^app/mailreg/([\d]+)/([^/]+)$|',
                'path_ext' => array(
                    'user_id' => array(),
                    'password_md5' => array(),
                ),
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
            'user_point_list' => array(
                'path' => 'app/user/points',
                'path_regexp' => '|^app/user/points/([^/]+)$|',
                'path_ext' => array(
                    'user_key' => array(),
                ),
            ),
            
            // コンテンツ系
            'page_form' => array(
                'path' => 'app/page/edit',
                'path_regexp' => '|^app/page/edit/([^/]+)$|',
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
                'path' => 'app/pages',
                'path_ext' => false,
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
            'event_list' => array(
                'path' => 'app/events',
                'path_ext' => false,
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
            'message_list' => array(
                'path' => 'app/messages',
                'path_ext' => false,
            ),
            
            // ライブ系
            'live_view' => array(
                'path_regexp' => '|^live/([^/]+)$|',
                'path_ext' => array(
                    'live_key' => array(),
                ),
            ),
            
            // QR系
            'qr_view' => array(
                'path' => 'qr',
                'path_ext' => false
            ),
            
            // ブロック系
            'ajax_event_list' => array(
                'path' => 'block/events',
                'path_ext' => false
            ),
            'ajax_artist_list' => array(
                'path' => 'block/artists',
                'path_ext' => false
            ),
            'ajax_message_list' => array(
                'path' => 'block/messages',
                'path_ext' => false
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
