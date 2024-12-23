<?php
// vim: foldmethod=marker
/**
 *  Ethna_Plugin_Logwriter.php
 *
 *  @author     Masaki Fujimoto <fujimoto@php.net>
 *  @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 *  @package    Ethna
 *  @version    $Id: Ethna_Plugin_Logwriter.php 298 2006-07-19 05:22:39Z fujimoto $
 */

// {{{ Ethna_Plugin_Logwriter
/**
 *  ログ出力基底クラス
 *
 *  @author     Masaki Fujimoto <fujimoto@php.net>
 *  @access     public
 *  @package    Ethna
 */
class Ethna_Plugin_Logwriter
{
    /**#@+
     *  @access private
     */

    /** @var    string  ログアイデンティティ文字列 */
    var $ident;

    /** @var    int     ログファシリティ */
    var $facility;

    /** @var    int     ログオプション */
    var $option;

    /** @var    bool    バックトレースが取得可能かどうか */
    var $have_backtrace;

    /** @var    array   ログレベル名テーブル */
    var $level_name_table = array(
        LOG_EMERG   => 'EMERG',
        LOG_ALERT   => 'ALERT',
        LOG_CRIT    => 'CRIT',
        LOG_ERR     => 'ERR',
        LOG_WARNING => 'WARNING',
        LOG_NOTICE  => 'NOTICE',
        LOG_INFO    => 'INFO',
        LOG_DEBUG   => 'DEBUG',
    );

    /**#@-*/

    /**
     *  Ethna_Plugin_Logwriterクラスのコンストラクタ
     *
     *  @access public
     *  @param  string  $log_ident      ログアイデンティティ文字列(プロセス名等)
     *  @param  int     $log_facility   ログファシリティ
     *  @param  string  $log_file       ログ出力先ファイル名(LOG_FILEオプションが指定されている場合のみ)
     *  @param  int     $log_option     ログオプション(LOG_FILE,LOG_FUNCTION...)
     */
    function __construct()
    {
    }

    /**
     *  ログオプションを設定する
     *
     *  @access public
     *  @param  int     $option     ログオプション(LOG_FILE,LOG_FUNCTION...)
     */
    function setOption($option)
    {
        $this->ident = $option['ident'];
        $this->facility = $option['facility'];
        $this->option = $option;
        $this->have_backtrace = function_exists('debug_backtrace');
    }

    /**
     *  ログ出力を開始する
     *
     *  @access public
     */
    function begin()
    {
    }

    /**
     *  ログを出力する
     *
     *  @access public
     *  @param  int     $level      ログレベル(LOG_DEBUG, LOG_NOTICE...)
     *  @param  string  $message    ログメッセージ(+引数)
     */
    function log($level, $message)
    {
    }

    /**
     *  ログ出力を終了する
     *
     *  @access public
     */
    function end()
    {
    }

    /**
     *  ログアイデンティティ文字列を取得する
     *
     *  @access public
     *  @return string  ログアイデンティティ文字列
     */
    function getIdent()
    {
        return $this->ident;
    }

    /**
     *  ログレベルを表示文字列に変換する
     *
     *  @access private
     *  @param  int     $level  ログレベル(LOG_DEBUG,LOG_NOTICE...)
     *  @return string  ログレベル表示文字列(LOG_DEBUG→"DEBUG")
     */
    function _getLogLevelName($level)
    {
        if (isset($this->level_name_table[$level]) == false) {
            return null;
        }
        return $this->level_name_table[$level];
    }

    /**
     *  ログ出力箇所の情報(関数名/ファイル名等)を取得する
     *
     *  @access private
     *  @return array   ログ出力箇所の情報
     */
    function _getBacktrace()
    {
        $skip_method_list = array(
            array('ethna', 'raise.*'),
            array(null, 'raiseerror'),
            array(null, 'handleerror'),
            array('ethna_logger', null),
            array('ethna_plugin_logwriter*', null),
            array('ethna_error', null),
            array('ethna_apperror', null),
            array('ethna_actionerror', null),
            array('ethna_backend', 'log'),
            array(null, 'ethna_error_handler'),
            array(null, 'trigger_error'),
        );

        if ($this->have_backtrace == false) {
            return null;
        }

        $bt = debug_backtrace();
        $i = 0;
        while ($i < count($bt)) {
            if (isset($bt[$i]['class']) == false) {
                $bt[$i]['class'] = null;
            }
            $skip = false;

            // メソッドスキップ処理
            foreach ($skip_method_list as $method) {
                $class = $function = true;
                if ($method[0] != null) {
                    $class = preg_match("/$method[0]/i", $bt[$i]['class']);
                }
                if ($method[1] != null) {
                    $function = preg_match("/$method[1]/i", $bt[$i]['function']);
                }
                if ($class && $function) {
                    $skip = true;
                    break;
                }
            }

            if ($skip) {
                $i++;
            } else {
                break;
            }
        }

        $c = Ethna_Controller::getInstance();
        $basedir = $c->getBasedir();

        $function = sprintf("%s.%s", isset($bt[$i]['class']) ? $bt[$i]['class'] : 'global', $bt[$i]['function']);

        $file = $bt[$i]['file'];
        if (strncmp($file, $basedir, strlen($basedir)) == 0) {
            $file = substr($file, strlen($basedir));
        }
        if (strncmp($file, ETHNA_BASE, strlen(ETHNA_BASE)) == 0) {
            $file = preg_replace('#^/+#', '', substr($file, strlen(ETHNA_BASE)));
        }
        $line = $bt[$i]['line'];
        return array('function' => $function, 'pos' => sprintf('%s:%s', $file, $line));
    }
}
// }}}
?>
