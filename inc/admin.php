<?php
require("login.php");

if ($CURUSER[id] > 3) die;

function show_letters() {
	foreach(range('A','Z') as $k => $v) {
		print("<option>$v</option>");
	}
}

/* user editing functions */

function showUserList() {
	$res = mysql_query("SELECT * FROM users ORDER BY id DESC");
	print("<ul>");
	while ($tmp = mysql_fetch_assoc($res)) {
		print("<li>#$tmp[id] <a href='javascript:void(0)' onclick='showUserEdit(\"$tmp[id]\")'>$tmp[name]</a> 
		<div class='controlBtns'><div class='ptr delBtn' onclick='delUser(\"$tmp[id]\")'></div></div></li>");
	}
	print("</ul>");
}

function showUserEdit() {
	if (isset($_GET[id])) $id = mysql_real_escape_string($_GET[id]);
	if ($id) $tmp = mysql_fetch_assoc(mysql_query("SELECT * FROM users WHERE id = '$id' LIMIT 1"));
	print("
		E-mail:<br/>
		<input type=text name='email' value='$tmp[email]'><br>
		Full name:<br/>
		<input type=text name='name' value='$tmp[name]'><br>
		Password:<br/>
		<input type=password name='pass'><br/><center>");
	if ($id)
		print("<input type=hidden name='id' value='$tmp[id]'><button class='btnSubmit' onclick='doSaveUser()'>Save</button>");
	else 
		print("<button class='btnSubmit' onclick='doAddUser()'>Add</button>");
		print("</center>");
}

function doAddUser() {
	foreach($_POST as $k => $v)
		$$k = mysql_real_escape_string($v);
	$password = md5(md5($email).md5($pass));	
	mysql_query("INSERT INTO users VALUES(null, '$email','$password','0','$name')") or die("database error");	
	$subject = "Grocerylist Registration";
  $headers = "From: noreply@grocerylist\r\n"."MIME-Version: 1.0\r\n"."Content-type: text/html; charset=iso-8859-1\r\n";
  $body = "Your registered email is $email and password is $pass.";
  mail($email,$subject,$body,$headers);
	print("user has been added");	
}

function doSaveUser() {
	foreach($_POST as $k => $v)
		$$k = mysql_real_escape_string($v);
	if (!empty($pass)) $pass_str = ", password = ".md5(md5($email).md5($pass));
	mysql_query("UPDATE users SET email = '$email', name = '$name' $pass_str WHERE id = '$id'");
	print("user details have been saved");	
}

function delUser() {
	$id = mysql_real_escape_string($_POST[id]);
	mysql_query("DELETE FROM users WHERE id = '$id'") or die("database error");
	print("user has been deleted");
}

/* categories editing functions */

function showCatList() {
	$res = mysql_query("SELECT * FROM categories ORDER BY sort_order ASC");
	print("<ul>");
	$count = mysql_num_rows($res);
	$i = 1;
	while ($tmp = mysql_fetch_assoc($res)) {
		if ($i == 1) $move = "<div class='ptr moveDown moveOne' onclick='moveCatDown(\"$tmp[id]\")'></div>";
		else if ($i == $count ) $move = "<div class='ptr moveUp moveOne' onclick='moveCatUp(\"$tmp[id]\")'></div>";
		else $move = "<div class='ptr moveUp' onclick='moveCatUp(\"$tmp[id]\")'></div><div class='ptr moveDown' onclick='moveCatDown(\"$tmp[id]\")'></div>";		
		print("<li><div class='moveBtns'>$move</div><a href='javascript:void(0)' onclick='showCatEdit(\"$tmp[id]\")'>$tmp[name]</a><div class='controlBtns'>
		<div class='ptr delBtn' onclick='delCat(\"$tmp[id]\")'></div></div></li>");
		$i++;
	}
	print("</ul>");
}

function showCatEdit() {
	if (isset($_GET[id])) $id = mysql_real_escape_string($_GET[id]);
	if ($id) {
		$tmp = mysql_fetch_assoc(mysql_query("SELECT id, name, created FROM categories WHERE id = '$id' LIMIT 1"));
		print("Category #$tmp[id]<br/><br/>");
	}
	print("
		Name:<br/>
		<input type=text name='name' value='$tmp[name]'><br/><center>");
	if ($id)
		print("<input type=hidden name='id' value='$tmp[id]'><button class='btnSubmit' onclick='doSaveCat()'>Save</button>");
	else 
		print("<button class='btnSubmit' onclick='doAddCat()'>Add</button>");
		print("</center>");
}

function doAddCat() {
	$name = mysql_real_escape_string($_POST[name]);
	$created = date("Y-m-d H:i:s");
	$tmp = mysql_fetch_assoc(mysql_query("SELECT sort_order FROM categories ORDER BY sort_order DESC LIMIT 1"));
	if (isset($tmp[sort_order])) $order = $tmp[sort_order] + 1;
	else $order = 0;
	mysql_query("INSERT INTO categories VALUES(null, '$name', '$order', '$created')") or die("database error");
	print("new category added");
}

function doSaveCat() {
	foreach($_POST as $k => $v)
		$$k = mysql_real_escape_string($v);
	mysql_query("UPDATE categories SET name = '$name' WHERE id = '$id'");
	print("category details have been saved");	
}

function delCat() {
	$id = mysql_real_escape_string($_REQUEST[id]);
	mysql_query("DELETE FROM categories WHERE id = '$id'") or die("database error");
	print("category has been deleted");
}

function moveCatUp() {
	$id = mysql_real_escape_string($_REQUEST[id]);
	$tmp = mysql_fetch_assoc(mysql_query("SELECT sort_order FROM categories WHERE id = '$id'"));
	$tmp2 = mysql_fetch_assoc(mysql_query("SELECT id, sort_order FROM categories WHERE sort_order < $tmp[sort_order] ORDER BY sort_order DESC LIMIT 1"));
	mysql_query("UPDATE categories SET sort_order = '$tmp[sort_order]' WHERE id = '$tmp2[id]'");
	mysql_query("UPDATE categories SET sort_order = '$tmp2[sort_order]' WHERE id = '$id'");
}

function moveCatDown() {
	$id = mysql_real_escape_string($_REQUEST[id]);
	$tmp = mysql_fetch_assoc(mysql_query("SELECT sort_order FROM categories WHERE id = '$id'"));
	$tmp2 = mysql_fetch_assoc(mysql_query("SELECT id, sort_order FROM categories WHERE sort_order > $tmp[sort_order] ORDER BY sort_order ASC LIMIT 1"));
	mysql_query("UPDATE categories SET sort_order = '$tmp[sort_order]' WHERE id = '$tmp2[id]'");
	mysql_query("UPDATE categories SET sort_order = '$tmp2[sort_order]' WHERE id = '$id'");
}

/* item editing functions */

function showItemList() {
	$cat = mysql_real_escape_string($_REQUEST[cat]);
	$res = mysql_query("SELECT * FROM items WHERE cat = '$cat' ORDER BY name ASC");
	print("<ul>");
	if (mysql_num_rows($res) == 0) die("<center>No items in this category</center></ul>");
	while ($tmp = mysql_fetch_assoc($res)) {
		print("<li><a href='javascript:void(0)' onclick='showItemEdit(\"$tmp[id]\")'>$tmp[name]</a><div class='controlBtns'>
		<div class='ptr delBtn' onclick='delItem(\"$tmp[id]\")'></div></div></li>");
	}
	print("</ul>");
}

function showItemEdit() {
	if (isset($_GET[id])) $id = mysql_real_escape_string($_GET[id]);
	if ($id) {
		$tmp = mysql_fetch_assoc(mysql_query("SELECT * FROM items WHERE id = '$id' LIMIT 1"));
		print("Item #$tmp[id]<br/><br/>");
	}
	print("<table><tr><td>Category:</td><td><select name='category'>");
	$res = mysql_query("SELECT id, name FROM categories ORDER BY name ASC");
	while ($tmp2 = mysql_fetch_assoc($res)) {
		if ($tmp2[id] == $tmp[cat]) $sel = "selected='selected'";
		else $sel = '';
		print("<option value='$tmp2[id]' $sel>$tmp2[name]</option>");
	}
	print("</select></td></tr>
		<tr><td>Name:</td><td><input type=text name='name' value='$tmp[name]'></td></tr>
		<tr><td>Price:</td><td><input type=text name='price' value='$tmp[price]'></td></tr>
		<tr><td>UPC:</td><td><input type=text name='upc' value='$tmp[upc]'></td></tr></table><center>");
	if ($id)
		print("<input type=hidden name='id' value='$tmp[id]'><button class='btnSubmit' onclick='doSaveItem()'>Save</button>");
	else 
		print("<button class='btnSubmit' onclick='doAddItem()'>Add</button>");
		print("</center>");
}

function show_cat_sort() {
	$res = mysql_query("SELECT id, name FROM categories ORDER BY name ASC");
	while ($tmp = mysql_fetch_assoc($res)) {
		print("<option value='$tmp[id]'>$tmp[name]</option>");
	}
}

function doAddItem() {
	foreach($_POST as $k => $v)
		$$k = mysql_real_escape_string($v);
	$created = date("Y-m-d H:i:s");
	mysql_query("INSERT INTO items VALUES(null, '$name', '$cat', '$price', '$upc', '$created')") or die("database error");
	print("new item added");
}



?>