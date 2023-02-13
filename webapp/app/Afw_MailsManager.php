<?php
/**
 *  Afw_MailsManager.php
 *
 *  @author     {$author}
 *  @package    Afw
 *  @version    $Id: skel.app_manager.php 387 2006-11-06 14:31:24Z cocoitiban $
 */

/**
 *  Afw_MailsManager
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Afw
 */
class Afw_MailsManager extends Afw_AppManager
{
	var $dbinfo = array(
		'mails'	=> array(
			'tablename'		=> 'mails',
			'keyname'		=> 'mail_id',
			'modifiedname'	=> false,
			'createdname'	=> 'r_datetime',
		),
	);
	
	public function pageMails($from = null, $q = null, $limit = null, $offset = null)
	{
		$params = array();
		if ($from) {
			$params[] = $this->like($from);
		}
		$wheres = array();
		if (strlen(trim($q))) {
			$queries = $this->explodeQuery($q);
			if (is_array($queries)) {
				foreach ($queries as $q) {
					$params[] = $this->like($q);
					$params[] = $this->like($q);
					$wheres[] = '(m.mail_subject LIKE ? OR m.mail_body LIKE ?)';
				}
			}
		}
		$sql = 'SELECT m.*'
			 . ' FROM mails m'
			 . ' WHERE m.is_removed = 0'
			 . ($from ? ' AND m.from_mail LIKE ?' : '')
			 . ($wheres ? (' AND ' . implode(' AND ', $wheres)) : '')
			 . ' ORDER BY m.mail_id DESC';
		return $this->getPage($sql, $params, $offset, $limit);
	}
}
?>
