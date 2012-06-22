$(init_profile);


function init_profile() {
	
}

function showProfile() {
	$("#area").load('inc/profile.php?do=showProfile');
}

function showPass() {
	$("#area").load('inc/profile.php?do=showPass');
}

function showPurchase() {
	$("#area").load('inc/profile.php?do=showPurchase',function(){
		$("#area tr:even").css('background','#eee');
	});
}

function showFavorites() {
	
}

function showUploads() {
	
}

function showDownloads() {
	
}

function saveProfile() {
	var data = {};
	$("#area :input").each(function(){
		data[$(this).attr('name')] = $(this).val();
	});
	$.post('inc/profile.php?do=saveProfile',data);
}

function savePass() {
	var data = {};
	$("#area :input").each(function(){
		data[$(this).attr('name')] = $(this).val();
	});
	$.post('inc/profile.php?do=savePass',data);
}