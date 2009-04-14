<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2006 Federico Bernardin <federico@bernardin.it>
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
 *   59: class tx_xflextemplate_pi1 extends tslib_pibase
 *   78:     function main($content,$conf)
 *  107:     function init()
 *  143:     function getTemplateString($templateName)
 *  158:     function putContent($templateString)
 *  357:     function getPhotogallery($conf,$field)
 *  425:     function pageBrowse($suffix,$totalPage,$conf)
 *
 * TOTAL FUNCTIONS: 6
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */


require_once(PATH_tslib.'class.tslib_pibase.php');
require_once(PATH_t3lib.'class.t3lib_tsparser.php');
require_once (PATH_site."/typo3conf/ext/xflextemplate/library/class.xmlTransformation.php");

/**
 * Plugin 'xtemplate' for the 'xtemplate' extension.
 *
 * @package typo3
 * @subpackage xflextemplate
 * @author	Federico Bernardin <federico@bernardin.it>
 * @version 1.0.0
 */
class tx_xflextemplate_pi1 extends tslib_pibase {
	var $prefixId = 'tx_xflextemplate_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_xtemplate_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey = 'xflextemplate';	// The extension key.
	var $pi_checkCHash = TRUE;
	//define stabdard variable used inside the code
	var $conf=array();
	var $imageUploadDir='uploads/pics/'; //directory for image repository
	var $mediaUploadDir='uploads/pics/';  //directory for media repository
	var $fileUploadDir='uploads/pics/';  //directory for file repository
	var $photogalleryStdCols=4;  // standard value for photogallery columns if none is choosen
	var $xflexData=array(); //Contain flexdata element from xflextemplate column

	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		The PlugIn content
	 * @param	array		The PlugIn configuration
	 * @return	string		The	content that is displayed on the website
	 */
	function main($content,$conf)	{

		$this->conf=$conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		//call init function to extract and recollect data
		$this->init();
		//if not static plugin is loaded generate an error, in the static template is defined many standard variable, the plugin could generate error without it
		if ($this->conf['installed']){
			if(strlen($this->template)>0){
				$content=$this->putContent($this->template);
			}
			else{
				$content=$this->pi_getLL('emptyTemplate','Template is not found or it\'s empty');
			}
		}
		else
			$content=$this->pi_getLL('noStaticTyposcriptLoaded','no static template is loaded!');
		return $content;
	}




	/**
	 * initializes the flexform and all config options
	 *
	 * @return	void		void
	 */
	function init(){
		$this->pi_initPIflexForm(); // Init and get the flexform data of the plugin
		//merge lConf with standard tt_content column, so in $this->cObj->data there are all data from xflextemplate
		$this->cObj->data=array_merge($this->cObj->data,tx_xft_div::getArrayFromXMLData($this->cObj->data['xflextemplate']));
		//fetch all other data from template
		$res=$GLOBALS['TYPO3_DB']->exec_SELECTquery('xml,file,typoscript','tx_xflextemplate_template','title="'.$this->cObj->data['xtemplate'].'" AND deleted=0 AND hidden=0');
		$dbrow=$GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		$this->typoscript=$dbrow['typoscript'];
		$ts=t3lib_div::makeInstance('t3lib_TSparser');
		$ts->parse($this->typoscript);
		//$ts contains all typoscript from xflextemplate
		$xml=str_replace("''","'",$dbrow['xml']);
		//create correct element data from xml in the xflextemplate
		$xmlArray=xmlTransformation::getArrayFromXML($xml);
		if(is_array($xmlArray)){
			foreach($xmlArray as $xElemet){
				$this->xflexData[$xElemet['name']]=$xElemet;
			}
		}
		//assign typoscript
		$this->typoscript=$ts->setup;
		$this->template=$this->cObj->TEMPLATE($this->typoscript['templateFile.']);
		if($this->template){
			$this->template=$this->cObj->getSubpart($this->template,  '###'.strtoupper($this->cObj->data['xtemplate']).'###');
		}
		$this->template=($this->template)?$this->template:$this->getTemplateString($dbrow['file']);//retrieve file data, only if is not defined in template
		//Updating conf array with typoscript
		$this->conf=t3lib_div::array_merge_recursive_overrule($this->conf,$ts->setup);
	}

	/**
	 * Retrieve the content of template, the name of template block will be retrieved from $this->cObj->data['xtemplate'] (xtemplate tt_content column)
	 *
	 * @param	string		name of xml part of the file
	 * @return	string		The content of the template otherwise if template doesn't exist return empty string
	 */
	function getTemplateString($templateName) {
		if ($templateName) { //fetch template string !DOCUMENT!
			$templateString=$this->cObj->fileResource($templateName);
			$template=$this->cObj->getSubpart($templateString, '###'.strtoupper($this->cObj->data['xtemplate']).'###');
			return $template;
		}
		return '';
	}

	/**
	 * Creation of content based on type of element
	 *
	 * @param	string		template content string
	 * @return	string		The	content to put on page
	 */
	function putContent($templateString) {
		//cycle from any content element of the content data
		$hookObjectsArr = array();
		if (is_array ($TYPO3_CONF_VARS['SC_OPTIONS']['typo3conf/ext/xflextemplate/class.tx_xflextemplate_pi1.php']['processContent']))	{
			foreach ($TYPO3_CONF_VARS['SC_OPTIONS']['typo3conf/ext/xflextemplate/class.tx_xflextemplate_pi1.php']['processContent'] as $classRef)	{
				$hookObjectsArr[] = &t3lib_div::getUserObj($classRef);
			}
		}
		//create object directly from typoscript without analyzing single object
		if ($this->conf['GCO']){
			$this->markerArray['###GCO###']=$this->cObj->CObjGet($this->typoscript);
		}
		//analyze single object and define data
		else {
			foreach ($this->xflexData as $key=>$xftitem) {
				//eseguo questa associazione per fare in modo che modifiche successive al template non implichino la visualizzazione di campi non riempiti
				$item=$this->cObj->data[$key];
				if ($item) {
					if(is_array($this->xflexData[$key])){
						$this->cObj->LOAD_REGISTER(array($key.'xft'=>$this->cObj->data[$key]),'');
						//analyze type of content
						//type allowed are image,multimedia,text, any other is print as is
						switch ($this->xflexData[$key]['xtype']) {
						//manage image object
							case 'image'://OK
							$imageArray=array();
							//debug($this->conf[$key.'.']);
								//check multiple image property
								$confImage=($this->conf[$key.'.'])?$this->conf[$key.'.']:array();
								//create conf array for configuration array of image object for cObj class
								$conf['image.']=($this->conf['image.'])?$this->conf['image.']:array();
								$conf['image.']['uploadDir']=($conf['image.']['uploadDir'])?$conf['image.']['uploadDir']:($this->xflexData[$key]['uploadFolder'])?$this->xflexData[$key]['uploadFolder']:$this->imageUploadDir;
								//merge data from static and xflex typoscript
								$confImage=t3lib_div::array_merge_recursive_overrule($conf['image.'],$confImage);
								//define if use gallery
								if ($this->conf[$key.'.']['photogallery']==1) {
									$this->markerArray['###'.strtoupper($key).'###']=$this->getPhotogallery($confImage,$key);
								}
								else { //no photogallery
									//extract all image
									$imageList=explode(',',$this->cObj->data[$key]);
									$caption=$this->cObj->stdWrap($confImage['caption'],$confImage['caption.']);
									if($confImage['captionSplit']){
										$captionArray=explode($confImage['captionSplit.']['separator'],$caption);
										$confCaption=$confImage['captionSplit.'];
									}
									if($confImage['linkwrapper']==1)
										$imageWrapper=explode(',',$this->cObj->stdWrap($confImage['linkwrapper'],$confImage['linkwrapper.']));
									for($imageCount=0;$imageCount<count($imageList);$imageCount++){
										$confImage['file']=$confImage['uploadDir'].$imageList[$imageCount];
										if($confImage['linkwrapper']==1 && isset($imageWrapper[$imageCount])){
											//$confImage['stdWrap']=1;
											$confImage['stdWrap.']['typolink.']['parameter']=$imageWrapper[$imageCount];
										}
										//create image tag
										$confCaption['value']=($captionArray[$imageCount])?$captionArray[$imageCount]:'';
										$captionValue='';
										$captionValue=$this->cObj->TEXT($confCaption);
										$imageTag=$this->cObj->IMAGE($confImage);
										if($confImage['captionBefore']){
											$imageArray[]=$this->cObj->stdWrap($captionValue.$imageTag,$confImage['imageAllWrap.']);
										}
										else{
											$imageArray[]=$this->cObj->stdWrap($imageTag.$captionValue,$confImage['imageAllWrap.']);
										}
									}
									//create and merge data in template marker
									if($confImage['single']==1){
										foreach($imageArray as $keyImg=>$elem){
											$confCaption['value']=($captionArray[$imageCount])?$captionArray[$imageCount]:'';
											$captionValue='';
											$captionValue=$this->cObj->TEXT($confCaption);
											$imageTag=$this->cObj->IMAGE($confImage);
											if($confImage['captionBefore']){
												$elem=$this->cObj->stdWrap($captionValue.$elem,$confImage['imageAllWrap.']);
											}
											else{
												$elem=$this->cObj->stdWrap($elem.$captionValue,$confImage['imageAllWrap.']);
											}
											$this->markerArray['###'.strtoupper($key).$keyImg.'###']=$elem;
										}
									}
									else{
										$this->markerArray['###'.strtoupper($key).'###']=implode('',$imageArray);
									}

								}
								//}fine getobject
							break;
						//manage multimedia object
							case 'multimedia':
								$multimediaList=explode(',',$this->cObj->data[$key]);
								foreach ($multimediaList as $multimediaItem) {
									$paramString='';
									if($multimediaItem){
										$confMultimedia=($this->conf[$key.'.'])?$this->conf[$key.'.']:array();
										$conf['multimedia.']=($this->conf['multimedia.'])?$this->conf['multimedia.']:array();
										$conf['multimedia.']['uploadDir']=($conf['multimedia.']['uploadDir'])?$conf['multimedia.']['uploadDir']:($this->xflexData[$key]['uploadFolder'])?$this->xflexData[$key]['uploadFolder']:$this->mediaUploadDir;
									//merge data from static and xflex typoscript
										$confMultimedia=t3lib_div::array_merge_recursive_overrule($conf['multimedia.'],$confMultimedia);
										$fileArray=explode('.',$multimediaItem);
										$ext=(count($fileArray)>0)?$fileArray[count($fileArray)-1]:'';
										$confMultimedia['file']=$confMultimedia['uploadDir'].$multimediaItem;

										//add parameter at the end of the filename
										if ($confMultimedia['paramAfterFile']){
											$arrParams=array();
											$arrParams=explode(',',$confMultimedia['paramAfterFile.']['params']);
											foreach($arrParams as $item){
												$paramString.=(t3lib_div::_GP($item))?(($confMultimedia['paramAfterFile.']['params.'][$item])?'&'.$confMultimedia['paramAfterFile.']['params.'][$item].'='.t3lib_div::_GP($item):'&'.$item.'='.t3lib_div::_GP($item)):'';
											}
											$paramString=($paramString)?'?'.substr($paramString,1):'';
										}
										if(!$paramString && $confMultimedia['paramAfterFile.']['default']){
											$paramString=(is_array($confMultimedia['paramAfterFile.']['default.']))?'?'.$this->cObj->stdWrap($confMultimedia['paramAfterFile.']['default.']['param'],$confMultimedia['paramAfterFile.']['default.']['param.']):'';
										}
										//apply all params in params field of MULTIMEDIA cObj object
										/*
										* width			int			width of multimedia object
										* height		int			height of multimedia object
										* bgcolor		string		backgroundcolor of multimedia object
										* loop			string		loop (true/false) in multimedia object
										* controller	string		controller (true/false) for multimedia object
										* autostart		string		autostart (true/false) in multimedia object
										* plugin		string		url for plugin of multimedia object
										*/
										if (strtolower($ext)=='swf' && $confMultimedia['jsFlash']==1){
											$GLOBALS['TSFE']->additionalHeaderData['js_flashscript']='<script type="text/javascript" src="/typo3conf/ext/xflextemplate/jsFlash.js"></script>';
											$divID=md5(mktime(date('h'),date('i'),date('s'),date('y'),date('m'),date('d')));
											$width=($confMultimedia['width'] || is_array($confMultimedia['width.']))?$this->cObj->stdWrap($confMultimedia['width'],$confMultimedia['width.']):(($confMultimedia['param.']['width'])?$confMultimedia['param.']['width']:'');
											$height=($confMultimedia['height'] || is_array($confMultimedia['height.']))?$this->cObj->stdWrap($confMultimedia['height'],$confMultimedia['height.']):(($confMultimedia['param.']['height'])?$confMultimedia['param.']['height']:'');
											$text=($this->cObj->stdWrap($confMultimedia['jsFlash.']['alternativeText'],$confMultimedia['jsFlash.']['alternativeText.']))?$this->cObj->stdWrap($confMultimedia['jsFlash.']['alternativeText'],$confMultimedia['jsFlash.']['alternativeText.']):$this->cObj->stdWrap($confMultimedia['flashText'],$confMultimedia['flashText.']);
											$bgcolor=($confMultimedia['bgcolor'] || is_array($confMultimedia['bgcolor.']))?$this->cObj->stdWrap($confMultimedia['bgcolor'],$confMultimedia['bgcolor.']):'';
											$multimediaArray[]='<span id="'.$divID.'">
												'.$text.'
											</span>
											<script type="text/javascript">
											var fo = new FlashObject("'.$confMultimedia['file'].$paramString.'", "'.$divID.'", "'.$width.'", "'.$height.'", "6,0,29,0", "'.$bgcolor.'");
											fo.write("'.$divID.'");
											</script>';
										}
										else{
											$confMultimedia['params'].=($confMultimedia['width'] || is_array($confMultimedia['width.']))?'width='.$this->cObj->stdWrap($confMultimedia['width'],$confMultimedia['width.']).chr(10).chr(13):(($confMultimedia['param.']['width'])?'width='.$confMultimedia['param.']['width'].chr(10).chr(13):'');
											$confMultimedia['params'].=($confMultimedia['height'] || is_array($confMultimedia['height.']))?'height='.$this->cObj->stdWrap($confMultimedia['height'],$confMultimedia['height.']).chr(10).chr(13):(($confMultimedia['param.']['height'])?'height='.$confMultimedia['param.']['height'].chr(10).chr(13):'');
											$confMultimedia['params'].=($confMultimedia['bgcolor'] || is_array($confMultimedia['bgcolor.']))?'bgcolor='.$this->cObj->stdWrap($confMultimedia['bgcolor'],$confMultimedia['bgcolor.']).chr(10).chr(13):'';
											$confMultimedia['params'].=($confMultimedia['loop'] || is_array($confMultimedia['loop.']))?'loop='.$this->cObj->stdWrap($confMultimedia['loop'],$confMultimedia['loop.']).chr(10).chr(13):'';
											$confMultimedia['params'].=($confMultimedia['controller'] || is_array($confMultimedia['controller.']))?'controller='.$this->cObj->stdWrap($confMultimedia['controller'],$confMultimedia['controller.']).chr(10).chr(13):'';
											$confMultimedia['params'].=($confMultimedia['autostart'] || is_array($confMultimedia['autostart.']))?'autostart='.$this->cObj->stdWrap($confMultimedia['autostart'],$confMultimedia['autostart.']).chr(10).chr(13):'';
											$confMultimedia['params'].=($confMultimedia['plugin'] || is_array($confMultimedia['plugin.']))?'plugin='.$this->cObj->stdWrap($confMultimedia['plugin'],$confMultimedia['plugin.']).chr(10).chr(13):'';
											$multimediaArray[]=($paramString)?str_replace($confMultimedia['file'],$confMultimedia['file'].$paramString,$this->cObj->MULTIMEDIA($confMultimedia)):$this->cObj->MULTIMEDIA($confMultimedia);
										}
									}
								}
								//merge all multimedia object in the marker of template
								$this->markerArray['###'.strtoupper($key).'###']=(is_array($multimediaArray))?implode('',$multimediaArray):'';
							break;
							//manage file object
							case 'file':
								$fileArray=array();
								//debug($this->conf[$key.'.']);
								//check multiple image property
								$confFile=($this->conf[$key.'.'])?$this->conf[$key.'.']:array();
								//create conf array for configuration array of image object for cObj class
								$conf['file.']=($this->conf['file.'])?$this->conf['file.']:array();
								$conf['file.']['uploadDir']=($conf['file.']['uploadDir'])?$conf['file.']['uploadDir']:($this->xflexData[$key]['uploadFolder']?$this->xflexData[$key]['uploadFolder']:$this->imageUploadDir);
								$fileList=explode(',',$this->cObj->data[$key]);
								//merge data from static and xflex typoscript
								$confFile=t3lib_div::array_merge_recursive_overrule($conf['file.'],$confFile);
								for($fileCount=0;$fileCount<count($fileList);$fileCount++){
									$confFile['file']=$confFile['uploadDir'].$fileList[$fileCount];																	//create image tag	
									$textFile=($confFile['fileText']||$confFile['fileText.'])?$this->cObj->stdWrap($confFile['fileText'],$confFile['fileText.']):$fileList[$fileCount];
									$confFile['value']=$textFile;
									$confFile['stdWrap.']['typolink.']['parameter']=$confFile['file'];
									$fileTag=$this->cObj->TEXT($confFile);									
									$fileArray[]=$this->cObj->stdWrap($fileTag,$confFile['fileAllWrap.']);
								}	
								$this->markerArray['###'.strtoupper($key).'###']=implode('',$fileArray);														break;
							//manage bodytext object
							case 'text':
								$confText=($this->conf[$key.'.'])?$this->conf[$key.'.']:array();
								$conf['text.']=($this->conf['text.'])?$this->conf['text.']:array();
								//merge data from static and xflex typoscript
								$confText=t3lib_div::array_merge_recursive_overrule($conf['text.'],$confText);
								$confText['value']=$this->cObj->data[$key];
								$this->markerArray['###'.strtoupper($key).'###']=$this->cObj->TEXT($confText);
							break;
							//manage cObject object
							case 'cObject':
								$this->markerArray['###'.strtoupper($key).'###']=$this->cObj->cObjGetSingle($this->conf[$key],$this->conf[$key.'.']);
							break;
							//if the type is not defined print as is
							default:
									// Hook: getMainFields_preProcess (requested by Thomas Hempel for use with the "dynaflex" extension)
								foreach ($hookObjectsArr as $hookObj)	{
									if (method_exists($hookObj,'processContent_preProcess'))	{
										$hookObj->getMainFields_preProcess($this,$key,$item);
									}
								}
								$this->markerArray['###'.strtoupper($key).'###']=htmlentities($item);
							break;
						}
					}
				}//if $item is not empty
				else{//if $item is  empty
					$this->markerArray['###'.strtoupper($key).'###']='';
				}
			}
		}
		$this->markerArray['###CONTENTUID###']=$this->cObj->data['uid'];
		//merge all marker in the output content object
		$content=$this->cObj->substituteMarkerArray($templateString,$this->markerArray,'',1);
		$this->markerArray=array();
		return $content;
	}

	/**
	 * Function to create photogallery
	 *
	 * @param	array		configuration array
	 * @param	string		name of object in $this->cObj->data array
	 * @return	string		html block with photogallery
	 */
	function getPhotogallery($conf,$field){
		$imageArray=explode(',',$this->cObj->data[$field]);
		if (count($imageArray)){
			$totalImagesinPage=count($imageArray);
			//define all variables for table creation
			$pageStartPhoto=0;
			$pageEndPhoto=$conf['photogallery.']['paging.']['photoXPage'];
			if ($conf['photogallery.']['paging']){
				$conf['photogallery.']['paging.']['photoXPage']=($conf['photogallery.']['paging.']['photoXPage'])?$conf['photogallery.']['paging.']['photoXPage']:count($imageArray);
				$page=($this->piVars['photogalleryPage'])?$this->piVars['photogalleryPage']-1:0;
				$pageStartPhoto=$page*$conf['photogallery.']['paging.']['photoXPage'];
				$pageEndPhoto=$pageStartPhoto+$conf['photogallery.']['paging.']['photoXPage'];
				$totalPages=ceil(count($imageArray)/$conf['photogallery.']['paging.']['photoXPage']);
				$totalImagesinPage=$conf['photogallery.']['paging.']['photoXPage'];
			}
			$tableCols=($conf['photogallery.']['cols'] || $conf['photogallery.']['cols.'])?($this->cObj->stdWrap($conf['photogallery.']['cols'],$conf['photogallery.']['cols.']))?$this->cObj->stdWrap($conf['photogallery.']['cols'],$conf['photogallery.']['cols.']):$this->photogalleryStdCols:$this->photogalleryStdCols;
			$tableRows=($conf['photogallery.']['rows'] || $conf['photogallery.']['rows.'])?($this->cObj->stdWrap($conf['photogallery.']['rows'],$conf['photogallery.']['rows.']))?$this->cObj->stdWrap($conf['photogallery.']['rows'],$conf['photogallery.']['rows.']):ceil($totalImagesinPage/$tableCols):ceil($totalImagesinPage/$tableCols);
			$tableRows=($tableRows)?$tableRows:ceil($totalImagesinPage/$tableCols);
			$tableColClass=$conf['photogallery.']['colClass'];
			$tableRowClass=$conf['photogallery.']['rowClass'];
			$tableClass=$conf['photogallery.']['tableClass'];
			/*$singleImageWrap=$conf['photogallery.']['singleImageWrap'];
			$captionSingle=$conf['photogallery.']['captionSingle'];
			$captionField=$conf['photogallery.']['captionField'];*/
			for($row=0;$row<$tableRows;$row++){
				for($col=0;$col<$tableCols;$col++){
					if($imageArray[($row*$tableCols)+$col+$pageStartPhoto] && (($row*$tableCols)+$col+$pageStartPhoto)<$pageEndPhoto ){
						$image='';
						$caption='';

						$conf['file']=$conf['uploadDir'].$imageArray[($row*$tableCols)+$col+$pageStartPhoto];
						$conf['imageLinkWrap.']['typolink']['parameter']=($conf['imageLinkWrap.']['typolink']['parameter'] || $conf['imageLinkWrap.']['typolink']['parameter.'])?$conf['imageLinkWrap.']['typolink']['parameter']:$conf['file'];
						$image=$this->cObj->IMAGE($conf);
						//$caption=$this->cObj->stdWrap($conf['caption'],$conf['caption.']);
						//if caption is a $conf['captionSplit.']['separator'] separator field
						$caption=$this->cObj->stdWrap($conf['caption'],$conf['caption.']);
						if($conf['captionSplit']){
							$captionArray=explode($conf['captionSplit.']['separator'],$caption);
							$confCaption=$conf['captionSplit.'];
							$confCaption['value']=(is_array($captionArray))?$captionArray[($row*$tableCols)+$col+$pageStartPhoto]:$caption;
						}
						else
						{
							$confCaption['value']=$caption;
						}
						$caption=$this->cObj->TEXT($confCaption);
					}
					else
						$image='&nbsp;';
					$colContent.='<td'.($tableColClass?' class="'.$tableColClass.'" ':'').'>'.$image.$caption.'</td>'."\n";
					$caption='';
				}
				$content.=($colContent)?'<tr'.($tableRowClass?' class="'.$tableRowClass.'" ':'').'>'.$colContent.'</tr>'."\n":'';
				$colContent='';
			}
			$content=($content)?'<table'.($tableClass?' class="'.$tableClass.'" ':'').'>'.$content.'</table>':'';
		}
		return $content.$this->pageBrowse('photogallery',$totalPages,$conf['photogallery.']['paging.']);
	}

	/**
	 * [Describe function...]
	 *
	 * @param	[type]		$suffix: ...
	 * @param	[type]		$totalPage: ...
	 * @param	[type]		$conf: ...
	 * @return	[type]		...
	 */
	function pageBrowse($suffix,$totalPage,$conf){
		if ($totalPage>1){
			$currentPage=($this->piVars[$suffix.'Page'])?$this->piVars[$suffix.'Page']:1;
			$template=$this->cObj->TEMPLATE($conf['template.']);
			$markerArray=array();
			for($i=1;$i<$totalPage+1;$i++){
				if ($i==$currentPage){
					if($conf['actLink']){
						$this->piVars=array();
						$this->piVars[$suffix.'Page']=$i;
						$tempConf=$conf['curPageWrap.'];
						$tempConf['value']=$i;
						$markerArray['###PAGELIST###'].=$this->cObj->StdWrap($this->pi_linkTP_keepPIvars($this->cObj->TEXT($tempConf),array(),0,0,$GLOBALS['TSFE']->id),$tempConf['allACTWrap']);
					}
					else{
						$this->piVars=array();
						$this->piVars[$suffix.'Page']=$i;
						$tempConf=$conf['curPageWrap.'];
						$tempConf['value']=$i;
						$markerArray['###PAGELIST###'].=$this->cObj->StdWrap($this->cObj->TEXT($tempConf),$tempConf['allACTWrap']);
					}
				}
				else{//not current page
					$this->piVars=array();
					$this->piVars[$suffix.'Page']=$i;
					$tempConf=$conf['stdPageWrap.'];
					$tempConf['value']=$i;
					$markerArray['###PAGELIST###'].=$this->cObj->StdWrap($this->pi_linkTP_keepPIvars($this->cObj->TEXT($tempConf),array(),0,0,$GLOBALS['TSFE']->id),$tempConf['allNOWrap']);
				}
			}//end for cycle
			if($currentPage>1){
				$this->piVars=array();
				$this->piVars[$suffix.'Page']=1;
				$markerArray['###PAGEFIRST###']=$this->cObj->StdWrap($this->pi_linkTP_keepPIvars($this->pi_getLL('pagebrowse.first'),array(),0,0,$GLOBALS['TSFE']->id),$tempConf['movWrap']);
				$this->piVars=array();
				$this->piVars[$suffix.'Page']=$currentPage-1;
				$markerArray['###PAGEPREVIOUS###']=$this->cObj->StdWrap($this->pi_linkTP_keepPIvars($this->pi_getLL('pagebrowse.previous'),array(),0,0,$GLOBALS['TSFE']->id),$tempConf['movWrap']);
			}
			else{
				$markerArray['###PAGEFIRST###']='';
				$markerArray['###PAGEPREVIOUS###']='';
			}
			if ($currentPage<$totalPage){
				$this->piVars=array();
				$this->piVars[$suffix.'Page']=$totalPage;
				$markerArray['###PAGELAST###']=$this->cObj->StdWrap($this->pi_linkTP_keepPIvars($this->pi_getLL('pagebrowse.last'),array(),0,0,$GLOBALS['TSFE']->id),$tempConf['movWrap']);
				$this->piVars=array();
				$this->piVars[$suffix.'Page']=$currentPage+1;
				$markerArray['###PAGENEXT###']=$this->cObj->StdWrap($this->pi_linkTP_keepPIvars($this->pi_getLL('pagebrowse.next'),array(),0,0,$GLOBALS['TSFE']->id),$tempConf['movWrap']);
			}
			else{
				$markerArray['###PAGELAST###']='';
				$markerArray['###PAGENEXT###']='';
			}
			$content=$this->cObj->substituteMarkerArray($template,$markerArray,'',1);
		}//end if page browser is visible
		return $content;
	}

}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/xflextemplate/pi1/class.tx_xflextemplate_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/xflextemplate/pi1/class.tx_xflextemplate_pi1.php']);
}

?>