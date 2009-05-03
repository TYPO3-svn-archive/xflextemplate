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
 *   61: class tx_xflextemplate_tcemain
 *   84:     function processDatamap_postProcessFieldArray($status, $table, $id, &$fieldArray, $obj)
 *  110:     function processDatamap_preProcessFieldArray(&$incomingFieldArray, $table, $id, $obj)
 *  239:     function getTCA(&$TCA,$xmlArray)
 *  289:     function getDataArray($arrayToEvaluate,$resultArray)
 *  310:     function getXMLArray($xml)
 *  353:     function getArrayFromXML($xml)
 *  377:     function getArrayFromXMLData($xml)
 *  400:     function getXMLDataFromArray($dataArray)
 *
 * TOTAL FUNCTIONS: 8
 * (This index is automatically created/updated by the extension "extdeveval")
 * 
 */

//require_once (PATH_site."/typo3conf/ext/xflextemplate/class.xft_div.php");

require_once (t3lib_extMgm::extPath('xflextemplate')."library/class.tcaTransformation.php");

 /**
  * Hook 'tx_xflextemplate_tcemain' for the 't3lib_tcemain.processDatamapClass'
  * php class.
  *
  * @package typo3
  * @subpackage xflextemplate
  * @author	Federico Bernardin <federico@bernardin.it>
  * @version 1.1.0
  */
class tx_xflextemplate_tcemain {



	/*
	* Questa variabile contiene il nome del plugin
	*
	* @var  string $_EXTKEY
	*/
	var $_EXTKEY='xflextemplate';
	
	/*
	* Questa variabile contiene il xml del template
	*
	* @var  string $xmldata
	*/
    var $xmldata='';

	

	/**
	 * This function converts $incomingFieldArray to new one. It transforms all fields from $incomingFieldArray to a new flexform
	 * with xml format.
	 * 
	 * 
	 *
	 * @param	array		array con i campi provenienti dalle form (passato per riferimento)
	 * @param	string		tabella TCA utilizzata
	 * @param	int		identificativo dell'elemento in questione
	 * @param	object		puntatore alla classe con cui viene effettuato lo hook (tcemain)
	 * @return	void		none
	 */
	
	function processDatamap_preProcessFieldArray(&$incomingFieldArray, $table, $id, $obj){
		//fetch data from CONF VARS
		$this->globalConf=unserialize($GLOBALS['TYPO3_CONF_VARS']["EXT"]["extConf"]['xflextemplate']);
		//only xflextemplate is managed
		if($incomingFieldArray['CType']==$this->_EXTKEY.'_pi1'){ 
			// define folder where images is stored
			$this->uploadFolder=$this->globalConf['uploadFolder']; 
			// xtemplate have to be setted
			if($incomingFieldArray['xtemplate']){
				// fetch from db the xml for template
				$res=$GLOBALS['TYPO3_DB']->exec_SELECTquery('xml','tx_xflextemplate_template','title="'.$incomingFieldArray['xtemplate'].'" AND deleted=0 AND hidden=0');
				$dbrow=$GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
				// save xml data from template in $this->xmldata
				$this->xmldata=$dbrow['xml'];
				// extract xml data from template and assign to fieldArray
				$fieldArray=xmlTransformation::getArrayFromXML($this->xmldata);
				$this->fieldArray = $fieldArray;
				// $fieldArray format is:
				// name=>name of element
				// xtype=>specific type of element wrap function (text, multimedia, image,...)
				// type=>type of field of form (input, text, group, ...)
				// other specific field
				// $fieldArray is an array if the template name is correct
				if(is_array($fieldArray)){
					// define class of file function to create a copy of file
					$this->fileFunc = t3lib_div::makeInstance('t3lib_basicFileFunctions'); 
					// if xml is correct update TCA
					$tcaTransformation = t3lib_div::makeInstance('tcaTransformation'); 
					$tcaTransformation->getFlexFieldTCA($GLOBALS['TCA']['tt_content'],$this->xmldata);
					// if the operation is an update the $id will be an integer and fetch old value form database
					if (t3lib_div::intval_positive($id)){ 
						$res=$GLOBALS['TYPO3_DB']->exec_SELECTquery('xtemplate','tt_content','uid='.$id.' AND deleted=0');
						$datarow=$GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
					}else{
						//if id is not numeric, it means is a new content element or copy and so $datarow['xtemplate']=$incomingFieldArray['xtemplate']
						$datarow['xtemplate']=$incomingFieldArray['xtemplate'];
					}
					//debug($datarow['xtemplate'].'='.$incomingFieldArray['xtemplate'],'pippo');
					//if template from forms is different from one is coming from database, reset and clear flex content (otherwise flex will  contain data from previous template)
					if($datarow['xtemplate']==$incomingFieldArray['xtemplate']){ // if user doesn't change template
						// reset $fileList so for each content teh list of file is empty
						$fileList=array();
										//debug($flexfields,'intval');
						//var_export($GLOBALS['TCA']['tt_content']['columns']);
					//	echo var_export($incomingFieldArray,true) . "\n\n";
						if(strlen($incomingFieldArray[$this->_EXTKEY])>0){							
								$flexFields=xmlTransformation::getArrayFromXMLData($incomingFieldArray[$this->_EXTKEY]);
						}					
						// check for each field to verify if it's a file (control the xml data not $incomingFieldArray)
						foreach($fieldArray as $key=>$elem){ 
							//if command is a copy manage the file replication and fetch data from flexform
							if(!t3lib_div::intval_positive($id) && strlen($incomingFieldArray[$this->_EXTKEY])>0){ //sono in copia
								// if element type is file save content in array fileList
								if ($elem['internal_type']=='file'){ 
									// verify if field is present in $flexFields
									if($flexFields[$elem['name']]){
										// reset file array
										$FileArrayDest=array();  
											// store in $fileArray the value from $flexFields and create array
											$fileArray=explode(',',$flexFields[$elem['name']]);
											// cycle from file in the file field
											foreach($fileArray as $fileItem){
												// get unique name for file
												$filename=$this->fileFunc->getUniqueName($fileItem,PATH_site.$this->uploadFolder);
												// copy file from source
												$ret = t3lib_div::upload_copy_move(PATH_site.$this->uploadFolder.'/'.$fileItem,$filename);
												// insert name of file in $FileArrayDest
												$FileArrayDest[]=substr($filename,strlen(PATH_site.$this->uploadFolder)+1);
											}
											// save in the source field the final list of file copied
											$flexDataFields[$elem['name']]=implode(',',$FileArrayDest);
										// save the file for each element in template with file in the variable $fileList
										foreach(explode(',',$flexDataFields[$elem['name']]) as $item){
											$fileList[]=$item;
										}
									}	
								}
								else{ // if not an internal_type=file save field from flexform into $flexDataFields
									$flexDataFields[$elem['name']]=$flexFields[$elem['name']];									
								}
							} // otherwise (not a copy) fetch data for flexfield from $incomingFieldArray
							else{
								// save the original value of $incomingFieldArray in the new variable 
								//$flexDataFields and add filename to xft_file if internal_type is file
								$flexDataFields[$elem['name']]=$incomingFieldArray[$elem['name']];
							}
						}
						// update ctf_files field
					/////	$incomingFieldArray['xft_files']=(is_array($fileList))?implode(',',$fileList):$incomingFieldArray['xft_files'];
						// save in xflextemplate field the array from $flexDataFields
						$flexField=xmlTransformation::getXMLDataFromArray($flexDataFields);
						$incomingFieldArray[$this->_EXTKEY]=$flexField;
						//BE use column bodytext for display name of element, so fill bodyfield column with header content
						$incomingFieldArray['bodytext']=$incomingFieldArray['header'];
						// hidden header column (name of element)
						$incomingFieldArray['header_layout']=100; //define hidden layout for header
					}
					else {
						//if template was changed, flex data will be erase.
						$incomingFieldArray[$this->_EXTKEY]='';
						// erase xft_files too
						/////$incomingFieldArray['xft_files']='';
					}
				}
				else{					
					//define hidden layout for header
					$incomingFieldArray['header_layout']=100;
				}
			}
		}
		//var_export($incomingFieldArray);
	}

	function processDatamap_postProcessFieldArray($status, $table, $id, &$fieldArray, $pObj){
		/*if(strlen($fieldArray[$this->_EXTKEY])>0 && is_array($this->fieldArray)){							
				$flexFields=xmlTransformation::getArrayFromXMLData($fieldArray[$this->_EXTKEY]);
				foreach($this->fieldArray as $key=>$elem){
					if ($elem['internal_type'] == 'file' && $flexFields[$elem['name']]){
						//echo $key . ':' . var_export($flexFields[$elem['name']],true) . "\n<br/>\n";
						$fileList[]=$flexFields[$elem['name']];
					}
				}
				if (is_array($fileList) && count($fileList))
					//$fieldArray['xft_files'] = implode(',',$fileList);
					$u=1;
		}
		var_export($fieldArray);*/	
	}	

}






if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/xflextemplate/hooks/class.tx_xflextemplate_tcemain.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/xflextemplate/hooks/class.tx_xflextemplate_tcemain.php']);
}
?>
