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
