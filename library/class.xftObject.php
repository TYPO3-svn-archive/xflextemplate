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

/**
 * XFT object for elementary function
 * 
 * @author Federico Bernardin <federico@bernardin.it>
 * @version 2.0
 * @package TYPO3
 * @subpackage xfletemplate
 */
class xftObject {
	
	/**
	 * @var string define the version of xft
	 */
	var $version = '2.0.0';
	
	/**
	 * This function retrieves rows from database
	 * 
	 * @return array list of template 
	 */
	function getTemplateList(){
		$rows = array();
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid,title,description,crdate,tstamp,hidden','tx_xflextemplate_template','deleted=0','','title');
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
			$rows[]=$row;
		}
		return $rows;
	}
	
	/**
	 * This function save data into database 
	 * @param array Array containing xftMain and xflextemplate subarray
	 * 
	 * @return void
	 */
	function save($dataArray){
		global $BE_USER;
		$uid = $dataArray['xftMain']['uid'];
		$dataArrayIndexed = array();
		$i=1; // counter for rewriting of array
		if($uid){ //data is an update
			foreach($dataArray['xflextemplate'] as $mainKey=>$item){
				$type = substr($item['type'],0,strlen($item['type'])-4);
				$title = $dataArray['xflextemplate'][$mainKey]['title'];
				foreach($item as $key=>$value){
					$keyReduced = substr($key,strlen($type)+1);
					switch ($key){
						case 'open':
							unset($dataArray['xflextemplate'][$mainKey][$key]);
						break;
						case 'palette':
							//if palette field contains string "element_" means it is a palette and translate into title name of palette field
							if (strstr($dataArray['xflextemplate'][$mainKey]['palette'],'element_')){
								$paletteID = substr($dataArray['xflextemplate'][$mainKey]['palette'],8);
								$dataArray['xflextemplate'][$mainKey]['palettes'] = $dataArray['xflextemplate'][$paletteID]['title'];
								unset($dataArray['xflextemplate'][$mainKey]['palette']);
								$paletteArray[$dataArray['xflextemplate'][$paletteID]['title']][] = $title;
							}
						break;
						case 'title':
							$dataArray['xflextemplate'][$mainKey]['name'] = $value;
							unset($dataArray['xflextemplate'][$mainKey]['title']);
						break;
						case 'type':
							$dataArray['xflextemplate'][$mainKey]['type'] = $type;
						break;
						default:
							if(strstr($key,$type . '_')){	
								if($value){
									$dataArray['xflextemplate'][$mainKey][$keyReduced] = $value;
									unset($dataArray['xflextemplate'][$mainKey][$key]);
								}	
								else						
									unset($dataArray['xflextemplate'][$mainKey][$key]);
							}								
						break;
					}
				}
				//move dataarray in dataarraIndexed so index starts with 1 and in incremental way
				$dataArrayIndexed[$i++] = $dataArray['xflextemplate'][$mainKey];
			}
			foreach($paletteArray as $key=>$item){
				$tempPaletteArray[$key] = implode(',', $item);
			}
			$xml = t3lib_div::array2xml($dataArrayIndexed);
			$savedData['title'] = $dataArray['xftMain']['xftTitle'];
			$savedData['description'] = $dataArray['xftMain']['xftDescription'];
			$savedData['typoscript'] = $dataArray['xftMain']['xftTyposcript'];
			$savedData['tstamp'] = mktime(date("H"),date("i"),date("s"),date("m"),date("d"),date("Y"));
			$savedData['html'] = $dataArray['xftMain']['xftHTML'];
			$savedData['palettes'] = serialize($tempPaletteArray);
			$savedData['xml'] = $xml;
			$savedData['version'] = $this->version;
			$GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_xflextemplate_template','uid=' . $uid ,$savedData);
		}
		else{ //data is an insert
			foreach($dataArray['xflextemplate'] as $mainKey=>$item){
				$type = substr($item['type'],0,strlen($item['type'])-4);
				$title = $dataArray['xflextemplate'][$mainKey]['title'];
				foreach($item as $key=>$value){
					$keyReduced = substr($key,strlen($type)+1);
					switch ($key){
						case 'open':
							unset($dataArray['xflextemplate'][$mainKey][$key]);
						break;
						case 'palette':
							//if palette field contains string "element_" means it is a palette and translate into title name of palette field
							if (strstr($dataArray['xflextemplate'][$mainKey]['palette'],'element_')){
								$paletteID = substr($dataArray['xflextemplate'][$mainKey]['palette'],8);
								$dataArray['xflextemplate'][$mainKey]['palettes'] = $dataArray['xflextemplate'][$paletteID]['title'];
								unset($dataArray['xflextemplate'][$mainKey]['palette']);
								$paletteArray[$dataArray['xflextemplate'][$paletteID]['title']][] = $title;
							}
						break;
						case 'title':
							$dataArray['xflextemplate'][$mainKey]['name'] = $value;
							unset($dataArray['xflextemplate'][$mainKey]['title']);
						break;
						case 'type':
							$dataArray['xflextemplate'][$mainKey]['type'] = $type;
						break;
						default:
							if(strstr($key,$type . '_')){	
								if($value){
									$dataArray['xflextemplate'][$mainKey][$keyReduced] = $value;
									unset($dataArray['xflextemplate'][$mainKey][$key]);
								}	
								else						
									unset($dataArray['xflextemplate'][$mainKey][$key]);
							}								
						break;
					}
				}
				//move dataarray in dataarraIndexed so index starts with 1 and in incremental way
				$dataArrayIndexed[$i++] = $dataArray['xflextemplate'][$mainKey];
			}
			foreach($paletteArray as $key=>$item){
				$tempPaletteArray[$key] = implode(',', $item);
			}
			$xml = t3lib_div::array2xml($dataArrayIndexed);
			$savedData['title'] = $dataArray['xftMain']['xftTitle'];
			$savedData['description'] = $dataArray['xftMain']['xftDescription'];
			$savedData['typoscript'] = $dataArray['xftMain']['xftTyposcript'];
			$savedData['crdate'] = mktime(date("H"),date("i"),date("s"),date("m"),date("d"),date("Y"));
			$savedData['tstamp'] = mktime(date("H"),date("i"),date("s"),date("m"),date("d"),date("Y"));
			$savedData['html'] = $dataArray['xftMain']['xftHTML'];
			$savedData['xml'] = $xml;
			$savedData['version'] = $this->version;
			$savedData['cruser_id'] = $BE_USER->user['uid'];
			$GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_xflextemplate_template',$savedData);			
		}
	}
	
	function load($uid) {
		//retrieve information on template
		$res=$GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tx_xflextemplate_template','uid='.$uid);
		$row=$GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		if (is_array($row)){
			$xftArray['xflextemplate'] = t3lib_div::xml2array($row['xml']);
			foreach($xftArray['xflextemplate'] as $key=>$value){
				$titleArray[$value['name']] = $key;
			}
			foreach($xftArray['xflextemplate'] as $mainKey=>$item){
				$type = $item['type'];
				foreach($item as $key=>$value)
					switch ($key){
						case 'palette':
							if ($xftArray['xflextemplate'][$mainKey]['palettes'] != 'none' && $xftArray['xflextemplate'][$mainKey]['palettes'] != '' )
								$xftArray['xflextemplate'][$mainKey]['palette'] = 'element_' . $titleArray[$xftArray['xflextemplate'][$mainKey]['palettes']];
						break;
						case 'name':
							$xftArray['xflextemplate'][$mainKey]['title'] = $value;
							unset($xftArray['xflextemplate'][$mainKey]['name']);
						break;
						case 'type':
							$xftArray['xflextemplate'][$mainKey]['type'] = $value . 'Type';
						break;
						default:
							if(!t3lib_div::inList('name,title,xtype,palette,palettes',$key)){								
								$xftArray['xflextemplate'][$mainKey][$type .'_' . $key] = $value;
								unset($xftArray['xflextemplate'][$mainKey][$key]);
							}					
						break;
						
					}
			}
			$xftArray['xftMain']['xftTitle'] = $row['title'];
			$xftArray['xftMain']['xftDescription'] = $row['description'];
			$xftArray['xftMain']['xftHTML'] = $row['html'];
			$xftArray['xftMain']['xftTyposcript'] = $row['typoscript'];
			$xftArray['xftMain']['uid'] = $row['uid'];
		}		
		return $xftArray;
	}
	
	function delete($uid){
		$deleteArray['deleted'] = 1;
		$GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_xflextemplate_template', 'uid='.$uid, $deleteArray);
	}
	
	function hideToggle($uid){
		$res=$GLOBALS['TYPO3_DB']->exec_SELECTquery('hidden','tx_xflextemplate_template','uid='.$uid);
		$row=$GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		$hidden = $row['hidden'];
		if($row['hidden']){			
			$updateArray['hidden'] = 0;
			$GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_xflextemplate_template', 'uid='.$uid, $updateArray);
			return 'button_unhide|button_hide';
		}
		else{			
			$updateArray['hidden'] = 1;
			$GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_xflextemplate_template', 'uid='.$uid, $updateArray);
			return 'button_hide|button_unhide';
		}
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
		$tmpArray=t3lib_div::xml2tree($row['xml']);
		$tmpArray=$tmpArray['template'][0]['ch']['el'];
		$index=0;
		foreach($tmpArray as $elem){
			foreach($elem['ch'] as $key=>$item){
				$XMLArray[$index][$key]=$item[0]['values'][0];
			}
			$index++;
		}
		$elArray=$XMLArray;
		$i=1;
		foreach($elArray as $value){
			foreach($value as $key=>$item){
				if(t3lib_div::inList('name,palettes,xtype,type',$key))
					$fieldArray[$i][$key]=$item;
				else
					$fieldArray[$i][$value['type'] . 'type_' . $key]=$item;
			}
			$i++;
		}
		//debug($fieldArray);
		$this->file=$row['file'];
		$this->description=$row['description'];
		$this->palettes=$row['palettes'];
		$this->enableGroups=$row['enablegroup'];
		$this->title=$row['title'];
		$this->typoscript=$row['typoscript'];
		$xftArray['xftMain']['title'] = $row['title'];
		$xftArray['xftMain']['description'] = $row['description'];
		$xftArray['xftMain']['enablegroup'] = $row['enablegroup'];
		$xftArray['xftMain']['typoscript'] = $row['typoscript'];
		$xftArray['xftMain']['uid'] = $row['uid'];
		$xftArray['xflextemplate'] = $fieldArray;
		//debug($row['xml'],'XMLdata');
		//debug($xftArray,'XML');
		$this->loaded=true;
		return $xftArray;
	}
	
	
	
}
?>