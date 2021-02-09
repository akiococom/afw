<?php

function smarty_modifier_zen($str)
{
	return mb_convert_kana($str, 'asKV');
}

function smarty_modifier_han($str)
{
	return mb_convert_kana($str, 'ask');
}

function smarty_modifier_filename($filename)
{
	$pathinfo = pathinfo($filename);
	return smarty_modifier_topic($pathinfo['filename'], 10) . '.' . $pathinfo['extension'];
}

function smarty_modifier_numeric($numeric)
{
	$int = floor($numeric);
	$decimal = (string)($numeric - $int);
	if ((float)$decimal == 0) {
		return number_format($int);
	} else {
		return number_format($int) . '.' . substr(str_replace('0.', '', $decimal), 0, 2);	
	}
}

function smarty_function_afwinput($params, &$smarty)
{
	if (isset($params['type'])) {
		$type = $params['type'];
	} else {
		$type = 'hidden';
	}

	if (isset($params['id'])) {
		$id = ' id="' . $params['id'] . '"';
	} else {
		$id = '';
	}
	
	if (isset($params['class'])) {
		$class = $params['class'];
	} else {
		$class = '';
	}
	
	if (isset($params['name'])) {
		$name = ' name="' . $params['name'] . '"';
	} else {
		$name = '';
	}
	
	if (isset($params['value'])) {
		$value = ' value="' . $params['value'] . '"';
		if (isset($params['checked'])) {
			// if ($params['value'] == $params['checked']) {
			if ($params['checked']) {
				$value .= ' checked="checked"';
			}
		}
	} else {
		$value = '';
	}
	
	if (isset($params['maxlength'])) {
		$maxlength = ' maxlength="' . $params['maxlength'] . '"';
	} else {
		$maxlength = '';
	}
	
	if (isset($params['placeholder'])) {
		$placeholder = ' placeholder="' . $params['placeholder'] . '"';
	} else {
		$placeholder = '';
	}
	
	if (isset($params['option'])) {
		$option = $params['option'];
	} else {
		$option = '';
	}
	
	if ($type != 'hidden' && $type != 'checkbox' && $type != 'radio') {
		$class .= ' form-control';
	}
	
	return sprintf('<input %s type="%s" class="%s" %s %s %s %s %s/>', $id, $type, $class, $name, $value, $maxlength, $placeholder, $option);
}

function smarty_function_afwtextarea($params, &$smarty)
{
	if (isset($params['id'])) {
		$id = ' id="' . $params['id'] . '"';
	} else {
		$id = '';
	}
	
	if (isset($params['class'])) {
		$class = $params['class'];
	} else {
		$class = '';
	}
	$class .= ' form-control';
	
	if (isset($params['name'])) {
		$name = ' name="' . $params['name'] . '"';
	} else {
		$name = '';
	}
	
	if (isset($params['value'])) {
		$value = $params['value'];
	} else {
		$value = '';
	}
	
	if (isset($params['rows'])) {
		$rows = ' rows="' . $params['rows'] . '"';
	} else {
		$rows = '';
	}
	
	if (isset($params['cols'])) {
		$cols = $params['cols'];
	} else {
		$cols = '';
	}
	
	if (isset($params['option'])) {
		$option = $params['option'];
	} else {
		$option = '';
	}
	
	if (isset($params['placeholder'])) {
		$placeholder = ' placeholder="' . $params['placeholder'] . '"';
	} else {
		$placeholder = '';
	}
	
	return sprintf('<textarea class="%s" %s %s %s %s %s %s>%s</textarea>', $class, $id, $name, $rows, $cols, $placeholder, $option, $value);
}

function smarty_function_afwselect($params, &$smarty)
{
	if (isset($params['name'])) {
		$name = 'name="' . $params['name'] . '"';
	} else {
		$name = '';
	}
	
	if (isset($params['class'])) {
		$class = $params['class'];
	} else {
		$class = '';
	}
	$class .= ' form-control';
	
	$options = array();
	if (isset($params['default'])) {
		$options[] = sprintf('<option value="">%s</option>', $params['default']);
	}
	
	if (isset($params['option'])) {
		$option = $params['option'];
	} else {
		$option = '';
	}
	
	if (isset($params['options']) && is_array($params['options'])) {
		foreach ($params['options'] as $key => $o) {
			$selected = '';
			if (isset($params['selected'])) {
				if ((string)$key === (string)$params['selected']) {
					$selected = ' selected="selected"';
				}
			}
			$options[] = sprintf('<option value="%s"%s>%s</option>', $key, $selected, $o);
		}
	}
	
	return sprintf('<select %s class="%s" %s>%s</select>', $name, $class, $option, implode(PHP_EOL, $options));
}

function smarty_modifier_tel($tel)
{
	$tels = array(
		'011' => 3, '0123' => 2, '0124' => 2, '0125' => 2, '0126' => 2, '01267' => 1, '0133' => 2, '0134' => 2, '0135' => 2, '0136' => 2, 
		'01372' => 1, '01374' => 1, '0137' => 2, '01377' => 1, '0138' => 2, '01392' => 1, '0139' => 2, '01397' => 1, '01398' => 1, '0142' => 2, 
		'0143' => 2, '0144' => 2, '0145' => 2, '01456' => 1, '01457' => 1, '0146' => 2, '01466' => 1, '0152' => 2, '0153' => 2, '0154' => 2, 
		'01547' => 1, '015' => 3, '0155' => 2, '01558' => 1, '0156' => 2, '01564' => 1, '0157' => 2, '0158' => 2, '01586' => 1, '01587' => 1, 
		'0162' => 2, '01632' => 1, '01634' => 1, '01635' => 1, '0163' => 2, '0164' => 2, '01648' => 1, '0165' => 2, '01654' => 1, '01655' => 1, 
		'01656' => 1, '01658' => 1, '0166' => 2, '0167' => 2, '0172' => 2, '0173' => 2, '0174' => 2, '0175' => 2, '0176' => 2, '017' => 3, 
		'0178' => 2, '0179' => 2, '0182' => 2, '0183' => 2, '0184' => 2, '0185' => 2, '0186' => 2, '0187' => 2, '018' => 3, '0191' => 2, 
		'0192' => 2, '0193' => 2, '0194' => 2, '0195' => 2, '019' => 3, '0197' => 2, '0198' => 2, '022' => 3, '0220' => 2, '0223' => 2, 
		'0224' => 2, '0225' => 2, '0226' => 2, '0228' => 2, '0229' => 2, '0233' => 2, '0234' => 2, '0235' => 2, '023' => 3, '0237' => 2, 
		'0238' => 2, '0240' => 2, '0241' => 2, '0242' => 2, '0243' => 2, '0244' => 2, '024' => 3, '0246' => 2, '0247' => 2, '0248' => 2, 
		'025' => 3, '0250' => 2, '0254' => 2, '0255' => 2, '0256' => 2, '0257' => 2, '0258' => 2, '0259' => 2, '0260' => 2, '0261' => 2, 
		'026' => 3, '0263' => 2, '0264' => 2, '0265' => 2, '0266' => 2, '0267' => 2, '0268' => 2, '0269' => 2, '0270' => 2, '027' => 3, 
		'0274' => 2, '0276' => 2, '0277' => 2, '0278' => 2, '0279' => 2, '0280' => 2, '0282' => 2, '0283' => 2, '0284' => 2, '0285' => 2, 
		'028' => 3, '0287' => 2, '0288' => 2, '0289' => 2, '0291' => 2, '029' => 3, '0293' => 2, '0294' => 2, '0295' => 2, '0296' => 2, 
		'0297' => 2, '0299' => 2, '03' => 4, '0422' => 2, '042' => 3, '0428' => 2, '04' => 4, '043' => 3, '0436' => 2, '0438' => 2, 
		'0439' => 2, '044' => 3, '045' => 3, '0460' => 2, '046' => 3, '0463' => 2, '0465' => 2, '0466' => 2, '0467' => 2, '0470' => 2, 
		'047' => 3, '0475' => 2, '0476' => 2, '0478' => 2, '0479' => 2, '048' => 3, '0480' => 2, '049' => 3, '0493' => 2, '0494' => 2, 
		'0495' => 2, '04992' => 1, '04994' => 1, '04996' => 1, '04998' => 1, '052' => 3, '053' => 3, '0531' => 2, '0532' => 2, '0533' => 2, 
		'0536' => 2, '0537' => 2, '0538' => 2, '0539' => 2, '054' => 3, '0544' => 2, '0545' => 2, '0547' => 2, '0548' => 2, '0550' => 2, 
		'0551' => 2, '055' => 3, '0553' => 2, '0554' => 2, '0555' => 2, '0556' => 2, '0557' => 2, '0558' => 2, '0561' => 2, '0562' => 2, 
		'0563' => 2, '0564' => 2, '0565' => 2, '0566' => 2, '0567' => 2, '0568' => 2, '0569' => 2, '0572' => 2, '0573' => 2, '0574' => 2, 
		'0575' => 2, '0576' => 2, '05769' => 1, '0577' => 2, '0578' => 2, '058' => 3, '0581' => 2, '0584' => 2, '0585' => 2, '0586' => 2, 
		'0587' => 2, '059' => 3, '0594' => 2, '0595' => 2, '0596' => 2, '0597' => 2, '05979' => 1, '0598' => 2, '0599' => 2, '06' => 4, 
		'072' => 3, '0721' => 2, '0725' => 2, '073' => 3, '0735' => 2, '0736' => 2, '0737' => 2, '0738' => 2, '0739' => 2, '0740' => 2, 
		'0742' => 2, '0743' => 2, '0744' => 2, '0745' => 2, '0746' => 2, '07468' => 1, '0747' => 2, '0748' => 2, '0749' => 2, '075' => 3, 
		'0761' => 2, '076' => 3, '0763' => 2, '0765' => 2, '0766' => 2, '0767' => 2, '0768' => 2, '0770' => 2, '0771' => 2, '0772' => 2, 
		'0773' => 2, '0774' => 2, '077' => 3, '0776' => 2, '0778' => 2, '0779' => 2, '078' => 3, '0790' => 2, '0791' => 2, '079' => 3, 
		'0794' => 2, '0795' => 2, '0796' => 2, '0797' => 2, '0798' => 2, '0799' => 2, '082' => 3, '0820' => 2, '0823' => 2, '0824' => 2, 
		'0826' => 2, '0827' => 2, '0829' => 2, '083' => 3, '0833' => 2, '0834' => 2, '0835' => 2, '0836' => 2, '0837' => 2, '0838' => 2, 
		'08387' => 1, '08388' => 1, '08396' => 1, '0845' => 2, '0846' => 2, '0847' => 2, '08477' => 1, '0848' => 2, '084' => 3, '08512' => 1, 
		'08514' => 1, '0852' => 2, '0853' => 2, '0854' => 2, '0855' => 2, '0856' => 2, '0857' => 2, '0858' => 2, '0859' => 2, '086' => 3, 
		'0863' => 2, '0865' => 2, '0866' => 2, '0867' => 2, '0868' => 2, '0869' => 2, '0875' => 2, '0877' => 2, '087' => 3, '0879' => 2, 
		'0880' => 2, '0883' => 2, '0884' => 2, '0885' => 2, '088' => 3, '0887' => 2, '0889' => 2, '0892' => 2, '0893' => 2, '0894' => 2, 
		'0895' => 2, '0896' => 2, '0897' => 2, '0898' => 2, '089' => 3, '092' => 3, '0920' => 2, '093' => 3, '0930' => 2, '0940' => 2, 
		'0942' => 2, '0943' => 2, '0944' => 2, '0946' => 2, '0947' => 2, '0948' => 2, '0949' => 2, '09496' => 1, '0950' => 2, '0952' => 2, 
		'0954' => 2, '0955' => 2, '0956' => 2, '0957' => 2, '095' => 3, '0959' => 2, '096' => 3, '0964' => 2, '0965' => 2, '0966' => 2, 
		'0967' => 2, '0968' => 2, '0969' => 2, '0972' => 2, '0973' => 2, '0974' => 2, '097' => 3, '0977' => 2, '0978' => 2, '0979' => 2, 
		'098' => 3, '0980' => 2, '09802' => 1, '0982' => 2, '0983' => 2, '0984' => 2, '0985' => 2, '0986' => 2, '0987' => 2, '09912' => 1, 
		'09913' => 1, '099' => 3, '0993' => 2, '0994' => 2, '0995' => 2, '0996' => 2, '09969' => 1, '0997' => 2,
		'050' => 4, '070' => 4, '080' => 4, '090' => 4, '0120' => 3,
	);
	$devidedTels = array();
	$tel = str_replace('-', '', $tel);
	for ($i = 5; $i >= 2; $i--) {
		if (isset($tels[substr($tel, 0, $i)])) {
			$devidedTels[] = substr($tel, 0, $i);
			$devidedTels[] = substr($tel, $i, $tels[substr($tel, 0, $i)]);
			$devidedTels[] = substr($tel, $tels[substr($tel, 0, $i)] + $i);
			break;
		}
	}
	return implode('-', $devidedTels);
}

function smarty_modifier_time($datetime)
{
	return date('G:i', strtotime($datetime));
}

/**
 * smarty modifier : bytes
 *
 * K, M, G, T に変換する
 *
 * @param	int	$byte
 * @param	int	$digit	小数点
 * @param	string	$mode	nullでxxxMB、'number''で数値のみ、'unit'で単位のみ
 * @param	bool	$kilo	1000以下はそのままでかつ小数点で出力
 * @param	int	$unitBytes	基本にする単位
 * @return	string
 */
function smarty_modifier_bytes($byte, $digit = 1, $mode = null, $kilo = true, $unitBytes = null)
{
	$unit = 1024;
	$prevByte = $byte;
	$unitNames = array('', 'K', 'M', 'G', 'T');
	$unitCount = 0;

	// 単位別にループで計算
	while (($byte > $unit && is_null($unitBytes)) || (!is_null($unitBytes) && $unitBytes > $unit)) {
		$byte = $byte / $unit;
		if (!is_null($unitBytes)) {
			$unitBytes = $unitBytes / $unit;
		}
		if (($kilo && $byte >= 10) || !$kilo) {
			// 2桁以上
			$unitCount++;
		} else {
			$byte = $prevByte;
			break;
		}
		$prevByte = $byte;
	}

	// 1000以上は小数点を表示
	if ($mode == 'unit') {
		return $unitNames[$unitCount];
	}
	if (!$kilo) {
		return $byte;
	} else {
		if ($byte < 1000) {
			if ($mode == 'number') {
				return number_format(sprintf('%.' . $digit . 'f', $byte), $digit);
			} else {
				return number_format(sprintf('%.' . $digit . 'f', $byte), $digit) . ($mode != 'number' ? ('<span>' . $unitNames[$unitCount] . 'B</span>') : '');
			}
		} else {
			if ($mode == 'number') {
				return sprintf('%s', number_format($byte));
			} else {
				return sprintf('%s<span>%sB</span>', number_format($byte), $unitNames[$unitCount]);
			}
		}
	}
}

// 期間（日付の簡易処理）　※ 日本語のみ
function smarty_modifier_period($start, $end)
{
	$date = '';
	
	$startDate = date('Ymd', strtotime($start));
	$endDate = date('Ymd', strtotime($end));
	
	$date = date('Y年n月j日(' . smarty_modifier_week(date('w', strtotime($start))) . ') H:i', strtotime($start));
	
	if ($startDate == $endDate) {
		$date .= ' 〜 ' . date('H:i', strtotime($end));
	} else {
		$date .= ' 〜 ' . date('Y年n月j日(' . smarty_modifier_week(date('w', strtotime($end))) . ') H:i', strtotime($end));
	}
	
	return $date;
}


// 割合(%)を取得
function smarty_modifier_rate($number, $deno)
{
	if (!is_numeric($number) || !is_numeric($deno)) {
		return 0;
	}
	if (!$deno) {
		return 100;
	}
	$rate = floor($number / $deno * 100);
	if ($rate > 100) {
		return 100;
	} else {
		return $rate;
	}
}

function smarty_modifier_lang($string)
{
	global $lang;
	$params = array_slice(func_get_args(), 2);

	if (!isset($lang[$string])) {
		if ($params) {
			return vsprintf($string, $params);
		} else {
			return $string;
		}
	}

	if ($params) {
		return vsprintf($lang[$string], $params);
	} else {
		return $lang[$string];
	}
}

// 文字数を調整
function smarty_modifier_space($string, $space = 10)
{
	$length = strlen($string);
	if ($length > $space) {
		$string = substr($string, 0, $space - 2);
		$postfix = '…';
	} else {
		$postfix = str_repeat(' ', $space - $length);
	}
	return $string . $postfix;
}

function smarty_modifier_qrsum($str)
{
	return substr(md5($str), 0, 3);
}

// 曜日を取得
function smarty_modifier_week($week, $isNumeric = false)
{
	$weekday = array(
		smarty_modifier_lang('日'),
		smarty_modifier_lang('月'),
		smarty_modifier_lang('火'),
		smarty_modifier_lang('水'),
		smarty_modifier_lang('木'),
		smarty_modifier_lang('金'),
		smarty_modifier_lang('土')
	);
	if (is_null($week)) {
		return $weekday;
	} else {
		return $weekday[$week];
	}
}

/**
 *  smarty modifier:view_link()
 *  sample:
 *  @param string $str
 *  @return string
 */
function smarty_modifier_link($str)
{
	$str = preg_replace('/(https?:\/\/[\w\.\~\-\/\?\&\+\=\:\@\%]+)/i', '<A href="${1}" target="_blank" class="a-link">${1}</A>', $str);
	return $str;
}

// 日付を取得
function smarty_modifier_day($date)
{
	return intval(date('d', strtotime($date)));
}

// 日付形式かチェック
function smarty_modifier_is_date($date)
{
	if ($date == date('Y-m-d', strtotime($date))) {
		return true;
	} else {
		return false;
	}
}

/**
 * 年齢取得(簡易処理)
 * 
 * @param	string	$date(0000-00-00)
 * @return	int
 */
function smarty_modifier_age($date)
{
	$date = str_replace('-', '', $date);
	return intval((intval(date('Ymd')) - intval($date)) / 10000);
}

/**
 *	smarty modifier: date
 *	日付変数を日付だけ表示
 *
 *	@param	string	$date(0000-00-00 00:00:00)
 *  @param	bool	$year	TRUE: 年を表示
 *	@param	bool	$day	(TRUE=日を表示)
 *  @param	bool	$week	TRUE: 曜日を表示
 *  @param	bool	$isJpn	TRUE: 月日  FALSE: /
 *  @param	bool	$shorYear	TRUE: 2008->08
 *	@return string	00:00
 */
function smarty_modifier_date($datetime, $year = true, $day = true, $week = false, $isJpn = true, $shortYear = false)
{
	$weeks = array('日', '月', '火', '水', '木', '金', '土');
	
	$datetimes = split(' ', $datetime);
	$dates = split('-', $datetimes[0]);
	if (count($dates) < 2) {
		$dates = split('/', $datetimes[0]);
	}
	if (!count($dates)) {
		$week = false;
	}
	
	return ($year?(($shortYear)?substr($dates[0], -2, 2):$dates[0]) . ($isJpn?'年':'/'):'') . intval($dates[1]) . ($isJpn?'月':'/') . ($day?(intval($dates[2]) . ($isJpn?'日':'')):'')
	       . ($week?'(' . $weeks[date('w', mktime(0,0,0,$dates[1],$dates[2],$dates[0]))] . ')':'');
}

/**
 * smarty modifier: topic
 * 
 * @param	string	$str
 * @param	int	$length
 * @param	string	$etc
 * @return	string
 */
function smarty_modifier_topic($str, $length = 80, $etc = '...')
{
	if (mb_strlen($str, 'utf8') > $length) {
		return mb_substr($str, 0, $length, 'utf8') . $etc;
	} else {
		return $str;
	}
}

/**
 * smarty modifier: html
 * 
 * @param	string	$str
 * @param	bool	$isBr	TRUE:<BR>タグ自動付加
 * @return
 */
function smarty_modifier_html($str, $isBr = true)
{
	if ($isBr) {
		$source = array('&lt;', '&gt;', "\n", '\\&', '\\', '  ');
		$target = array('<', '>', '<br />', '&', '&#xa5;', ' &nbsp;');
	} else {
		$source = array('&lt;', '&gt;', '\\&', '\\');
		$target = array('<', '>', '&', '&#xa5;');
	}
	return str_replace($source, $target, stripslashes($str));
}

/**
 * smarty modifier: br
 * 改行を<br />タグに置き換える
 * 
 * @param	string	$srt
 * @return	
 */
function smarty_modifier_br($str)
{
	$source = array("\n", '\\&', '\\', '  ');
	$target = array('<br />', '&', '&#xa5;', ' &nbsp;');
	return str_replace($source, $target, stripslashes($str));
}

/**
 *	smarty modifier: datetime
 *	日付変数を日本語表示に
 *
 *	@param	string	$date(0000-00-00 00:00:00)
 *	@return string	xxx年x月x日 00:00
 */
function smarty_modifier_datetime($datetime, $useYear = true, $useWeek = true, $isDate = true, $isTime = true, $isHideSameYear = false, $isHideEdge = false)
{
	if (!strtotime($datetime) || $datetime == '0000-00-00 00:00:00') {
		return '';
	}

	$time = strtotime($datetime);
	$hhmm = date('H:i', $time);
	$year = date('Y', $time);

	if ($isDate) {
		if ($useYear) {
			if ($_SESSION['lang'] != 'en') { // フレームワークのためSESSION直接参照
				if ($isHideSameYear && date('Y') == $year) {
					$date = 'n月j日';
				} else {
					$date = 'Y年n月j日';
				}
			} else {
				if ($isHideSameYear && date('Y') == $year) {
					$date = 'M d';
				} else {
					$date = 'M d,Y';
				}
			}
		} else {
			if ($_SESSION['lang'] != 'en') { // フレームワークのためSESSION直接参照
				$date = 'n月j日';
			} else {
				$date = 'M d';
			}
		}
		if ($useWeek) {
			$date .= '(' . '\\' . implode('\\', str_split(smarty_modifier_week(date('w', $time)))) . ')'; // dateフォーマット対策
		}
		$date .= ' ';
	} else {
		$date = '';
	}
	if ($isTime && $hhmm != '00:00') {
		return date($date . 'H:i', $time);
	} else {
		return date($date, $time);
	}
}

/**
 *  smarty modifier:strip()
 *  sample:
 *  @param string $str	バックスラッシュ文字列
 *  @return string
 */
function smarty_modifier_strip($str)
{
	$source = array('\\&', '\\','&#039;','&quot;','&amp;');
	$target = array('&', '&#xa5;', '\'', '"', '&');
	return str_replace($source, $target, $str);
	//return str_replace($source, $target, stripslashes($str));
}

/**
 *  smarty modifier: radio()
 *  sample:
 *  @param	string	$v
 *  @param	string	$correct
 *  @return string
 */
function smarty_modifier_radio($v, $correct, $default = null)
{
	if ($v == $correct || $v == $default) {
		return 'checked="true" ';
	} else {
		return '';
	}
}

/**
 *	smarty function:messages()
 *	sample:
 *	@param	array	$param 	 
 *			('errors' => エラー配列, 
 *			 'message' => メッセージ,
 *			)
 *	@return	string	HTML
 */
function smarty_function_messages($param, &$smarty)
{
	$html = '';
	
	if (is_array($param['errors'])) {
		foreach ($param['errors'] as $e) {
			$html .= '<span class="error">' . $e . '</span><br />';
		}
	}
	
	if ($param['message']) {
		$html .= '<span class="message">' . stripslashes($param['message']) . '</span><br />';
	}
	
	return $html;
}

/**
 *	smarty function:pager()
 *	sample:
 *	{pager total=$app.topic_count per=2 current=$form.p url="/topics/PAGE/"}
 *	{pager total=$app.topic_count per=2 current=$form.p onclick="location.href='index.php?action=hoge_hoge&list=PAGE'"}
 *	{pager total=$app.topic_count per=2 current=$form.p url="`$config.url`topics/PAGE/" units="Total: TOTAL  From:BEGIN To:END" unit="CURRENT: BEGIN"}
 *
 *	@param	array	$param 	 
 *			('total' => トータルの件数, 
 *			 'per' => ページあたりの件数,
 *			 'current' => 現在のページ(デフォルト:1)
 *
 *			 'url' => ページ移動のURL(PAGEがページ番号に置換される)
 *			 'onclick' => onclick時の文字列(PAGEがページ番号に置換される)
 *
 *			 'range' => 表示するページ数(省略で全てのページ),
 *			 'nolist' => リスト表示しない・する(trueで次へ戻るのみ)
 *			 'edge' => 最初・最後を表示(trueで表示する)
 *
 *			 'next' => 次へボタンの名称(省略で 次へ)
 *			 'prev' => 前へボタンの名称(省略で 前へ)
 *			 'nextedge' => 最後へボタンの名称(省略で 最後へ)
 *			 'prevedge' => 最初へボタンの名称(省略で 最初へ)
 *			 'units' => 件数表示 (省略でTOTAL件中BEGIN～END件を表示) 
 *			 'unit' => 1ページの場合の表示(省略で　BEGINページ)
 *			)
 *	@return	string
 */
function smarty_function_pager($param, &$smarty)
{
	$html = '';
	
	// 必須項目を変数に代入
	$total = $param['total'];
	$per = $param['per']?$param['per']:1;
	$current = ($param['current'] > 0)?$param['current']:1;
	$url = $param['url'];
	
	// 現在の表示件数のセット
	if ($total > 0) {
		$pageBegin = (($current - 1) * $per + 1);
		$pageEnd = (($current * $per) > $total)?$total:($current * $per);
	} else {
		$pageBegin = 0;
		$pageEnd = 0;
	}
	
	// 現在の件数をHTMLにセット
	if ($pageBegin < $pageEnd) {
		if (isset($param['units'])) {
			$htmlCurrent = $param['units'];
		} else {
			$htmlCurrent = 'TOTAL件中BEGIN～END件';
		}
		$htmlCurrent = str_replace(array('TOTAL', 'BEGIN', 'END'), array($total, $pageBegin, $pageEnd), $htmlCurrent);
	} else {
		if (isset($param['unit'])) {
			$htmlCurrent = $param['unit'];
		} else {
			$htmlCurrent = 'BEGINページ';
		}
		$htmlCurrent = str_replace('BEGIN', $pageBegin, $htmlCurrent);
	}
	
	// onclickパラメータのセット
	if (isset($param['onclick'])) {
		$onclick = $param['onclick'];
	} else {
		$onclick = null;
	}
	
	// 次へボタン
	if (isset($param['next'])) {
		$next = $param['next'];
	} else {
		$next = '次へ';
	}
	
	// 前へボタン
	if (isset($param['prev'])) {
		$prev = $param['prev'];
	} else {
		$prev = '前へ';
	}
	
	// 最初へボタン
	if (isset($param['prevedge'])) {
		$prevedge = $param['prevedge'];
	} else {
		$prevedge = '最初へ';
	}
	
	// 最後へボタン
	if (isset($param['nextedge'])) {
		$nextedge = $param['nextedge'];
	} else {
		$nextedge = '最後へ';
	}


	// 次のページをHTMLにセット
	if (($current * $per) < $total) {
		if ($onclick) {
			$htmlNext = sprintf('<li><a href="#" onclick="%s; return false;" aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>', str_replace('PAGE', ($current + 1), $onclick));
			if (isset($param['edge'])) {
				$htmlNext .= sprintf('<li><a href="#" onclick="%s; return false;" aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>', str_replace('PAGE', (ceil($total/$per)), $onclick));
			}
		} else {
			$htmlNext = sprintf('<li><a href="%s" aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>', str_replace('PAGE', ($current + 1), $url));
			/*
			$htmlNext = sprintf('<a href="%s">%s</a>', str_replace('PAGE', ($current + 1), $url), $next);
			if (isset($param['edge'])) {
				$htmlNext .= sprintf(' | <a href="%s">%s</a>', str_replace('PAGE', (ceil($total/$per)), $url), $nextedge);
			}
			*/
		}
	} else {
		$htmlNext = '<li class="disabled"><span aria-hidden="true">&raquo;</span></li>';
		/*
		if (isset($param['edge'])) {
			$htmlNext = sprintf('<span class="gray">%s | %s</span>', $next, $nextedge);
		} else {
			$htmlNext = sprintf('<span class="gray">%s</span>', $next);
		}
		*/
	}
		
	// 戻るページHTMLをセット
	if ($current > 1) {
		if ($onclick) {
			if (isset($param['edge'])) {
				$htmlPrev = sprintf('<li><a href="#" onclick="%s; return false;" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>', str_replace('PAGE', 1, $onclick));
			}
			$htmlPrev .= sprintf('<li><a href="#" onclick="%s; return false;" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>', str_replace('PAGE', ($current - 1), $onclick));
		} else {
			$htmlPrev = sprintf('<li><a href="%s" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>', str_replace('PAGE', ($current - 1), $url));
			/*
			if (isset($param['edge'])) {
				$htmlPrev = sprintf('<a href="%s">%s</a> | ', str_replace('PAGE', 1, $url), $prevedge);
			}
			$htmlPrev .= sprintf('<a href="%s">%s</a> | ', str_replace('PAGE', ($current - 1), $url), $prev);
			*/
		}
	} else {
		$htmlPrev = '<li class="disabled"><span aria-hidden="true">&laquo;</span></li>';
		/*
		if (isset($param['edge'])) {
			$htmlPrev = sprintf('<span class="gray">%s | %s</span> | ', $prevedge, $prev);
		} else {
			$htmlPrev = sprintf('<span class="gray">%s</span> | ', $prev);
		}
		*/
	}
	
	// ページリストの作成
	$html_list = '';
	
	/* パラメーターrangeに合わせて表示をずらす処理を追加 */
	
	if (!$total) {
		$total = 1;
	}
	
	// rangeが指定されている
	if (isset($param['range'])) {
		$begin = $current - $param['range'];
		$end = $current + $param['range'] + (($pageBegin < 2)?1:0);
		
		// 最小値が1以下
		if ($begin < 1) {
			$diff = - $begin;
			$begin = 1;
		} else {
			$diff = 0;
		}
		
		$end += $diff;
		
		if ($end > ceil($total / $per)) {
			$diff = $end - ceil($total / $per);
			$end = ceil($total / $per);
		} else {
			$diff = 0;
		}
		
		$begin -= $diff;
		
		if ($begin < 1) {
			$begin = 1;
		}
		
	// rangeが指定されていない
	} else {
		$begin = 1;
		$end = ceil($total / $per);
	}
	
	// HTMLの設定
	$i = 0;
	foreach (range($begin, $end) as $page) {
		if ($page == $current) {
			$htmlList .= '<li class="active"><a href="#">' . $page .  '<span class="sr-only">(current)</span></a></li>';
			//$htmlList .= '<b class="currentpage">' . $page . '</b> ';
		} else {
			if ($onclick) {
				$htmlList .= sprintf('<li><a href="#" onclick="%s; return false;">%d</a></li>', str_replace('PAGE', $page, $onclick), $page);
			} else {
				$htmlList .= sprintf('<li><a href="%s">%d</a></li>', str_replace('PAGE', $page, $url), $page);
				//$htmlList .= sprintf('<a href="%s">%d</a> ', str_replace('PAGE', $page, $url), $page);
			}
		}
	}
	
	// 件数表示
	
	// HTMLの生成
	$html = '<nav aria-label="Page navigation">';
	// $html .= '<div class="pagecount">' . $htmlCurrent . '</div>';
	if ($end > 1) {
		$html .= sprintf('<ul class="pagination">%s%s%s</ul>', $htmlPrev, $htmlList, $htmlNext);
		// $html .= sprintf('<ul class="pagination">%s%s%s</ul>', $htmlPrev, (!isset($param['nolist'])?$htmlList . ' | ':''), $htmlNext);
	}
	$html .='</nav>';
	
	return $html;	
}

/**
 *	smarty function:required()
 *  必須項目マーク
 *	@return	string	<span class="required">*</span>
 */
function smarty_function_required($param, &$smarty)
{
	return '<span class="required">*</span>';
}
