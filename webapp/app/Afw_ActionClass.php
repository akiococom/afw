<?php
// vim: foldmethod=marker
/**
 *  Afw_ActionClass.php
 *
 *  @author     akio.co.com
 *  @package    Afw
 */


// {{{ Afw_ActionClass
/**
 *  action実行クラス
 *
 *  @author     akio.co.com
 *  @package    Afw
 *  @access     public
 */
class Afw_ActionClass extends Ethna_ActionClass
{
	var $utils;
	var $settings;
	var $langs;
	
	/**
	 * コンストラクタ
	 */
	function Afw_ActionClass(&$backend)
	{
		// オブジェクトの生成
		$this->utils = $backend->getManager('utils');
		$this->settings = $backend->getManager('settings');
		$this->mails = $backend->getManager('mails');		
		
		// SmartyActionの追加
        $c = $backend->getController();
        $r = $c->getRenderer();
        $r->setPlugin('yyy', 'function', array($this, 'AfwSmartyAction')); // yyyは自由に変更してください。
        $r->setPlugin('lang', 'modifier', array($this, 'AfwSmartyModifierLang'));
        $r->setPlugin('pager', 'function', array($this, 'AfwSmartyFunctionPager'));
        $r->setPlugin('table', 'block', array($this, 'AfwSmartyBlockTable'));
        $r->setPlugin('order', 'modifier', array($this, 'AfwSmartyModifierOrder'));
        $r->setPlugin('yymm', 'modifier', array($this, 'AfwSmartyModifierYYMM'));
        $r->setPlugin('is_manager', 'modifier', array($this, 'isManager'));
        $r->setPlugin('is_sm', 'modifier', array($this, 'isSmartPhone'));
        
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
		if (strpos($actionName, 'authenticate') === 0) {
			$basicAuthors = $this->config->get('manage_authors');
			if (!empty($_SERVER["PHP_AUTH_USER"]) && !empty($_SERVER["PHP_AUTH_PW"])) {
				if ($basicAuthors[$_SERVER["PHP_AUTH_USER"]] == $_SERVER["PHP_AUTH_PW"]) {
					return null;
				}
			}
			
			header("WWW-Authenticate: Basic realm=\"Enter your account.\"");
			header("HTTP/1.0 401 Unauthorized");
			//キャンセル時の表示
			echo "Authorization Required";
			exit;
		}
		
		// セッションにユーザーIDがあるけれどもauthが仮登録ユーザーの場合は強制的にユーザーフォームへ飛ばす
		if ($this->session->get('user_id') && $this->session->get('user_auth') == $this->config->get('user_auth_first')) {
			if (strpos($actionName, 'user_form') === false
				&& strpos($actionName, 'user_modal') === false
				&& strpos($actionName, 'ajax_user') === false
				&& strpos($actionName, 'sign_out') === false
			) {
				return $this->backend->perform('user_form');
			}
		}
		
		if ($this->session->get('user_id')) {
			// 有料会員
			$this->af->setApp('is_expire', $this->plants->isExpire());
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
		if ($message) {
			echo json_encode(array('error' => $message));
		} else {
			echo json_encode(array('success' => false, 'error' => implode("\n", $this->ae->getMessageList())));
		}
		exit;
	}
	
	public function respondAjaxSuccess($response = '')
	{
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
		if (!$limit) {
			return false;
		}
		
		$beginNumber = ($page - 1) * $limit + 1;
		$endPage = ceil($count / $limit);
		$endNumber = ($page < $endPage ? ($beginNumber + $limit - 1) : $count);
		
		if ($endNumber) {
			$counter = $isCounter ? smarty_modifier_lang(sprintf('%d件中 %d 〜 %d件', $count, $beginNumber, $endNumber)) : '';
			$prefix = '<nav><ul class="pagination justify-content-end">';
			$firstlink = '<li class="page-item"><a href="#" aria-label="Previous" class="a-exec-pager page-link" page="1"><span aria-hidden="true">&laquo;&laquo;</span></a></li>';
			$prevlink = '<li class="page-item"><a href="#" aria-label="Previous" class="a-exec-pager page-link" page="%d"><span aria-hidden="true">&laquo;</span></a></li>';
			$pager = '<li class="page-item%s"><a href="#" class="a-exec-pager page-link" page="%d">%d</a></li>';
			$option = '<option value="%d"%s>%d</option>';
			$nextlink = '<li class="page-item"><a href="#" aria-label="Next" class="a-exec-pager page-link" page="%d"><span aria-hidden="true">&raquo;</span></a></li>';
			$endlink = '<li class="page-item"><a href="#" aria-label="Next" class="a-exec-pager page-link" page="%d"><span aria-hidden="true">&raquo;&raquo;</span></a></li>';
			$postfix = '</ul></nav>'; 
	 		$clicker = '<script>'
					 . '$(function() { $(".a-exec-pager").off().on("click", function(e) { e.preventDefault(); %s; }); $(".select-exec-pager").off().on("change", function(e) { %s; }); })'
					 . '</script>';
			
			// HTML生成
			$html = sprintf($clicker, $scriptAnchor, $scriptSelect);
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
						$html .= $firstlink;				
					}
					if ($page > 1) {
						$html .= sprintf($prevlink, $page - 1);
					}
					for ($i = 1; $i <= $endPage; $i++) {
						$html .= sprintf($pager, ($i == $page ? ' active' : ''), $i, $i);
					}
					if ($page < $endPage) {
						$html .= sprintf($nextlink, $page + 1);
					}
					if ($page < $endPage - 1) {
						$html .= sprintf($endlink, $endPage);
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
	
	/**
	 * smarty function: table()
	 * 
	 * @param	array	$tables
	 */
	function AfwSmartyBlockTable($params, $content = '', &$smarty = array(), &$repeat = true)
	{
		if (!$repeat) {
			// Formats
			if (strlen(trim($content)) && !($contents = json_decode(json_encode(simplexml_load_string('<document>' . $content . '</document>')), 1))) {
				return 'format error.';
			}
			
			// 配列の再構築
			$formats = array();
			if (is_array($contents)) {
				foreach ($contents as $type => $c) {
					$number = 0;
					$tagNumbers = array();
					foreach ($c as $tag => $cc) {
						if (isset($cc['@attributes'])) {
							$formats[$type][$tag][$cc['@attributes']['col']] = $cc['@attributes']['value'];
							$tagNumbers[$tag]++;
						} else {
							foreach ($cc as $ccc) {
								$formats[$type][$tag][$ccc['@attributes']['col']] = $ccc['@attributes']['value'];
								$tagNumbers[$tag]++;
							}
						}
					}
				}
			}

			// パラメータ
			$tables = isset($params['tables']) ? $params['tables'] : array();	// テーブル本体
			$id = isset($params['id']) ? $params['id'] : '';	// テーブルのID
			$class = isset($params['class']) ? $params['class'] : '';	// テーブルのCLASS
			$isNoHeader = isset($params['noheader']) ? $params['noheader'] : false;
			
			// ヘッダ生成
			if (!$isNoHeader) {
				$html = '<div class="table-responsive">';
				$html .= sprintf('<table id="%s" class="table %s">', $id, $class);
			}
			
			// テーブル
			if (is_array($tables)) {
				if (!$isNoHeader) {
					// テーブルヘッダ
					if (isset($tables['header']) && is_array($tables['header'])) {
						$html .= '<thead class="thead-light">';
						if (!is_array($tables['header'][0])) {
							$tables['header'] = array($tables['header']);
						}
						foreach ($tables['header'] as $row => $h) {
							$html .= '<tr>';
							foreach ($h as $col => $cell) {
								$cell = htmlspecialchars_decode($cell);
								
								// フォーマット
								$type = isset($formats['header']['type']) ? $this->_checkTableFormat($formats['header']['type'], $col) : false;
								$class = isset($formats['header']['class']) ? $this->_checkTableFormat($formats['header']['class'], $col) : '';
								$style = isset($formats['header']['style']) ? $this->_checkTableFormat($formats['header']['style'], $col) : '';
								$order = isset($formats['header']['order']) ? $this->_checkTableFormat($formats['header']['order'], $col) : false;
								
								if ($order) {
									$desc = '_desc';
									if ($this->af->get('o') == $order) {
										$icon = '▲'; //'<i class="glyphicon glyphicon-sort-by-attributes"></i>';
										$order .= $desc;
									} elseif ($this->af->get('o') == $order . $desc) {
										$icon = '▼'; //'<i class="glyphicon glyphicon-sort-by-attributes-alt"></i>';
									} elseif (!$this->af->get('o') && isset($formats['header']['order']['default']) && $formats['header']['order']['default'] == $order) {
										// デフォルト
										$icon = '▲'; //'<i class="glyphicon glyphicon-sort-by-attributes"></i>';
										$order .= $desc;
									} else {
										$icon = ''; //'<i class="glyphicon glyphicon-sort"></i>';
									}
									
									$cell = sprintf('<a href="#" class="%s" order="%s">%s%s</a>',
										$formats['header']['order']['class'],
										$order,
										$cell,
										$icon
									);
								}
								$html .= sprintf('<th class="%s" style="%s" scope="row">%s</th>', $class, $style, $cell);
							}
							$html .= '</tr>';
						}
						$html .= '</thead>';
					}
				}
				
				// テーブル本体
				$html .= '<tbody>';
				if (isset($tables['body']) && is_array($tables['body']) && $tables['body']) {
					foreach ($tables['body'] as $row => $b) {
						$html .= sprintf('<tr>');
						
						foreach ($b as $col => $cell) {
							$cell = htmlspecialchars_decode($cell);
							
							// フォーマット
							$type = isset($formats['body']['type']) ? $this->_checkTableFormat($formats['body']['type'], $col) : false;
							$class = isset($formats['body']['class']) ? $this->_checkTableFormat($formats['body']['class'], $col) : (is_numeric($cell) ? 'text-right' : '');
							$style = isset($formats['body']['style']) ? $this->_checkTableFormat($formats['body']['style'], $col) : '';
							
							$html .= sprintf('<%s class="%s" style="%s" scope="row">%s</%s>', ($type ? $type : 'td'), $class, $style, $cell, ($type ? $type : 'td'));
						}
						$html .= '</tr>';
					}
				} else {
					$html .= sprintf('<td colspan="%d">%s</td>', count(array_shift($tables['header'])), $this->initval($params['nodata'], ''));
				}
				$html .= '</tbody>';
			}
			
			if (!$isNoHeader) {
				$html .= '</table></div>';
			}
			
			return $html;
		}
	}
	
	private function _checkTableFormat($formats, $col)
	{
		if (isset($formats[$col])) {
			return $formats[$col];
		} elseif (isset($formats['all'])) {
			return $formats['all'];
		} else {
			return '';
		}
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
		if (in_array($this->session->get('user_auth'), $this->config->get('user_auth_admins'))) {
			return true;
		}
		return false;
	}
	
	// 管理者ではない場合はリダイレクト
	public function requiredManager($url = null)
	{
		if (in_array($this->session->get('user_auth'), $this->config->get('user_auth_admins'))) {
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
		
		$this->redirect();
	}
	
	public function isSmartPhone()
	{
		// see http://matsui89.com/etc/260/
		$ua = $_SERVER['HTTP_USER_AGENT'];
		if (
		 	(strpos($ua, 'iPhone') === false)
			&& (strpos($ua, 'iPad') === false)
			&& (strpos($ua, 'Andoid') === false)
		) {
			return false;
		}
		return true;
	}
}
// }}}
?>
