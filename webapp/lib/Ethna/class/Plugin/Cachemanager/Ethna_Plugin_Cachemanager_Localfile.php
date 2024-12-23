<?php
// vim: foldmethod=marker tabstop=4 shiftwidth=4 autoindent
/**
 *  Ethna_Plugin_Cachemanager_Localfile.php
 *
 *  @author     Masaki Fujimoto <fujimoto@php.net>
 *  @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 *  @package    Ethna
 *  @version    $Id: Ethna_Plugin_Cachemanager_Localfile.php 346 2006-10-04 09:11:40Z ichii386 $
 */

/**
 *  キャッシュマネージャクラス(ローカルファイルキャッシュ版)
 *
 *  @author     Masaki Fujimoto <fujimoto@php.net>
 *  @access     public
 *  @package    Ethna
 */
class Ethna_Plugin_Cachemanager_Localfile extends Ethna_Plugin_Cachemanager
{
    /**#@+  @access private */

    /**#@-*/

    /**
     *  キャッシュに設定された値を取得する
     *
     *  キャッシュに値が設定されている場合はキャッシュ値
     *  が戻り値となる。キャッシュに値が無い場合やlifetime
     *  を過ぎている場合、エラーが発生した場合はPEAR_Error
     *  オブジェクトが戻り値となる。
     *
     *  @access public
     *  @param  string  $key        キャッシュキー
     *  @param  int     $lifetime   キャッシュ有効期間
     *  @param  string  $namespace  キャッシュネームスペース
     *  @return array   キャッシュ値
     */
    function get($key, $lifetime = null, $namespace = null)
    {
        $namespace = is_null($namespace) ? $this->namespace : $namespace;
        $cache_file = $this->_getCacheFile($namespace, $key);

        // ライフタイムチェック
        clearstatcache();
        if (is_readable($cache_file) === false
            || ($st = stat($cache_file)) === false) {
            return Ethna::raiseError('fopen failed', E_CACHE_NO_VALUE);
        }
        if (is_null($lifetime) == false) {
            if (($st[9]+$lifetime) < time()) {
                return Ethna::raiseError('fopen failed', E_CACHE_EXPIRED);
            }
        }

        $fp = fopen($cache_file, "r");
        if ($fp == false) {
            return Ethna::raiseError('fopen failed', E_CACHE_NO_VALUE);
        }
        // ロック
        $timeout = 3;
        while ($timeout > 0) {
            $r = flock($fp, LOCK_EX|LOCK_NB);
            if ($r) {
                break;
            }
            $timeout--;
            sleep(1);
        }
        if ($timeout <= 0) {
            fclose($fp);
            return Ethna::raiseError('fopen failed', E_CACHE_GENERAL);
        }

        $n = 0;
        while ($st[7] == 0) {
            clearstatcache();
            $st = stat($cache_file);
            usleep(1000*1);
            $n++;
            if ($n > 5) {
                break;
            }
        }

        if ($st == false || $n > 5) {
            fclose($fp);
            return Ethna::raiseError('stat failed', E_CACHE_NO_VALUE);
        }
        $value = fread($fp, $st[7]);
        fclose($fp);

        return unserialize($value);
    }

    /**
     *  キャッシュの最終更新日時を取得する
     *
     *  @access public
     *  @param  string  $key        キャッシュキー
     *  @param  string  $namespace  キャッシュネームスペース
     *  @return int     最終更新日時(unixtime)
     */
    function getLastModified($key, $namespace = null)
    {
        $namespace = is_null($namespace) ? $this->namespace : $namespace;
        $cache_file = $this->_getCacheFile($namespace, $key);

        clearstatcache();
        if (is_readable($cache_file) === false
            || ($st = stat($cache_file)) === false) {
            return Ethna::raiseError('fopen failed', E_CACHE_NO_VALUE);
        }
        return $st[9];
    }

    /**
     *  値がキャッシュされているかどうかを取得する
     *
     *  @access public
     *  @param  string  $key        キャッシュキー
     *  @param  int     $lifetime   キャッシュ有効期間
     *  @param  string  $namespace  キャッシュネームスペース
     */
    function isCached($key, $lifetime = null, $namespace = null)
    {
        $namespace = is_null($namespace) ? $this->namespace : $namespace;
        $cache_file = $this->_getCacheFile($namespace, $key);

        // ライフタイムチェック
        clearstatcache();
        if (is_readable($cache_file) === false
            || ($st = stat($cache_file)) === false) {
            return false;
        }
        if (is_null($lifetime) == false) {
            if (($st[9]+$lifetime) < time()) {
                return false;
            }
        }

        return true;
    }

    /**
     *  キャッシュに値を設定する
     *
     *  @access public
     *  @param  string  $key        キャッシュキー
     *  @param  mixed   $value      キャッシュ値
     *  @param  int     $timestamp  キャッシュ最終更新時刻(unixtime)
     *  @param  string  $namespace  キャッシュネームスペース
     */
    function set($key, $value, $timestamp = null, $namespace = null)
    {
        $namespace = is_null($namespace) ? $this->namespace : $namespace;
        $dir = $this->_getCacheDir($namespace, $key);

        // キャッシュディレクトリチェック
        $r = Ethna_Util::mkdir($dir, 0777);
        if ($r == false && is_dir($dir) == false) {
            return Ethna::raiseError('mkdir(%s) failed', E_USER_WARNING, $dir);
        }

        $cache_file = $this->_getCacheFile($namespace, $key);
        $fp = fopen($cache_file, "a+");
        if ($fp == false) {
            return Ethna::raiseError('fopen failed', E_CACHE_GENERAL);
        }

        // ロック
        $timeout = 3;
        while ($timeout > 0) {
            $r = flock($fp, LOCK_EX|LOCK_NB);
            if ($r) {
                break;
            }
            $timeout--;
            sleep(1);
        }
        if ($timeout <= 0) {
            fclose($fp);
            return Ethna::raiseError('fopen failed', E_CACHE_GENERAL);
        }
        rewind($fp);
        ftruncate($fp, 0);
        fwrite($fp, serialize($value));
        fclose($fp);
        Ethna_Util::chmod($cache_file, 0666);

        if (is_null($timestamp)) {
            // this could suppress warning
            touch($cache_file);
        } else {
            touch($cache_file, $timestamp);
        }

        return 0;
    }

    /**
     *  キャッシュ値を削除する
     *
     *  @access public
     *  @param  string  $key        キャッシュキー
     *  @param  string  $namespace  キャッシュネームスペース
     */
    function clear($key, $namespace = null)
    {
        $namespace = is_null($namespace) ? $this->namespace : $namespace;
        $cache_file = $this->_getCacheFile($namespace, $key);

        if (file_exists($cache_file)) {
            unlink($cache_file);
        }
    }

    /**
     *  キャッシュ対象ディレクトリを取得する
     *
     *  @access private
     */
    function _getCacheDir($namespace, $key)
    {
        $len = strlen($key);
        // intentionally avoid using -2 or -4
        $dir1 = substr($key, $len-4, 2);
        if ($len-4 < 0 || strlen($dir1) < 2) {
            $dir1 = "__dir1";
        }
        $dir2 = substr($key, $len-2, 2);
        if ($len-2 < 0 || strlen($dir2) < 2) {
            $dir2 = "__dir2";
        }

        $map = $this->config->get('cachemanager_localfile');
        $tmp_key = $namespace . "::" . $key;
        // PHP依存:)
        $dir = "default";

        if (is_array($map)) {
            foreach ($map as $key => $value) {
                if (strncmp($key, $tmp_key, strlen($key)) == 0) {
                    $dir = $value;
                    break;
                }
            }
        }
        
        return sprintf("%s/cache/%s/cache_%s/%s/%s", $this->backend->getTmpdir(), $dir, $this->_escape($namespace), $this->_escape($dir1), $this->_escape($dir2));
    }

    /**
     *  キャッシュファイルを取得する
     *
     *  @access private
     */
    function _getCacheFile($namespace, $key)
    {
        return sprintf("%s/%s", $this->_getCacheDir($namespace, $key), $this->_escape($key));
    }

    /**
     *  キーをファイルシステム用にエスケープする
     *
     *  @access private
     */
    function _escape($string)
    {
        return preg_replace_callback('/([^0-9A-Za-z_])/', function($m){
            return sprintf('%%%02X', ord($m[1]));
        }, $string);
    }
}
?>
