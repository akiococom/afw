<?php
/*Excel_Peruser Ver0.10a
 *  Author:kishiyan
 *
 * Copyright (c) 2006-2007 kishiyan <excelreviser@gmail.com>
 * All rights reserved.
 *
 * Support
 *   URL  http://chazuke.com/forum/
 *
 * Redistribution and use in source, with or without modification, are
 * permitted provided that the following conditions are met:
 * 1. Redistributions of source code must retain the above copyright
 *    notice, this list of conditions and the following disclaimer,
 *    without modification, immediately at the beginning of the file.
 * 2. The name of the author may not be used to endorse or promote products
 *    derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE AUTHOR ``AS IS'' AND ANY EXPRESS OR
 * IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES
 * OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
 * IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT
 * NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF
 * THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

/*  HISTORY
2007.01.23 1st release

*/

require_once 'reviser.php';

define('Type_FONT', 0x31);
define('Type_FORMAT', 0x041e);
define('Type_XF', 0xe0);
define('Type_DEFAULTROWHEIGHT', 0x225);
define('Type_DEFCOLWIDTH', 0x55);
define('Type_COLINFO', 0x7d);
define('Type_MERGEDCELLS', 0xe5);
define('Type_HLINK', 0x1b8);
define('Const_fontH', 14);

class Excel_Peruser extends Excel_Reviser
{
	var $recFONT=array();
	var $recFORMAT=array();
	var $recXF=array();
	var $rowheight=array();
	var $colwidth=array();
	var $celmergeinfo=array();
	var $hlink=array();
	var $maxcell=array();
	var $maxrow=array();
	var $sheetnum;
	var $sheetname=array();
	var $palette=array(
		"000000","FFFFFF","FF0000","00FF00",
		"0000FF","FFFF00","FF00FF","00FFFF",
		"000000","FFFFFF","FF0000","00FF00",
		"0000FF","FFFF00","FF00FF","00FFFF",
		"800000","008000","000080","808000",
		"800080","008080","C0C0C0","808080",
		"9999FF","993366","FFFFCC","CCFFFF",
		"660066","FF8080","0066CC","CCCCFF",
		"000080","FF00FF","FFFF00","00FFFF",
		"800080","800000","008080","0000FF",
		"00CCFF","CCFFFF","CCFFCC","FFFF99",
		"99CCFF","FF99CC","CC99FF","FFCC99",
		"3366FF","33CCCC","99CC00","FFCC00",
		"FF9900","FF6600","666699","969696",
		"003366","339966","003300","333300",
		"993300","993366","333399","333333",
		"000000"	/* 0x40 */
		);
	var $infilename;
	var $headfoot=array();

	function Excel_Peruser(){

	}

	function fileread($filename){
		mb_regex_encoding($this->charset);
		$this->setconst();
		$this->infilename=$filename;
		$this->parseFile($filename);
		$this->getxf();
		$this->getheightwidth();
		$this->getmergecells();
		foreach($this->cellblock as $keysheet=>$sheet){
			$maxcol=0;
			foreach($sheet as $keyrow=>$rows){
				foreach($rows as $keycol=>$cell){
					if ($maxcol < $keycol) $maxcol=$keycol;
				}
			}
			$this->maxcell[$keysheet]=$maxcol;
			$this->maxrow[$keysheet]=$keyrow;
		}
		$this->sheetnum=count($this->boundsheets);
		foreach($this->boundsheets as $keysheet=>$sheet){
			$name = substr($sheet['name'],2);
			if (substr($sheet['name'],1,1)!="\x00") {
				$name = mb_convert_encoding($name,$this->charset,'UTF-16LE');
			}
			$this->sheetname[$keysheet]=$name;
		}
	}

	function getHEADER($sn){
		if (strlen(trim($this->headfoot[$sn]['header'])))
			return $this->sepheadfoot($this->headfoot[$sn]['header'],$sn);
		else
			return NULL;
	}

	function getFOOTER($sn){
		if (strlen(trim($this->headfoot[$sn]['footer'])))
			return $this->sepheadfoot($this->headfoot[$sn]['footer'],$sn);
		else
			return NULL;
	}

	function getColWidth($sn,$col){
		return (isset($this->colwidth[$sn][$col]))? $this->colwidth[$sn][$col]:$this->colwidth[$sn][-1];
	}

	function getRowHeight($sn,$row){
		return (isset($this->rowheight[$sn][$row]))? $this->rowheight[$sn][$row]:$this->rowheight[$sn][-1];
	}

	function getAttribute($sn,$row,$col){
		$xfno=$this->cellblock[$sn][$row][$col]['xf'];
		if ($xfno !== null) {
			$tmp=$this->recXF[$xfno];
			$tmp['xf']=$xfno;
			$tmp['format']=$this->recFORMAT[$this->recXF[$xfno]['formindex']];
			$tmp['font']=$this->recFONT[$this->recXF[$xfno]['fontindex']];
			return $tmp;
		} else return null;
	}

	function getxf(){
		$dat=$this->globaldat['presheet'];
		$pos=0;
		$i=0;
		$poslimit=strlen($dat);
		while ($pos < $poslimit){
			$code = $this->__get2($dat,$pos);
			$length = $this->__get2($dat,$pos+2);
			switch ($code) {
				case Type_FONT:
					$font['height']=$this->__get2($dat,$pos+4);
					$font['style']=$this->__get2($dat,$pos+6);
		//			$tmp=$this->__get2($dat,$pos+8);
		//			if ($tmp != 32767)
					$font['color']= $this->__get2($dat,$pos+8);
					$font['weight']=$this->__get2($dat,$pos+10);
					$font['escapement']=$this->__get2($dat,$pos+12);
					$font['underline']=$this->__get1($dat,$pos+14);
					$font['family']=$this->__get1($dat,$pos+15);
					$font['charset']=$this->__get1($dat,$pos+16);
					$font['fontname']=$this->getstring(substr($dat,$pos+18,$length-14),1);
					$this->recFONT[]=$font;
					unset($font);
					break;
				case Type_FORMAT:
					$fmt=$this->getstring(substr($dat,$pos+6,$length-2),2);
					$this->recFORMAT[$this->__get2($dat,$pos+4)]=$fmt;
					break;
				case Type_XF:
					$xf['attrib']=($this->__get1($dat,$pos+13) & 0xfc) >> 2;
					$xf['stylexf']=($this->__get1($dat,$pos+8) & 0x4) >> 2;
					$oya=($this->__get2($dat,$pos+8) & 0xfff0) >> 4;
					if ($oya != 0xfff) $xf['parent']=$oya;
					$cond = $xf['stylexf'] ? ~$xf['attrib'] : $xf['attrib'];
		//$cond=0xFF;
					if ($cond & 0x2)
						$xf['fontindex']=$this->__get2($dat,$pos+4)-1;
						else $xf['fontindex']=0;
					if ($cond & 0x1)
						$xf['formindex']=$this->__get2($dat,$pos+6);
					if ($cond & 0x4){
						$xf['halign']=$this->__get1($dat,$pos+10) & 0x7;
						$xf['wrap']=($this->__get1($dat,$pos+10) & 0x8) >> 3;
						$xf['valign']=($this->__get1($dat,$pos+10) & 0x70)>> 4;
						$xf['rotation']=$this->__get1($dat,$pos+11);
					}
					if ($cond & 0x8){
						$xf['Lstyle']=$this->__get1($dat,$pos+14) & 0x0f;
						$xf['Rstyle']=($this->__get1($dat,$pos+14) & 0xf0) >> 4;
						$xf['Tstyle']=$this->__get1($dat,$pos+15) & 0x0f;
						$xf['Bstyle']=($this->__get1($dat,$pos+15) & 0xf0) >> 4;
						$xf['Lcolor']=$this->__get1($dat,$pos+16) & 0x7f;
						$xf['Rcolor']=($this->__get2($dat,$pos+16) & 0x3f80) >> 7;
						$xf['diagonalL2R']=($this->__get1($dat,$pos+17) & 0x40) >> 6;
						$xf['diagonalR2L']=($this->__get1($dat,$pos+17) & 0x80) >> 7;
						$xf['Tcolor']=$this->__get1($dat,$pos+18) & 0x7f;
						$xf['Bcolor']=($this->__get2($dat,$pos+18) & 0x3f80) >> 7;
						$xf['Dcolor']=($this->__get4($dat,$pos+18) & 0x1fc000) >> 14;
						$xf['Dstyle']=($this->__get2($dat,$pos+20) & 0x1e0) >> 5;
					}
					if ($cond & 0x10){
						$xf['fillpattern']=($this->__get4($dat,$pos+21) & 0xfc) >> 2;
						$xf['PtnFRcolor']=$this->__get1($dat,$pos+22) & 0x7f;
						$xf['PtnBGcolor']=($this->__get2($dat,$pos+22)>> 7) & 0x7f;
					}
					$this->recXF[]=$xf;
					unset($xf);
					break;
			}
			$pos += $length + 4;
		}
	}

	function getstring(&$chars,$len){
		if ($len==1) {
			$strpos=2;
			$opt=$this->__get1($chars,1);
		} elseif ($len==2){
			$strpos=3;
			$opt=$this->__get1($chars,2);
		} else return substr($chars,2);
		if ($opt)
			return mb_convert_encoding(substr($chars,$strpos),$this->charset,'UTF-16LE');
		else
			return substr($chars,$strpos);
	}

	function getstyle($num){
		switch ($num) {
			case 0: $style='none;';	break;
			case 1: $style='solid;'; break;
			case 2: $style='solid;'; break;
			case 3: $style='dashed;'; break;
			case 4: $style='dotted;'; break;
			case 5: $style='solid;'; break;
			case 6: $style='double;'; break;
			case 7: $style='solid;'; break;
			case 8: $style='dashed;'; break;
			case 9: $style='dashed;'; break;
			case 10: $style='dashed;'; break;
			case 11: $style='dashed;'; break;
			case 12: $style='dashed;'; break;
			case 13: $style='dashed;'; break;
		}
		return $style;
	}

	function getwidth($num){
		switch ($num) {
			case 0: $style='0px;'; break;
			case 1: $style='thin;'; break;
			case 2: $style='medium;'; break;
			case 3: $style='thin;'; break;
			case 4: $style='thin;'; break;
			case 5: $style='thick;'; break;
			case 6: $style='3px;'; break;
			case 7: $style='1px;'; break;
			case 8: $style='medium;'; break;
			case 9: $style='thin;'; break;
			case 10: $style='medium;'; break;
			case 11: $style='thin;'; break;
			case 12: $style='medium;'; break;
			case 13: $style='medium;'; break;
		}
		if ($style=='thin;') $style='1px;';
		if ($style=='medium;') $style='2px;';
		if ($style=='thick;') $style='3px;';
		return $style;
	}

	function setconst(){
		$this->recFORMAT[0]='';
		$this->recFORMAT[1]='0';
		$this->recFORMAT[2]='0.00';
		$this->recFORMAT[3]='#,##0';
		$this->recFORMAT[4]='#,##0.00';
		$this->recFORMAT[5]='"$"#,##0_);("$"#,##0)';
		$this->recFORMAT[6]='"$"#,##0_);[Red]("$"#,##0)';
		$this->recFORMAT[7]='"$"#,##0.00_);("$"#,##0.00)';
		$this->recFORMAT[8]='"$"#,##0.00_);[Red]("$"#,##0.00)';
		$this->recFORMAT[9]='0%';
		$this->recFORMAT[10]='0.00%';
		$this->recFORMAT[11]='0.00E+00';
		//$this->recFORMAT[12]='# ?/?';
		//$this->recFORMAT[13]='# ??/??';
		//$this->recFORMAT[14]='M/D/YY';
		$this->recFORMAT[14]='YYYY/M/D';
		$this->recFORMAT[15]='D-MMM-YY';
		$this->recFORMAT[16]='D-MMM';
		$this->recFORMAT[17]='MMM-YY 49 Text @';
		$this->recFORMAT[18]='h:mm AM/PM';
		$this->recFORMAT[19]='h:mm:ss AM/PM';
		$this->recFORMAT[20]='h:mm';
		$this->recFORMAT[21]='h:mm:ss';
		$this->recFORMAT[22]='M/D/YY h:mm';
		$this->recFORMAT[37]='_(#,##0_);(#,##0)';
		$this->recFORMAT[38]='_(#,##0_);[Red](#,##0)';
		$this->recFORMAT[39]='_(#,##0.00_);(#,##0.00)';
		$this->recFORMAT[40]='_(#,##0.00_);[Red](#,##0.00)';
		$this->recFORMAT[41]='_("$"* #,##0_);_("$"* (#,##0);_("$"* "-"_);_(@_)';
		$this->recFORMAT[42]='_(* #,##0_);_(* (#,##0);_(* "-"_);_(@_)';
		$this->recFORMAT[43]='_("$"* #,##0.00_);_("$"* (#,##0.00);_("$"* "-"??_);_(@_)';
		$this->recFORMAT[44]='_(* #,##0.00_);_(* (#,##0.00);_(* "-"??_);_(@_)';
		$this->recFORMAT[45]='mm:ss';
		$this->recFORMAT[46]='[h]:mm:ss';
		$this->recFORMAT[47]='mm:ss.0';
		$this->recFORMAT[48]='##0.0E+0';
		//$this->recFORMAT[49]='@';
		$this->recFORMAT[50]='[$-0411]GE.M.D';
		$this->recFORMAT[51]=mb_convert_encoding('[$-0411]GGGE年M月D日',$this->charset,'EUC-JP');
		$this->recFORMAT[52]=mb_convert_encoding('[$-0411]YYYY年M月',$this->charset,'EUC-JP');
		$this->recFORMAT[53]=mb_convert_encoding('[$-0411]M月D日',$this->charset,'EUC-JP');
		$this->recFORMAT[54]=mb_convert_encoding('[$-0411]GGGE年M月D日',$this->charset,'EUC-JP');
		$this->recFORMAT[55]=mb_convert_encoding('[$-0411]YYYY年M月',$this->charset,'EUC-JP');
		$this->recFORMAT[56]=mb_convert_encoding('[$-0411]M月D日',$this->charset,'EUC-JP');
		$this->recFORMAT[57]='[$-0411]GE.M.D';
		$this->recFORMAT[58]=mb_convert_encoding('[$-0411]GGGE年M月D日',$this->charset,'EUC-JP');
	}

	function fontdeco($opt){
		if ($opt & 0x04) $tmp =' underline'; else $tmp='';
		if ($opt & 0x08) $tmp .=' line-through';
		return $tmp;
	}

	function makecss(){
		$tmp='';
		foreach($this->recXF as $key=>$val){
			$tmp.=".XF". $key . " {\n";
			if (isset($val['Tstyle'])){
				if ($val['Tstyle']!=0){
					$tmp.='border-top-width: '. $this->getwidth($val['Tstyle']) . "\n";
					$tmp.='border-top-style: '. $this->getstyle($val['Tstyle']) . "\n";
				}
			}
			if (isset($val['Tcolor'])){
				if ($val['Tcolor']!=0 && $val['Tcolor']<56){
					$tmp.='border-top-color: #'. $this->palette[$val['Tcolor']] . ";\n";
				} else $tmp.='border-top-color: #'. $this->palette[0] . ";\n";
			}
			if (isset($val['Lstyle'])){
				if ($val['Lstyle']!=0){
					$tmp.='border-left-width: '. $this->getwidth($val['Lstyle']) . "\n";
					$tmp.='border-left-style: '. $this->getstyle($val['Lstyle']) . "\n";
				}
			}
			if (isset($val['Lcolor'])){
				if ($val['Lcolor']!=0 && $val['Lcolor']<56){
					$tmp.='border-left-color: #'. $this->palette[$val['Lcolor']] . ";\n";
				} else $tmp.='border-left-color: #'. $this->palette[0] . ";\n";
			}
			if (isset($val['Bstyle'])){
				if ($val['Bstyle']!=0){
					$tmp.='border-bottom-width: '. $this->getwidth($val['Bstyle']) . "\n";
					$tmp.='border-bottom-style: '. $this->getstyle($val['Bstyle']) . "\n";
				}
			}
			if (isset($val['Bcolor'])){
				if ($val['Bcolor']!=0 && $val['Bcolor']<56){
					$tmp.='border-bottom-color: #'. $this->palette[$val['Bcolor']] . ";\n";
				} else $tmp.='border-bottom-color: #'. $this->palette[0] . ";\n";
			}
			if (isset($val['Rstyle'])){
				if ($val['Rstyle']!=0){
					$tmp.='border-right-width: '. $this->getwidth($val['Rstyle']) . "\n";
					$tmp.='border-right-style: '. $this->getstyle($val['Rstyle']) . "\n";
				}
			}
			if (isset($val['Rcolor'])){
				if ($val['Rcolor']!=0 && $val['Rcolor']<56){
					$tmp.='border-right-color: #'. $this->palette[$val['Rcolor']] . ";\n";
				} else $tmp.='border-right-color: #'. $this->palette[0] . ";\n";
			}
			$ftmp='';
			if ($key==0) $val['fontindex']=0;
			if (isset($val['fontindex'])){
				if ($val['fontindex']>=0){
					if ($this->recFONT[$val['fontindex']]['color'] < 56)
						$tmp.='color: #'. $this->palette[$this->recFONT[$val['fontindex']]['color']] . ";\n";
					if($this->recFONT[$val['fontindex']]['style'] & 0x2) $ftmp.=' italic';
					if($this->recFONT[$val['fontindex']]['style'] & 0x1) $ftmp.=' bold';
					$ftmp.=' '.($this->recFONT[$val['fontindex']]['height']/Const_fontH)."px";
					$ftmp.=' "'.$this->recFONT[$val['fontindex']]['fontname'].'"';
					$tmp.='font: '. $ftmp . ";\n";
					if($this->recFONT[$val['fontindex']]['style'] & 0xc)
						$tmp.='text-decoration:'.$this->fontdeco($this->recFONT[$val['fontindex']]['style']) . ";\n";
				}
			} else $tmp.='font: '.($this->recFONT[0]['height']/Const_fontH)."px".";\n";
			if (isset($val['fillpattern'])){
				if($val['fillpattern']==1){
					$tmp.='background-color: #'. $this->palette[$val['PtnFRcolor']] . ";\n";
				} elseif ($val['fillpattern'] <=18 && $val['fillpattern'] >1){
					$tmp .='background-color: #'. $this->palette[$val['PtnBGcolor']] . ";\n";
					$tmp .= 'background-image:URL("'. $_SERVER['PHP_SELF'] .'?ptn=' . $val['fillpattern'] . '&fc=' . $val['PtnFRcolor'] . "\");\n";
				}
			} elseif(isset($val['PtnBGcolor'])){
				if ($val['PtnBGcolor'] < 65 && $val['PtnBGcolor'] > 0)
					$tmp.='background-color: #'. $this->palette[$val['PtnBGcolor']] . ";\n";
			}
			if (!isset($val['wrap'])) $tmp.='white-space: nowrap;'."\n";
			if (isset($val['valign'])){
				if ($val['halign']==1) $tmp.='text-align: left;'."\n";
				if ($val['halign']==2) $tmp.='text-align: center;'."\n";
				if ($val['halign']==3) $tmp.='text-align: right;'."\n";
				if ($val['halign']==5) $tmp.='text-align: justify;'."\n";
				if ($val['valign']==1) $tmp.='vertical-align: middle;'."\n";
				if ($val['valign']==2) $tmp.='vertical-align: bottom;'."\n";
				if ($val['valign']==0) $tmp.='vertical-align: top;'."\n";
			}
			$tmp.="}\n";
		}
		return $tmp."\n";
	}

	function dispcell($sn,$row,$col){
		$cell=$this->cellblock[$sn][$row][$col];
		switch ($cell['type']) {
			case Type_LABELSST:
				$strnum=$this->__get2(pack("H*",$cell['dat']),0);
				$sstr=$this->eachsst[$strnum]['str'];
				$desc=mb_convert_encoding(pack("H*",$sstr),$this->charset,'UTF-16LE');
				break;
			case Type_RK:
			case Type_RK2:
				$desc=$this->Getrknum($this->__get4(pack("H*",$cell['dat']),0));
				$desc=$this->dispf($desc,$this->recFORMAT[$this->recXF[$cell['xf']]['formindex']]);
				break;
			case Type_NUMBER:
				$strnum=unpack("d",pack("H*",$cell['dat']));
				$desc=$this->dispf($strnum[1],$this->recFORMAT[$this->recXF[$cell['xf']]['formindex']]);
				break;
			case Type_FORMULA:
			case Type_FORMULA2:
				$result=substr(pack("H*",$cell['dat']),0,8);
				if (substr($result,6,2)=="\xFF\xFF"){
					switch (substr($result,0,1)) {
					case "\x00":
						$desc=$this->getstring(substr($cell['string'],4),2);
						break;
					case "\x01":
						$desc=(substr($result,2,1)=="\x01")? "TRUE":"FALSE";
						break;
					case "\x02": $desc='#ERROR!';
						break;
					case "\x03": $desc='';
						break;
					}
				} else {
					$desc0=unpack("d",$result);
					$desc=$this->dispf($desc0[1],$this->recFORMAT[$this->recXF[$cell['xf']]['formindex']]);
				}
				break;
			case Type_BOOLERR:
				$desc='&nbsp;';
				break;
			case Type_BLANK:
				$desc='&nbsp;';
				break;
			default:
				$desc='&nbsp;';
		}
		return $desc;
//		return $desc."[".$this->recFORMAT[$this->recXF[$cell['xf']]['formindex']]."]".$this->recXF[$cell['xf']]['formindex'];
	}

	function Getrknum($rknum){
		if (($rknum & 0x02) != 0) {
			$value = $rknum >> 2;
		} else {
			$sign = ($rknum & 0x80000000) >> 31;
			$exp = ($rknum & 0x7ff00000) >> 20;
			$mantissa = (0x100000 | ($rknum & 0x000ffffc));
			$value = $mantissa / pow( 2 , (20- ($exp - 1023)));
			if ($sign) {$value = -1 * $value;}
		}
		if (($rknum & 0x01) != 0) $value /= 100;
		return $value;
	}

	function getheightwidth(){
		$snum=count($this->sheetbin);
		for ($sno=0;$sno<$snum;$sno++){
			$dat=$this->sheetbin[$sno]['preCB'];
			$pos=0;
			$poslimit=strlen($dat);
			while ($pos < $poslimit){
				$code = $this->__get2($dat,$pos);
				$length = $this->__get2($dat,$pos+2);
				switch ($code) {
					case Type_DEFAULTROWHEIGHT:
						$defheight=$this->__get2($dat,$pos+6)/15;
						$this->rowheight[$sno][-1]=$defheight;
						break;
					case Type_DEFCOLWIDTH:
						$defwidth=$this->__get2($dat,$pos+4);
						$this->colwidth[$sno][-1]=$defwidth * 9;
						break;
					case Type_COLINFO:
						$st=$this->__get2($dat,$pos+4);
						$en=$this->__get2($dat,$pos+6);
						$wd=$this->__get2($dat,$pos+8);
						for ($i=$st;$i<=$en;$i++){
							$this->colwidth[$sno][$i]=$wd/32;
						}
						break;
					case Type_HEADER:
						$this->headfoot[$sno]['header']=$this->getstring(substr($dat, $pos+4, $length),2);
						break;
					case Type_FOOTER:
						$this->headfoot[$sno]['footer']=$this->getstring(substr($dat, $pos+4, $length),2);
						break;
				}
				$pos+=$length+4;
			}
			if(isset($this->rowblock[$sno]))
			foreach($this->rowblock[$sno] as $key=>$val){
				$ph=$this->__get2(pack("H*",$val['rowfoot']),0)/15;
				if ($ph & 0x8000) $this->rowheight[$sno][$key]=$defheight;
				else $this->rowheight[$sno][$key]=$ph;
			}
		}
	}

	function dispf($val,$form){
		if (strlen(trim($form))==0) return $val;
		$form = mb_ereg_replace ("\"","",$form);
		$form = mb_ereg_replace ("\[.+\]","",$form);
		$form = mb_ereg_replace ("\;.*$","",$form);
		if (mb_ereg("[MDYhmsGg]",$form)){
                        $sr= $this->dtform($val,$form);
		} elseif (mb_ereg("[0#]",$form)){
			$sr= $this->numform($val,$form);
		} else $sr= " unknown type [".$form."]  ".$val;
		return $sr;
	}

	function numform($val,$form){
		$yen = (strpos($form,"\\")!== FALSE) ? TRUE: FALSE;
		$percent = (strpos($form,"%")!== FALSE) ? TRUE: FALSE;
		$form =str_replace("%","",$form);
		$exp = (strpos($form,'E+')!== FALSE) ? TRUE: FALSE;
		$numformat = (strpos($form,'#')!== FALSE) ? TRUE: FALSE;
		if (mb_ereg("^.*0\.0*.*$",$form)){
			$num = strlen(mb_ereg_replace("^.*0(\.0*).*$",'\\1',$form));
		} else $num=0;
		$num -=1;
		if ($num <0) $num=0;
		$val = ($percent) ? $val * 100: $val;
		if ($numformat) {
			$result = number_format($val,$num);
		} else {
			if ($exp){
				$result=sprintf('%.'.($num+1)."e", $val);
			} else {
				$result=sprintf('%01.'.$num."f", $val);
			}
		}
		if ($yen) $result = '\\' . $result;
		if ($percent) $result = $result . '%';
		return $result;
	}

	function dtform($val,$form){
		$form = (mb_ereg("dddd",$form)) ? mb_convert_encoding(pack("H*","5900590059005900745e4d0008674400e565"),$this->charset,'UTF-16LE'): $form;
		$form = mb_eregi_replace ('M/D/YY','yyyy/m/d',$form);
		$form = mb_ereg_replace ('[\\\"]','',$form);
		$ut=$this->ms2unixtime($val);
		$ge=$this->towareki($ut[0]);
		if (mb_eregi("AM/PM",$form)) {
			$form=mb_eregi_replace (' ?am/pm','',$form);
			if ($ut['hours'] > 12) {
				$ut['hours'] -= 12;
				$ap=" PM";
			} else $ap=" AM";
			$ampm = true;
		} else  $ampm = false;
		$result=mb_eregi_replace ('yyyy',$ut['year'],$form);
		$result=mb_eregi_replace ('mmmmm','xxxx',$result);
		$result=mb_eregi_replace ('mmmm','xxx',$result);
		$result=mb_eregi_replace ('mmm','xx',$result);
		$result=mb_eregi_replace ('mm','x',$result);
		$result=mb_eregi_replace ('ss',$ut['seconds'],$result);
		$result=mb_eregi_replace ('h',$ut['hours'],$result);
		$result=mb_eregi_replace ('m',$ut['mon'],$result);
		$result=mb_eregi_replace ('d',$ut['mday'],$result);
		$result=mb_eregi_replace ('ggg',$ge['gg'],$result);
		$result=mb_eregi_replace ('g',$ge['ga'],$result);
		$result=mb_eregi_replace ('e',$ge['ge'],$result);
		$result=mb_eregi_replace ('yy',substr("".$ut['year'],-2),$result);
                $result=mb_eregi_replace ('xxxx',substr($ut['month'],0,1),$result);
                $result=mb_eregi_replace ('xxx',$ut['month'],$result);
                $result=mb_eregi_replace ('xx',substr($ut['month'],0,3),$result);
                $result=mb_eregi_replace ('x',$ut['minutes'],$result);
		return ($ampm) ? $result .$ap: $result;
	}

	function towareki($dt) {
	    $ge = array();
	    $tm = getdate($dt);
	    if ($dt < -1812186000){
	        $ge['gg'] = "0e66bb6c";
	        $ge['ga'] = "M";
	        $ge['ge'] = $tm["year"] - 1867;
	    } elseif ($dt < -1357635600) {
	        $ge['gg'] = "2759636b";
	        $ge['ga'] = "T";
	        $ge['ge'] = $tm["year"] - 1911;
	    } elseif ($dt < 600188400) {
	        $ge['gg'] = "2d668c54";
	        $ge['ga'] = "S";
	        $ge['ge'] = $tm["year"] - 1925;
	    } else {
	        $ge['gg'] = "735e1062";
	        $ge['ga'] = "H";
	        $ge['ge'] = $tm["year"] - 1988;
	    }
		$ge['gg'] = mb_convert_encoding(pack("H*",$ge['gg']),$this->charset,'UTF-16LE');
	    return $ge;
	}

	function ms2unixtime($timevalue,$offset1904 = 0){
		if ($timevalue > 1)
			$timevalue -= ($offset1904 ? 24107 : 25569);
		return getdate(round(($timevalue * 24 -9) * 60 * 60));
	}

	function getmergecells(){
		$snum=count($this->sheetbin);
		for ($sno=0;$sno<$snum;$sno++){
			$dat=$this->sheetbin[$sno]['tail'];
			$pos=0;
			$poslimit=strlen($dat);
			while ($pos < $poslimit){
				$code = $this->__get2($dat,$pos);
				$length = $this->__get2($dat,$pos+2);
				switch ($code) {
				case Type_MERGEDCELLS:
					$numrange=$this->__get2($dat,$pos+4);
					for($i=0;$i<$numrange;$i++){
						$rows=$this->__get2($dat,$pos+6+$i*8);
						$rowe=$this->__get2($dat,$pos+8+$i*8);
						$cols=$this->__get2($dat,$pos+10+$i*8);
						$cole=$this->__get2($dat,$pos+12+$i*8);
						for($r=$rows;$r<=$rowe;$r++)
						for($c=$cols;$c<=$cole;$c++){
							$this->celmergeinfo[$sno][$r][$c]['cond']=-1;
						}
						$this->celmergeinfo[$sno][$rows][$cols]['cond']=1;
						$this->celmergeinfo[$sno][$rows][$cols]['rspan']=$rowe-$rows+1;
						$this->celmergeinfo[$sno][$rows][$cols]['cspan']=$cole-$cols+1;
					}
					break;
				case Type_HLINK:
					$hlrow = $this->__get2($dat,$pos+4);
					$hlcol = $this->__get2($dat,$pos+8);
					$hlopt = $this->__get2($dat,$pos+32);
					if(($hlopt & 0x1)==0x1){
						$spos = ($hlopt & 0x14) ? $this->__get2($dat,$pos+36) * 2 + 4: 0;
						$hlnum = $this->__get4($dat,$pos+52+$spos)-1;
						if(52+$spos+$hlnum > $length) break;
						$this->hlink[$sno][$hlrow][$hlcol] = mb_convert_encoding(substr($dat,$pos+56+$spos,$hlnum),$this->charset,'UTF-16LE');
					}
					break;
				}
				$pos+=$length+4;
			}
		}
	}

	function sepheadfoot($str,$sn){
		$str=mb_ereg_replace ('&&', '&amp;' ,$str);
		$str=mb_ereg_replace ('&P', ($sn+1) ,$str);
		$str=mb_ereg_replace ('&N', $this->sheetnum ,$str);
		$str=mb_ereg_replace ('&D', date("Y/m/d") ,$str);
		$str=mb_ereg_replace ('&T', date("H:i:s") ,$str);
		$fname=mb_ereg_replace (".*\/", "" ,$this->infilename);
		$path=mb_ereg_replace ("^(.*\/).+", "\\1" ,$this->infilename);
		$str=mb_ereg_replace ('&A', $this->sheetname[$sn] ,$str);
		$str=mb_ereg_replace ('&F', $fname ,$str);
		$str=mb_ereg_replace ('&Z', $path ,$str);
		$str=mb_ereg_replace ('&G', '' ,$str);
		if (ereg('.*&R(.*)$',$str)){
			$s['right'] = mb_ereg_replace ('.*&R(.*)$', "\\1" ,$str);
			$str= mb_ereg_replace ("&R.*$", "" ,$str);
		}
		if (ereg(".*&C(.*)$",$str)){
			$s['center'] = mb_ereg_replace ('.*&C(.*)$', "\\1" ,$str);
			$str= mb_ereg_replace ('&C.*$', '' ,$str);
		}
		$s['left'] = mb_ereg_replace ('&L(.*)$', "\\1" ,$str);
		return $s;
	}
}

function makeptn($nn,$fc){
  $frcolor=array(
	"000000","FFFFFF","FF0000","00FF00","0000FF","FFFF00","FF00FF","00FFFF",
	"000000","FFFFFF","FF0000","00FF00","0000FF","FFFF00","FF00FF","00FFFF",
	"800000","008000","000080","808000","800080","008080","C0C0C0","808080",
	"9999FF","993366","FFFFCC","CCFFFF","660066","FF8080","0066CC","CCCCFF",
	"000080","FF00FF","FFFF00","00FFFF","800080","800000","008080","0000FF",
	"00CCFF","CCFFFF","CCFFCC","FFFF99","99CCFF","FF99CC","CC99FF","FFCC99",
	"3366FF","33CCCC","99CC00","FFCC00","FF9900","FF6600","666699","969696",
	"003366","339966","003300","333300","993300","993366","333399","333333",
	);
	if (($nn<2) || ($nn>18)) exit;
	if ($fc<0 || $fc >64) $fc=0;
	$fillptn0="47494638396104000400f00000";
	$frcolor= $frcolor[$fc];
	$fillptn1="ffffff21f90401000001002c000000000400040000080";
	$fillptn3[2]="c000104103870a04082070302003b";
	$fillptn3[3]="d000300183850a0408200040604003b";
	$fillptn3[4]="d000104183850a0408201040604003b";
	$fillptn3[5]="c0001081c283080c183060302003b";
	$fillptn3[6]="e0001000810402041830507060808003";
	$fillptn3[7]="e00010008405020c1000207220c08003";
	$fillptn3[8]="e000304000060604182020b0e0c08003";
	$fillptn3[9]="e000100081040204182060b020808003";
	$fillptn3[10]="d000100081040a04082060d0604003b";
	$fillptn3[11]="c0001080410a0a0c183050302003b";
	$fillptn3[12]="d000104182890e0c00005030404003b";
	$fillptn3[13]="d00010418184020418303010404003b";
	$fillptn3[14]="c0003080430b0600082020302003b";
	$fillptn3[15]="e0001081418a0208082010e160c08003b";
	$fillptn3[16]="d00010410383040418206010404003b";
	$fillptn3[17]="b000104184890a0c0820101003b";
	$fillptn3[18]="90001041848b0a0c180003b";
	$patern=$fillptn0.$frcolor.$fillptn1.$fillptn3[$nn];
	header("Content-type: image/gif");
	print pack("H*",$patern);
	exit;
}
?>
