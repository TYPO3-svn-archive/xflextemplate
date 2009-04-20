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
	
	function init($fileName=''){
		$this->xft = t3lib_div::makeInstance('xftObject');
		//debug($this->template,'ffffff');
	}
	
	function getTemplateList(){
		 $templateList = $this->xft->getTemplateList();
		 if(count($templateList)){
		 	$tableHeader = '<table class="xft-table">
								<thead>
									<tr>
										<th>Title</th>
										<th>Description</th>
										<th>Create</th>
										<th>Updated</th>
										<th>Hidden</th>
									</tr>
								</thead>
							</table>';
			$content = $tableHeader;
		 }
		 return $content;
	} 
	
}
?>