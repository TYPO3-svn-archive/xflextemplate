ajaxUrl = 'http://testplugin/typo3conf/ext/xflextemplate/mod1/index1.php';
var languageArray =new Array;
$(document).ready(function(){
	elementId=0;
	var languageKeyArray = new Array('dialogYes', 'dialogNo', 'dialogOK', 'dialogCancel');
	parameters = {
			url: ajaxUrl
		}
	$(languageKeyArray).each(function(i,j){
		parameters.data = 'ajax=1&action=getLL&key=' + j;
		ajaxObj = new ajaxClass(parameters);
		ret = ajaxObj.exec();
		languageArray[j] = ret;
	});
	$('#testbtn').bind('click',function(){
		//alert('pippo');
		element = 'inputType';
		elementId++;
		palette = Array();
		$('.xftTitle').each(function(){
			dataArray = $(this).attr('id').split('_');
			id = dataArray[1];
			if($(this).val())
				palette.push($(this).val()+'_'+id);
		});
		palette = palette.join('|');
		parameters = {
			url: ajaxUrl,
			data: 'ajax=1&action=newElement&subElement=' + element + '&elementID=' + elementId + '&palette=' + palette,
			success: function(msg){
				//alert(msg);
			}
		}
		ajaxObj = new ajaxClass(parameters);
		ret = ajaxObj.exec();
		$('.column').append(ret);
		addSortProperties(elementId);
		addTypeHandler(elementId);
		addTitleHandler(elementId);
		addDeleteHandler(elementId)
	});
	$(".column").sortable({
		connectWith: ['.column']
	});

	$(".portlet").addClass("ui-widget ui-widget-content ui-helper-clearfix ui-corner-all")
		.find(".portlet-header")
			.addClass("ui-widget-header ui-corner-all")
			.prepend('<span class="ui-icon ui-icon-plusthick"></span>')
			.end()
		.find(".portlet-content");

	$(".portlet-header .ui-icon").click(function() {
		$(this).toggleClass("ui-icon-minusthick");
		$(this).toggleClass("ui-icon-plusthick");
		$(this).parents(".portlet:first").find(".portlet-content").toggle();
		dataArray = $(this).parent().parent().attr('id').split('_');
		elementId = dataArray[1];
		console.log(elementId + '--->' + $('#xflextemplate_' + elementId + '_open').val());
		if($('#xflextemplate_' + elementId + '_open').val() == '1')
			$('#xflextemplate_' + elementId + '_open').val('0');
		else
			$('#xflextemplate_' + elementId + '_open').val('1');
	});
	$(".portlet-header .ui-icon").each(function(){
		dataArray = $(this).parent().parent().attr('id').split('_');
		elementId = dataArray[1];
		if($('#xflextemplate_' + elementId + '_open').val() == '1'){
			$(this).toggleClass("ui-icon-minusthick");
			$(this).toggleClass("ui-icon-plusthick");
			$(this).parents(".portlet:first").find(".portlet-content").toggle();			
		}
	})
	
	$(".portlet-header .ui-icon-delete").bind('click',function() {
		oThis = this;
		yesButton = languageArray['dialogYes'];
		cancelButton = languageArray['dialogCancel'];
		$("#dialog").dialog({
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
					removePaletteOptionByElementDelete($(oThis).parent().parent().attr('id'));
					$(oThis).parent().parent().remove();
					$(this).dialog('close');
				},
				'dialogCancel': function() {
					$(this).dialog('close');
				}
			}
		});
		$('.ui-dialog-buttonpane button').each(function(){
			$(this).html(languageArray[$(this).html()]);
		})

		
		
	});
	$('.column .portlet').each(function(){
		arrayID = this.id.split('_');
		id = arrayID[1];
		elementId++;
		addTypeHandler(id);
		addTitleHandler(id);
	});
	
});


//TODO: creare un event handler object


function addTitleHandler(id){
	$("#element_"+id+".portlet .xftTitle").bind('keyup',function(){
		dataArray = $(this).attr('id').split('_');
		elementId = dataArray[1];
		$("#element_"+id+".portlet .title").html($(this).val());
	});	
	$("#element_"+id+".portlet .xftTitle").bind('blur',function(){
		dataArray = $(this).attr('id').split('_');
		elementId = dataArray[1];
		changeAllPaletteByChangerID(id,$(this).val());
	});	
}

function addTypeHandler(id){
	$("#element_"+id+".portlet .typeHandler").bind('change',function(){
		dataArray = $(this).attr('id').split('_');
		elementId = dataArray[1];
		parameters = {
			url: ajaxUrl,
			data: 'ajax=1&action=changeSubElement&subElementType=' + $(this).val() + '&elementID=' + elementId,
			success: function(msg){
				//alert(msg);
			}
		}
		ajaxObj = new ajaxClass(parameters);
		ret = ajaxObj.exec();
		$('#subelement_' + elementId).html(ret);
	});
}

function changeAllPaletteByChangerID(id,title){
	$('.xft-palette').each(function(){
		dataArray = $(this).attr('id').split('_');
		elementId = dataArray[1];
		selectedItem = this.selectedIndex;
		if (elementId!=id){
			found = 0;
			$('option', $(this)).each(function(){
				if ($(this).val() == ('element'+id)){
					$(this).html(title);
					found = 1;
				}
			})
			if(!found){
				$(this).addOption('element_' + id, title);
			}
			this.selectedIndex = selectedItem;
		}
	})
}

function addDeleteHandler(id){
	$('#element_'+ id +' .portlet-header .ui-icon-delete').bind('click',function() {
		oThis = this;
		yesButton = languageArray['dialogYes'];
		cancelButton = languageArray['dialogCancel'];
		$("#dialog").dialog({
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
					$(oThis).parent().parent().remove();
					removePaletteOptionByElementDelete($(oThis).parent().parent().attr('id'));
					$(this).dialog('close');
				},
				'dialogCancel': function() {
					$(this).dialog('close');
				}
			}
		});
		$('.ui-dialog-buttonpane button').each(function(){
			$(this).html(languageArray[$(this).html()]);
		});
	});
}

function removePaletteOptionByElementDelete(id){
	$('.xft-palette').each(function(){
		selectedIndex = this.selectedIndex;
		selectedItem = this.options[this.selectedIndex].value;
		$(this).removeOption(id);
		if(selectedItem == id)
			this.selectedIndex = 0
		else
			this.selectedIndex = selectedIndex;
	})
}

function addSortProperties(id){
	$("#element_"+id+".portlet").addClass("ui-widget ui-widget-content ui-helper-clearfix ui-corner-all")
	.find(".portlet-header")
		.addClass("ui-widget-header ui-corner-all")
		.prepend('<span class="ui-icon ui-icon-plusthick"></span>')
		.end()
	.find(".portlet-content");

	$("#element_"+id+" .portlet-header .ui-icon").click(function() {
		$(this).toggleClass("ui-icon-minusthick");
		$(this).toggleClass("ui-icon-plusthick");
		$(this).parents(".portlet:first").find(".portlet-content").toggle();
		dataArray = $(this).parent().parent().attr('id').split('_');
		elementId = dataArray[1];
		console.log(elementId + '--->' + $('#xflextemplate_' + elementId + '_open').val());
		if($('#xflextemplate_' + elementId + '_open').val() == '1')
			$('#xflextemplate_' + elementId + '_open').val('0');
		else
			$('#xflextemplate_' + elementId + '_open').val('1');
	});
}