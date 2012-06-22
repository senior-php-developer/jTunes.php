<?php
include("login.php");

if (!empty($_FILES)) uploadCover();

function uploadCover() {
  $temp_file = $_FILES['cover']['tmp_name'];
  $id = mysql_real_escape_string($_POST['id']);
  $owner = $_COOKIE['user'];
  $file = pathinfo($_FILES['cover']['name']);
  $ext = strtolower($file[extension]);
  $allowed = array('jpg','jpeg','png');
  if (!in_array($ext,$allowed))
  	die("ERR_EXT");
  if (preg_match('/jpg|jpeg/',$ext))
    $img_r=imagecreatefromjpeg($temp_file);
  if (preg_match('/png/',$ext)) 
    $img_r=imagecreatefrompng($temp_file);
  $target = '../upload/'.$owner.'/'.$id.'_f.jpg';
  list($w,$h) = getimagesize($_FILES['cover']['tmp_name']);
  $dst_r = ImageCreateTrueColor($w, $h);
  imagecopy($dst_r,$img_r,0,0,0,0,$w,$h);
  imagejpeg($dst_r,$target,100);
  make_thumb($id);
}

function make_thumb($id) {
  $jpeg_quality = 80;
	$owner = $_COOKIE['user'];	
  $src = '../upload/'.$owner.'/'.$id.'_f.jpg';
  $dst = str_replace($id.'_f', $id.'_t' ,$src);

  list($w, $h) = getimagesize($src);
  if ($w > $h) {
  	$main_side = $h;
  	$src_x = ($w - $h) / 2;
  	$src_y = 0;
  }
  if ($w < $h) {
  	$main_side = $w;
  	$src_x = 0;
  	$src_y = ($h - $w) / 2;
  }
  if ($w == $h) {
  	$main_side = $w;
  	$src_x = 0;
  	$src_y = 0;
  }
  $img_r=imagecreatefromjpeg($src);
  $dst_r = ImageCreateTrueColor(100, 100);
  imagecopyresampled($dst_r,$img_r,0,0,$src_x,$src_y,100,100,$main_side,$main_side);
  imagejpeg($dst_r,$dst,$jpeg_quality);
  print(str_replace('../','',$dst));
}

function saveUpload() {
	foreach($_POST as $k => $v) {
		$$k = mysql_real_escape_string($v);
	}
	mysql_query("UPDATE audio SET artist='$artist', title = '$title', descr = '$descr', activation = '$act', price = '$price', cat = '$cat' WHERE id = '$fileid'");
}

function showUploaded() {
	$user = $_COOKIE[user];
	$res = mysql_query("SELECT id, artist, title, descr, activation, price, (SELECT name FROM categories WHERE id = cat) as cat FROM audio WHERE uploader = '$user'");
	print("<table><tr><th>&nbsp;</th><th>Title</th><th>Description</th><th>Category</th><th style='max-width: 34px;'>Active</th></tr>");
	while ($tmp = mysql_fetch_assoc($res)) {
		print("<tr gid='$tmp[id]'><td><img width='60' src='upload/$user/$tmp[id]_t.jpg'/></td><td width='120'><b>$tmp[artist] </b><br/><br/>$tmp[title]</td><td class='desc-w'>$tmp[descr]</td><td width='80'>$tmp[cat]</td><td align='center'>$tmp[activation]</td><td><img class='ptr' style='margin-bottom: 10px;' title='Edit' src='css/i/edit.png' onclick='editFile(\"$tmp[id]\")'/><br/><img class='ptr' title='Delete' src='css/i/del.png' onclick='delFile(\"$tmp[id]\")'/></td></tr>");
	}
	print("</table>");
}

function editFile() {
	$id = mysql_real_escape_string($_GET['id']);
	$user = mysql_real_escape_string($_COOKIE['user']);
	$tmp = mysql_fetch_assoc(mysql_query("SELECT id, artist, title, descr, activation, price, cat FROM audio WHERE id = '$id' AND uploader = '$user'"));
	$activation = "<select name='act'><option value='1'>1</option><option value='3'>3</option><option value='6'>6</option></select>";
	$category = "<select name='cat'>";
	$res = mysql_query("SELECT id, name FROM categories");
	while ($tm = mysql_fetch_assoc($res)) {
		if ($tmp[cat] == $tm[id]) $c = "selected='selected'"; else $c = '';
		$category .= "<option $c value='$tm[id]'>$tm[name]</option>";
	} $category .= "</select>";
	print("<td class='img-upload'><button style='background: #eee; padding: 3px; -moz-border-radius: 3px; -webkit-border-radius: 3px; border: 1px solid #aaa;'>Browse</button></td><td><input style='width:100px;' type='text' name='artist' value='$tmp[artist]'/><br/><br/><input style='width:100px;'  type='text' name='title' value='$tmp[title]'/></td><td><textarea name='descr' style='height: 70px; width: 200px'>$tmp[descr]</textarea></td><td style='min-width:100px;'>$category</td><td>$activation</td><td><img class='ptr' style='margin-bottom: 10px;' title='Save' src='css/i/save.png' onclick='saveFile(\"$tmp[id]\")'/><br/><img class='ptr' title='Cancel' src='css/i/cancel.png' onclick='cancelSave(\"$tmp[id]\")'/></td>");
}

function showFile() {
	$id = mysql_real_escape_string($_POST['id']);
	$user = $_COOKIE[user];
	$tmp = mysql_fetch_assoc(mysql_query("SELECT id, artist, title, descr, activation, price, (SELECT name FROM categories WHERE id = cat) as cat FROM audio WHERE uploader = '$user' AND id = '$id'"));
	print("<td><img width='60' src='upload/$user/$tmp[id]_t.jpg'/></td><td width='120'><b>$tmp[artist]</b><br/><br/>$tmp[title]</td><td class='desc-w'>$tmp[descr]</td><td width='80'>$tmp[cat]</td><td align='center'>$tmp[activation]</td><td><img class='ptr' style='margin-bottom: 10px;' title='Edit' src='css/i/edit.png' onclick='editFile(\"$tmp[id]\")'/><br/><img class='ptr' title='Delete' src='css/i/del.png' onclick='delFile(\"$tmp[id]\")'/></td>");
}

function delFile() {
	$id = mysql_real_escape_string($_POST['id']);
	$user = $_COOKIE[user];
	$res = mysql_query("SELECT id FROM audio WHERE id = '$id' AND uploader = '$user'");
	if (mysql_num_rows($res) > 0) {
		@unlink("../upload/$user/$id");
		@unlink("../upload/$user/${id}_t.jpg");
		@unlink("../upload/$user/${id}_f.jpg");
		mysql_query("DELETE FROM audio WHERE id = '$id'");
	}
}

function saveFile() {
	foreach($_POST as $k => $v) 
		$$k = mysql_real_escape_string($v);
	$user = $_COOKIE[user];
	mysql_query("UPDATE audio SET artist='$artist', title = '$title', descr = '$descr', activation = '$act', cat = '$cat' WHERE id = '$id' AND uploader = '$user'");
}

?>