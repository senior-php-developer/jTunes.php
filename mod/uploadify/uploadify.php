<?php
require("../../inc/login.php");

if (!empty($_FILES)) {
	$uploader = mysql_real_escape_string($_POST['userid']);
	$filename = $_FILES['Filedata']['name'];
  $temp_file = $_FILES['Filedata']['tmp_name'];
  $info = pathinfo($filename);
	mysql_query("INSERT INTO audio VALUES(null, '$uploader', '$info[extension]', '','','','','0','0','0')");
	$id = mysql_insert_id();
	$target = '../../upload/'.$uploader.'/'.$id;
  @mkdir(dirname($target), 0755, true);
  @move_uploaded_file($temp_file,$target);	
	
	$res = mysql_query("SELECT id, name FROM categories ORDER BY name ASC");
	$catlist = "<select name='cat'>";
	while ($tmp = mysql_fetch_assoc($res)) {
		$catlist .= "<option value='$tmp[id]'>$tmp[name]</option>";
	}
	$catlist .= "</select>";
	$activation = "<select name='act'><option value='1'>1 month</option><option value='3'>3 months</option><option value='6'>6 months</option></select>";	
	print("<div class='file-edit'><input type='hidden' name='fileid' value='$id'/><table>
		<tr><td>Artist</td><td><input type='text' name='artist'/></td><td rowspan='5' style='vertical-align: top;'>Description<br/><textarea name='descr'></textarea></td><td rowspan='5'><button style='margin-left: 15px;' gid='$id' class='btn' onclick='uploadCover(\"$id\")'>Upload Image</button><br/><div gid='$id' class='cover'></div></td></tr>
		<tr><td>Title</td><td><input type='text' name='title' value='$info[filename]'/></td></tr>
		<tr><td>Category</td><td>$catlist</td></tr>
		<tr><td>Price</td><td><input type='text' name='price'/></td></tr>
		<tr><td>Activation</td><td>$activation</td></tr>");
}
?>