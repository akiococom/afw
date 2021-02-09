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
        /*
        'sample' => array(
            // フォームの定義
            'type'          => VAR_TYPE_INT,    // 入力値型
            'form_type'     => FORM_TYPE_TEXT,  // フォーム型
            'name'          => 'サンプル',      // 表示名

            // バリデータ(記述順にバリデータが実行されます)
            'required'      => true,            // 必須オプション(true/false)
            'min'           => null,            // 最小値
            'max'           => null,            // 最大値
            'regexp'        => null,            // 文字種指定(正規表現)

            // フィルタ
            'filter'        => null,            // 入力値変換フィルタオプション
        ),
        */
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
        return null;
    }
}
?>