<?php
// vim: foldmethod=marker
/**
 *  Afw_ActionClass.php
 *
 *  @author     {$author}
 *  @package    Afw
 */


// {{{ Afw_ActionClass
/**
 *  action実行クラス
 *
 *  @author     {$author}
 *  @package    Afw
 *  @access     public
 */
class Afw_ActionClass extends Ethna_ActionClass
{
	/** @var Afw_UtilsManager */
	var $utils;
	/** @var Afw_SettingsManager */
	var $settings;
	var $langs;
	/** @var Afw_PlantsManager */
	var $plants;
	/** @var Afw_SakurasManager */
	var $sakuras;
	
	/**
	 * コンストラクタ
	 */
	function Afw_ActionClass(&$backend)
	{
		// オブジェクトの生成
		$this->utils = $backend->getManager('utils');
		$this->settings = $backend->getManager('settings');
		$this->mails = $backend->getManager('mails');
		
		$this->plants = $backend->getManager('plants');
		$this->sakuras = $backend->getManager('sakuras');
		$this->auths = $backend->getManager('auths');
		
		// SmartyActionの追加
        $c = $backend->getController();
        $r = $c->getRenderer();
        $r->setPlugin('yyy', 'function', array($this, 'AfwSmartyAction')); // yyyは自由に変更してください。
        $r->setPlugin('lang', 'modifier', array($this, 'AfwSmartyModifierLang'));
        $r->setPlugin('pager', 'function', array($this, 'AfwSmartyFunctionPager'));
        $r->setPlugin('order', 'modifier', array($this, 'AfwSmartyModifierOrder'));
        $r->setPlugin('yymm', 'modifier', array($this, 'AfwSmartyModifierYYMM'));
        $r->setPlugin('file', 'function', array($this, 'AfwSmartyFunctionFile'));
        $r->setPlugin('is_manager', 'modifier', array($this, 'isManager'));
        $r->setPlugin('is_sm', 'modifier', array($this, 'isSmartPhone'));
        $r->setPlugin('script', 'block', array($this, 'AfwSmartyBlockScript'));
        
        // 初期フォルダの作成
        if (!file_exists(BASE . '/log')) {
        	@mkdir(BASE . '/log');
        	@chmod(BASE . '/log', 0777);
        }
        
		return parent::__construct($backend);
	}
	
    /**
     *  アクション実行前の認証処理を行う
     *
     *  @access public
     *  @return string  遷移名(nullなら正常終了, falseなら処理終了)
     */
	function authenticate()
	{
        // URLがconfigurlではじまっていないときはリダイレクト
        if (strpos($this->config->get('url'), @$_SERVER['HTTP_HOST']) === false) {
        	// $this->redirect();
        }
        
		// WKWebView対応でCookieを強制発行する
		if ($_REQUEST['user_token_login']) {
			$user = $this->users->getUserByLogin($_REQUEST['user_token_login']);
			$this->users->setSession($user['user_id']);
		}
		
		$settings = $this->settings->get();
		foreach ((array)$this->config->get('config_keys') as $key) {
			if (!isset($settings[$key])) {
				$settings[$key] = $this->config->get($key);
			}
		}
		$this->af->setApp('settings', $settings);
		
		if (strpos($_SERVER['SERVER_NAME'], 'localhost') !== false || strpos($_SERVER['SERVER_NAME'], '192.168.') !== false) {
			$this->af->setApp('is_local', true);
		}
		
		/*
        // 言語
		if ($this->session->get('lang')) {
			$langs = array();
			if (file_exists(BASE . '/lang/' . $this->session->get('lang') . '.php')) {
				require BASE . '/lang/' . $this->session->get('lang') . '.php';
				$this->langs = $langs;
			}
		}
		*/
		
		$actionName = $this->backend->getController()->getCurrentActionName();
		
		// ユーザー別の認証
		if ((strpos($actionName, 'authenticate') === 0 || ($this->config->get('is_test') && strpos($actionName, 'api') !== 0))
			&& $actionName != 'stripe_subscription_update_accept'
		) {
			$basicAuthors = $this->config->get('basic_authors');
			if ($_SERVER["PHP_AUTH_USER"] && $_SERVER["PHP_AUTH_PW"]) {
				if ($basicAuthors[$_SERVER["PHP_AUTH_USER"]] != $_SERVER["PHP_AUTH_PW"]) {
					header("WWW-Authenticate: Basic realm=\"Enter your account.\"");
					header("HTTP/1.0 401 Unauthorized");
					//キャンセル時の表示
					echo "Authorization Required";
					exit;
				}
			} else {
				header("WWW-Authenticate: Basic realm=\"Enter your account.\"");
				header("HTTP/1.0 401 Unauthorized");
				//キャンセル時の表示
				echo "Authorization Required";
				exit;
			}			
		}
		
		// セッションにユーザーIDがあるけれどもauthが仮登録ユーザーの場合は強制的にユーザーフォームへ飛ばす
		if ($this->session->get('user_id') && $this->session->get('user_auth') == $this->config->get('user_auth_first')) {
			if (strpos($actionName, 'user_form') === false
				&& strpos($actionName, 'user_modal') === false
				&& strpos($actionName, 'ajax_user') === false
				&& strpos($actionName, 'sign_out') === false
				&& strpos($actionName, 'upload_accept') === false
				&& strpos($actionName, 'css_common_view') === false
			) {
				return $this->backend->perform('user_form');
			}
		}
		
		if ($this->session->get('user_id')) {
			// 有料会員
			$this->app('is_expire', $this->plants->isExpire());
			// ポイント
			$this->app('user_point', (int)$this->plants->getUserPointByUserId($this->session->get('user_id')));
			// セッショントークン(パスワード文字列をさらにsalt)
			$this->app('session_token', $this->utils->getMd5($this->session->get('password_md5') . $this->config->get('md5_salt')));
		}
		
		return parent::authenticate();
    }

    /**
     *  アクション実行前の処理(フォーム値チェック等)を行う
     *
     *  @access public
     *  @return string  遷移名(nullなら正常終了, falseなら処理終了)
     */
	function prepare()
	{
		return parent::prepare();
	}

    /**
     *  アクション実行
     *
     *  @access public
     *  @return string  遷移名(nullなら遷移は行わない)
     */
	function perform()
	{
		return parent::perform();
	}
	
    /**
     * 指定した名称のSmarty変数に値を代入
     * 
     * @param	string	$formname
     * @param	array	&$array
     * @return	array
     */
    function app($formname, $array)
    {
    	$this->af->setApp($formname, $array);
    	return $array;
    }
    
    /**
     * 指定した名称のSmarty変数にカウント値を代入(0:合計, 1:配列)
     * 
     * @param	string	$formname
     * @param	array	&$array
     * @return	array
     */
    function appcount($formcountname, $formarrayname, $array)
    {
    	$this->af->setApp($formcountname, $array[0]);
    	$this->af->setApp($formarrayname, $array[1]);
    	return $array;
    }
    
    /**
     * デフォルト値を設定
     * 
     * @param	mixed	$source
     * @param	mixed	$initial
     * @return	mixed
     */
    function initval($source, $initial = 0)
    {
    	return ($source)?$source:$initial;
    }
    
    /**
     * ページのデフォルト値
     */
    function setPage()
    {
    	if (!$this->af->get('p')) {
    		$this->af->set('p', 1);
    	}
    }
    
    // limitのデフォルト値
    function setLimit()
    {
    	if (!$this->af->get('limit')) {
    		$this->af->set('limit', $this->config->get('default_limit'));
    	}
    }
    
    /**
     * ページのオフセット値
     * 
     * @param	int	$per	ページあたりの表示数
     */
    function getOffset($per)
    {
		return (($this->af->get('p') - 1) * $per);
    }
    
    /**
     * ログ書き出し
     * 
     * @param	string	$string
     * @param	string	$directory
     * @return
     */
    function writeLog($string = '', $directory = null)
    {
    	// ログ整形
    	$log = date('Y-m-d H:i:s') . "\t" . $this->utils->getHost() . ($this->session->get('user_id') ? ("\t" . $this->session->get('user_login')) : '') . "\t" . $string . "\n";
    	if (is_null($directory) && $this->config->get('log_directory')) {
			$directory = $this->config->get('log_directory');
		}
		if ($directory) {
			$fh = fopen($directory . date('Ymd') . '.log', 'a');
			fputs($fh, $log);
			fclose($fh);
			@chmod($directory . date('Ymd') . '.log', 0777);
		}
    }
	
   	/**
	 * 403エラー
	 */
	function denied()
	{
		header('HTTP/1.0 403 denied');
		exit;
	}

	/**
	 * リダイレクト
	 * 
	 * @param	string	$url	(config('url')以降のアドレス)
	 */
	function redirect($url = '', $isDomainInclude = false)
	{
		if ($isDomainInclude) {
			header(sprintf('Location: %s', $url));
		} else {
			header(sprintf('Location: %s%s', $this->config->get('url'), $url));
		}
		exit;
	}

    
	/**
	 * 読み込んだデータをアクションフォームに格納(編集フォーム用)
	 * 
	 * @param	array	$params
	 * @return	null
	 */
	function setForm($array)
	{
		if (is_array($array)) {
			foreach ($array as $k => $v) {
				$this->af->set($k, $v);
			}
		}
		
		return $array;
	}
	
	/**
	 * リファラチェック
	 * 
	 */
	function isReferer()
	{
		// リファラチェック
		if (strpos($_SERVER['HTTP_REFERER'], $this->config->get('url')) === false) {
			return false;
		}
		return true;
	}

	/**
	 * URLHandlerのパス登録されているURLかチェックする
	 */
	function isUrl($string)
	{
		$url = $this->backend->getController()->getUrlHandler()->action_map[$_SERVER['URL_HANDLER']];
		
		foreach ((array)$url as $k => $v) {
			if (isset($v['path']) && $v['path'] == $string) {
				// 単純パスがある場合
				return true;
			} elseif (isset($v['path_regexp'])) {
				if (is_array($v['path_regexp'])) {
					// 正規表現が複数ある場合
					foreach ((array)$v['path_regexp'] as $vv) {
						$path = explode('/', str_replace(array('|^'), '', $vv));
						if ($path[0] == $string) {
							return true;
						}						
					}
				} else {
					// 正規表現が1つの場合
					$path = explode('/', str_replace(array('|^'), '', $v['path_regexp']));
					if ($path[0] == $string) {
						return true;
					}
				}
			}
		}
		return false;
	}
	
	/**
	 * Smarty Action
	 * 
	 * @param	array	$param
	 * @param	object	$smarty
	 * @return	null
	 */
	function AfwSmartyAction($param, &$smarty)
	{
		// パラメータをActionFormに登録
		if (is_array($param)) {
			foreach ($param as $k => $v) {
				$this->af->set($k, $v);
			}
		}
		
		// View を実行(Ethna_Controllerからコピー)
		$c = $this->backend->getController();
		$forwardName = $this->backend->perform($param['action']);
		$viewClassName = $c->getViewClassName($forwardName);
		$this->view = new $viewClassName($this->backend, $forwardName, $c->_getForwardPath($forwardName));
		$this->view->preforward();
		if ($param['is_capture']) {
	        $renderer =& $this->view->_getRenderer();
	        $this->view->_setDefault($renderer);
	        return $renderer->perform($c->_getForwardPath($forwardName), true);
		} else {
			$this->view->forward();
		}

		return null;
	}
	
	/**
	 * Smarty modifier pref
	 * 
	 * @param	int	$prefNumber
	 * @return	string
	 */
	function AfwSmartyModifierLang($string)
	{
		$params = array_slice(func_get_args(), 1);
		
		if (!isset($this->langs[$string])) {
			if ($params) {
				return vsprintf($string, $params);
			} else {
				return $string;
			}
		}
	
		if ($params) {
			return vsprintf($this->langs[$string], $params);
		} else {
			return $this->langs[$string];
		}
	}
	
	// API戻り値セット(error時は$paramsにエラーコード)
	public function json($isSuccess, $params = null)
	{
		if ($isSuccess) {
			$responses = array(
				'status' => 'success',
				'data' => $params,
			);
		} else {
			$responses = array(
				'status' => 'failed',
				'error_code' => (int)$params,
			);
		}
		if ($_GET['debug']) {
			echo '<pre>'; print_r($responses); echo '</pre>';
		} else {
			echo json_encode($responses);
		}
		exit;
	}

	public function respondAjaxError($message = null)
	{
		if ($this->af->getApp('is_chain')) {
			return false;
		}
		
		if ($message) {
			echo json_encode(array('error' => $message));
		} else {
			echo json_encode(array('success' => false, 'error' => implode("\n", $this->ae->getMessageList())));
		}
		exit;
	}
	
	public function respondAjaxSuccess($response = '')
	{
		if ($this->af->getApp('is_chain')) {
			return true;
		}
		
		echo json_encode(array('success' => true, 'response' => $response));
		exit;		
	}
	

	/**
	 * smarty function: pager()
	 * 
	 * @param	array	$param
	 * 	(
	 * 		'count' => 件数,
	 * 		'page' => 現在のページ,
	 * 		'limit' => ページあたりの件数,
	 * 		'script' => 実行するスクリプト(PAGEをページ番号に置換)
	 *  )
	 */
	function AfwSmartyFunctionPager($param, &$smarty)
	{
		$count = (int)$param['count'];
		$page = (int)$param['page'];
		$limit = (int)$param['limit'];
		$selectThreshold = isset($param['threshold']) ? (int)$param['threshold'] : 20;
		$isCounter = isset($param['counter']) ? $param['counter'] : true;
		$scriptAnchor = str_replace('PAGE', '$(this).attr("page")', $param['script']);
		$scriptSelect = str_replace('PAGE', '$(this).val()', $param['script']);
		$url = str_replace('PAGE', '%d', $param['url']);
		if (!$limit) {
			return false;
		}
		
		$beginNumber = ($page - 1) * $limit + 1;
		$endPage = ceil($count / $limit);
		$endNumber = ($page < $endPage ? ($beginNumber + $limit - 1) : $count);
		
		if ($endNumber) {
			$counter = $isCounter ? smarty_modifier_lang(sprintf('%d件中 %d 〜 %d件', $count, $beginNumber, $endNumber)) : '';
			$prefix = '<nav><ul class="pagination justify-content-end">';
			$firstlink = '<li class="page-item"><a href="%s" aria-label="Previous" class="a-exec-pager page-link" page="1"><span aria-hidden="true">&laquo;&laquo;</span></a></li>';
			$prevlink = '<li class="page-item"><a href="%s" aria-label="Previous" class="a-exec-pager page-link" page="%d"><span aria-hidden="true">&laquo;</span></a></li>';
			$pager = '<li class="page-item%s"><a href="%s" class="a-exec-pager page-link" page="%d">%d</a></li>';
			$option = '<option value="%d"%s>%d</option>';
			$nextlink = '<li class="page-item"><a href="%s" aria-label="Next" class="a-exec-pager page-link" page="%d"><span aria-hidden="true">&raquo;</span></a></li>';
			$endlink = '<li class="page-item"><a href="%s" aria-label="Next" class="a-exec-pager page-link" page="%d"><span aria-hidden="true">&raquo;&raquo;</span></a></li>';
			$postfix = '</ul></nav>';
	 		$clicker = '<script>'
					 . '$(function() { $(".a-exec-pager").off().on("click", function(e) { e.preventDefault(); %s; }); $(".select-exec-pager").off().on("change", function(e) { %s; }); })'
					 . '</script>';
			
			// HTML生成
			if ($scriptAnchor) {
				$html = sprintf($clicker, $scriptAnchor, $scriptSelect);
			} else {
				$html = '';
			}
			$html .= '<div class="row"><div class="col-4 margin-top-md">' . $counter . '</div>';
			
			if ($endPage > 1) {
				$html .= '<div class="col margin-right-lg">' . $prefix;
				if ($endPage > $selectThreshold) {
					$html .= '<li class="form-inline"><select class="form-control select-exec-pager">';
					for ($i = 1; $i <= $endPage; $i++) {
						$html .= sprintf($option, $i, ($i == $page ? ' selected="selected"' : ''), $i);
					}
					$html .= '</select> / ' . $endPage . '</li>';
				} else {
					if ($page > 2) {
						$html .= sprintf($firstlink, $url ? sprintf($url, 1): '#');				
					}
					if ($page > 1) {
						$html .= sprintf($prevlink, $url ? sprintf($url, $page - 1): '#', $page - 1);
					}
					for ($i = 1; $i <= $endPage; $i++) {
						$html .= sprintf($pager, ($i == $page ? ' active' : ''), $url ? sprintf($url, $i): '#', $i, $i);
					}
					if ($page < $endPage) {
						$html .= sprintf($nextlink, $url ? sprintf($url, $page + 1): '#', $page + 1);
					}
					if ($page < $endPage - 1) {
						$html .= sprintf($endlink, $url ? sprintf($url, $endPage): '#', $endPage);
					}
				}
				$html .= $postfix . '</div>';
			}
		}
		$html .= '</div>';			

		return $html;
	}
	
	function AfwSmartyModifierOrder($name/* 表示名 */, $key/* ORDERのキー */, $postfix/* class名のポストフィックス */ = '')
	{
		$desc = '_desc';
		
		if ($this->af->get('o') == $key) {
			$icon = '▲';
			$key .= $desc;
		} elseif ($this->af->get('o') == $key . $desc) {
			$icon = '▼';
		} elseif (!$this->af->get('o')) {
			// デフォルト
			$icon = '▲';
			$key .= $desc;
		} else {
			$icon = '';
		}
		
		$link = sprintf('<a href="#" class="a-order%s" order="%s">%s%s</a>',
			$postfix,
			$key,
			$name,
			$icon
		);
			
		return $link;
	}
	
	public function AfwSmartyModifierYYMM($date)
	{
		list($year, $month) = $this->utils->getYM($date);
		$str = '';
		if ($year > 0) {
			$str = $year . '年';
		}
		$str .= $month . 'ヶ月';
		return $str;
	}
	
	// 管理者チェック
	public function isManager()
	{
		if (in_array($this->session->get('user_auth'), (array)$this->config->get('user_auth_admins'))) {
			return true;
		}
		return false;
	}
	
	// 管理者ではない場合はリダイレクト
	public function requiredManager($url = null)
	{
		if (in_array($this->session->get('user_auth'), (array)$this->config->get('user_auth_admins'))) {
			return true;
		}
		$this->redirect($url);
	}
	
	// ログイン状態のチェック
	public function requiredLogin($url = null)
	{
		if ($this->session->get('user_id')) {
			return true;
		}
		
		$this->redirect($url);
	}
	
	public function isSmartPhone()
	{
		// see http://matsui89.com/etc/260/
		$ua = $_SERVER['HTTP_USER_AGENT'];
		if (
		 	(strpos($ua, 'iPhone') === false)
			&& (strpos($ua, 'iPad') === false)
			&& (strpos($ua, 'Android') === false)
		) {
			return false;
		}
		return true;
	}
	
	function AfwSmartyBlockScript($param = false, $content = '', &$smarty = array(), &$repeat = true)
	{
		if (!$repeat) {
			if ($this->config->get('is_js')) {
				require_once BASE . '/lib/jsmin/jsmin.php';
				$content = '<script>' . trim(JSMin::minify($content)) . '</script>';			
				return $content;
			} else {
				return '<script>' . $content . '</script>';
			}
		}
		
		return $repeat ? false : true;
	}
	
	function AfwSmartyFunctionFile($param, &$smarty)
	{
		$fileId = (int)$param['id'];
		$alt = (string)$param['alt'];
		$class = (string)$param['class'];
		$isFileOnly = (bool)$param['is_file_only'];
		if (!$fileId && !$alt) {
			return '';
		}
		
		$file = $this->plants->get('files', $fileId);
		if (!$file && $alt) {
			$file['server_filename'] = $alt;
			$file['file_ext'] = pathinfo($alt, PATHINFO_EXTENSION); 
		}
		
		if ($isFileOnly) {
			return $this->config->get('url') . $this->config->get('upload_url') . $file['server_filename'];
		}

		if (in_array($file['file_ext'], array('jpg', 'png', 'jpeg', 'gif'))) {
			return sprintf('<img src="%s%s%s" class="%s" />',
				$this->config->get('base'), $this->config->get('upload_url'), $file['server_filename'], $class
			);
		} elseif (in_array($file['file_ext'], array('mov', 'mp4', 'webm'))) {
			return sprintf('<video src="%s%s%s" class="%s" controls></video>',
				$this->config->get('base'), $this->config->get('upload_url'), $file['server_filename'], $class
			);
		}
	}
	
	function getView($name)
	{
		$views = $this->config->get('views');
		if ($views[$name]) {
			return $views[$name];
		} else {
			$this->redirect();
		}
	}
}
// }}}
