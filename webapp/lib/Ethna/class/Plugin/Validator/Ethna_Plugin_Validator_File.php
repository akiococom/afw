<?php
// vim: foldmethod=marker
/**
 *  Ethna_Plugin_Validator_File.php
 *
 *  @author     ICHII Takashi <ichii386@schweetheart.jp>
 *  @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 *  @package    Ethna
 *  @version    $Id: Ethna_Plugin_Validator_File.php 460 2007-04-11 13:12:30Z cocoitiban $
 */

// UPLOAD_ERR_* が未定義の場合
if (defined('UPLOAD_ERR_OK') == false) { // PHP 4.3.0
    define('UPLOAD_ERR_OK', 0);
}
if (defined('UPLOAD_ERR_INI_SIZE') == false) { // PHP 4.3.0
    define('UPLOAD_ERR_INI_SIZE', 1);
}
if (defined('UPLOAD_ERR_FORM_SIZE') == false) { // PHP 4.3.0
    define('UPLOAD_ERR_FORM_SIZE', 2);
}
if (defined('UPLOAD_ERR_PARTIAL') == false) { // PHP 4.3.0
    define('UPLOAD_ERR_PARTIAL', 3);
}
if (defined('UPLOAD_ERR_NO_FILE') == false) { // PHP 4.3.0
    define('UPLOAD_ERR_NO_FILE', 4);
}
if (defined('UPLOAD_ERR_NO_TMP_DIR') == false) { // PHP 4.3.10, 5.0.3
    define('UPLOAD_ERR_NO_TMP_DIR', 6);
}
if (defined('UPLOAD_ERR_CANT_WRITE') == false) { // PHP 5.1.0
    define('UPLOAD_ERR_CANT_WRITE', 7);
}

// {{{ Ethna_Plugin_Validator_File
/**
 *  ファイルチェックプラグイン
 *
 *  @author     ICHII Takashi <ichii386@schweetheart.jp>
 *  @access     public
 *  @package    Ethna
 */
class Ethna_Plugin_Validator_File extends Ethna_Plugin_Validator
{
    /** @var    bool    配列を受け取るかフラグ */
    var $accept_array = false;

    /**
     *  アップロードされたファイルのチェックを行う
     *  XXX: プラグインのエラーコードを修正する
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
        if ($this->getFormType($name) != VAR_TYPE_FILE) {
            return $true;
        }

        // そもそもアップロードされていない場合はスキップ
        if ($var['error'] == UPLOAD_ERR_NO_FILE) {
            return $true;
        }


        // エラーコードの検査
        $msg = '';
        switch ($var['error']) {
        case UPLOAD_ERR_INI_SIZE: 
            $msg = $glexalang['glexa']['ethna_plugin_validator_file_001'];
            break;
        case UPLOAD_ERR_FORM_SIZE:
            $msg = $glexalang['glexa']['ethna_plugin_validator_file_002'];
            break;
        case UPLOAD_ERR_PARTIAL:
            $msg= $glexalang['glexa']['ethna_plugin_validator_file_003'];
            break;
        case UPLOAD_ERR_NO_FILE:
            $msg = $glexalang['glexa']['ethna_plugin_validator_file_004'];
            break;
        case UPLOAD_ERR_NO_TMP_DIR:
            $msg = $glexalang['glexa']['ethna_plugin_validator_file_005'];
            break;
        case UPLOAD_ERR_CANT_WRITE:
            $msg= $glexalang['glexa']['ethna_plugin_validator_file_006'];
            break;
        }
        if ($msg != '') {
            if (isset($params['error'])) {
                $msg = $params['error'];
            }
            return Ethna::raiseNotice($msg, E_FORM_WRONGTYPE_FILE);
        }


        // tmp_name の検査
        if (isset($var['tmp_name']) == false || is_uploaded_file($var['tmp_name']) == false) {
            if (isset($params['error'])) {
                $msg = $params['error'];
            } else {
                $msg = 'tmp_name ' . $glexalang['glexa']['ethna_plugin_validator_file_007'];
            }
            return Ethna::raiseNotice($msg, E_FORM_WRONGTYPE_FILE);
        }


        // size の検査
        if (isset($params['size_max'])) {
            $st = stat($var['tmp_name']);
            if ($st[7] > $this->_getSizeAsBytes($params['size_max'])) {
                if (isset($params['error'])) {
                    $msg = $params['error'];
                } else {
                    $msg = $glexalang['glexa']['ethna_plugin_validator_file_008'];
                }
                return Ethna::raiseNotice($msg, E_FORM_WRONGTYPE_FILE, array($params['size_max']));
            }
        }
        if (isset($params['size_min'])) {
            $st = stat($var['tmp_name']);
            if ($st[7] < $this->_getSizeAsBytes($params['size_min'])) {
                if (isset($params['error'])) {
                    $msg = $params['error'];
                } else {
                    $msg = $glexalang['glexa']['ethna_plugin_validator_file_009'];
                }
                return Ethna::raiseNotice($msg, E_FORM_WRONGTYPE_FILE, array($params['size_min']));
            }
        }


        // type の検査
        if (isset($params['type'])) {
            $type_list = to_array($params['type']);
            $posted_mime = explode('/', $var['type'], 2);
            foreach ($type_list as $type) {
                $wanted_mime = explode('/', $type, 2);
                $test = (count($wanted_mime) == 1)
                        ? (strcasecmp($wanted_mime[0], $posted_mime[0]) == 0)
                : (strcasecmp($type, $var['type']) == 0);  
                if ($test == true) {
                    break;
                }
            }
            if ($test == false) {
                if (isset($params['error'])) {
                    $msg = $params['error'];
                } else {
                    $msg = $glexalang['glexa']['ethna_plugin_validator_file_010'];
                }
                return Ethna::raiseNotice($msg, E_FORM_WRONGTYPE_FILE);
            }
        }

        // name(ファイル名)の検査
        if (isset($params['name'])) {
            $test = ($params['name']{0} == '/')
                ? preg_match($params['name'], $var['name'])
                : (strcmp($params['name'], $var['name']) == 0);
            if ($test == false) {
                if (isset($params['error'])) {
                    $msg = $params['error'];
                } else {
                    $msg = $glexalang['glexa']['ethna_plugin_validator_file_011'];
                }
                return Ethna::raiseNotice($msg, E_FORM_WRONGTYPE_FILE);
            }
        }

        return $true;
    }


    function _getSizeAsBytes($size)
    {
        $unit = 1;
        if (preg_match('/^([0-9]+)([mk])?(b(ytes?)?)?$/i', trim($size), $matches)) {
            if (isset($matches[1])) {
                $size = $matches[1];
            }
            if (isset($matches[2])) {
                if (strtolower($matches[2]) === 'm') {
                    $unit = 1048576;
                } else if (strtolower($matches[2]) === 'k') {
                    $unit = 1024;
                }
            }
        }
        return intval($matches[1]) * $unit;
    }
}
// }}}
?>
