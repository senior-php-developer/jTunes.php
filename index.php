<?php
if (empty($_GET['mod'])) $_GET['mod'] = "home";
include("inc/".$_GET['mod'].".php");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<link rel="stylesheet" type="text/css" href="css/reset.css"/>
<link rel="stylesheet" type="text/css" href="css/main.css"/>
<link rel="Shortcut Icon" type="image/x-icon" href="img/favicon.ico" />
<!--[if lt IE 8]><link rel="stylesheet" type="text/css" href="css/ie.css"><![endif]-->
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/default.js"></script>
<title>Audio Sharing</title>
</head>

<body>
<div id="canvas">
	<div id="header">
	<? if (!$CURUSER) print('<div id="loginArea"><table><tr><td>Username</td><td><input type="text" name="login"></td></tr><tr><td>Password</td><td><input type="password" name="pass"></td></tr><tr><td colspan="2" align="right"><span style=\'margin-right: 20px;\' class="lnk" onclick="showReg()">Not registered?</span><button class="btn" onclick="doLogin()">Sign In</button></td></tr></table></div>');
		 else print('<div id="loginArea"><button class="btn rt" onclick="doLogout()" style="margin:30px;">Sign Out</button></div>'); 
		 ?>
	</div>
	<div id="navigation">
		<div class="tab" onclick="loadPage('Home')">Home</div>
	<? if ($CURUSER) print('<div class="tab" onclick="loadPage(\'profile\')">Profile</div><div class="tab" onclick="loadPage(\'music\')">Music</div>'); 
		if ($CURUSER and $CURUSER['class'] < 2) print('<div class="tab" onclick="loadPage(\'upload\')">Upload</div>'); 
		if ($CURUSER and $CURUSER['class'] == 0) print('<div class="tab" onclick="loadPage(\'admin\')">Admin</div>'); 
		if ($CURUSER) print('<div id="searchBox"><input style=\'padding: 2px;\' type="text"> <img onclick="doSearch()" href="javascript:;" src="css/i/search-but.png"></div><div class="clr"></div>');
	?>
	</div>
	<div id="content">
		<? include("tpl/".$_GET['mod'].".tpl"); ?>	
	</div>
</div>

<div id="footer">

</div>

<div id="loginDlg" class="brd hid"></div>
<div id="loader" class="hid"><img src="img/ajax-loader.gif"></div>
<div id="infoDiv" class="bc hid"></div>
</body>
</html>