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
 *   52: class tcaTransformation
 *   61:     function getFormTCA(&$TCA,$xmlArray)
 *  121:     function getFlexFieldTCA(&$TCA,$xmlArray)
 *  176:     function getTCApalettes(&$TCA,$palettes)
 *  200:     function setSelectItems($field)
 *
 * TOTAL FUNCTIONS: 4
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */

require_once (t3lib_extMgm::extPath('xflextemplate')."library/class.xmlTransformation.php");

/**
 * Library for TCA management.
 *
 * @package typo3
 * @subpackage xflextemplate
 * @author	Federico Bernardin <federico@bernardin.it>
 * @version 1.1.0
 */
class tcaTransformation	{
/**
 * This function creates a fake TCA['tt_content'] for adding the field from flexible template.
 * It returns void but changes TCA array, so it contains the new columns as they were true.
 *
 * @param	array		TCA tree of tt_content, so you have to pass as TCA['tt_content']
 * @param	array		xml field from template
 * @return	void		none
 */
	function getFormTCA(&$TCA,$xmlArray) {
		$fieldArray=xmlTransformation::getArrayFromXML($xmlArray); // create array of field from xml template
		if(is_array($fieldArray)){ // if array is correct
			foreach($fieldArray as $object){
				$palettes=$name='';
				foreach($object as $key=>$item){ //create TCA array from fields
					switch ($key){
						case 'name':
							$name=$item; //name of column
						break;
						case 'defaultExtras':
							$xflexTceForms[$name][$key]=$item; //valid only for rte
						break;
						case 'items':
							$xflexTceForms[$name]['config'][$key]=$this->setSelectItems($item); // items for select and radio buttons
						break;
						case 'palettes': //list of palettes
							$palettes=$item;
						break;
						default:
							$xflexTceForms[$name]['config'][$key]=$item; // standard config fields
						break;
					}
				}
				//defines personalization in label of field, it can be fetch from dynamicfieldtranslation
				if (is_array($this->ts->setup['language.'][$name.'.']['beLabel.']))
					$xflexTceForms[$name]['label']=($this->ts->setup['language.'][$name.'.']['beLabel.'][$GLOBALS['BE_USER']->uc['lang']])?$this->ts->setup['language.'][$name.'.']['beLabel.'][$GLOBALS['BE_USER']->uc['lang']]:$this->ts->setup['language.'][$name.'.']['beLabel.']['default'];
				//exclude field is always set to zero
				$xflexTceForms[$name]['exclude']='0';
				$globalConf=unserialize($GLOBALS['TYPO3_CONF_VARS']["EXT"]["extConf"]['xflextemplate']);
				//this fields is for RTE and other implementation of particular field (documentation in TYPO3 core api)
				if(!$xflexTceForms[$name]['defaultExtras']) //defaultExtras is defined as follow
					$xflexTceForms[$name]['defaultExtras']=$globalConf['defaultExtra'];;
				if($xflexTceForms[$name]['config']['internal_type']=='file'){
					$xflexTceForms[$name]['config']['uploadfolder']=$globalConf['uploadFolder'];;
				}
				//create types fields for palettes
				if (!$palettes) {
					$paletteValue=($this->translatePalettesArray[$name])?$this->translatePalettesArray[$name]:'';
					$showfields[]=$name.';;'.$paletteValue.';;';
				}
			}
		}
		$showfields=(is_array($showfields))?$showfields:array();
		//Update TCA!! It's very important pass TCA for reference!!!
		$TCA['columns']=(is_array($xflexTceForms))?array_merge_recursive($TCA['columns'],$xflexTceForms):$TCA['columns'];//if template is hidden not merge array but use original TCA
		$TCA['types'][$this->_EXTKEY.'_pi1']['showitem']=$TCA['types'][$this->_EXTKEY.'_pi1']['showitem'].','.implode(',',$showfields);
	}

/**
 * Function to create the flexform for definition of flexform field in TCA column.
 * Fields are fetched from xml data from templates table.
 *
 * @param	array		TCA tree of tt_content, so you have to pass as TCA['tt_content']
 * @param	array		xml field from template
 * @return	void		none
 */
	 function getFlexFieldTCA(&$TCA,$xmlArray) {
	 	$fieldArray=xmlTransformation::getArrayFromXML($xmlArray); // create array of field from xml template
		if(is_array($fieldArray)){ // if array is correct
			foreach($fieldArray as $object){ // each object is a content subitem
				$temp=array();
				$name=$tempConfig='';
				foreach($object as $key=>$item){ //create TCA array from fields
					switch ($key){
						case 'name':
							$name=$item; //name of column
						break;
						case 'defaultExtras':
							$defaultExtras='<'.$key.'>'.$item.'</'.$key.'>'; //valid only for rte
						break;
						case 'items':
							$xflexTceForms[$name]['config'][$key]=$this->setSelectItems($item); // items for select and radio buttons
						break;
						case 'palettes': //list of palettes
							$palettes=$item;
						break;
						default:
							$tempConfig.='<'.$key.'>'.$item.'</'.$key.'>'."\n";
						break;
					}
					//flextstring contains the xml block for each TCA column
					$flexString.='<'.$name.'><TCEforms>'.$label.$defaultExtras.'<config>'.$tempConfig.'</config></TCEforms></'.$name.'>'."\n";
				}
			}
		}
		$flexString='<T3DataStructure><meta><langDisable>1</langDisable></meta><ROOT><type>array</type><el>'.$flexString.'</el></ROOT></T3DataStructure>';
		$xflexTceForms[$this->_EXTKEY]=array(
			'exclude' => 1,
			'config'=>array(
				"type" => "flex",
				"ds" => array (
					"default" => $flexString
				)
			)
		);
		//update TCA
		$TCA['columns'][$this->_EXTKEY]=$xflexTceForms[$this->_EXTKEY];
	}





	/**
	 * This function changes the palettes part of TCA.
	 * The $palettes parameter is a serilized array containing palette data
	 *
	 * @param	array		TCA tree of tt_content, so you have to pass as TCA['tt_content']
	 * @param	array		palettes serialized array
	 * @return	void		none
	 */
	function getTCApalettes(&$TCA,$palettes) {
		//unserialize the value palettes
		$palettesArray=unserialize($palettes);
		// Order array by means of key
		ksort($TCA['palettes']);
		// fetch last key (greatest)
		end($TCA['palettes']);
	 	$last=key($TCA['palettes'])+1;
	 	//in this way $last is the last index +1 (gratest index) in the array and function uses a grater value
		if($palettesArray){
			foreach($palettesArray as $key=>$value){
				$TCA['palettes'][$last]=array('showitem'=>$value);
				$this->translatePalettesArray[$key]=$last;
				$last++;
			}
		}
	}

	/**
	 * This function creates the array from items will be passed to TCA constructor for creating select or radio items.
	 *
	 * @param	string		in this parameter there are all items for select or radio separated from carriage return "/n" and each item is comma separated
	 * @return	void		an array with row and column for each item
	 */
	function setSelectItems($field){
		$rowArray=explode("\n",$field);
		foreach($rowArray as $value){
			$tmpArray[]=explode(',',$value);
		}
		return $tmpArray;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/xflextemplate/library/class.tcaTransformation.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/xflextemplate/library/class.tcaTransformation.php']);
}
?>