/**
 * @author federico
 */

 
templateList = function(){
	
}

templateList.prototype = {
	
	addOperationHandler: function(){
		$('.tableOperationIcon').each(function(){
			$(this).bind('click',function(){
				img = this;
				operationArray = this.id.split('-');
				if (operationArray[0] == 'edit' || operationArray[0] == 'new'){
					document.location.href = 'index.php?templateId=' + operationArray[1] + '&action=' + operationArray[0];
				}
				else{
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
