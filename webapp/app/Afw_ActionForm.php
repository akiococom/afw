<?php
// vim: foldmethod=marker
/**
 *  Afw_ActionForm.php
 *
 *  @author     {$author}
 *  @package    Afw
 *  @version    $Id: app.actionform.php 323 2006-08-22 15:52:26Z fujimoto $
 */

// {{{ Afw_ActionForm
/**
 *  アクションフォームクラス
 *
 *  @author     {$author}
 *  @package    Afw
 *  @access     public
 */
class Afw_ActionForm extends Ethna_ActionForm
{
    /**#@+
     *  @access private
     */

    /** @var    array   フォーム値定義(デフォルト) */
    var $form_template = array();

    /** @var    bool    バリデータにプラグインを使うフラグ */
    var $use_validator_plugin = true;

    /**#@-*/

    /**
     *  フォーム値検証のエラー処理を行う
     *
     *  @access public
     *  @param  string      $name   フォーム項目名
     *  @param  int         $code   エラーコード
     */
    function handleError($name, $code)
    {
        return parent::handleError($name, $code);
    }

    /**
     *  フォーム値定義テンプレートを設定する
     *
     *  @access protected
     *  @param  array   $form_template  フォーム値テンプレート
     *  @return array   フォーム値テンプレート
     */
    function _setFormTemplate($form_template)
    {
        return parent::_setFormTemplate($form_template);
    }

    /**
     *  フォーム値定義を設定する
     *
     *  @access protected
     */
    function _setFormDef()
    {
        return parent::_setFormDef();
    }


	/**
	 * JPG拡張子のチェック
	 */
	function checkImageJpeg($name)
	{
		$form = $this->form_vars[$name];
		if (!$form['error']) {
			// 拡張子のセット
			$exts = array('jpg', 'jpeg');
			
			// エラーチェック
			if (!$this->_checkExtension($name, $exts)) {
				$this->ae->add($name, "画像ファイルはJPEGファイルのみ登録できます。", E_FORM_INVALIDVALUE);
			}
		}
	}
	
	/**
	 * 拡張子チェックサブ
	 */
	function _checkExtension($name, $exts) 
	{
		// 拡張子の取得
		$form = $this->form_vars[$name];
		$filename = $form['name'];
		$path_parts = pathinfo($filename);
		
		if ($filename) {
			// 拡張子のチェック
			foreach ($exts as $ext) {
				if (strtolower($path_parts['extension']) == strtolower($ext)) {
					return true;
				}
			}
			return false;
		} else {
			return true;
		}
	}
	

}
// }}}
?>
