<?php
/**
 *  Tool/Db/Templater.php
 *
 *  @author     akio.co.com
 *  @package    Afw
 *  @version    1.0
 */

define('TAB', "\n");

/**
 *  tool_db_templaterフォームの実装
 *
 *  @author     akio.co.com
 *  @access     public
 *  @package    Afw
 */
class Afw_Cli_Form_ToolDbTemplater extends Afw_ActionForm
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
 *  tool_db_templaterアクションの実装
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Afw
 */
class Afw_Cli_Action_ToolDbTemplater extends Afw_ActionClass
{
    /**
     *  tool_db_templaterアクションの前処理
     *
     *  @access public
     *  @return string      遷移名(正常終了ならnull, 処理終了ならfalse)
     */
    function prepare()
    {
        return null;
    }

    /**
     *  tool_db_templaterアクションの実装
     *
     *  @access public
     *  @return string  遷移名
     */
    function perform()
    {
    	//	・使い方
    	//	DBからテーブル情報を取得してプログラムのひながたを作る
    	//		php -f bin/tool_db_templater.php [オブジェクト名(*users)] [modifiedname(*m_datetime)] [createdname(*r_datetime)]
    	global $argv;
    	
    	$tableNames = $this->getTableNames();
    	
    	// テーブル設定
    	foreach ((array)$tableNames as $tableName) {
    		$fields = $this->getFields($tableName);    		
			printf("		'%s'	=> array(" . PHP_EOL
			. "			'tablename'		=> '%s'," . PHP_EOL
			. "			'keyname'		=> '%s'," . PHP_EOL
			. "			'modifiedname'	=> '%s'," . PHP_EOL
			. "			'createdname'	=> '%s'," . PHP_EOL
			. "		)," . PHP_EOL
			, $tableName, $tableName, $fields['PRIMARY'], $argv[2] ? $argv[2] : 'm_datetime', $argv[3] ? $argv[3] : 'r_datetime'
			);
    	}
    	
    	// 基本SQLアクション
    	foreach ((array)$tableNames as $tableName) {
    		echo $tableName . PHP_EOL;
    		echo '----------------------------------------' . PHP_EOL;
    		
    		$fields = $this->getFields($tableName);
    		
    		printf("   		\$this->%s->get('%s', (int)\$this->af->get('%s'));" . PHP_EOL . PHP_EOL
    		, $argv[1] ? $argv[1] : 'users', $tableName, $fields['PRIMARY']);
    		
    		
    		printf("   		\$this->%s->set('%s', (int)\$this->af->get('%s'), array(" . PHP_EOL
    		, $argv[1] ? $argv[1] : 'users', $tableName, $fields['PRIMARY']);
    		
    		
    		foreach ((array)$fields as $field => $types) {
    			if ($field == 'PRIMARY') {
    				continue;
    			}
    			if (strpos($field, 'user_id') !== false) {
					printf("   			'%s' => %s\$this->session->get('%s')," . PHP_EOL, $field, $types[0], $field);
    			} else {
					printf("   			'%s' => %s\$this->af->get('%s')," . PHP_EOL, $field, $types[0], $field);
    			}
    		}
    		echo "   			));" . PHP_EOL . PHP_EOL;
    		
    		
    		printf("   		\$this->%s->remove('%s', (int)\$this->af->get('%s'));" . PHP_EOL . PHP_EOL
    		, $argv[1] ? $argv[1] : 'users', $tableName, $fields['PRIMARY']);
    		
    		
			printf("		if (\$this->af->get('%s')) {" . PHP_EOL
			. "			\$this->setForm(\$this->%s->get('%s', \$this->af->get('%s')));" . PHP_EOL
			. "		} else {" . PHP_EOL
			. "		}" . PHP_EOL . PHP_EOL
			, $fields['PRIMARY'], $argv[1] ? $argv[1] : 'users', $tableName, $fields['PRIMARY']);
			
			
    		printf("   			\$params = array();" . PHP_EOL
			. "  			\$sql = 'SELECT %s.*'" . PHP_EOL
			. "   				 . ' FROM %s %s'" . PHP_EOL
			. "   				 . ' LEFT JOIN %s %s ON %s.%s = %s.%s AND %s.is_removed = 0'" . PHP_EOL . PHP_EOL
			. "   				 . ' WHERE %s.is_removed = 0';" . PHP_EOL
			. "   			return \$this->getAll(\$sql, \$params);" . PHP_EOL . PHP_EOL
			, substr($tableName, 0, 1), $tableName, substr($tableName, 0, 1)
			, $tableName, substr($tableName, 0, 1), substr($tableName, 0, 1), $fields['PRIMARY'], substr($tableName, 0, 1), $fields['PRIMARY'], substr($tableName, 0, 1)
			, substr($tableName, 0, 1));
		
		
    	}
    	
    	// フィールド系プログラム雛形
    	foreach ((array)$tableNames as $tableName) {
    		echo $tableName . PHP_EOL;
    		echo '----------------------------------------' . PHP_EOL;
    		
   			// バリデート
    		foreach ((array)$fields as $field => $types) {
    			if ($field == 'PRIMARY') {
    				continue;
    			}
				printf("		'%s' => array(" .  PHP_EOL
				. "			'type'	=> VAR_TYPE_%S," .  PHP_EOL
				. "			'name'	=> '%s'," .  PHP_EOL
				. "			'required'	=> false," .  PHP_EOL
				. "		)," .  PHP_EOL
				, $field, $types[1], strtoupper($field));
    		}
    		
			// アクションフォームDB
			echo "array(" . PHP_EOL;
    		foreach ((array)$fields as $field => $types) {
    			if ($field == 'PRIMARY') {
    				continue;
    			}
    			if (strpos($field, 'user_id') !== false) {
					printf("	'%s' => %s\$this->session->get('%s')," . PHP_EOL, $field, $types[0], $field);
    			} else {
					printf("	'%s' => %s\$this->af->get('%s')," . PHP_EOL, $field, $types[0], $field);
    			}
    		}
    		echo ")" . PHP_EOL;
    		
    		echo PHP_EOL . PHP_EOL;
    	}
    	
    	return null;
    }
    
    private function getFields($tableName)
    {
		$sql = 'DESCRIBE ' . $tableName;
		$fields = $this->tools->getAll($sql);
		
		$params = array();
		foreach ((array)$fields as $f) {
			if (strpos($f['Type'], 'int') !== false) {
				$params[$f['Field']] = array('(int)', 'INT');
			} elseif (
				(strpos($f['Type'], 'char') !== false)
				|| (strpos($f['Type'], 'text') !== false)
				|| (strpos($f['Type'], 'date') !== false)
			) {
				$params[$f['Field']] = array('(string)', 'STRING');
			} elseif (strpos($f['Type'], 'time') !== false) {
				$params[$f['Field']] = array('(int)', 'INT');
			} elseif (strpos($f['Type'], 'double') !== false) {
				$params[$f['Field']] = array('(float)', 'FLOAT');
			} else {
				$params[$f['Field']] = '';
			}
		}
		
		$params['PRIMARY'] = $fields[0]['Field'];
		
		return $params;
    }
    
    private function getTableNames()
    {
    	$sql = 'SHOW tables';
    	$tables = $this->tools->getAll($sql);
    	$tableNames = array();
    	
    	foreach ((array)$tables as $t) {
    		$tableNames[] = $t['Tables_in_afw'];
    	}
    	
    	return $tableNames;
    }
}
?>
