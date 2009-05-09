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
require_once('../library/class.xftObject.php');

/**
 * List of template class.
 * This class create a table based list of template form DB
 * 
 * @author Federico Bernardin <federico@bernardin.it>
 * @version 2.0
 * @package TYPO3
 * @subpackage xfletemplate
 */
class listTemplate {
	
	/**
	 * @var object xftObject
	 */
	var $xft;
	
	/**
	 * @var string template content string
	 */
	var $template;
	
	/**
	 * @var object content object (tslib_content.php)
	 */
	var $cObj;
	
	/**
	 * @var object language TYPO3 object
	 */
	var $language;
	
	/**
	 * @var array unserialize array containing EXTCONF
	 */
	var $globalConf;
	
	/**
	 * This function bind element from parameters to element of class
	 * 
	 * @param object language TYPO3 object
	 * @param string filename string
	 * @param array unserialize array containing EXTCONF 
	 * 
	 * @return void
	 */
	function init($langObj, $fileName='', $globalConf){
		$this->xft = t3lib_div::makeInstance('xftObject');
		$this->cObj = t3lib_div::makeInstance('tslib_cObj');
		$this->template = ($fileName) ? file_get_contents($fileName) : '';
		$this->language = $langObj;
		$this->globalConf = $globalConf;
	}
	// TODO
	
	/**
	 * This function creates the HTML code for template list
	 * 
	 * @return string HTML code 
	 */
	function getTemplateList(){
		global $BACK_PATH;
		 $templateList = $this->xft->getTemplateList();
		 //if there is some database rows
		 if(count($templateList)){	
		 	//retrieve template subparts	 	
			$tableContent = $this->cObj->getSubpart($this->template,'###TEMPLATELIST###');
			$rowTemplate = $this->cObj->getSubpart($tableContent,'###TEMPLATELISTCOLUMN###');
			$columnContent = '';
			//builds column marker array
			foreach($templateList as $item){
				$markerColumnArray = array();
				$markerColumnArray['titlecolumn'] = $item['title'];
				$markerColumnArray['crdatecolumn'] = date($this->globalConf['date'],$item['crdate']);
				$markerColumnArray['tstampcolumn'] = date($this->globalConf['date'],$item['tstamp']);
				$markerColumnArray['descriptioncolumn'] = $item['description'];
				$markerColumnArray['uidRowID'] = $item['uid'];
				$hiddenIcon = ($item['hidden'])?'button_unhide':'button_hide';
				$hiddenColumnTips = ($item['hidden'])?$this->language->getLL('showColumnTips'):$this->language->getLL('hiddenColumnTips');
				$markerColumnArray['iconsColumn'] = '<img id="edit-' . $item['uid'] . '" class="tableOperationIcon pointer-icon xftEdit" ' . t3lib_iconWorks::skinImg($BACK_PATH,'gfx/edit2.gif','') . ' title="' . $this->language->getLL('editColumnTips') . '"/>
													<img id="hide-' . $item['uid'] . '" class="tableOperationIcon pointer-icon xftHidden" ' . t3lib_iconWorks::skinImg($BACK_PATH,'gfx/' . $hiddenIcon . '.gif','') . ' title="' . $hiddenColumnTips . '"/>
													<img id="dele-' . $item['uid'] . '" class="tableOperationIcon pointer-icon xftDelete" ' . t3lib_iconWorks::skinImg($BACK_PATH,'gfx/garbage.gif','') . ' title="' . $this->language->getLL('deleteColumnTips') . '"/>';
				$columnContent .= $this->cObj->substituteMarkerArray($rowTemplate,$markerColumnArray,'###|###',1);
			}
		 }
		//builds header marker array
		$markerTableArray['titleHeader'] = $this->language->getLL("titleHeader");
		$markerTableArray['descriptionHeader'] = $this->language->getLL("descriptionHeader");
		$markerTableArray['crdateHeader'] = $this->language->getLL("crdateHeader");
		$markerTableArray['tstampHeader'] = $this->language->getLL("tstampHeader");
		$markerTableArray['iconsHeader'] = '<img id="new-NEW" class="tableOperationIcon pointer-icon xftNew" ' . t3lib_iconWorks::skinImg($BACK_PATH,'gfx/new_el.gif','') . ' title="' . $this->language->getLL('newColumnTips') . '"/>';
		$markerTableArray['deleteelementtitle'] = $this->language->getLL('deleteelementtitle');
		$markerTableArray['deleteelementmessage'] = $this->language->getLL('deleteelementmessage');
		$content = $this->cObj->substituteSubpart($tableContent, '###TEMPLATELISTCOLUMN###', $columnContent);
		$content = $this->cObj->substituteMarkerArray($content,$markerTableArray,'###|###',1);
		 return $content;
	} 	
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/xflextemplate/library/class.listTemplate.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/xflextemplate/library/class.listTemplate.php']);
}
?>