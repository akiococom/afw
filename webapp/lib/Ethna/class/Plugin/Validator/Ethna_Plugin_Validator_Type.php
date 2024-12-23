<?php
// vim: foldmethod=marker
/**
 *  Ethna_Plugin_Validator_Type.php
 *
 *  @author     ICHII Takashi <ichii386@schweetheart.jp>
 *  @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 *  @package    Ethna
 *  @version    $Id: Ethna_Plugin_Validator_Type.php 298 2006-07-19 05:22:39Z fujimoto $
 */

// {{{ Ethna_Plugin_Validator_Type
/**
 *  タイプチェックプラグイン
 *
 *  @author     ICHII Takashi <ichii386@schweetheart.jp>
 *  @access     public
 *  @package    Ethna
 */
class Ethna_Plugin_Validator_Type extends Ethna_Plugin_Validator
{
    /** @var    bool    配列を受け取るかフラグ */
    var $accept_array = false;

    /**
     *  フォーム値の型チェックを行う
     *
     *  @access public
     *  @param  string  $name       フォームの名前
     *  @param  mixed   $var        フォームの値
     *  @param  array   $params     プラグインのパラメータ
     */
    function &validate($name, $var, $params)
    {
    	global $glexalang;
        $true = true;
        $type = $params['type'];
        if ($type == VAR_TYPE_FILE || $this->isEmpty($var, $type)) {
            return $true;
        }

        foreach (array_keys(to_array($var)) as $key) {
            switch ($type) {
                case VAR_TYPE_INT:
                    if (!preg_match('/^-?\d+$/', $var)) {
                        if (isset($params['error'])) {
                            $msg = $params['error'];
                        } else {
                            $msg = "{form}" . $glexalang['glexa']['ethna_plugin_validator_type_001'];
                        }
                        return Ethna::raiseNotice($msg, E_FORM_WRONGTYPE_INT);
                    }
                    break;

                case VAR_TYPE_FLOAT:
                    if (!preg_match('/^-?\d+$/', $var) && !preg_match('/^-?\d+\.\d+$/', $var)) {
                        if (isset($params['error'])) {
                            $msg = $params['error'];
                        } else {
                            $msg = "{form}" . $glexalang['glexa']['ethna_plugin_validator_type_002'];
                        }
                        return Ethna::raiseNotice($msg, E_FORM_WRONGTYPE_FLOAT);
                    }
                    break;

                case VAR_TYPE_BOOLEAN:
                    if ($var != "1" && $var != "0") {
                        if (isset($params['error'])) {
                            $msg = $params['error'];
                        } else {
                            $msg = "{form}" . $glexalang['glexa']['ethna_plugin_validator_type_003'];
                        }
                        return Ethna::raiseNotice($msg, E_FORM_WRONGTYPE_BOOLEAN);
                    }
                    break;

                case VAR_TYPE_DATETIME:
                    $r = strtotime($var);
                    if ($r == -1 || $r === false) {
                        if (isset($params['error'])) {
                            $msg = $params['error'];
                        } else {
                            $msg = "{form}" . $glexalang['glexa']['ethna_plugin_validator_type_004'];
                        }
                        return Ethna::raiseNotice($msg, E_FORM_WRONGTYPE_DATETIME);
                    }
                    break;
            }
        }

        return $true;
    }
}
// }}}
?>
