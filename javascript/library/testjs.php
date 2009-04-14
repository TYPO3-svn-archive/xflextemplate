<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Insert title here</title>
<style type="text/css">
	.column {
float:left;
padding-bottom:100px;
width:100%;
}
.portlet {
margin:0 1em 1em;
border: 2px solid black;
}
.portlet-header {
margin:0.3em;
padding-bottom:4px;
padding-left:0.2em;
background-color: #cccccc;
color: #ffffff;
}
.portlet-header .ui-icon {
float:left;
background:  -31px 0px url(../../res/css/images/plusminus.png);
width: 16px;
height: 16px;
}
.portlet-content {
padding:0.4em;
}
.ui-sortable-placeholder {
border:1px dotted black;
height:50px !important;
visibility:visible !important;
}
.ui-sortable-placeholder * {
visibility:hidden;
}
.portlet-header .ui-icon-plusthick{
background-position: -0px 0px;
}
.portlet-header .ui-icon-minusthick{
background-position:  -16px 0px;
}

.ui-sortable-helper{
	border: 2px solid red;
	width: 100%;
}

.subelement input {
	display: block;
}
	
	</style>

<script type="text/javascript" src="../jquery/jquery-1.2.6.pack.js"></script>
<script type="text/javascript" src="../jquery/jquery-ui-1.5.3.min.js"></script>
<script type="text/javascript" src="class.ajax.js"></script>
<script type="text/javascript">
ajaxUrl = 'http://testplugin/typo3conf/ext/xflextemplate/mod1/index.php';
$(document).ready(function(){
	elementId=0;
	$('#testbtn').bind('click',function(){
		//alert('pippo');
		element = 'MAIN_ELEMENT';
		elementId++;
		palette = Array();
		$('.xftTitle').each(function(){
			palette.push($(this).val());
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
	});
	$('.column .portlet').each(function(){
		arrayID = this.id.split('_');
		id = arrayID[1];
		console.log(id);
	});
	
});

function addTitleHandler(id){
	$("#"+id+".portlet .xftTitle").bind('keypress',function(){
		dataArray = $(this).attr('id').split('_');
		elementId = dataArray[1];
		$("#"+id+".portlet .title").html($(this).val());
	});	
}

function addTypeHandler(id){
	$("#"+id+".portlet .typeHandler").bind('change',function(){
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

function addSortProperties(id){
	$("#"+id+".portlet").addClass("ui-widget ui-widget-content ui-helper-clearfix ui-corner-all")
	.find(".portlet-header")
		.addClass("ui-widget-header ui-corner-all")
		.prepend('<span class="ui-icon ui-icon-plusthick"></span>')
		.end()
	.find(".portlet-content");

$("#"+id+" .portlet-header .ui-icon").click(function() {
	$(this).toggleClass("ui-icon-minusthick");
	$(this).toggleClass("ui-icon-plusthick");
	$(this).parents(".portlet:first").find(".portlet-content").toggle();
});
}

</script>
</head>
<body>
<?php 
	echo('pippo');
	require_once('../../library/class.elementTemplate.php');
	echo('pippo');
	$template = t3lib_div::makeInstance('elementTemplate');
	$template->init(PATH_typo3conf .'ext/xflextemplate/configuration/subelement.tmpl');
	if(count($_POST['xflextemplate'])){
		foreach ($_POST['xflextemplate'] as $key => $item){
			foreach ($item as $subKey => $value)
				$elementArray[$subKey] = $value;
			print_r($template->getSubElementValueArray(substr($elementArray['type'],0,strlen($elementArray['type'])-5), $elementArray));
		}
	}
	//print_r($_POST);
?>
<form action="" method="post">
<input type="submit" id="test1btn" value="esegui" />
<input type="button" id="testbtn" value="test ajax" />
<div class="column">
</div>
</form>
</body>
</html>