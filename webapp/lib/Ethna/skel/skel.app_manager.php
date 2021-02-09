<?php
/**
 *  {$app_path}
 *
 *  @author     akio.co.com
 *  @package    Afw
 *  @version    1.0
 */

/**
 *  {$app_manager}Manager
 *
 *  @author     akio.co.com
 *  @access     public
 *  @package    Afw
 */
class {$app_manager}Manager extends Afw_AppManager
{
	var $dbinfo = array(
		'tables'	=> array(
			'tablename'		=> 'tables',
			'keyname'		=> 'table_id',
			'modifiedname'	=> 'm_datetime',
			'createdname'	=> 'r_datetime',
		),
	);
}
?>
