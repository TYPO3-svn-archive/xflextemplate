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
 *   44: class tx_xft_div
 *   55:     function getArrayFromXML($xml)
 *   80:     function getArrayFromXMLData($xml)
 *
 * TOTAL FUNCTIONS: 2
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */
class xmlTransformation {


	/**
	 * This function creates the array from an xml passed as parameter to an array in output
	 * The use of this function is restricted to xml in xft format (like defined in table tx_xflextemplate_template)
	 *
	 * @param	string		$xml: xml from template
	 * @return	array		an array with data from xml
	 * @ver 1.1.0
	 */
	function getArrayFromXML($xml){
		$XMLArray=array(); //set value, this value will be send to array merge overrule, and if it'isn't an array an error will raise
		$tmpArray=t3lib_div::xml2tree($xml); // tranform xml into array typo3 format
		if(is_array($tmpArray)){
			$tmpArray=$tmpArray['template'][0]['ch']['el']; // define start level inside array
			$index=0; // reset index of final array result
			if(is_array($tmpArray)){
				foreach($tmpArray as $elem){ // cycle from element inside array
					foreach($elem['ch'] as $key=>$item){
						$XMLArray[$index][$key]=$item[0]['values'][0]; // save value into XMLArray
					}
					$index++; // increment index of final array
				}
			}
		}
		return $XMLArray;
	}

	/**
	 * This function creates the array from an xml passed as parameter to an array in output
	 * The use of this function is restricted to xml in xft format (like defined in table tx_xflextemplate_template)
	 * The different from getArrayFromXML is the format of xml, this is the xml of Typo3 FlexData
	 *
	 * @param	string		$xml: xml from template
	 * @return	array		an array with data from xml
	 * @ver 1.0.0
	 */
	function getArrayFromXMLData($xml){
		$XMLArray=array(); //set value, this value will be send to array merge overrule, and if it'isn't an array an error will raise
		$tmpArray=t3lib_div::xml2array($xml); // tranform xml into array typo3 format
		if(is_array($tmpArray))	{
			$tmpArray=$tmpArray['data']['sDEF']['lDEF']; // define start level inside array
			$index=0; // reset index of final array result
			if(is_array($tmpArray)){
				foreach($tmpArray as $key=>$item){ // cycle from element inside array
					$XMLArray[$key]=$item['vDEF']; // save value into XMLArray
				}
			}
		}
		return $XMLArray;
	}
	
	function addFiledsFromArray($array,$fieldsArray){
		if(is_array($array))
			if(is_array($array['data']['sDEF']['lDEF']))
				foreach($array['data']['sDEF']['lDEF'] as $key=>$value)
					$fieldsArray[$key]=$value['vDEF'];
		return $fieldsArray;
	}
	
/**
	 * This function creates the array of Flex Form in TYPO3 format from the array passed as value
	 *
	 * @param	array		$dataArray: array to transform
	 * @return	array		an array with data from input in TYPO3 flex form format
	 * @ver 1.0.4
	 */
	function getXMLDataFromArray($dataArray){
		if(is_array($dataArray)){
			foreach($dataArray as $key=>$item){
				$resultArray[$key]['vDEF']=$item; //create single item in form: [$key]['vDEF']=item
			}
		}
		$XMLDataArray['data']['sDEF']['lDEF']=$resultArray; // link array created above with ['data']['sDEF']['lDEF']
		return $XMLDataArray;
	}

}





if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/xflextemplate/library/class.xmlTransformation.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/xflextemplate/library/class.xmlTransformation.php']);
}

?>