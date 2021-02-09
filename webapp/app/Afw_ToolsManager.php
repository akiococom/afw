<?php
/**
 *  Afw_ToolsManager.php
 *
 *  @author     akio.co.com
 *  @package    Afw
 *  @version    1.0
 */

/**
 *  Afw_ToolsManager
 *
 *  @author     akio.co.com
 *  @access     public
 *  @package    Afw
 */
class Afw_ToolsManager extends Afw_AppManager
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
