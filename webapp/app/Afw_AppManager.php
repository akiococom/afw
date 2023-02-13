<?php
/**
 *  Afw_AppManager
 *
 *  @author     A.Ohnishi
 *  @package    Afw
 *  @version    20171104
 */

// Exception用エラーコード
define('ERROR_CODE_DUPLICATE', 1);
define('ERROR_CODE_LENGTHOVER', 2);

/**
 *  Afw_AppManager
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Afw
 */
class Afw_AppManager extends Ethna_AppManager
{
	/** @var Afw_UtilsManager */
	protected $utils;
	
	var $logMode = null;
	
	/**
	 * コンストラクタ
	 */
	public function Afw_AppManager(&$backend)
	{
		$this->utils = $backend->getManager('utils');
		parent::__construct($backend);
	}
	
	/**
	 * PEAR の戻り値を検証して PEAR_Error の場合は例外として送出する
	 * 
	 * @param mixed $pearResult  PEAR のメソッドの戻り値
	 * @return 正常時は引数をそのまま返却
	 * @throws PearException
	 */
	private function ensure($pearResult)
	{
		if (PEAR::isError($pearResult)) {
			return false;
			//throw new PearException($pearResult);
		}
		return $pearResult;
	}
	
	/**
	 * DBクエリー実行
	 * 
	 * @param string $sql
	 * @param array  $params
	 * @return mixed
	 * @throws PearException
	 */
	public function query($sql, array $params = array(), $debug = false)
	{
		assert('is_string($sql)');
		$r = $this->db->db->query($sql, $params);
		if ($r->userinfo) {
			//var_dump($r->userinfo);
			$this->writeLog($r->userinfo);
		}
		return $this->ensure($r);
	}
	
	/**
	 * DBから全レコード取得
	 * 
	 * @param string $sql
	 * @param array  $params
	 * @return
	 */
	public function getAll($sql, array $params = array(), $debug = false)
	{
		assert('is_string($sql)');
		$r = $this->db->db->getAll($sql, DB_FETCHMODE_ASSOC, $params);
		if ($r->userinfo) {
			//var_dump($r->userinfo);
			$this->writeLog($r->userinfo);
		}
		return $this->ensure($r);
	}
	
	/**
	 * DBから１レコード取得
	 * 
	 * @param string $sql
	 * @param array  $params
	 * @return array
	 * @throws PearException
	 */
	public function getRow($sql, array $params = array(), $debug = false)
	{
		assert('is_string($sql)');
		$r = $this->db->db->getRow($sql, DB_FETCHMODE_ASSOC, $params);
		if ($r->userinfo) {
			//var_dump($r->userinfo);
			$this->writeLog($r->userinfo);
		}
		return $this->ensure($r);
	}
	
	/**
	 * DBから１フィールド取得
	 * 
	 * @param string $sql
	 * @param array  $params
	 * @return mixed
	 * @throws PearException
	 */
	public function getOne($sql, array $params = array(), $debug = false)
	{
		assert('is_string($sql)');
		$r = $this->db->db->getOne($sql, $params);
		if ($r->userinfo) {
			//var_dump($r->userinfo);
			$this->writeLog($r->userinfo);
		}
		return $this->ensure($r);
	}
	
	/**
	 * データベースから指定した行の配列を取得
	 * 
	 * @param string $sql
	 * @param array  $params
	 * @param int    $offset
	 * @param int    $count
	 * @return array(count, array)
	 * @throws PearException
	 */
	public function getPage($sql, array $params = array(), $offset = 0, $count = 10, $debug = false)
	{
		$result = $this->query($sql, $params, $debug);
		
		if ($offset === null && $count === null) {
			$offset = 0;
			$count  = $result->numRows();
		}
		$rows = array();
		for ($i = 0; $i < $count; $i++) {
			$row = null;
			if (!$result->fetchInto($row, DB_FETCHMODE_ASSOC, $offset + $i)) {
				break;
			}
		    $rows[] = $row;
		}
		return array($result->numRows(), $rows);
	}
	
	
	/**
	 * DBフィールド１件取得
	 * 
	 * @param string  $infokey    マネージャクラス先頭で指定したDB設定配列のキー名
	 * @param string  $fieldname  取得するフィールド名
	 * @param array   $conditions array('フィールド' => '値')
	 * @return array
	 * @throws PearException
	 */
	public function selectOneByConditions($infokey, $fieldname, array $conditions)
	{
		assert('isset($this->dbinfo[$infokey])');
		
		$dbinfo = $this->dbinfo[$infokey];
		
		$sql = sprintf('SELECT %s FROM %s WHERE %s',
			$fieldname, $dbinfo['tablename'], self::where($conditions)
			);
		$params = array_values($conditions);
		
		return $this->getOne($sql, $params);
	}
	
	/**
	 * DBレコード１件取得
	 * 
	 * SELECT * FROM {table} WHERE {conditions} または
	 * 
	 * SELECT * FROM {table} WHERE {conditions} ORDER BY {$orderBy} LIMIT 1
	 * 
	 * @param string  $infokey    マネージャクラス先頭で指定したDB設定配列のキー名
	 * @param array   $conditions array('フィールド' => '値')
	 * @param string  $orderBy    複数件から先頭１件を選択する際の ORDER BY 句
	 * @return array
	 * @throws PearException
	 */
	public function selectRowByConditions($infokey, array $conditions, $orderBy = null)
	{
		assert('isset($this->dbinfo[$infokey])');
		
		$dbinfo = $this->dbinfo[$infokey];
		
		$sql = sprintf('SELECT * FROM %s WHERE %s',
			$dbinfo['tablename'], self::where($conditions)
			);
		if ($orderBy !== null) {
			$sql .= sprintf(' ORDER BY %s LIMIT 1', $orderBy);
		}
		$params = array_values($conditions);
		
		return $this->getRow($sql, $params);
	}
	
	/**
	 * DBレコード一覧取得
	 * 
	 * @param string $infokey    マネージャクラス先頭で指定したDB設定配列のキー名
	 * @param array  $conditions array('フィールド' => '値')
	 * @param string $orderBy    ORDER BY 句 (省略時はプライマリキー昇順)
	 * @return array
	 * @throws PearException
	 */
	public function selectAllByConditions($infokey, array $conditions, $orderBy = null)
	{
		assert('isset($this->dbinfo[$infokey])');
		assert('is_null($orderBy) || is_string($orderBy)');
		
		$dbinfo = $this->dbinfo[$infokey];
		
		if ($orderBy === null) {
			$orderBy = $dbinfo['keyname'] . ' ASC';
		}
		$sql = sprintf('SELECT * FROM %s WHERE %s ORDER BY %s',
			$dbinfo['tablename'], self::where($conditions), $orderBy
			);
		$params = array_values($conditions);
		
		return $this->getAll($sql, $params);
	}
	
	/**
	 * DBレコード１件取得
	 * 
	 * @param string $infokey マネージャクラス先頭で指定したDB設定配列のキー名
	 * @param int    $id      array('フィールド' => '値')
	 * @return array
	 * @throws PearException
	 */
	public function select($infokey, $id)
	{
		assert('isset($this->dbinfo[$infokey]) && is_scalar($id)');
		
		$dbinfo = $this->dbinfo[$infokey];
		
		return $this->selectRowByConditions($infokey, array($dbinfo['keyname'] => $id));
	}
	
	/**
	 * DBレコード一覧取得
	 * 
	 * @param string $infokey    マネージャクラス先頭で指定したDB設定配列のキー名
	 * @param string $orderBy    ORDER BY 句 (省略時はプライマリキー順)
	 * @return array
	 * @throws PearException
	 */
	public function selectAll($infokey, $orderBy = null)
	{
		assert('isset($this->dbinfo[$infokey])');
		assert('is_null($orderBy) || is_string($orderBy)');
		
		return $this->selectAllByConditions($infokey, array(), $orderBy);
	}
	
	/**
	 * DBレコード挿入
	 * 
	 * @param string $infokey マネージャクラス先頭で指定したDB設定配列のキー名
	 * @param array  $params  array('フィールド' => '値')
	 * @return int 挿入したレコードのプライマリキーID
	 */
	public function insert($infokey, array $params, $debug = false)
	{
		assert('isset($this->dbinfo[$infokey]) && !empty($params)');
		
		$dbinfo = $this->dbinfo[$infokey];
		
		// 更新日時・登録日時
		$currentDateTime = $this->utils->getCurrentDateTime();
		if (!empty($dbinfo['modifiedname'])) {
			if (!isset($params[$dbinfo['modifiedname']])) {
				$params[$dbinfo['modifiedname']] = $currentDateTime;
			}
		}
		if (!empty($dbinfo['createdname'])) {
			if (!isset($params[$dbinfo['createdname']])) {
				$params[$dbinfo['createdname']] = $currentDateTime;
			}
		}
		
		// SQL生成
		$fieldnames = array_keys($params);
		$placeholders = array_fill(0, count($params), '?');
		$sql = sprintf('INSERT INTO %s (%s) VALUES (%s)',
			$dbinfo['tablename'],
			'`' . implode('`, `', $fieldnames) . '`',
			implode(', ', $placeholders)
		);
		
		$this->query($sql, $params, $debug);
		
		return $this->getOne('SELECT LAST_INSERT_ID() AS id');
	}
	
	/**
	 * DBレコード更新
	 * 
	 * @param string $infokey    マネージャクラス先頭で指定したDB設定配列のキー名
	 * @param array  $params     array('フィールド' => '値')
	 * @param array  $conditions array('フィールド' => '値')
	 * @return bool
	 */
	public function updateByConditions($infokey, array $params, array $conditions = array(), $debug = null)
	{
		assert('isset($this->dbinfo[$infokey]) && !empty($params)');
		
		$dbinfo = $this->dbinfo[$infokey];
		
		// 更新日時
		$currentDateTime = $this->utils->getCurrentDateTime();
		if (!empty($dbinfo['modifiedname'])) {
			$params[$dbinfo['modifiedname']] = $currentDateTime;
		}
		
		// SQL生成
		$setExprs = array_map(array(__CLASS__, 'makeEqualExpr'), array_keys($params));
		$sql = sprintf('UPDATE %s SET %s WHERE %s',
			$dbinfo['tablename'], implode(', ', $setExprs), self::where($conditions)
			);
		$params = array_merge(
			array_values($params),
			array_values($conditions)
			);

		return $this->query($sql, $params, $debug);
	}
	
	/**
	 * DBレコード更新
	 * 
	 * @param string $infokey マネージャクラス先頭で指定したDB設定配列のキー名
	 * @param int    $id      プライマリキーID
	 * @param array  $params  array('フィールド' => '値')
	 * @return bool
	 */
	public function update($infokey, $id, array $params, $debug = null)
	{
		assert('isset($this->dbinfo[$infokey]) && is_scalar($id)');
		
		$dbinfo = $this->dbinfo[$infokey];
		
		$this->updateByConditions(
			$infokey, $params, array($dbinfo['keyname'] => $id), $debug
		);
		
		return $id;
	}
	
	/**
	 * DBレコード削除
	 * 
	 * @param string $infokey    マネージャクラス先頭で指定したDB設定配列のキー名
	 * @param array  $conditions array('フィールド' => '値')
	 * @throws PearException
	 */
	public function deleteByConditions($infokey, array $conditions)
	{
		assert('isset($this->dbinfo[$infokey])');
		
		$dbinfo = $this->dbinfo[$infokey];
		
		$sql = sprintf('DELETE FROM %s WHERE %s',
			$dbinfo['tablename'], self::where($conditions)
			);
		$params = array_values($conditions);
		
		$this->query($sql, $params);
	}
	
	/**
	 * DBレコード削除
	 * 
	 * @param string $infokey マネージャクラス先頭で指定したDB設定配列のキー名
	 * @param int    $id      プライマリキーID
	 * @return bool
	 */
	public function delete($infokey, $id)
	{
		assert('isset($this->dbinfo[$infokey])');
		
		$dbinfo = $this->dbinfo[$infokey];
		
		$this->deleteByConditions($infokey, array($dbinfo['keyname'] => $id));
	}
	
	/**
	 * WHERE句を生成
	 * 
	 * @param array $conditions array('フィールド' => '値')
	 * @return string
	 */
	public static function where(array $conditions)
	{
		if (empty($conditions)) {
			return 'TRUE';
		}
		$whereExprs = array_map(array(__CLASS__, 'makeEqualExpr'), array_keys($conditions));
		return implode(' AND ', $whereExprs);
	}
	
	/**
	 * SQL文のイコール表現を生成 (SET句、WHERE句などで利用)
	 * 
	 * @param string $fieldname
	 * @return string "$fieldname = ?"
	 */
	public static function makeEqualExpr($fieldname)
	{
		return $fieldname . ' = ?';
	}
	
	/**
	 * PDO スタイルのクエリとパラメータを PEAR スタイルのクエリとパラメータに変換
	 * 
	 * $sql = "SELECT * FROM table WHERE id = :id"; $params = array(':id' => $id);
	 *      ↓
	 * $sql = "SELECT * FROM table WHERE id = ?"; $params = array($id);
	 * 
	 * @param string $pdoQuery
	 * @param array $pdoParams
	 * @return array($sql, $params)
	 */
	public static function makePearSqlAndParams($pdoQuery, array $pdoParams)
	{
		$pearParams = array();
		if (preg_match_all('/:\w+/', $pdoQuery, $matches, PREG_PATTERN_ORDER)) {
			foreach ($matches[0] as $placeholder)
				$pearParams[] = $pdoParams[$placeholder];
		}
		$pearSql = preg_replace('/:\w+/', '?', $pdoQuery);
		return array($pearSql, $pearParams);
	}
	
	/**
	 * クエリパラメータエスケープ (SQLインジェクション対策)
	 * 
	 * @param string $string
	 * @return string
	 */
	public static function escapeSqlParam($string)
	{
		return strtr($string, array(
			"'" => "\\'",
			';' => '\\;',
			));
	}
	
	/**
	 * ORDER BY 句の正当性を検証 (SQLインジェクション対策)
	 * 
	 * @param string $orderBy
	 * @throws InvalidArgumentException
	 */
	public static function validateOrderByExpr($orderBy)
	{
		foreach (explode(',', $orderBy) as $expr) {
			if (!preg_match('/^\s*(\w+\.)?(\w+)(\s+(ASC|DESC))?\s*$/i', $expr))
				throw new InvalidArgumentException();
		}
	}
	
    /**
     * ログ書き出し
     * 
     * @param	string	$string
     * @param	string	$directory
     * @return
     */
    function writeLog($string = '', $mode = null, $directory = null, $filename = null)
    {
    	// ログ整形
    	if (is_null($filename)) {
    		if ($this->logMode) {
    			$filename = $this->logMode . '_';
    		} else {
    			$filename = '';
    		}
    		$filename .= date('Ymd') . '.log';
    	}
    	//echo '<xmp>'; var_dump($filename); echo '</xmp>';
    	$log = date('Y-m-d H:i:s') . "\t" . $this->utils->getHost() . ($this->session->get('user_id') ? ("\t" . $this->session->get('user_login')) : '') . "\t" . $string . "\n";
		if (is_null($directory) && $this->config->get('log_directory')) {
			$directory = $this->config->get('log_directory');
		}
		if ($directory) {
			file_put_contents($directory . $filename, $log, FILE_APPEND);
			@chmod($directory . date('Ymd') . '.log', 0777);
		}
    }    
	
    /**
     * デフォルト値を設定
     * 
     * @param	mixed	$source
     * @param	mixed	$initial
     * @return	mixed
     */
    function initval($source, $initial = 0)
    {
    	return ($source)?$source:$initial;
    }
    
	/**
	 * LIKE検索用文字列
	 * 
	 * @param	string	$param
	 * @return	string
	 */
	function like($param)
	{
		// 最初から%が含まれていれば % は付加しない
		if (strpos($param, '%') !== false) {
			return $param;
		} else {
			return '%' . $param . '%';
		}
	}

    /**
     * 検索文字列を配列に分割
     * 
     * @param	string	$query
     * @return	array
     */
    function explodeQuery($query)
    {
    	// sample
    	/*
		if ($q) {
			$queries = $this->explodeQuery($q);
			if (is_array($queries)) {
				foreach ($queries as $q) {
					$params[] = $this->like($q);
					$wheres[] = '(xxx LIKE ? AND xxx LIKE ?)';
				}
			}
		}
    	*/
		mb_regex_encoding('UTF-8');
		$query = str_replace('　', ' ', $query);
    	return mb_split("[\s,]+", $query);
    }
    
 
	/**
	 * SQLインジェクション対応文字列
	 *
	 * @param	string	$sql
	 * @return	string
	 */
	function sql($sql)
	{
		return str_replace(array('\'', ';'), '', $sql);
	}
	
	// 1件取得のエイリアス
	function get($tableName, $tableId, $isIncludeRemoved = false)
	{
		$key = $this->dbinfo[$tableName]['keyname'];
		$params = array((int)$tableId);
		$sql = 'SELECT *'
			 . ' FROM `' . $this->sql($tableName) . '`'
			 . ' WHERE ' . $this->sql($key) . ' = ?'
			 . ($isIncludeRemoved ? '' : ' AND is_removed = 0');
		return $this->getRow($sql, $params);
	}
	
	// 削除のエイリアス
	function remove($tableName, $tableId)
	{
		$params = array(
			'is_removed' => 1 
		);
		return $this->update($tableName, $tableId, $params);
	}
	
	// 登録のエイリアス
	// $xxxFunc は代入する$paramsを引数として処理して戻り値として返す。
	function set($tableName, $tableId, $params, $insertFunc = null, $updateFunc = null)
	{
		if ($tableId) {
			if ($updateFunc) {
				$params = $updateFunc($params);
			}
			return $this->update($tableName, $tableId, $params);
		} else {
			if ($insertFunc) {
				$params = $insertFunc($params);
			}
			return $this->insert($tableName, $params);
		}
	}
	
	// 1項目をアップデート
	function updateParam($tableName, $tableId, $key, $value = null)
	{
		if (!is_array($key)) {
			$key = array($key);
		}
		
		foreach ($key as $k) {
			if (is_null($value)) {
				$params[$k] = $this->af->get($k);
			} else {
				$params[$k] = $value;
			}
		}
		
		return $this->update($tableName, $tableId, $params);
	}
	
	// DB登録時の配列を自動生成
	function params($tableName, $removeKeys = array())
	{
		$sql = 'DESCRIBE ' . $tableName;
		$fields = $this->getAll($sql);
		
		// キー削除
		$removeFields = array(
			$this->dbinfo[$tableName]['keyname'],
			'is_removed',
		);
		if ($this->dbinfo[$tableName]['modifiedname']) {
			$removeFields[] = $this->dbinfo[$tableName]['modifiedname'];
		}
		if ($this->dbinfo[$tableName]['createdname']) {
			$removeFields[] = $this->dbinfo[$tableName]['createdname'];
		}
		
		$params = array();
		if (is_array($fields)) {
			foreach ($fields as $f) {
				if (
					in_array($f['Field'], $removeFields)
					|| in_array($f['Field'], $removeKeys)
				) {
					continue;
				}
				if (strpos($f['Type'], 'int') !== false) {
					$params[$f['Field']] = (int)$this->af->get($f['Field']);
				} elseif (
					(strpos($f['Type'], 'char') !== false)
					|| (strpos($f['Type'], 'text') !== false)
				) {
					$params[$f['Field']] = (string)$this->af->get($f['Field']);
				} else {
					$params[$f['Field']] = $this->af->get($f['Field']);
				}
			}
		}
		
		return $params;
	}
}
