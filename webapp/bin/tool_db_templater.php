<?php
require_once dirname(__FILE__) . '/../../config.inc.php';

$_SERVER['URL_HANDLER'] = 'afw';

$action = pathinfo(__FILE__, PATHINFO_FILENAME);
Afw_Controller::main_cli('Afw_Controller', $action);
