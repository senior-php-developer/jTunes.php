<?php
require("inc/admin.php");

if (!$CURUSER || $CURUSER[id] > 3) die;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<meta http-equiv="Content-Language" content="en"/>
<meta http-equiv="Cache-Control" content="no-cache">
<meta http-equiv="Pragma" content="no-cache">
<!-- css stylesheets -->
<link rel="stylesheet" type="text/css" href="css/reset.css"/>
<link rel="stylesheet" type="text/css" href="css/main.css"/>
<link rel="stylesheet" type="text/css" href="css/admin.css"/>
<!--[if lt IE 8]>
<link rel="stylesheet" type="text/css" href="css/ie.css">
<![endif]-->
<title>Groceries List Administration</title>
<script type="text/javascript">

</script>
</head>
<body>
<div id="canvas">
	<div id="header">
		<div id="goBackBtn" class="ptr btnImg"><span>Go Back</span></div>
	</div>
	<div id="content">
		<div id="usersMg" class="contentDiv brd">
		  <h3>Users</h3>
			<div id="userEdit" class="section"></div>
			<input type="text" id="userSortInp"><select><?=show_letters();?></select>
			<div id="userList" class="section"></div>
			<center><a href="javascript:void(0)" onclick="showUserList()">Add User</a></center>
		</div>
		<div class="contentDiv brd">
			<h3>Categories</h3>
			<div id="catEdit" class="section"></div>
			<div id="catList" class="section"></div>
			<center><a href="javascript:void(0)" onclick="showCatList()">Add Category</a></center>
		</div>
		<div class="contentDiv brd">
			<h3>Items</h3>
			<div id="itemEdit" class="section"></div>
			<select id="itemSortSel"><?show_cat_sort();?></select>
			<div id="itemList" class="section"></div>
			<center><a href="javascript:void(0)" onclick="showItemList()">Add Items</a></center>
		</div>
		<div class="clr"></div>
	</div>
	<div id="footer">
	
	</div>
</div>
<!-- dialogs -->
<div id="loader" class="hid"><img src="img/ajax-loader.gif"></div>
<div id="infoDiv" class="bc hid"></div>

<!-- javascript -->
<script type="text/javascript" src="js/jquery/jquery.js"></script>
<script type="text/javascript" src="js/default.js"></script>
<script type="text/javascript" src="js/login.js"></script>
<script type="text/javascript" src="js/admin.js"></script>

</body>
</html>