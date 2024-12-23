<?php
// vim: foldmethod=marker
/**
 *  Ethna_Plugin_Validator_Custom.php
 *
 *  @author     ICHII Takashi <ichii386@schweetheart.jp>
 *  @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 *  @package    Ethna
 *  @version    $Id: Ethna_Plugin_Validator_Custom.php 312 2006-08-03 03:30:42Z ichii386 $
 */

// {{{ Ethna_Plugin_Validator_Custom
/**
 *  customバリデータのラッパープラグイン
 *
 *  @author     ICHII Takashi <ichii386@schweetheart.jp>
 *  @access     public
 *  @package    Ethna
 */
class Ethna_Plugin_Validator_Custom extends Ethna_Plugin_Validator
{
    /** @var    bool    配列を受け取るかフラグ */
    var $accept_array = true;

    /**
     *  customバリデータのラッパー
     *
     *  @access public
     *  @param  string  $name       フォームの名前
     *  @param  mixed   $var        フォームの値
     *  @param  array   $params     プラグインのパラメータ
     */
    function &validate($name, $var, $params)
    {
        $true = true;
        $false = false;

        $method_list = preg_split('/\s*,\s*/', $params['custom'], -1, PREG_SPLIT_NO_EMPTY);
        if (is_array($method_list) == false) {
            return $true;
        }

        foreach ($method_list as $method) {
            if (method_exists($this->af, $method)) {
                $ret = $this->af->$method($name);
                if (Ethna::isError($ret)) {
                    // このエラーはすでに af::checkSomething() で ae::add()
                    // してある
                    return $false;
                }
            }
        }

        return $true;
    }
}
// }}}
?>
