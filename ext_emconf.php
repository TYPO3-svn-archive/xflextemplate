<?php

########################################################################
# Extension Manager/Repository config file for ext: "xflextemplate"
#
# Auto generated 08-05-2009 14:27
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'XFlexTemplate',
	'description' => 'general template extension to extend tt_content object in flexible way',
	'category' => 'plugin',
	'author' => 'Federico Bernardin',
	'author_email' => 'federico@bernardin.it',
	'shy' => '',
	'dependencies' => 'cms',
	'conflicts' => '',
	'priority' => '',
	'module' => 'mod1',
	'state' => 'beta',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 1,
	'lockType' => '',
	'author_company' => '',
	'version' => '2.0.0',
	'constraints' => array(
		'depends' => array(
			'cms' => '',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:78:{s:9:"ChangeLog";s:4:"2ab8";s:10:"README.txt";s:4:"ee2d";s:39:"class.tx_xflextemplate_importexport.php";s:4:"9f52";s:21:"ext_conf_template.txt";s:4:"abe8";s:12:"ext_icon.gif";s:4:"0cbf";s:17:"ext_localconf.php";s:4:"4697";s:14:"ext_tables.php";s:4:"da84";s:14:"ext_tables.sql";s:4:"4463";s:16:"locallang_db.xml";s:4:"d30a";s:38:"configuration/elementConfiguration.php";s:4:"82a3";s:29:"configuration/subelement.tmpl";s:4:"9f08";s:14:"doc/manual.sxw";s:4:"d1c8";s:19:"doc/wizard_form.dat";s:4:"e028";s:20:"doc/wizard_form.html";s:4:"54dc";s:41:"hooks/class.tx_xflextemplate_tceforms.php";s:4:"7b58";s:40:"hooks/class.tx_xflextemplate_tcemain.php";s:4:"941e";s:25:"javascript/backEndBLTP.js";s:4:"bf1d";s:25:"javascript/backEndBSTP.js";s:4:"5cd7";s:38:"javascript/jquery/jquery-1.2.6.pack.js";s:4:"7447";s:40:"javascript/jquery/jquery-ui-1.5.3.min.js";s:4:"f1a1";s:36:"javascript/jquery/jquery.bgiframe.js";s:4:"880b";s:35:"javascript/jquery/jquery.blockUI.js";s:4:"a1d1";s:32:"javascript/jquery/jquery.form.js";s:4:"0fe0";s:39:"javascript/jquery/jquery.selectboxes.js";s:4:"0be9";s:32:"javascript/library/class.ajax.js";s:4:"0d7c";s:35:"javascript/library/class.element.js";s:4:"2d25";s:35:"javascript/library/class.general.js";s:4:"1ee7";s:40:"javascript/library/class.templateList.js";s:4:"f41f";s:48:"javascript/library/editor/css/t3editor_inner.css";s:4:"27f9";s:43:"javascript/library/editor/css/xmlcolors.css";s:4:"847a";s:42:"javascript/library/editor/js/codemirror.js";s:4:"c649";s:38:"javascript/library/editor/js/editor.js";s:4:"814b";s:43:"javascript/library/editor/js/mirrorframe.js";s:4:"9944";s:40:"javascript/library/editor/js/parsecss.js";s:4:"0e37";s:46:"javascript/library/editor/js/parsehtmlmixed.js";s:4:"ce7e";s:47:"javascript/library/editor/js/parsejavascript.js";s:4:"f18a";s:43:"javascript/library/editor/js/parsesparql.js";s:4:"f30b";s:47:"javascript/library/editor/js/parsetyposcript.js";s:4:"4808";s:40:"javascript/library/editor/js/parsexml.js";s:4:"a90f";s:38:"javascript/library/editor/js/select.js";s:4:"0530";s:44:"javascript/library/editor/js/stringstream.js";s:4:"c2a6";s:40:"javascript/library/editor/js/tokenize.js";s:4:"c008";s:50:"javascript/library/editor/js/tokenizejavascript.js";s:4:"448d";s:50:"javascript/library/editor/js/tokenizetyposcript.js";s:4:"43b8";s:36:"javascript/library/editor/js/undo.js";s:4:"12f2";s:36:"javascript/library/editor/js/util.js";s:4:"52ee";s:31:"language/locallang_template.xml";s:4:"ded9";s:33:"library/class.elementTemplate.php";s:4:"a6cd";s:30:"library/class.listTemplate.php";s:4:"93e0";s:35:"library/class.tcaTransformation.php";s:4:"10e7";s:49:"library/class.tx_xflextemplate_handletemplate.php";s:4:"e677";s:27:"library/class.xftObject.php";s:4:"e3d9";s:35:"library/class.xmlTransformation.php";s:4:"a1b3";s:13:"mod1/conf.php";s:4:"1129";s:14:"mod1/index.php";s:4:"06a6";s:18:"mod1/locallang.xml";s:4:"bab9";s:22:"mod1/locallang_mod.php";s:4:"84c8";s:19:"mod1/moduleicon.gif";s:4:"0cbf";s:22:"mod1/_notes/dwsync.xml";s:4:"37b7";s:34:"pi1/class.tx_xflextemplate_pi1.php";s:4:"344c";s:42:"pi1/class.tx_xflextemplate_pi1_wizicon.php";s:4:"6eea";s:17:"pi1/locallang.xml";s:4:"bb99";s:15:"pi1/xft_wiz.gif";s:4:"c305";s:19:"pi1/xft_wiz_old.gif";s:4:"7709";s:20:"pi1/static/setup.txt";s:4:"796b";s:19:"res/group_clear.gif";s:4:"3881";s:17:"res/listmanage.js";s:4:"4d53";s:20:"res/css/template.css";s:4:"0314";s:19:"res/css/ui.tabs.css";s:4:"7966";s:21:"res/css/xmlcolors.css";s:4:"9e47";s:28:"res/css/images/falco2009.jpg";s:4:"31fb";s:25:"res/css/images/header.jpg";s:4:"e817";s:29:"res/css/images/loading_24.gif";s:4:"2139";s:28:"res/css/images/plusminus.jpg";s:4:"39d8";s:28:"res/css/images/plusminus.png";s:4:"c8c3";s:28:"res/css/images/tab copia.png";s:4:"83a7";s:22:"res/css/images/tab.png";s:4:"83a7";s:23:"res/css/images/tab1.png";s:4:"095d";}',
	'suggests' => array(
	),
);

?>