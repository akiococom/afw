<?php
/**
 *  Afw_UtilsManager.php
 *
 *  @author     {$author}
 *  @package    Afw
 *  @version    $Id: skel.app_manager.php 387 2006-11-06 14:31:24Z cocoitiban $
 */

/**
 *  Afw_UtilsManager
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Afw
 */
class Afw_UtilsManager extends Ethna_AppManager
{
	// URLからoggを取得
	// see https://hsmt-web.com/blog/open-graph-protcol/
	public function getOgp($url)
	{
		$ch = curl_init($url);// urlは対象のページ
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);// exec時に出力させない
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);// リダイレクト許可
		curl_setopt($ch, CURLOPT_MAXREDIRS, 5);// 最大リダイレクト数
		$html = curl_exec($ch);
		$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		
		//リンク先がないとき(404のとき)は処理しない
		if( $status_code !== 404 ):
		
			$dom_document = new \DOMDocument();
			$from_encoding = mb_detect_encoding($html, ['ASCII', 'ISO-2022-JP', 'UTF-8', 'EUC-JP', 'SJIS'], true);
			if ( ! $from_encoding)
			{
				$from_encoding = 'SJIS';
			}
		
			@$dom_document->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', $from_encoding));
			$xml_object = simplexml_import_dom($dom_document);
				
			$ogp = [];
			
			//sitename
			if( $xml_object->xpath('//meta[@property="og:site_name"]/@content')[0] ):
				$ogp["site_name"][] = (string)$xml_object->xpath('//meta[@property="og:site_name"]/@content')[0];
			elseif( $xml_object->xpath('//site_name')[0] ):
				$ogp["site_name"][] = (string)$xml_object->xpath('//site_name')[0];
			endif;
			
			//title
			if( $xml_object->xpath('//meta[@property="og:title"]/@content')[0] ):
				$ogp["title"][] = (string)$xml_object->xpath('//meta[@property="og:title"]/@content')[0];
			elseif( $xml_object->xpath('//title')[0] ):
				$ogp["title"][] = (string)$xml_object->xpath('//title')[0];
			endif;
			
			//description
			if( $xml_object->xpath('//meta[@property="og:description"]/@content')[0] ):
				$ogp["description"][] = (string)$xml_object->xpath('//meta[@property="og:description"]/@content')[0];
			elseif( $xml_object->xpath('//meta[@name="description"]/@content')[0] ):
				$ogp["description"][] = (string)$xml_object->xpath('//meta[@name="description"]/@content')[0];
			endif;
			
			//thumbnail
			if( $xml_object->xpath('//meta[@property="og:image"]/@content')[0] ):
				$ogp["image"][] = (string)$xml_object->xpath('//meta[@property="og:image"]/@content')[0];
			elseif( $xml_object->xpath('//meta[@name="thumbnail"]/@content')[0] ):
				$ogp["image"][] = (string)$xml_object->xpath('//meta[@name="thumbnail"]/@content')[0];
			else:
				$ogp["image"][] = $this->config->get('base') . '/images⁄noimage.jpg';
			endif;
		endif;
		
		return $ogp;
	}
	
	
	// ファイルネーム用にNG文字を全角変換
	// see https://ameblo.jp/samosamosalmon/entry-10496982880.html
	function filename($filename)
	{
		$filename = str_replace("\\", "￥", $filename);
		$filename = str_replace("/", "／", $filename);
		$filename = str_replace(":", "：", $filename);
		$filename = str_replace("*", "＊", $filename);
		$filename = str_replace("?", "？", $filename);
		$filename = str_replace("\"", "”", $filename);
		$filename = str_replace("<", "＜", $filename);
		$filename = str_replace(">", "＞", $filename);
		$filename = str_replace("|", "｜", $filename);
		return $filename;
	}
	
	function getYM($date, $currentDate = null)
	{
		if ($currentDate) {
			$currentTime = strtotime($currentDate);
		} else {
			$currentTime = time();
		}
		$dateTime = strtotime($date);
		
		$currentNumeric = date('Y', $currentTime) * 12 + date('n', $currentTime);
		$dateNumeric = date('Y', $dateTime) * 12 + date('n', $dateTime);
		
		$month = $currentNumeric - $dateNumeric;
		$year = floor($month / 12);
		$month = $month % 12;
		
		return array($year, $month);
	}
	
	function removeBOM($string)
	{
		$bom = pack('H*', 'EFBBBF');
		$string = preg_replace("/^$bom/", '', $string);
		return $string;
	}
	
	/**
	 * アクセスホストの取得
	 * 
	 * @return
	 */
	function getHost($isAddr = false)
	{
		if ($isAddr) {
			return @$_SERVER['REMOTE_ADDR'];
		} else {
			return @gethostbyaddr(@$_SERVER['REMOTE_ADDR']);
		}
	}

	/**
	 * 指定した日数後を取得する
	 * 
	 * @param	int	$day
	 * @param	string	$date = null (YYYY-MM-DD) 指定した日付
	 */
	function getDate($day = 0, $date = null)
	{
		if (is_null($date)) {
			// 現在からの日数
			return date('Y-m-d', time() + (86400 * $day));
		} else {
			// 指定日時からの日数
			$dates = split('-', $date);			
			return date('Y-m-d', mktime(0, 0, 0, $dates[1], $dates[2], $dates[0]) + (86400 * $day));
		}
	}
	
	/**
	 * MD5の取得
	 * 
	 * @param	string	$str
	 * @return	string
	 */
	function getMd5($str)
	{
		return md5($str);	
	}
	
	/**
	 * マイクロタイムの取得
	 */
	function getMicroTime()
	{
		return str_replace(array(' ', '.'), '', microtime());
	}
	
	/**
	 * メールの送信
	 * 
	 * @param	string	$to
	 * @param	string	$template
	 * @param	string	$params
	 * @param	string	$option	'mobile':携帯用メール、
	 */
	function sendMail($to, $template, $params = array(), $attach = null)
	{
		// オブジェクトの生成
		$mail = new Ethna_MailSender($this->backend);
		
		// デバッグ時はデバッグアドレスへ
		if ($this->config->get('debug')) {
			$to = $this->config->get('mail_debug');
		}
		
		// テンプレートの設定
		$mail->def = array('1' => $template);
		
		// 送り主メールの追加
		$params['mail_from'] = $this->config->get('mail_from');
		$params['config'] = $this->config->get();
		
		// メール送信
		return $mail->send($to, '1', $params, $attach);
	}
	
	/**
	 * 写真を縮小する
	 * 
	 * @param string $filenameSource	対象の画像ファイル名(*.jpg)
	 * @param string $filenameTarget	書き出し先ファイル名(*.jpg)
	 * @param int $width		目的の幅(px)
	 * @param int $height		目的の高さ(px)
	 * @param int $quality		JPEGのクォリティ
	 * @return bool
	 **/
	function resizeImage($filenameSource, $filenameTarget, $width, $height, $quality = 80)
	{
		// ファイルのサイズを取得する
		list($widthOrig, $heightOrig) = getimagesize($filenameSource);
		
		if ($widthOrig > $width || $heightOrig > $height) {
			// 元画像が指定値よりも大きい場合だけ処理を行う。
			if ($height && $width && ($widthOrig < $heightOrig)) {
				$height = intval(($width / $widthOrig) * $heightOrig);
			} else {
				$width = ($height / $heightOrig) * $widthOrig;
			}

			// ファイルの読み込み
			$pathInfo = pathinfo($filenameSource);
			$ext = strtolower($pathInfo['extension']);
			if ($ext == 'jpg' || $ext == 'jpeg') {
				$image = imagecreatefromjpeg($filenameSource);
			} elseif ($ext == 'gif') {
				$image = imagecreatefromgif($filenameSource);
			} elseif ($ext == 'png') {
				$image = imagecreatefrompng($filenameSource);
			} else {
				return false;
			}
			
			// 再サンプル
			$image_p = imagecreatetruecolor($width, $height);
			imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $widthOrig, $heightOrig);

			// 出力
			if ($ext == 'jpg' || $ext == 'jpeg') {
				return imagejpeg($image_p, $filenameTarget, $quality);
			} elseif ($ext == 'gif') {
				return imagegif($image_p, $filenameTarget);
			} elseif ($ext == 'png') {
				return imagepng($image_p, $filenameTarget, 6);
			}
		} else {
			copy($filenameSource, $filenameTarget);
			return true;
		}
	}
	
	// see http://php.o0o0.jp/article/php-orientation
	/**
	 * 
	 * Exif 画像の向き 回転/反転による調整
	 * @param string $file_name
	 * @param text $mode
	 */
	function imageRotation($file_name, $isSave = true, $isOnlyOrientation = false, $destFilename = false, $orientation = false)
	{
	    // 存在チェック
	    if (! file_exists($file_name)) {
	        exit;
	    }
	
	    // EXIFヘッダを読み込み
	    $exif = exif_read_data($file_name, 'EXIF');
	    
	    // コンテントタイプ
	    if (!$isSave) {
	    	header('Content-type: image/jpeg');
	    }
	
	    // 読み込み
	    $im = imagecreatefromjpeg($file_name);
	
	    $degrees = 0;
	    $mode = '';
	    
	    if ($isOnlyOrientation) {
	    	return $exif['Orientation'];
	    }
	    if ($orientation) {
	    	$exif['Orientation'] = $orientation;
	    }
	    
	    switch($exif['Orientation']) {
	        case 1: // 通常
	            break;
	        case 2: // 水平反転
	            $mode = 'IMG_FLIP_HORIZONTAL';
	            break;
	        case 3: // （反時計回りに）180°回転
	            $degrees = 180;
	            break;
	        case 4: // 垂直反転
	            $mode = 'IMG_FLIP_VERTICAL';
	            break;
	        case 5: // 水平反転、 反時計回りに90°回転(反時計回りに270°回転で正常)
	            $degrees = 270;
	            $mode = 'IMG_FLIP_HORIZONTAL';
	            break;
	        case 6: // 反時計回りに90°回転（反時計回りに270°回転で正常）
	            $degrees = 270;
	            break;
	        case 7: // 垂直反転、 反時計回りに90°回転（反時計回りに270°回転で正常）
	            $degrees = 270;
	            $mode = 'IMG_FLIP_VERTICAL';
	            break;
	        case 8: // 反時計回りに270°回転（反時計回りに90°回転で正常）
	            $degrees = 90;
	            break;
	    }
	    
	    // 反転
	    if (! empty($mode)) {
	        $im = imageflip($im, $mode);
	    }
	    
	    // 回転(反時計回り)
	    if ($degrees > 0) {
	        $im = imagerotate($im, $degrees, 0);
	    }
	    
	    // 出力
	    if ($isSave) {
		    imagejpeg($im, $destFilename ? $destFilename : $file_name);
	    } else {
		    imagejpeg($im);
	    }
	    
	    // メモリの解放
	    imagedestroy($im);    
	}

	/**
	 * 写真を縮小する
	 * 
	 * @param string $filenameSource	対象の画像ファイル名(*.jpg)
	 * @param string $filenameTarget	書き出し先ファイル名(*.jpg)
	 * @param int $width		目的の幅(px)
	 * @param int $height		目的の高さ(px)
	 * @param int $quality		JPEGのクォリティ
	 * @return bool
	 **/
	function resizeJpeg($filenameSource, $filenameTarget, $width, $height, $quality = 80)
	{
		// ファイルのサイズを取得する
		list($widthOrig, $heightOrig) = getimagesize($filenameSource);
		
		if ($widthOrig > $width || $heightOrig > $height) {
			// 元画像が指定値よりも大きい場合だけ処理を行う。
			if ($width && ($widthOrig < $heightOrig)) {
				$width = ($height / $heightOrig) * $widthOrig;
			} else {
				$height = intval(($width / $widthOrig) * $heightOrig);
			}
		
			// 再サンプル
			$image_p = imagecreatetruecolor($width, $height);
			$image = imagecreatefromjpeg($filenameSource);
			imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $widthOrig, $heightOrig);
		
			// 出力
			return imagejpeg($image_p, $filenameTarget, $quality);
		} else {
			if ($filenameSource != $filenameTarget) {
				return copy($filenameSource, $filenameTarget);
			}
		}
	}
	
	/**
	 * 現在時刻の取得
	 */
	function getCurrentDateTime()
	{
		return date('Y-m-d H:i:s');
	}

	/**
	 * CSVファイルを作成する(入力はUTF8で)
	 * 
	 * @param	array	$array	([行][カラム])
	 * @return	string
	 */
	function getCsv($array)
	{
		$csv = '';
		if (is_array($array)) {
			// 行を分解
			foreach ($array as $line) {
				if (is_array($line)) {
					// 列を分解
					//$csv .= '"' . join('","', $line) . '"' . "\n";
					$csv .= mb_convert_encoding('"' . join('","', $line) . '"' . "\n", 'sjis', 'utf8');
				}
			}
		}
		return $csv;
	}

	/**
	 * CSVファイルの分割(改行は考慮しない)
	 * 
	 * @param	string	$filename	ファイル名
	 * @param	char	$delim		デリミタ
	 * @param	char	$encl		囲み枠
	 * @param	int		$optional	囲まれた改行を１つの行として認識
	 * @param	string	$conv_after	文字エンコード後
	 * @param	string	$conv_before	文字エンコード前
	 */
	function parseCsv($filename, $delim = ',', $encl = '"', $optional = 1, $conv_after = 'utf-8', $conv_before = 'sjis-win')
	{
		$content = join('', file($filename));
		// Mac対応
		$content = str_replace("\r", "\n", $content . "\n");
		
		$reg = '/(('.$encl.')'.($optional?'?(?(2)':'('). '[^'.$encl.']*'.$encl.'|[^' . $delim.'\n]*))('.$delim.'|\n)/smi';
		preg_match_all($reg, $content, $treffer);
		$linecount = 0;
		
		for ($i = 0; $i<=count($treffer[3]);$i++) {
			// 前後が"の場合は削除
			$liste[$linecount][] = trim(mb_convert_encoding($treffer[1][$i], $conv_after, $conv_before), '"');
		    if ($treffer[3][$i] != $delim) {
		    	$linecount++;
		    }
		}
		return $liste;
	}
	
	/**
	 * ファイルから行を取得し、CSVフィールドを処理する(parseCSVの内部メソッド)
	 * @param resource handle
	 * @param int length
	 * @param string delimiter
	 * @param string enclosure
	 * @return ファイルの終端に達した場合を含み、エラー時にFALSEを返します。
	 */
    function parseCsvIncludeBreak($filename, $convAfter = 'utf-8', $convBefore = 'sjis-win', $length = null, $d = ',', $e = '"')
    {
		$handle = fopen($filename, 'r');
		$returns = array();	
		while (($data = $this->_parseCsvIncludeBreak($handle, $convAfter, $convBefore, $length, $d, $e)) !== false) {
			$returns[] = $data;
		}
		fclose($handle);
		return $returns;
	}
	
		/**
		 * ファイルポインタから行を取得し、CSVフィールドを処理する(parseCSVの内部メソッド)
		 * @param resource handle
		 * @param int length
		 * @param string delimiter
		 * @param string enclosure
		 * @return ファイルの終端に達した場合を含み、エラー時にFALSEを返します。
		 */
	    function _parseCsvIncludeBreak(&$handle, $convAfter = 'utf-8', $convBefore = 'sjis-win', $length = null, $d = ',', $e = '"')
	    {
			$eof = null;
			$d = preg_quote($d);
			$e = preg_quote($e);
			$_line = "";
			while ($eof != true) {
				$_line .= (empty($length) ? fgets($handle) : fgets($handle, $length));
				$itemcnt = preg_match_all('/'.$e.'/', $_line, $dummy);
				if ($itemcnt % 2 == 0) $eof = true;
			}
			$_line = mb_convert_encoding($_line, $convAfter, $convBefore);
			$_csv_line = preg_replace('/(?:\r\n|[\r\n])?$/', $d, trim($_line));
			$_csv_pattern = '/('.$e.'[^'.$e.']*(?:'.$e.$e.'[^'.$e.']*)*'.$e.'|[^'.$d.']*)'.$d.'/';
			preg_match_all($_csv_pattern, $_csv_line, $_csv_matches);
			$_csv_data = $_csv_matches[1];
			for($_csv_i=0;$_csv_i<count($_csv_data);$_csv_i++){
				$_csv_data[$_csv_i]=preg_replace('/^'.$e.'(.*)'.$e.'$/s','$1',$_csv_data[$_csv_i]);
				$_csv_data[$_csv_i]=str_replace($e.$e, $e, $_csv_data[$_csv_i]);
			}
			return empty($_line) ? false : $_csv_data;
		}
	
	/**
	 * ダウンロードファイルの出力
	 * 
	 * @param	string	$sourceFilename	// 絶対パス
	 * @param	string	$text			// テキスト
	 * @param	string	$downloadFilename	// ダウンロード時のファイル名
	 * @param	bool	$isAttach	// TRUE:添付ファイル, FALSE:拡張子別
	 * @return	binary
	 */
	function getDownload($sourceFilename = null, $text = null, $downloadFilename = null, $isAttach = false)
	{
    	// ファイルの存在チェック
    	if (!file_exists($sourceFilename) && is_null($text)) {
    		return false;
    	}
    	
    	// ファイルタイプ別ヘッダ
    	$isMP4 = false;
    	if ($isAttach) {
    		// 必ずダウンロードさせる
    		$type = 'application/octet-stream';
    		$disposition = 'attachement';
    	} else {
    		// ブラウザで表示
			$disposition = 'inline';
	    	$pathParts = pathinfo($sourceFilename);
	    	switch (strtolower($pathParts['extension']))
	    	{
	    	case 'm4v':
	    		$type = 'video/x-m4v';
	    		break;

	    	case 'mp4':
	    		$type = 'video/mp4';
	    		$isMP4 = true;
	    		break;

	    	case 'jpg':
	    	case 'jpeg':
	    		$type = 'image/jpeg';
	    		break;
	    		
	    	case 'tif':
	    	case 'tiff':
	    		$type = 'image/tiff';
	    		break;
	    		
	    	case 'gif':
	    	case 'png':
	    	case 'bmp':
	    		$type = 'image/' . $pathParts['extension'];
	    		break;
	    		
	    	case 'mp3':
	    		$type = 'audio/x-mp3';
	    		break;
			
			case 'pdf':
	    		$type = 'application/pdf';
	    		break;
	    		
	    	default:
	    		$type = 'application/octet-stream';
	    		$disposition = 'attachement';
	    		break;
	    		
	    	}
    	}

    	if (is_null($downloadFilename)) {
    		$downloadFilename = $sourceFilename;
    	}
		
		if ($isMP4) {
			// HTTP_RANGE
			// see http://www.systemexpress.co.jp/php/mp4.html
			
			$size = filesize($sourceFilename);
			$length = $size;
			
			$etag = md5($_SERVER['REQUEST_URI']).$size;
			$fp = fopen($sourceFilename,"rb");

			
			if (!@$_SERVER['HTTP_RANGE']) {
				header('Accept-Ranges: bytes');
	    		header('Content-Type: video/mp4');
			} else {
			    header('HTTP/1.1 206 Partial Content');
			    header('Accept-Ranges: bytes');
			    header('Content-Type: video/mp4');
				list($start,$end) = sscanf($_SERVER['HTTP_RANGE'],'bytes=%d-%d');
				$range = sprintf('bytes %d-%d/%d',$start,$end,$size);
	    		header('Content-Range: {$range}');
	    		$length = $end - $start + 1;
	    		fseek($fp,$start);
			}
			header('Content-Length: {$length}');
			header('Etag: "{$etag}"');
			echo fread($fp,$length);
			fclose($fp);
			// exit;
		} else {
	        ob_clean();
			// ヘッダ出力
			header('Content-type: ' . $type);
	    	header('Content-length: ' . $sourceFilename ? filesize($sourceFilename) : strlen($text));
			header('Content-Disposition: ' . $disposition . '; filename="' . $downloadFilename . '"');
	
	    	if ($sourceFilename) {
				// ファイルを読んで出力 
				readfile($sourceFilename, 'rb');
	    	} elseif ($text) {
	    		// テキストを出力
	    		echo ($text);
	    	}
	    	
	        ob_end_flush();
			// exit;			
		}

	}
	
	// ディレクトリを作成して777に
	public function setDirectoryPermission($directory, $targetDirectory = '')
	{
	    $directories = explode('/', $directory);
	    if (is_array($directories)) {
	        foreach ($directories as $number => $d) {
	        	if ($d) {
		            $targetDirectory .= '/' . $d;
		            if (!file_exists($targetDirectory)) {
		                mkdir($targetDirectory);
		                @chmod($targetDirectory, 0777);
		            } elseif (count($directories) == $number + 1) {
		                @chmod($targetDirectory, 0777);
		            }
	        	}
	        }
	    }
	    return $directory;
	}
	
	/**
	 * unlink<br>
	 * 基本は生unlinkではなくこちらを使用する。
	 * @param string $pathname パス
	 * @param boolean $recursive 再帰フラグ
	 * @return type
	 */
	function unlink($pathname, $recursive = true)
	{
		if (!file_exists($pathname)) {
			return;
		}
		
		if (is_dir($pathname)) {
			if ($recursive) {
				foreach (glob($pathname . '/*') as $subpath) {
					$this->unlink($subpath, $recursive);
				}
			}
			rmdir($pathname);
		} else {
			if (!unlink($pathname)) {
			    echo 'cannot unlink ' . $pathname . PHP_EOL;
			}
		}
	}
	
	// バイト数をKB,MB,GB表示にする
	function prettyByte2Str($bytes)
	{
		if ($bytes >= 1073741824) {
			$bytes = number_format($bytes / 1073741824, 2) . ' GB';
		} elseif ($bytes >= 1048576) {
			$bytes = number_format($bytes / 1048576, 2) . ' MB';
		} elseif ($bytes >= 1024) {
			$bytes = number_format($bytes / 1024, 2) . ' KB';
		} elseif ($bytes > 1) {
			$bytes = $bytes . ' bytes';
		} elseif ($bytes == 1) {
			$bytes = $bytes . ' byte';
		} else {
			$bytes = '0 bytes';
		}
		return $bytes;
	}
}