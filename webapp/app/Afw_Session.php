<?php
/**
 *  DBに保持するセッション
 *  
 *  テーブル名は etc/*-ini.php の 'session_table' => 'テーブル名' で設定
 *  
 *  [セッションテーブル定義]
 *  CREATE TABLE IF NOT EXISTS `sessions` (
 *    `session_id` varchar(255) NOT NULL,
 *    `modified_at` int(10) NOT NULL,
 *    `remote_addr` varchar(255) NOT NULL,
 *    `session_data` text NOT NULL,
 *    PRIMARY KEY (`session_id`)
 *  ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
 */
class Afw_Session extends Ethna_Session
{
    /** @var string */
    private $tableName;

    /** @var Ethna_DB */
    private $db;

    /**
     *  コンストラクタ
     *  
     *  @param string $appid
     *  @param string $save_dir
     *  @param Ethna_Logger $logger
     */
    public function Afw_Session($appid, $save_dir, $logger)
    {
        $controller = Ethna_Controller::getInstance();
        if ($this->tableName = $controller->config->get('session_table')) {
            // DBオブジェクトのインスタンスを直接生成
            $dsn = $controller->getDSN();
            $persistent = $controller->getDSN_persistent();
            $class_factory = $controller->getClassFactory();
            $db_class_name = $class_factory->getObjectName('db');
            $this->db = new $db_class_name($controller, $dsn, $persistent);
            $result = $this->db->connect();
            if (Ethna::isError($result)) {
                error_log('Database connection refused');
                die;
            }

            // セッションをDBに保存するハンドラを登録
            ini_set('session.save_handler', 'user');
            session_set_save_handler(
                array($this, '_session_open'),
                array($this, '_session_close'),
                array($this, '_session_read'),
                array($this, '_session_write'),
                array($this, '_session_destroy'),
                array($this, '_session_gc')
                );

            // クラスインスタンスが無効になる前にセッションを
            // 破棄するように明示的にフックを登録
            // (これを行わないとセッション破棄コールバックが
            //  呼び出される時には既にクラスインスタンスが
            //  生存していないため致命的なエラーが発生する)
            // 
            // また、Ethna においては Ethna_Backend->shutdownDB()
            // によって早期にDBの接続が切断され、それよりも
            // 前にセッション破棄処理が実行される必要があるため、
            // デストラクタではなく register_shutdown_function() を利用
            register_shutdown_function(array($this, '_shutdown'));
        }
        parent::__construct($appid, $save_dir, $logger);
    }

    /**
     *  シャットダウンコールバック
     */
    public function _shutdown()
    {
        // セッションを破棄してからDBを切断
        session_write_close();
        $this->db->disconnect();
    }

    /**
     *  セッション open コールバック
     *  
     *  @param string $savePath
     *  @param string $sessionName
     *  @return boolean  true:成功, false:失敗
     */
    public function _session_open($savePath, $sessionName)
    {
        // コンストラクタで既にDBに接続しているので、ここでは何もしない
        return true;
    }

    /**
     *  セッション close コールバック
     *  
     *  @return boolean  true:成功, false:失敗
     */
    public function _session_close()
    {
        return true;
    }

    /**
     *  セッション read コールバック
     *  
     *  @param string $sessionId
     *  @param string シリアライズされたセッションデータ
     */
    public function _session_read($sessionId)
    {
        $sql = "SELECT session_data FROM $this->tableName WHERE session_id = ? LIMIT 1";
        $params = array($sessionId);
        $result = $this->db->db->getOne($sql, $params);
        if (!Ethna::isError($result)) {
            return $result;
        }
        return '';
    }

    /**
     *  セッション write コールバック
     *  
     *  @param string $sessionId
     *  @param string $data シリアライズされたセッションデータ
     */
    public function _session_write($sessionId, $data)
    {
        $sql = "REPLACE INTO $this->tableName (session_id, modified_at, remote_addr, session_data) VALUES (?, ?, ?, ?)";
        $params = array($sessionId, time(), $_SERVER['REMOTE_ADDR'], $data);
        $result = $this->db->db->query($sql, $params);
        return !Ethna::isError($result);
    }

    /**
     *  セッション destroy コールバック
     *  
     *  @param string $sessionId
     *  @return boolean 
     */
    public function _session_destroy($sessionId)
    {
        setcookie($this->session_name, '', 0, '/');
        $sql = "DELETE FROM $this->tableName WHERE session_id = ? LIMIT 1";
        $params = array($sessionId);
        $result = $this->db->db->query($sql, $params);
        return !Ethna::isError($result);
    }

    /**
     *  セッション gc コールバック
     *  
     *  @param int $lifetime 
     */
    public function _session_gc($lifetime)
    {
        $sql = "DELETE FROM $this->tableName WHERE modified_at < ?";
        $params = array(time() - $lifetime);
        $result = $this->db->db->query($sql, $params);
        return !Ethna::isError($result);
    }


    /**
     *  セッションの正当性チェック
     *
     *  @access public
     *  @return bool    true:正当なセッション false:不当なセッション
     */
    function isValid()
    {
        if (!$this->session_start) {
            if (!empty($_COOKIE[$this->session_name]) || session_id() != null) {
                setcookie($this->session_name, "", 0, "/");
            }
            return false;
        }

        return true;
    }
}
