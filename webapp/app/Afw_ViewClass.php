<?php
// vim: foldmethod=marker
/**
 *  Afw_ViewClass.php
 *
 *  @author     {$author}
 *  @package    Afw
 *  @version    $Id: app.viewclass.php 323 2006-08-22 15:52:26Z fujimoto $
 */

// {{{ Afw_ViewClass
/**
 *  viewクラス
 *
 *  @author     {$author}
 *  @package    Afw
 *  @access     public
 */
class Afw_ViewClass extends Ethna_ViewClass
{
    /**
     *  共通値を設定する
     *
     *  @access protected
     *  @param  object  Afw_Renderer  レンダラオブジェクト
     */
    function _setDefault(&$renderer)
    {
    	if (isset($_SESSION)) {
	    	$renderer->setPropByRef('is_admin', in_array($_SESSION['user_auth'], $this->config->get('user_auth_admins', true)));
    	}
    }
}
// }}}
?>
