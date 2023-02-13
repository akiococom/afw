<?php
/**
 *  {$action_name}.php
 *
 *  @author     {$author}
 *  @package    Afw
 *  @version    $Id: skel.entry_cli.php 432 2006-11-28 04:52:54Z ichii386 $
 */
chdir(dirname(__FILE__));
require_once '{$dir_app}/Afw_Controller.php';

ini_set('max_execution_time', 0);

Afw_Controller::main_CLI('Afw_Controller', '{$action_name}');
?>
