/**
 * @author federico
 * javascript testato su IE e funzionante al 15/4/2009 ore 19:19
 */

//javascript global variables
ajaxUrl = 'http://testplugin/typo3conf/ext/xflextemplate/mod1/index.php';
var languageArray =new Array;

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
	
	$('.xftSaveDok').bind('click',function(){
		$('#xftTyposcriptEditor').val(editor.getCode());
		$('#xftForm')[0].submit();
	})
});