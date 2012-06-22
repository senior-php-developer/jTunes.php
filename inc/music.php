<?php
include("login.php");

$activation = "<span style='margin-left: 270px;'>Activation: </span><select style='background: #C6E4FF' id='act' onchange='changeAct()'><option value='0'>Any</option><option value='1'>1 month</option><option value='3'>3 months</option><option value='6'>6 months</option></select>";
$categories = "<span style='margin-left: 15px;'>Genre: </span><select id='cat' onchange='changeCat()' style='background: #C6E4FF'><option>All</option>";
$res = mysql_query("SELECT id, name FROM categories");
while ($tmp = mysql_fetch_assoc($res)) {
	$categories .= "<option>$tmp[name]</option>";
} $categories .= "</select>";

function showFiles() {
	$user = $_COOKIE[user];
	$res = mysql_query("SELECT id, uploader, artist, title, descr, activation, price, (SELECT name FROM categories WHERE id = cat) as cat FROM audio WHERE id NOT IN (SELECT audio_id FROM audio_users WHERE user_id = '$user' AND active = '1') LIMIT 10");
	print("<table>");
	$res2 = mysql_query("SELECT id, uploader, artist, title, descr, activation, price, (SELECT name FROM categories WHERE id = cat) as cat FROM audio WHERE id IN (SELECT audio_id FROM audio_users WHERE user_id = '$user' AND active = '1') LIMIT 1");
	if (mysql_num_rows($res2) > 0) {
		$tmp2 = mysql_fetch_assoc($res2);
		print("<tr class='applied-file' gid='$tmp2[id]'><td><img width='60' src='upload/$tmp2[uploader]/$tmp2[id]_t.jpg'/></td><td width='120'><b>$tmp2[artist]</b><br/><br/>$tmp2[title]</td><td class='desc-w'>$tmp2[descr]</td><td width='80'>$tmp2[cat]</td><td align='center' width='40'>$tmp2[activation] </td><td><img class='ptr' style='margin-bottom: 10px;' title='Cancel' src='css/i/cancel.png' onclick='cancelFile(\"$tmp2[id]\")'/></td></tr>");	
		$apply = false;
	} else
		$apply = true;
	while ($tmp = mysql_fetch_assoc($res)) {
		$apply ? $a = "<img class='ptr' style='margin-bottom: 10px;' title='Purchase' src='css/i/arrdn.png' onclick='applyFile(\"$tmp[id]\")'/>" : $a = "&nbsp;";
		print("<tr gid='$tmp[id]'><td><img width='60' src='upload/$tmp[uploader]/$tmp[id]_t.jpg'/></td><td width='120'><b>$tmp[artist]</b><br/><br/>$tmp[title]</td><td class='desc-w'>$tmp[descr]</td><td width='80'>$tmp[cat]</td><td align='center' width='40'>$tmp[activation]</td><td>$a</td></tr>");
	}
	print("</table>");
}

function showArtists() {
	$res = mysql_query("SELECT artist, COUNT(artist) as freq FROM audio GROUP BY artist ORDER BY freq DESC");
	print("<li onclick='loadAll()' title='Load all artists'>All</li>");
	while ($tmp = mysql_fetch_assoc($res)) {
		print("<li freq='$tmp[freq]' onclick='loadArtist(\"$tmp[artist]\")' title='$tmp[freq] track(s)'>$tmp[artist]</li> ");
	}
}

function loadArtist() {
	$art = mysql_real_escape_string($_POST['artist']);
	$user = $_COOKIE['user'];
	$res = mysql_query("SELECT id, uploader, artist, title, descr, activation, price, (SELECT name FROM categories WHERE id = cat) as cat FROM audio WHERE artist = '$art' LIMIT 10");
	print("<table>");	
	$res2 = mysql_query("SELECT id, uploader, artist, title, descr, activation, price, (SELECT name FROM categories WHERE id = cat) as cat FROM audio WHERE id IN (SELECT audio_id FROM audio_users WHERE user_id = '$user' AND active = '1') LIMIT 1");
	if (mysql_num_rows($res2) > 0) {
		$apply = false;
	} else
		$apply = true;
	while ($tmp = mysql_fetch_assoc($res)) {
		$apply ? $a = "<img class='ptr' style='margin-bottom: 10px;' title='Purchase' src='css/i/arrdn.png' onclick='applyFile(\"$tmp[id]\")'/>" : $a = "&nbsp;";
		print("<tr gid='$tmp[id]'><td><img width='60' src='upload/$tmp[uploader]/$tmp[id]_t.jpg'/></td><td width='120'><b>$tmp[artist]</b><br/><br/>$tmp[title]</td><td class='desc-w'>$tmp[descr]</td><td width='80'>$tmp[cat]</td><td align='center' width='40'>$tmp[activation]</td><td>$a</td></tr>");
	}
	print("</table>");
}

function applyFile() {
	$terms = 'This Agreement sets forth the legally binding terms for your use of the Services. By using the Services, and in consideration of Provider providing the Services to you, you agree to be bound by this Agreement, whether you are a "Visitor" (which means that you have not registered with the Website) or you are a "Member" (which means that you have registered with the Website). The term "User" refers to a Visitor or a Member. You are only authorized to use the Services (regardless of whether your access or use is intended) if you agree to abide by all applicable laws and to this Agreement. Please read this Agreement carefully and save it. If you do not agree with it, you should leave the Website and discontinue use of the Services immediately.<br/><br/>
1. Use of Services and additional terms<br/><br/>
This Agreement includes Provider’s policy for acceptable use of the Services and content posted on the Website, your rights, obligations and restrictions regarding your use of the Services and Provider’s Privacy Policy. In order to participate in certain Services, you may be notified that you are required to download software or content and/or agree to additional terms and conditions.<br/><br/>
2. Modification<br/><br/>
Provider may modify this Agreement from time to time and such modification shall be effective upon posting by Provider on the Website. We will provide a clear link within the Website to the then current Agreement. You agree to be bound by any changes to this Agreement when you access the Website or use the Services after any such modification is posted. If you do not agree to be bound by them, you should not use the Website or the Services.<br/><br/>
3. Unacceptable Content<br/><br/>
Please choose carefully the information you post on the Website and that you provide to other Users. Your Website profile and other Content submitted by you to the Website may not include the following items last names, telephone numbers, street addresses or other contact details or identifying information of private individuals, contact details of public figures and any photographs containing nudity, or obscene, lewd, violent, sexually explicit or otherwise objectionable subject matter.';

	$id = mysql_real_escape_string($_POST[id]);
	$tmp = mysql_fetch_assoc(mysql_query("SELECT id, uploader, artist, title, descr, activation, price, (SELECT name FROM categories WHERE id = cat) as cat FROM audio WHERE id = '$id'"));
	print("<div id='purch-info'><table><tr><td colspan='2' style='border-bottom: 2px solid #666;'><h4>Purchase information for $tmp[title]</h4></td></tr>
				<tr><td>Artist</td><td>$tmp[artist]</td></tr><tr><td>Title</td><td>$tmp[title]</td></tr><tr><td>Category</td><td>$tmp[cat]</td></tr><tr><td>Description</td><td>$tmp[descr]</td></tr><tr><td>Activation</td><td>$tmp[activation] month(s)</td></tr><tr><td>Price</td><td>$tmp[price]</td></tr><tr><td colspan='2'><center><h4>Terms and Conditions</h4></center><br/><div id='terms'>$terms</div></td></tr><tr><td colspan='2'><center><button class='btn' onclick='confirmFile(\"$id\")'>Agree and Continue</button>&nbsp;&nbsp;&nbsp;<a href='javascript:;' onclick='ol.close();'>Decline</a></center></td></tr></table></div>");
}

function confirmFile() {
	$audio = mysql_real_escape_string($_POST[id]);
	$user = mysql_real_escape_string($_COOKIE[user]);
	$aud = mysql_fetch_assoc(mysql_query("SELECT activation FROM audio WHERE id ='$audio'"));
	$exp = date("Y-m-d H:i:s",time()+$aud['activation']*18144000);
	mysql_query("UPDATE audio_users SET active = '0' WHERE active = '1' AND user_id = '$user'");
	mysql_query("INSERT INTO audio_users VALUES (null,'$user','$audio','$exp','1')");
	$tmp = mysql_fetch_assoc(mysql_query("SELECT id, uploader, artist, title, descr, activation, price, (SELECT name FROM categories WHERE id = cat) as cat FROM audio WHERE id = '$audio'"));
	print("<div id='purch-info'><table><tr><td colspan='2' style='border-bottom: 2px solid #666;'><h4>Receipt for $tmp[title]</h4></td></tr><tr><td>Artist</td><td>$tmp[artist]</td></tr><tr><td>Title</td><td>$tmp[title]</td></tr><tr><td>Category</td><td>$tmp[cat]</td></tr><tr><td>Description</td><td>$tmp[descr]</td></tr><tr><td>Activation</td><td>$tmp[activation] month(s)</td></tr><tr><td>Price</td><td>$tmp[price]</td></tr><tr><td colspan='2'><center><button class='btn' onclick='ol.close()'>Close</button></center></td></tr></table></div>");
}

function cancelFile() {
	$audio = mysql_real_escape_string($_POST[id]);
	$user = mysql_real_escape_string($_COOKIE[user]);
	mysql_query("UPDATE audio_users SET active = '0' WHERE audio_id = '$audio' AND user_id = '$user'");
}

function loadPopular() {
	$res = mysql_query("SELECT audio_id, count(id) as times FROM audio_users GROUP BY audio_id ORDER BY count(id) DESC");
	while ($tmp = mysql_fetch_assoc($res)) {
		$tmp2 = mysql_fetch_assoc(mysql_query("SELECT uploader FROM audio WHERE id = '$tmp[audio_id]'"));
		print("<img width='60' src='upload/$tmp2[uploader]/$tmp[audio_id]_t.jpg' title='$tmp[times] times' onclick='loadTune(\"$tmp[audio_id]\")'/>");
	}
}

function loadTune() {
	$id = mysql_real_escape_string($_POST[id]);
	$user = $_COOKIE['user'];
	$res = mysql_query("SELECT id, uploader, artist, title, descr, activation, price, (SELECT name FROM categories WHERE id = cat) as cat FROM audio WHERE id = '$id' LIMIT 1");
	print("<table>");	
	$res2 = mysql_query("SELECT id, uploader, artist, title, descr, activation, price, (SELECT name FROM categories WHERE id = cat) as cat FROM audio WHERE id IN (SELECT audio_id FROM audio_users WHERE user_id = '$user' AND active = '1') LIMIT 1");
	if (mysql_num_rows($res2) > 0)
		$apply = false;
	else
		$apply = true;
	while ($tmp = mysql_fetch_assoc($res)) {
		$apply ? $a = "<img class='ptr' style='margin-bottom: 10px;' title='Purchase' src='css/i/arrdn.png' onclick='applyFile(\"$tmp[id]\")'/>" : $a = "&nbsp;";
		print("<tr gid='$tmp[id]' class='single-item'><td><img width='60' src='upload/$tmp[uploader]/$tmp[id]_t.jpg'/></td><td width='120'><b>$tmp[artist]</b><br/><br/>$tmp[title]</td><td class='desc-w'>$tmp[descr]</td><td width='80'>$tmp[cat]</td><td align='center' width='40'>$tmp[activation]</td><td>$a</td></tr>");
	}
	print("</table>");
}



?>