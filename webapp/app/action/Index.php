<?php
/**
 *  Index.php
 *
 *  @author     {$author}
 *  @package    Afw
 *  @version    $Id: skel.action.php 387 2006-11-06 14:31:24Z cocoitiban $
 */

/**
 *  indexフォームの実装
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Afw
 */
class Afw_Form_Index extends Afw_ActionForm
{
    /** @var    bool    バリデータにプラグインを使うフラグ */
    var $use_validator_plugin = true;

    /**
     *  @access private
     *  @var    array   フォーム値定義
     */
    var $form = array(
    );
}

/**
 *  indexアクションの実装
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Afw
 */
class Afw_Action_Index extends Afw_ActionClass
{
    /**
     *  indexアクションの前処理
     *
     *  @access public
     *  @return string      遷移名(正常終了ならnull, 処理終了ならfalse)
     */
	function prepare()
	{
		return null;
	}

    /**
     *  indexアクションの実装
     *
     *  @access public
     *  @return string  遷移名
     */
	function perform()
	{
		return 'index';
	}
}
?>
