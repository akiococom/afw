<?php
// vim: foldmethod=marker tabstop=4 shiftwidth=4 autoindent
/**
 *  Ethna_Plugin_Cachemanager_Memcache.php
 *
 *  - Point Cutしたいと思った！
 *  - キャッシュキーには250文字までしか使用できないので注意して下さい
 *
 *  @todo   ネームスペース/キャッシュキー長のエラーハンドリング
 *
 *  @author     Masaki Fujimoto <fujimoto@php.net>
 *  @package    Ethna
 *  @version    $Id: Ethna_Plugin_Cachemanager_Memcache.php 416 2006-11-17 08:41:54Z ichii386 $
 */

/**
 *  キャッシュマネージャクラス(memcache版)
 *
 *  @author     Masaki Fujimoto <fujimoto@php.net>
 *  @access     public
 *  @package    Ethna
 */
class Ethna_Plugin_Cachemanager_Memcache extends Ethna_Plugin_Cachemanager
{
    /**#@+  @access private */

    /** @var    object  MemCache    MemCacheオブジェクト */
    var $memcache = null;

    /** @var bool 圧縮フラグ */
    var $compress = true;

    /**#@-*/

    /**
     *  Ethna_Plugin_Cachemanager_Memcacheクラスのコンストラクタ
     *
     *  @access public
     */
    function __construct()
    {
        parent::__construct();
        $this->memcache_pool = array();
    }

    /**
     *  memcacheキャッシュオブジェクトを生成、取得する
     *
     *  @access protected
     */
    function _getMemcache($cache_key, $namespace = null)
    {
        $retry = $this->config->get('memcache_retry');
        if ($retry == "") {
            $retry = 3;
        }
        $timeout = $this->config->get('memcache_timeout');
        if ($timeout == "") {
            $timeout = 3;
        }
        $r = false;

        list($host, $port) = $this->_getMemcacheInfo($cache_key, $namespace);
        if (isset($this->memcache_pool["$host:$port"])) {
            // activate
            $this->memcache = $this->memcache_pool["$host:$port"];
            return $this->memcache;
        }
        $this->memcache_pool["$host:$port"] = new MemCache();

        while ($retry > 0) {
            if ($this->config->get('memcache_use_connect')) {
                $r = $this->memcache_pool["$host:$port"]->connect($host, $port, $timeout);
            } else {
                $r = $this->memcache_pool["$host:$port"]->pconnect($host, $port, $timeout);
            }
            if ($r) {
                break;
            }
            sleep(1);
            $retry--;
        }
        if ($r == false) {
            trigger_error("memcache: connection failed");
            $this->memcache_pool["$host:$port"] = null;
        }

        $this->memcache = $this->memcache_pool["$host:$port"];
        return $this->memcache;
    }

    /**
     *  memcache接続情報を取得する
     *
     *  @access protected
     *  @todo   $cache_keyから$indexを決める方法を変更できるようにする
     */
    function _getMemcacheInfo($cache_key, $namespace)
    {
        $namespace = is_null($namespace) ? $this->namespace : $namespace;

        $memcache_info = $this->config->get('memcache');
        $default_memcache_host = $this->config->get('memcache_host');
        if ($default_memcache_host == "") {
            $default_memcache_host = "localhost";
        }
        $default_memcache_port = $this->config->get('memcache_port');
        if ($default_memcache_port == "") {
            $default_memcache_port = 11211;
        }
        if ($memcache_info == null || isset($memcache_info[$namespace]) == false) {
            return array($default_memcache_host, $default_memcache_port);
        }

        // namespace/cache_keyで接続先を決定
        $n = count($memcache_info[$namespace]);

        $index = $cache_key % $n;
        return array(
            isset($memcache_info[$namespace][$index]['memcache_host']) ?
                $memcache_info[$namespace][$index]['memcache_host'] :
                'localhost',
            isset($memcache_info[$namespace][$index]['memcache_port']) ?
                $memcache_info[$namespace][$index]['memcache_port'] :
                11211,
        );

        // for safe
        return array($default_memcache_host, $default_memcache_port);
    }

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
        $this->_getMemcache($key, $namespace);
        if ($this->memcache == null) {
            return Ethna::raiseError('memcache server not available', E_CACHE_NO_VALUE);
        }

        $namespace = is_null($namespace) ? $this->namespace : $namespace;

        $cache_key = $this->_getCacheKey($namespace, $key);
        if ($cache_key == null) {
            return Ethna::raiseError('invalid cache key (too long?)', E_CACHE_NO_VALUE);
        }

        $value = $this->memcache->get($cache_key);
        if ($value == null) {
            return Ethna::raiseError('no such cache', E_CACHE_NO_VALUE);
        }
        $time = $value['time'];
        $data = $value['data'];

        // ライフタイムチェック
        if (is_null($lifetime) == false) {
            if (($time+$lifetime) < time()) {
                return Ethna::raiseError('lifetime expired', E_CACHE_EXPIRED);
            }
        }

        return $data;
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
        $this->_getMemcache($key, $namespace);
        if ($this->memcache == null) {
            return Ethna::raiseError('memcache server not available', E_CACHE_NO_VALUE);
        }

        $namespace = is_null($namespace) ? $this->namespace : $namespace;

        $cache_key = $this->_getCacheKey($namespace, $key);
        if ($cache_key == null) {
            return Ethna::raiseError('invalid cache key (too long?)', E_CACHE_NO_VALUE);
        }

        $value = $this->memcache->get($cache_key);

        return $value['time'];
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
        $r = $this->get($key, $lifetime, $namespace);

        return PEAR::isError($r) ? false: true;
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
        $this->_getMemcache($key, $namespace);
        if ($this->memcache == null) {
            return Ethna::raiseError('memcache server not available', E_CACHE_NO_VALUE);
        }

        $namespace = is_null($namespace) ? $this->namespace : $namespace;

        $cache_key = $this->_getCacheKey($namespace, $key);
        if ($cache_key == null) {
            return Ethna::raiseError('invalid cache key (too long?)', E_CACHE_NO_VALUE);
        }

        $time = $timestamp ? $timestamp : time();
        $this->memcache->set($cache_key, array('time' => $time, 'data' => $value), $this->compress ? MEMCACHE_COMPRESSED : null);
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
        $this->_getMemcache($key, $namespace);
        if ($this->memcache == null) {
            return Ethna::raiseError('memcache server not available', E_CACHE_NO_VALUE);
        }

        $namespace = is_null($namespace) ? $this->namespace : $namespace;

        $cache_key = $this->_getCacheKey($namespace, $key);
        if ($cache_key == null) {
            return Ethna::raiseError('invalid cache key (too long?)', E_CACHE_NO_VALUE);
        }

        $this->memcache->delete($cache_key, -1);
    }

    /**
     *  キャッシュデータをロックする
     *
     *  @access public
     *  @param  string  $key        キャッシュキー
     *  @param  int     $timeout    ロックタイムアウト
     *  @param  string  $namespace  キャッシュネームスペース
     *  @return bool    true:成功 false:失敗
     */
    function lock($key, $timeout = 5, $namespace = null)
    {
        $this->_getMemcache($key, $namespace);
        if ($this->memcache == null) {
            return Ethna::raiseError('memcache server not available', E_CACHE_LOCK_ERROR);
        }

        // ロック用キャッシュデータを利用する
        $namespace = is_null($namespace) ? $this->namespace : $namespace;
        $cache_key = "lock::" . $this->_getCacheKey($namespace, $key);
        $lock_lifetime = 30;

        do {
            $r = $this->memcache->add($cache_key, true, false, $lock_lifetime);
            if ($r != false) {
                break;
            }
            sleep(1);
            $timeout--;
        } while ($timeout > 0);

        if ($r == false) {
            return Ethna::raiseError('lock timeout', E_CACHE_LOCK_TIMEOUT);
        }

        return true;
    }

    /**
     *  キャッシュデータのロックを解除する
     *
     *  @access public
     *  @param  string  $key        キャッシュキー
     *  @param  string  $namespace  キャッシュネームスペース
     *  @return bool    true:成功 false:失敗
     */
    function unlock($key, $namespace = null)
    {
        $this->_getMemcache($key, $namespace);
        if ($this->memcache == null) {
            return Ethna::raiseError('memcache server not available', E_CACHE_LOCK_ERROR);
        }

        $namespace = is_null($namespace) ? $this->namespace : $namespace;
        $cache_key = "lock::" . $this->_getCacheKey($namespace, $key);

        $this->memcache->delete($cache_key, -1);
    }

    /**
     *  ネームスペースからキャッシュキーを生成する
     *
     *  @access private
     */
    function _getCacheKey($namespace, $key)
    {
        // 少し乱暴だけど...
        $key = str_replace(":", "_", $key);
        $cache_key = $namespace . "::" . $key;
        if (strlen($cache_key) > 250) {
            return null;
        }
        return $cache_key;
    }

    /**
     * 圧縮フラグを立てる
     *
     * MySQLなどいくつかの子クラスで有効
     * 
     * @access public
     * @param bool $flag フラグ
     */
    function setCompress($flag) {
        $this->compress = $flag;
    }
}
?>
