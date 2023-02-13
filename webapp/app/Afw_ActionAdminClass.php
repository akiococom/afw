<?php
// vim: foldmethod=marker
/**
 *  Afw_ActionAdminClass.php
 *
 *  @author     {$author}
 *  @package    Afw
 */

// {{{ Afw_ActionAdminClass
/**
 *  action実行クラス
 *
 *  @author     {$author}
 *  @package    Afw
 *  @access     public
 */
class Afw_ActionAdminClass extends Afw_ActionClass
{
    /**
     *  アクション実行前の認証処理を行う
     *
     *  @access public
     *  @return string  遷移名(nullなら正常終了, falseなら処理終了)
     */
	function authenticate()
	{
		$basicAuthors = $this->config->get('basic_authors');
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
}
// }}}
?>
