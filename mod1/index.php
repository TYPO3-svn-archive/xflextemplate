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

	// DEFAULT initialization of a module [BEGIN]
unset($MCONF);
require ("conf.php");
$BACK_PATH = '/Users/federico/Sites/TYPO3/testplugin/typo3/';
require ($BACK_PATH."init.php");
require ($BACK_PATH."template.php");
$LANG->includeLLFile("EXT:xflextemplate/mod1/locallang.php");
#include ("locallang.php");
require_once (PATH_t3lib."class.t3lib_scbase.php");
require_once (PATH_t3lib."class.t3lib_extmgm.php");

require_once('../library/class.elementTemplate.php');
require_once ('../library/class.listTemplate.php');
require_once (PATH_site."/typo3conf/ext/xflextemplate/class.tx_xflextemplate_importexport.php");
require_once ('../library/class.xftObject.php');
require_once (PATH_site."/typo3conf/ext/xflextemplate/class.fbgp.php");
$BE_USER->modAccess($MCONF,1);	// This checks permissions and exits if the users has no permission for entry.
$BACK_PATH = '/typo3/';
	// DEFAULT initialization of a module [END]

/*
 * @todo GESTIRE ERRORI SUGLI ELEMENTI
 * @todo controlla re che il campo template o templateFile ci siano 
 */
 
// TODO pippo

class tx_xflextemplate_module1 extends t3lib_SCbase {
	var $pageinfo;

	

		var $description;
		var $file;
		var $title;
		var $typoscript;
		var $palettes;
		var $enableGroups;
		var $loaded=false;
		var $max;
		var $maxFileLength=30;
		var $maxArrayKey=1000;

		var $extKey='xflextemplate';
		var $extensionDir='xflextemplate';
		
		var $cObj;
		var $templateFile;
		var $template;
		var $language;
		var $xftObject;
		
		var $elementArray;


	/**
	 * @return	[type]		...
	 */
	function init()	{
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;
		parent::init();
		$this->language = $LANG;
		$this->globalConf=unserialize($GLOBALS['TYPO3_CONF_VARS']["EXT"]["extConf"]['xflextemplate']);
		$this->textareaCols=($this->globalConf['textareaCols'])?$this->globalConf['textareaCols']:80;
		$this->textareaRows=($this->globalConf['textareaRows'])?$this->globalConf['textareaRows']:40;	
		$this->templateFile = PATH_site.'/typo3conf/ext/xflextemplate/configuration/subelement.tmpl';
		$this->template = file_get_contents($this->templateFile);
		$this->elementArray = array();
		$this->xftObject = t3lib_div::makeInstance('xftObject');
		
	}

	/**
	 * Adds items to the ->MOD_MENU array. Used for the function menu selector.
	 *
	 * @return	[type]		...
	 */
	function menuConfig()	{

	}

		// If you chose "web" as main module, you will need to consider the $this->id parameter which will contain the uid-number of the page clicked in the page tree
	/**
	 * Main function of the module. Write the content to $this->content
	 *
	 * @return	[type]		...
	 */
	function main()	{
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;
		$this->backPath=$BACK_PATH;
		$tmpDirectory = t3lib_div::dirname(t3lib_div::getIndpEnv('SCRIPT_NAME'));
		$this->extensionDir = t3lib_div::dirname($tmpDirectory).'/';
		// Access check!
		// The page will show only if there is a valid page and if this page may be viewed by the user
		$this->pageinfo = t3lib_BEfunc::readPageAccess($this->id,$this->perms_clause);
		$access = is_array($this->pageinfo) ? 1 : 0;
		/*la pagina deve essere sempre vista*/
		if (($this->id && $access) || ($BE_USER->user["admin"] && !$this->id))	{
        //if (1) {
		    $this->cObj = t3lib_div::makeInstance('tslib_cObj');
		        	//
				// call ajax
			//debug($_POST);
			if (t3lib_div::_GP('ajax')==1){
				$this->mainArray = t3lib_div::_GP('xftMain');
				$template = t3lib_div::makeInstance('elementTemplate');
				$template->init(PATH_typo3conf .'ext/xflextemplate/configuration/subelement.tmpl');
				switch(t3lib_div::_GP('action')){
					case 'newElement':
						$subElement = t3lib_div::_GP('subElement');
						$elementID = t3lib_div::_GP('elementID');
						$palette = t3lib_div::_GP('palette');
						//debug($palette);
						$parameters = array(
								'ID' => $elementID,
							);
						
						$parameters['paletteArray'] = (strlen(str_replace('|','',$palette))) ? explode('|',trim($palette)) : array();
						//$subelement = $template->setSubElementType('inputType',$parameters);
						//$parameters['subelement'] = $subelement;
						$content = $template->setSubElement($subElement,$parameters);
						echo($content);
						exit();
					break;
					case 'changeSubElement':
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
					case 'getLL':
						$key = t3lib_div::_GP('key');
						echo(htmlentities(utf8_decode($LANG->getLL($key))));
						exit();
					break;
					case 'dele':
						$this->xftObject->delete(t3lib_div::_GP('templateId'));
						exit();
					break;
					case 'hide':
						echo $this->xftObject->hideToggle(t3lib_div::_GP('templateId'));
						exit();
					break;
					default:
						switch ($this->mainArray['operation']){
							case 'submit':
								if ($this->evaluateError()){
									//system generates error
									//var_export($this->errorList);
									foreach($this->errorList as $key => $item){
										$errorString .= '<div class="xft-error xft-error-' . $key . '">' . htmlentities(utf8_decode(implode(',',$item))) . '</div>';
									}
									echo '1|' . $errorString;
								}
								else{
									$xftArray['xftMain'] = t3lib_div::_GP('xftMain');
									$xftArray['xflextemplate'] = t3lib_div::_GP('xflextemplate');
									$xml = $this->xftObject->save($xftArray);
									//echo '0|' . var_export($_POST,true);
									echo '0|' . $xml;
								}
								//var_export($_POST);
								exit();
							break;
						}
					break;
				}
				
			}
			else{
				
				//debug($_POST);
			}
			$this->doc = t3lib_div::makeInstance("bigDoc");
			$this->doc->backPath = $BACK_PATH;
			$this->doc->form='<form onsubmit="return false" id="xftForm" action="index.php" method="POST">';
			$this->doc->docType = 'xhtml_trans';
			$this->doc->styleSheetFile2='../typo3conf/ext/xflextemplate/res/css/template.css';
				// JavaScript
			$this->doc->JScode = '
			<link  rel="stylesheet" type="text/css" href="../res/css/ui.tabs.css" />
			<link href="' . $this->doc->backPath . 'sysext/t3editor/css/t3editor.css" type="text/css" rel="stylesheet" />
			<script type="text/javascript">
					PATH_t3e = "' . $this->doc->backPath . 'sysext/t3editor/";
					//PATH_xft = "' . $this->doc->backPath . '../typo3conf/ext/xflextemplate/";
			</script>
			<script type="text/javascript" src="../javascript/jquery/jquery-1.2.6.pack.js"></script>
			<script type="text/javascript" src="../javascript/jquery/jquery-ui-1.5.3.min.js"></script>


			';
			
			


			if(t3lib_div::_GP('templateId') && ((t3lib_div::_GP('action') == 'edit') || (t3lib_div::_GP('action') == 'new'))){				
				//$this->mainArray = t3lib_div::_GP('xftMain');
				if(t3lib_div::_GP('action') == 'edit' && t3lib_div::_GP('templateId')){
					$xftArray = $this->xftObject->load(t3lib_div::_GP('templateId'));
					//debug($xftArray);
					$this->mainArray = $xftArray['xftMain'];
					$this->xFlexArray = $xftArray['xflextemplate'];
				}
				
				//$this->elementArray = array('typoscriptbody'=>'ecco il typoscript','generalbody'=>'prova di general');
				// Render content:
				//debug($this->mainArray);
				$this->moduleContent();
				$this->getGeneralTab();
				$this->getTyposcriptTab();
				$this->getHTMLTab();
				$this->getDescriptionTab();
				$this->getElementTab();
				$content = $this->makeTabs();
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
			else{
				$templateListObject = t3lib_div::makeInstance('listTemplate');
				$templateListObject->init($this->language, PATH_typo3conf .'ext/xflextemplate/configuration/subelement.tmpl', $this->globalConf);
				$content = $templateListObject->getTemplateList();
				$this->doc->JScode .= '<script type="text/javascript" src="../javascript/library/class.ajax.js"></script>
				<script type="text/javascript" src="../javascript/library/class.templateList.js"></script>
										<script type="text/javascript" src="../javascript/backEnd.js"></script>';
			}
			// ShortCut
			
			$this->content.=$this->doc->startPage($LANG->getLL("title"));
			$this->content.=$this->doc->spacer(10);
			$this->content.=$this->doc->header($LANG->getLL("title"));
			$this->content.=$this->doc->divider(15);
			$this->content.=$content;
		}
	}
	
	function getGeneralTab(){
		$this->elementArray['generalicons'] = '<img class="pointer-icon xftSaveDok" ' . t3lib_iconWorks::skinImg($this->backPath,'gfx/savedok.gif','') . ' title="' . $this->language->getLL('xftSaveDokTitle') . '"/>';
		$this->elementArray['xfttitle'] = $this->language->getLL('xftTitle');
		$this->elementArray['generalbody'] = '<div class="tab-inner-container" ><label for="xftTitle">' . $this->language->getLL('xftTitle') . '</label><input type="text" id="xftTitle" name="xftMain[xftTitle]" value="' . $this->mainArray['xftTitle'] . '" /></div>';
	}
	
	function getTyposcriptTab(){
		$this->elementArray['typoscripticons'] = '<img class="pointer-icon xftSaveDok" ' . t3lib_iconWorks::skinImg($this->backPath,'gfx/savedok.gif','') . ' title="' . $this->language->getLL('xftSaveDokTitle') . '"/>';
		$this->elementArray['xftTyposcriptTitle'] = $this->language->getLL('xftTyposcriptTitle');
		$this->elementArray['typoscriptbody'] = '<div class="tab-inner-container" ><textarea class="fixed-font enable-tab t3editor" id="xftTyposcriptEditor" name="xftMain[xftTyposcript]" >' . $this->mainArray['xftTyposcript'] . '</textarea></div>';
	}
	
	function getHTMLTab(){
		$this->elementArray['htmlicons'] = '<img class="pointer-icon xftSaveDok" ' . t3lib_iconWorks::skinImg($this->backPath,'gfx/savedok.gif','') . ' title="' . $this->language->getLL('xftSaveDokTitle') . '"/>';
		$this->elementArray['xftHTMLTitle'] = $this->language->getLL('xftHTMLTitle');
		$this->elementArray['HTMLbody'] = '<div class="tab-inner-container" ><textarea class="fixed-font enable-tab t3editor" id="xftHTMLEditor" name="xftMain[xftHTML]" cols="' . $this->textareaCols . '" rows="' . $this->textareaCols . '" >' . $this->mainArray['xftHTML'] . '</textarea></div>';
	}
	
	function getDescriptionTab(){
		//$this->elementArray['generalicons'] = '<img class="pointer-icon xftSaveDok" ' . t3lib_iconWorks::skinImg($this->backPath,'gfx/savedok.gif','') . ' title="' . $this->language->getLL('xftSaveDokTitle') . '"/>';
		$this->elementArray['xftDescriptionTitle'] = $this->language->getLL('xftDescriptionTitle');
		$this->elementArray['descriptionbody'] = '<div class="tab-inner-container" ><textarea class="xftDescriptionClass" name="xftMain[xftDescription]" >' . $this->mainArray['xftDescription'] . '</textarea></div>';
	}
	
	function getElementTab(){
		$this->elementArray['elementicons'] = '<img class="pointer-icon xftSaveDok" ' . t3lib_iconWorks::skinImg($this->backPath,'gfx/savedok.gif','') . ' title="' . $this->language->getLL('xftSaveDokTitle') . '"/><img class="pointer-icon xftNewElement" ' . t3lib_iconWorks::skinImg($this->backPath,'gfx/new_el.gif','') . ' title="' . $this->language->getLL('xftNewElementTitle') . '"/>';
		$this->elementArray['xftElementTitle'] = $this->language->getLL('xftElementTitle');
		$template = t3lib_div::makeInstance('elementTemplate');
		$template->init(PATH_typo3conf .'ext/xflextemplate/configuration/subelement.tmpl');
		//debug($_POST['xflextemplate'],'xftpost');
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
			foreach ($this->xFlexArray as $key => $item){
				$element[$key]['paletteArray'] = $paletteArray;
				//debug($element[$key],'elemento numero: ' . $key);
				$columns .= $template->setSubElement($element[$key]['type'], $element[$key]);	
			}
		}
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
	 * @return	[type]		...
	 */
	function printContent()	{

		$this->content.=$this->doc->endPage();
		//inserisco l'action, altrimenti mi si porta dietro anche il GET
		//$encode_type=(t3lib_div::_GP('op')=='import')?'enctype="multipart/form-data"':'';
		//$this->content=str_replace('<form action="" method="POST">','<form action="index.php" method="POST" '.$encode_type.'>',$this->content);
		echo $this->content;
	}
	
	
	function makeTabs(){
		$this->elementArray['generalTitle'] = $this->language->getLL('generalTitle');
		$this->elementArray['descriptionTitle'] = $this->language->getLL('descriptionTitle');
		$this->elementArray['typoscriptTitle'] = $this->language->getLL('typoscriptTitle');
		$this->elementArray['elementTitle'] = $this->language->getLL('elementTitle');
		$this->elementArray['HTMLTitle'] = $this->language->getLL('HTMLTitle');
		$subpart = $this->cObj->getSubpart($this->template,'TABS');
		$tabSelected = ($this->mainArray['TabSelected'])?$this->mainArray['TabSelected']:0;
		$uid = ($this->mainArray['uid'])?$this->mainArray['uid']:0; 
		//debug($this->elementArray);
		//debug($this->cObj->substituteMarkerArray($subpart,$this->elementArray,'###|###',1));
		$this->hiddenFields[]='<input type="hidden" name="ajax" value="1" />';
		$this->hiddenFields[]='<input type="hidden" id="xftTabSelected" value="' . $tabSelected . '" name="xftMain[TabSelected]" />';
		$this->hiddenFields[]='<input type="hidden" id="xftOperation" name="xftMain[operation]" value="submit" />';
		$this->hiddenFields[]='<input type="hidden" id="xftUid" name="xftMain[uid]" value="' . $uid . '" />';
		return implode(chr(10),$this->hiddenFields) . $this->cObj->substituteMarkerArray($subpart,$this->elementArray,'###|###',1);
		//debug($this->content);
	}
	
	

	/**
	 * Generates the module content
	 *
	 * @return	[type]		...
	 */
	function moduleContent()	{
		global $LANG,$BE_USER;	

	}
	
	
	
	function evaluateError(){
		//check Template name
		$error = 0;
		//var_export($this->mainArray);
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
		else{
			$error = 1;
			$this->errorList['title'][] = $this->language->getLL('emptyTitleEntry');
		}
		if (!count(t3lib_div::_GP('xflextemplate'))){
			$error = 1;
			$this->errorList['element'][] = $this->language->getLL('emptyElementEntry');
		}
		else{
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