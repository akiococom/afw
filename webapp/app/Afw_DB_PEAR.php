<?php
require_once BASE . '/lib/Ethna/class/DB/Ethna_DB_PEAR.php';

/**
 *  参照カウンタ付きDB接続オブジェクトコンテナ
 */
class Afw_DB_PEAR_Connection
{
    /** @var DB_common */
    private $instance;
    /** @var int */
    private $refcount;

    /**
     *  コンストラクタ
     *  
     *  @param DB_common $instance  DB接続オブジェクトのインスタンス
     */
    public function __construct(DB_common $instance)
    {
        $this->instance = $instance;
        $this->refcount = 1;
    }

    /**
     *  インスタンスの参照を取得し、参照カウントをインクリメント
     *  
     *  @return DB_common
     */
    public function get()
    {
        ++$this->refcount;
        return $this->instance;
    }

    /**
     *  参照カウントをデクリメントし、現在の参照カウント数を取得
     *  
     *  @return int
     */
    public function release()
    {
        return --$this->refcount;
    }
}

/**
 *  Ethna_DBクラスの実装(PEAR版)
 *
 *  @author     Masaki Fujimoto <fujimoto@php.net>
 *  @access     public
 *  @package    Ethna
 */
class Afw_DB_PEAR extends Ethna_DB_PEAR
{
    /** @var Afw_DB_PEAR_Connection[] */
    private static $pool = array();

    /**
     *  DBに接続する
     *  
     *  @access public
     *  @return mixed   0:正常終了 Ethna_Error:エラー
     */
    function connect()
    {
    	if (!$this->dsn) {
    		return false;
    	}
        if (isset(self::$pool[$this->dsn])) {
            // 接続プールから取得して参照カウント追加
            $this->db = self::$pool[$this->dsn]->get();
            return 0;
        }
        // プールに無ければインスタンス生成
        $r = parent::connect();
        if (Ethna::isError($r)) {
            return $r;
        }
        $this->onConnectSuccess();
        // 接続プールに追加
        self::$pool[$this->dsn] = new Afw_DB_PEAR_Connection($this->db);
        return 0;
    }

    /**
     *  DB接続を切断する
     *  
     *  @access public
     */
    function disconnect()
    {
    	if (!$this->dsn) {
    		return false;
    	}
        assert('isset(self::$pool[$this->dsn])');
        if (self::$pool[$this->dsn]->release() > 0) {
            // 参照カウントが残っていたら切断しない
            return;
        }
        unset(self::$pool[$this->dsn]);
        parent::disconnect();
    }

    /**
     *  DB接続成功時に行う処理
     */
    private function onConnectSuccess()
    {
        // MySQL の接続文字コード設定
        $this->query('SET CHARACTER SET utf8mb4;');
    }
}
