<?php
// vim: foldmethod=marker
/**
 *  Ethna_Controller.php
 *
 *  @author     Masaki Fujimoto <fujimoto@php.net>
 *  @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 *  @package    Ethna
 *  @version    $Id: Ethna_Controller.php 459 2007-02-18 20:30:55Z ichii386 $
 */

// {{{ Ethna_Controller
/**
 *  コントローラクラス
 *
 *  @todo       gatewayでswitchしてるところがダサダサ
 *
 *  @author     Masaki Fujimoto <fujimoto@php.net>
 *  @access     public
 *  @package    Ethna
 */
class Ethna_Controller
{
    /**#@+
     *  @access private
     */

    /** @var    string      アプリケーションID */
    var $appid = 'ETHNA';

    /** @var    string      アプリケーションベースディレクトリ */
    var $base = '';

    /** @var    string      アプリケーションベースURL */
    var $url = '';

    /** @var    string      アプリケーションDSN(Data Source Name) */
    var $dsn;

    /** @var    array       アプリケーションディレクトリ */
    var $directory = array();

    /** @var    array       アプリケーションディレクトリ(デフォルト) */
    var $directory_default = array(
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

    /** @var    array       DBアクセス定義 */
    var $db = array(
        ''              => DB_TYPE_RW,
    );

    /** @var    array       拡張子設定 */
    var $ext = array(
        'php'           => 'php',
        'tpl'           => 'tpl',
    );

    /** @var    array       クラス設定 */
    var $class = array();

    /** @var    array       クラス設定(デフォルト) */
    var $class_default = array(
        'class'         => 'Ethna_ClassFactory',
        'backend'       => 'Ethna_Backend',
        'config'        => 'Ethna_Config',
        'db'            => 'Ethna_DB',
        'error'         => 'Ethna_ActionError',
        'form'          => 'Ethna_ActionForm',
        'i18n'          => 'Ethna_I18N',
        'logger'        => 'Ethna_Logger',
        'plugin'        => 'Ethna_Plugin',
        'renderer'      => 'Ethna_Renderer_Smarty',
        'session'       => 'Ethna_Session',
        'sql'           => 'Ethna_AppSQL',
        'view'          => 'Ethna_ViewClass',
        'url_handler'   => 'Ethna_UrlHandler',
    );

    /** @var    array       検索対象となるプラグインのアプリケーションIDのリスト */
    var $plugin_search_appids;

    /** @var    array       フィルタ設定 */
    var $filter = array(
    );

    /** @var    string      使用言語設定 */
    var $language;

    /** @var    string      システム側エンコーディング */
    var $system_encoding;

    /** @var    string      クライアント側エンコーディング */
    var $client_encoding;

    /** @var    string  現在実行中のアクション名 */
    var $action_name;

    /** @var    string  現在実行中のXMLRPCメソッド名 */
    var $xmlrpc_method_name;

    /** @var    array   forward定義 */
    var $forward = array();

    /** @var    array   action定義 */
    var $action = array();

    /** @var    array   action(CLI)定義 */
    var $action_cli = array();

    /** @var    array   action(XMLRPC)定義 */
    var $action_xmlrpc = array();

    /** @var    array   アプリケーションマネージャ定義 */
    var $manager = array();

    /** @var    object  レンダラー */
    var $renderer = null;

    /** @var    array   smarty modifier定義 */
    var $smarty_modifier_plugin = array();

    /** @var    array   smarty function定義 */
    var $smarty_function_plugin = array();

    /** @var    array   smarty block定義 */
    var $smarty_block_plugin = array();

    /** @var    array   smarty prefilter定義 */
    var $smarty_prefilter_plugin = array();

    /** @var    array   smarty postfilter定義 */
    var $smarty_postfilter_plugin = array();

    /** @var    array   smarty outputfilter定義 */
    var $smarty_outputfilter_plugin = array();


    /** @var    array   フィルターチェイン(Ethna_Filterオブジェクトの配列) */
    var $filter_chain = array();

    /** @var    object  Ethna_ClassFactory  クラスファクトリオブジェクト */
    var $class_factory = null;

    /** @var    object  Ethna_ActionForm    フォームオブジェクト */
    var $action_form = null;

    /** @var    object  Ethna_View          ビューオブジェクト */
    var $view = null;

    /** @var    object  Ethna_Config        設定オブジェクト */
    var $config = null;

    /** @var    object  Ethna_Logger        ログオブジェクト */
    var $logger = null;

    /** @var    object  Ethna_Plugin        プラグインオブジェクト */
    var $plugin = null;

    /** @var    string  リクエストのゲートウェイ(www/cli/rest/xmlrpc/soap...) */
    var $gateway = GATEWAY_WWW;

    /**#@-*/


    /**
     *  Ethna_Controllerクラスのコンストラクタ
     *
     *  @access     public
     */
    function __construct($gateway = GATEWAY_WWW)
    {
        $GLOBALS['_Ethna_controller'] = $this;
        if ($this->base === "") {
            // EthnaコマンドなどでBASEが定義されていない場合がある
            if (defined('BASE')) {
                $this->base = BASE;
            }
        }

        $this->gateway = $gateway;

        // クラス設定の未定義値を補完
        foreach ($this->class_default as $key => $val) {
            if (isset($this->class[$key]) == false) {
                $this->class[$key] = $val;
            }
        }

        // ディレクトリ設定の未定義値を補完
        foreach ($this->directory_default as $key => $val) {
            if (isset($this->directory[$key]) == false) {
                $this->directory[$key] = $val;
            }
        }

        // クラスファクトリオブジェクトの生成
        $class_factory = $this->class['class'];
        $this->class_factory = new $class_factory($this, $this->class);

        // エラーハンドラの設定
        Ethna::setErrorCallback(array(&$this, 'handleError'));

        // ディレクトリ名の設定(相対パス->絶対パス)
        foreach ($this->directory as $key => $value) {
            if ($key == 'plugins') {
                // Smartyプラグインディレクトリは配列で指定する
                $tmp = array();
                foreach (to_array($value) as $elt) {
                    if (Ethna_Util::isAbsolute($elt) == false) {
                        $tmp[] = $this->base . (empty($this->base) ? '' : '/') . $elt;
                    }
                }
                $this->directory[$key] = $tmp;
            } else {
                if (Ethna_Util::isAbsolute($value) == false) {
                    $this->directory[$key] = $this->base . (empty($this->base) ? '' : '/') . $value;
                }
            }
        }

        // 初期設定
        list($this->language, $this->system_encoding, $this->client_encoding) = $this->_getDefaultLanguage();

        $this->config = $this->getConfig();
        $this->dsn = $this->_prepareDSN();
        $this->url = $this->config->get('url');

        // プラグインオブジェクトの用意
        $this->plugin = $this->getPlugin();

        //// assert (experimental)
        //if ($this->config->get('debug') === false) {
        //    ini_set('assert.active', 0);
        //}

        // ログ出力開始
        $this->logger = $this->getLogger();
        $this->plugin->setLogger($this->logger);
        $this->logger->begin();

        // Ethnaマネージャ設定
        $this->_activateEthnaManager();
    }

    /**
     *  (現在アクティブな)コントローラのインスタンスを返す
     *
     *  @access public
     *  @return object  Ethna_Controller    コントローラのインスタンス
     *  @static
     */
    static function &getInstance()
    {
        if (isset($GLOBALS['_Ethna_controller'])) {
            return $GLOBALS['_Ethna_controller'];
        } else {
            $_ret_object = null;
            return $_ret_object;
        }
    }

    /**
     *  アプリケーションIDを返す
     *
     *  @access public
     *  @return string  アプリケーションID
     */
    function getAppId()
    {
        return ucfirst(strtolower($this->appid));
    }

    /**
     *  アプリケーションIDをチェックする
     *
     *  @access public
     *  @param  string  $id     アプリケーションID
     *  @return mixed   true:OK Ethna_Error:NG
     *  @static
     */
    function &checkAppId($id)
    {
        $true = true;
        if (strcasecmp($id, 'ethna') === 0
            || strcasecmp($id, 'app') === 0) {
            return Ethna::raiseError("Application Id [$id] is reserved\n");
        }
        if (preg_match('/^[0-9a-zA-Z]+$/', $id) === 0) {
            return Ethna::raiseError(
                "Only Numeric(0-9) and Alphabetical(A-Z) is allowed for Application Id\n"
            );
        }
        return $true;
    }

    /**
     *  アクション名をチェックする
     *
     *  @access public
     *  @param  string  $action_name    アクション名
     *  @return mixed   true:OK Ethna_Error:NG
     *  @static
     */
    function &checkActionName($action_name)
    {
        $true = true;
        if (preg_match('/^[a-zA-Z\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*/',
                       $action_name) === 0) {
            return Ethna::raiseError("invalid action name [$action_name]");
        }
        return $true;
    }

    /**
     *  ビュー名をチェックする
     *
     *  @access public
     *  @param  string  $view_name    ビュー名
     *  @return mixed   true:OK Ethna_Error:NG
     *  @static
     */
    function &checkViewName($view_name)
    {
        $true = true;
        if (preg_match('/^[a-zA-Z\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*/',
                       $view_name) === 0) {
            return Ethna::raiseError("invalid view name [$view_name]");
        }
        return $true;
    }

    /**
     *  DSNを返す
     *
     *  @access public
     *  @param  string  $db_key DBキー
     *  @return string  DSN
     */
    function getDSN($db_key = "")
    {
        if (isset($this->dsn[$db_key]) == false) {
            return null;
        }
        return $this->dsn[$db_key];
    }

    /**
     *  DSNの持続接続設定を返す
     *
     *  @access public
     *  @param  string  $db_key DBキー
     *  @return bool    true:persistent false:non-persistent(あるいは設定無し)
     */
    function getDSN_persistent($db_key = "")
    {
        $key = sprintf("dsn%s_persistent", $db_key == "" ? "" : "_$db_key");

        $dsn_persistent = $this->config->get($key);
        if (is_null($dsn_persistent)) {
            return false;
        }
        return $dsn_persistent;
    }

    /**
     *  DB設定を返す
     *
     *  @access public
     *  @param  string  $db_key DBキー("", "r", "rw", "default", "blog_r"...)
     *  @return string  $db_keyに対応するDB種別定義(設定が無い場合はnull)
     */
    function getDBType($db_key = null)
    {
        if (is_null($db_key)) {
            // 一覧を返す
            return $this->db;
        }

        if (isset($this->db[$db_key]) == false) {
            return null;
        }
        return $this->db[$db_key];
    }

    /**
     *  アプリケーションベースURLを返す
     *
     *  @access public
     *  @return string  アプリケーションベースURL
     */
    function getURL()
    {
        return $this->url;
    }

    /**
     *  アプリケーションベースディレクトリを返す
     *
     *  @access public
     *  @return string  アプリケーションベースディレクトリ
     */
    function getBasedir()
    {
        return $this->base;
    }

    /**
     *  クライアントタイプ/言語からテンプレートディレクトリ名を決定する
     *
     *  @access public
     *  @return string  テンプレートディレクトリ
     */
    function getTemplatedir()
    {
        $template = $this->getDirectory('template');

        // 言語別ディレクトリ
        if (file_exists($template . '/' . $this->language)) {
            $template .= '/' . $this->language;
        }

        return $template;
    }

    /**
     *  アクションディレクトリ名を決定する
     *
     *  @access public
     *  @return string  アクションディレクトリ
     */
    function getActiondir($gateway = null)
    {
        $key = 'action';
        $gateway = is_null($gateway) ? $this->getGateway() : $gateway;
        switch ($gateway) {
        case GATEWAY_WWW:
            $key = 'action';
            break;
        case GATEWAY_CLI:
            $key = 'action_cli';
            break;
        case GATEWAY_XMLRPC:
            $key = 'action_xmlrpc';
            break;
        }

        return (empty($this->directory[$key]) ? ($this->base . (empty($this->base) ? '' : '/')) : ($this->directory[$key] . "/"));
    }

    /**
     *  ビューディレクトリ名を決定する
     *
     *  @access public
     *  @return string  アクションディレクトリ
     */
    function getViewdir()
    {
        return (empty($this->directory['view']) ? ($this->base . (empty($this->base) ? '' : '/')) : ($this->directory['view'] . "/"));
    }

    /**
     *  アプリケーションディレクトリ設定を返す
     *
     *  @access public
     *  @param  string  $key    ディレクトリタイプ("tmp", "template"...)
     *  @return string  $keyに対応したアプリケーションディレクトリ(設定が無い場合はnull)
     */
    function getDirectory($key)
    {
        // for B.C.
        if ($key == 'app' && isset($this->directory[$key]) == false) {
            return BASE . '/app';
        }

        if (isset($this->directory[$key]) == false) {
            return null;
        }
        return $this->directory[$key];
    }

    /**
     *  アプリケーション拡張子設定を返す
     *
     *  @access public
     *  @param  string  $key    拡張子タイプ("php", "tpl"...)
     *  @return string  $keyに対応した拡張子(設定が無い場合はnull)
     */
    function getExt($key)
    {
        if (isset($this->ext[$key]) == false) {
            return null;
        }
        return $this->ext[$key];
    }

    /**
     *  クラスファクトリオブジェクトのアクセサ(R)
     *
     *  @access public
     *  @return object  Ethna_ClassFactory  クラスファクトリオブジェクト
     */
    function &getClassFactory()
    {
        return $this->class_factory;
    }

    /**
     *  アクションエラーオブジェクトのアクセサ
     *
     *  @access public
     *  @return object  Ethna_ActionError   アクションエラーオブジェクト
     */
    function &getActionError()
    {
        return $this->class_factory->getObject('error');
    }

    /**
     *  アクションフォームオブジェクトのアクセサ
     *
     *  @access public
     *  @return object  Ethna_ActionForm    アクションフォームオブジェクト
     */
    function &getActionForm()
    {
        // 明示的にクラスファクトリを利用していない
        return $this->action_form;
    }

    /**
     *  ビューオブジェクトのアクセサ
     *
     *  @access public
     *  @return object  Ethna_View          ビューオブジェクト
     */
    function &getView()
    {
        // 明示的にクラスファクトリを利用していない
        return $this->view;
    }

    /**
     *  backendオブジェクトのアクセサ
     *
     *  @access public
     *  @return object  Ethna_Backend   backendオブジェクト
     */
    function &getBackend()
    {
        return $this->class_factory->getObject('backend');
    }

    /**
     *  設定オブジェクトのアクセサ
     *
     *  @access public
     *  @return object  Ethna_Config    設定オブジェクト
     */
    function &getConfig()
    {
        return $this->class_factory->getObject('config');
    }

    /**
     *  i18nオブジェクトのアクセサ(R)
     *
     *  @access public
     *  @return object  Ethna_I18N  i18nオブジェクト
     */
    function &getI18N()
    {
        return $this->class_factory->getObject('i18n');
    }

    /**
     *  ログオブジェクトのアクセサ
     *
     *  @access public
     *  @return object  Ethna_Logger        ログオブジェクト
     */
    function &getLogger()
    {
        return $this->class_factory->getObject('logger');
    }

    /**
     *  セッションオブジェクトのアクセサ
     *
     *  @access public
     *  @return object  Ethna_Session       セッションオブジェクト
     */
    function &getSession()
    {
        return $this->class_factory->getObject('session');
    }

    /**
     *  SQLオブジェクトのアクセサ
     *
     *  @access public
     *  @return object  Ethna_AppSQL    SQLオブジェクト
     */
    function &getSQL()
    {
        return $this->class_factory->getObject('sql');
    }

    /**
     *  プラグインオブジェクトのアクセサ
     *
     *  @access public
     *  @return object  Ethna_Plugin    プラグインオブジェクト
     */
    function &getPlugin()
    {
        return $this->class_factory->getObject('plugin');
    }

    /**
     *  URLハンドラオブジェクトのアクセサ
     *
     *  @access public
     *  @return object  Ethna_UrlHandler    URLハンドラオブジェクト
     */
    function &getUrlHandler()
    {
        return $this->class_factory->getObject('url_handler');
    }

    /**
     *  マネージャ一覧を返す
     *
     *  @access public
     *  @return array   マネージャ一覧
     *  @obsolete
     */
    function getManagerList()
    {
        return $this->manager;
    }

    /**
     *  実行中のアクション名を返す
     *
     *  @access public
     *  @return string  実行中のアクション名
     */
    function getCurrentActionName()
    {
        return $this->action_name;
    }

    /**
     *  実行中のXMLRPCメソッド名を返す
     *
     *  @access public
     *  @return string  実行中のXMLRPCメソッド名
     */
    function getXmlrpcMethodName()
    {
        return $this->xmlrpc_method_name;
    }

    /**
     *  使用言語を取得する
     *
     *  @access public
     *  @return array   使用言語,システムエンコーディング名,クライアントエンコーディング名
     */
    function getLanguage()
    {
        return array($this->language, $this->system_encoding, $this->client_encoding);
    }

    /**
     *  ゲートウェイを取得する
     *
     *  @access public
     */
    function getGateway()
    {
        return $this->gateway;
    }

    /**
     *  ゲートウェイモードを設定する
     *
     *  @access public
     */
    function setGateway($gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     *  アプリケーションのエントリポイント
     *
     *  @access public
     *  @param  string  $class_name     アプリケーションコントローラのクラス名
     *  @param  mixed   $action_name    指定のアクション名(省略可)
     *  @param  mixed   $fallback_action_name   アクションが決定できなかった場合に実行されるアクション名(省略可)
     *  @static
     */
    function main($class_name, $action_name = "", $fallback_action_name = "")
    {
        $c = new $class_name;
        $c->trigger($action_name, $fallback_action_name);
    }

    /**
     *  CLIアプリケーションのエントリポイント
     *
     *  @access public
     *  @param  string  $class_name     アプリケーションコントローラのクラス名
     *  @param  string  $action_name    実行するアクション名
     *  @param  bool    $enable_filter  フィルタチェインを有効にするかどうか
     *  @static
     */
    function main_CLI($class_name, $action_name, $enable_filter = true)
    {
        $c = new $class_name(GATEWAY_CLI);
        $c->action_cli[$action_name] = array();
        $c->trigger($action_name, "", $enable_filter);
    }

    /**
     *  XMLRPCアプリケーションのエントリポイント
     *
     *  @access public
     *  @static
     */
    function main_XMLRPC($class_name)
    {
        if (extension_loaded('xmlrpc') == false) {
            die("xmlrpc extension is required to enable this gateway");
        }

        $c = new $class_name(GATEWAY_XMLRPC);
        $c->trigger("", "", false);
    }

    /**
     *  SOAPアプリケーションのエントリポイント
     *
     *  @access public
     *  @param  string  $class_name     アプリケーションコントローラのクラス名
     *  @param  mixed   $action_name    指定のアクション名(省略可)
     *  @param  mixed   $fallback_action_name   アクションが決定できなかった場合に実行されるアクション名(省略可)
     *  @static
     */
    function main_SOAP($class_name, $action_name = "", $fallback_action_name = "")
    {
        $c = new $class_name(GATEWAY_SOAP);
        $c->trigger($action_name, $fallback_action_name);
    }

    /**
     *  フレームワークの処理を開始する
     *
     *  @access public
     *  @param  mixed   $default_action_name    指定のアクション名
     *  @param  mixed   $fallback_action_name   アクション名が決定できなかった場合に実行されるアクション名
     *  @param  bool    $enable_filter  フィルタチェインを有効にするかどうか
     *  @return mixed   0:正常終了 Ethna_Error:エラー
     */
    function trigger($default_action_name = "", $fallback_action_name = "", $enable_filter = true)
    {
        // フィルターの生成
        if ($enable_filter) {
            $this->_createFilterChain();
        }

        // 実行前フィルタ
        for ($i = 0; $i < count($this->filter_chain); $i++) {
            $r = $this->filter_chain[$i]->preFilter();
            if (Ethna::isError($r)) {
                return $r;
            }
        }

        // trigger
        switch ($this->getGateway()) {
        case GATEWAY_WWW:
            $this->_trigger_WWW($default_action_name, $fallback_action_name);
            break;
        case GATEWAY_CLI:
            $this->_trigger_CLI($default_action_name);
            break;
        case GATEWAY_XMLRPC:
            $this->_trigger_XMLRPC();
            break;
        case GATEWAY_SOAP:
            $this->_trigger_SOAP();
            break;
        }

        // 実行後フィルタ
        for ($i = count($this->filter_chain) - 1; $i >= 0; $i--) {
            $r = $this->filter_chain[$i]->postFilter();
            if (Ethna::isError($r)) {
                return $r;
            }
        }
    }

    /**
     *  フレームワークの処理を実行する(WWW)
     *
     *  引数$default_action_nameに配列が指定された場合、その配列で指定された
     *  アクション以外は受け付けない(指定されていないアクションが指定された
     *  場合、配列の先頭で指定されたアクションが実行される)
     *
     *  @access private
     *  @param  mixed   $default_action_name    指定のアクション名
     *  @param  mixed   $fallback_action_name   アクション名が決定できなかった場合に実行されるアクション名
     *  @return mixed   0:正常終了 Ethna_Error:エラー
     */
    function _trigger_WWW($default_action_name = "", $fallback_action_name = "")
    {
        // アクション名の取得
        $action_name = $this->_getActionName($default_action_name, $fallback_action_name);

        // アクション定義の取得
        $action_obj = $this->_getAction($action_name);
        if (is_null($action_obj)) {
            if ($fallback_action_name != "") {
                $this->logger->log(LOG_DEBUG, 'undefined action [%s] -> try fallback action [%s]', $action_name, $fallback_action_name);
                $action_obj = $this->_getAction($fallback_action_name);
            }
            if (is_null($action_obj)) {
                return Ethna::raiseError("undefined action [%s]", E_APP_UNDEFINED_ACTION, $action_name);
            } else {
                $action_name = $fallback_action_name;
            }
        }

        // アクション実行前フィルタ
        for ($i = 0; $i < count($this->filter_chain); $i++) {
            $r = $this->filter_chain[$i]->preActionFilter($action_name);
            if ($r != null) {
                $this->logger->log(LOG_DEBUG, 'action [%s] -> [%s] by %s', $action_name, $r, get_class($this->filter_chain[$i]));
                $action_name = $r;
            }
        }
        $this->action_name = $action_name;

        // 言語設定
        $this->_setLanguage($this->language, $this->system_encoding, $this->client_encoding);

        // オブジェクト生成
        $backend = $this->getBackend();

        $form_name = $this->getActionFormName($action_name);
        $this->action_form = new $form_name($this);
        $this->action_form->setFormVars();

        // バックエンド処理実行
        $backend->setActionForm($this->action_form);

        $session = $this->getSession();
        $session->restore();
        $forward_name = $backend->perform($action_name);

        // アクション実行後フィルタ
        for ($i = count($this->filter_chain) - 1; $i >= 0; $i--) {
            $r = $this->filter_chain[$i]->postActionFilter($action_name, $forward_name);
            if ($r != null) {
                $this->logger->log(LOG_DEBUG, 'forward [%s] -> [%s] by %s', $forward_name, $r, get_class($this->filter_chain[$i]));
                $forward_name = $r;
            }
        }

        // コントローラで遷移先を決定する(オプション)
        $forward_name = $this->_sortForward($action_name, $forward_name);

        if ($forward_name != null) {
            $view_class_name = $this->getViewClassName($forward_name);
            $this->view = new $view_class_name($backend, $forward_name, $this->_getForwardPath($forward_name));
            $this->view->preforward();
            $this->view->forward();
        }

        return 0;
    }

    /**
     *  フレームワークの処理を実行する(CLI)
     *
     *  @access private
     *  @param  mixed   $default_action_name    指定のアクション名
     *  @return mixed   0:正常終了 Ethna_Error:エラー
     */
    function _trigger_CLI($default_action_name = "")
    {
        return $this->_trigger_WWW($default_action_name);
    }

    /**
     *  フレームワークの処理を実行する(XMLRPC)
     *
     *  @access private
     *  @param  mixed   $action_name    指定のアクション名
     *  @return mixed   0:正常終了 Ethna_Error:エラー
     */
    function _trigger_XMLRPC($action_name = "")
    {
        // prepare xmlrpc server
        $xmlrpc_gateway_method_name = "_Ethna_XmlrpcGateway";
        $xmlrpc_server = xmlrpc_server_create();

        $method = null;
        $param = xmlrpc_decode_request(file_get_contents('php://input'), $method);
        $this->xmlrpc_method_name = $method;

        $request = xmlrpc_encode_request(
            $xmlrpc_gateway_method_name,
            $param,
            array(
                'output_type'   => 'xml',
                'verbosity'     => 'pretty',
                'escaping'      => array('markup'),
                'version'       => 'xmlrpc',
                'encoding'      => 'utf-8'
            )
        ); 

        xmlrpc_server_register_method(
            $xmlrpc_server,
            $xmlrpc_gateway_method_name,
            $xmlrpc_gateway_method_name
        );

        // send request
        $r = xmlrpc_server_call_method(
            $xmlrpc_server,
            $request,
            null,
            array(
                'output_type'   => 'xml',
                'verbosity'     => 'pretty',
                'escaping'      => array('markup'),
                'version'       => 'xmlrpc',
                'encoding'      => 'utf-8'
            )
        );

        header('Content-Length: ' . strlen($r));
        header('Content-Type: text/xml; charset=UTF-8');
        print $r;
    }

    /**
     *  _trigger_XMLRPCのコールバックメソッド
     *
     *  @access public
     */
    function trigger_XMLRPC($method, $param)
    {
        // アクション定義の取得
        $action_obj = $this->_getAction($method);
        if (is_null($action_obj)) {
            return Ethna::raiseError("undefined xmlrpc method [%s]", E_APP_UNDEFINED_ACTION, $method);
        }

        // オブジェクト生成
        $backend = $this->getBackend();

        $form_name = $this->getActionFormName($method);
        $this->action_form = new $form_name($this);
        $def = $this->action_form->getDef();
        $n = 0;
        foreach ($def as $key => $value) {
            if (isset($param[$n]) == false) {
                $this->action_form->set($key, null);
            } else {
                $this->action_form->set($key, $param[$n]);
            }
            $n++;
        }

        // バックエンド処理実行
        $backend->setActionForm($this->action_form);

        $session = $this->getSession();
        $session->restore();
        $r = $backend->perform($method);

        return $r;
    }

    /**
     *  SOAPフレームワークの処理を実行する
     *
     *  @access private
     */
    function _trigger_SOAP()
    {
        // SOAPエントリクラス
        $gg = new Ethna_SOAP_GatewayGenerator();
        $script = $gg->generate();
        eval($script);

        // SOAPリクエスト処理
        $server = new SoapServer(null, array('uri' => $this->config->get('url')));
        $server->setClass($gg->getClassName());
        $server->handle();
    }

    /**
     *  エラーハンドラ
     *
     *  エラー発生時の追加処理を行いたい場合はこのメソッドをオーバーライドする
     *  (アラートメール送信等−デフォルトではログ出力時にアラートメール
     *  が送信されるが、エラー発生時に別にアラートメールをここで送信
     *  させることも可能)
     *
     *  @access public
     *  @param  object  Ethna_Error     エラーオブジェクト
     */
    function handleError(&$error)
    {
        // ログ出力
        list ($log_level, $dummy) = $this->logger->errorLevelToLogLevel($error->getLevel());
        $message = $error->getMessage();
        $this->logger->log($log_level, sprintf("%s [ERROR CODE(%d)]", $message, $error->getCode()));
    }

    /**
     *  エラーメッセージを取得する
     *
     *  @access public
     *  @param  int     $code       エラーコード
     *  @return string  エラーメッセージ
     */
    function getErrorMessage($code)
    {
        $message_list = $GLOBALS['_Ethna_error_message_list'];
        for ($i = count($message_list)-1; $i >= 0; $i--) {
            if (array_key_exists($code, $message_list[$i])) {
                return $message_list[$i][$code];
            }
        }
        return null;
    }

    /**
     *  実行するアクション名を返す
     *
     *  @access private
     *  @param  mixed   $default_action_name    指定のアクション名
     *  @return string  実行するアクション名
     */
    function _getActionName($default_action_name, $fallback_action_name)
    {
        // フォームから要求されたアクション名を取得する
        $form_action_name = $this->_getActionName_Form();
        $form_action_name = preg_replace('/[^a-z0-9\-_]+/i', '', $form_action_name);
        $this->logger->log(LOG_DEBUG, 'form_action_name[%s]', $form_action_name);

        // Ethnaマネージャへのフォームからのリクエストは拒否
        if ($form_action_name == "__ethna_info__" ||
            $form_action_name == "__ethna_unittest__") {
            $form_action_name = "";
        }

        // フォームからの指定が無い場合はエントリポイントに指定されたデフォルト値を利用する
        if ($form_action_name == "" && count($default_action_name) > 0) {
            $tmp = is_array($default_action_name) ? $default_action_name[0] : $default_action_name;
            if ($tmp[strlen($tmp)-1] == '*') {
                $tmp = substr($tmp, 0, -1);
            }
            $this->logger->log(LOG_DEBUG, '-> default_action_name[%s]', $tmp);
            $action_name = $tmp;
        } else {
            $action_name = $form_action_name;
        }

        // エントリポイントに配列が指定されている場合は指定以外のアクション名は拒否する
        if (is_array($default_action_name)) {
            if ($this->_isAcceptableActionName($action_name, $default_action_name) == false) {
                // 指定以外のアクション名で合った場合は$fallback_action_name(or デフォルト)
                $tmp = $fallback_action_name != "" ? $fallback_action_name : $default_action_name[0];
                if ($tmp[strlen($tmp)-1] == '*') {
                    $tmp = substr($tmp, 0, -1);
                }
                $this->logger->log(LOG_DEBUG, '-> fallback_action_name[%s]', $tmp);
                $action_name = $tmp;
            }
        }

        $this->logger->log(LOG_DEBUG, '<<< action_name[%s] >>>', $action_name);

        return $action_name;
    }

    /**
     *  フォームにより要求されたアクション名を返す
     *
     *  アプリケーションの性質に応じてこのメソッドをオーバーライドして下さい。
     *  デフォルトでは"action_"で始まるフォーム値の"action_"の部分を除いたもの
     *  ("action_sample"なら"sample")がアクション名として扱われます
     *
     *  @access protected
     *  @return string  フォームにより要求されたアクション名
     */
    function _getActionName_Form()
    {
        if (isset($_SERVER['REQUEST_METHOD']) == false) {
            return null;
        }

        $url_handler = $this->getUrlHandler();
        if ($_SERVER['REQUEST_METHOD'] == "GET") {
            $tmp_vars = $_GET;
        } else if ($_SERVER['REQUEST_METHOD'] == "POST") {
            $tmp_vars = $_POST;
        }

        if (empty($_SERVER['URL_HANDLER']) == false) {
            $tmp_vars['__url_handler__'] = $_SERVER['URL_HANDLER'];
            $tmp_vars['__url_info__'] = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : null;
            $tmp_vars = $url_handler->requestToAction($tmp_vars);

            if ($_SERVER['REQUEST_METHOD'] == "GET") {
                $_GET = array_merge($_GET, $tmp_vars);
            } else if ($_SERVER['REQUEST_METHOD'] == "POST") {
                $_POST = array_merge($_POST, $tmp_vars);
            }
            $_REQUEST = array_merge($_REQUEST, $tmp_vars);
        }

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') == 0) {
            $http_vars = $_POST;
        } else {
            $http_vars = $_GET;
        }

        // フォーム値からリクエストされたアクション名を取得する
        $action_name = $sub_action_name = null;
        foreach ($http_vars as $name => $value) {
            if ($value == "" || strncmp($name, 'action_', 7) != 0) {
                continue;
            }

            $tmp = substr($name, 7);

            // type="image"対応
            if (preg_match('/_x$/', $name) || preg_match('/_y$/', $name)) {
                $tmp = substr($tmp, 0, strlen($tmp)-2);
            }

            // value="dummy"となっているものは優先度を下げる
            if ($value == "dummy") {
                $sub_action_name = $tmp;
            } else {
                $action_name = $tmp;
            }
        }
        if ($action_name == null) {
            $action_name = $sub_action_name;
        }

        return $action_name;
    }

    /**
     *  アクション名を指定するクエリ/HTMLを生成する
     *
     *  @access public
     *  @param  string  $action action to request
     *  @param  string  $type   hidden, url...
     *  @todo   consider gateway
     */
    function getActionRequest($action, $type = "hidden")
    {
        $s = null; 
        if ($type == "hidden") {
            $s = sprintf('<input type="hidden" name="action_%s" value="true">', htmlspecialchars($action, ENT_QUOTES));
        } else if ($type == "url") {
            $s = sprintf('action_%s=true', urlencode($action));
        }
        return $s;
    }

    /**
     *  フォームにより要求されたアクション名に対応する定義を返す
     *
     *  @access private
     *  @param  string  $action_name    アクション名
     *  @return array   アクション定義
     */
    function &_getAction($action_name, $gateway = null)
    {
        $action = array();
        $gateway = is_null($gateway) ? $this->getGateway() : $gateway;
        switch ($gateway) {
        case GATEWAY_WWW:
            $action = $this->action;
            break;
        case GATEWAY_CLI:
            $action = $this->action_cli;
            break;
        case GATEWAY_XMLRPC:
            $action = $this->action_xmlrpc;
            break;
        }

        $action_obj = array();
        if (isset($action[$action_name])) {
            $action_obj = $action[$action_name];
            if (isset($action_obj['inspect']) && $action_obj['inspect']) {
                return $action_obj;
            }
        } else {
            $this->logger->log(LOG_DEBUG, "action [%s] is not defined -> try default", $action_name);
        }

        // アクションスクリプトのインクルード
        $this->_includeActionScript($action_obj, $action_name);

        // 省略値の補正
        if (isset($action_obj['class_name']) == false) {
            $action_obj['class_name'] = $this->getDefaultActionClass($action_name);
        }

        if (isset($action_obj['form_name']) == false) {
            $action_obj['form_name'] = $this->getDefaultFormClass($action_name);
        } else if (class_exists($action_obj['form_name']) == false) {
            // 明示指定されたフォームクラスが定義されていない場合は警告
            $this->logger->log(LOG_WARNING, 'stated form class is not defined [%s]', $action_obj['form_name']);
        }

        // 必要条件の確認
        if (class_exists($action_obj['class_name']) == false) {
            $this->logger->log(LOG_NOTICE, 'action class is not defined [%s]', $action_obj['class_name']);
            $_ret_object = null;
            return $_ret_object;
        }
        if (class_exists($action_obj['form_name']) == false) {
            // フォームクラスは未定義でも良い
            $class_name = $this->class_factory->getObjectName('form');
            $this->logger->log(LOG_DEBUG, 'form class is not defined [%s] -> falling back to default [%s]', $action_obj['form_name'], $class_name);
            $action_obj['form_name'] = $class_name;
        }

        $action_obj['inspect'] = true;
        $action[$action_name] = $action_obj;
        return $action[$action_name];
    }

    /**
     *  アクション名とアクションクラスからの戻り値に基づいて遷移先を決定する
     *
     *  @access protected
     *  @param  string  $action_name    アクション名
     *  @param  string  $retval         アクションクラスからの戻り値
     *  @return string  遷移先
     */
    function _sortForward($action_name, $retval)
    {
        return $retval;
    }

    /**
     *  フィルタチェインを生成する
     *
     *  @access private
     */
    function _createFilterChain()
    {
        $this->filter_chain = array();
        foreach ($this->filter as $filter) {
            //バージョン0.2.0以前のフィルタ群から探す
            $file = sprintf("%s/%s.%s", $this->getDirectory('filter'), $filter,$this->getExt('php'));
            if (file_exists($file)) {
                include_once $file;
                if (class_exists($filter)) {
                    $this->filter_chain[] = new $filter($this);
                }
            } else {  //プラグインから探す．
                $filter_plugin = $this->plugin->getPlugin('Filter', $filter);
                if (Ethna::isError($filter_plugin)) {
                    continue;
                }

                $this->filter_chain[] = $filter_plugin;
            }
        }
    }

    /**
     *  アクション名が実行許可されているものかどうかを返す
     *
     *  @access private
     *  @param  string  $action_name            リクエストされたアクション名
     *  @param  array   $default_action_name    許可されているアクション名
     *  @return bool    true:許可 false:不許可
     */
    function _isAcceptableActionName($action_name, $default_action_name)
    {
        foreach (to_array($default_action_name) as $name) {
            if ($action_name == $name) {
                return true;
            } else if ($name[strlen($name)-1] == '*') {
                if (strncmp($action_name, substr($name, 0, -1), strlen($name)-1) == 0) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     *  指定されたアクションのフォームクラス名を返す(オブジェクトの生成は行わない)
     *
     *  @access public
     *  @param  string  $action_name    アクション名
     *  @return string  アクションのフォームクラス名
     */
    function getActionFormName($action_name)
    {
        $action_obj = $this->_getAction($action_name);
        if (is_null($action_obj)) {
            return null;
        }

        return $action_obj['form_name'];
    }

    /**
     *  アクションに対応するフォームクラス名が省略された場合のデフォルトクラス名を返す
     *
     *  デフォルトでは[プロジェクトID]_Form_[アクション名]となるので好み応じてオーバライドする
     *
     *  @access public
     *  @param  string  $action_name    アクション名
     *  @return string  アクションフォーム名
     */
    function getDefaultFormClass($action_name, $gateway = null)
    {
        $gateway_prefix = $this->_getGatewayPrefix($gateway);

        $postfix = preg_replace_callback('/_(.)/', function($m){ return strtoupper($m[1]);}, ucfirst($action_name));
        $r = sprintf("%s_%sForm_%s", $this->getAppId(), $gateway_prefix ? $gateway_prefix . "_" : "", $postfix);
        $this->logger->log(LOG_DEBUG, "default action class [%s]", $r);

        return $r;
    }

    /**
     *  getDefaultFormClass()で取得したクラス名からアクション名を取得する
     *
     *  getDefaultFormClass()をオーバーライドした場合、こちらも合わせてオーバーライド
     *  することを推奨(必須ではない)
     *
     *  @access public
     *  @param  string  $class_name     フォームクラス名
     *  @return string  アクション名
     */
    function actionFormToName($class_name)
    {
        $prefix = sprintf("%s_Form_", $this->getAppId());
        if (preg_match("/$prefix(.*)/", $class_name, $match) == 0) {
            // 不明なクラス名
            return null;
        }
        $target = $match[1];

        $action_name = substr(preg_replace_callback('/([A-Z])/', function($m){ return '_' . strtolower($m[1]);}, $target), 1);

        return $action_name;
    }

    /**
     *  アクションに対応するフォームパス名が省略された場合のデフォルトパス名を返す
     *
     *  デフォルトでは_getDefaultActionPath()と同じ結果を返す(1ファイルに
     *  アクションクラスとフォームクラスが記述される)ので、好みに応じて
     *  オーバーライドする
     *
     *  @access public
     *  @param  string  $action_name    アクション名
     *  @return string  form classが定義されるスクリプトのパス名
     */
    function getDefaultFormPath($action_name)
    {
        return $this->getDefaultActionPath($action_name);
    }

    /**
     *  指定されたアクションのクラス名を返す(オブジェクトの生成は行わない)
     *
     *  @access public
     *  @param  string  $action_name    アクションの名称
     *  @return string  アクションのクラス名
     */
    function getActionClassName($action_name)
    {
        $action_obj = $this->_getAction($action_name);
        if ($action_obj == null) {
            return null;
        }

        return $action_obj['class_name'];
    }

    /**
     *  アクションに対応するアクションクラス名が省略された場合のデフォルトクラス名を返す
     *
     *  デフォルトでは[プロジェクトID]_Action_[アクション名]となるので好み応じてオーバライドする
     *
     *  @access public
     *  @param  string  $action_name    アクション名
     *  @return string  アクションクラス名
     */
    function getDefaultActionClass($action_name, $gateway = null)
    {
        $gateway_prefix = $this->_getGatewayPrefix($gateway);

        $postfix = preg_replace_callback('/_(.)/', function($m){ return strtoupper($m[1]);}, ucfirst($action_name));
        $r = sprintf("%s_%sAction_%s", $this->getAppId(), $gateway_prefix ? $gateway_prefix . "_" : "", $postfix);
        $this->logger->log(LOG_DEBUG, "default action class [%s]", $r);

        return $r;
    }

    /**
     *  getDefaultActionClass()で取得したクラス名からアクション名を取得する
     *
     *  getDefaultActionClass()をオーバーライドした場合、こちらも合わせてオーバーライド
     *  することを推奨(必須ではない)
     *
     *  @access public
     *  @param  string  $class_name     アクションクラス名
     *  @return string  アクション名
     */
    function actionClassToName($class_name)
    {
        $prefix = sprintf("%s_Action_", $this->getAppId());
        if (preg_match("/$prefix(.*)/", $class_name, $match) == 0) {
            // 不明なクラス名
            return null;
        }
        $target = $match[1];

        $action_name = substr(preg_replace_callback('/([A-Z])/', function($m){ return '_' . strtolower($m[1]);}, $target), 1);

        return $action_name;
    }

    /**
     *  アクションに対応するアクションパス名が省略された場合のデフォルトパス名を返す
     *
     *  デフォルトでは"foo_bar" -> "/Foo/Bar.php"となるので好み応じてオーバーライドする
     *
     *  @access public
     *  @param  string  $action_name    アクション名
     *  @return string  アクションクラスが定義されるスクリプトのパス名
     */
    function getDefaultActionPath($action_name)
    {
        $r = preg_replace_callback('/_(.)/', function($m){ return '/' . strtoupper($m[1]); }, ucfirst($action_name)) . '.' . $this->getExt('php');
        $this->logger->log(LOG_DEBUG, "default action path [%s]", $r);

        return $r;
    }

    /**
     *  指定された遷移名に対応するビュークラス名を返す(オブジェクトの生成は行わない)
     *
     *  @access public
     *  @param  string  $forward_name   遷移先の名称
     *  @return string  view classのクラス名
     */
    function getViewClassName($forward_name)
    {
        if ($forward_name == null) {
            return null;
        }

        if (isset($this->forward[$forward_name])) {
            $forward_obj = $this->forward[$forward_name];
        } else {
            $forward_obj = array();
        }

        if (isset($forward_obj['view_name'])) {
            $class_name = $forward_obj['view_name'];
            if (class_exists($class_name)) {
                return $class_name;
            }
        } else {
            $class_name = null;
        }

        // viewのインクルード
        $this->_includeViewScript($forward_obj, $forward_name);

        if (is_null($class_name) == false && class_exists($class_name)) {
            return $class_name;
        } else if (is_null($class_name) == false) {
            $this->logger->log(LOG_WARNING, 'stated view class is not defined [%s] -> try default', $class_name);
        }

        $class_name = $this->getDefaultViewClass($forward_name);
        if (class_exists($class_name)) {
            return $class_name;
        } else {
            $class_name = $this->class_factory->getObjectName('view');
            $this->logger->log(LOG_DEBUG, 'view class is not defined for [%s] -> use default [%s]', $forward_name, $class_name);
            return $class_name;
        }
    }

    /**
     *  遷移名に対応するビュークラス名が省略された場合のデフォルトクラス名を返す
     *
     *  デフォルトでは[プロジェクトID]_View_[遷移名]となるので好み応じてオーバライドする
     *
     *  @access public
     *  @param  string  $forward_name   forward名
     *  @return string  view classクラス名
     */
    function getDefaultViewClass($forward_name, $gateway = null)
    {
        $gateway_prefix = $this->_getGatewayPrefix($gateway);

        $postfix = preg_replace_callback('/_(.)/', function($m){ return strtoupper($m[1]); }, ucfirst($forward_name));
        $r = sprintf("%s_%sView_%s", $this->getAppId(), $gateway_prefix ? $gateway_prefix . "_" : "", $postfix);
        $this->logger->log(LOG_DEBUG, "default view class [%s]", $r);

        return $r;
    }

    /**
     *  遷移名に対応するビューパス名が省略された場合のデフォルトパス名を返す
     *
     *  デフォルトでは"foo_bar" -> "/Foo/Bar.php"となるので好み応じてオーバーライドする
     *
     *  @access public
     *  @param  string  $forward_name   forward名
     *  @return string  view classが定義されるスクリプトのパス名
     */
    function getDefaultViewPath($forward_name)
    {
        $r = preg_replace_callback('/_(.)/', function($m){ return '/' . strtoupper($m[1]); }, ucfirst($forward_name)). '.' . $this->getExt('php');
        $this->logger->log(LOG_DEBUG, "default view path [%s]", $r);

        return $r;
    }

    /**
     *  遷移名に対応するテンプレートパス名が省略された場合のデフォルトパス名を返す
     *
     *  デフォルトでは"foo_bar"というforward名が"foo/bar" + テンプレート拡張子となる
     *  ので好み応じてオーバライドする
     *
     *  @access public
     *  @param  string  $forward_name   forward名
     *  @return string  forwardパス名
     */
    function getDefaultForwardPath($forward_name)
    {
        return str_replace('_', '/', $forward_name) . '.' . $this->ext['tpl'];
    }
    
    /**
     *  テンプレートパス名から遷移名を取得する
     *
     *  getDefaultForwardPath()をオーバーライドした場合、こちらも合わせてオーバーライド
     *  することを推奨(必須ではない)
     *
     *  @access public
     *  @param  string  $forward_path   テンプレートパス名
     *  @return string  遷移名
     */
    function forwardPathToName($forward_path)
    {
        $forward_path = preg_replace('/^\/+/', '', $forward_path);
        $forward_path = preg_replace(sprintf('/\.%s$/', $this->getExt('tpl')), '', $forward_path);

        return str_replace('/', '_', $forward_path);
    }

    /**
     *  遷移名からテンプレートファイルのパス名を取得する
     *
     *  @access private
     *  @param  string  $forward_name   forward名
     *  @return string  テンプレートファイルのパス名
     */
    function _getForwardPath($forward_name)
    {
        $forward_obj = null;

        if (isset($this->forward[$forward_name]) == false) {
            // try default
            $this->forward[$forward_name] = array();
        }
        $forward_obj = $this->forward[$forward_name];
        if (isset($forward_obj['forward_path']) == false) {
            // 省略値補正
            $forward_obj['forward_path'] = $this->getDefaultForwardPath($forward_name);
        }

        return $forward_obj['forward_path'];
    }

    /**
     *  レンダラを取得する(getTemplateEngine()はそのうち廃止されgetRenderer()に統合される予定)
     *
     *  @access public
     *  @return object  Ethna_Renderer  レンダラオブジェクト
     */
    function &getRenderer()
    {
        $_ret_object = $this->getTemplateEngine();
        return $_ret_object;
    }

    /**
     *  テンプレートエンジン取得する
     *
     *  @access public
     *  @return object  Ethna_Renderer  レンダラオブジェクト
     *  @obsolete
     */
    function &getTemplateEngine()
    {
        if (is_object($this->renderer)) {
            return $this->renderer;
        }
        
        $this->renderer = $this->class_factory->getObject('renderer');
       
        // {{{ for B.C.
        if (strtolower(get_class($this->renderer)) == "ethna_renderer_smarty") {
            // user defined modifiers
            foreach ($this->smarty_modifier_plugin as $modifier) {
                $name = str_replace('smarty_modifier_', '', $modifier);
                $this->renderer->setPlugin($name,'modifier', $modifier);
            }

            // user defined functions
            foreach ($this->smarty_function_plugin as $function) {
                if (!is_array($function)) {
                    $name = str_replace('smarty_function_', '', $function);
                    $this->renderer->setPlugin($name, 'function', $function);
                } else {
                    $this->renderer->setPlugin($function[1], 'function', $function);
                }
            }

            // user defined blocks
            foreach ($this->smarty_block_plugin as $block) {
                if (!is_array($block)) {
                    $name = str_replace('smarty_block_', '', $block);
                    $this->renderer->setPlugin($name,'block', $block);
                } else {
                    $this->renderer->setPlugin($block[1],'block', $block);
                }
            }

            // user defined prefilters
            foreach ($this->smarty_prefilter_plugin as $prefilter) {
                if (!is_array($prefilter)) {
                    $name = str_replace('smarty_prefilter_', '', $prefilter);
                    $this->renderer->setPlugin($name,'prefilter', $prefilter);
                } else {
                    $this->renderer->setPlugin($prefilter[1],'prefilter', $prefilter);
                }
            }

            // user defined postfilters
            foreach ($this->smarty_postfilter_plugin as $postfilter) {
                if (!is_array($postfilter)) {
                    $name = str_replace('smarty_postfilter_', '', $postfilter);
                    $this->renderer->setPlugin($name,'postfilter', $postfilter);
                } else {
                    $this->renderer->setPlugin($postfilter[1],'postfilter', $postfilter);
                }
            }

            // user defined outputfilters
            foreach ($this->smarty_outputfilter_plugin as $outputfilter) {
                if (!is_array($outputfilter)) {
                    $name = str_replace('smarty_outputfilter_', '', $outputfilter);
                    $this->renderer->setPlugin($name,'outputfilter', $outputfilter);
                } else {
                    $this->renderer->setPlugin($outputfilter[1],'outputfilter', $outputfilter);
                }
            }
        }

        //テンプレートエンジンのデフォルトの設定
        $this->_setDefaultTemplateEngine($this->renderer);
        // }}}

        return $this->renderer;
    }
	function _setDefaultTemplateEngine(&$renderer)
	{
		$renderer->engine->left_delimiter = '{{';
		$renderer->engine->right_delimiter = '}}';
	}
	
    /**
     *  使用言語を設定する
     *
     *  将来への拡張のためのみに存在しています。現在は特にオーバーライドの必要はありません。
     *
     *  @access protected
     *  @param  string  $language           言語定義(LANG_JA, LANG_EN...)
     *  @param  string  $system_encoding    システムエンコーディング名
     *  @param  string  $client_encoding    クライアントエンコーディング
     */
    function _setLanguage($language, $system_encoding = null, $client_encoding = null)
    {
        $this->language = $language;
        $this->system_encoding = $system_encoding;
        $this->client_encoding = $client_encoding;

        $i18n = $this->getI18N();
        $i18n->setLanguage($language, $system_encoding, $client_encoding);
    }

    /**
     *  デフォルト状態での使用言語を取得する
     *
     *  @access protected
     *  @return array   使用言語,システムエンコーディング名,クライアントエンコーディング名
     */
    function _getDefaultLanguage()
    {
        return array(LANG_JA, null, null);
    }

    /**
     *  デフォルト状態でのゲートウェイを取得する
     *
     *  @access protected
     *  @return int     ゲートウェイ定義(GATEWAY_WWW, GATEWAY_CLI...)
     */
    function _getDefaultGateway($gateway)
    {
        if (is_null($GLOBALS['_Ethna_gateway']) == false) {
            return $GLOBALS['_Ethna_gateway'];
        }
        return GATEWAY_WWW;
    }

    /**
     *  ゲートウェイに対応したクラス名のプレフィクスを取得する
     *
     *  @access public
     *  @param  string  $gateway    ゲートウェイ
     *  @return string  ゲートウェイクラスプレフィクス
     */
    function _getGatewayPrefix($gateway = null)
    {
        $gateway = is_null($gateway) ? $this->getGateway() : $gateway;
        switch ($gateway) {
        case GATEWAY_WWW:
            $prefix = '';
            break;
        case GATEWAY_CLI:
            $prefix = 'Cli';
            break;
        case GATEWAY_XMLRPC:
            $prefix = 'Xmlrpc';
            break;
        default:
            $prefix = '';
            break;
        }

        return $prefix;
    }

    /**
     *  マネージャクラス名を取得する
     *
     *  @access public
     *  @param  string  $name   マネージャキー
     *  @return string  マネージャクラス名
     */
    function getManagerClassName($name)
    {
        return sprintf('%s_%sManager', $this->getAppId(), ucfirst($name));
    }

    /**
     *  アプリケーションオブジェクトクラス名を取得する
     *
     *  @access public
     *  @param  string  $name   アプリケーションオブジェクトキー
     *  @return string  マネージャクラス名
     */
    function getObjectClassName($name)
    {
        if (preg_match_all('/_(.)/', ucfirst($name), $m)){
            foreach ($m[1] as &$v) $v = strtoupper($v);
            $name = str_replace($m[0], $m[1], ucfirst($name));
        }
        return sprintf('%s_%s', $this->getAppId(), $name);
    }

    /**
     *  アクションスクリプトをインクルードする
     *
     *  ただし、インクルードしたファイルにクラスが正しく定義されているかどうかは保証しない
     *
     *  @access private
     *  @param  array   $action_obj     アクション定義
     *  @param  string  $action_name    アクション名
     */
    function _includeActionScript($action_obj, $action_name)
    {
        $class_path = $form_path = null;

        $action_dir = $this->getActiondir();

        // class_path属性チェック
        if (isset($action_obj['class_path'])) {
            // フルパス指定サポート
            $tmp_path = $action_obj['class_path'];
            if (Ethna_Util::isAbsolute($tmp_path) == false) {
                $tmp_path = $action_dir . $tmp_path;
            }

            if (file_exists($tmp_path) == false) {
                $this->logger->log(LOG_WARNING, 'class_path file not found [%s] -> try default', $tmp_path);
            } else {
                include_once $tmp_path;
                $class_path = $tmp_path;
            }
        }

        // デフォルトチェック
        if (is_null($class_path)) {
            $class_path = $this->getDefaultActionPath($action_name);
            if (file_exists($action_dir . $class_path)) {
                include_once $action_dir . $class_path;
            } else {
                $this->logger->log(LOG_DEBUG, 'default action file not found [%s] -> try all files', $class_path);
                $class_path = null;
            }
        }
        
        // 全ファイルインクルード
        if (is_null($class_path)) {
            $this->_includeDirectory($this->getActiondir());
            return;
        }

        // form_path属性チェック
        if (isset($action_obj['form_path'])) {
            // フルパス指定サポート
            $tmp_path = $action_obj['form_path'];
            if (Ethna_Util::isAbsolute($tmp_path) == false) {
                $tmp_path = $action_dir . $tmp_path;
            }

            if ($tmp_path == $class_path) {
                return;
            }
            if (file_exists($tmp_path) == false) {
                $this->logger->log(LOG_WARNING, 'form_path file not found [%s] -> try default', $tmp_path);
            } else {
                include_once $tmp_path;
                $form_path = $tmp_path;
            }
        }

        // デフォルトチェック
        if (is_null($form_path)) {
            $form_path = $this->getDefaultFormPath($action_name);
            if ($form_path == $class_path) {
                return;
            }
            if (file_exists($action_dir . $form_path)) {
                include_once $action_dir . $form_path;
            } else {
                $this->logger->log(LOG_DEBUG, 'default form file not found [%s] -> maybe falling back to default form class', $form_path);
            }
        }
    }

    /**
     *  ビュースクリプトをインクルードする
     *
     *  ただし、インクルードしたファイルにクラスが正しく定義されているかどうかは保証しない
     *
     *  @access private
     *  @param  array   $forward_obj    遷移定義
     *  @param  string  $forward_name   遷移名
     */
    function _includeViewScript($forward_obj, $forward_name)
    {
        $view_dir = $this->getViewdir();

        // view_path属性チェック
        if (isset($forward_obj['view_path'])) {
            // フルパス指定サポート
            $tmp_path = $forward_obj['view_path'];
            if (Ethna_Util::isAbsolute($tmp_path) == false) {
                $tmp_path = $view_dir . $tmp_path;
            }

            if (file_exists($tmp_path) == false) {
                $this->logger->log(LOG_WARNING, 'view_path file not found [%s] -> try default', $tmp_path);
            } else {
                include_once $tmp_path;
                return;
            }
        }

        // デフォルトチェック
        $view_path = $this->getDefaultViewPath($forward_name);
        if (file_exists($view_dir . $view_path)) {
            include_once $view_dir . $view_path;
            return;
        } else {
            $this->logger->log(LOG_DEBUG, 'default view file not found [%s]', $view_path);
            $view_path = null;
        }
    }

    /**
     *  ディレクトリ以下の全てのスクリプトをインクルードする
     *
     *  @access private
     */
    function _includeDirectory($dir)
    {
        $ext = "." . $this->ext['php'];
        $ext_len = strlen($ext);

        if (is_dir($dir) == false) {
            return;
        }

        $dh = opendir($dir);
        if ($dh) {
            while (($file = readdir($dh)) !== false) {
                if ($file != '.' && $file != '..' && is_dir("$dir/$file")) {
                    $this->_includeDirectory("$dir/$file");
                }
                if (substr($file, -$ext_len, $ext_len) != $ext) {
                    continue;
                }
                include_once $dir . '/' . $file;
            }
        }
        closedir($dh);
    }

    /**
     *  設定ファイルのDSN定義から使用するデータを再構築する(スレーブアクセス分岐等)
     *
     *  DSNの定義方法(デフォルト:設定ファイル)を変えたい場合はここをオーバーライドする
     *
     *  @access protected
     *  @return array   DSN定義(array('DBキー1' => 'dsn1', 'DBキー2' => 'dsn2', ...))
     */
    function _prepareDSN()
    {
        $r = array();

        foreach ($this->db as $key => $value) {
            $config_key = "dsn";
            if ($key != "") {
                $config_key .= "_$key";
            }
            $dsn = $this->config->get($config_key);
            if (is_array($dsn)) {
                // 種別1つにつき複数DSNが定義されている場合はアクセス分岐
                $dsn = $this->_selectDSN($key, $dsn);
            }
            $r[$key] = $dsn;
        }
        return $r;
    }

    /**
     *  DSNのアクセス分岐を行う
     *  
     *  スレーブサーバへの振分け処理(デフォルト:ランダム)を変更したい場合はこのメソッドをオーバーライドする
     *
     *  @access protected
     *  @param  string  $type       DB種別
     *  @param  array   $dsn_list   DSN一覧
     *  @return string  選択されたDSN
     */
    function _selectDSN($type, $dsn_list)
    {
        if (is_array($dsn_list) == false) {
            return $dsn_list;
        }

        // デフォルト:ランダム
        list($usec, $sec) = explode(' ', microtime());
        mt_srand($sec + ((float) $usec * 100000));
        $n = mt_rand(0, count($dsn_list)-1);
        
        return $dsn_list[$n];
    }

    /**
     *  Ethnaマネージャを設定する
     *
     *  不要な場合は空のメソッドとしてオーバーライドしてもよい
     *
     *  @access protected
     */
    function _activateEthnaManager()
    {
        if ($this->config->get('debug') == false) {
            return;
        }

        require_once ETHNA_BASE . '/class/Ethna_InfoManager.php';
        
        // see if we have simpletest
        if (file_exists_ex('simpletest/unit_tester.php', true)) {
            require_once ETHNA_BASE . '/class/Ethna_UnitTestManager.php';
        }

        // action設定
        $this->action['__ethna_info__'] = array(
            'form_name' =>  'Ethna_Form_Info',
            'form_path' =>  sprintf('%s/class/Action/Ethna_Action_Info.php', ETHNA_BASE),
            'class_name' => 'Ethna_Action_Info',
            'class_path' => sprintf('%s/class/Action/Ethna_Action_Info.php', ETHNA_BASE),
        );

        // forward設定
        $this->forward['__ethna_info__'] = array(
            'forward_path'  => sprintf('%s/tpl/info.tpl', ETHNA_BASE),
            'view_name'     => 'Ethna_View_Info',
            'view_path'     => sprintf('%s/class/View/Ethna_View_Info.php', ETHNA_BASE),
        );
        
        
        // action設定
        $this->action['__ethna_unittest__'] = array(
            'form_name' =>  'Ethna_Form_UnitTest',
            'form_path' =>  sprintf('%s/class/Action/Ethna_Action_UnitTest.php', ETHNA_BASE),
            'class_name' => 'Ethna_Action_UnitTest',
            'class_path' => sprintf('%s/class/Action/Ethna_Action_UnitTest.php', ETHNA_BASE),
        );

        // forward設定
        $this->forward['__ethna_unittest__'] = array(
            'forward_path'  => sprintf('%s/tpl/unittest.tpl', ETHNA_BASE),
            'view_name'     => 'Ethna_View_UnitTest',
            'view_path'     => sprintf('%s/class/View/Ethna_View_UnitTest.php', ETHNA_BASE),
        );

    }

    /**
     *  CLI実行中フラグを取得する
     *
     *  @access public
     *  @return bool    CLI実行中フラグ
     *  @obsolete
     */
    function getCLI()
    {
        return $this->gateway == GATEWAY_CLI ? true : false;
    }

    /**
     *  CLI実行中フラグを設定する
     *
     *  @access public
     *  @param  bool    CLI実行中フラグ
     *  @obsolete
     */
    function setCLI($cli)
    {
        $this->gateway = $cli ? GATEWAY_CLI : $this->_getDefaultGateway();
    }
}
// }}}

/**
 *  XMLRPCゲートウェイのスタブクラス
 *
 *  @access     public
 */
function _Ethna_XmlrpcGateway($method_stub, $param)
{
    $ctl = Ethna_Controller::getInstance();
    $method = $ctl->getXmlrpcMethodName();
    $r = $ctl->trigger_XMLRPC($method, $param);
    if (Ethna::isError($r)) {
        return array(
            'faultCode' => $r->getCode(),
            'faultString' => $r->getMessage(),
        );
    }
    return $r;
}
?>
