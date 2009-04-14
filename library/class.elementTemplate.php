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
 */

require_once($BACK_PATH . 'sysext/cms/tslib/class.tslib_content.php');
require_once('../configuration/elementConfiguration.php');

class elementTemplate {
	
	var $cObj;
	var $template;
	
	function init($fileName=''){
		$this->cObj = t3lib_div::makeInstance('tslib_cObj');
		//debug($this->template,'ddddd');
		$this->template = ($fileName) ? file_get_contents($fileName) : '';
		//debug($this->template,'ffffff');
	}
	
	function getSubElementObject($elementName,$fileName=''){
		if(!$this->template)
			$this->init($fileName);
		//echo($this->template);
		return ($this->template && $elementName)?$this->cObj->getSubpart($this->template,strtoupper($elementName)):'errore';		
	}
	
	function setSubElementType($elementName = 'inputType',$elementArray = array()){		
		global $LANG;
		$content = $this->getSubElementObject(strtoupper($elementName) . '_SUBELEMENT');
		$markerArray = array();
		foreach($GLOBALS['configuration']['subElement'][$elementName] as $item){
			$markerArray[$item . '_label'] = $LANG->sL('LLL:EXT:xflextemplate/language/locallang_template.xml:' . $item . 'label');
			if(t3lib_div::inList('wrap',$item)){
				$markerArray[$item . 'checked'] = ($elementArray[$item]) ? 'checked' : '';
			}
			if(t3lib_div::inList('hardtype',$item)){
				if ($elementArray[$item] == 'file'){
					$markerArray['fileselected'] = 'selected';
					$markerArray['databaseselected'] = '';
				}
				elseif($elementArray[$item] == 'database'){
					$markerArray['fileselected'] = '';
					$markerArray['databaseselected'] = 'selected';
				}
				else{
					$markerArray['fileselected'] = '';
					$markerArray['databaseselected'] = '';
				} 
				$markerArray[$item . 'selected'] = ($elementArray[$item] == 'file') ? 'checked' : '';
			}
		}
		$markerValueArray = $this->getSubElementValueArray($elementName, $elementArray);
		$markerArray = t3lib_div::array_merge_recursive_overrule($elementArray,$markerArray);
		$markerArray = t3lib_div::array_merge_recursive_overrule($markerValueArray,$markerArray);
		$content = $this->cObj->substituteMarkerArray($content,$markerArray,'###|###',1);
		$content = ereg_replace('###[a-zA-Z0-9]*###','',$content);
		return $content;
	}
	
	function setSubElement($elementName,$elementArray = array()){
		global $LANG, $BACK_PATH;
		//debug($elementArray,$elementArray['title']);
		$content = $this->getSubElementObject('MAIN_ELEMENT');
		if (count($GLOBALS['configuration']['subElement']['main'])){
			foreach($GLOBALS['configuration']['subElement']['main'] as $item){
				$markerArray[$item] = $LANG->sL('LLL:EXT:xflextemplate/language/locallang_template.xml:' . $item);
			}
		}
		$optionType = array();
		if (count($GLOBALS['configuration']['subElement']['type'])){
			foreach($GLOBALS['configuration']['subElement']['type'] as $item){
				$selected = ($elementArray['type'] == $item) ? ' selected ' : '';
				$optionType[] = '<option value="' . $item . '"' . $selected . '>' . $LANG->sL('LLL:EXT:xflextemplate/language/locallang_template.xml:' . $item) . '</option>';
			}
		}
		$markerArray['TYPESELECT'] = implode(chr(10),$optionType);
		$optionType = array();
		if (count($GLOBALS['configuration']['subElement']['xtype'])){
			foreach($GLOBALS['configuration']['subElement']['xtype'] as $item){
				$selected = ($elementArray['xtype'] == $item) ? ' selected ' : '';
				$optionType[] = '<option value="' . $item . '"' . $selected . '>' . $LANG->sL('LLL:EXT:xflextemplate/language/locallang_template.xml:' . $item) . '</option>';
			}
		}
		$markerArray['XTYPESELECT'] = implode(chr(10),$optionType);
		$optionType = array();
		$optionType[] = '<option value="none">' . $LANG->sL('LLL:EXT:xflextemplate/language/locallang_template.xml:palettenone') . '</option>';
		if (count($elementArray['paletteArray']) && is_array($elementArray['paletteArray'])){
			foreach($elementArray['paletteArray'] as $item){				
				$paletteSubItem = explode('_',$item);
				$selected = ($elementArray['palette'] == 'element_' . $paletteSubItem[1]) ? ' selected ' : '';
				if($paletteSubItem[0] != $elementArray['title']){
					$optionType[] = '<option value="element_' . $paletteSubItem[1] . '" ' . $selected . '>' . $paletteSubItem[0] . '</option>';
				}
			}
		}
		
		$markerArray['PALETTESELECT'] = implode(chr(10),$optionType);
		$markerArray['DELETEICON'] = '<img ' . t3lib_iconWorks::skinImg($BACK_PATH,'gfx/garbage.gif','') . '/>';
		$markerArray['TITLEVALUE'] = $elementArray['title'];
		$markerArray['OPENVALUE'] = $elementArray['open'];
		$markerArray['SUBELEMENT'] = $this->setSubElementType($elementName,$elementArray);
		$markerArray = t3lib_div::array_merge_recursive_overrule($elementArray,$markerArray);
		//debug($markerArray);
		$content = $this->cObj->substituteMarkerArray($content,$markerArray,'###|###',1);
		$content = ereg_replace('###[a-zA-Z0-9]*###','',$content);
		return $content;
	}
	
	function getSubElementValueArray($elementName,$elementArray = array()){
		$markerArray = array();
		if (count($elementArray)){
			foreach($GLOBALS['configuration']['subElement'][$elementName] as $item){
				$markerArray[$item . 'value'] = $elementArray[substr($elementName,0,strlen($elementName)-4) . '_' . $item];
			}
		}
		return $markerArray;
	}
	
}
?>