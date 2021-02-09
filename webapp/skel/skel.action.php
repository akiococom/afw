<?php
/**
 *  {$action_path}
 *
 *  @author     akio.co.com
 *  @package    Afw
 *  @version    1.0
 */

/**
 *  {$action_name}フォームの実装
 *
 *  @author     akio.co.com
 *  @access     public
 *  @package    Afw
 */
class {$action_form} extends Afw_ActionForm
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
 *  {$action_name}アクションの実装
 *
 *  @author     akio.co.com
 *  @access     public
 *  @package    Afw
 */
class {$action_class} extends Afw_ActionClass
{
    /**
     *  {$action_name}アクションの前処理
     *
     *  @access public
     *  @return string      遷移名(正常終了ならnull, 処理終了ならfalse)
     */
	function prepare()
	{
		return null;
	}

    /**
     *  {$action_name}アクションの実装
     *
     *  @access public
     *  @return string  遷移名
     */
	function perform()
	{
		return '{$action_name}';
	}
}
?>
