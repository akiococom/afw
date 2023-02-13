<?php
/**
 *  Afw_SettingsManager.php
 *
 *  @author     {$author}
 *  @package    Afw
 *  @version    $Id: skel.app_manager.php 387 2006-11-06 14:31:24Z cocoitiban $
 */

/**
 *  Afw_SettingsManager
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Afw
 */
class Afw_SettingsManager extends Afw_AppManager
{
	var $dbinfo = array(
		'settings'	=> array(
			'tablename'		=> 'settings',
			'keyname'		=> 'setting_id',
			'modifiedname'	=> 'm_datetime',
			'createdname'	=> 'r_datetime',
		),
	);
	
	function reset($key, $userId = null)
	{
		// 現在のデータを取得
		$params = array($key);
		if ($userId) {
			$params[] = $userId;
		}
		$sql = 'SELECT setting_id FROM settings'
			 . ' WHERE `setting_key` = ?'
			 . ($userId ? ' AND user_id = ?' : '')
			 . ' LIMIT 1';
		$settingId = $this->getOne($sql, $params);
		return $this->remove('settings', $settingId);
	}
	
	/**
	 * 設定を保存
	 * 
	 * @param	string	$key
	 * @param	string	$value
	 * @return	bool
	 */
	function set($key, $value, $userId = null)
	{
		// 既存チェック
		$params = array($key);
		if ($userId) {
			$params[] = $userId;
		}
		$sql = 'SELECT * FROM settings'
			 . ' WHERE `setting_key` = ? AND is_removed = 0'
			 . ($userId ? ' AND user_id = ?' : '')
			 . ' LIMIT 1';
		$result = $this->getRow($sql, $params);
		
		// 登録用パラメータ
		$params = array(
			'user_id' => (int)$userId,
			'setting_key' => $key,
			'setting_value' => $value,
		);
		
		if ($settingId = $result['setting_id']) {
			// 更新
			return $this->update('settings', $settingId, $params);
		} else {
			// 新規
			return $this->insert('settings', $params);
		}
	}
	
	/**
	 * 設定を読み込む
	 * 
	 * @param	string	$key
	 * @return	string
	 */
	function get($key = null, $userId = null)
	{
		if ($key) {
			$params = array($key);
			if ($userId) {
				$params[] = $userId;
			}
			$sql = 'SELECT `setting_value` FROM settings'
				 . ' WHERE `setting_key` = ? AND is_removed = 0'
				 . ($userId ? ' AND user_id = ?' : '')
				 . ' LIMIT 1';
			$value = $this->getOne($sql, $params);
			$unserialize = @unserialize($value);
			if ($unserialize) {
				return $unserialize;
			} else {
				if (is_null($value)) {
					return $this->config->get($key);
				} else {
					return $value;
				} 
			}
		} else {
			$sql = 'SELECT * FROM settings';
			if ($userId) {
				$sql .= ' WHERE user_id = ?';
			}
			$tmpSettings = $this->getAll($sql);
			$settings = array();
			if (is_array($tmpSettings)) {
				foreach ($tmpSettings as $s) {
					$unserialize = @unserialize($s['setting_value']);
					if ($unserialize) {
						$settings[$s['setting_key']] = $unserialize;
					} else {
						$settings[$s['setting_key']] = $s['setting_value'];
					}
				}
			}
			return $settings;
		}
	}
}
?>
