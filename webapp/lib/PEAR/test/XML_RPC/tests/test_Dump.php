<?php

/**
 * Actually performs an XML_RPC request.
 *
 * PHP versions 4 and 5
 *
 * @category   Web Services
 * @package    XML_RPC
 * @author     Daniel Convissor <danielc@php.net>
 * @copyright  2005-2010 The PHP Group
 * @license    http://www.php.net/license/3_01.txt  PHP License
 * @version    SVN: $Id: test_Dump.php 300962 2010-07-03 02:24:24Z danielc $
 * @link       http://pear.php.net/package/XML_RPC
 */

/*
 * If the package version number is found in the left hand
 * portion of the if() expression below, that means this file has
 * come from the PEAR installer.  Therefore, let's test the
 * installed version of XML_RPC which should be in the include path.
 *
 * If the version has not been substituted in the if() expression,
 * this file has likely come from a SVN checkout or a .tar file.
 * Therefore, we'll assume the tests should use the version of
 * XML_RPC that has come from there as well.
 */
if ('1.5.5' == '@'.'package_version'.'@') {
    ini_set('include_path', '../'
            . PATH_SEPARATOR . '.' . PATH_SEPARATOR
            . ini_get('include_path')
    );
}
require_once 'XML/RPC/Dump.php';


$val = new XML_RPC_Value(array(
    'title'    =>new XML_RPC_Value('das ist der Titel', 'string'),
    'startDate'=>new XML_RPC_Value(mktime(0,0,0,13,11,2004), 'dateTime.iso8601'),
    'endDate'  =>new XML_RPC_Value(mktime(0,0,0,15,11,2004), 'dateTime.iso8601'),
    'arkey'    => new XML_RPC_Value( array(
        new XML_RPC_Value('simple string'),
        new XML_RPC_Value(12345, 'int')
        ), 'array')
    )
    ,'struct');

XML_RPC_Dump($val);

echo '==============' . "\r\n";
$val2 = new XML_RPC_Value(44353, 'int');
XML_RPC_Dump($val2);

echo '==============' . "\r\n";
$val3 = new XML_RPC_Value('this should be a string', 'string');
XML_RPC_Dump($val3);

echo '==============' . "\r\n";
$val4 = new XML_RPC_Value(true, 'boolean');
XML_RPC_Dump($val4);

echo '==============' . "\r\n";
echo 'Next we will test the error handling...' . "\r\n";
$val5 = new XML_RPC_Value(array(
    'foo' => 'bar'
    ), 'struct');
XML_RPC_Dump($val5);

echo '==============' . "\r\n";
echo 'DONE' . "\r\n";
