<?php
require("db.php");
require_once('recaptchalib.php');
error_reporting(E_ALL  & ~E_NOTICE);
session_start();

checkLogin();
if (isset($_GET['do'])) call_user_func($_GET['do']);

function generator() {
   list($usec, $sec) = explode(' ', microtime());
   srand((float) $sec + ((float) $usec * 100000));
   $validchars = "0123456789abcdfghjkmnpqrstvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
   $password  = "";
   $counter   = 0;
   while ($counter < 8) {
     $actChar = substr($validchars, rand(0, strlen($validchars)-1), 1);
     if (!strstr($password, $actChar)) {
        $password .= $actChar;
        $counter++;
     }
   }
   return $password;
}

function showDob() {
	$dob = "<select name='dobD'>";
	for($i = 1; $i<32; $i++)
		$dob.="<option>$i</option>";
	$dob.= "</select><select name='dobM'>";
	for($i = 1; $i<13; $i++)
		$dob.="<option>$i</option>";
	$dob.= "</select><select name='dobY'>";
	for($i = 1991; $i>1952; $i--)
		$dob.="<option>$i</option>";
	$dob.="</select>";
	return $dob;
}

function showReg() {
	$dob = showDob();
	print("
	<h4>Please Sign Up:</h4><br/> 
	Login:<br/><input type=text name='login' /><br/>
	Password:<br/><input type=password name='pass' /><br/>
	E-mail:<br/><input type=text name='email' /><br/>
	Class:<br/><select name='class'><option value='1'>Provider</option><option value='2'>Customer</option></select><br/>
	Full name:<br/><input type=text name='name' /><br/>
	Country:<br/><input type=text name='country' /><br/>
	Date of Birth:<br/>$dob<br/><br/>
	<button class='btn rt' id=doRegBtn onclick='doReg()'>Register</button><br>
	<br/><center><a href='javascript:;' onclick='showReset()'>Forgot password?</a></center>
	<a href='javascript:void(0)' onclick='$(\"#loginDlg\").fadeOut()' style='position: absolute; top: 3px; right: 5px;'>[x]</a>
	<div id='captchaDiv'></div>");
}

function showReset() {
	print("
	<h4>Provide details:</h4><br/> 
	E-mail:<br/><input type=text name='email' /><br/>
	<h6><center>check email for confirmation code</center></h6>
	Code:<br/><input type=text name='code' /><br/>
	Password:<br/><input type=password name='pass' /><br><br/>
	<button class='btn lt' id=getCodeBtn onclick='getCode()'>Get Code</button>
	<button class='btn rt hid' id=doResetBtn onclick='doReset()'>Change</button><br>
	<a href='javascript:void(0)' onclick='$(\"#loginDlg\").fadeOut()' style='position: absolute; top: 3px; right: 5px;'>[x]</a>");
}

function doReg() {
	foreach ($_POST as $key => $value) {
    $$key = mysql_real_escape_string($value);
  } 
  $privatekey = "6LeeUwkAAAAAANIF8AmuYSAzfLCXhyWx2OruiQlp";
  $resp = recaptcha_check_answer($privatekey, $_SERVER["REMOTE_ADDR"],$recaptcha_challenge_field,$recaptcha_response_field);
	if (!$resp->is_valid) die("captcha entered incorrectly");
	$passhash = md5(md5($login).md5($pass));
	$dob = $dobY.'-'.$dobM.'-'.$dobD;
  mysql_query("INSERT INTO users VALUES (null, '$login', '$passhash', '$email', '$class', '$name', '$country', '$dob', '', '0')") or die("database error");
  $id = mysql_insert_id();
  // writing email
  $subject = "Audioshare Registration";
  $headers = "From: noreply@audioshare\r\n"."MIME-Version: 1.0\r\n"."Content-type: text/html; charset=iso-8859-1\r\n";
  $body = "Your registered email is $email, login is $login and password is $pass.";
  mail($email,$subject,$body,$headers);
	// adding cookies
	setcookie("user", $id, 0x7fffffff, "/");
  setcookie("pass", $passhash, 0x7fffffff, "/");
	print("REG_OK");  	
}

function doLogin() {
	$login = mysql_real_escape_string($_POST[login]);
  $pass = mysql_real_escape_string($_POST[pass]);
  $password = md5(md5($login).md5($pass));
  $res = mysql_query("SELECT id FROM users WHERE login = '$login' AND password = '$password' LIMIT 1");  
  if (mysql_num_rows($res) > 0) {
  	$tmp = mysql_fetch_assoc($res);
    setcookie("user", $tmp[id], 0x7fffffff, "/");
    setcookie("pass", $password, 0x7fffffff, "/");
    print("LOGIN_OK");
  } else 
			print("password incorrect");
}

function doLogout() {
	setcookie("user", "", 0x7fffffff, "/");
  setcookie("pass", "", 0x7fffffff, "/");
}

function getCode() {
	$email = mysql_real_escape_string($_POST[email]);
	$res = mysql_query("SELECT id FROM users WHERE email = '$email'");
	if (mysql_num_rows($res) == 0) die("wrong code provided");
	$confirmstr = generator();
	mysql_query("UPDATE users SET code = '$confirmstr' WHERE email = '$email'");
	$subject = "Grocerylist Password Reset";
  $headers = "From: noreply@grocerylist\r\n"."MIME-Version: 1.0\r\n"."Content-type: text/html; charset=iso-8859-1\r\n";
  $body = "Your confirmation code for resetting password is $confirmstr";
  if(mail($email,$subject,$body,$headers))
  	print("SENT_OK");
  else print("error occured");
}

function doReset() {
	foreach($_POST as $key => $val)
		$$key = mysql_real_escape_string($val);
	$res = mysql_query("SELECT id, login FROM users WHERE email = '$email' AND code = '$code' LIMIT 1");
	if (mysql_num_rows($res) > 0)	{
		$tmp = mysql_fetch_assoc($res);
		$password = md5(md5($tmp[login]).md5($pass)); 
		mysql_query("UPDATE users SET password = '$password' WHERE id = ".$tmp[id]);
		setcookie("user", $tmp[id], 0x7fffffff, "/");
    setcookie("pass", $password, 0x7fffffff, "/");
		print("RESET_OK");
	} else
		print("incorrect confirmation code");
}

function checkLogin() {
	$user = $_COOKIE[user];
  $password = $_COOKIE[pass];
  $res = mysql_query("SELECT * FROM users WHERE id = '$user' AND password = '$password'");
  if (mysql_num_rows($res) > 0) {
    $GLOBALS[CURUSER] = mysql_fetch_assoc($res);
  }
}




?>