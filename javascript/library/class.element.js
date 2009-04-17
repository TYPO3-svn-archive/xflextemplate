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
		columnClass: 'column',
		iconDeleteClass: 'ui-icon-delete',
		uiIconClass: 'ui-icon',
		elementPreId: 'element',
		subElementPreId: 'subelement',
		titleClass: 'xftTitle',
		typeClass: 'typeHandler',
		palettesClass: 'xft-palette',
		language: {
			dialogYes: 'Yes',
			dialogCancel: 'Cancel'
		},
		defaultElementName: 'inputType',
		create: 1,
		open: 1
	};
	$.extend(this.configuration, initializeParameters);
 }

 element.prototype=
 {
 	add: function(id){
		var oThis = this;
		this.id = id;
		if (oThis.configuration.create) {
			palette = Array();
			$(' .' + oThis.configuration.titleClass).each(function(){
				if ($(this).val()) 
					palette.push($(this).val() + '_' + id);
			});
			palette = palette.join('|');
			parameters = {
				url: ajaxUrl,
				data: 'ajax=1&action=newElement&subElement=' + oThis.configuration.defaultElementName + '&elementID=' + id + '&palette=' + palette
			}
			ajaxObj = new ajaxClass(parameters);
			ret = ajaxObj.exec();
			$('.' + oThis.configuration.columnClass).append(ret);
		}
		this.addTitleHandler();
		this.addTypeHandler();
        this.addSortProperties();
        this.addDeleteHandler();
	},
		
	addTitleHandler: function(){
		var oThis = this;
		$('#' + oThis.configuration.elementPreId + '_'+oThis.id+'.' + oThis.configuration.portletClass + ' .' + oThis.configuration.titleClass).bind('keyup',function(){
			dataArray = $(this).attr('id').split('_');
			$('#' + oThis.configuration.elementPreId + '_'+oThis.id+'.' + oThis.configuration.portletClass + ' .title').html(htmlentities($(this).val()));
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
			actualElementID = 'element_' + oThis.id;
			$(this).removeOption(actualElementID);
			if(selectedItem == actualElementID)
				this.selectedIndex = 0
			else
				this.selectedIndex = selectedIndex;
		});
	},
	
	addSortProperties: function(){
		var oThis = this;
		if (oThis.configuration.open == 1) {
			className = 'ui-icon-minusthick';
		}
		else {
			className = 'ui-icon-plusthick';
			$('#' + oThis.configuration.elementPreId + '_' + oThis.id + '.portlet').find('.portlet-content').toggle();
		}
		$('#' + oThis.configuration.elementPreId + '_' + oThis.id + '.portlet').addClass('ui-widget ui-widget-content ui-helper-clearfix ui-corner-all')
		.find('.portlet-header')
			.addClass('ui-widget-header ui-corner-all')
			.prepend('<span class="ui-icon ' + className + '"></span>')
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
						oThis.removePaletteOptionByElementDelete();
						$('#' + oThis.configuration.elementPreId + '_' + oThis.id).remove();
						$(this).dialog('close');
					},
					'dialogCancel': function() {
						$(this).dialog('close');
					}
				}
			});
			$('.ui-dialog-buttonpane button').each(function(){
				//alert($(this).html());
				$(this).html(oThis.configuration.language[$(this).html()]);
			});
		});
	}
 }
