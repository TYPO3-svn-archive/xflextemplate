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

class listTemplate {
	
	var $xft;
	var $template;
	var $cObj;
	var $language;
	var $globalConf;
	
	function init($langObj, $fileName='', $globalConf){
		$this->xft = t3lib_div::makeInstance('xftObject');
		$this->cObj = t3lib_div::makeInstance('tslib_cObj');
		$this->template = ($fileName) ? file_get_contents($fileName) : '';
		$this->language = $langObj;
		$this->globalConf = $globalConf;
		//debug($this->template,'ffffff');
	}
	
	function getTemplateList(){
		global $BACK_PATH;
		 $templateList = $this->xft->getTemplateList();
		 if(count($templateList)){		 	
			$tableContent = $this->cObj->getSubpart($this->template,'###TEMPLATELIST###');
			$rowTemplate = $this->cObj->getSubpart($tableContent,'###TEMPLATELISTCOLUMN###');
			$columnContent = '';
			foreach($templateList as $item){
				$markerColumnArray = array();
				$markerColumnArray['titlecolumn'] = $item['title'];
				$markerColumnArray['crdatecolumn'] = date($this->globalConf['date'],$item['crdate']);
				$markerColumnArray['tstampcolumn'] = date($this->globalConf['date'],$item['tstamp']);
				$markerColumnArray['descriptioncolumn'] = $item['description'];
				$markerColumnArray['uidRowID'] = $item['uid'];
				$hiddenIcon = ($item['hidden'])?'button_unhide':'button_hide';
				$markerColumnArray['iconsColumn'] = '<img id="edit-' . $item['uid'] . '" class="tableOperationIcon pointer-icon xftSaveDok" ' . t3lib_iconWorks::skinImg($BACK_PATH,'gfx/edit2.gif','') . ' title="' . $this->language->getLL('xftEditIcon') . '"/>
													<img id="hide-' . $item['uid'] . '" class="tableOperationIcon pointer-icon xftHidden" ' . t3lib_iconWorks::skinImg($BACK_PATH,'gfx/' . $hiddenIcon . '.gif','') . ' title="' . $this->language->getLL('xftHiddenIcon') . '"/>
													<img id="dele-' . $item['uid'] . '" class="tableOperationIcon pointer-icon xftSaveDok" ' . t3lib_iconWorks::skinImg($BACK_PATH,'gfx/garbage.gif','') . ' title="' . $this->language->getLL('xftDelteIcon') . '"/>';
				$columnContent .= $this->cObj->substituteMarkerArray($rowTemplate,$markerColumnArray,'###|###',1);
			}
			$markerTableArray['titleHeader'] = $this->language->getLL("titleHeader");
			$markerTableArray['descriptionHeader'] = $this->language->getLL("descriptionHeader");
			$markerTableArray['crdateHeader'] = $this->language->getLL("crdateHeader");
			$markerTableArray['tstampHeader'] = $this->language->getLL("tstampHeader");
			$markerTableArray['iconsHeader'] = $this->language->getLL("iconsHeader");
			$markerTableArray['deleteelementtitle'] = $this->language->getLL('deleteelementtitle');
			$markerTableArray['deleteelementmessage'] = $this->language->getLL('deleteelementmessage');
			$content = $this->cObj->substituteSubpart($tableContent, '###TEMPLATELISTCOLUMN###', $columnContent);
			$content = $this->cObj->substituteMarkerArray($content,$markerTableArray,'###|###',1);
		 }
		 return $content;
	} 
	
}
?>