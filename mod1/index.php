<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2005 Federico Bernardin (federico@bernardin.it)
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
 * Module 'mod1' for the 'xflextemplate' extension.
 *
 * @author	Federico Bernardin <federico@bernardin.it>
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 */

unset($MCONF);
require ("conf.php");
require ($BACK_PATH."init.php");
require ($BACK_PATH."template.php");
$LANG->includeLLFile("EXT:xflextemplate/mod1/locallang.php");
require_once (PATH_t3lib."class.t3lib_scbase.php");
require_once (PATH_t3lib."class.t3lib_extmgm.php");
require_once('../library/class.elementTemplate.php');
require_once ('../library/class.listTemplate.php');
require_once (PATH_site."/typo3conf/ext/xflextemplate/class.tx_xflextemplate_importexport.php");
require_once ('../library/class.xftObject.php');
$BE_USER->modAccess($MCONF,1);	// This checks permissions and exits if the users has no permission for entry.

/**
 * @todo GESTIRE ERRORI SUGLI ELEMENTI
 * @todo controlla re che il campo template o templateFile ci siano 
 */
 
// TODO pippo

class tx_xflextemplate_module1 extends t3lib_SCbase {
	
		
	
		var $extKey='xflextemplate';
		var $extensionDir='xflextemplate';
		
		/**
		 * @var object content Object (tslib_content.php)
		 */	
		var $cObj;
		
		/**
		 * @var object object accessible without global keyword
		 */
		var $language;
		
		/**
		 * @var object instance of xflextemplate Template Object
		 */
		var $xftObject;
		
		/**
		 * @var array array containing marker for template
		 */
		var $elementArray;
		
		/**
		 * @var string text contained into $templateFile
		 * @see $templateFile 
		 */
		var $template;	
		
		
		/**
		 * @var string name of template file for backend customization
		 * @see $template 
		 */
		var $templateFile;
		
		/**
		 * @var array extension configuration from unserialization of $GLOBALS['TYPO3_CONF_VARS']["EXT"]["extConf"]['xflextemplate']
		 */
		var $globalConf;
		
		/**
		 * @var string path to typo3 directory
		 */
		var $backPath;
		
		/**
		 * @var array error description list of errors
		 */
		var $errorList;


	/**
	 * Initialization of XFT engine
	 * @return	void		
	 */
	function init()	{
		global $BE_USER,$LANG,$BACK_PATH;
		parent::init();
		//Assign objects
		$this->language = $LANG;		
		$this->backPath=$BACK_PATH;
		$this->globalConf=unserialize($GLOBALS['TYPO3_CONF_VARS']["EXT"]["extConf"]['xflextemplate']);
		//$this->textareaCols=($this->globalConf['textareaCols'])?$this->globalConf['textareaCols']:80;
		//$this->textareaRows=($this->globalConf['textareaRows'])?$this->globalConf['textareaRows']:40;	
		$this->templateFile = PATH_site.'/typo3conf/ext/xflextemplate/configuration/subelement.tmpl';
		$this->template = file_get_contents($this->templateFile);
		$this->xftObject = t3lib_div::makeInstance('xftObject');
		$this->elementArray = array();
		$this->errorList = array();		
	}

	/**
	 * Main function of the module. Write the content to $this->content
	 * This function answers both ajax and standard calls
	 *
	 * @return	void	
	 */
	function main()	{
		global $BE_USER;
		// Access check!
		// The page will show only if there is a valid page and if this page may be viewed by the user
		$this->pageinfo = t3lib_BEfunc::readPageAccess($this->id,$this->perms_clause);
		$access = is_array($this->pageinfo) ? 1 : 0;
		if (($this->id && $access) || ($BE_USER->user["admin"] && !$this->id))	{
		    $this->cObj = t3lib_div::makeInstance('tslib_cObj');
			//if post variable ajax=1 the calling is a ajax post
			if (t3lib_div::_GP('ajax')==1){
				$this->mainArray = t3lib_div::_GP('xftMain');
				$template = t3lib_div::makeInstance('elementTemplate');
				$template->init($this->templateFile);
				//identify post operation from ajax
				switch(t3lib_div::_GP('action')){
					case 'newElement': //newelement from elements TAB
						$subElement = t3lib_div::_GP('subElement');
						$elementID = t3lib_div::_GP('elementID');
						$palette = t3lib_div::_GP('palette');
						$parameters = array(
								'ID' => $elementID,
							);
						
						$parameters['paletteArray'] = (strlen(str_replace('|','',$palette))) ? explode('|',trim($palette)) : array();
						$content = $template->setSubElement($subElement,$parameters);
						echo($content);
						exit();
					break;
					case 'changeSubElement': //change subelement part of element (example radio, group, etc...)
						$elementID = t3lib_div::_GP('elementID');
						$parameters = array(
								'ID' => $elementID,
								'hardtype' => 'database',
							);
						$subElementType = t3lib_div::_GP('subElementType');
						$subelement = $template->setSubElementType($subElementType,$parameters);
						echo($subelement);
						exit();
					break;
					case 'getLL': //get label for a specific identificator (locallang file)
						$key = t3lib_div::_GP('key');
						echo(htmlentities(utf8_decode($this->language->getLL($key))));
						exit();
					break;
					case 'dele': //deleting of template object
						$this->xftObject->delete(t3lib_div::_GP('templateId'));
						exit();
					break;
					case 'hide': //hide/show a specific template object
						echo $this->xftObject->hideToggle(t3lib_div::_GP('templateId'));
						exit();
					break;
					default:
						switch ($this->mainArray['operation']){
							case 'submit': //submit for saving a specific template
								if ($this->evaluateError()){
									foreach($this->errorList as $key => $item){
										$errorString .= '<div class="xft-error xft-error-' . $key . '">' . htmlentities(utf8_decode(implode(',',$item))) . '</div>';
									}
									echo '1|' . $errorString;
								}
								else{
									$xftArray['xftMain'] = t3lib_div::_GP('xftMain');
									$xftArray['xflextemplate'] = t3lib_div::_GP('xflextemplate');
									$xml = $this->xftObject->save($xftArray);
									echo '0|' . $xml;
								}
								exit();
							break;
						}
					break;
				}
				
			}
			
			
			// PAGE CREATION
			$this->doc = t3lib_div::makeInstance("bigDoc");
			$this->doc->backPath = $this->backPath;
			$this->doc->form='<form onsubmit="return false" id="xftForm" action="index.php" method="POST">';
			$this->doc->docType = 'xhtml_trans';
			
			//stylesheets and js file
			$this->doc->JScode = '
				<link  rel="stylesheet" type="text/css" href="../res/css/template.css" />
				<link  rel="stylesheet" type="text/css" href="../res/css/ui.tabs.css" />
				<link href="' . $this->doc->backPath . 'sysext/t3editor/css/t3editor.css" type="text/css" rel="stylesheet" />
				<script type="text/javascript">
					PATH_t3e = "' . $this->doc->backPath . 'sysext/t3editor/";
					PATH_xft = "' . $this->doc->backPath . '../typo3conf/ext/xflextemplate/";
				</script>
				<script type="text/javascript" src="../javascript/jquery/jquery-1.2.6.pack.js"></script>
				<script type="text/javascript" src="../javascript/jquery/jquery-ui-1.5.3.min.js"></script>
			';
			
			// if operation is an edit or new creation template
			if(t3lib_div::_GP('templateId') && ((t3lib_div::_GP('action') == 'edit') || (t3lib_div::_GP('action') == 'new'))){				
				//if operation is edit retrive postvar templateId and load the specific template
				if(t3lib_div::_GP('action') == 'edit' && t3lib_div::_GP('templateId')){
					$xftArray = $this->xftObject->load(t3lib_div::_GP('templateId'));
					//load array: xftMain containing all general object (title, typoscript, description and html), xFlexArray containing xft elements
					$this->mainArray = $xftArray['xftMain'];
					$this->xFlexArray = $xftArray['xflextemplate'];
				}
				
				//generate marker Array for each content elements
				$this->getGeneralTab();
				$this->getTyposcriptTab();
				$this->getHTMLTab();
				$this->getDescriptionTab();
				$this->getElementTab();
				$content = $this->makeTabs();
				
				//This jscode will be put only for edit or creation element no for listing display
				$this->doc->JScode .= '
						<script type="text/javascript" src="../javascript/jquery/jquery.bgiframe.js"></script>
						<script type="text/javascript" src="../javascript/jquery/jquery.selectboxes.js"></script>
						<script type="text/javascript" src="../javascript/jquery/jquery.form.js"></script>
						<script type="text/javascript" src="../javascript/jquery/jquery.blockUI.js"></script>
						<script type="text/javascript" src="../javascript/library/class.general.js"></script>
						<script type="text/javascript" src="../javascript/library/class.ajax.js"></script>
						<script type="text/javascript" src="../javascript/library/class.element.js"></script>
						<script type="text/javascript" src="../javascript/library/editor/js/codemirror.js"></script>
						<script type="text/javascript" src="../javascript/library/mainBE.js"></script>';
			}
			else{ //if operation is a listing display (default view)
				//initialize listTemplate object
				$templateListObject = t3lib_div::makeInstance('listTemplate');
				$templateListObject->init($this->language, $this->templateFile, $this->globalConf);
				
				//retrieve content string for listing template
				$content = $templateListObject->getTemplateList();
				
				//This jscode will be put only for listing display and no for edit or creation element
				$this->doc->JScode .= '
						<script type="text/javascript" src="../javascript/library/class.ajax.js"></script>
						<script type="text/javascript" src="../javascript/library/class.templateList.js"></script>
						<script type="text/javascript" src="../javascript/backEnd.js"></script>';
			}
			
			
			//FINALIZE PAGE GENERATION			
			$this->content.=$this->doc->startPage($this->language->getLL("title"));
			//space before title
			$this->content.=$this->doc->spacer(10);
			//title
			$this->content.=$this->doc->header($this->language->getLL("title"));
			//line between title and page content
			$this->content.=$this->doc->divider(15);
			
			$this->content.=$content;
		}
	}
	
	/**
	 * Function creates marker for general tab
	 
	 * @return void
	 */
	function getGeneralTab(){
		$this->elementArray['generalicons'] = '<img class="pointer-icon xftSaveDok" ' . t3lib_iconWorks::skinImg($this->backPath,'gfx/savedok.gif','') . ' title="' . $this->language->getLL('xftSaveDokTitle') . '"/>';
		$this->elementArray['xfttitle'] = $this->language->getLL('xftTitle');
		$this->elementArray['generalbody'] = '<div class="tab-inner-container" ><label for="xftTitle">' . $this->language->getLL('xftTitle') . '</label><input type="text" id="xftTitle" name="xftMain[xftTitle]" value="' . $this->mainArray['xftTitle'] . '" /></div>';
	}
	
	/**
	 * Function creates marker for typoscript tab
	 
	 * @return void
	 */
	function getTyposcriptTab(){
		$this->elementArray['typoscripticons'] = '<img class="pointer-icon xftSaveDok" ' . t3lib_iconWorks::skinImg($this->backPath,'gfx/savedok.gif','') . ' title="' . $this->language->getLL('xftSaveDokTitle') . '"/>';
		$this->elementArray['xftTyposcriptTitle'] = $this->language->getLL('xftTyposcriptTitle');
		$this->elementArray['typoscriptbody'] = '<div class="tab-inner-container" ><textarea class="fixed-font enable-tab t3editor" id="xftTyposcriptEditor" name="xftMain[xftTyposcript]" >' . $this->mainArray['xftTyposcript'] . '</textarea></div>';
	}
	
	/**
	 * Function creates marker for HTML tab
	 
	 * @return void
	 */
	function getHTMLTab(){
		$this->elementArray['htmlicons'] = '<img class="pointer-icon xftSaveDok" ' . t3lib_iconWorks::skinImg($this->backPath,'gfx/savedok.gif','') . ' title="' . $this->language->getLL('xftSaveDokTitle') . '"/>';
		$this->elementArray['xftHTMLTitle'] = $this->language->getLL('xftHTMLTitle');
		$this->elementArray['HTMLbody'] = '<div class="tab-inner-container" ><textarea class="fixed-font enable-tab t3editor" id="xftHTMLEditor" name="xftMain[xftHTML]" cols="' . $this->textareaCols . '" rows="' . $this->textareaCols . '" >' . $this->mainArray['xftHTML'] . '</textarea></div>';
	}
	
	/**
	 * Function creates marker for description tab
	 
	 * @return void
	 */
	function getDescriptionTab(){
		$this->elementArray['xftDescriptionTitle'] = $this->language->getLL('xftDescriptionTitle');
		$this->elementArray['descriptionbody'] = '<div class="tab-inner-container" ><textarea class="xftDescriptionClass" name="xftMain[xftDescription]" >' . $this->mainArray['xftDescription'] . '</textarea></div>';
	}
	
	/**
	 * Function creates marker for element tab
	 
	 * @return void
	 */
	function getElementTab(){
		$this->elementArray['elementicons'] = '<img class="pointer-icon xftSaveDok" ' . t3lib_iconWorks::skinImg($this->backPath,'gfx/savedok.gif','') . ' title="' . $this->language->getLL('xftSaveDokTitle') . '"/><img class="pointer-icon xftNewElement" ' . t3lib_iconWorks::skinImg($this->backPath,'gfx/new_el.gif','') . ' title="' . $this->language->getLL('xftNewElementTitle') . '"/>';
		$this->elementArray['xftElementTitle'] = $this->language->getLL('xftElementTitle');
		$template = t3lib_div::makeInstance('elementTemplate');
		$template->init($this->templateFile);
		//starting element ID
		$elementID = 1;
		if(count($this->xFlexArray)){
			foreach ($this->xFlexArray as $key => $item){
				foreach ($item as $subKey => $value)
					$elementArray[$subKey] = $value;
				$elementArray['id'] = $elementID;
				$elementID++;
				$paletteArray[] = $item['title'] . '_' . $elementArray['id'];
				$element[$key] = $elementArray;
			}
			//$element contains all element object
			
			//setting palette array (with every title) for each object
			foreach ($this->xFlexArray as $key => $item){
				$element[$key]['paletteArray'] = $paletteArray;
				
				//render element
				$columns .= $template->setSubElement($element[$key]['type'], $element[$key]);	
			}
		}
		
		//generate element content HTML with dialogs
		$this->elementArray['elementbody'] = '
			<div class="column"> ' . $columns . '
			</div>
			<div id="dialogContainer">
				<div id="dialog" title="' . $this->language->getLL('deleteelementtitle') . '">
					<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span><div class="dialogContent">' .  $this->language->getLL('deleteelementmessage') . '</div></p>
				</div>
				<div id="dialogError" title="' . $this->language->getLL('dialogErrorTitle') . '">
					<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span><div class="dialogContent"></div></p>
				</div>
			</div>
			<span class="clear">&nbsp;</span>			
		';
	}
	

	/**
	 * Prints out the module HTML
	 *
	 * @return	void		
	 */
	function printContent()	{

		$this->content.=$this->doc->endPage();
		echo $this->content;
	}
	
	/**
	 * Merge all tabs into and return the form content
	 
	 * @return string main content HTML string
	 */
	function makeTabs(){
		$this->elementArray['generalTitle'] = $this->language->getLL('generalTitle');
		$this->elementArray['descriptionTitle'] = $this->language->getLL('descriptionTitle');
		$this->elementArray['typoscriptTitle'] = $this->language->getLL('typoscriptTitle');
		$this->elementArray['elementTitle'] = $this->language->getLL('elementTitle');
		$this->elementArray['HTMLTitle'] = $this->language->getLL('HTMLTitle');
		
		//merge tabs inside template
		$subpart = $this->cObj->getSubpart($this->template,'TABS');
		
		//insert template uid, if it's an insert operation setting uid to 0
		$uid = ($this->mainArray['uid'])?$this->mainArray['uid']:0; 
		
		//insert hidden HTML input element
		$this->hiddenFields[]='<input type="hidden" name="ajax" value="1" />';
		$this->hiddenFields[]='<input type="hidden" id="xftOperation" name="xftMain[operation]" value="submit" />';
		$this->hiddenFields[]='<input type="hidden" id="xftUid" name="xftMain[uid]" value="' . $uid . '" />';
		
		//merge all and returning content
		return implode(chr(10),$this->hiddenFields) . $this->cObj->substituteMarkerArray($subpart,$this->elementArray,'###|###',1);
	}
	
	/**
	 * Evaluates if an error is submitted during ajax submition operation (saving data)
	 * 
	 * @return int 0 no error, 1 at least one error
	 */
	function evaluateError(){
		//resetting error variable
		$error = 0;
		//if title is present check a duplicated possible value from database
		if ($this->mainArray['xftTitle']){
			if(!$this->mainArray['uid']){
				//title is inserted but it must control for inserting operation the uniqness of name
				//debug($GLOBALS['TYPO3_DB']->SELECTquery('title','tx_xflextemplate_template','deleted=0'));
				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('title','tx_xflextemplate_template','deleted=0');
				while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
					if ($row['title'] == $this->mainArray['xftTitle']){
						$error = 1;
						$this->errorList['title'][] = $this->language->getLL('duplicateTitleEntry');
						break;
					}
				}
			}				
		}
		else{ //if title is empty thrown an error
			$error = 1;
			$this->errorList['title'][] = $this->language->getLL('emptyTitleEntry');
		}
		// if any element is inserted into template thrown an error
		if (!count(t3lib_div::_GP('xflextemplate'))){
			$error = 1;
			$this->errorList['element'][] = $this->language->getLL('emptyElementEntry');
		}
		else{ // if at least one element is present, check if title value is defined
			foreach(t3lib_div::_GP('xflextemplate') as $mainKey=>$item)
				foreach($item as $key=>$value){
					if ($key == 'title' and strlen($value) == 0){
						$this->errorList['element'][] = sprintf($this->language->getLL('emptyElementTitleEntry'), $mainKey);
						$error = 1;
					}
				}
		}
		return $error;
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/xflextemplate/mod1/index.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/xflextemplate/mod1/index.php']);
}




// Make instance:
$SOBE = t3lib_div::makeInstance('tx_xflextemplate_module1');
$SOBE->init();

// Include files?
foreach($SOBE->include_once as $INC_FILE)	include_once($INC_FILE);

$SOBE->main();
$SOBE->printContent();

?>