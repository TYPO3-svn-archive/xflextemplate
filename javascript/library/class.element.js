/**
 * This class contains the code to manage single element of xflextemplate
 * @package xflextemplate
 * @author federico
 * @ver 2.0
 * @state beta
 */

 element = function(initializeParameters){
 	var id;
	var name;
	var type;
	var xtype;
	var renderType;
	var palettes; 
	this.configuration = {
		portletClass: 'portlet',
		portletHeaderClass: 'portlet-header',
		iconDeleteClass: 'ui-icon-delete',
		uiIconClass: 'ui-icon',
		elementPreId: 'element',
		subElementPreId: 'subelement',
		titleClass: 'xftTitle',
		typeClass: 'typeHandler',
		palettesClass: 'xft-palette',
		language: {
			dialogYes: 'Yes',
			dialogCancel: 'Cancel',
		}
	};
	$.extend(this.configuration, initializeParameters);
 }

 element.prototype=
 {
 	add: function(id){
		this.id = id;
		this.addTitleHandler();
		this.addTypeHandler();
	},
		
	addTitleHandler: function(){
		var oThis = this;
		$('#' + oThis.configuration.elementPreId + '_'+oThis.id+'.' + oThis.configuration.portletClass + ' .' + oThis.configuration.titleClass).bind('keyup',function(){
			dataArray = $(this).attr('id').split('_');
			$('#' + oThis.configuration.elementPreId + '_'+oThis.id+'.' + oThis.configuration.portletClass + ' .title').html($(this).val());
		});	
		$('#' + oThis.configuration.elementPreId + '_'+oThis.id+'.' + oThis.configuration.portletClass + ' .' + oThis.configuration.titleClass).bind('blur',function(){
			oThis.changeAllPaletteByChangerID($(this).val());
		});	
	},
	
	addTypeHandler: function(){
		var oThis = this;
		$('#' + oThis.configuration.elementPreId + '_'+oThis.id+'.' + oThis.configuration.portletClass + ' .' + oThis.configuration.typeClass).bind('change',function(){
			var parameters = {
				url: ajaxUrl,
				data: 'ajax=1&action=changeSubElement&subElementType=' + $(this).val() + '&elementID=' + oThis.id
			}
			var ajaxObj = new ajaxClass(parameters);
			var ret = ajaxObj.exec();
			$('#' + oThis.configuration.subElementPreId + '_' + oThis.id).html(ret);
		});
	},
	
	changeAllPaletteByChangerID: function(title){
		var oThis = this;
		$('.' + oThis.configuration.palettesClass).each(function(){
			dataArray = $(this).attr('id').split('_');
			elementId = dataArray[1];
			selectedItem = this.selectedIndex;
			if (elementId!=oThis.id){
				found = 0;
				$('option', $(this)).each(function(){
					if ($(this).val() == ('element'+oThis.id)){
						$(this).html(title);
						found = 1;
					}
				})
				if(!found){
					$(this).addOption('element_' + oThis.id, title);
				}
				this.selectedIndex = selectedItem;
			}
		})
	},
	
	removePaletteOptionByElementDelete: function(){
		var oThis = this;
		$('.' + oThis.configuration.palettesClass).each(function(){
			selectedIndex = this.selectedIndex;
			selectedItem = this.options[this.selectedIndex].value;
			$(this).removeOption(oThis.id);
			if(selectedItem == oThis.id)
				this.selectedIndex = 0
			else
				this.selectedIndex = selectedIndex;
		});
	},
	
	addSortProperties: function(){
		var oThis = this;
		$('#' + oThis.configuration.elementPreId + '_' + oThis.id + '.portlet').addClass('ui-widget ui-widget-content ui-helper-clearfix ui-corner-all')
		.find('.portlet-header')
			.addClass('ui-widget-header ui-corner-all')
			.prepend('<span class="ui-icon ui-icon-plusthick"></span>')
			.end()
		.find('.portlet-content');
	
		$('#' + oThis.configuration.elementPreId + '_' + oThis.id + ' .' + oThis.configuration.portletHeaderClass + ' .' + oThis.configuration.uiIconClass).click(function() {
			$(this).toggleClass('ui-icon-minusthick');
			$(this).toggleClass('ui-icon-plusthick');
			$(this).parents('.portlet:first').find('.portlet-content').toggle();
			dataArray = $(this).parent().parent().attr('id').split('_');
			elementId = dataArray[1];
			if($('#xflextemplate_' + elementId + '_open').val() == '1')
				$('#xflextemplate_' + elementId + '_open').val('0');
			else
				$('#xflextemplate_' + elementId + '_open').val('1');
		});
	},
	
	addDeleteHandler: function(){
		oThis = this;
		$('#' + oThis.configuration.elementPreId + '_'+ this.id +' .portlet-header .ui-icon-delete').bind('click',function() {
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
				console.log(oThis.configuration.language);
				$(this).html(oThis.configuration.language[$(this).html()]);
			});
		});
	}
 }
