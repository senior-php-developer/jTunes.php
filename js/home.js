$(init);
pollAnswers = 2;

function init() {
	$("#poll-area").load('inc/poll.php?do=getPoll');
}


function openPollEdit() {
	$("#poll-edit").load('inc/poll.php?do=getPollEdit',function(){
		var w = ($(window).width() / 2)-150;
		var h = ($(window).height() / 2)-200;
		$("#poll-edit").css('top',h+'px').css('left',w+'px').fadeIn('slow');
	})
}


function addAnswers() {
	pollAnswers++;  
  $("#ans-table").append('<tr><td>Answer '+pollAnswers+':</td><td><input type="text" name="'+pollAnswers+'"></td></tr>');
}

function addPoll() {
	var data = {};
	$("#poll-edit :input").each(function(){
		data[$(this).attr('name')] = $(this).val();
	});
	$.post('inc/poll.php?do=addPoll',data,function(){
		$("#poll-edit").load('inc/poll.php?do=getPollEdit');
	});
}

function delPoll(id) {
	$.post('inc/poll.php?do=delPoll',{id:id},function(){
		$("#poll-edit").load('inc/poll.php?do=getPollEdit');
	});
}

function activatePoll(id) {
	$.post('inc/poll.php?do=actPoll',{id:id},function(){
		$("#poll-edit").load('inc/poll.php?do=getPollEdit');
	})
}

function closePollEdit() {
	$("#poll-area").load('inc/poll.php?do=getPoll',function(){
		$("#poll-edit").fadeOut();	
	});
}

function vote() {
	var sel = $("#poll-area :checked").val();
	$.post('inc/poll.php?do=vote',{opt:sel},function(){
		$("#poll-area").load('inc/poll.php?do=getPoll');
	});
}