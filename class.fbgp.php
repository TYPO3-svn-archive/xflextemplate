<?php

/***************************************************************
*  Copyright notice
*
*  (c) 2006  Federico Bernardin <federico@bernardin.it>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/


/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   58: class fbgp
 *   69:     function getDate($timestamp,$flag)
 *   93:     function getTStemp($date,$flag)
 *  118:     function getSignificativeNumber($number,$value)
 *  145:     function getTimeFromMinute($minutes,$day=0)
 *  166:     function getMinuteFromTime($time,$separator)
 *  179:     function getItalianDate($date,$dateArray,$separator)
 *  192:     function getMysqlDate($date,$dateArray,$separator)
 *  205:     function getMonth($month,$flag)
 *  220:     function formattingLine($text,$len)
 *
 * TOTAL FUNCTIONS: 9
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */


 /**
  * Hook 'tx_xflextemplate_tceforms' for the 't3lib_tceforms.getMainFieldsClass'
  * php class.
  *
  * @package typo3
  * @subpackage generalpurpose
  * @author	Federico Bernardin <federico@bernardin.it>
  * @version 0.1.0
  */
class fbgp{
	/**
 * This function is used to translate date from timestamp (unix) fromat in a human readable format.
 * the $flag parameter specify the output format, available format is:
 * it: dd/mm/yyyy
 * en: yyyy-mm-dd
 *
 * @param	[int]		$timestamp: timestamp for date
 * @param	[type]		$flag: ...
 * @return	[string]		$flag: output standard value
 */
	function getDate($timestamp,$flag){
		if ($timestamp){
			switch ($flag){
				case 'it':
					return date('d',$timestamp).'/'.date('m',$timestamp).'/'.date('Y',$timestamp);
				break;
				case 'en':
					return date('Y',$timestamp).'-'.date('m',$timestamp).'-'.date('d',$timestamp);
				break;
			}
		}
		return '';
	}

	/**
	 * This function is used to translate date from human readable format in timestamp (unix).
	 * the $flag parameter specify the output format, available format is:
	 * it: dd/mm/yyyy
	 * en: yyyy-mm-dd
	 *
	 * @param	[int]		$date: date to transform
	 * @param	[type]		$flag: ...
	 * @return	[string]		$flag: output standard value
	 */
	function getTStemp($date,$flag){
		if(strstr($date,'/')){
			$tmpArray=explode('/',$date);
			if (count($tmpArray)==3){
				switch ($flag){
					case 'it':
						return mktime(0,0,0,$tmpArray[1],$tmpArray[0],$tmpArray[2]);
					break;
					case 'en':
						return mktime(0,0,0,$tmpArray[1],$tmpArray[2],$tmpArray[0]);
					break;
				}
			}
		}
		return mktime(0,0,0,0,0,0);
	}

	/**
	 * This function return a float value with specific number of digit after dot
	 * !IMPORTANT the decimal separator have to be the dot
	 *
	 * @param	[int]		$number: number of decimal digit
	 * @param	[float]		$value: value to evaluate
	 * @return	[float]		number with comma decimal separator
	 */
	function getSignificativeNumber($number,$value){
		for($i=0;$i<$number;$i++)
			$tmpDecimal.='0';
		if($value){
			$tmpArray=explode('.',$value);
			if($tmpArray[1]){
				$tmpArray[1]=substr($tmpArray[1],0,$number+1);
				$tmpArray[1]=(strlen($tmpArray[1])>$number)?round($tmpArray[1]/10):$tmpArray[1];
			}
			else{
				$tmpArray[1]=$tmpDecimal;
			}
			for($i=0;$i<$number-strlen($tmpArray[1]);$i++)
				$tmpArray[1].='0';
			return $tmpArray[1]?implode(',',$tmpArray):$tmpArray[0];
		}
		else
			return '0,'.$tmpDecimal;
	}

	/**
	 * transform a number in minutes to a readable time format
	 *
	 * @param	[int]		$minutes: integer represents total minutes to tranform
	 * @param	[int]		$day: (optional) if 1 the transformation use day too
	 * @return	[string]		...
	 */
	function getTimeFromMinute($minutes,$day=0){
		$hour=floor($minutes/60);
		$minute=$minutes%60;
		if ($day){
			$days=floor($hour/24);
			$hour=$hour%24;
		}
		$hour=($hour)?$hour.'h':'';
		$minute=($minute)?$minute.'m':'';
		$minute=($hour)?' '.$minute:$minute;
		$hour=($days)?$days.'d '.$hour:$hour;
		return $hour.$minute;
	}

	/**
	 * Trasform a time value (express with a separator from each single value) in total number of minutes
	 *
	 * @param	[string]		$time: in the format hh:mm (: could be another symbol)
	 * @param	[string]		$separator: the symbol that divedes hours from minutes
	 * @return	[int]		amount of minutes
	 */
	function getMinuteFromTime($time,$separator){
		$tmpArray=explode($separator,$time);
		return $tmpArray[0]*60+$tmpArray[1];
	}

	/**
	 * Translate a date in the italian format
	 *
	 * @param	[string]		$date: date value with a specific separator between each value
	 * @param	[array]		$dateArray: an associative array (example(array('d'=>0,'m'=>1,'y'=>2)) where key is d,m,y and value is the position in the date value
	 * @param	[string]		$separator: the symbol that divedes hours from minutes
	 * @return	[string]		Italian date
	 */
	function getItalianDate($date,$dateArray,$separator){
		$tmpArray=explode($separator,$date);
		return $tmpArray[$dateArray['d']].'/'.$tmpArray[$dateArray['m']].'/'.$tmpArray[$dateArray['y']];
	}

	/**
	 * Translate standard date in mysql standard date
	 *
	 * @param	[string]		$date: date value with a specific separator between each value
	 * @param	[array]		$dateArray: an associative array (example(array('d'=>0,'m'=>1,'y'=>2)) where key is d,m,y and value is the position in the date value
	 * @param	[string]		$separator: the symbol that divedes hours from minutes
	 * @return	[string]		Mysql date
	 */
	function getMysqlDate($date,$dateArray,$separator){
		$tmpArray=explode($separator,$date);
		return $tmpArray[$dateArray['Y']].'-'.$tmpArray[$dateArray['m']].'-'.$tmpArray[$dateArray['d']];
	}


	/**
	 * Return name of Month from number
	 *
	 * @param	[int]		$month: number of month (start from 1 to 12)
	 * @param	[string]		$flag: the code of nation
	 * @return	[string]		name of month
	 */
	function getMonth($month,$flag){
		$months=array(
					'it'=>array('','Gennaio','Febbraio','Marzo','Aprile','Maggio','Giugno','Luglio','Agosto','Settembre','Ottobre','Novembre','Dicembre'),
					'en'=>array('','Jannuary','February','March','April','May','June','July','August','September','October','November','December')
					);
		return $months[$flag][$month];
	}

	/**
	 * This function insert carriage return in each line to mantain specific number of characters
	 *
	 * @param	[string]		$text: input text
	 * @param	[int]		$len: number of character per line
	 * @return	[string]		formatted text
	 */
	function formattingLine($text,$len){
		//print($text.'<br />');
		$text=str_replace('\r\n','\n',$text);
		$i=0;
		while(strlen($text)>$len){
			$evaluateString=substr($text,0,$len);
			//print($i.' to evaluate:'.$evaluateString."<BR>");
			$crPosition=strpos($evaluateString,"\n");
			if($crPosition===false){
				$spacePosition=strrpos($evaluateString,chr(32));
				if($spacePosition===false){
					$evaluateString.="\n";
					$i=$len+1;
					//print($i.'-nospace:'.$evaluateString."<BR>");
				}
				else {
					//$evaluateString=substr_replace($evaluateString,"\n",$spacePosition,1);
					$evaluateString=substr($evaluateString,0,$spacePosition)."\n";
					$i=$spacePosition+1;
					//print($spacePosition.'-space:'.$evaluateString."<BR>");
				}
			}
			else{
				$i=$crPosition+1;
				$evaluateString=substr($evaluateString,0,$crPosition)."\n";
				//print($crPosition.'-cr:'.$evaluateString."<BR>");
			}
			$text=substr($text,$i);
			$outText.=$evaluateString;
		}
		$outText.=$text;
		return $outText;
	}


}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/xflextemplate/class.fbgp.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/xflextemplate/class.fbgp.php']);
}

?>
