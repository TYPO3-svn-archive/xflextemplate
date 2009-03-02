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
 *   57: class tx_xflextemplate_tceforms
 *   76:     function getMainFields_preProcess($table, &$row, $pObj)
 *  113:     function translatepages($list)
 *  138:     function getTCA(&$TCA,$xmlArray)
 *  203:     function getTCApalettes(&$TCA,$palettes)
 *  228:     function setSelectItems($field)
 *
 * TOTAL FUNCTIONS: 5
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */

 /**
  * Hook 'tx_xflextemplate_tceforms' for the 't3lib_tceforms.getMainFieldsClass'
  * php class.
  *
  * @package typo3
  * @subpackage xflextemplate
  * @author	Federico Bernardin <federico@bernardin.it>
  * @version 1.0.0
  */


require_once(PATH_t3lib.'class.t3lib_tsparser.php');

class tx_xflextemplate_tceforms	{
	/*
	* Name of plugin
	*
	* @var  string $_EXTKEY
	*/
	var $_EXTKEY='xflextemplate';

	/**
	 * This function translate the xml data from xft template into array for TCE Forms creation
	 *
	 * @param	string		E' il nome della tabella TCA di cui vengono effettuate le operazioni
	 * @param	array		E' l'array  contenente i campi da inserire nelle form (e con cui creare le form)
	 * @param	object		E' il puntatore alla classe con cui viene effetuato lo hook (tceforms)
	 * @return	void		none
	 * @ver 1.0.0
	 */
	function getMainFields_preProcess($table, &$row, $pObj)	{
		if($row['xtemplate'] && $row['xtemplate']!='notemplate' && $table = 'tt_content'){ //if xtemplate is not set none to do
		//fetch from db the xml which will create the forms
			$res=$GLOBALS['TYPO3_DB']->exec_SELECTquery('typoscript,xml,palettes','tx_xflextemplate_template','title="'.$row['xtemplate'].'" AND deleted=0 AND hidden=0');
			$dbrow=$GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
			$this->ts=t3lib_div::makeInstance('t3lib_TSparser');
			$this->ts->parse($dbrow['typoscript']);
			$xml=str_replace("''","'",$dbrow['xml']);
			//update the TCA with newer one
			$this->getTCApalettes($GLOBALS['TCA']['tt_content'],$dbrow['palettes']);
			$this->getTCA($GLOBALS['TCA']['tt_content'],$xml);
			$flexFields=tx_xft_div::getArrayFromXMLData($row[$this->_EXTKEY]);
			if(is_array($flexFields)){ //if flexdata is an array data will be put into flexdata array
				//put any field from flexfields
				foreach($flexFields as $key=>$obj){
					$row[$key]=$obj;
				}
			}
		}
	}


	/**
	 * La funzione permette la generazione del TCA a partire dal xml passato
	 * come secondo parametro, vengono cioe' aggiunti i campi relativi ai valori
	 * presenti nel xml
	 *
	 * @param	array		E' l'albero TCA del tt_content, quindi deve essere passato come TCA['tt_content']
	 * @param	array		E' l'array  modificato dalla funzione t3lib_div::xml2tree del xml presente nella tabella tx_xflextemplate_template
	 * @return	void		none
	 * @ver 1.0.0
	 */
	function getTCA(&$TCA,$xmlArray) {
		global $BE_USER;
		$fieldArray=tx_xft_div::getArrayFromXML($xmlArray); // create array of field from xml template
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
					$xflexTceForms[$name]['label']=($this->ts->setup['language.'][$name.'.']['beLabel.'][$BE_USER->uc['lang']])?$this->ts->setup['language.'][$name.'.']['beLabel.'][$BE_USER->uc['lang']]:$this->ts->setup['language.'][$name.'.']['beLabel.']['default'];
				else
					$xflexTceForms[$name]['label']='LLL:EXT:xflextemplate/dynamicfieldtranslation.xml:'.$name;
				$xflexTceForms[$name]['label']=(strlen($xflexTceForms[$name]['label'])>0)?$xflexTceForms[$name]['label']:'LLL:EXT:xflextemplate/dynamicfieldtranslation.xml:'.$name;
				//exclude field is always set to zero
				$xflexTceForms[$name]['exclude']='0';
				//this fields is for RTE and other implementation of particular field (documentation in TYPO3 core api)
				if(!$xflexTceForms[$name]['defaultExtras']) //defaultExtras is defined as follow
					$xflexTceForms[$name]['defaultExtras']='richtext[]:rte_transform[flag=rte_enabled|mode=ts]';
				if($xflexTceForms[$name]['config']['internal_type']=='file'){
					$xflexTceForms[$name]['config']['uploadfolder']='uploads/pics/';
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
	 * Questa funzione permette di generare dinamicamente il contenuto
	 * dell'array palettes, in modo coerente con l'xml fornito. Le palettes
	 * servono per definire i campi secondari associati alle voci principali.
	 *
	 * @param	array		E' l'albero TCA del tt_content, quindi deve essere passato come TCA['tt_content']
	 * @param	array		E' l'array  modificato dalla funzione t3lib_div::xml2tree del xml presente nella tabella tx_xflextemplate_template
	 * @return	void		none
	 * @ver 1.0.0
	 */
	function getTCApalettes(&$TCA,$palettes) {
		//unserialize the value palettes
		$palettesArray=unserialize($palettes);
		// Order array by means of key
		ksort($TCA['palettes']);
		// fetch last key (gratest)
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
	 * This function create the array from items will be passed to TCA constructor for creating select or radio items.
	 *
	 * @param	[type]		$field: in this parameter there are all items for select or radio separated from carriage return "/n" and each item is comma separated
	 * @return	[type]		an array with row and column for each item
	 * @ver 1.0.0
	 */
	function setSelectItems($field){
		$rowArray=explode("\n",$field);
		foreach($rowArray as $value){
			$tmpArray[]=explode(',',$value);
		}
		return $tmpArray;
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/xflextemplate/class.tx_xflextemplate_tceforms.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/xflextemplate/class.tx_xflextemplate_tceforms.php']);
}
?>
