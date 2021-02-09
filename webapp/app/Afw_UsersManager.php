<?php
/**
 *  Afw_UsersManager.php
 *
 *  @author     akio.co.com
 *  @package    Afw
 *  @version    1.0
 */

/**
 *  Afw_UsersManager
 *
 *  @author     akio.co.com
 *  @access     public
 *  @package    Afw
 */
class Afw_UsersManager extends Afw_AppManager
{
	var $dbinfo = array(
		'users'	=> array(
			'tablename'		=> 'users',
			'keyname'		=> 'user_id',
			'modifiedname'	=> 'm_datetime',
			'createdname'	=> 'r_datetime',
		),
		'user_logs'	=> array(
			'tablename'		=> 'user_logs',
			'keyname'		=> 'user_log_id',
			'modifiedname'	=> false,
			'createdname'	=> 'r_datetime',
		),
	);
	
	public function setLog($logMode, $logBody = null)
	{
		$params = array(
			'user_id' => $this->session->get('user_id') ? $this->session->get('user_id') : $this->af->get('user_token_id'),
			'log_mode' => (string)$logMode,
			'log_body' => (string)$logBody,
		);
		return $this->set('user_logs', null, $params);
	}
	
	public function getUserByLogin($userLogin)
	{
		$params = array($userLogin);
		$sql = 'SELECT u.*'
			 . ' FROM users u'
			 . ' WHERE u.user_login = ? AND u.is_removed = 0';
		return $this->getRow($sql, $params);
	}
	
	public function removeSession($userId)
	{
		$user = $this->get('users', $userId);
		if (is_array($user)) {
			foreach ($user as $k => $v) {
				$this->session->remove($k, $v);
			}
		}
		$this->session->remove('is_expire');
		$this->session->destroy();	
	}
	
	public function setSession($userId, $isExpire = true)
	{
		if ($this->session->isStart()) {
			$this->session->destroy();
		}
		$this->session->start($isExpire ? 365 * 10 * 86400 : 0);
		
		$user = $this->get('users', $userId);
		if (is_array($user)) {
			foreach ($user as $k => $v) {
				$this->session->set($k, $v);
			}
		}
		$this->session->set('is_expire', $isExpire);
	}
	
	public function pageUsers($userAuth = null, $q = null, $order = null, $limit = null, $offset = null)
	{
		$params = array();
		$wheres = array();
		if ($userAuth) {
			$params[] = $userAuth;
		}
		if ($q) {
			$queries = $this->explodeQuery($q);
			if (is_array($queries)) {
				foreach ($queries as $q) {
					$params[] = $this->like($q);
					$params[] = $this->like($q);
					$params[] = $this->like($q);
					$wheres[] = '(user_name LIKE ? OR user_mail LIKE ? OR user_login LIKE ?)';
				}
			}
		}
		$sql = 'SELECT u.*'
			 . ' FROM users u'
			 . ' WHERE u.is_removed = 0'
			 . ($userAuth ? ' AND u.user_auth = ?' : '')
			 . ($wheres ? (' AND ' . implode(' AND ', $wheres)) : '')
			 . ' ORDER BY u.user_auth, u.user_login ASC';
		return $this->getPage($sql, $params, $offset, $limit);
	}
}
?>
