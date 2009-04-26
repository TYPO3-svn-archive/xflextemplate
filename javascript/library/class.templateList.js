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
 * Templatelist class for managing list of xFlexTemplate
 * 
 * @author Federico Bernardin <federico@bernardin.it>
 * @version 2.0
 * @package TYPO3
 * @subpackage xfletemplate
 */


//main class
templateList = function(){
	
}


//list of method
templateList.prototype = {
	
	/**
	 * This function add click events to button image icons of table
	 */
	addOperationHandler: function(){
		$('.tableOperationIcon').each(function(){
			$(this).bind('click',function(){
				img = this;
				operationArray = this.id.split('-');
				//if operation is edit or new, redirect to index.php with element id for editing or NEW for new element
				if (operationArray[0] == 'edit' || operationArray[0] == 'new'){
					document.location.href = 'index.php?templateId=' + operationArray[1] + '&action=' + operationArray[0];
				}
				else{
					//delete operation
					if (operationArray[0] == 'dele'){
						$('#dialog').dialog({
							bgiframe: true,
							resizable: false,
							height:140,
							modal: true,
							overlay: {
								backgroundColor: '#000',
								opacity: 0.5
							},
							buttons: {
								'dialogYes': function() {
									var parameters = {
										url: ajaxUrl,
										data: 'ajax=1&action=' + operationArray[0] + '&templateId=' + operationArray[1]
									};
									var ajaxObj = new ajaxClass(parameters);
									var ret = ajaxObj.exec();
									//console.log($(img).parents().parents().html());
									//$('#xftRow' + operationArray[1]).remove();
									$(img).parent().parent().remove();
									$(this).dialog('close');
									//alert($(img).parents());
								},
								'dialogCancel': function() {
									$(this).dialog('close');
								}
							}
						});
						$('.ui-dialog-buttonpane button').each(function(){
							$(this).html(languageArray[$(this).html()]);
						});
					}
					//hide/show code
					else{
						var parameters = {
							url: ajaxUrl,
							data: 'ajax=1&action=' + operationArray[0] + '&templateId=' + operationArray[1]
						};
						var ajaxObj = new ajaxClass(parameters);
						var ret = ajaxObj.exec();
						hideArray = ret.split('|');
						$(img).attr('src',$(img).attr('src').replace(hideArray[0],hideArray[1]));
						if(hideArray[1] == 'button_unhide')
							$(img).attr('title',languageArray['showColumnTips']);
						else
							$(img).attr('title',languageArray['hiddenColumnTips']);
					}
				}
			});
		});
	}
	
}
