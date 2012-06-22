<?php
include("login.php");

function getPoll() {
	if (!$GLOBALS[CURUSER])
 		getResults(0);
	else {
		$userid = $_COOKIE[user];
		$res = mysql_query("SELECT pr.answer as id FROM poll_res pr WHERE pr.answer IN (SELECT pa.id FROM poll_ans pa WHERE pa.poll = (SELECT p.id FROM poll p WHERE p.active = '1')) AND pr.user = '$userid'") or die(mysql_error());
		if (mysql_num_rows($res) > 0) {
  		$tmp = mysql_fetch_assoc($res);
  		getResults($tmp[id]);
  	} else
  		getQuestions();
	}
}
	

function addPoll() {
  $q = mysql_real_escape_string($_POST['question']);
  $date = date("Y-m-d");
  $user = $_COOKIE[user];
  mysql_query("UPDATE poll SET active = '0'");
  mysql_query("INSERT INTO poll VALUES (null, '$user', '$q', '$date', '1')");
  $poll = mysql_insert_id();
  foreach ($_POST as $key => $val) {
    if ($key == 'question')
      continue;
    $ans = mysql_real_escape_string($val);
    mysql_query("INSERT INTO poll_ans VALUES (null, '$poll','$ans')");  
  }
  print('Poll Added');    
}  

function delPoll() {
  $id = mysql_real_escape_string($_POST['id']);
  mysql_query("DELETE FROM poll WHERE id = '$id'");
  $tmp = mysql_fetch_assoc(mysql_query("SELECT id FROM poll ORDER BY id DESC LIMIT 1"));
  mysql_query("UPDATE poll SET active = '1' WHERE id = '$tmp[id]'");
  print('Poll Deleted');      
}

function vote($id) {
  $user = $_COOKIE['user'];
  $opt = mysql_real_escape_string($_POST[opt]);
  mysql_query("INSERT INTO poll_res VALUES (null, '$opt','$user')");  
}

function getQuestions() {
  $res = mysql_query("SELECT p.question as qst, a.answer as ans, a.id as ansid FROM poll p, poll_ans a WHERE a.poll = p.id AND p.active = '1'");
  if (mysql_num_rows($res)==0) print("<div id='poll' style='padding: 20px; margin-top: 15px; text-align: center'>No Polls Yet</div><br>"); 
  else {
    while ($tmp = mysql_fetch_assoc($res)) {
    	$q = $tmp[qst];
      $results .= "<input type='radio' name='poll' value='$tmp[ansid]' id='opt$tmp[ansid]'><label for='opt$tmp[ansid]'>&nbsp; $tmp[ans]</label><br>";
    }
    print("<div class='poll'><p class='quest'>$q</p><br/><p class='results'>$results</p><br/><center><button class='btn' onclick='vote()' >Vote</button></center></div><hr style='border: 1px solid #888;'/><a href='javascript:;' onclick='getOlderPolls()'>Previous Polls</a>");
  }
  if ($CURUSER['class'] == 0) 
    print('<a href="javascript:;" onclick="openPollEdit()" class="rt">Add Poll</a>');
}

function getResults($id = 0, $poll = 0) {
  if ($poll == 0) $where = 'active = 1'; else $where = 'id = '.$poll;
  $res2 = mysql_query("SELECT question FROM poll WHERE ".$where);
  $tmp2 = mysql_fetch_assoc($res2);
  print("<p class='quest'>$tmp2[question]</p>");
  $res = mysql_query("SELECT pr.answer as id, COUNT(pr.id) as votes, pa.answer as ans, pa.poll as poll FROM poll_res pr, poll_ans pa WHERE pr.answer IN (SELECT pa.id FROM poll_ans pa WHERE pa.poll = (SELECT p.id FROM poll p WHERE ".$where.")) AND pr.answer = pa.id GROUP BY ans");
  $totv = mysql_fetch_assoc(mysql_query("SELECT COUNT(pr.id) as sum FROM poll_res pr, poll_ans pa WHERE pr.answer IN (SELECT pa.id FROM poll_ans pa WHERE pa.poll = (SELECT p.id FROM poll p WHERE ".$where.")) AND pr.answer = pa.id"));
  $total_votes = $totv[sum];
  $results_html = "<div id='poll-results'>";  
  while ($row = mysql_fetch_assoc($res)) {  
  	$poll = $row[poll];
    $percent = round(($row['votes']/$total_votes)*100);  
    if (!($row['id'] == $id))
      $results_html .= "$row[ans] ($percent%)<br/><div class='bar' style='width:$percent%;'>&nbsp;</div>";  
    else
      $results_html .= "$row[ans] ($percent%)<br/><div class='bar' style='width:$percent%;background-color:#e33;'>&nbsp;</div>";  
  }
  $res3 = mysql_query("SELECT pa.answer as ans FROM poll_ans pa WHERE pa.poll = '$poll' AND pa.id NOT IN (SELECT answer FROM poll_res )");
  while ($tmp = mysql_fetch_assoc($res3)) {
		$results_html .= "$tmp[ans] (0%)<br/><div class='bar' style='width:1%;'>&nbsp;</div>";  
  }    
  $results_html .= "<br><p>Total Votes: $total_votes</p></div><hr style='border: 1px solid #888;'/><a href='javascript:;' onclick='getOlderPolls()'>Previous Polls</a>";
  if ($CURUSER['class'] == 0) 
    $results_html .=  '<a href="javascript:;" onclick="openPollEdit()" class="rt">Add Poll</a>';
  print $results_html;  
}  

function getPollEdit() {
	print("<div class='rt' style='position: relative; top: -4px;'><a href='javascript:;' onclick='closePollEdit()'>[close]</a></div><div class='part'>Question:<br/><input type='text' name='question' style='width: 290px; margin-top: 5px;'/></div><br/>
	<div id='answers' class='part'><table id='ans-table'><tr><td>Answer 1:</td><td><input type='text' name='1'></td></tr><tr><td>Answer 2:</td><td><input type='text' name='2'></td></tr></table></div><br/>
<button class='btn' onclick='addPoll()'>Save Poll</button><a href='javascript:;' onclick='addAnswers()' style='float: right;'>Add more answers</a> ");	
  $res = mysql_query("SELECT id, `date`, question, active, (SELECT login FROM users WHERE id = user) as user FROM poll ORDER BY `id` DESC");
  print("<div id='poll-list'>");
  while ($tmp = mysql_fetch_assoc($res)) {
    if ($tmp['active'] == 1) $act = '(active)'; else $act = "<a href='javascript:;' onclick='activatePoll($tmp[id])'>[activate]</a>";
    print("<b>$tmp[question]</b><br/><span>by $tmp[user] @ $tmp[date] $act <a href='javascript:;' onclick='delPoll($tmp[id])'>[delete]</a></span><br/><hr>");
  }
  print("</div>");
}  

function actPoll() {
	$id = mysql_real_escape_string($_POST[id]);
	mysql_query("UPDATE poll SET active = '0'");
	mysql_query("UPDATE poll SET active = '1' WHERE id = '$id'");
}

?>
