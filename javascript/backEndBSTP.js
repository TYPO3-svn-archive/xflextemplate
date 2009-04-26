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
 * Main JS File for Backend Single Templating Programming (BSTP)
 * 
 * @author Federico Bernardin <federico@bernardin.it>
 * @version 2.0
 * @package TYPO3
 * @subpackage xfletemplate
 */


//javascript global variables
var languageArray =new Array;
var typoscriptEditor=""; 
var HTMLEditor=""; 
var ajaxUrl;


ajaxUrl = URL_xft + 'index.php';

//Main document ready function
$(document).ready(function(){
	//exec tabs
	$("#xft-mainblock>ul").tabs();
	
	//reset element counter
	elementId=0;
	
	//elements array containing element objects
	var elements = new Array();
	
	//Array for possible dialog button label name
	var languageKeyArray = new Array('dialogYes', 'dialogNo', 'dialogOK', 'dialogCancel');
	
	//retrieve labels for translation by ajax calls
	parameters = {
		url: ajaxUrl
	}
	$(languageKeyArray).each(function(i,j){
		parameters.data = 'ajax=1&action=getLL&key=' + j;
		ajaxObj = new ajaxClass(parameters);
		ret = ajaxObj.exec();
		languageArray[j] = ret;
	});
	
	//Bind sortable to element column
	$(".column").sortable({
		connectWith: ['.column'],
		handle: '.portlet-header',
		placeholder: '.ui-sortable-placeholder'
	});
	
	//Update and add (creation) element from html page
	$('.portlet').each(function(){
		dataArray = $(this).attr('id').split('_');
		elementId = dataArray[1];
		param = {portletClass: 'portlet',create: 0,language:{dialogYes:languageArray['dialogYes'],dialogCancel:languageArray['dialogCancel']}};
		elements[elementId] = new element(param);
        elements[elementId].add(elementId);
	});
	
	//Bind button for new element creation
	$('.xftNewElement').bind('click',function(){
		elementId++;		
        elements[elementId] = new element({portletClass: 'portlet',language:{dialogYes:languageArray['dialogYes'],dialogCancel:languageArray['dialogCancel']}});
        elements[elementId].add(elementId);
    });
	
	// after submission of form
	var optionsForm = { 
		success: function(responseText, statusText){
			//unblock frame on window
			$.unblockUI();
			returnArray = responseText.split('|');
			error = returnArray[0];
			if (error == 1) {
				$('#dialogError .dialogContent').html(returnArray[1]);
				$('#dialogError').dialog({
					bgiframe: true,
					resizable: false,
					height: 140,
					modal: true,
					overlay: {
						backgroundColor: '#000',
						opacity: 0.5
					},
					buttons: {
						'dialogOK': function(){
							$(this).dialog('close');
						}
					}
				});
				//apply label to button
				$('.ui-dialog-buttonpane button').each(function(){
					$(this).html(languageArray['dialogOK']);
				});
			}
		}	    
    }; 
	
	//before submit code
	$('.xftSaveDok').bind('click',function(){
		//save typoscript editor content to textarea
		$('#xftTyposcriptEditor').val(typoscriptEditor.getCode());
		//save HTML editor content to textarea
		$('#xftHTMLEditor').val(htmlEditor.getCode());
		//show block frame during submitting form
		$.blockUI({message : '<img src="../res/css/images/loading_24.gif"', css : {width: '24px', height: '24px', border: 0, top: '50%', left : '50%', margin: '-15px 0 0 -15px', padding: '5px', background : 'transparent'} });
		//submit form
		$('#xftForm').ajaxSubmit(optionsForm); 
	});
	
	//Typoscript editor
	typoscriptEditor = CodeMirror.fromTextArea("xftTyposcriptEditor" , {
		parserfile: ["tokenizetyposcript.js", "parsetyposcript.js"],
		path: "/typo3conf/ext/xflextemplate/javascript/library/editor/js/",
		stylesheet: "/typo3conf/ext/xflextemplate/javascript/library/editor/css/t3editor_inner.css",
    	lineNumbers: false,    
		continuousScanning: 500,
    	textWrapping: false
	});
	
	//HTML Editor
	htmlEditor = CodeMirror.fromTextArea('xftHTMLEditor', {
	    height: "350px",
	    parserfile: ["parsexml.js"],
	    stylesheet: "/typo3conf/ext/xflextemplate/javascript/library/editor/css/xmlcolors.css",
	    path: "/typo3conf/ext/xflextemplate/javascript/library/editor/js/",
	    continuousScanning: 500,
	    lineNumbers: false,
	    textWrapping: false
  	});
});