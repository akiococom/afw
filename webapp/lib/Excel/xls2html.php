<?php
/*
　このサンプルの使い方
動作条件
	サーバー上でPHPが使用できること
	ファイルのuploadを許可する設定であること
	アップロードするファイルは65000バイト未満であること

動作方法
　１．同梱の peruser.php と reviser.php と一緒にこのファイルを
　　　WEBアクセス可能なフォルダーに置きます。
　２．ブラウザーでこのファイルをアクセスします
　３．ファイルアップロードのフォームがでますのでそこに
　　　EXCELファイル(同梱のexcel-color.xlsでよい)を送信します
　４．後は、画面でエラーが出ていないことを確認してください

注意
　これはあくまでサンプルソフトです。このソフトが動作する状態で
　インターネット上に公開しないでください。
　xss等のセキュリティー対策は未実施です。
　あくまでExcel_Peruserの動作確認用に限定して使用してください。
*/

require_once 'peruser.php';

// makeptn関数(セルのパターン塗りつぶし画像生成)を使う場合は必ず先頭で処理
if (isset($_GET['ptn'])) makeptn($_GET['ptn'],$_GET['fc']);

// utf-8以外の文字エンコーディングを使用する場合は、
// 以下のutf-8を変更してください。文字コードによっては機種依存文字が化ける
// 場合があります
$charset='utf-8';
mb_internal_encoding($charset);
putheader($charset);

$uperror=-1;
if (isset($_FILES['userfile']['tmp_name'])){
	switch($_FILES['userfile']['error']){
	case 0: $errmes="";
		break;
	case 1:
		$errmes="アップロードされたファイルは、php.ini の upload_max_filesize ディレクティブの値を超えています。";
		break;
        case 2:
		$errmes="アップロードされたファイルは、HTML フォームで指定された MAX_FILE_SIZE を超えています。";
                break;
        case 3:
                $errmes="アップロードされたファイルは一部しかアップロードされていません。";
                break;
        case 4:
                $errmes="ファイルはアップロードされませんでした。";
                break;
        case 5:
                $errmes="不明なエラー";
                break;
        case 6:
                $errmes="テンポラリフォルダがありません。php.iniまたはテンポラリーフォルダー有無およびパーミッションの確認をしてください";
                break;
        case 7:
                $errmes="ディスクへの書き込みに失敗しました。パーミッションの確認をしてください";
                break;
	}
	if (($errmes=="") && (!mb_ereg("\.xls$",$_FILES['userfile']['name']))) $errmes="EXCELファイルでは有りません";
}
if (isset($_FILES['userfile']['tmp_name']) && $errmes==="") {
	$obj =NEW Excel_Peruser;
	$obj->setInternalCharset($charset);
	$obj->fileread($_FILES['userfile']['tmp_name']);

	putcss();
}
	putform();

	if($errmes) print $errmes;
	if (isset($_FILES['userfile']['tmp_name']) && $errmes==="") {
		print "<p>ファイル名　".$_FILES['userfile']['name'] ."</p>\n";
		for($sn=0;$sn<$obj->sheetnum;$sn++){
			$w=32;
			for($i=0;$i<=$obj->maxcell[$sn];$i++){
				$w+=$obj->getColWidth($sn,$i);
			}
print "シート".$sn."： ".$obj->sheetname[$sn] ."<br>\n";
$hd=$obj->getHEADER($sn);
$ft=$obj->getFOOTER($sn);
if ($hd!==null){
print <<<STR1
<table width="${w}" border="0" cellpadding="0" cellspacing="1" bordercolor="#CCCCCC" bgcolor="#CCCCCC"><tr>
    <td width="30" nowrap><font size="1">ヘッダ</font></td>
    <td bgcolor="#FFFFFF"><div align="left"> ${hd['left']} </div></td>
    <td bgcolor="#FFFFFF"><div align="center"> ${hd['center']} </div></td>
    <td bgcolor="#FFFFFF"><div align="right"> ${hd['right']} </div></td>
</tr></table>
STR1;
}
print <<<STR2
<table border="0" cellpadding="0" cellspacing="0" width="${w}" bgcolor="#FFFFFF">
  <tr bgcolor="#CCCCCC">
    <th bgcolor="#CCCCCC" scope="col" width="32">&nbsp;</th>
STR2;
		for($i=0;$i<=$obj->maxcell[$sn];$i++){
			$tdwidth=$obj->getColWidth($sn,$i);
print '    <th bgcolor="#CCCCCC" scope="col" width="';
print $tdwidth.'">'.$i.'</th>'."\n";
		}
print "  </tr>\n";
		for($r=0;$r<=$obj->maxrow[$sn];$r++){
print '  <tr height="'.$obj->getRowHeight($sn,$r).'">'."\n";
print '    <th bgcolor="#CCCCCC" scope="row">'.$r."</th>\n";
			for($i=0;$i<=$obj->maxcell[$sn];$i++){
				$tdwidth=$obj->getColWidth($sn,$i);
				$dispval=$obj->dispcell($sn,$r,$i);
				$dispval=mb_eregi_replace ('<','&lt;',$dispval);
				$dispval=mb_eregi_replace ('>','&gt;',$dispval);
				if (isset($obj->hlink[$sn][$r][$i])){
					$dispval='<a href="'.$obj->hlink[$sn][$r][$i].'">'.$dispval.'</a>';
				}
	$xf=$obj->getAttribute($sn,$r,$i);
	$xfno=($xf['xf']>0) ? $xf['xf']: 0;
	if(isset($obj->celmergeinfo[$sn][$r][$i]['cond'])){
		if($obj->celmergeinfo[$sn][$r][$i]['cond']==1){
			$colspan=$obj->celmergeinfo[$sn][$r][$i]['cspan'];
			$rowspan=$obj->celmergeinfo[$sn][$r][$i]['rspan'];
			if($colspan>1) $rcspan =' colspan="'.$colspan.'"';else $rcspan=' width="'.$tdwidth.'"';
			if($rowspan>1) $rcspan.=' rowspan="'.$rowspan.'"';
print '    <td class="XF'.$xfno.'" '.$rcspan.'>'.$dispval."</td>\n";
				}
	} else {
print '    <td class="XF'.$xfno.'" width="'.$tdwidth.'" >'.$dispval."</td>\n";
	}
			}
print "</tr>\n";
		}
print "</table>\n";
if ($ft!==null){
print <<<STR3
<table width="${w}" border="0" cellpadding="0" cellspacing="1" bordercolor="#CCCCCC" bgcolor="#CCCCCC"><tr>
    <td width="30" nowrap><font size="1">フッタ</font></td>
    <td bgcolor="#FFFFFF"><div align="left">${ft['left']} </div></td>
    <td bgcolor="#FFFFFF"><div align="center">${ft['center']}</div></td>
    <td bgcolor="#FFFFFF"><div align="right">${ft['right']}</div></td>
</tr></table>
STR3;
}
print "<p> </p>\n";
	}
}
print <<<EOD
<p> </p><hr>
<!--copyright--><div style="color: gray; font-size: 9px; text-align: center">
Powered by Excel_Peruser & Excel_Reviser<br>
Copyright &copy; 2007 kishiyan <a href="http://chazuke.com">茶漬けドットコム</a>
</div><!--copyright-->
</body>
</html>
EOD;

function putheader($charset){
print <<<_HEADER_
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html"; charset="${charset}">
<title>Excel_Reviser ユーティリティ[EXCEL Viewer]</title>
_HEADER_;
}

function putform(){
print <<<STR4
</head><body bgcolor="#FFFFF0"><center>
<H2>EXCEL Viewer Ver1.0</H2>
<form enctype="multipart/form-data" action=${_SERVER['PHP_SELF']}  method="POST">
<table border="0" cellpadding="0" cellspacing="1" bgcolor="#CCCCCC">
  <tr bgcolor="#F8FFFF">
    <td colspan="2" align="right" nowrap>EXCELファイルを送信するとHTMLに変換して表示します</td>
  </tr>
  <tr>
    <td width="100" align="right" bgcolor="#F8FFFF">送信ファイル</td>
    <td bgcolor="#FFFFF8">
    <input type="hidden" name="MAX_FILE_SIZE" value="65000" />
    <input name="userfile" type="file" />
    <input type="submit" value="送信" /></td>
  </tr>
</table>
</form></center>
<hr>
STR4;
}

function putcss(){
	global $obj;
	$css=$obj->makecss();
print <<<_CSS
<style type="text/css">
<!--
body,td,th {
	font-size: normal;
}
${css}
-->
</style>
_CSS;
}
?>

