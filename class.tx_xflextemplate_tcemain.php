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

require_once (PATH_site."/typo3conf/ext/xflextemplate/class.xft_div.php");

 /**
  * Hook 'tx_xflextemplate_tcemain' for the 't3lib_tcemain.processDatamapClass'
  * php class.
  *
  * @package typo3
  * @subpackage xflextemplate
  * @author	Federico Bernardin <federico@bernardin.it>
  * @version 1.0.4
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
	 * Questa funzione permette di gestire in maniera personalizzata l'array TCA
	 * in modo da creare la struttura flex con i campi presenti nel template
	 * e passarla quindi mediante hook alla classe tcemain perchè esegua le
	 * operazioni come se i valori provenissero da una flexdata
	 *
	 * @param	array		array con i campi provenienti dalle form (passato per riferimento)
	 * @param	string		tabella TCA utilizzata
	 * @param	int		identificativo dell'elemento in questione
	 * @param	object		puntatore alla classe con cui viene effettuato lo hook (tcemain)
	 * @return	void		none
	 */
	
	function processDatamap_preProcessFieldArray(&$incomingFieldArray, $table, $id, $obj){
		//only xflextemplate is managed
		if($incomingFieldArray['CType']==$this->_EXTKEY.'_pi1'){ 
			// define folder where images is stored
			$this->uploadFolder='uploads/pics/'; 
			// xtemplate have to be setted
			if($incomingFieldArray['xtemplate']){
				// fetch from db the xml for template
				$res=$GLOBALS['TYPO3_DB']->exec_SELECTquery('xml','tx_xflextemplate_template','title="'.$incomingFieldArray['xtemplate'].'" AND deleted=0 AND hidden=0');
				$dbrow=$GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
				// save xml data from template in $this->xmldata
				$this->xmldata=$dbrow['xml'];
				// extract xml data from template and assign to fieldArray
				$fieldArray=tx_xft_div::getArrayFromXML($this->xmldata);
				// $fieldArray format is:
				// name=>name of element
				// xtype=>specific type of element wrap function (text, multimedia, image,...)
				// type=>type of field of form (input, text, group, ...)
				// other specific field
				
				// if $fieldArray id an array, so if xml will be transform in an array code proceeds
				if(is_array($fieldArray)){
					// define class of file function to create a copy of file
					$this->fileFunc = t3lib_div::makeInstance('t3lib_basicFileFunctions'); 
					// if xml is correct update TCA
					$this->getTCA($GLOBALS['TCA']['tt_content'],$this->xmldata);
					// if the operation is an update the $id will be an integer
					if (is_numeric($id)){ 
						$res=$GLOBALS['TYPO3_DB']->exec_SELECTquery('xtemplate','tt_content','uid='.$id.' AND deleted=0');
						$datarow=$GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
					}else{
						//if id is not numeric, it means is a new content element and so $datarow['xtemplate']=$incomingFieldArray['xtemplate']
						$datarow['xtemplate']=$incomingFieldArray['xtemplate'];
						// during multiple copy TCA will remain modified in second, third or above, so if $incomingFieldArray[$this->_EXTKEY] is an array the 
						// tx_xft_div::getArrayFromXMLData will not call, but $incomingFieldArray fetch data from $incomingFieldArray[$this->_EXTKEY]
						if(is_array($incomingFieldArray[$this->_EXTKEY]))
							$incomingFieldArray=tx_xft_div::addFiledsFromArray($incomingFieldArray[$this->_EXTKEY],$incomingFieldArray);
						$incomingFieldArray=t3lib_div::array_merge_recursive_overrule($incomingFieldArray,tx_xft_div::getArrayFromXMLData($incomingFieldArray[$this->_EXTKEY]));
					}
					//if template from forms is different from one is coming from database, reset and clear flex content (otherwise flex will  contain data from previous template)
					if($datarow['xtemplate']==$incomingFieldArray['xtemplate']){ // if user doesn't change template
						// reset $fileList so for each content teh list of file is empty
						$fileList=array();
						// check for each field to verify if it's a file (control the xml data not $incomingFieldArray)
						foreach($fieldArray as $key=>$elem){ 
							// if element type is file save content in array fileList
							if ($elem['internal_type']=='file'){ 
								// verify if field is present in $incomingFieldArray
								if($incomingFieldArray[$elem['name']]){
									// reset file array
									$FileArrayDest=array();  
									// if id is not numeric and flexdata is not empty means the operation is copy
									if(!is_numeric($id) && strlen($incomingFieldArray[$this->_EXTKEY])>0){
										// store in $fileArray the value from $incomingFieldArray and create array
										$fileArray=explode(',',$incomingFieldArray[$elem['name']]);
										// cycle from file in the file field
										foreach($fileArray as $fileItem){
											// get unique name for file
											$filename=$this->fileFunc->getUniqueName($fileItem,PATH_site.$this->uploadFolder);
											// copy file from source
											t3lib_div::upload_copy_move(PATH_site.$this->uploadFolder.$fileItem,$filename);
											// insert name of file in $FileArrayDest
											$FileArrayDest[]=substr($filename,strlen(PATH_site.$this->uploadFolder));
										}
										// save in the source field the final list of file copied
										$incomingFieldArray[$elem['name']]=implode(',',$FileArrayDest);
									}
									// save the file for each element in template with file in the variable $fileList
									foreach(explode(',',$incomingFieldArray[$elem['name']]) as $item){
										$fileList[]=$item;
									}
								}	
							}
							// save the original value of $incomingFieldArray in the new variable $flexDataFields
							$flexDataFields[$elem['name']]=$incomingFieldArray[$elem['name']];
						}
						// update ctf_files field
						$incomingFieldArray['xft_files']=(is_array($fileList))?implode(',',$fileList):$incomingFieldArray['xft_files'];
						// save in xflextemplate field the array from $flexDataFields
						$flexField=$this->getXMLDataFromArray($flexDataFields);
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
						$incomingFieldArray['xft_files']='';
					}
				}
				else{					
					//define hidden layout for header
					$incomingFieldArray['header_layout']=100;
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
	 */
	 function getTCA(&$TCA,$xmlArray) {
	 	$fieldArray=tx_xft_div::getArrayFromXML($xmlArray); // create array of field from xml template
		if(is_array($fieldArray)){ // if array is correct
			foreach($fieldArray as $object){
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
	 * This function creates the array of Flex Form in TYPO3 format from the array passed as value
	 *
	 * @param	array		$dataArray: array to transform
	 * @return	array		an array with data from input in TYPO3 flex form format
	 * @ver 1.0.4
	 */
	function getXMLDataFromArray($dataArray){
		if(is_array($dataArray)){
			foreach($dataArray as $key=>$item){
				$resultArray[$key]['vDEF']=$item; //create single item in form: [$key]['vDEF']=item
			}
		}
		$XMLDataArray['data']['sDEF']['lDEF']=$resultArray; // link array created above with ['data']['sDEF']['lDEF']
		return $XMLDataArray;
	}


}




if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/xflextemplate/class.tx_xflextemplate_tcemain.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/xflextemplate/class.tx_xflextemplate_tcemain.php']);
}
?>
