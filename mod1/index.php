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
 *
 *
 *   74: class tx_xflextemplate_module1 extends t3lib_SCbase
 *  158:     function init()
 *  179:     function menuConfig()
 *  189:     function main()
 *  220:     function jumpToUrl(URL)
 *  230:     function submitFormwithCheck(extradata,checkstr)
 *  245:     function moveElement(id,direction)
 *  257:     function jumpToUrlwithCheck(URL,checkstr)
 *  495:     function printContent()
 *  509:     function moduleContent()
 *  608:     function checkError()
 *  668:     function createForm()
 *  804:     function addContent($type,$newkey)
 *  842:     function getType($type,$fieldNumber)
 *  857:     function getHTMLTag($type,$fieldNumber)
 *  899:     function getArrayFromXML($uid)
 *  953:     function pippo($xml)
 *  973:     function moveElement($id,$direction)
 * 1007:     function saveTemplate($uid)
 * 1063:     function getTemplateList()
 *
 * TOTAL FUNCTIONS: 19
 * (This index is automatically created/updated by the extension "extdeveval")
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
						echo(htmlentities($LANG->getLL($key)));
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
										$errorString .= '<div class="xft-error xft-error-' . $key . '">' . htmlentities(implode(',',$item)) . '</div>';
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
			$this->content.=$this->doc->header($LANG->getLL("title"));
			$this->content.=$this->doc->spacer(5);
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
		$this->elementArray['descriptionbody'] = '<div class="tab-inner-container" ><textarea class="xftDescriptionClass" name="xftMain[xftDescription]" cols="' . $this->textareaCols . '" rows="' . $this->textareaCols . '" >' . $this->mainArray['xftDescription'] . '</textarea></div>';
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
		return $error;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	

	/**
	 * [Describe function...]
	 *
	 * @return	[type]		...
	 */
	function checkError(){
		global $LANG;
		//name miss error
		$this->errorList=array();
		$this->errorDataArray=array();
		if($this->fieldsArray){
			/*check for tt_content column*/
			$res=$GLOBALS['TYPO3_DB']->sql(TYPO3_db,'show columns from `'.TYPO3_db.'`.tt_content');
			while ($row=$GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
				$tt_contentFieldsList[]=$row['Field'];
			}
			foreach($this->fieldsArray as $key=>$value){
				$reverseFieldsArray[$value['name']]=$key;
				$reverseFieldsArrayCount[$value['name']]=($reverseFieldsArrayCount[$value['name']])?$reverseFieldsArrayCount[$value['name']]+1:1;
			}
			if (is_array($this->fieldsArray)){
				foreach($this->fieldsArray as $key=>$value){
					if(!$value['name']){
						$this->errorList[2]=$LANG->getLL('namemissing');
						$this->errorDataArray[$key]['name']=1;
					}
					else {
						//check if title is duplicate or present in tt_content table
						if((array_key_exists($value['name'],$reverseFieldsArrayCount) && $reverseFieldsArrayCount[$value['name']]>1) || (in_array($value['name'],$tt_contentFieldsList))){
							$this->errorList[4]=$LANG->getLL('nameduplication');
							$this->errorDataArray[$key]['duplicate']=1;
						}
					}
					if($value['palettes']){
						$tmpPalettesArray[$value['palettes']][]=$value['name'];
						$tmpPalettesArray[$value['palettes']]['key']=$reverseFieldsArray[$value['palettes']];
						$listValuePalettes[$value['name']]=1;
					}
				}
			}
			//debug($tmpPalettesArray);
			//debug($reverseFieldsArray);
			if($tmpPalettesArray){
				foreach($tmpPalettesArray as $key=>$value){
					if (array_key_exists($key,$listValuePalettes))
						$this->errorList[3]=$LANG->getLL('paletteserror');
						$this->errorDataArray[$value['key']]['palettes']=1;
				}
			}
			/*if(!$this->file){
					$this->errorList[1]=$LANG->getLL('filemissing');
					$this->errorDataArray['file']=1;
			}*/
			if(!$this->title){
					$this->errorList[0]=$LANG->getLL('titlemissing');
					$this->errorDataArray['title']=1;
			}
		}
		return (count($this->errorList))?1:0;
	}

	/**
	 * [Describe function...]
	 *
	 * @return	[type]		...
	 */
	function createForm(){
		global $LANG;
		if (is_array($this->fieldsArray)){
			reset($this->fieldsArray);
			$this->start=key($this->fieldsArray);
			end($this->fieldsArray);
			$this->end=key($this->fieldsArray);
		}
				//debug($this->fieldsArray);
				//solo se non caricato
				if($this->fieldsArray){
					foreach($this->fieldsArray as $value)
						$this->palettesValue[]=$value['name'];
				}

				$uid=($uid)?$uid:((t3lib_div::_GP('loadXML'))?t3lib_div::_GP('loadXML'):t3lib_div::_GP('uidupdated'));
				$uid=($uid)?$uid:0;
				//debug(t3lib_div::_GP('loadXML'));
				if(!$this->loaded){
					//debug(t3lib_div::_GP('operation'));
					switch (t3lib_div::_GP('operation')){
						case 'add':
							$this->max=t3lib_div::_GP('items')+1;
						break;
						case 'del':
							if(t3lib_div::_GP('items')>1){
								unset($this->fieldsArray[t3lib_div::_GP('extradata')]);
								$this->max=t3lib_div::_GP('items')-1;
							}
							else
								$this->max=(t3lib_div::_GP('items'))?t3lib_div::_GP('items'):1;
						break;
						case 'move':
							$tempArray=explode(',',t3lib_div::_GP('extradata'));
							$this->moveElement($tempArray[0],$tempArray[1])	;
						break;
						case 'save':
						case 'saveandclose':
							if(!$this->error){
								$uid=$this->saveTemplate($uid);
								$this->errorDataArray['notsaved']=0;
								if(t3lib_div::_GP('operation')=='saveandclose'){
									return $this->getTemplateList();
								}
							}
							else
								$this->errorDataArray['notsaved']=1;
							$this->max=(t3lib_div::_GP('items'))?t3lib_div::_GP('items'):1;
						break;
						case 'close':
							return $this->getTemplateList();
						break;
						default:
							$this->max=(t3lib_div::_GP('items'))?t3lib_div::_GP('items'):1;
						break;
					}
				}
				//debug($this->fieldsArray,'fieldsArray');
				//debug($this->max,'max');
				$formHidden[]='<input type="hidden" name="items" value="'.$this->max.'" />';
				$formHidden[]='<input type="hidden" name="operation" value="" />';
				$formHidden[]='<input type="hidden" name="extradata" value="" />';
				$formHidden[]='<input type="hidden" name="op" value="reload" />';
				$formHidden[]='<input type="hidden" name="uidupdated" value="'.$uid.'" />';
				$last=1;
				$content='';
				if(count($this->errorList)){
					$notsaved=($this->errorDataArray['notsaved'])?'<div class="tx_xflextemplate_erroradvisor">'.$LANG->getLL('notsaved').'</div>':'';
					$content.='<div id="tx_xflextemplate_errorheader">'.$notsaved.'<div class="tx_xflextemplate_erroradvisor">'.$LANG->getLL('erroradvisor').'</div><ul class="tx_xflextemplate_errorlist"';
					ksort($this->errorList);
					foreach($this->errorList as $value){
					$content.='<li>'.$value.'</li>';
					}
					$content.='</ul></div>';
				}
				$content.='<table class="tx_xflextemplate_block_table_main">';
				if($this->file){
					$filenameArray=explode('/',$this->file);
					$filename=$filenameArray[count($filenameArray)-1];
					$options='<option value="'.$filename.'">'.$filename.'</option>';
				}
				$error=($this->error && $this->errorDataArray['title'])?'<span class="tx_xflextemplate_errorsingle">'.$LANG->getLL('singletitlemissing').'</span>':'';
				$content.='<tr><td class="tx_xflextemplate_label_column">'.$LANG->getLL("templatetitle").'</td><td class="tx_xflextemplate_value_column" colspan="2"><input type="text" name="title" value="'.$this->title.'"/>'.$error.'</td></tr>';
				$content.='<tr><td class="tx_xflextemplate_label_column">'.$LANG->getLL("description").'</td><td class="tx_xflextemplate_value_column" colspan="2"><textarea name="description"  cols="'.$this->textareaCols.'" rows="'.$this->textareaRows.'">'.$this->description.'</textarea></td></tr>';
				$content.='<tr><td class="tx_xflextemplate_label_column">'.$LANG->getLL("typoscript").'</td><td class="tx_xflextemplate_value_column" colspan="2"><textarea name="typoscript"  cols="'.$this->textareaCols.'" rows="'.$this->textareaRows.'">'.$this->typoscript.'</textarea></td></tr>';
				/*$error=($this->error && $this->errorDataArray['file'])?'<span class="tx_xflextemplate_errorsingle">'.$LANG->getLL('singlefilemissing').'</span>':'';*/
				$content.='<tr><td class="tx_xflextemplate_label_column">'.$LANG->getLL("templatefile").'</td><td class="tx_xflextemplate_value_column" colspan="2"><select size="1" multiple="0"  name="file_list"  style="width:250px;">'.$options.'</select><a href="#" onclick="setFormValueManipulate(\'file\',\'Remove\'); return false;"><img src="'.$this->backPath.'sysext/t3skin/icons/gfx/group_clear.gif" width="14" height="14" border="0"  alt="Rimuovi gli elementi selezionati" title="Rimuovi gli elementi selezionati" /></a><a href="#" onclick="setFormValueOpenBrowser(\'file\',\'file|||html,htm|\'); return false;"><img src="'.$this->backPath.'sysext/t3skin/icons/gfx/insert3.gif" width="15" height="15" border="0"  alt="Sfoglia Files" title="Sfoglia Files" /></a><input type="hidden" name="file" value="'.$this->file.'" /><input type="hidden" name="file_mul" value="0" />'.$error.'</td></tr>';
				$this->enableGroups=($this->enableGroups)?$this->enableGroups:'0';
				$optionsGroupSelected='';
				$res=$GLOBALS['TYPO3_DB']->exec_SELECTquery('uid,title','be_groups',' deleted=0 AND uid in ('.$this->enableGroups.')','','title');
				while($row=$GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
					$optionsGroupSelected.='<option value="'.$row['uid'].'" >'.$row['title'].'</option>';
				}
				if(t3lib_div::inList('-1',$this->enableGroups)){
					$optionsGroupSelected.='<option value="-1" >Admin</option>';
				}
				$optionsGroup='';
				$res=$GLOBALS['TYPO3_DB']->exec_SELECTquery('uid,title','be_groups',' deleted=0 AND uid not in ('.$this->enableGroups.')','','title');
				while($row=$GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
					$optionsGroup.='<option value="'.$row['uid'].'" >'.$row['title'].'</option>';
				}
				if(!t3lib_div::inList('-1',$optionsGroup)){
					$optionsGroup.='<option value="-1" >Admin</option>';
				}
				$content.='<tr><td class="tx_xflextemplate_label_column">'.$LANG->getLL("Permissions").'</td><td class="tx_xflextemplate_value_column" colspan="2"><table class="tx_xflextemplate_list_table"><tr><td>'.$LANG->getLL("groupChoosen").'<div class="tx_xflextemplate_list"><select name="groupselected" id="groupselected" style="width: 150px;" size="8">'.$optionsGroupSelected.'</select></div></td><td><div class="tx_xflextemplate_button"><a href="#" onclick="move(document.getElementById(\'groupselected\'),document.getElementById(\'groups\'));getElementInHidden(document.getElementById(\'enablegroups\'),document.getElementById(\'groupselected\')); return false;"><img src="'.$this->extensionDir.'res/group_clear.gif"  alt="'.$LANG->getLL("deleteButtonList").'" title="'.$LANG->getLL("deleteButtonList").'"/></a></div></td><td>'.$LANG->getLL("groupList").'<div class="tx_xflextemplate_list"><select name="groups" style="width: 150px;" id="groups" onChange="move(this,document.getElementById(\'groupselected\'));getElementInHidden(document.getElementById(\'enablegroups\'),document.getElementById(\'groupselected\'))" size="8">'.$optionsGroup.'</select></div></td></tr></table></td></tr></table>';
				$formHidden[]='<input type="hidden" id="enablegroups" name="enablegroups" value="" />';


				if (is_array($this->fieldsArray)){
					foreach($this->fieldsArray as $j=>$value){
						$content.=$this->addContent($this->fieldsArray[$j]['type'],$j);

					}
					$last=$j+1;
				}
				if($this->max>count($this->fieldsArray))
					$content.=$this->addContent('input',$last);
				$hiddenFields=implode(chr(10).chr(13),$formHidden);
				$content='<a href="#" onClick="document.forms[0].operation.value=\'add\';document.forms[0].action=\'index.php#anchor'.($last).'\';document.forms[0].submit()"><img src="'.$this->backPath.'sysext/t3skin/icons/gfx/new_el.gif" alt="'.$LANG->getLL("newtemplate").'" title="'.$LANG->getLL("newtemplateitem").'"/></a>
				<a href="#" onClick="document.forms[0].operation.value=\'save\';document.forms[0].submit()"><img src="'.$this->backPath.'sysext/t3skin/icons/gfx/savedok.gif" alt="'.$LANG->getLL("savetemplate").'" title="'.$LANG->getLL("savetemplate").'"/></a>
				<a href="#" onClick="document.forms[0].operation.value=\'saveandclose\';document.forms[0].submit()"><img src="'.$this->backPath.'sysext/t3skin/icons/gfx/saveandclosedok.gif" alt="'.$LANG->getLL("saveandclosetemplate").'" title="'.$LANG->getLL("saveandclosetemplate").'"/></a>
				<a href="#" onClick="document.forms[0].operation.value=\'close\';document.forms[0].submit()"><img src="'.$this->backPath.'sysext/t3skin/icons/gfx/closedok.gif" alt="'.$LANG->getLL("closetemplate").'" title="'.$LANG->getLL("closetemplate").'"/></a><br /><br />'.$hiddenFields.$content.'<a href="#" onClick="document.forms[0].operation.value=\'add\';document.forms[0].action=\'index.php#anchor'.($last).'\';document.forms[0].submit()"><img src="/typo3/sysext/t3skin/icons/gfx/new_el.gif" alt="'.$LANG->getLL("newtemplate").'" title="'.$LANG->getLL("newtemplateitem").'"/></a>
				<a href="#" onClick="document.forms[0].operation.value=\'save\';document.forms[0].submit()"><img src="'.$this->backPath.'sysext/t3skin/icons/gfx/savedok.gif" alt="'.$LANG->getLL("savetemplate").'" title="'.$LANG->getLL("savetemplate").'"/></a>
				<a href="#" onClick="document.forms[0].operation.value=\'saveandclose\';document.forms[0].submit()"><img src="'.$this->backPath.'sysext/t3skin/icons/gfx/saveandclosedok.gif" alt="'.$LANG->getLL("saveandclosetemplate").'" title="'.$LANG->getLL("saveandclosetemplate").'"/></a>
				<a href="#" onClick="document.forms[0].operation.value=\'close\';document.forms[0].submit()"><img src="'.$this->backPath.'sysext/t3skin/icons/gfx/closedok.gif" alt="'.$LANG->getLL("closetemplate").'" title="'.$LANG->getLL("closetemplate").'"/></a';
				return $content;
	}

function createForm1(){
		global $LANG;
		if (is_array($this->fieldsArray)){
			reset($this->fieldsArray);
			$this->start=key($this->fieldsArray);
			end($this->fieldsArray);
			$this->end=key($this->fieldsArray);
		}
				//debug($this->fieldsArray);
				//solo se non caricato
				if($this->fieldsArray){
					foreach($this->fieldsArray as $value)
						$this->palettesValue[]=$value['name'];
				}

				$uid=($uid)?$uid:((t3lib_div::_GP('loadXML'))?t3lib_div::_GP('loadXML'):t3lib_div::_GP('uidupdated'));
				$uid=($uid)?$uid:0;
				//debug(t3lib_div::_GP('loadXML'));
				if(!$this->loaded){
					//debug(t3lib_div::_GP('operation'));
					switch (t3lib_div::_GP('operation')){
						case 'add':
							$this->max=t3lib_div::_GP('items')+1;
						break;
						case 'del':
							if(t3lib_div::_GP('items')>1){
								unset($this->fieldsArray[t3lib_div::_GP('extradata')]);
								$this->max=t3lib_div::_GP('items')-1;
							}
							else
								$this->max=(t3lib_div::_GP('items'))?t3lib_div::_GP('items'):1;
						break;
						case 'move':
							$tempArray=explode(',',t3lib_div::_GP('extradata'));
							$this->moveElement($tempArray[0],$tempArray[1])	;
						break;
						case 'save':
						case 'saveandclose':
							if(!$this->error){
								$uid=$this->saveTemplate($uid);
								$this->errorDataArray['notsaved']=0;
								if(t3lib_div::_GP('operation')=='saveandclose'){
									return $this->getTemplateList();
								}
							}
							else
								$this->errorDataArray['notsaved']=1;
							$this->max=(t3lib_div::_GP('items'))?t3lib_div::_GP('items'):1;
						break;
						case 'close':
							return $this->getTemplateList();
						break;
						default:
							$this->max=(t3lib_div::_GP('items'))?t3lib_div::_GP('items'):1;
						break;
					}
				}
				//debug($this->fieldsArray,'fieldsArray');
				//debug($this->max,'max');
				$formHidden[]='<input type="hidden" name="items" value="'.$this->max.'" />';
				$formHidden[]='<input type="hidden" name="operation" value="" />';
				$formHidden[]='<input type="hidden" name="extradata" value="" />';
				$formHidden[]='<input type="hidden" name="op" value="reload" />';
				$formHidden[]='<input type="hidden" name="uidupdated" value="'.$uid.'" />';
				$last=1;
				$content='';
				if(count($this->errorList)){
					$notsaved=($this->errorDataArray['notsaved'])?'<div class="tx_xflextemplate_erroradvisor">'.$LANG->getLL('notsaved').'</div>':'';
					$content.='<div id="tx_xflextemplate_errorheader">'.$notsaved.'<div class="tx_xflextemplate_erroradvisor">'.$LANG->getLL('erroradvisor').'</div><ul class="tx_xflextemplate_errorlist"';
					ksort($this->errorList);
					foreach($this->errorList as $value){
					$content.='<li>'.$value.'</li>';
					}
					$content.='</ul></div>';
				}
				$content.='<div id="xft-main-block" class="tx_xflextemplate_block_main">';
				if($this->file){
					$filenameArray=explode('/',$this->file);
					$filename=$filenameArray[count($filenameArray)-1];
					$options='<option value="'.$filename.'">'.$filename.'</option>';
				}
				$error=($this->error && $this->errorDataArray['title'])?'<span class="tx_xflextemplate_errorsingle">'.$LANG->getLL('singletitlemissing').'</span>':'';
				$content.=$error.'<label for="title" class="tx_xflextemplate_label">'.$LANG->getLL("templatetitle").'</label><input type="text" name="title" value="'.$this->title.'" class="tx_xflextemplate_mainfield" />';
				$content.='<label for="description" class="tx_xflextemplate_label">'.$LANG->getLL("description").'</label><textarea name="description"  cols="'.$this->textareaCols.'" rows="'.$this->textareaRows.'" class="tx_xflextemplate_mainfield">'.$this->description.'</textarea>';
				$content.='<label for="typoscript" class="tx_xflextemplate_label">'.$LANG->getLL("typoscript").'</label><textarea name="typoscript"  cols="'.$this->textareaCols.'" rows="'.$this->textareaRows.'" class="tx_xflextemplate_mainfield">'.$this->typoscript.'</textarea>';
				/*$error=($this->error && $this->errorDataArray['file'])?'<span class="tx_xflextemplate_errorsingle">'.$LANG->getLL('singlefilemissing').'</span>':'';*/
				$content.=$error.'<label for="typoscript" class="tx_xflextemplate_label">'.$LANG->getLL("templatefile").'</label><a href="#" onclick="setFormValueManipulate(\'file\',\'Remove\'); return false;"><img class="tx_xflextemplate_imgfloatright" src="'.$this->backPath.'sysext/t3skin/icons/gfx/group_clear.gif" width="14" height="14" border="0"  alt="Rimuovi gli elementi selezionati" title="Rimuovi gli elementi selezionati" /></a><a href="#" onclick="setFormValueOpenBrowser(\'file\',\'file|||html,htm|\'); return false;"><img class="tx_xflextemplate_imgfloatright" src="'.$this->backPath.'sysext/t3skin/icons/gfx/insert3.gif" width="15" height="15" border="0"  alt="Sfoglia Files" title="Sfoglia Files" /></a><input type="hidden" name="file" value="'.$this->file.'" /><input type="hidden" name="file_mul" value="0" /><select class="tx_xflextemplate_mainfield_nofloat" size="1" multiple="0"  name="file_list"  style="width:250px;">'.$options.'</select><span class="clearer">&nbsp;</span>';
				$this->enableGroups=($this->enableGroups)?$this->enableGroups:'0';
				$optionsGroupSelected='';
				$res=$GLOBALS['TYPO3_DB']->exec_SELECTquery('uid,title','be_groups',' deleted=0 AND uid in ('.$this->enableGroups.')','','title');
				while($row=$GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
					$optionsGroupSelected.='<option value="'.$row['uid'].'" >'.$row['title'].'</option>';
				}
				if(t3lib_div::inList('-1',$this->enableGroups)){
					$optionsGroupSelected.='<option value="-1" >Admin</option>';
				}
				$optionsGroup='';
				$res=$GLOBALS['TYPO3_DB']->exec_SELECTquery('uid,title','be_groups',' deleted=0 AND uid not in ('.$this->enableGroups.')','','title');
				while($row=$GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
					$optionsGroup.='<option value="'.$row['uid'].'" >'.$row['title'].'</option>';
				}
				if(!t3lib_div::inList('-1',$optionsGroup)){
					$optionsGroup.='<option value="-1" >Admin</option>';
				}
				$content.='<label for="typoscript" class="tx_xflextemplate_label">'.$LANG->getLL("Permissions").'</label><table class="tx_xflextemplate_list_table"><tr><td>'.$LANG->getLL("groupChoosen").'<div class="tx_xflextemplate_list"><select name="groupselected" id="groupselected" style="width: 150px;" size="8">'.$optionsGroupSelected.'</select></div></td><td><div class="tx_xflextemplate_button"><a href="#" onclick="move(document.getElementById(\'groupselected\'),document.getElementById(\'groups\'));getElementInHidden(document.getElementById(\'enablegroups\'),document.getElementById(\'groupselected\')); return false;"><img src="'.$this->extensionDir.'res/group_clear.gif"  alt="'.$LANG->getLL("deleteButtonList").'" title="'.$LANG->getLL("deleteButtonList").'"/></a></div></td><td>'.$LANG->getLL("groupList").'<div class="tx_xflextemplate_list"><select name="groups" style="width: 150px;" id="groups" onChange="move(this,document.getElementById(\'groupselected\'));getElementInHidden(document.getElementById(\'enablegroups\'),document.getElementById(\'groupselected\'))" size="8">'.$optionsGroup.'</select></div></td></tr></table></div><span class="clearer">&nbsp;</span>';
				$formHidden[]='<input type="hidden" id="enablegroups" name="enablegroups" value="" />';


				if (is_array($this->fieldsArray)){
					foreach($this->fieldsArray as $j=>$value){
						$content.=$this->addContent($this->fieldsArray[$j]['type'],$j);

					}
					$last=$j+1;
				}
				if($this->max>count($this->fieldsArray))
					$content.=$this->addContent('input',$last);
				$hiddenFields=implode(chr(10).chr(13),$formHidden);
				$content='<a href="#" onClick="document.forms[0].operation.value=\'add\';document.forms[0].action=\'index.php#anchor'.($last).'\';document.forms[0].submit()"><img src="'.$this->backPath.'sysext/t3skin/icons/gfx/new_el.gif" alt="'.$LANG->getLL("newtemplate").'" title="'.$LANG->getLL("newtemplateitem").'"/></a>
				<a href="#" onClick="document.forms[0].operation.value=\'save\';document.forms[0].submit()"><img src="'.$this->backPath.'sysext/t3skin/icons/gfx/savedok.gif" alt="'.$LANG->getLL("savetemplate").'" title="'.$LANG->getLL("savetemplate").'"/></a>
				<a href="#" onClick="document.forms[0].operation.value=\'saveandclose\';document.forms[0].submit()"><img src="'.$this->backPath.'sysext/t3skin/icons/gfx/saveandclosedok.gif" alt="'.$LANG->getLL("saveandclosetemplate").'" title="'.$LANG->getLL("saveandclosetemplate").'"/></a>
				<a href="#" onClick="document.forms[0].operation.value=\'close\';document.forms[0].submit()"><img src="'.$this->backPath.'sysext/t3skin/icons/gfx/closedok.gif" alt="'.$LANG->getLL("closetemplate").'" title="'.$LANG->getLL("closetemplate").'"/></a><br /><br />'.$hiddenFields.$content.'<a href="#" onClick="document.forms[0].operation.value=\'add\';document.forms[0].action=\'index.php#anchor'.($last).'\';document.forms[0].submit()"><img src="/typo3/sysext/t3skin/icons/gfx/new_el.gif" alt="'.$LANG->getLL("newtemplate").'" title="'.$LANG->getLL("newtemplateitem").'"/></a>
				<a href="#" onClick="document.forms[0].operation.value=\'save\';document.forms[0].submit()"><img src="'.$this->backPath.'sysext/t3skin/icons/gfx/savedok.gif" alt="'.$LANG->getLL("savetemplate").'" title="'.$LANG->getLL("savetemplate").'"/></a>
				<a href="#" onClick="document.forms[0].operation.value=\'saveandclose\';document.forms[0].submit()"><img src="'.$this->backPath.'sysext/t3skin/icons/gfx/saveandclosedok.gif" alt="'.$LANG->getLL("saveandclosetemplate").'" title="'.$LANG->getLL("saveandclosetemplate").'"/></a>
				<a href="#" onClick="document.forms[0].operation.value=\'close\';document.forms[0].submit()"><img src="'.$this->backPath.'sysext/t3skin/icons/gfx/closedok.gif" alt="'.$LANG->getLL("closetemplate").'" title="'.$LANG->getLL("closetemplate").'"/></a';
				return $content;
	}

	/**
	 * [Describe function...]
	 *
	 * @param	[type]		$type: ...
	 * @param	[type]		$newkey: ...
	 * @return	[type]		...
	 */
	function addContent($type,$newkey){
		global $LANG;
		$content='<a name="anchor'.$newkey.'"></a><table class="tx_xflextemplate_block_table">';
		$error=($this->error && $this->errorDataArray[$newkey]['name'])?'<span class="tx_xflextemplate_errorsingle">'.$LANG->getLL('singlenamemissing').'</span>':'';
		$errorDuplicate=($this->error && $this->errorDataArray[$newkey]['duplicate'])?'<span class="tx_xflextemplate_errorsingle">'.$LANG->getLL('duplicatename').'</span>':'';
		$content.='<tr><td class="tx_xflextemplate_label_column">'.$LANG->getLL("eltitle").'</td><td class="tx_xflextemplate_value_column" colspan="2"><input type="text" name="tx_xflextemplate['.$newkey.'][name]" value="'.$this->fieldsArray[$newkey]['name'].'"/>'.$error.$errorDuplicate.'</td></tr>';
		if(count($this->palettesValue)>0){
			$options='<option value="">No palettes</option>';
		foreach($this->palettesValue as $key=>$value){
			$check=($this->fieldsArray[$newkey]['palettes']==$value)?'SELECTED':'';
			$options.=$this->cr.'<option '.$check.' value="'.$value.'">'.$value.'</option>';
		}

		$error=($this->error && $this->errorDataArray[$newkey]['palettes'])?'<span class="tx_xflextemplate_errorsingle">'.$LANG->getLL('singlepaletteerror').'</span>':'';
		$content.='<tr><td class="tx_xflextemplate_label_column">'.$LANG->getLL("selectpalettes").'</td><td class="tx_xflextemplate_value_column" colspan="2"><select name="tx_xflextemplate['.$newkey.'][palettes]">'.$options.'</select>'.$error.'</td></tr>';
		}
		$options='';
		foreach($this->xTypeArray as $key=>$value){
			$check=($this->fieldsArray[$newkey]['xtype']==$key)?'SELECTED':'';
			$options.=$this->cr.'<option '.$check.' value="'.$key.'">'.$value.'</option>';
		}
		$content.='<tr><td class="tx_xflextemplate_label_column">'.$LANG->getLL("type").'</td><td class="tx_xflextemplate_value_column" colspan="2"><select name="tx_xflextemplate['.$newkey.'][xtype]">'.$options.'</select></td></tr>';
		$content.='<tr><td class="tx_xflextemplate_label_column">'.$LANG->getLL("xtype").'</td><td class="tx_xflextemplate_value_column" colspan="2">'.$this->getType($type,$newkey).'</td></tr>';
		$content.=$this->getHTMLTag($type,$newkey);
		$content.='</table>';
		$upButton=($newkey!=$this->start)?'<span><a href="#" onClick="moveElement(\''.$newkey.'\',\'-1\');" ><img src="'.$this->backPath.'sysext/t3skin/icons/gfx/button_up.gif" alt="'.$LANG->getLL("moveupitem").'" title="'.$LANG->getLL("moveupitem").'"/></span>':'';
		$downButton=($newkey!=$this->end)?'<span><a href="#" onClick="moveElement(\''.$newkey.'\',\'+1\');" ><img src="'.$this->backPath.'sysext/t3skin/icons/gfx/button_down.gif" alt="'.$LANG->getLL("movedownitem").'" title="'.$LANG->getLL("movedownitem").'"/></span>':'';
		$content.='<div class="tx_xflextemplate_garbage"><a href="#" onClick="submitFormwithCheck(\''.$newkey.'\',\''.addslashes($LANG->getLL('deletemessageitem')).'\');" ><img src="'.$this->backPath.'sysext/t3skin/icons/gfx/garbage.gif" alt="'.$LANG->getLL("deleteitem").'" title="'.$LANG->getLL("deleteitem").'"/></a><span class="tx_xflextemplate_order">'.$upButton.$downButton.'</span></div>';
		return $content;
	}

	/**
	 * [Describe function...]
	 *
	 * @param	[type]		$type: ...
	 * @param	[type]		$fieldNumber: ...
	 * @return	[type]		...
	 */
	function getType($type,$fieldNumber){
		foreach($this->typeArray as $key=>$value){
			$check=($key==$type)?'SELECTED':'';
			$options.='<option value="'.$key.'" '.$check.'>'.$key.'</option>'.$this->cr;
		}
		return '<select name="tx_xflextemplate['.$fieldNumber.'][type]" onChange="javascript:document.forms[0].action=\'index.php#anchor'.$fieldNumber.'\';document.forms[0].operation.value=\'\';document.forms[0].submit()">'.$options.'</select>'.$this->cr;
	}

	/**
	 * [Describe function...]
	 *
	 * @param	[type]		$type: ...
	 * @param	[type]		$fieldNumber: ...
	 * @return	[type]		...
	 */
	function getHTMLTag($type,$fieldNumber){
		global $LANG;
		foreach($this->typeArray[$type] as $key=>$value){
			$tmpArray=explode(':',$value);
			//debug($tmpArray,$value);
			switch ($tmpArray[0]){
				case 'input':
					$size=($tmpArray[1])?$tmpArray[1]:'10';
					$field='<input type="text" name="tx_xflextemplate['.$fieldNumber.']['.$key.']" size="'.$size.'" value="'.$this->fieldsArray[$fieldNumber][$key].'"/>';
				break;
				case 'check':
					$check=($this->fieldsArray[$fieldNumber][$key])?'CHECKED':'';
					$field='<input '.$check.' type="checkbox" name="tx_xflextemplate['.$fieldNumber.']['.$key.']" value="1" />';
				break;
				case 'select':
					$options='';
					for($i=1;$i<count($tmpArray);$i++){
						$check=($this->fieldsArray[$fieldNumber][$key]==$tmpArray[$i])?'SELECTED':'';
						$options.='<option '.$check.' value="'.$tmpArray[$i].'">'.$tmpArray[$i].'</option>';
					}
					$field='<select type="text" name="tx_xflextemplate['.$fieldNumber.']['.$key.']" >'.$options.'</select>';
				break;
				case 'text':
					$cols=($tmpArray[1])?$tmpArray[1]:'40';
					$rows=($tmpArray[2])?$tmpArray[2]:'6';
					$field='<textarea name="tx_xflextemplate['.$fieldNumber.']['.$key.']" rows="'.$rows.'" cols="'.$cols.'" />'.$this->fieldsArray[$fieldNumber][$key].'</textarea>';
				break;
				case 'hidden':
					$value=($tmpArray[1])?$tmpArray[1]:'';
					$field='<input type="hidden" name="tx_xflextemplate['.$fieldNumber.']['.$key.']" value="'.$value.'" />';
				break;
			}
			$content.=($tmpArray[0]!='hidden')?'<tr><td>&nbsp;</td><td class="tx_xflextemplate_label_column">'.$LANG->getLL($key).'</td><td class="tx_xflextemplate_value_column">'.$field.'</td></tr>'.$this->cr:$field.$this->cr;
		}
		return $content;
	}

	/**
	 * [Describe function...]
	 *
	 * @param	[type]		$uid: ...
	 * @return	[type]		...
	 */
	function getArrayFromXML($uid){
		$res=$GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tx_xflextemplate_template','uid='.$uid);
		$row=$GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		//debug(t3lib_div::xml2tree($row['xml']));
		//$this->pippo($row['xml']);
		/*if (!$dom = domxml_open_mem($row['xml'])) {
		 return 0;
		}
		$elArray=$dom->get_elements_by_tagname('el');
		$i=1;
		foreach($elArray as $key=>$value){
			$tmpArray=$value->get_elements_by_tagname('name');
			$fieldArray[$i]['name']=$tmpArray[0]->get_content();
			$tmpArray=$value->get_elements_by_tagname('xtype');
			$fieldArray[$i]['xtype']=$tmpArray[0]->get_content();
			$tmpArray=$value->get_elements_by_tagname('palettes');
			if($tmpArray)
				$fieldArray[$i]['palettes']=$tmpArray[0]->get_content();
			$tmpArray=$value->get_elements_by_tagname('type');
			$fieldArray[$i]['type']=$tmpArray[0]->get_content();
			foreach($this->typeArray[$fieldArray[$i]['type']] as $ukey=>$uvalue){
				$tmpArray=$value->get_elements_by_tagname($ukey);
				if($tmpArray){
					$fieldArray[$i][$ukey]=$tmpArray[0]->get_content();
				}
			}
			$i++;
		}*/
		$elArray=$this->pippo($row['xml']);
		$i=1;
		foreach($elArray as $value){
			foreach($value as $key=>$item)
				$fieldArray[$i][$key]=$item;
			$i++;
		}
		$this->file=$row['file'];
		$this->description=$row['description'];
		$this->palettes=$row['palettes'];
		$this->enableGroups=$row['enablegroup'];
		$this->title=$row['title'];
		$this->typoscript=$row['typoscript'];
		//debug($row['xml'],'XMLdata');
		//debug($fieldArray,'XML');
		$this->max=count($fieldArray);
		$this->loaded=true;
		return $fieldArray;
	}

	/**
	 * [Describe function...]
	 *
	 * @param	[type]		$xml: ...
	 * @return	[type]		...
	 */
	function pippo($xml){
		$tmpArray=t3lib_div::xml2tree($xml);
		$tmpArray=$tmpArray['template'][0]['ch']['el'];
		$index=0;
		foreach($tmpArray as $elem){
			foreach($elem['ch'] as $key=>$item){
				$XMLArray[$index][$key]=$item[0]['values'][0];
			}
			$index++;
		}
		return $XMLArray;
	}

	/**
	 * [Describe function...]
	 *
	 * @param	[type]		$id: ...
	 * @param	[type]		$direction: ...
	 * @return	[type]		...
	 */
	function moveElement($id,$direction){
		//cerca elemento in direzione con id più vicino
		if($direction>0){
			$destination=$id+1;
			while (!array_key_exists($destination,$this->fieldsArray) && $destination<$this->end){
				$destination++;
			}
			if ($destination<=$this->end){
				// salva destinazione in $tempValue e mettin in destianzione la sorgente.
				$tempValue=$this->fieldsArray[$destination];
				$this->fieldsArray[$destination]=$this->fieldsArray[$id];
				$this->fieldsArray[$id]=$tempValue;
			}
		}
		else{
			$destination=$id-1;
			while (!array_key_exists($destination,$this->fieldsArray) && $destination>$this->start){
				$destination--;
			}
			if ($destination>=$this->start){
				// salva destinazione in $tempValue e mettin in destianzione la sorgente.
				$tempValue=$this->fieldsArray[$destination];
				$this->fieldsArray[$destination]=$this->fieldsArray[$id];
				$this->fieldsArray[$id]=$tempValue;
			}
		}
	}

	/**
	 * [Describe function...]
	 *
	 * @param	[type]		$uid: ...
	 * @return	[type]		...
	 */
	function saveTemplate($uid){
		global $BE_USER;
		foreach($this->fieldsArray as $key=>$value){
			$xml.='<el>'.$this->cr;
			foreach($value as $keyChild=>$valueChild){
				if($valueChild!=''){
					$xml.='<'.$keyChild.'>'.$valueChild.'</'.$keyChild.'>'.$this->cr;
				}
				if($keyChild=='palettes' && $valueChild!=''){
					$tmpPalettesArray[$valueChild][]=$value['name'];
					//mi salvo la lista dei valori puntatori, cioè che sono dentro una palettes
					$listValuePalettes[$value['name']]=1;
				}
			}
			$xml.='</el>'.$this->cr;
		}
		if($tmpPalettesArray){
			foreach($tmpPalettesArray as $key=>$value){
				if (!array_key_exists($key,$listValuePalettes))
					$palettesArray[$key]=implode(',',$value);
			}
		}
		$xml='<?xml version="1.0" encoding="utf-8" standalone="yes" ?>'.$this->cr.'
<template>'.$this->cr.$xml.'</template>';
		//debug($palettesArray);
		$file=(strstr($this->file,$_SERVER['DOCUMENT_ROOT'].'/'))?substr($this->file,strlen($_SERVER['DOCUMENT_ROOT'].'/')):$this->file;
		$file=(substr($file,strlen($file)-1)==',')?substr($file,0,strlen($file)-1):$file;
		$insertArray=array(
			'title'=>$this->title,
			'description'=>$this->description,
			'file'=>$file,
			'xml'=>str_replace("'","''",$xml),
			'typoscript'=>$this->typoscript,
			'palettes'=>serialize($palettesArray),
			'enablegroup'=>$this->enableGroups,
			'tstamp'=>mktime(date("h"),date("m"),date("s"),date("m"),date("d"),date("Y")),
			);
		if($uid){//update
			//debug($GLOBALS['TYPO3_DB']->UPDATEquery('tx_xflextemplate_template','uid='.$uid,$insertArray));
			$insertArray['crdate']=$insertArray['tstamp'];
			$insertArray['cruser_id']=$BE_USER->user['uid'];
			$GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_xflextemplate_template','uid='.$uid,$insertArray);
			return $uid;
		}
		else{
			//debug($GLOBALS['TYPO3_DB']->INSERTquery('tx_xflextemplate_template',$insertArray));
			$GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_xflextemplate_template',$insertArray);
			return $GLOBALS['TYPO3_DB']->sql_insert_id();
		}
	}

	/**
	 * [Describe function...]
	 *
	 * @return	[type]		...
	 */
	function getTemplateList(){
		global $LANG,$BACK_PATH;
		$res=$GLOBALS['TYPO3_DB']->exec_SELECTquery('title,tstamp,file,description,hidden,uid','tx_xflextemplate_template','','','title');
		$content='<input type="hidden" value="new" name="op" /><input type="image" '.t3lib_iconWorks::skinImg($BACK_PATH,'gfx/new_el.gif','').' style="border:0" alt=""/><a href="#" onClick="window.open(\'index.php?op=import\',\'importexport\',\'height=350,width=400,status=0,menubar=0,resizable=1,scrollbars=1\')"><img '.t3lib_iconWorks::skinImg($BACK_PATH,'gfx/import.gif','').' /></a><br /><br /><table class="tx_xflextemplate_listtemplate_table">
		<tr class="bgColor5"><td>'.$LANG->getLL('titlelist').'</td><td>'.$LANG->getLL('descriptionlist').'</td><td>'.$LANG->getLL('filelist').'</td><td>'.$LANG->getLL('datelist').'</td><td>&nbsp;</td></tr>';
		while($row=$GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
			$hiddenIcon=($row['hidden'])?'/typo3/sysext/t3skin/icons/gfx/button_unhide.gif':'/typo3/sysext/t3skin/icons/gfx/button_hide.gif';
			$hiddenState=($row['hidden'])?0:1;
			$content.='<tr class="tx_xflextemplate_listtemplate_table_tr">
				<td class="tx_xflextemplate_listtemplate_td"><span class="tx_xflextemplate_listtemplate_title">'.$row['title'].'</span></td>
				<td class="tx_xflextemplate_listtemplate_td"><span class="tx_xflextemplate_listtemplate_description">'.$row['description'].'</span></td>
				<td class="tx_xflextemplate_listtemplate_td"><span class="tx_xflextemplate_listtemplate_file">'.((strlen($row['file'])>$this->maxFileLength)?'...'.substr($row['file'],strlen($row['file'])-$this->maxFileLength):$row['file']).'</span></td>
				<td class="tx_xflextemplate_listtemplate_td"><span class="tx_xflextemplate_listtemplate_tstamp">'.fbgp::getDate($row['tstamp'],'en').'</span></td>
				<td class="tx_xflextemplate_listtemplate_td">
					<table class="tx_xflextemplate_listtemplate_icontable">
						<tr>
							<td class="tx_xflextemplate_listtemplate_icontable_td"><a href="index.php?loadXML='.$row['uid'].'&op=edit"><img src="'.$this->backPath.'sysext/t3skin/icons/gfx/edit2.gif" /></a></td>
							<td class="tx_xflextemplate_listtemplate_icontable_td"><a href="#" onClick=" {jumpToUrlwithCheck(\'index.php?uid='.$row['uid'].'&op=delete\',\''.$LANG->getLL('deletemessage').'\');} return false;"><img src="'.$this->backPath.'sysext/t3skin/icons/gfx/garbage.gif" /></a></td>
							<td class="tx_xflextemplate_listtemplate_icontable_td"><a href="index.php?uid='.$row['uid'].'&hiddenstate='.$hiddenState.'&op=hidden"><img src="'.$hiddenIcon.'" /></a></td>
							<td class="tx_xflextemplate_listtemplate_icontable_td"><a href="#" onClick="window.open(\'index.php?uid='.$row['uid'].'&op=export\',\'importexport\')"><img src="'.$this->backPath.'sysext/t3skin/icons/gfx/savesnapshot.gif" /></a></td>
						</tr>
					</table>
				</td>
				</tr>';
		}
		$content.='</table>';
		return $content;
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