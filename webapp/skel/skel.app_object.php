<?php
/**
 *  {$app_path}
 *
 *  @author     akio.co.com
 *  @package    Afw
 *  @version    1.0
 */

/**
 *  {$app_object}Manager
 *
 *  @author     akio.co.com
 *  @access     public
 *  @package    Afw
 */
class {$app_object}Manager extends Afw_AppManager
{
	var $dbinfo = array(
		'settings'	=> array(
			'tablename'		=> 'settings',
			'keyname'		=> 'setting_id',
			'modifiedname'	=> 'm_datetime',
			'createdname'	=> 'r_datetime',
		),
	);
}

/**
 *  {$app_object}
 *
 *  @author     akio.co.com
 *  @access     public
 *  @package    Afw
 */
class {$app_object} extends Ethna_AppObject
{
    /**
     *  プロパティの表示名を取得する
     *
     *  @access public
     */
    function getName($key)
    {
        return $this->get($key);
    }
}
?>
