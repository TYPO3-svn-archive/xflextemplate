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
<link rel="stylesheet" type="text/css" src="../../res/css/template.css">
<script type="text/javascript" src="../jquery/jquery-1.2.6.pack.js"></script>
<script type="text/javascript" src="../jquery/jquery-ui-1.5.3.min.js"></script>
<script type="text/javascript" src="class.ajax.js"></script>
<script type="text/javascript">
ajaxUrl = 'http://testplugin/typo3conf/ext/xflextemplate/mod1/index.php';
$(document).ready(function(){
	$('#dialogError').dialog();
});
</script>
</head>
<body>
<form action="" method="post">
<input type="submit" id="test1btn" value="esegui" />
<input type="button" id="testbtn" value="test ajax" />
<div class="column">
</div>
			<div id="dialogError" style="" title="jhjhhkhkjhkjhkjhkjh">
				<p class="dialogContainer"><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span><div class="dialogContent">sdfasdfasdafsa</div></p>
			</div>
</form>
</body>
</html>