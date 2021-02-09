<?php
/* �ϊ��O�̊G�����^�C�v */
define('MPC_FROM_FOMA'    , 'FOMA');
define('MPC_FROM_EZWEB'   , 'EZWEB');
define('MPC_FROM_SOFTBANK', 'SOFTBANK');
/* �ϊ��O�̊G�����̌n */
define('MPC_FROM_OPTION_RAW' , 'RAW'); // �o�C�i���R�[�h
define('MPC_FROM_OPTION_WEB' , 'WEB'); // Web���̓R�[�h
define('MPC_FROM_OPTION_IMG' , 'IMG'); // �摜
/* �ϊ��O�̕�����̕����R�[�h */
define('MPC_FROM_CHARSET_SJIS', 'SJIS');
define('MPC_FROM_CHARSET_UTF8', 'UTF-8');
/* �ϊ���̕�����̕����R�[�h */
define('MPC_TO_CHARSET_SJIS', 'SJIS');
define('MPC_TO_CHARSET_UTF8', 'UTF-8');

// {{{ class MobilePictogramConverter
/**
* �G�����ϊ��N���X
* 
* <pre>
* MobilePictogramConverter  Factory Method �N���X
*
* MPC_Common      �S�ẴL�����A�ɑ΂��ċ��ʂ���@�\��������x�[�X�N���X
* |
* +-MPC_FOMA      FOMA�G�������瑼�̊G�����ɕϊ�����ۂɃx�[�X�N���X
* |               MobilePictogramConverter::factory�̑�������MPC_FROM_FOMA���w�肵���ꍇ�ɌĂяo����܂��B
* |
* +-MPC_EZweb     EZweb�G�������瑼�̊G�����ɕϊ�����ۂ̃x�[�X�N���X
* |               MobilePictogramConverter::factory�̑�������MPC_FROM_EZWEB���w�肵���ꍇ�ɌĂяo����܂��B
* |
* +-MPC_SoftBank  SoftBank�G�������瑼�̊G�����ɕϊ�����ۂ̃x�[�X�N���X
*                 MobilePictogramConverter::factory�̑�������MPC_FROM_SOFTBANK���w�肵���ꍇ�ɌĂяo����܂��B
* </pre>
* 
* @author   ryster <ryster@php-develop.org>
* @license  http://www.opensource.org/licenses/mit-license.php The MIT License
* @version  Release: 1.2.0
* @link     http://php-develop.org/MobilePictogramConverter/
*/
class MobilePictogramConverter
{
    /**
    * �^�C�v�ɍ��킹�āA��p�̃N���X�I�u�W�F�N�g�𐶐�
    * 
    * ��.
    * <code>
    * require_once("MobilePictogramConverter.php");
    * 
    * $mpc = MobilePictogramConverter::factory($str, MPC_FROM_FOMA, MPC_FROM_CHARSET_SJIS);
    * if (is_object($mpc) == false) {
    *     die($mpc);
    * }
    * </code>
    * 
    * @param string  $str     �ϊ��O������
    * @param string  $carrier $str�̊G�����L�����A (MPC_FROM_FOMA, MPC_FROM_EZWEB, MPC_FROM_SOFTBANK)
    * @param string  $charset �����R�[�h         (MPC_FROM_CHARSET_SJIS, MPC_FROM_CHARSET_UTF8)
    * @param string  $type    $str�̊G�����^�C�v  (MPC_FROM_OPTION_RAW, MPC_FROM_OPTION_WEB, MPC_FROM_OPTION_IMG)
    * @return mixed
    */
    function &factory($str, $carrier, $charset, $type = MPC_FROM_OPTION_RAW)
    {
        $filepath = dirname(__FILE__).'/Carrier/'.strtolower($carrier).'.php';
        if (file_exists($filepath) == false) {
            $error = 'The file doesn\'t exist.';
            return $error;
        }
        
        require_once($filepath);
        $classname = 'MPC_'.$carrier;
        
        if (class_exists($classname) == false) {
            $error = 'The class doesn\'t exist.';
            return $error;
        }
        
        $mpc = new $classname;
        $mpc->setFromCharset($charset);
        $mpc->setString($str);
        $mpc->setFrom(strtoupper($carrier));
        $mpc->setStringType($type);
        
        return $mpc;
    }
}
// }}}
?>