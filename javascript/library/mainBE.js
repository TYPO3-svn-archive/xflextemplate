/**
 * @author federico
 * javascript testato su IE e funzionante al 15/4/2009 ore 19:19
 */

//javascript global variables
ajaxUrl = 'http://testplugin/typo3conf/ext/xflextemplate/mod1/index.php';
var languageArray =new Array;
var typoscriptEditor=""; 
var HTMLEditor=""; 

//Main document ready function
$(document).ready(function(){
	//exec tabs
	
	$("#xft-mainblock>ul").tabs({selected: parseInt($('#xftTabSelected').val())});
	$('#xft-mainblock>ul').bind('tabsselect', function(event, ui) {
  		$('#xftTabSelected').val(ui.index);
	});
	
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
		if ($('.xft-main-open',$(this)).val()==1)
			openValue = 0;
		else
			openValue = 1;
		param = {portletClass: 'portlet',create: 0,language:{dialogYes:languageArray['dialogYes'],dialogCancel:languageArray['dialogCancel']}};
		param['open'] = openValue;
		elements[elementId] = new element(param);
        elements[elementId].add(elementId);
	});
	
	//Bind button for new element creation
	$('.xftNewElement').bind('click',function(){
		elementId++;		
        elements[elementId] = new element({portletClass: 'portlet',language:{dialogYes:languageArray['dialogYes'],dialogCancel:languageArray['dialogCancel']}});
        elements[elementId].add(elementId);
    });
	
	
	var optionsForm = { 
	        success:       function(responseText, statusText){
								//alert(responseText);
								$.unblockUI();
								returnArray = responseText.split('|');
								error = returnArray[0];
								if (error == 1){
									//alert(returnArray[1]);
									$('#dialogError .dialogContent').html(returnArray[1]);
									$('#dialogError').dialog({
										bgiframe: true,
										resizable: false,
										height:140,
										modal: true,
										overlay: {
											backgroundColor: '#000',
											opacity: 0.5
										},
										buttons: {
											'dialogOK': function() {
												$(this).dialog('close');
											}
										}
									});
									$('.ui-dialog-buttonpane button').each(function(){
										//alert($(this).html());
										$(this).html(languageArray['dialogOK']);
									});
								}
								//alert(responseText);
							}  // post-submit callback 
	    
    }; 
	
	$('.xftSaveDok').bind('click',function(){
		$('#xftTyposcriptEditor').val(typoscriptEditor.getCode());
		$('#xftHTMLEditor').val(htmlEditor.getCode());
		$.blockUI({message : '<img src="../res/css/images/loading_24.gif"', css : {width: '24px', height: '24px', border: 0, top: '50%', left : '50%', margin: '-15px 0 0 -15px', padding: '5px', background : 'transparent'} });
		$('#xftForm').ajaxSubmit(optionsForm); 
	});
 
    // bind form using 'ajaxForm' 
    //$('#xftForm').ajaxForm(options);
	
	typoscriptEditor = CodeMirror.fromTextArea("xftTyposcriptEditor" , {
	  parserfile: ["tokenizetyposcript.js", "parsetyposcript.js"],
	  path: "/typo3conf/ext/xflextemplate/javascript/library/editor/js/",
	  stylesheet: "/typo3conf/ext/xflextemplate/javascript/library/editor/css/t3editor_inner.css",
    lineNumbers: false,    
	continuousScanning: 500,
    textWrapping: false
	  //textWrapping: false,
	  //lineNumbers: true
	});
	
	htmlEditor = CodeMirror.fromTextArea('xftHTMLEditor', {
    height: "350px",
    parserfile: ["parsexml.js"],
    stylesheet: "/typo3conf/ext/xflextemplate/javascript/library/editor/css/xmlcolors.css",
    path: "/typo3conf/ext/xflextemplate/javascript/library/editor/js/",
    continuousScanning: 500,
    lineNumbers: false,
    textWrapping: false
  });


	
	/*$('.xftSaveDok').bind('click',function(){
		$('#xftTyposcriptEditor').val(editor.getCode());
		$('#xftForm')[0].submit();
	})*/
});