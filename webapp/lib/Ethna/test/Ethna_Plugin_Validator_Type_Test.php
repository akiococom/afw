<?php
/**
 *  Ethna_Plugin_Validator_Type_Test.php
 */

/**
 *  Ethna_Plugin_Validator_Typeクラスのテストケース
 *
 *  @access public
 */
class Ethna_Plugin_Validator_Type_Test extends Ethna_UnitTestBase
{
    function testCheckValidatorType()
    {
        $ctl = Ethna_Controller::getInstance();
        $plugin = $ctl->getPlugin();
        $vld = $plugin->getPlugin('Validator', 'Type');

        $form_int = array(
                          'type'          => VAR_TYPE_INT,
                          'required'      => true,
                          'error'         => '{form}には数字(整数)を入力して下さい'
                          );
        $vld->af->setDef('namae_int', $form_int);

        $pear_error = $vld->validate('namae_int', 10, $form_int);
        $this->assertFalse(is_a($pear_error, 'PEAR_Error'));

        $pear_error = $vld->validate('namae_int', '', $form_int);
        $this->assertFalse(is_a($pear_error, 'PEAR_Error'));

        $pear_error = $vld->validate('namae_int', '76', $form_int);
        $this->assertFalse(is_a($pear_error, 'PEAR_Error'));

        // 整数以外の文字列が入力された
        $pear_error = $vld->validate('namae_int', '11asd', $form_int);
        $this->assertTrue(is_a($pear_error, 'PEAR_Error'));
        $this->assertEqual(E_FORM_WRONGTYPE_INT, $pear_error->getCode());
        $this->assertEqual($form_int['error'], $pear_error->getMessage());

        // 整数以外の文字列が入力された
        $pear_error = $vld->validate('namae_int', '7.6', $form_int);
        $this->assertTrue(is_a($pear_error, 'PEAR_Error'));
        $this->assertEqual(E_FORM_WRONGTYPE_INT, $pear_error->getCode());
        $this->assertEqual($form_int['error'], $pear_error->getMessage());



        $form_float = array(
                            'type'          => VAR_TYPE_FLOAT,
                            'required'      => true,
                            'error'         => '{form}には数字(小数)を入力して下さい'
                            );
        $vld->af->setDef('namae_float', $form_float);

        $pear_error = $vld->validate('namae_float', 10.1, $form_float);
        $this->assertFalse(is_a($pear_error, 'PEAR_Error'));

        $pear_error = $vld->validate('namae_float', 10, $form_float);
        $this->assertFalse(is_a($pear_error, 'PEAR_Error'));

        $pear_error = $vld->validate('namae_float', '', $form_float);
        $this->assertFalse(is_a($pear_error, 'PEAR_Error'));

        // 数字以外の文字列が入力された
        $pear_error = $vld->validate('namae_float', '1-0.1', $form_float);
        $this->assertTrue(is_a($pear_error, 'PEAR_Error'));
        $this->assertEqual(E_FORM_WRONGTYPE_FLOAT, $pear_error->getCode());
        $this->assertEqual($form_float['error'], $pear_error->getMessage());



        $form_boolean = array(
                             'type'          => VAR_TYPE_BOOLEAN,
                             'required'      => true,
                             'error'         => '{form}には1または0のみ入力できます'
                             );
        $vld->af->setDef('namae_boolean', $form_boolean);

        $pear_error = $vld->validate('namae_boolean', 1, $form_boolean);
        $this->assertFalse(is_a($pear_error, 'PEAR_Error'));

        $pear_error = $vld->validate('namae_boolean', 0, $form_boolean);
        $this->assertFalse(is_a($pear_error, 'PEAR_Error'));

        $pear_error = $vld->validate('namae_boolean', '', $form_boolean);
        $this->assertFalse(is_a($pear_error, 'PEAR_Error'));

        // 0,1以外の文字が入力された
        $pear_error = $vld->validate('namae_boolean', 'aaa', $form_boolean);
        $this->assertTrue(is_a($pear_error, 'PEAR_Error'));
        $this->assertEqual(E_FORM_WRONGTYPE_BOOLEAN, $pear_error->getCode());
        $this->assertEqual($form_boolean['error'], $pear_error->getMessage());

        // 0,1以外の文字が入力された
        $pear_error = $vld->validate('namae_boolean', 10.1, $form_boolean);
        $this->assertTrue(is_a($pear_error, 'PEAR_Error'));
        $this->assertEqual(E_FORM_WRONGTYPE_BOOLEAN, $pear_error->getCode());
        $this->assertEqual($form_boolean['error'], $pear_error->getMessage());



        $form_datetime = array(
                               'type'          => VAR_TYPE_DATETIME,
                               'required'      => true,
                               'error'         => '{form}には日付を入力して下さい'
                               );
        $vld->af->setDef('namae_datetime', $form_datetime);

        // 正常な日付
        $pear_error = $vld->validate('namae_datetime', "July 1, 2000 00:00:00 UTC", $form_datetime);
        $this->assertFalse(is_a($pear_error, 'PEAR_Error'));
        $pear_error = $vld->validate('namae_datetime', "+89 day", $form_datetime);
        $this->assertFalse(is_a($pear_error, 'PEAR_Error'));

        // empty は required でやるので type ではチェックしない
        $pear_error = $vld->validate('namae_datetime', "", $form_datetime);
        $this->assertFalse(is_a($pear_error, 'PEAR_Error'));

        // 日付に変換できない文字列が入力された
        $pear_error = $vld->validate('namae_datetime', "monkey", $form_datetime);
        $this->assertTrue(is_a($pear_error, 'PEAR_Error'));
        $this->assertEqual(E_FORM_WRONGTYPE_DATETIME, $pear_error->getCode());
        $this->assertEqual($form_datetime['error'], $pear_error->getMessage());

        // 日付に変換できない文字列が入力された
        $pear_error = $vld->validate('namae_datetime', "--1", $form_datetime);
        $this->assertTrue(is_a($pear_error, 'PEAR_Error'));
        $this->assertEqual(E_FORM_WRONGTYPE_DATETIME, $pear_error->getCode());
        $this->assertEqual($form_datetime['error'], $pear_error->getMessage());

    }
}
?>
