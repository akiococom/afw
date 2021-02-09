<?php
// vim: foldmethod=marker
/**
 *  Ethna_MailSender.php
 *
 *  @author     Masaki Fujimoto <fujimoto@php.net>
 *  @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 *  @package    Ethna
 *  @version    $Id: Ethna_MailSender.php 462 2007-04-19 14:59:38Z ichii386 $
 */

/** メールテンプレートタイプ: 直接送信 */
define('MAILSENDER_TYPE_DIRECT', 0);


// {{{ Ethna_MailSender
/**
 *  メール送信クラス
 *
 *  @author     Masaki Fujimoto <fujimoto@php.net>
 *  @access     public
 *  @package    Ethna
 */
class Ethna_MailSender
{
    /**#@+
     *  @access private
     */

    /** @var    array   メールテンプレート定義 */
    var $def = array(
    );

    /** @var    string  メールテンプレートディレクトリ */
    var $mail_dir = 'mail';

    /** @var    int     送信メールタイプ */
    var $type;

    /** @var    string  送信オプション */
    var $option = '';

    /** @var    object  Ethna_Backend   backendオブジェクト */
    var $backend;

    /** @var    object  Ethna_Config    設定オブジェクト */
    var $config;

    var $utils;

    /** @var    Ethna_ActionForm    アクションフォームオブジェクト */
    var $action_form;

    /** @var    Ethna_ActionForm    アクションフォームオブジェクト(省略形) */
    var $af;

    /** @var    Ethna_Session       セッションオブジェクト */
    var $session;


    /**#@-*/

    /**
     *  Ethna_MailSenderクラスのコンストラクタ
     *
     *  @access public
     *  @param  object  Ethna_Backend   &$backend       backendオブジェクト
     */
    function __construct(&$backend)
    {
        $this->backend = $backend;
        $this->utils = & $backend->getManager('utils');
        $this->action_form = $this->backend->getActionForm();
        $this->af = $this->action_form;
        $this->session = $this->backend->getSession();
        $this->config = $this->backend->getConfig();
    }

    /**
     *  メールオプションを設定する
     *
     *  @access public
     *  @param  string  $option メール送信オプション
     */
    function setOption($option)
    {
        $this->option = $option;
    }
    /**
     * ログ書き出し
     *
     * @param	string	$category
     * @param	string	$string
     * @return
     */
    function writeLog($category = '', $string = '')
    {
        // ログ整形
        $log = date('Y-m-d H:i:s') . "\t" . $this->utils->getHost() . "\t" . $category . "\t" . $string . "\n";
        file_put_contents($this->config->get('log_directory') . date('Ymd') . '.log', $log, FILE_APPEND);
        @chmod($this->config->get('log_directory') . date('Ymd') . '.log', 0777);
    }

    /**
     *  メールを送信する
     *
     *  $attach の指定方法:
     *  - 既存のファイルを添付するとき
     *  <code>
     *  array('filename' => '/tmp/hoge.xls', 'content-type' => 'application/vnd.ms-excel')
     *  </code>
     *  - 文字列に名前を付けて添付するとき
     *  <code>
     *  array('name' => 'foo.txt', 'content' => 'this is foo.')
     *  </code>
     *  'content-type' 省略時は 'application/octet-stream' となる。
     *  複数添付するときは上の配列を添字0から始まるふつうの配列に入れる。
     *
     *  @access public
     *  @param  string  $to         メール送信先アドレス (nullのときは送信せずに内容を return する)
     *  @param  string  $template   メールテンプレート名 or タイプ
     *  @param  array   $macro      テンプレートマクロ or $templateがMAILSENDER_TYPE_DIRECTのときはメール送信内容)
     *  @param  array   $attach     添付ファイル
     *  @return bool|string  mail() 関数の戻り値 or メール内容
     */
    function send($to, $template, $macro, $attach = null, $writeLog = false)
    {
        // メール内容を作成
        if ($template === MAILSENDER_TYPE_DIRECT) {
            $mail = $macro;
        } else {
            $renderer = $this->getTemplateEngine();

            // 基本情報設定
            $renderer->setProp("env_datetime", strftime('%Y年%m月%d日 %H時%M分%S秒'));
            $renderer->setProp("env_useragent", $_SERVER["HTTP_USER_AGENT"]);
            $renderer->setProp("env_remoteaddr", $_SERVER["REMOTE_ADDR"]);

            // デフォルトマクロ設定
            $macro = $this->_setDefaultMacro($macro);

            // ユーザ定義情報設定
            if (is_array($macro)) {
                foreach ($macro as $key => $value) {
                    $renderer->setProp($key, $value);
                }
            }
            if (isset($this->def[$template])) {
                $template = $this->def[$template];
            }
            $mail = $renderer->perform(sprintf('%s/%s', $this->mail_dir, $template), true);
        }
        if ($to === null) {
            return $mail;
        }

        // メール内容をヘッダと本文に分離
        $mail = str_replace("\r\n", "\n", $mail);
        list($header, $body) = $this->_parse($mail);

        // 添付ファイル (multipart)
        if ($attach !== null) {
            $attach = isset($attach[0]) ? $attach : array($attach);
            $boundary = Ethna_Util::getRandom(); 
            $body = "This is a multi-part message in MIME format.\n\n" .
                "--$boundary\n" .
                "Content-Type: text/plain; charset=utf-8\n" .
                "Content-Transfer-Encoding: 8bit\n\n" .
                "$body\n";
            foreach ($attach as $part) {
                if (isset($part['content']) === false
                    && isset($part['filename']) && is_readable($part['filename'])) {
                    $part['content'] = file_get_contents($part['filename']);
                    $part['filename'] = basename($part['filename']);
                }
                if (isset($part['content']) === false) {
                    continue;
                }
                if (isset($part['content-type']) === false) {
                    $part['content-type'] = 'application/octet-stream';
                }
                if (isset($part['name']) === false) {
                    $part['name'] = $part['filename'];
                }
                if (isset($part['filename']) === false) {
                    $part['filename'] = $part['name'];
                }
                if (preg_match_all('/([^\x00-\x7f]+)/', $part['name'], $m)){
                    foreach ($m[1] as &$v) $v = Ethna_Util::encode_MIME($v);
                    $part['name'] = str_replace($m[0], $m[1], $part['name']);
                }
                if (preg_match_all('/([^\x00-\x7f]+)/', $part['filename'], $m)){
                    foreach ($m[1] as &$v) $v = Ethna_Util::encode_MIME($v);
                    $part['filename'] = str_replace($m[0], $m[1], $part['filename']);
                }

                $body .=
                    "--$boundary\n" .
                    "Content-Type: " . $part['content-type'] . ";\n" .
                        "\tname=\"" . $part['name'] . "\"\n" .
                    "Content-Transfer-Encoding: base64\n" . 
                    "Content-Disposition: attachment;\n" .
                        "\tfilename=\"" . $part['filename'] . "\"\n\n";
                $body .= chunk_split(base64_encode($part['content']));
            }
            $body .= "--$boundary--";
        }

        // ヘッダ
        if (isset($header['mime-version']) === false) {
            $header['mime-version'] = array('Mime-Version', '1.0');
        }
        if (isset($header['subject']) === false) {
            $header['subject'] = array('Subject', 'no subject in original');
        }
        if (isset($header['content-type']) === false) {
            $header['content-type'] = array(
                'Content-Type',
                $attach === null ? 'text/plain; charset=utf-8'
                                 : "multipart/mixed; \n\tboundary=\"$boundary\"",
            );
        }

        $header_line = "";
        foreach ($header as $key => $value) {
            if ($key == 'subject') {
                // should be added by mail()
                continue;
            }
            if ($header_line != "") {
                $header_line .= "\n";
            }
            $header_line .= $value[0] . ": " . $value[1];
        }

        // 改行コードを CRLF に
        if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') {
            $body = str_replace("\n", "\r\n", $body);
        }
        //$header_line = str_replace("\n", "\r\n", $header_line);
        // 送信
        foreach (to_array($to) as $rcpt) {
            if (is_string($this->option)) {
                if ($writeLog) {
                    if (mail($rcpt, $header['subject'][1], $body, $header_line, $this->option)) {
                        $this->writeLog('全員にメール', $this->session->get('member_id') . '、' . $this->af->get('class_id') . 'の全員にメールの内容のお知らせ投稿しました');
                    } else {
                        $this->writeLog('全員にメール', $this->session->get('member_id') . '、' . $this->af->get('class_id') . 'の全員にメールの内容のお知らせ投稿に失敗しました');
                    }
                } else {
                    mail($rcpt, $header['subject'][1], $body, $header_line, $this->option);
                }

            } else {
                if ($writeLog) {
                    if (mail($rcpt, $header['subject'][1], $body, $header_line)) {
                        $this->writeLog('全員にメール', $this->session->get('member_id') . '、' . $this->af->get('class_id') . 'の全員にメールの内容のお知らせ投稿しました');
                    } else {
                        $this->writeLog('全員にメール', $this->session->get('member_id') . '、' . $this->af->get('class_id') . 'の全員にメールの内容のお知らせ投稿に失敗しました');
                    }
                } else {
                    mail($rcpt, $header['subject'][1], $body, $header_line);
                }

            }
        }
    }

    /**
     *  アプリケーション固有のマクロを設定する
     *
     *  @access protected
     *  @param  array   $macro  ユーザ定義マクロ
     *  @return array   アプリケーション固有処理済みマクロ
     */
    function _setDefaultMacro($macro)
    {
        return $macro;
    }

    /**
     *  テンプレートメールのヘッダ情報を取得する
     *
     *  @access private
     *  @param  string  $mail   メールテンプレート
     *  @return array   ヘッダ, 本文
     */
    function _parse($mail)
    {
        list($header_line, $body) = preg_split('/\r?\n\r?\n/', $mail, 2);
        $header_line .= "\n";

        $header_lines = explode("\n", $header_line);
        $header = array();
        foreach ($header_lines as $h) {
            if (strstr($h, ':') == false) {
                continue;
            }
            list($key, $value) = preg_split('/\s*:\s*/', $h, 2);
            $i = strtolower($key);
            $header[$i] = array();
            $header[$i][] = $key;
            $header[$i][] = preg_replace_callback('/([^\x00-\x7f]+)/',function($m){ return Ethna_Util::encode_MIME($m[1]); }, $value);
        }

        //$body = mb_convert_encoding($body, "ISO-2022-JP");

        return array($header, $body);
    }

    /**
     *  メールフォーマット用レンダラオブジェクト取得する
     *
     *  @access public
     *  @return object  Ethna_Renderer  レンダラオブジェクト
     */
    function &getRenderer()
    {
        $_ret_object = $this->getTemplateEngine();
        return $_ret_object;
    }

    /**
     *  メールフォーマット用レンダラオブジェクト取得する
     *
     *  @access public
     *  @return object  Ethna_Renderer  レンダラオブジェクト
     */
    function &getTemplateEngine()
    {
        $c = $this->backend->getController();
        $renderer = $c->getRenderer();
        return $renderer;
    }
}
// }}}
?>
