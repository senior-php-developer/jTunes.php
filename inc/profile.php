<?php
include("login.php");

function showProfile() {
	$user = mysql_real_escape_string($_REQUEST[user]);
	$tmp = mysql_fetch_assoc(mysql_query("SELECT * FROM users WHERE id = '$user'"));
	$dob = showDob();
	if ($tmp['class'] == '0') $class = 'Administrator';
	if ($tmp['class'] == '1') $class = 'Provider';
	if ($tmp['class'] == '2') $class = 'Consumer';
	print("<h4>Editing profile</h4><hr noshade='noshade'>");
	print("<table>
		<tr><td width=100>Username:</td><td width=200>$tmp[login]</td><td>Class:</td><td>$class</td></tr>
		<tr><td>Real name:</td><td><input type='text' name='real' value='$tmp[realname]'></td><td>E-mail:</td><td>$tmp[email]</td></tr>
		<tr><td>Country:</td><td> <input type='text' name='country' value='$tmp[country]'></td><td>Date of Birth:</td><td>$dob</td></tr>
		<tr><td>Phone #:</td><td> <input type='text' name='phone' value='$tmp[phone]'></td></tr>
		<tr><td colspan=4 align=center><button class='btn' onclick='saveProfile()'>Save</button></td></tr>
	");
}

function saveProfile() {
	foreach($_POST as $k => $v) 
		$$k = mysql_real_escape_string($v);
	$dob = $dobY.'-'.$dobM.'-'.$dobD;
	$id = $_REQUEST[user];
	mysql_query("UPDATE users SET realname = '$real', country = '$country', dob = '$dob', phone = '$phone' WHERE id = '$id'");
	print("profile saved");
}

function showPass() {
	print("<h4>Changing password</h4><hr noshade='noshade'>");
	print("<table>
		<tr><td width=130>Login:</td><td><input type='text' name='login'></td><td>Old password:</td><td> <input type='password' name='oldpass'></td></tr>
		<tr><td>New password:</td><td> <input type='password' name='newpass'></td><td>Confirm password:</td><td> <input type='password' name='newpass2'></td></tr>
		<tr><td colspan=4 align=center><button class='btn' onclick='savePass()'>Change</button></td></tr>
	");
}

function savePass() {
	foreach($_POST as $k => $v) 
		$$k = mysql_real_escape_string($v);
	$passhash = md5(md5($login).md5($oldpass));
	$user = $_REQUEST[user];
	$res = mysql_query("SELECT id FROM users WHERE id = '$user' AND password = '$passhash'");
	if (mysql_num_rows($res) > 0) {
		$tmp = mysql_fetch_assoc($res);
		$passhash = md5(md5($login).md5($newpass));
		mysql_query("UPDATE users SET password = '$passhash' WHERE id = '$tmp[id]'");
		print("password changed");
	} else 
		print("password incorrect");
}

function showPurchase() {
	$user = $_COOKIE['user'];
	print("<table><tr class='headings'><th>Genre</th><th>Artist</th><th>Title</th><th>Price (\$)</th><th>Active</th><th>Uploader</th><th>Expires</th></tr>");
	$res = mysql_query("SELECT au.id, au.end_date, au.active, a.artist, a.title, a.price, a.activation, u.login, c.name as cat FROM audio_users au, audio a, users u, categories c WHERE au.user_id = '$user' AND au.audio_id = a.id AND a.uploader = u.id AND c.id = a.cat ORDER BY id DESC");
	while ($tmp = mysql_fetch_assoc($res)) {
		if ($tmp[active]) $act = "class='active'"; else $act = "";
		print("<tr $act><td>$tmp[cat]</td><td>$tmp[artist]</td><td>$tmp[title]</td><td>$tmp[price]</td><td>$tmp[activation]</td><td>$tmp[login]</td><td>$tmp[end_date]</td></tr>");
	}
	print("</table>");
}

?>