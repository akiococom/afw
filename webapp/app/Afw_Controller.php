<?php
/**
 *  Afw_Controller.php
 *
 *  @author     {$author}
 *  @package    Afw
 *  @version    $Id: app.controller.php 470 2007-07-08 17:48:26Z ichii386 $
 */

define('TAB', "\t");
define('EMPTY_DATETIME', '0000-00-00 00:00:00');

// 不明ファイル読み出し時の処理
if (strpos(@$_SERVER['REQUEST_URI'], 'common/') !== false) {
	exit;
}
if (strpos(@$_SERVER['REQUEST_URI'], 'assets/') !== false) {
	exit;
}
if (strpos(@$_SERVER['REQUEST_URI'], 'files/') !== false) {
	exit;
}

/** 文字コードの設定 **/
mb_internal_encoding('utf8');

/** アプリケーションベースディレクトリ */
define('BASE', dirname(dirname(__FILE__)));

if (defined('E_DEPRECATED')) {
	error_reporting(error_reporting() & ~E_WARNING & ~E_DEPRECATED & ~E_STRICT & ~E_NOTICE);
}

/** include_pathの設定(アプリケーションディレクトリを追加) */
$app = BASE . "/app";
$lib = BASE . "/lib";

$include_path = array(
	$app,
	$lib,
);
ini_set('include_path', implode(PATH_SEPARATOR, $include_path) . PATH_SEPARATOR .  ini_get('include_path'));

function AfwLoadClass($className)
{
	if (file_exists($classFile = BASE . "/app/class/$className.php"))
		require_once $classFile;
}
spl_autoload_register('AfwLoadClass');

/** アプリケーションライブラリのインクルード */
if (!class_exists('Ethna')) {
	require_once $lib . '/Ethna/Ethna.php';
}
require_once 'Afw_Error.php';
require_once 'Afw_ActionClass.php';
require_once 'Afw_ActionAdminClass.php';
require_once 'Afw_ActionForm.php';
require_once 'Afw_AppManager.php';
require_once 'Afw_ViewClass.php';
require_once 'Afw_DB_PEAR.php';
require_once 'Afw_Session.php';
require_once 'Afw_SmartyPlugin.php';

/**
 *  Afwアプリケーションのコントローラ定義
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Afw
 */
class Afw_Controller extends Ethna_Controller
{
    /**#@+
     *  @access private
     */

    /**
     *  @var    string  アプリケーションID
     */
    var $appid = 'Afw';

    /**
     *  @var    array   forward定義
     */
    var $forward = array(
        /*
         *  TODO: ここにforward先を記述してください
         *
         *  記述例：
         *
         *  'index'         => array(
         *      'view_name' => 'Afw_View_Index',
         *  ),
         */
    );

    /**
     *  @var    array   action定義
     */
    var $action = array(
        /*
         *  TODO: ここにaction定義を記述してください
         *
         *  記述例：
         *
         *  'index'     => array(),
         */
    );

    /**
     *  @var    array   soap action定義
     */
    var $soap_action = array(
        /*
         *  TODO: ここにSOAPアプリケーション用のaction定義を
         *  記述してください
         *  記述例：
         *
         *  'sample'            => array(),
         */
    );

    /**
     *  @var    array       アプリケーションディレクトリ
     */
    var $directory = array(
        'action'        => 'app/action',
        'action_cli'    => 'app/action_cli',
        'action_xmlrpc' => 'app/action_xmlrpc',
        'app'           => 'app',
        'plugin'        => 'app/plugin',
        'bin'           => 'bin',
        'etc'           => 'etc',
        'filter'        => 'app/filter',
        'locale'        => 'locale',
        'log'           => 'log',
        'plugins'       => array(),
        'template'      => 'template',
        'template_c'    => 'tmp',
        'tmp'           => 'tmp',
        'view'          => 'app/view',
        'www'           => 'www',
    );

    /**
     *  @var    array       DBアクセス定義
     */
    var $db = array(
        ''              => DB_TYPE_RW,
    );

    /**
     *  @var    array       拡張子設定
     */
    var $ext = array(
        'php'           => 'php',
        'tpl'           => 'tpl',
    );

    /**
     *  @var    array   クラス定義
     */
    var $class = array(
        /*
         *  TODO: 設定クラス、ログクラス、SQLクラスをオーバーライド
         *  した場合は下記のクラス名を忘れずに変更してください
         */
        'class'         => 'Ethna_ClassFactory',
        'backend'       => 'Ethna_Backend',
        'config'        => 'Ethna_Config',
        'db'            => 'Afw_DB_PEAR',
        'error'         => 'Ethna_ActionError',
        'form'          => 'Afw_ActionForm',
        'i18n'          => 'Ethna_I18N',
        'logger'        => 'Ethna_Logger',
        'plugin'        => 'Ethna_Plugin',
        'session'       => 'Afw_Session',
        'sql'           => 'Ethna_AppSQL',
        'view'          => 'Afw_ViewClass',
        'renderer'      => 'Ethna_Renderer_Smarty',
        'url_handler'   => 'Afw_UrlHandler',
    );

    /**
     *  @var    array       検索対象となるプラグインのアプリケーションIDのリスト
     */
    var $plugin_search_appids = array(
        /*
         *  プラグイン検索時に検索対象となるアプリケーションIDのリストを記述します。
         *
         *  記述例：
         *  Common_Plugin_Foo_Bar のような命名のプラグインがアプリケーションの
         *  プラグインディレクトリに存在する場合、以下のように指定すると
         *  Common_Plugin_Foo_Bar, Afw_Plugin_Foo_Bar, Ethna_Plugin_Foo_Bar
         *  の順にプラグインが検索されます。 
         *
         *  'Common', 'Afw', 'Ethna',
         */
        'Afw', 'Ethna',
    );

    /**
     *  @var    array       フィルタ設定
     */
    var $filter = array(
        /*
         *  TODO: フィルタを利用する場合はここにそのプラグイン名を
         *  記述してください
         *  (クラス名を指定するとfilterディレクトリからフィルタクラス
         *  を読み込みます)
         *
         *  記述例：
         *
         *  'ExecutionTime',
         */
    );

    /**
     *  @var    array   smarty modifier定義
     */
    var $smarty_modifier_plugin = array(
        /*
         *  TODO: ここにユーザ定義のsmarty modifier一覧を記述してください
         *
         *  記述例：
         *
         *  'smarty_modifier_foo_bar',
         */
    	'smarty_modifier_age',
    	'smarty_modifier_br',
    	'smarty_modifier_link',
    	'smarty_modifier_date',
    	'smarty_modifier_datetime',
    	'smarty_modifier_html',
    	'smarty_modifier_radio',
    	'smarty_modifier_topic',
    	'smarty_modifier_strip',
    	'smarty_modifier_topic',
    	'smarty_modifier_is_date',
    	'smarty_modifier_day',
    	'smarty_modifier_week',
    	'smarty_modifier_space',
    	'smarty_modifier_rate',
    	'smarty_modifier_qrsum',
    	'smarty_modifier_period',
    	'smarty_modifier_bytes',
    	'smarty_modifier_time',
    	'smarty_modifier_tel',
    	'smarty_modifier_numeric',
    	'smarty_modifier_filename',
    	'smarty_modifier_han',
    	'smarty_modifier_zen',
    );

    /**
     *  @var    array   smarty function定義
     */
    var $smarty_function_plugin = array(
        /*
         *  TODO: ここにユーザ定義のsmarty function一覧を記述してください
         *
         *  記述例：
         *
         *  'smarty_function_foo_bar',
         */
    	'smarty_function_required',
    	'smarty_function_messages',
    	'smarty_function_afwinput',
    	'smarty_function_afwtextarea',
    	'smarty_function_afwselect',
    );

    /**
     *  @var    array   smarty block定義
     */
    var $smarty_block_plugin = array(
        /*
         *  TODO: ここにユーザ定義のsmarty block一覧を記述してください
         *
         *  記述例：
         *
         *  'smarty_block_foo_bar',
         */
    );

    /**
     *  @var    array   smarty prefilter定義
     */
    var $smarty_prefilter_plugin = array(
        /*
         *  TODO: ここにユーザ定義のsmarty prefilter一覧を記述してください
         *
         *  記述例：
         *
         *  'smarty_prefilter_foo_bar',
         */
    );

    /**
     *  @var    array   smarty postfilter定義
     */
    var $smarty_postfilter_plugin = array(
        /*
         *  TODO: ここにユーザ定義のsmarty postfilter一覧を記述してください
         *
         *  記述例：
         *
         *  'smarty_postfilter_foo_bar',
         */
    );

    /**
     *  @var    array   smarty outputfilter定義
     */
    var $smarty_outputfilter_plugin = array(
        /*
         *  TODO: ここにユーザ定義のsmarty outputfilter一覧を記述してください
         *
         *  記述例：
         *
         *  'smarty_outputfilter_foo_bar',
         */
    );

	/**
	 *  フォームにより要求されたアクション名を返す
	 *
	 *  @access protected
	 *  @return string  フォームにより要求されたアクション名
	 */
	function _getActionName_Form()
	{
	    if (array_key_exists('action', $_REQUEST) == false) {
	        return parent::_getActionName_Form();
	    }
	    return $_REQUEST['action'];
	}

	/**
	 *  パフォーマンス改善のためにダミー実装でオーバーライド
	 *  
	 *  (Glexa においては、このメソッドによって生成されるクラス名が
	 *   実在するクラスにヒットすることは無いが、不必要に呼び出されると
	 *   大きなボトルネックとなるため、ダミーに置き換える)
	 *  
	 *  ※ JMeter によるベンチマークで約60%パフォーマンス向上
	 *  
	 *  @param string $name  マネージャ名
	 *  @return string  実在のクラス名 (本来の実装では引数の値から生成)
	 */
	function getObjectClassName($name)
	{
		// 必ず存在するクラス名を返すことで重い処理をスキップさせる
		//return parent::getObjectClassName($name);
		return 'stdClass';
	}	
    /**#@-*/
}
?>
