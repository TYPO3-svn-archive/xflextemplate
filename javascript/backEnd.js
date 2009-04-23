/**
 * @author federico
 */

var ajaxUrl = 'http://testplugin/typo3conf/ext/xflextemplate/mod1/index.php';

var languageArray = new Array;

//Main document ready function
$(document).ready(function(){
	var elements = new Array();
	
	//Array for possible dialog button label name
	var languageKeyArray = new Array('dialogYes', 'dialogNo', 'dialogOK', 'dialogCancel','showColumnTips','hiddenColumnTips');
	
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
	
	var list = new templateList;
	list.addOperationHandler();
});